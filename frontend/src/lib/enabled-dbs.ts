import type { AstroGlobal } from "astro"

export function fetchEnabledDbs(context: AstroGlobal) {
    if(context.locals.runtime.env.PUBLIC_ENABLED_DBS === '*') {
        return [
            'postgres16',
            'postgres15',
            'postgres14',
            'postgres13',
            'postgres12',
            'mysql8.4',
            'mysql8.0',
            'maria11.8.1',
            'oracle23ai',
            'oracle21c'
        ]
    } else {
        return context.locals.runtime.env.PUBLIC_ENABLED_DBS.split(',')
    }
}