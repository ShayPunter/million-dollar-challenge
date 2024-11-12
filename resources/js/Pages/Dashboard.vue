<template>
    <Head>
        <title>$1M Dashboard</title>
    </Head>

    <AuthenticatedLayout title="Dashboard">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                $1M Challenge Dashboard
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <!-- Page Setup Form (if no page exists) -->
                <div v-if="!hasPage" class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg p-6">
                    <h2 class="text-xl font-bold mb-4 text-gray-900 dark:text-gray-100">Create Your $1M Challenge Page</h2>
                    <form @submit.prevent="form.post(route('user-page.store'))" class="space-y-4">
                        <div>
                            <InputLabel for="slug" value="Page URL" />
                            <div class="flex items-center mt-1">
                                <span class="text-gray-500 dark:text-gray-400 mr-2">mymilliondollargrid.com/grid/</span>
                                <TextInput
                                    id="slug"
                                    v-model="form.slug"
                                    type="text"
                                    class="flex-1"
                                    :class="{ 'border-red-500': form.errors.slug }"
                                    placeholder="your-url-slug"
                                />
                            </div>
                            <InputError :message="form.errors.slug" class="mt-2" />
                        </div>

                        <div>
                            <InputLabel for="title" value="Page Title" />
                            <TextInput
                                id="title"
                                v-model="form.title"
                                type="text"
                                class="mt-1 block w-full"
                                :class="{ 'border-red-500': form.errors.title }"
                            />
                            <InputError :message="form.errors.title" class="mt-2" />
                        </div>

                        <div>
                            <InputLabel for="description" value="Description" />
                            <textarea
                                id="description"
                                v-model="form.description"
                                class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                                :class="{ 'border-red-500': form.errors.description }"
                                rows="3"
                            />
                            <InputError :message="form.errors.description" class="mt-2" />
                        </div>

                        <PrimaryButton :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                            Create Page
                        </PrimaryButton>
                    </form>
                </div>

                <!-- Dashboard Content (if page exists) -->
                <template v-else>
                    <!-- Projects Section -->
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg p-6">
                        <div class="flex justify-between items-center mb-4">
                            <h2 class="text-xl font-bold text-gray-900 dark:text-gray-100">Projects</h2>
                            <PrimaryButton @click="showNewProjectModal = true">
                                Add Project
                            </PrimaryButton>
                        </div>

                        <!-- Projects List with Expandable Revenues -->
                        <div class="space-y-4">
                            <div
                                v-for="project in projects"
                                :key="project.id"
                                class="bg-gray-50 dark:bg-gray-700 rounded-lg overflow-hidden"
                            >
                                <!-- Project Header -->
                                <div
                                    class="p-4 flex justify-between items-center cursor-pointer"
                                    @click="toggleProject(project.id)"
                                >
                                    <div class="flex items-center gap-2">
                                        <ChevronDownIcon
                                            class="w-5 h-5 transition-transform duration-200 dark:text-white"
                                            :class="{ 'rotate-90': expandedProjects.includes(project.id) }"
                                        />
                                        <span class="font-medium text-gray-900 dark:text-gray-100">{{ project.name }}</span>
                                    </div>
                                    <div class="flex items-center gap-4">
                                        <span class="text-gray-700 dark:text-gray-300">${{ numberFormat(project.amount || 0) }}</span>
                                        <SecondaryButton @click.stop="openRevenueModal(project)">
                                            Add Revenue
                                        </SecondaryButton>

                                        <div class="project-menu" @click.stop>
                                            <button
                                                type="button"
                                                @click.stop="toggleProjectMenu(project.id)"
                                                class="p-1 rounded-full hover:bg-gray-200 dark:hover:bg-gray-600"
                                            >
                                                <EllipsisVerticalIcon class="w-5 h-5 text-gray-500 dark:text-gray-400" />
                                            </button>

                                            <!-- Project Actions Menu -->
                                            <div
                                                v-show="activeProjectMenu === project.id"
                                                class="right-0 mt-2 w-48 rounded-md shadow-lg bg-white dark:bg-gray-600 ring-1 ring-black ring-opacity-5 z-50"
                                                style="position: absolute; min-width: 12rem;"
                                            >
                                                <div class="py-1">
                                                    <button
                                                        type="button"
                                                        @click.stop="openEditProject(project)"
                                                        class="w-full text-left px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-500"
                                                    >
                                                        Edit
                                                    </button>
                                                    <button
                                                        type="button"
                                                        @click.stop="confirmDeleteProject(project)"
                                                        class="w-full text-left px-4 py-2 text-sm text-red-600 dark:text-red-400 hover:bg-gray-100 dark:hover:bg-gray-500"
                                                    >
                                                        Delete
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Revenue Entries -->
                                <div v-if="expandedProjects.includes(project.id)" class="border-t border-gray-200 dark:border-gray-600">
                                    <div class="p-4 space-y-2">
                                        <!-- Revenue Table -->
                                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-600">
                                            <thead class="bg-gray-50 dark:bg-gray-700">
                                            <tr>
                                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Date</th>
                                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Amount</th>
                                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Actions</th>
                                            </tr>
                                            </thead>
                                            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-600">
                                            <tr v-for="revenue in project.revenues" :key="revenue.id">
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                                    {{ formatDate(revenue.created_at) }}
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                                        <span v-if="editingRevenue?.id !== revenue.id">
                                                            ${{ numberFormat(revenue.amount) }}
                                                        </span>
                                                    <TextInput
                                                        v-else
                                                        v-model="editingRevenue.amount"
                                                        type="number"
                                                        step="0.01"
                                                        class="w-32"
                                                        @keyup.enter="updateRevenue"
                                                        @keyup.esc="cancelEdit"
                                                    />
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                                    <div class="flex justify-end gap-2">
                                                        <button
                                                            v-if="editingRevenue?.id !== revenue.id"
                                                            @click="startEdit(revenue)"
                                                            class="text-indigo-600 dark:text-indigo-400 hover:text-indigo-900 dark:hover:text-indigo-300"
                                                        >
                                                            Edit
                                                        </button>
                                                        <div v-else class="flex gap-2">
                                                            <button
                                                                @click="updateRevenue"
                                                                class="text-green-600 dark:text-green-400 hover:text-green-900 dark:hover:text-green-300"
                                                            >
                                                                Save
                                                            </button>
                                                            <button
                                                                @click="cancelEdit"
                                                                class="text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-300"
                                                            >
                                                                Cancel
                                                            </button>
                                                        </div>
                                                        <button
                                                            v-if="editingRevenue?.id !== revenue.id"
                                                            @click="confirmDelete(revenue)"
                                                            class="text-red-600 dark:text-red-400 hover:text-red-900 dark:hover:text-red-300"
                                                        >
                                                            Delete
                                                        </button>
                                                    </div>
                                                </td>
                                            </tr>
                                            <tr v-if="project.revenues.length === 0">
                                                <td colspan="3" class="px-6 py-4 text-center text-gray-500 dark:text-gray-400">
                                                    No revenue entries yet
                                                </td>
                                            </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </template>

                <!-- Add this section in your dashboard after the projects section -->
                <div v-if="hasPage" class="mt-8 bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg p-6">
                    <div class="flex justify-between items-center mb-6">
                        <h2 class="text-xl font-bold text-gray-900 dark:text-gray-100">Page Settings</h2>
                        <PrimaryButton @click="openPageSettingsModal">
                            Edit Page Settings
                        </PrimaryButton>
                    </div>

                    <div class="space-y-4">
                        <div>
                            <label class="text-sm font-medium text-gray-700 dark:text-gray-300">Page URL</label>
                            <div class="mt-1 flex items-center">
                                <span class="text-gray-500 dark:text-gray-400">mymilliondollargrid.com/grid/</span>
                                <span class="text-gray-900 dark:text-gray-100">{{ page.slug }}</span>
                            </div>
                        </div>

                        <div>
                            <label class="text-sm font-medium text-gray-700 dark:text-gray-300">Title</label>
                            <div class="mt-1 text-gray-900 dark:text-gray-100">{{ page.title }}</div>
                        </div>

                        <div>
                            <label class="text-sm font-medium text-gray-700 dark:text-gray-300">Description</label>
                            <div class="mt-1 text-gray-900 dark:text-gray-100">{{ page.description }}</div>
                        </div>
                    </div>
                </div>

                <!-- Add this modal for editing page settings -->
                <Modal :show="showPageSettingsModal" @close="closePageSettingsModal">
                    <div class="p-6">
                        <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                            Edit Page Settings
                        </h2>

                        <form @submit.prevent="updatePageSettings" class="mt-6">
                            <div class="space-y-4">
                                <div>
                                    <InputLabel for="edit_page_title" value="Page Title" />
                                    <TextInput
                                        id="edit_page_title"
                                        v-model="pageSettingsForm.title"
                                        type="text"
                                        class="mt-1 block w-full"
                                        :class="{ 'border-red-500': pageSettingsForm.errors.title }"
                                    />
                                    <InputError :message="pageSettingsForm.errors.title" class="mt-2" />
                                </div>

                                <div>
                                    <InputLabel for="edit_page_description" value="Description" />
                                    <textarea
                                        id="edit_page_description"
                                        v-model="pageSettingsForm.description"
                                        rows="3"
                                        class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                                        :class="{ 'border-red-500': pageSettingsForm.errors.description }"
                                    />
                                    <InputError :message="pageSettingsForm.errors.description" class="mt-2" />
                                </div>
                            </div>

                            <div class="mt-6 flex justify-end">
                                <SecondaryButton @click="closePageSettingsModal">
                                    Cancel
                                </SecondaryButton>

                                <PrimaryButton
                                    class="ml-3"
                                    :class="{ 'opacity-25': pageSettingsForm.processing }"
                                    :disabled="pageSettingsForm.processing"
                                >
                                    Update Settings
                                </PrimaryButton>
                            </div>
                        </form>
                    </div>
                </Modal>

                <!-- New Project Modal -->
                <Modal :show="showNewProjectModal" @close="showNewProjectModal = false">
                    <div class="p-6">
                        <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                            Create New Project
                        </h2>

                        <form @submit.prevent="submitProject" class="mt-6">
                            <div>
                                <InputLabel for="project_name" value="Project Name" />
                                <TextInput
                                    id="project_name"
                                    v-model="projectForm.name"
                                    type="text"
                                    class="mt-1 block w-full"
                                    :class="{ 'border-red-500': projectForm.errors.name }"
                                />
                                <InputError :message="projectForm.errors.name" class="mt-2" />
                            </div>

                            <div class="mt-6 flex justify-end">
                                <SecondaryButton @click="showNewProjectModal = false">
                                    Cancel
                                </SecondaryButton>

                                <PrimaryButton
                                    class="ml-3"
                                    :class="{ 'opacity-25': projectForm.processing }"
                                    :disabled="projectForm.processing"
                                >
                                    Create Project
                                </PrimaryButton>
                            </div>
                        </form>
                    </div>
                </Modal>

                <!-- Delete Confirmation Modal -->
                <Modal :show="showDeleteModal" @close="showDeleteModal = false">
                    <div class="p-6">
                        <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                            Delete Revenue Entry
                        </h2>

                        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                            Are you sure you want to delete this revenue entry? This action cannot be undone.
                        </p>

                        <div class="mt-6 flex justify-end">
                            <SecondaryButton @click="showDeleteModal = false">
                                Cancel
                            </SecondaryButton>

                            <DangerButton
                                class="ml-3"
                                :class="{ 'opacity-25': revenueDeletingForm.processing }"
                                :disabled="revenueDeletingForm.processing"
                                @click="deleteRevenue"
                            >
                                Delete Revenue
                            </DangerButton>
                        </div>
                    </div>
                </Modal>

                <!-- Add Revenue Modal -->
                <Modal :show="showRevenueModal" @close="showRevenueModal = false">
                    <div class="p-6">
                        <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                            Add Revenue
                        </h2>

                        <form @submit.prevent="submitRevenue" class="mt-6">
                            <div>
                                <InputLabel for="revenue_amount" value="Amount" />
                                <TextInput
                                    id="revenue_amount"
                                    v-model="revenueForm.amount"
                                    type="number"
                                    step="0.01"
                                    class="mt-1 block w-full"
                                    :class="{ 'border-red-500': revenueForm.errors.amount }"
                                />
                                <InputError :message="revenueForm.errors.amount" class="mt-2" />
                            </div>

                            <div class="mt-6 flex justify-end">
                                <SecondaryButton @click="showRevenueModal = false">
                                    Cancel
                                </SecondaryButton>

                                <PrimaryButton
                                    class="ml-3"
                                    :class="{ 'opacity-25': revenueForm.processing }"
                                    :disabled="revenueForm.processing"
                                >
                                    Add Revenue
                                </PrimaryButton>
                            </div>
                        </form>
                    </div>
                </Modal>

                <!-- Edit Project Modal -->
                <Modal :show="showEditProjectModal" @close="closeEditProjectModal">
                    <div class="p-6">
                        <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                            Edit Project
                        </h2>

                        <form @submit.prevent="updateProject" class="mt-6">
                            <div>
                                <InputLabel for="edit_project_name" value="Project Name" />
                                <TextInput
                                    id="edit_project_name"
                                    v-model="editProjectForm.name"
                                    type="text"
                                    class="mt-1 block w-full"
                                    :class="{ 'border-red-500': editProjectForm.errors.name }"
                                />
                                <InputError :message="editProjectForm.errors.name" class="mt-2" />
                            </div>

                            <div class="mt-6 flex justify-end">
                                <SecondaryButton @click="closeEditProjectModal">
                                    Cancel
                                </SecondaryButton>

                                <PrimaryButton
                                    class="ml-3"
                                    :class="{ 'opacity-25': editProjectForm.processing }"
                                    :disabled="editProjectForm.processing"
                                >
                                    Update Project
                                </PrimaryButton>
                            </div>
                        </form>
                    </div>
                </Modal>

                <!-- Delete Project Confirmation Modal -->
                <Modal :show="showDeleteProjectModal" @close="closeDeleteProjectModal">
                    <div class="p-6">
                        <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                            Delete Project
                        </h2>

                        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                            Are you sure you want to delete this project? All associated revenue entries will be permanently deleted. This action cannot be undone.
                        </p>

                        <div class="mt-6 flex justify-end">
                            <SecondaryButton @click="closeDeleteProjectModal">
                                Cancel
                            </SecondaryButton>

                            <DangerButton
                                class="ml-3"
                                :class="{ 'opacity-25': deleteProjectForm.processing }"
                                :disabled="deleteProjectForm.processing"
                                @click="deleteProject"
                            >
                                Delete Project
                            </DangerButton>
                        </div>
                    </div>
                </Modal>

            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import {onMounted, onUnmounted, ref} from 'vue'
