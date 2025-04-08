<script setup lang="ts">

const { type, text } = defineProps<{
    type: 'error' | 'message'
    text: string
}>()

</script>

<template>
    <span :data-style="type">{{ text }}</span>
</template>

<style scoped>

span {
    font-family: 'Source Code Pro', monospace;
    border-left: 4px solid var(--thick-border-color);
    border-right: 4px solid var(--thick-border-color);
    border-top: 1px solid var(--thin-border-color);
    border-bottom: 1px solid var(--thin-border-color);
    color: var(--color);
    background-color: var(--background-color);
    padding: 5px 12px;
    font-weight: bold;
    font-size: 1rem;
    letter-spacing: -0.72px;
}

[data-style=error] {
    --thick-border-color: #950000;
    --thin-border-color: #bf7b7b;
    --color: #950000;
    --background-color: #fbf5f5;
}

[data-style=message] {
    --thick-border-color: #535353;
    --thin-border-color: #959595;
    --color: #313131;
    --background-color: #f0f0f0;
}

</style>