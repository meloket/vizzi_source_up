<template>
    <router-view />
</template>

<script>
export default {
    layout: 'app',
    middleware: 'any'
}
</script>

<style>
    nav {
        width: 100%
    }
    .backHall {
        top: 5%;
        left: 2%;
        color: red;
        font-weight: 900;
        border: none;
        border-radius: 4px;
        cursor: pointer;
        z-index: 2;
        box-shadow: 0 0 4px;
    }
</style>