import { useForm } from '@inertiajs/vue3'
import InputLabel from '@/Components/InputLabel.vue'
import TextInput from '@/Components/TextInput.vue'
import InputError from '@/Components/InputError.vue'
import PrimaryButton from '@/Components/PrimaryButton.vue'
import SecondaryButton from '@/Components/SecondaryButton.vue'
import Modal from '@/Components/Modal.vue'
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import DangerButton from "@/Components/DangerButton.vue";
import {ChevronDownIcon, EllipsisVerticalIcon} from "@heroicons/vue/16/solid/index.js";
import {Head} from "@inertiajs/vue3";

const props = defineProps({
    hasPage: Boolean,
    projects: Array,
    page: Object  // Add this prop
})


const showNewProjectModal = ref(false)
const showRevenueModal = ref(false)
const selectedProject = ref(null)
const expandedProjects = ref([])
const editingRevenue = ref(null)
const showDeleteModal = ref(false)
const revenueToDelete = ref(null)
const activeProjectMenu = ref(null)
const showEditProjectModal = ref(false)
const showDeleteProjectModal = ref(false)
const projectToEdit = ref(null)
const projectToDelete = ref(null)
const showPageSettingsModal = ref(false)

const form = useForm({
    slug: '',
    title: '',
    description: ''
})

