<script setup>

import DataTable from 'datatables.net-dt'
import { ref, onMounted } from 'vue'

const table = ref()

const { columns, rows } = defineProps({
	/** @type string[] */
    columns: {
        required: true
    },
    /** @type string[][] */
    rows: {
        required: true
    }
})

onMounted(() => {
    new DataTable(table.value, {
        columns: columns.map(column => ({ title: column })),
        data: rows,
        order: []
    })
})

</script>

<template>
    <table ref="table" class="cell-border compact stripe hover order-column"></table>
</template>

<style>
    @import "https://cdn.datatables.net/2.2.2/css/dataTables.dataTables.min.css";
</style>