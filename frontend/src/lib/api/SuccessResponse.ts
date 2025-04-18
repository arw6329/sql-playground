export class SuccessResponse extends Response {
    constructor() {
        super(JSON.stringify({
            success: true
        }), {
            status: 200
        })
    }
}
