<template>
    <div>
        <h1>Chào mừng đến với Laravel + Inertia + Vue!</h1>
        <p>{{ message }}</p>
    </div>
</template>

<script setup>
defineProps({
    message: String
})
</script>