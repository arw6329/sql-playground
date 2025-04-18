export interface AsyncFormOptions {
    sendEmptyStrings?: boolean
}

function formToJson(form: HTMLFormElement, nullOnEmptyString: boolean): { [k: string]: unknown } {
    let data = new FormData(form)
    let json: { [k: string]: unknown } = {}

    // include submit button data in posted form
    let focused_elem = document.activeElement
    if(
        focused_elem
        && focused_elem.tagName === 'BUTTON'
        && form.contains(focused_elem)
        && focused_elem.type === 'submit'
        && focused_elem.name
    ) {
        data.append(focused_elem.name, focused_elem.value)
    }

    Array.from(data.entries()).forEach(([key, value]) => {
        json[key] = value !== '' || !nullOnEmptyString ? value : null
    })

    return json
}

export function asyncify(
    form: HTMLFormElement,
    { sendEmptyStrings = true, ...options }: AsyncFormOptions & {
        onBeforeSubmit?: () => void,
        onSuccess?: (response: { [k: string]: unknown }) => void,
        onError?: (error: Error) => void
    } = {}
) {
    const root: HTMLFieldSetElement = form.firstElementChild as HTMLFieldSetElement

    form.addEventListener('submit', async evt => {
        if(evt.submitter && evt.submitter.tagName === 'BUTTON' && evt.submitter.getAttribute('type') !== 'submit') {
            // prevent the terrible behavior where forms are submitted by any button without a type attribute (defaults to type=submit)
            evt.preventDefault()
            return
        }

        if(!form.checkValidity()) {
            return
        }

        evt.preventDefault()

        options.onBeforeSubmit?.()

        // this has to be done before setting root fieldset to disabled
        const requestData = formToJson(form, !sendEmptyStrings)

        root.disabled = true

        try {
            // form.action causes issues if the form contains an input with name=action
            const response = await fetch(form.getAttribute('action') ?? location.href, {
                method: form.method,
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(requestData)
            })

            if(response.status !== 200 && response.status !== 400) {
                throw new Error(`Bad status from server: ${response.status} ${response.statusText}`)
            }

            const json = await response.json()

            if(!json.success) {
                throw new Error(json.error)
            }

            options.onSuccess?.(json)
            form.dispatchEvent(new CustomEvent('success', {
                detail: json
            }))
        } catch(e) {
            options.onError?.(e as Error)
            form.dispatchEvent(new CustomEvent('error', {
                detail: {
                    error: e
                }
            }))
        } finally {
            root.disabled = false
        }
    })

    root.disabled = false
}
