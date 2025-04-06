export type RunResult = {
    type: 'resultset'
    columns: string[]
    rows: string[][]
} | {
    type: 'message'
    message: string
} | {
    type: 'error'
    error: {
        SQLSTATE: string
        driverError: string
        driverErrorMsg: string
    }
}
