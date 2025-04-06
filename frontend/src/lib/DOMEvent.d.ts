export type DOMEvent<Target extends HTMLElement> = Event & {
    target: Target
}
