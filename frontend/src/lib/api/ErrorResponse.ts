export class ErrorResponse extends Response {
    constructor(error: string | Error) {
        super(JSON.stringify({
            success: false,
            error: error instanceof Error ? error.message : error
        }), {
            status: 400
        })
    }
}
