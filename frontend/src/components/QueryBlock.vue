<script setup lang="ts">

import { ref, onMounted } from 'vue'
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome'
import { faTrashCan } from '@fortawesome/free-regular-svg-icons'
import { faArrowDown, faArrowUp, faPlusMinus, faGear } from '@fortawesome/free-solid-svg-icons'
import hljs from 'highlight.js/lib/core'
import sql from 'highlight.js/lib/languages/sql'
import { getCursor, setCursor } from '#/utils/cursor'
import DataTable from '#/components/DataTable.vue'

hljs.registerLanguage('sql', sql);

const {
    results,
    locked,
    loading,
    failure,
    onDelete,
    onMoveUp,
    onMoveDown,
    onInsertBefore,
    onInsertAfter
} = defineProps<{
    results: ({
        type: 'resultset'
        columns: string[]
        rows: string[][]
    } | {
        type: 'error'
        error: {
            SQLSTATE: string
            driverError: string
            driverErrorMsg: string
        }
    })[]
    locked: boolean
    loading: boolean
    failure: boolean
    onDelete: () => void
    onMoveUp: () => void
    onMoveDown: () => void
    onInsertBefore: () => void
    onInsertAfter: () => void
}>()

const queryBlock = ref()
const queryInput = ref()
const moreOptionsBlock = ref()
const moreOptionsButton = ref()

const mode = ref('query')
const moreOptions = ref(false)

onMounted(() => {
    // getThis().getOperation = function() {
    //     return {
    //         query: {
    //             type: 'query',
    //             query: queryInput.value.innerText
    //         },
    //         loadfile: {
    //             type: 'loadfile',
    //             file: null,
    //             table: null
    //         }
    //     }[mode.value]
    // }
})

