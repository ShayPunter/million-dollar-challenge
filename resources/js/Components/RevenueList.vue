<template>
    <div class="space-y-2">
        <h3 class="font-medium">Recent Entries</h3>
        <div class="space-y-2 max-h-48 overflow-y-auto">
            <div
                v-for="entry in revenues"
                :key="entry.id"
                class="flex justify-between p-2 bg-gray-50 rounded"
            >
                <div>
                    <div class="font-medium">
                        ${{ numberFormat(entry.amount) }}
                    </div>
                    <div v-if="entry.description" class="text-sm text-gray-500">
                        {{ entry.description }}
                    </div>
                </div>
                <div class="text-sm text-gray-500">
                    {{ formatDate(entry.created_at) }}
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
defineProps({
    revenues: {
        type: Array,
        required: true
    }
})

const numberFormat = (value) => {
    return new Intl.NumberFormat('en-US').format(value)
}

const formatDate = (date) => {
    return new Date(date).toLocaleDateString()
}
</script>
