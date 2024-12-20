<script setup>

import { onMounted, ref } from 'vue'
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome'
import { faPlay, faSpinner } from '@fortawesome/free-solid-svg-icons'
import { autosize } from '@arw6329/autosize-textarea'

const { enabledDbs } = defineProps({
    /** @type string[] */
    enabledDbs: {
        type: Array,
        required: true
    }
})

const queryTray = ref()
const dbmsInput = ref()
const descriptionInput = ref()

const error = ref(null)
const loading = ref(false)

onMounted(() => {
    autosize(descriptionInput.value)
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
        const res = await fetch('/api/run.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                database: dbmsInput.value.value,
                queries: elems.map(elem => elem.getQuery())
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
        <h1>Worksheet title here - some title </h1>
        <p>
            Sed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium doloremque laudantium, totam rem aperiam, eaque ipsa quae ab illo inventore veritatis et quasi architecto beatae vitae dicta sunt explicabo. Nemo enim ipsam voluptatem quia voluptas sit aspernatur aut odit aut fugit, sed quia consequuntur magni dolores eos qui ratione voluptatem sequi nesciunt. Neque porro quisquam est, qui dolorem ipsum quia dolor sit amet, consectetur, adipisci velit, sed quia non numquam eius modi tempora incidunt ut labore et dolore magnam aliquam quaerat voluptatem. Ut enim ad minima veniam, quis nostrum exercitationem ullam corporis suscipit laboriosam, nisi ut aliquid ex ea commodi consequatur? Quis autem vel eum iure reprehenderit qui in ea voluptate velit esse quam nihil molestiae consequatur, vel illum qui dolorem eum fugiat quo voluptas nulla pariatur?
        </p>
        <textarea ref="descriptionInput"></textarea>
        <p v-if="error" class="error">Run failed with error: {{ error }}</p>
    </div>
    <div ref="queryTray">
        <query-block></query-block>
    </div>
</template>

<style>

:host {
    display: flex;
    flex-direction: column;
    border: 2px solid cornflowerblue;
    margin: 4px;
}

</style>

<style scoped>

header {
    display: flex;
    padding: 6px 10px;
    background-color: #f6faff;
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
    font-family: sans-serif;
    font-size: 1.5rem;
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