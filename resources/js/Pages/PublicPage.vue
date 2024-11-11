<template>
    <Head>
        <title>{{ stats.user.name }}'s $1M Grid</title>
        <meta name="description" content="{{ stats.user.name }}'s has made ${{ numberFormat(stats.total_revenue) }} on their journey, view their grid and create your own.">
        <meta name="robots" content="noindex,nofollow">
    </Head>

    <div class="min-h-screen bg-gray-100 dark:bg-vulcan-950">

    <div class="fixed bottom-4 right-4 z-50">
        <a
            href="https://mymilliondollargrid.com"
            target="_blank"
            rel="noopener noreferrer"
            class="inline-flex items-center space-x-1 text-xs text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 bg-white dark:bg-gray-800 rounded-full px-3 py-1 shadow-lg transition-all duration-200 hover:shadow-xl"
        >
            <span>Create Your</span>
            <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 text-red-500" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M3.172 5.172a4 4 0 015.656 0L10 6.343l1.172-1.171a4 4 0 115.656 5.656L10 17.657l-6.828-6.829a4 4 0 010-5.656z" clip-rule="evenodd" />
            </svg>
            <span>Million Dollar Grid</span>
        </a>
    </div>

        <!-- Header -->
        <header class="bg-white dark:bg-vulcan-950">
            <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                <h1 class="text-3xl text-center font-bold text-gray-900 dark:text-gray-100">
                    {{ page.title }}
                </h1>
                <p class="mt-2 text-center text-gray-600 dark:text-gray-400">
                    {{
                        page.description
                    }}
                </p>
            </div>
        </header>

        <main><!-- Add this to your PublicPage.vue, right after the opening body div -->
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <!-- Progress Overview -->
                <div class="overflow-hidden p-6 mb-8">

                    <!-- Cube Grid -->
                    <div>
                        <div class="p-8 rounded-lg overflow-auto">
                            <div class="flex justify-center">
                                <div class="inline-grid grid-cols-20 lg:grid-cols-40 gap-[3px]">
                                    <div
                                        v-for="i in 1000"
                                        :key="i"
                                        class="w-3 h-3 relative"
                                    >
                                        <div
                                            v-if="i <= stats.completed_cubes"
                                            class="absolute top-0 left-0 w-full h-full z-10 animate-big-pulse bg-green-500 dark:bg-green-600 pointer-events-none"
                                        />
                                        <div
                                            class="block w-full h-full relative group transition-all duration-200"
                                            :class="[
                            i <= stats.completed_cubes
                                ? 'bg-green-500 dark:bg-green-600 animate-scale-in'
                                : 'bg-gray-200 dark:bg-gray-700'
                        ]"
                                            :style="{ 'animation-delay': `${i * 10}ms` }"
                                        >
                                            <div class="opacity-0 group-hover:opacity-100 scale-0 group-hover:scale-100 transition duration-200 absolute bottom-full left-1/2 transform -translate-x-1/2 mb-2 px-2 py-1 text-xs text-white bg-gray-700 rounded-md pointer-events-none whitespace-nowrap z-20">
                                                <div class="absolute bottom-0 left-1/2 transform -translate-x-1/2 translate-y-1/2 w-2 h-2 bg-gray-700 rotate-45"></div>
                                                ${{ numberFormat(i * 1000) }}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="flex flex-col max-w-[37rem] mx-auto lg:flex-row gap-4 lg:gap-0 items-center justify-between mt-2">
                                <p class="text-sm text-gray-700 dark:text-gray-400"><strong class="dark:text-gray-200">${{ numberFormat(stats.total_revenue) }}</strong> of $1,000,000 goal</p>
                                <p class="text-sm text-gray-700 dark:text-gray-400"><span class="inline-block w-2 h-2 bg-green-500 rounded-full mr-1 animate-pulse"></span><strong class="dark:text-gray-200 mr-1">Last Update:</strong>{{ formatLastUpdate(stats.last_updated.updated_at) }}</p>
                            </div>
                            <p class="mt-4 text-sm text-center font-medium text-gray-500 dark:text-gray-400 mb-4">Each cube represents $1,000</p>
                        </div>
                    </div>
                </div>

                <!-- Projects Breakdown -->
                <div class="max-w-[37rem] px-14 mx-auto overflow-hidden shadow-xl sm:rounded-lg">
                    <h2 class="text-xl font-semibold text-gray-900 dark:text-gray-100 mb-6">Projects/Categories</h2>

                    <div class="space-y-2">
                        <div
                            v-for="project in stats.projects"
                            :key="project.id"
                            class="bg-gray-50 dark:bg-vulcan-900 rounded-lg p-4"
                        >
                            <div class="flex justify-between items-center">
                                <span class="font-medium text-gray-900 dark:text-gray-100">{{ project.name }}</span>
                                <span class="text-gray-700 dark:text-gray-100">${{ numberFormat(project.total) }}</span>
                            </div>
                        </div>

                        <div v-if="stats.projects.length === 0" class="text-center text-gray-600 dark:text-gray-400">
                            No projects yet
                        </div>
                    </div>
                </div>
            </div>

            <p class="mt-8 text-center text-gray-600 dark:text-gray-400">
                My Million Dollar Grid. Find out more about the million dollar challenge: <a href="https://www.florin-pop.com/blog/one-million/" class="text-green-600">Florin Pop Million Dollar Challenge.</a>
            </p>

            <p class="mt-8 pb-8 text-center text-gray-600 dark:text-gray-400">
                Created with <3 / Shay Punter
            </p>
        </main>
    </div>
</template>

<script setup>
import {Head} from "@inertiajs/vue3";

defineProps({
    page: {
        type: Object,
        required: true
    },
    stats: {
        type: Object,
        required: true,
        default: () => ({
            total_revenue: 0,
            completed_cubes: 0,
            progress_to_next_cube: 0,
            projects: []
        })
    }
})

const numberFormat = (value) => {
    return new Intl.NumberFormat('en-US').format(value)
}

const formatLastUpdate = (date) => {
    const now = new Date()
    const updateDate = new Date(date)
    const diffInHours = Math.floor((now - updateDate) / (1000 * 60 * 60))
    const diffInDays = Math.floor(diffInHours / 24)

    if (diffInHours < 1) {
        return 'less than an hour ago'
    } else if (diffInHours < 24) {
        return `${diffInHours} ${diffInHours === 1 ? 'hour' : 'hours'} ago`
    } else if (diffInDays < 7) {
        return `${diffInDays} ${diffInDays === 1 ? 'day' : 'days'} ago`
    } else {
        return updateDate.toLocaleDateString('en-US', {
            year: 'numeric',
            month: 'short',
            day: 'numeric'
        })
    }
}
</script>

<style>
@keyframes scale-in {
    from {
        transform: scale(0);
        opacity: 0;
    }
    to {
        transform: scale(1);
        opacity: 1;
    }
}

@keyframes big-pulse {
    0% {
        opacity: 0;
        transform: scale(1);
    }
    50% {
        opacity: 0.3;
        transform: scale(1.3);
    }
    100% {
        opacity: 0;
        transform: scale(1.5);
    }
}

.animate-scale-in {
    animation: scale-in 0.5s ease-out forwards;
}

.animate-big-pulse {
    animation: big-pulse 2s ease-in-out infinite;
}
</style>