const pageSettingsForm = useForm({
    title: '',
    description: ''
})

const projectForm = useForm({
    name: ''
})

const revenueForm = useForm({
    amount: '',
    project_id: null
})

const submitProject = () => {
    projectForm.post(route('projects.store'), {
        preserveScroll: true,
        onSuccess: () => {
            showNewProjectModal.value = false
            projectForm.reset()
        }
    })
}

const openRevenueModal = (project) => {
    selectedProject.value = project
    revenueForm.project_id = project.id
    showRevenueModal.value = true
}

const submitRevenue = () => {
    revenueForm.post(route('revenues.store'), {
        preserveScroll: true,
        onSuccess: () => {
            showRevenueModal.value = false
            revenueForm.reset()
        }
    })
}

const numberFormat = (value) => {
    return new Intl.NumberFormat('en-US').format(value)
}

const toggleProject = (projectId) => {
    const index = expandedProjects.value.indexOf(projectId)
    if (index === -1) {
        expandedProjects.value.push(projectId)
    } else {
        expandedProjects.value.splice(index, 1)
    }
}

const startEdit = (revenue) => {
    editingRevenue.value = {
        id: revenue.id,
        amount: revenue.amount
    }
}

const cancelEdit = () => {
    editingRevenue.value = null
}

const revenueEditingForm = useForm({
    amount: ''
})

