export function fetchEnabledDbs() {
    if(import.meta.env.PUBLIC_ENABLED_DBS === '*') {
        return [
            'postgres16',
            'postgres15',
            'postgres14',
            'postgres13',
            'postgres12',
            'mysql8.4',
            'mysql8.0',
            'oracle23ai'
        ]
    } else {
        return import.meta.env.PUBLIC_ENABLED_DBS.split(',')
    }
}