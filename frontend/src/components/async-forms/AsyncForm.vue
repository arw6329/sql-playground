<script setup lang="ts">

import { onMounted, useTemplateRef, type FormHTMLAttributes } from 'vue';
import { asyncify, type AsyncFormOptions } from './asyncify';

const props = defineProps</* @vue-ignore */ FormHTMLAttributes & AsyncFormOptions>()

const emit = defineEmits<{
    beforeSubmit: void
    success: [response: { [k: string]: unknown }]
    error: [error: Error]
}>()

const form = useTemplateRef('form')

onMounted(() => {
    asyncify(form.value!, {
        ...props,
        onBeforeSubmit: () => emit('beforeSubmit'),
        onSuccess: (response) => emit('success', response),
        onError: (error) => emit('error', error)
    })
})

</script>

<template>
    <form ref="form" v-bind="props">
        <fieldset disabled="true">
            <slot />
        </fieldset>
    </form>
</template>

<style scoped>

fieldset {
    display: contents;
}

</style>