<script setup lang="ts">

import { onMounted, reactive, ref } from 'vue'
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome'
import { faPlay, faSpinner } from '@fortawesome/free-solid-svg-icons'
import { autosize } from '@arw6329/autosize-textarea'
import QueryBlock from './QueryBlock.vue'
import { OperationsList } from '#/lib/operations-list'

const { enabledDbs } = defineProps<{
    enabledDbs: string[]
}>()

const queryTray = ref()
const dbmsInput = ref()
const titleInput = ref()
const descriptionInput = ref()

const operations = reactive(new OperationsList)
const error = ref(null)
const loading = ref(false)
const editingMetadata = ref(true) // starting out as true is required for autosize() to work on mount

const title = ref('')
const description = ref('')

onMounted(() => {
    autosize(titleInput.value)
    autosize(descriptionInput.value)
    editingMetadata.value = false
})

async function submit() {
    const elems = [...queryTray.value.querySelectorAll('query-block')]

    elems.forEach(elem => {
        elem.locked = true
        elem.loading = true
        elem.failure = false
    })

    error.value = null
    loading.value = true

    let data = null

    try {
        const res = await fetch(`//${import.meta.env.PUBLIC_API_HOST}/api/run.php`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                database: dbmsInput.value.value,
                operations: elems.map(elem => elem.getOperation())
            })
        })

        data = await res.json()
    } catch(e) {
        data = {
            success: false,
            error: 'Network error: ' + e
        }
    }

    elems.forEach(elem => {
        elem.locked = false
        elem.loading = false
    })

    loading.value = false

    if(!data.success) {
        error.value = data.error

        elems.forEach(elem => {
            elem.failure = true
        })
    } else {
        data.results.forEach((result, i) => {
            elems[i].results = result.results
        })
    }
}

</script>

<template>
    <div class="root">
        <header>
            <select ref="dbmsInput">
                <optgroup label="PostgreSQL" v-if="enabledDbs.some(db => db.startsWith('postgres'))">
                    <option value="postgres16" v-if="enabledDbs.includes('postgres16')">PostgreSQL 16</option>
                    <option value="postgres15" v-if="enabledDbs.includes('postgres15')">PostgreSQL 15</option>
                    <option value="postgres14" v-if="enabledDbs.includes('postgres14')">PostgreSQL 14</option>
                    <option value="postgres13" v-if="enabledDbs.includes('postgres13')">PostgreSQL 13</option>
                    <option value="postgres12" v-if="enabledDbs.includes('postgres12')">PostgreSQL 12</option>
                </optgroup>
                <optgroup label="MySQL" v-if="enabledDbs.some(db => db.startsWith('mysql'))">
                    <option value="mysql8.4" v-if="enabledDbs.includes('mysql8.4')">MySQL 8.4</option>
                    <option value="mysql8.0" v-if="enabledDbs.includes('mysql8.0')">MySQL 8.0</option>
                </optgroup>
                <optgroup label="Oracle" v-if="enabledDbs.some(db => db.startsWith('oracle'))">
                    <option value="oracle23ai" v-if="enabledDbs.includes('oracle23ai')">Oracle DB 23ai</option>
                </optgroup>
            </select>
            <button v-show="!editingMetadata" @click="editingMetadata = true">
                <span>Edit title and description</span>
            </button>
            <div style="flex-grow: 1"></div>
            <button v-if="!loading" class="run-button" title="Run worksheet" @click="submit">
                <FontAwesomeIcon :icon="faPlay" />
                <span>Run</span>
            </button>
            <button v-else class="run-button running" disabled>
                <FontAwesomeIcon :icon="faSpinner" />
                <span>Running...</span>
            </button>
        </header>
        <div class="info">
            <h1 v-show="title && !editingMetadata">{{ title }}</h1>
            <p v-show="description && !editingMetadata" style="white-space: pre-wrap">{{ description }}</p>
            <div v-show="editingMetadata" class="edit-form">
                <textarea ref="titleInput" placeholder="Worksheet title" class="title-input">{{ title }}</textarea>
                <textarea ref="descriptionInput" placeholder="Description" style="min-width: 100%; max-width: 100%">{{ description }}</textarea>
                <button @click="title = titleInput.value; description = descriptionInput.value; editingMetadata = false">
                    <span>Save</span>
                </button>
            </div>
            <p v-show="error" class="error">Run failed with error: {{ error }}</p>
        </div>
        <div ref="queryTray">
            <QueryBlock
                v-for="operation in operations"
                :key="operation"
                :onDelete="() => operations.delete(operation)"
                :onMoveUp="() => operations.moveUp(operation)"
                :onMoveDown="() => operations.moveDown(operation)"
                :onInsertBefore="() => operations.insertBefore(operation)"
                :onInsertAfter="() => operations.insertAfter(operation)"
                :locked="loading"
                :loading="loading"
                :failure="false"
                :results="[]"
            />
        </div>
    </div>
</template>

<style scoped>

.root {
    display: flex;
    flex-direction: column;
    border: 2px solid cornflowerblue;
    margin: 4px;
    font-family: sans-serif;
}

header {
    display: flex;
    padding: 6px 10px;
    background-color: #f6faff;
    gap: 10px;
}

.run-button {
    display: flex;
    height: 40px;
    padding: 6px 8px;
    justify-content: space-around;
    align-items: center;
    background-color: #72ff74;
    border: 2px solid green;
    border-right-color: #003700;
    border-bottom-color: #003700;
    border-top-width: 1px;
    border-left-width: 1px;
    border-radius: 6px;
    color: #003700;
    font-size: 1rem;
    font-weight: bold;
    font-family: sans-serif;
    gap: 9px;
    cursor: pointer;
}

@keyframes rotate {
    from {
        transform: rotate(0deg);
    }

    to {
        transform: rotate(360deg);
    }
}

.run-button.running {
    background-color: yellow;
    border-color: #949400;
    border-right-color: #686806;
    border-bottom-color: #686806;
    color: #525200;
}

.run-button > * {
    height: 100%;
    display: flex;
    justify-content: center;
    align-items: center;
}

.run-button.running > svg {
    animation: rotate linear 1.5s infinite;
} 

.info {
    padding: 0 20px;
    color: #444;
}

h1 {
    font-size: 1.5rem;
}

.edit-form {
    display: flex;
    flex-direction: column;
    gap: 6px;
    align-items: baseline;
    margin: 16px 0;
}

.title-input {
    min-width: min(400px, 100%);
    font-size: 1.5rem;
    font-weight: bold;
    color: inherit;
}

textarea {
    font-family: inherit;
}

.edit-form button {
    padding: 6px 12px;
}

.error {
    font-family: monospace;
    border: 2px solid #950000;
    color: #950000;
    font-weight: 700;
    font-size: 1rem;
    background-color: #ffd6d6;
    border-radius: 6px;
    padding: 10px 16px;
}

</style>