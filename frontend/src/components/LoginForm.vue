<script setup lang="ts">

import { ref } from 'vue';
import InfoBlock from './InfoBlock.vue';
import Logo from './Logo.vue';
import AsyncForm from './async-forms/AsyncForm.vue';

let error = ref<Error | null>(null)

function redirect() {
    location.href = '/'
}

</script>

<template>
    <AsyncForm
        method="POST"
        action="/api/auth/login"
        :send-empty-strings="true"
        @before-submit="() => error = null"
        @success="() => redirect()"
        @error="e => error = e"
    >
        <div class="logo-wrapper">
            <span>Log in to</span>
            <Logo />
        </div>
        <InfoBlock v-if="error" type="error" :text="error.toString()" />
        <div class="input-wrapper">
            <label for="email">Email</label>
            <input id="email" type="text" name="email" required>
        </div>
        <div class="input-wrapper">
            <label for="password">Password</label>
            <input id="password" type="password" name="password" required>
        </div>
        <button type="submit">Log in</button>
        <span class="or">or</span>
        <a href="/auth/create-account">Create a new account</a>
    </AsyncForm>
</template>

<style>

form {
    display: flex;
    padding: 15px 20px;
    flex-direction: column;
    gap: 17px;
    background-color: white;
    box-shadow: 0px 0px 8px rgba(0, 0, 0, 0.08);
    margin: auto;
    border: 2px solid #a4c4ff;
    min-width: min(400px, 100%);
    max-width: min(400px, 100%);
    box-sizing: border-box;
}

</style>

<style scoped>

.logo-wrapper {
    display: flex;
    align-items: center;
    gap: 7px;
    font-size: 19px;
    font-weight: 300;
}

.input-wrapper {
    display: flex;
    flex-direction: column;
    gap: 4px;
}

label {
    font-weight: bold;
    font-size: 0.85rem;
}

input {
    padding: 6px 8px;
}

button {
    padding: 8px;
    background-color: cornflowerblue;
    color: white;
    border: none;
    border-radius: 2px;
    cursor: pointer;
}

.or {
    width: 100%;
    display: flex;
    justify-content: center;
    align-items: center;
    gap: 10px;
    padding: 0 10px;
    box-sizing: border-box;
    color: #626262;
}

.or::before, .or::after {
    content: ' ';
    height: 1px;
    background-color: #cacaca;
    display: block;
    flex-grow: 1;
}
    
a {
    text-align: center;
}

</style>