<template>
    <div
        v-if="modelValue"
        class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4"
    >
        <div class="bg-white rounded-lg p-6 max-w-md w-full">
            <div class="mb-4">
                <slot></slot>
            </div>
            <div class="flex justify-end">
                <button
                    @click="$emit('update:modelValue', false)"
                    class="px-4 py-2 text-gray-600 hover:text-gray-800"
                >
                    Cancel
                </button>
            </div>
        </div>
    </div>
</template>

<script setup>
defineProps({
    modelValue: {
        type: Boolean,
        required: true
    }
})

defineEmits(['update:modelValue'])
</script>
