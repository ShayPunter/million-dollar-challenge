<template>
    <div class="max-w-6xl mx-auto p-6 space-y-6">
        <div class="bg-white rounded-lg shadow-lg p-6">
            <div class="text-center mb-6">
                <h1 class="text-2xl font-bold">$1M Challenge</h1>
                <div class="text-xl font-semibold">
                    ${{ numberFormat(totalRevenue) }} / $1,000,000
                </div>
                <div class="text-sm text-gray-500">
                    Each cube represents $1,000
                </div>
            </div>

            <!-- Cube Grid -->
            <div class="bg-white p-4 rounded-lg shadow-inner mb-6">
                <cube-grid
                    :completedCubes="completedCubes"
                    @cube-hover="handleCubeHover"
                />
            </div>

            <!-- Stats -->
            <div class="grid grid-cols-2 gap-4 text-center mb-6">
                <div class="p-4 bg-gray-50 rounded-lg">
                    <div class="text-2xl font-bold">{{ completedCubes }}</div>
                    <div class="text-sm text-gray-500">Cubes Completed</div>
                </div>
                <div class="p-4 bg-gray-50 rounded-lg">
                    <div class="text-2xl font-bold">${{ progressToNextCube.toFixed(2) }}</div>
                    <div class="text-sm text-gray-500">Progress to Next Cube</div>
                </div>
            </div>

            <!-- Revenue Form -->
            <revenue-form @revenue-added="handleRevenueAdded" />

            <!-- Recent Entries -->
            <revenue-list :revenues="revenues" />
        </div>
    </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import axios from 'axios'
import CubeGrid from './CubeGrid.vue'
import RevenueForm from './RevenueForm.vue'
import RevenueList from './RevenueList.vue'

const props = defineProps({
    challengeId: {
        type: String,
        required: true
    }
})

const totalRevenue = ref(0)
const revenues = ref([])

const completedCubes = computed(() => Math.floor(totalRevenue.value / 1000))
const progressToNextCube = computed(() => totalRevenue.value % 1000)

const fetchProgress = async () => {
    const response = await axios.get(`/api/v1/challenges/${props.challengeId}/progress`)
    totalRevenue.value = response.data.total_revenue
}

const fetchRevenues = async () => {
    const response = await axios.get(`/api/v1/challenges/${props.challengeId}/revenues`)
    revenues.value = response.data
}

const handleRevenueAdded = async (revenueData) => {
    await axios.post(`/api/v1/challenges/${props.challengeId}/revenues`, revenueData)
    await fetchProgress()
    await fetchRevenues()
}

const numberFormat = (value) => {
    return new Intl.NumberFormat('en-US').format(value)
}

onMounted(() => {
    fetchProgress()
    fetchRevenues()
})
</script>
