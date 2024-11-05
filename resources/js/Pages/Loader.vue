<template>
    <div class="loader">
        <div class="dot"></div>
        <div class="dot"></div>
        <div class="dot"></div>
    </div>
</template>

<style scoped>
.loader {
    display: flex;
    justify-content: center;
    align-items: center;
    height: 100%;
}

.dot {
    width: 8px;
    height: 8px;
    margin: 0 4px;
    background-color: #000;
    border-radius: 50%;
    animation: dot-flashing 1s infinite linear alternate;
}

@keyframes dot-flashing {
    0% {
        background-color: #000;
    }
    50%,
    100% {
        background-color: #ccc;
    }
}
</style>
