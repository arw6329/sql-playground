<template>
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
</template>

<style>

@import './DataTable.css';

</style>