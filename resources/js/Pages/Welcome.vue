<template>
    <Head>
        <title>The Million Dollar Grid Tracker</title>
        <meta name="description" content="Create your own unique million dollar grid and share with the world your progress to a million dollars.">
    </Head>
    <div class="bg-vulcan-50 dark:bg-vulcan-950">

        <nav class="bg-white dark:bg-vulcan-950 border-b border-vulcan-100 dark:border-vulcan-800">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-16 items-center">
                    <!-- Logo Section -->
                    <div class="flex items-center z-10">
                        <a class="flex items-center">
                            <img
                                class="h-8 w-auto"
                                src="/logo1m.png"
                                alt="MyMillionDollarGrid"
                            />
                            <span class="ml-3 text-lg font-semibold text-vulcan-900 dark:text-vulcan-50">
                        MyMillionDollarGrid
                    </span>
                        </a>
                    </div>

                    <!-- Navigation Links -->
                    <div class="flex items-center space-x-4 z-10">
                        <a
                            href="/login"
                            class="text-vulcan-600 dark:text-vulcan-300 hover:text-vulcan-900 dark:hover:text-vulcan-100 px-3 py-2 text-sm font-medium"
                        >
                            Log in
                        </a>
                        <a
                            href="/register"
                            class="bg-vulcan-600 text-white hover:bg-vulcan-500 px-3 py-2 rounded-md text-sm font-medium"
                        >
                            Get Started
                        </a>
                    </div>
                </div>
            </div>
        </nav>

        <!-- Hero Section -->
        <div class="relative isolate">
            <!-- Background gradient effect -->
            <div class="absolute inset-x-0 -top-40 -z-10 transform-gpu overflow-hidden blur-3xl sm:-top-80">
                <div class="relative left-[calc(50%-11rem)] aspect-[1155/678] w-[36.125rem] -translate-x-1/2 rotate-[30deg] bg-gradient-to-tr from-vulcan-400 to-vulcan-600 opacity-20 sm:left-[calc(50%-30rem)] sm:w-[72.1875rem]"></div>
            </div>

            <div class="mx-auto max-w-7xl px-6 py-24 sm:py-32 lg:px-8">
                <div class="grid lg:grid-cols-2 gap-12 items-center">
                    <!-- Left side: Text content -->
                    <div>
                        <h1 class="text-4xl font-bold tracking-tight text-vulcan-900 dark:text-vulcan-50 sm:text-6xl">
                            Track Your Journey to $1,000,000
                        </h1>
                        <p class="mt-6 text-lg leading-8 text-vulcan-600 dark:text-vulcan-300">
                            Visualize your progress, track multiple revenue streams, and share your journey to becoming a millionaire.
                        </p>
                        <div class="mt-10 flex items-center gap-x-6">
                            <a
                                href="/register"
                                class="rounded-md bg-vulcan-600 px-6 py-3 text-sm font-semibold text-white shadow-sm hover:bg-vulcan-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-vulcan-600"
                            >
                                Get Started
                            </a>
                            <a
                                href="/login"
                                class="text-sm font-semibold leading-6 text-vulcan-900 dark:text-vulcan-100"
                            >
                                Log in <span aria-hidden="true">→</span>
                            </a>
                        </div>
                    </div>

                    <!-- Right side: Hero Image -->
                    <div class="relative rounded-xl overflow-hidden shadow-xl">
                        <img
                            src="/hero.png"
                            alt="Million Dollar Grid Dashboard"
                            class="w-full h-auto"
                        />
                    </div>
                </div>
            </div>
        </div>

        <!-- Feature Section -->
        <div class="py-24 sm:py-32 bg-white dark:bg-vulcan-900">
            <div class="mx-auto max-w-7xl px-6 lg:px-8">
                <div class="mx-auto max-w-2xl lg:text-center">
                    <h2 class="text-base font-semibold leading-7 text-vulcan-600 dark:text-vulcan-300">Track & Share</h2>
                    <p class="mt-2 text-3xl font-bold tracking-tight text-vulcan-900 dark:text-vulcan-50 sm:text-4xl">
                        Everything you need to track your million dollar journey
                    </p>
                </div>
                <dl class="mx-auto mt-16 grid max-w-xl grid-cols-1 gap-x-8 gap-y-16 lg:max-w-none lg:grid-cols-3">
                    <div class="flex flex-col">
                        <dt class="flex items-center gap-x-3 text-base font-semibold leading-7 text-vulcan-900 dark:text-vulcan-50">
                            <ChartBarSquareIcon class="h-5 w-5 flex-none text-vulcan-600 dark:text-vulcan-400" />
                            Visual Progress Tracking
                        </dt>
                        <dd class="mt-4 flex flex-auto flex-col text-base leading-7 text-vulcan-600 dark:text-vulcan-300">
                            <p class="flex-auto">Watch your progress with our unique cube grid visualization. Each cube represents $1,000 towards your goal.</p>
                        </dd>
                    </div>

                    <div class="flex flex-col">
                        <dt class="flex items-center gap-x-3 text-base font-semibold leading-7 text-vulcan-900 dark:text-vulcan-50">
                            <FolderIcon class="h-5 w-5 flex-none text-vulcan-600 dark:text-vulcan-400" />
                            Multiple Projects
                        </dt>
                        <dd class="mt-4 flex flex-auto flex-col text-base leading-7 text-vulcan-600 dark:text-vulcan-300">
                            <p class="flex-auto">Organize your revenue streams into different projects. Track and manage each source separately.</p>
                        </dd>
                    </div>

                    <div class="flex flex-col">
                        <dt class="flex items-center gap-x-3 text-base font-semibold leading-7 text-vulcan-900 dark:text-vulcan-50">
                            <ShareIcon class="h-5 w-5 flex-none text-vulcan-600 dark:text-vulcan-400" />
                            Share Your Journey
                        </dt>
                        <dd class="mt-4 flex flex-auto flex-col text-base leading-7 text-vulcan-600 dark:text-vulcan-300">
                            <p class="flex-auto">Get your own public page to share your progress with the world and inspire others.</p>
                        </dd>
                    </div>
                </dl>
            </div>
        </div>

        <!-- Live Example Section -->
        <div class="relative isolate overflow-hidden bg-vulcan-50 dark:bg-vulcan-900 py-24 sm:py-32">
            <div class="mx-auto max-w-7xl px-6 lg:px-8">
                <div class="mx-auto max-w-2xl lg:mx-0">
                    <h2 class="text-3xl font-bold tracking-tight text-vulcan-900 dark:text-vulcan-50 sm:text-4xl">
                        See it in action
                    </h2>
                    <p class="mt-6 text-lg leading-8 text-vulcan-600 dark:text-vulcan-300">
                        Check out a live example of someone tracking their journey to $1M.
                    </p>
                </div>

                <!-- Live Example Frame -->
                <div class="mt-16 rounded-xl bg-white dark:bg-vulcan-800 p-8 shadow-xl">
                    <div class="aspect-[16/9] overflow-hidden rounded-lg">
                        <iframe
                            src="https://mymilliondollargrid.com/grid/shaypunter"
                            class="w-full h-full"
                            frameborder="0"
                        ></iframe>
                    </div>
                    <div class="mt-4 text-center">
                        <a
                            href="https://mymilliondollargrid.com/grid/shaypunter"
                            target="_blank"
                            class="text-vulcan-600 dark:text-vulcan-300 hover:text-vulcan-500 dark:hover:text-vulcan-200"
                        >
                            View full page →
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- CTA Section -->
        <div class="py-24 sm:py-32 bg-white dark:bg-vulcan-900">
            <div class="mx-auto max-w-7xl px-6 lg:px-8">
                <div class="mx-auto max-w-2xl text-center">
                    <h2 class="text-3xl font-bold tracking-tight text-vulcan-900 dark:text-vulcan-50 sm:text-4xl">
                        Ready to start your journey?
                    </h2>
                    <p class="mx-auto mt-6 max-w-xl text-lg leading-8 text-vulcan-600 dark:text-vulcan-300">
                        Create your account now and begin tracking your progress towards one million dollars.
                    </p>
                    <div class="mt-10 flex items-center justify-center gap-x-6">
                        <a
                            href="/register"
                            class="rounded-md bg-vulcan-600 px-6 py-3 text-sm font-semibold text-white shadow-sm hover:bg-vulcan-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-vulcan-600"
                        >
                            Get Started for Free
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <footer class="bg-vulcan-50 dark:bg-vulcan-950">
            <div class="mx-auto max-w-7xl px-6 py-12 md:flex md:items-center md:justify-between lg:px-8">
                <div class="flex justify-center space-x-6 md:order-2">
                    <a href="/login" class="text-vulcan-500 hover:text-vulcan-600 dark:text-vulcan-400 dark:hover:text-vulcan-300">
                        Login
                    </a>
                    <a href="/register" class="text-vulcan-500 hover:text-vulcan-600 dark:text-vulcan-400 dark:hover:text-vulcan-300">
                        Sign up
                    </a>
                </div>
                <div class="mt-8 md:order-1 md:mt-0">
                    <p class="text-center text-xs leading-5 text-vulcan-500 dark:text-vulcan-400">
                        &copy; {{ new Date().getFullYear() }} MyMillionDollarGrid. All rights reserved.
                    </p>

                    <a href="/tos" class="text-center underline text-xs leading-5 text-vulcan-500 dark:text-vulcan-400">
                        Privacy Policy & Terms and Conditions
                    </a>
                </div>
            </div>
        </footer>
    </div>
</template>
<script setup>
import { Link } from '@inertiajs/vue3'
import { ChartBarSquareIcon, FolderIcon, ShareIcon } from '@heroicons/vue/24/outline'
import {Head} from "@inertiajs/vue3";
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
