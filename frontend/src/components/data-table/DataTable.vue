<script setup lang="ts">

const { columns, rows } = defineProps<{
    columns: string[]
    rows: string[][]
}>()

</script>

<template>
    <div class="resultset" :style="{ '--column-count': columns.length }">
        <div class="row header-row">
            <div v-for="column in columns" class="cell">
                {{ column }}
            </div>
        </div>
        <div v-for="row in rows" class="row">
            <div v-for="elem in row" class="cell">
                {{ elem }}
            </div>
        </div>
    </div>
</template>

<style>

@import './DataTable.css';

</style>