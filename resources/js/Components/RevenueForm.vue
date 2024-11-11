<template>
    <form @submit.prevent="handleSubmit" class="space-y-4">
        <div class="flex items-center gap-2">
            <span class="text-gray-500">$</span>
            <input
                v-model="amount"
                type="number"
                min="0"
                step="0.01"
                class="w-full px-4 py-2 border rounded-lg"
                placeholder="Enter amount"
            />
        </div>
        <input
            v-model="description"
            type="text"
            class="w-full px-4 py-2 border rounded-lg"
            placeholder="Description (optional)"
        />
        <button
            type="submit"
            :disabled="!amount"
            class="w-full px-4 py-2 bg-blue-500 text-white rounded-lg disabled:opacity-50"
        >
            Add Revenue
        </button>
    </form>
</template>

<script setup>
import { ref } from 'vue'

const emit = defineEmits(['revenue-added'])

const amount = ref('')
const description = ref('')

const handleSubmit = () => {
    emit('revenue-added', {
        amount: parseFloat(amount.value),
        description: description.value
    })
    amount.value = ''
    description.value = ''
}
</script>
