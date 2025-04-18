import { ErrorResponse } from "#/lib/api/ErrorResponse"
import { SuccessResponse } from "#/lib/api/SuccessResponse"
import { Identity } from "#/lib/auth/Identity"
import type { APIRoute } from "astro"
import { object } from "doubletime"

export const prerender = false

export const POST: APIRoute = async ({ request }) => {
    const body = await request.json()

    const { value: params, error } = object({
        email: 'trimmed non-empty string',
        password: 'non-empty string'
    }).safeValidate(body)

    if(error) {
        return new ErrorResponse(error)
    }

    const identity = await Identity.getIdentityByIdentifier(params.email)

    if(!identity) {
        return new ErrorResponse(`User with email "${params.email}" does not exist`)
    }

    if(!await identity.verifyPassword(params.password)) {
        return new ErrorResponse(`Password incorrect`)
    }

    return new SuccessResponse
}
