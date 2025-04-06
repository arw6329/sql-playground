export type Operation = {
    type: 'query'
    query: string
} | {
    type: 'loadfile'
    file: null
    table: null
}
