<template>
    <div class="space-y-1">
        <div
            v-for="row in rows"
            :key="row"
            class="flex justify-center"
        >
            <div
                v-for="col in cubesPerRow"
                :key="col"
                class="w-4 h-4 m-0.5 rounded-sm transition-all duration-300"
                :class="getCubeClasses(getCubeIndex(row, col))"
                @mouseover="() => emitCubeHover(getCubeIndex(row, col))"
            />
        </div>
    </div>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
    completedCubes: {
        type: Number,
        required: true
    }
})

const emit = defineEmits(['cube-hover'])

const cubesPerRow = 25
const totalCubes = 1000
const rows = Math.ceil(totalCubes / cubesPerRow)

const getCubeIndex = (row, col) => (row - 1) * cubesPerRow + (col - 1)

const getCubeClasses = (index) => ({
    'bg-green-500 shadow-lg shadow-green-500/50 animate-pulse': index < props.completedCubes,
    'bg-gray-200': index >= props.completedCubes,
    'hover:scale-110 cursor-pointer': true
})

const emitCubeHover = (index) => {
    emit('cube-hover', (index + 1) * 1000)
}
</script>
