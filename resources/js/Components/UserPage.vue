<template>
    <div class="max-w-6xl mx-auto p-6 space-y-6">
        <div class="bg-white rounded-lg shadow-lg p-6">
            <div class="text-center mb-6">
                <h1 class="text-2xl font-bold">{{ page.title }}</h1>
                <div class="text-gray-600 mt-2">{{ page.description }}</div>
                <div class="text-xl font-semibold mt-4">
                    ${{ numberFormat(stats.total_revenue) }} / $1,000,000
                </div>
            </div>

            <!-- Cube Grid -->
            <div class="bg-white p-4 rounded-lg shadow-inner mb-6">
                <cube-grid
                    :completedCubes="stats.completed_cubes"
                    @cube-hover="handleCubeHover"
                />
            </div>

            <!-- Project Stats -->
            <div class="space-y-4">
                <h2 class="text-lg font-semibold">Projects</h2>
                <div class="grid gap-4">
                    <div
                        v-for="project in stats.projects"
                        :key="project.id"
                        class="bg-gray-50 p-4 rounded-lg"
                    >
                        <div class="flex justify-between items-center">
                            <span class="font-medium">{{ project.name }}</span>
                            <span>${{ numberFormat(project.total) }}</span>
                        </div>
                        <div
                            class="mt-2 h-2 bg-gray-200 rounded-full overflow-hidden"
                        >
                            <div
                                class="h-full bg-green-500"
                                :style="{ width: `${(project.total / 1000000) * 100}%` }"
                            />
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import axios from 'axios'
import CubeGrid from './CubeGrid.vue'

const props = defineProps({
    slug: {
        type: String,
        required: true
    }
})

const page = ref({})
const stats = ref({
    total_revenue: 0,
    completed_cubes: 0,
    progress_to_next_cube: 0,
    projects: []
})

const fetchPage = async () => {
    const response = await axios.get(`/api/page/${props.slug}`)
    page.value = response.data.page
    stats.value = response.data.stats
}

const numberFormat = (value) => {
    return new Intl.NumberFormat('en-US').format(value)
}

onMounted(fetchPage)
</script>