function updateSyntaxHighlighting(event) {
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

function insertTab(event) {
    if(event.keyCode === 9) {
        event.preventDefault()
        document.execCommand('insertText', false, '\u0009')
    }
}

function dismissMoreOptions(event) {
    if(!moreOptionsBlock.value?.contains(event.target) && !moreOptionsButton.value?.contains(event.target)) {
        moreOptions.value = false
    }
}

</script>

<template>
    <div class="query-block" ref="queryBlock" @click="dismissMoreOptions">
        <div class="button-column-wrapper">
            <header></header>
            <fieldset class="button-column" :disabled="locked || null">
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
                    <div :style="{ display: 'flex', transform: 'scaleY(-1)', width: '100%' }">
                        <FontAwesomeIcon :icon="faPlusMinus" />
                    </div>
                </button>
                <button @click="moreOptions = true" title="More options" ref="moreOptionsButton">
                    <FontAwesomeIcon :icon="faGear" />
                </button>
                <div v-if="moreOptions" class="button-column-more-options" ref="moreOptionsBlock">
                    <button v-if="mode === 'loadfile'" @click="mode = 'query'">Switch to SQL query</button>
                    <button v-else-if="mode === 'query'" @click="mode = 'loadfile'">Switch to LOAD TABLE</button>
                </div>
            </fieldset>
        </div>
        <div class="main">
            <div>
                <header>
                    <span>Query</span>
                </header>
                <pre v-if="mode === 'query'" class="needs-placeholder" :contenteditable="!locked" spellcheck=false ref="queryInput" @input="updateSyntaxHighlighting" @keydown="insertTab"></pre>
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
            </div>
            <div>
                <header>
                    <span>Result</span>
                </header>
                <div class="results" v-if="loading">
                    <div class="resultset" :style="{ '--column-count': 3 }">
                        <div class="row header-row">
                            <div class="cell">
                                <div class="loading-cell-line"></div>
                            </div>
                            <div class="cell">
                                <div class="loading-cell-line"></div>
                            </div>
                            <div class="cell">
                                <div class="loading-cell-line"></div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="cell">
                                <div class="loading-cell-line">
                                    <div class="loading-cell-line-stripe"></div>
                                </div>
                            </div>
                            <div class="cell">
                                <div class="loading-cell-line">
                                    <div class="loading-cell-line-stripe"></div>
                                </div>
                            </div>
                            <div class="cell">
                                <div class="loading-cell-line">
                                    <div class="loading-cell-line-stripe"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="results" v-else-if="failure">
                    <div class="error">[ERROR] Run failed</div>
                </div>
                <div class="results" v-else>
                    <template v-for="result in results">
                        <DataTable v-if="result.type === 'resultset'" :columns="result.columns" :rows="result.rows"></DataTable>
                        <div class="message" v-else-if="result.type === 'message'">
                            {{ result.message }}
                        </div>
                        <div class="error" v-else>
                            {{ `[ERROR] [SQLSTATE ${result.error.SQLSTATE} DriverError ${result.error.driverError}]: ${result.error.driverErrorMsg}` }}
                        </div>
                    </template>
                </div>
            </div>
        </div>
    </div>
</template>

<style>
    @import 'highlight.js/scss/atom-one-light.scss';

    :host {
        display: flex;
    }
</style>

<style scoped>
    .query-block {
        display: flex;
        width: 100%;
    }

    .button-column-wrapper {
        display: flex;
        flex-direction: column;
    }

    .button-column {
        display: flex;
        flex-direction: column;
        justify-content: space-evenly;
        flex-grow: 1;
        padding: 6px 2px;
        gap: 4px;
        border: none;
        margin: 0;
        position: relative;
    }

    .button-column button {
        min-width: 30px;
        height: 30px;
        cursor: pointer;
        display: flex;
        padding: 5px;
        justify-content: center;
        align-items: center;
        color: #222;
        background-color: #e1e1e1;
        border-radius: 3px;
        white-space: nowrap;
    }

    .button-column-more-options {
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

    .button-column-more-options button {
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
        background-color: #6495ed;
        padding: 4px 10px;
        display: flex;
        white-space: nowrap;
        font-size: .8rem;
        color: #00163c;
        font-family: monospace;
        font-weight: 700;
        height: 20px;
    }

    pre {
        margin: 4px;
        font-size: 1rem;
        box-sizing: border-box;
        padding: 12px 20px;
        flex-grow: 1;
        white-space: preserve-spaces;
    }

    pre.needs-placeholder::before {
        content: 'Write query here';
        color: gray;
        pointer-events: none;
    }

    .operation {
        font-family: monospace;
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

    .resultset {
        font-family: monospace;
        display: grid;
        grid-template-columns: repeat(var(--column-count), auto);
        border: 1px solid gray;
        gap: 1px;
        background-color: gray;
        min-width: min-content;
    }

    .resultset .row {
        display: contents;
    }

    .resultset .cell {
        padding: 6px 10px;
        background-color: white;
    }

    .resultset .header-row .cell {
        background-color: #b7b7b7;
    }

    .loading-cell-line {
        width: 10ch;
        height: 1rem;
        background-color: #b6b6b6;
        border-radius: 6px;
        position: relative;
        overflow: hidden;
    }

    .header-row .loading-cell-line {
        background-color: #fff;
    }

    @keyframes stripe-swipe {
        0% {
            left: 20%;
        }

        22.2% {
            left: 100%;
        }

        22.2001% {
            left: -20%;
        }

        33.3% {
            left: 20%;
        }

        100% {
            left: 20%;
        }
    }

    .loading-cell-line-stripe {
        width: 20%;
        height: 100%;
        background-color: #c4c4c4;
        left: 20%;
        position: absolute;
        transform: skewX(-20deg);
        animation: stripe-swipe 1.2s ease-out infinite;
    }

    .error {
        font-family: monospace;
        border-left: 4px solid #950000;
        color: #950000;
        padding: 5px 0 5px 12px;
        font-weight: bold;
        font-size: 1rem;
    }

    .message {
        font-family: monospace;
        border-left: 4px solid #444;
        color: #444;
        padding: 5px 0 5px 12px;
        font-weight: bold;
        font-size: 1rem;
    }
</style>