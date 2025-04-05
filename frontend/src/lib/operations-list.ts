export class OperationsList {
    constructor(
        private operations: string[] = [ crypto.randomUUID() ]
    ) {}

    *[Symbol.iterator]() {
        yield* this.operations
    }

    delete(operation: string) {
        const newList = this.operations.filter(o => o !== operation)
        if(newList.length === 0) {
            this.operations = [ crypto.randomUUID() ]
        } else {
            this.operations = newList
        }
    }

    moveUp(operation: string) {
        if(this.indexOf(operation) === 0) {
            // prevents -1 from appearing as param to this.operations.slice
            return
        }

        this.operations = [
            ...this.operations.slice(0, this.indexOf(operation) - 1),
            operation,
            ...this.operations.slice(this.indexOf(operation) - 1, this.indexOf(operation)),
            ...this.operations.slice(this.indexOf(operation) + 1, this.operations.length)
        ]
    }

    moveDown(operation: string) {
        this.operations = [
            ...this.operations.slice(0, this.indexOf(operation)),
            ...this.operations.slice(this.indexOf(operation) + 1, this.indexOf(operation) + 2),
            operation,
            ...this.operations.slice(this.indexOf(operation) + 2, this.operations.length)
        ]
    }

    insertBefore(operation: string) {
        this.operations = [
            ...this.operations.slice(0, this.indexOf(operation)),
            crypto.randomUUID(),
            ...this.operations.slice(this.indexOf(operation), this.operations.length)
        ]
    }

    insertAfter(operation: string) {
        this.operations = [
            ...this.operations.slice(0, this.indexOf(operation) + 1),
            crypto.randomUUID(),
            ...this.operations.slice(this.indexOf(operation) + 1, this.operations.length)
        ]
    }

    private indexOf(operation: string) {
        const index = this.operations.indexOf(operation)
        if(index === -1) {
            throw new Error(`Operation ${operation} not found in operation list`)
        }
        return index
    }
}