const updateRevenue = () => {
    revenueEditingForm.amount = editingRevenue.value.amount
    revenueEditingForm.put(route('revenues.update', editingRevenue.value.id), {
        preserveScroll: true,
        onSuccess: () => {
            editingRevenue.value = null
        }
    })
}

const confirmDelete = (revenue) => {
    revenueToDelete.value = revenue
    showDeleteModal.value = true
}

const revenueDeletingForm = useForm({})

const deleteRevenue = () => {
    revenueDeletingForm.delete(route('revenues.destroy', revenueToDelete.value.id), {
        preserveScroll: true,
        onSuccess: () => {
            showDeleteModal.value = false
            revenueToDelete.value = null
        }
    })
}

const formatDate = (date) => {
    return new Date(date).toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'short',
        day: 'numeric'
    })
}

const editProjectForm = useForm({
    name: ''
})

const deleteProjectForm = useForm({})

// Toggle project menu
const toggleProjectMenu = (projectId) => {
    event?.preventDefault()
    event?.stopPropagation()
    console.log('Current:', activeProjectMenu.value, 'Clicking:', projectId)
    activeProjectMenu.value = activeProjectMenu.value === projectId ? null : projectId
    console.log('After toggle:', activeProjectMenu.value)
}

const handleClickOutside = (event) => {
    if (!event.target.closest('.project-menu')) {
        activeProjectMenu.value = null
    }
}

