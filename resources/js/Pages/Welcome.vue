<template>
    <div class="bg-gray-50 dark:bg-gray-900">
        <!-- Hero Section -->
        <div class="relative isolate">
            <div class="mx-auto max-w-7xl px-6 py-24 sm:py-32 lg:px-8">
                <div class="text-center">
                    <h1 class="text-4xl font-bold tracking-tight text-gray-900 dark:text-gray-100 sm:text-6xl">
                        Track Your Journey to $1,000,000
                    </h1>
                    <p class="mt-6 text-lg leading-8 text-gray-600 dark:text-gray-400">
                        Visualize your progress, track multiple revenue streams, and share your journey to becoming a millionaire.
                    </p>
                    <div class="mt-10 flex items-center justify-center gap-x-6">
                        <Link
                            :href="route('register')"
                            class="rounded-md bg-indigo-600 px-6 py-3 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600"
                        >
                            Get Started
                        </Link>
                        <Link
                            :href="route('login')"
                            class="text-sm font-semibold leading-6 text-gray-900 dark:text-gray-100"
                        >
                            Log in <span aria-hidden="true">→</span>
                        </Link>
                    </div>
                </div>
            </div>
        </div>

        <!-- Feature Section -->
        <div class="py-24 sm:py-32">
            <div class="mx-auto max-w-7xl px-6 lg:px-8">
                <div class="mx-auto max-w-2xl lg:text-center">
                    <h2 class="text-base font-semibold leading-7 text-indigo-600 dark:text-indigo-400">Track & Share</h2>
                    <p class="mt-2 text-3xl font-bold tracking-tight text-gray-900 dark:text-gray-100 sm:text-4xl">
                        Everything you need to track your million dollar journey
                    </p>
                </div>
                <div class="mx-auto mt-16 max-w-2xl sm:mt-20 lg:mt-24 lg:max-w-none">
                    <dl class="grid max-w-xl grid-cols-1 gap-x-8 gap-y-16 lg:max-w-none lg:grid-cols-3">
                        <!-- Feature 1 -->
                        <div class="flex flex-col">
                            <dt class="flex items-center gap-x-3 text-base font-semibold leading-7 text-gray-900 dark:text-gray-100">
                                <ChartBarSquareIcon class="h-5 w-5 flex-none text-indigo-600 dark:text-indigo-400" />
                                Visual Progress Tracking
                            </dt>
                            <dd class="mt-4 flex flex-auto flex-col text-base leading-7 text-gray-600 dark:text-gray-400">
                                <p class="flex-auto">Watch your progress with our unique cube grid visualization. Each cube represents $1,000 towards your goal.</p>
                            </dd>
                        </div>

                        <!-- Feature 2 -->
                        <div class="flex flex-col">
                            <dt class="flex items-center gap-x-3 text-base font-semibold leading-7 text-gray-900 dark:text-gray-100">
                                <FolderIcon class="h-5 w-5 flex-none text-indigo-600 dark:text-indigo-400" />
                                Multiple Projects
                            </dt>
                            <dd class="mt-4 flex flex-auto flex-col text-base leading-7 text-gray-600 dark:text-gray-400">
                                <p class="flex-auto">Organize your revenue streams into different projects. Track and manage each source separately.</p>
                            </dd>
                        </div>

                        <!-- Feature 3 -->
                        <div class="flex flex-col">
                            <dt class="flex items-center gap-x-3 text-base font-semibold leading-7 text-gray-900 dark:text-gray-100">
                                <ShareIcon class="h-5 w-5 flex-none text-indigo-600 dark:text-indigo-400" />
                                Share Your Journey
                            </dt>
                            <dd class="mt-4 flex flex-auto flex-col text-base leading-7 text-gray-600 dark:text-gray-400">
                                <p class="flex-auto">Get your own public page to share your progress with the world and inspire others.</p>
                            </dd>
                        </div>
                    </dl>
                </div>
            </div>
        </div>

        <!-- Demo Section -->
        <div class="relative isolate overflow-hidden bg-white dark:bg-gray-800 py-24 sm:py-32">
            <div class="mx-auto max-w-7xl px-6 lg:px-8">
                <div class="mx-auto max-w-2xl lg:mx-0">
                    <h2 class="text-3xl font-bold tracking-tight text-gray-900 dark:text-gray-100 sm:text-4xl">See it in action</h2>
                    <p class="mt-6 text-lg leading-8 text-gray-600 dark:text-gray-400">
                        Check out how your progress will be visualized with our unique cube grid system.
                    </p>
                </div>

                <!-- Demo Grid -->
                <div class="mt-16 rounded-xl bg-gray-50 dark:bg-gray-900 p-8">
                    <div class="flex justify-center">
                        <div class="inline-grid grid-cols-40 gap-[3px]">
                            <div
                                v-for="i in 1000"
                                :key="i"
                                class="w-3 h-3 relative"
                            >
                                <div
                                    v-if="i <= 100"
                                    class="absolute top-0 left-0 w-full h-full z-10 animate-big-pulse bg-green-500 dark:bg-green-600 pointer-events-none"
                                />
                                <div
                                    class="block w-full h-full relative group transition-all duration-200"
                                    :class="[
                                        i <= 100
                                            ? 'bg-green-500 dark:bg-green-600 animate-scale-in'
                                            : 'bg-gray-200 dark:bg-gray-700'
                                    ]"
                                    :style="{ 'animation-delay': `${i * 10}ms` }"
                                >
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- CTA Section -->
        <div class="py-24 sm:py-32">
            <div class="mx-auto max-w-7xl px-6 lg:px-8">
                <div class="mx-auto max-w-2xl text-center">
                    <h2 class="text-3xl font-bold tracking-tight text-gray-900 dark:text-gray-100 sm:text-4xl">
                        Ready to start your journey?
                    </h2>
                    <p class="mx-auto mt-6 max-w-xl text-lg leading-8 text-gray-600 dark:text-gray-400">
                        Create your account now and begin tracking your progress towards one million dollars.
                    </p>
                    <div class="mt-10 flex items-center justify-center gap-x-6">
                        <Link
                            :href="route('register')"
                            class="rounded-md bg-indigo-600 px-6 py-3 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600"
                        >
                            Get Started for Free
                        </Link>
                    </div>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <footer class="bg-white dark:bg-gray-800">
            <div class="mx-auto max-w-7xl px-6 py-12 md:flex md:items-center md:justify-between lg:px-8">
                <div class="flex justify-center space-x-6 md:order-2">
                    <Link :href="route('login')" class="text-gray-400 hover:text-gray-500 dark:hover:text-gray-300">
                        Login
                    </Link>
                    <Link :href="route('register')" class="text-gray-400 hover:text-gray-500 dark:hover:text-gray-300">
                        Sign up
                    </Link>
                </div>
                <div class="mt-8 md:order-1 md:mt-0">
                    <p class="text-center text-xs leading-5 text-gray-500 dark:text-gray-400">
                        &copy; {{ new Date().getFullYear() }} MyMillionDollarGrid. All rights reserved.
                    </p>
                </div>
            </div>
        </footer>
    </div>
</template>

<script setup>
import { Link } from '@inertiajs/vue3'
import { ChartBarSquareIcon, FolderIcon, ShareIcon } from '@heroicons/vue/24/outline'
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
