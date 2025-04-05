// https://phuoc.ng/collection/html-dom/get-or-set-the-cursor-position-in-a-content-editable-element/

function lengthOfNode(node) {
    if(node.nodeType === Node.TEXT_NODE) {
        return node.textContent.length
    } else if(node.tagName === 'BR') {
        return 1
    } else {
        return [...node.childNodes].map(node => lengthOfNode(node)).reduce((a, b) => a + b)
    }
}

const createRange = (node, targetPosition) => {
    let range = document.createRange()
    range.selectNode(node)

    let pos = 0
    const stack = [node]
    while (stack.length > 0) {
        const current = stack.pop()

        if (current.nodeType === Node.TEXT_NODE) {
            const len = current.textContent.length
            if (pos + len >= targetPosition) {
                range.setStart(current, targetPosition - pos)
                range.setEnd(current, targetPosition - pos)
                return range
            }
            pos += len
        } else if(current.tagName === 'BR') {
            const len = 1
            if (pos + len >= targetPosition) {
                const nextSibling = stack.pop()
                if(nextSibling) {
                    range.setStart(nextSibling, 0)
                    range.setEnd(nextSibling, 0)
                } else {
                    throw 1
                }
                return range
            }
            pos += len
        } else if (current.childNodes && current.childNodes.length > 0) {
            for (let i = current.childNodes.length - 1; i >= 0; i--) {
                stack.push(current.childNodes[i])
            }
        }
    }

    // The target position is greater than the
    // length of the contenteditable element.
    range.setStart(node, node.childNodes.length)
    range.setEnd(node, node.childNodes.length)
    return range
}

export function setCursor(element, cursorPosition) {
    const range = createRange(element, cursorPosition)
    const selection = window.getSelection()
    selection.removeAllRanges()
    selection.addRange(range)
}

export function getCursor(element) {
    const selection = window.getSelection()

    let pos = 0
    const stack = [element]
    while (stack.length > 0) {
        const current = stack.pop()

        if(current === selection.focusNode) {
            if (current.nodeType === Node.TEXT_NODE) {
                return pos + selection.focusOffset
            } else {
                for(let i = 0; i < selection.focusOffset; i++) {
                    pos += lengthOfNode(current.childNodes[i])
                }
                return pos
            }
        } else {
            if (current.nodeType === Node.TEXT_NODE) {
                const len = current.textContent.length
                pos += len
            } else if(current.tagName === 'BR') {
                pos += 1
            } else if (current.childNodes && current.childNodes.length > 0) {
                for (let i = current.childNodes.length - 1; i >= 0; i--) {
                    stack.push(current.childNodes[i])
                }
            }
        }
    }
}