// Add click outside listener
onMounted(() => {
    document.addEventListener('click', handleClickOutside)
})

onUnmounted(() => {
    document.removeEventListener('click', handleClickOutside)
})

// Edit Project
const openEditProject = (project) => {
    projectToEdit.value = project
    editProjectForm.name = project.name
    showEditProjectModal.value = true
    activeProjectMenu.value = null
}

const closeEditProjectModal = () => {
    showEditProjectModal.value = false
    projectToEdit.value = null
    editProjectForm.reset()
}

const updateProject = () => {
    editProjectForm.put(route('projects.update', projectToEdit.value.id), {
        preserveScroll: true,
        onSuccess: () => {
            closeEditProjectModal()
        }
    })
}

// Delete Project
const confirmDeleteProject = (project) => {
    projectToDelete.value = project
    showDeleteProjectModal.value = true
    activeProjectMenu.value = null
}

const closeDeleteProjectModal = () => {
    showDeleteProjectModal.value = false
    projectToDelete.value = null
}

const deleteProject = () => {
    deleteProjectForm.delete(route('projects.destroy', projectToDelete.value.id), {
        preserveScroll: true,
        onSuccess: () => {
            closeDeleteProjectModal()
        }
    })
}

const openPageSettingsModal = () => {
    pageSettingsForm.title = props.page.title
    pageSettingsForm.description = props.page.description
    showPageSettingsModal.value = true
}

const closePageSettingsModal = () => {
    showPageSettingsModal.value = false
    pageSettingsForm.reset()
}

const updatePageSettings = () => {
    pageSettingsForm.put(route('user-page.update'), {
        preserveScroll: true,
        onSuccess: () => {
            showPageSettingsModal.value = false
        }
    })
}
</script>
