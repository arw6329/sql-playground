<script setup lang="ts">

import { ref } from 'vue'
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome'
import { faTrashCan } from '@fortawesome/free-regular-svg-icons'
import { faArrowDown, faArrowUp, faPlusMinus, faGear } from '@fortawesome/free-solid-svg-icons'
import hljs from 'highlight.js/lib/core'
import sql from 'highlight.js/lib/languages/sql'
import { getCursor, setCursor } from '#/utils/cursor'
import DataTable from '#/components/data-table/DataTable.vue'
import type { RunResult } from '#/lib/RunResult'
import type { Operation } from '#/lib/Operation'
import type { DOMEvent } from '#/lib/DOMEvent'
import InfoBlock from './InfoBlock.vue'
import LoadingDataTable from './data-table/LoadingDataTable.vue'

hljs.registerLanguage('sql', sql);

const {
    results,
    locked,
    loading,
    failure,
    onChange,
    onDelete,
    onMoveUp,
    onMoveDown,
    onInsertBefore,
    onInsertAfter
} = defineProps<{
    results: RunResult[]
    locked: boolean
    loading: boolean
    failure: boolean
    onChange: (operation: Operation) => void
    onDelete: () => void
    onMoveUp: () => void
    onMoveDown: () => void
    onInsertBefore: () => void
    onInsertAfter: () => void
}>()

const moreOptionsBlock = ref()
const moreOptionsButton = ref()

const mode = ref<'query' | 'loadfile'>('query')
const moreOptions = ref<boolean>(false)

function handleInput(event: Event) {
    updateSyntaxHighlighting(event)
    onChange({
        query: {
            type: 'query' as const,
            query: event.target.innerText
        },
        loadfile: {
            type: 'loadfile' as const,
            file: null,
            table: null
        }
    }[mode.value])
}

function updateSyntaxHighlighting(event: DOMEvent<HTMLPreElement>) {
    const blank = event.target.innerText === '\n' || event.target.innerText === ''
    if(blank) {
        event.target.innerHTML = ''
        event.target.classList.add('needs-placeholder')
        setCursor(event.target, 0)
        return
    } 

    const cursor = getCursor(event.target)

    event.target.innerHTML = hljs.highlight(event.target.innerText, {
        language: 'sql'
    }).value.replaceAll('\n', '<br>')

    event.target.classList.remove('needs-placeholder')

    setCursor(event.target, cursor)
}

function insertTab(event: DOMEvent<HTMLPreElement>) {
    if(event.keyCode === 9) {
        event.preventDefault()
        document.execCommand('insertText', false, '    ')
    }
}

function dismissMoreOptions(event: DOMEvent<HTMLPreElement>) {
    if(!moreOptionsBlock.value?.contains(event.target) && !moreOptionsButton.value?.contains(event.target)) {
        moreOptions.value = false
    }
}

</script>

<template>
    <div class="query-block" @click="dismissMoreOptions">
        <div class="main">
            <div>
                <header>
                    <span>Query</span>
                </header>
                <pre
                    v-if="mode === 'query'"
                    class="needs-placeholder"
                    :contenteditable="!locked"
                    spellcheck="false"
                    @input="handleInput"
                    @keydown="insertTab"
                ></pre>
                <div v-else-if="mode === 'loadfile'" class="operation">
                    <div class="operation-piece">
                        <span>LOAD CSV FILE</span>
                        <select></select>
                    </div>
                    <div class="operation-piece">
                        <span>INTO TABLE</span>
                        <input type="text">
                    </div>
                </div>
                <fieldset class="button-row" :disabled="locked || null">
                    <button @click="onDelete" title="Delete query">
                        <FontAwesomeIcon :icon="faTrashCan" />
                    </button>
                    <button @click="onMoveUp" title="Move up">
                        <FontAwesomeIcon :icon="faArrowUp" />
                    </button>
                    <button @click="onMoveDown" title="Move down">
                        <FontAwesomeIcon :icon="faArrowDown" />
                    </button>
                    <button @click="onInsertBefore" title="Insert query above">
                        <FontAwesomeIcon :icon="faPlusMinus" />
                    </button>
                    <button @click="onInsertAfter" title="Insert query below">
                        <div :style="{ transform: 'scaleY(-1)' }">
                            <FontAwesomeIcon :icon="faPlusMinus" />
                        </div>
                    </button>
                    <button @click="moreOptions = true" title="More options" ref="moreOptionsButton">
                        <FontAwesomeIcon :icon="faGear" />
                    </button>
                    <div v-if="moreOptions" class="button-row-more-options" ref="moreOptionsBlock">
                        <button v-if="mode === 'loadfile'" @click="mode = 'query'">Switch to SQL query</button>
                        <button v-else-if="mode === 'query'" @click="mode = 'loadfile'">Switch to LOAD TABLE</button>
                    </div>
                </fieldset>
            </div>
            <div>
                <header>
                    <span>Result</span>
                </header>
                <div class="results" v-if="loading">
                    <LoadingDataTable />
                </div>
                <div class="results" v-else-if="failure">
                    <InfoBlock type="error" text="[ERROR] Run failed" />
                </div>
                <div class="results" v-else>
                    <template v-for="result in results">
                        <DataTable v-if="result.type === 'resultset'" :columns="result.columns" :rows="result.rows"></DataTable>
                        <InfoBlock v-else-if="result.type === 'message'" type="message" :text="result.message" />
                        <InfoBlock v-else type="error" :text="`[ERROR] [SQLSTATE ${result.error.SQLSTATE} DriverError ${result.error.driverError}]: ${result.error.driverErrorMsg}`" />
                    </template>
                </div>
            </div>
        </div>
    </div>
