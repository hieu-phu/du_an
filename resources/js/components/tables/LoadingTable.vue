<template>
    <td colspan="100%" class="py-8">
        <div class="flex items-center justify-center">
            <div class="loader"></div>
        </div>
    </td>
</template>

<style scoped>
.loader {
    width: 32px;
    height: 32px;
    border-radius: 50%;
    background:
        linear-gradient(0deg, rgb(59 130 246 / 30%) 30%, transparent 0 70%, rgb(59 130 246 / 60%) 0) 50%/8% 100%,
        linear-gradient(90deg, rgb(59 130 246 / 15%) 30%, transparent 0 70%, rgb(59 130 246 / 45%) 0) 50%/100% 8%;
    background-repeat: no-repeat;
    animation: l23 1s infinite steps(12);
    position: relative;
}

.loader::before,
.loader::after {
    content: "";
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    border-radius: 50%;
    background: inherit;
    opacity: 0.8;
    transform: rotate(30deg);
}

.loader::after {
    opacity: 0.6;
    transform: rotate(60deg);
}

/* Dark mode */
:global(.dark) .loader {
    background:
        linear-gradient(0deg, rgb(96 165 250 / 40%) 30%, transparent 0 70%, rgb(96 165 250 / 70%) 0) 50%/8% 100%,
        linear-gradient(90deg, rgb(96 165 250 / 20%) 30%, transparent 0 70%, rgb(96 165 250 / 50%) 0) 50%/100% 8%;
}

@keyframes l23 {
    100% {
        transform: rotate(1turn)
    }
}
</style>