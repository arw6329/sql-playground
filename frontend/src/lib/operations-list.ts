import type { Operation } from "./Operation"
import type { RunResult } from "./RunResult"

interface KeyedOperationWithResults {
    operationId: string
    operation: Operation
    results: RunResult[]
}

export class OperationsList {
    constructor(
        private operations: KeyedOperationWithResults[] = [ this.newOperation() ]
    ) {}

    *[Symbol.iterator]() {
        yield* this.operations
    }

    delete(operationId: string) {
        const newList = this.operations.filter(o => o.operationId !== operationId)
        if(newList.length === 0) {
            this.operations = [ this.newOperation() ]
        } else {
            this.operations = newList
        }
    }

    moveUp(operationId: string) {
        const { index, operation } = this.getOperation(operationId)

        if(index === 0) {
            // prevents -1 from appearing as param to this.operations.slice
            return
        }

        this.operations = [
            ...this.operations.slice(0, index - 1),
            operation,
            ...this.operations.slice(index - 1, index),
            ...this.operations.slice(index + 1, this.operations.length)
        ]
    }

    moveDown(operationId: string) {
        const { index, operation } = this.getOperation(operationId)

        this.operations = [
            ...this.operations.slice(0, index),
            ...this.operations.slice(index + 1, index + 2),
            operation,
            ...this.operations.slice(index + 2, this.operations.length)
        ]
    }

    insertBefore(operationId: string) {
        const { index } = this.getOperation(operationId)

        this.operations = [
            ...this.operations.slice(0, index),
            this.newOperation(),
            ...this.operations.slice(index, this.operations.length)
        ]
    }

    insertAfter(operationId: string) {
        const { index } = this.getOperation(operationId)

        this.operations = [
            ...this.operations.slice(0, index + 1),
            this.newOperation(),
            ...this.operations.slice(index + 1, this.operations.length)
        ]
    }

    setOperation(operationId: string, operation: Operation) {
        const { index } = this.getOperation(operationId)
        this.operations[index].operation = operation
    }

    setResults(index: number, results: RunResult[]) {
        this.operations[index].results = results
    }

    private getOperation(operationId: string) {
        const index = this.operations.findIndex(operation => operation.operationId === operationId)
        if(index === -1) {
            throw new Error(`Operation ${operationId} not found in operation list`)
        }
        return {
            index: index,
            operation: this.operations[index]
        }
    }

    private newOperation(): KeyedOperationWithResults {
        return {
            operationId: crypto.randomUUID(),
            operation: {
                type: 'query',
                query: ''
            },
            results: []
        }
    }
}