</template>

<style>
    @import 'highlight.js/scss/atom-one-light.scss';
</style>

<style scoped>
    .query-block {
        display: flex;
        width: 100%;
    }

    .button-row {
        display: flex;
        padding: 6px;
        gap: 4px;
        border: none;
        margin: 0;
    }

    .button-row button {
        width: 26px;
        height: 26px;
        cursor: pointer;
        display: flex;
        padding: 0px;
        justify-content: center;
        align-items: center;
        color: #222;
        background-color: #eeecec;
        border-radius: 2px;
        white-space: nowrap;
        border: 1px solid gray;
    }

    .button-row button:hover {
        background-color: #c6c6c8;
    }

    .button-row-more-options {
        position: absolute;
        top: 5px;
        left: calc(100% + 5px);
        display: flex;
        padding: 4px;
        background: #e5e5e5;
        border: 1px solid #bdbdbd;
        border-radius: 3px;
        box-shadow: 0px 0px 3px rgba(0, 0, 0, 0.2);
    }

    .button-row-more-options button {
        white-space: nowrap;
        padding: 6px 12px;
    }

    .main {
        display: flex;
        min-height: 100px;
        flex-grow: 1;
        min-width: 0;
    }

    .main > div {
        box-sizing: border-box;
        display: flex;
        flex-direction: column;
        flex: 1;
        min-width: 0;
    }

    .main > div:first-of-type {
        width: 40%;
        flex: 0 0 auto;
    }

    header {
        background-color: #a4c4ff;
        padding: 4px 10px 4px 20px;
        display: flex;
        white-space: nowrap;
        font-size: .8rem;
        color: #00163c;
        font-family: monospace;
        font-weight: 700;
        height: 15px;
    }

    pre {
        margin: 4px;
        font-size: 1rem;
        box-sizing: border-box;
        padding: 12px 20px;
        flex-grow: 1;
        white-space: preserve-spaces;
    }

    pre, pre * {
        font-family: 'Source Code Pro', monospace;
    }

    pre.needs-placeholder::before {
        content: 'Write query here';
        color: gray;
        pointer-events: none;
    }

    .operation {
        font-family: 'Source Code Pro', monospace;
        margin: 4px;
        font-size: 1rem;
        padding: 12px 20px;
        display: flex;
        flex-wrap: wrap;
        column-gap: 1ch;
        row-gap: .5rem;
    }

    .operation-piece {
        display: flex;
        align-items: stretch;
        gap: 1ch;
        height: 30px;
    }

    .operation-piece span {
        display: flex;
        align-items: center;
        white-space: nowrap;
        color: #a626a4;
    }

    .results {
        display: flex;
        flex-direction: column;
        gap: 20px;
        padding: 8px 15px;
        align-self: flex-start;
        flex-grow: 1;
        overflow-x: scroll;
        max-width: 100%;
        box-sizing: border-box;
    }
</style>