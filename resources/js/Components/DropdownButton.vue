<script setup>
defineProps({
    disabled: {
        type: Boolean,
        default: false,
    },
    type: {
        type: String,
        default: 'button',
    },
})
</script>

<template>
    <button
        :type="type"
        :disabled="disabled"
        class="block w-full px-4 py-2 text-start text-sm font-medium leading-4 text-gray-500 transition duration-150 ease-in-out hover:bg-gray-100 focus:bg-gray-100 focus:outline-none disabled:opacity-50 disabled:pointer-events-none"
    >
        <slot />
    </button>
</template>
