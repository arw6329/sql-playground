import type { D1Database } from "@cloudflare/workers-types"
import { getPlatformProxy } from "wrangler"
import * as argon2 from 'argon2'

export class Identity {
    private constructor(
        public readonly id: string
    ) {}

    static async getIdentityByIdentifier(identifer: string): Promise<Identity | null> {
        const db: D1Database = (await getPlatformProxy()).env.AUTH_DB

        const identity = await db.prepare(`
            SELECT identities.*
            FROM identities LEFT JOIN unique_identifiers
            WHERE unique_identifiers.identifier = ?`
        ).bind(identifer).first<{ _id: string }>()

        return identity ? new Identity(identity._id) : null
    }

    async setPassword(password: string): Promise<void> {
        const db: D1Database = (await getPlatformProxy()).env.AUTH_DB

        const id = crypto.randomUUID()

        await db.prepare(`
            INSERT INTO passwords (_id, identity_id, hash)
            VALUES (?, ?, ?)`
        ).bind(id, this.id, await argon2.hash(password)).run()
    }

    async verifyPassword(password: string): Promise<boolean> {
        const db: D1Database = (await getPlatformProxy()).env.AUTH_DB

        const storedPassword = await db.prepare(`
            SELECT * FROM passwords
            WHERE identity_id = ?`
        ).bind(this.id).first<{
            _id: string
            hash: string
        }>()

        if(!storedPassword) {
            return false
        }

        return await argon2.verify(storedPassword.hash, password)
    }
}
