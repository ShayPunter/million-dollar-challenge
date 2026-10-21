<script setup>
import { computed, ref } from 'vue'
import { Head } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import TextInput from '@/Components/TextInput.vue'

const props = defineProps({
    users: Array,
    stats: Object,
})

const search = ref('')

const filteredUsers = computed(() => {
    const term = search.value.trim().toLowerCase()
    if (!term) return props.users

    return props.users.filter(user =>
        [user.name, user.email, user.page?.slug, user.page?.title]
            .filter(Boolean)
            .some(value => value.toLowerCase().includes(term))
    )
})

const formatNumber = (value) => {
    return new Intl.NumberFormat('en-US').format(value)
}

const formatDate = (date) => {
    return new Date(date).toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'short',
        day: 'numeric',
    })
}
</script>

<template>
    <Head>
        <title>Admin</title>
    </Head>

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                Admin
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
                <!-- Stats -->
                <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
                    <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-4">
                        <div class="text-sm text-gray-500 dark:text-gray-400">Users</div>
                        <div class="mt-1 text-2xl font-bold text-gray-900 dark:text-gray-100">
                            {{ formatNumber(stats.totalUsers) }}
                        </div>
                    </div>
                    <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-4">
                        <div class="text-sm text-gray-500 dark:text-gray-400">New this week</div>
                        <div class="mt-1 text-2xl font-bold text-gray-900 dark:text-gray-100">
                            {{ formatNumber(stats.newThisWeek) }}
                        </div>
                    </div>
                    <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-4">
                        <div class="text-sm text-gray-500 dark:text-gray-400">Pages created</div>
                        <div class="mt-1 text-2xl font-bold text-gray-900 dark:text-gray-100">
                            {{ formatNumber(stats.totalPages) }}
                        </div>
                    </div>
                    <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-4">
                        <div class="text-sm text-gray-500 dark:text-gray-400">Revenue tracked</div>
                        <div class="mt-1 text-2xl font-bold text-gray-900 dark:text-gray-100">
                            ${{ formatNumber(stats.totalRevenue) }}
                        </div>
                    </div>
                </div>

                <!-- Users table -->
                <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg">
                    <div class="p-4 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">
                            Users
                            <span class="text-sm font-normal text-gray-500 dark:text-gray-400">
                                ({{ filteredUsers.length }})
                            </span>
                        </h3>
                        <TextInput
                            v-model="search"
                            type="search"
                            class="w-full sm:w-72"
                            placeholder="Search name, email or page"
                        />
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700 text-sm">
                            <thead class="bg-gray-50 dark:bg-gray-900/40">
                                <tr class="text-left text-gray-500 dark:text-gray-400">
                                    <th class="px-4 py-3 font-medium">User</th>
                                    <th class="px-4 py-3 font-medium">Sign-in</th>
                                    <th class="px-4 py-3 font-medium">Page</th>
                                    <th class="px-4 py-3 font-medium text-right">Projects</th>
                                    <th class="px-4 py-3 font-medium text-right">Revenue</th>
                                    <th class="px-4 py-3 font-medium">Joined</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                                <tr
                                    v-for="user in filteredUsers"
                                    :key="user.id"
                                    class="text-gray-900 dark:text-gray-100"
                                >
                                    <td class="px-4 py-3">
                                        <div class="font-medium">{{ user.name }}</div>
                                        <div class="text-gray-500 dark:text-gray-400">{{ user.email }}</div>
                                    </td>
                                    <td class="px-4 py-3">
                                        <span
                                            class="inline-flex rounded-full px-2 py-0.5 text-xs font-medium"
                                            :class="user.google
                                                ? 'bg-blue-100 text-blue-800 dark:bg-blue-900/50 dark:text-blue-300'
                                                : 'bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300'"
                                        >
                                            {{ user.google ? 'Google' : 'Email' }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3">
                                        <a
                                            v-if="user.page"
                                            :href="route('public-page', user.page.slug)"
                                            target="_blank"
                                            class="text-indigo-600 hover:underline dark:text-indigo-400"
                                        >
                                            /grid/{{ user.page.slug }}
                                        </a>
                                        <span v-else class="text-gray-400 dark:text-gray-500">None</span>
                                    </td>
                                    <td class="px-4 py-3 text-right">{{ user.projectsCount }}</td>
                                    <td class="px-4 py-3 text-right">${{ formatNumber(user.totalRevenue) }}</td>
                                    <td class="px-4 py-3 whitespace-nowrap">{{ formatDate(user.createdAt) }}</td>
                                </tr>
                                <tr v-if="filteredUsers.length === 0">
                                    <td colspan="6" class="px-4 py-8 text-center text-gray-500 dark:text-gray-400">
                                        No users found.
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
