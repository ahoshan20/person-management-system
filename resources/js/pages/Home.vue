<template>
    <div class="min-h-screen flex flex-col bg-[#FAFAFA]">

        <!-- ── Header ── -->
        <header class="bg-white border-b border-gray-200 sticky top-0 z-30">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <div class="w-8 h-8 bg-gray-900 rounded-md flex items-center justify-center text-white font-semibold text-lg">P</div>
                    <h1 class="text-xl font-semibold tracking-tight text-gray-900">Directory</h1>
                </div>
                <div class="flex items-center space-x-3">
                    <button @click="openAuditLog"
                        class="inline-flex items-center gap-2 px-3 py-1.5 text-sm font-medium text-gray-600 bg-white border border-gray-300 rounded-md shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-gray-900 focus:ring-offset-1 transition-colors">
                        <iconify-icon icon="solar:history-linear" stroke-width="1.5"></iconify-icon>
                        Audit Log
                    </button>
                    <button @click="openAddModal"
                        class="inline-flex items-center gap-2 px-3 py-1.5 text-sm font-medium text-white bg-gray-900 border border-transparent rounded-md shadow-sm hover:bg-gray-800 focus:outline-none focus:ring-2 focus:ring-gray-900 focus:ring-offset-1 transition-colors">
                        <iconify-icon icon="solar:user-plus-linear" stroke-width="1.5"></iconify-icon>
                        Add Person
                    </button>
                </div>
            </div>
        </header>

        <!-- ── Main ── -->
        <main class="flex-1 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 w-full">

            <!-- Toolbar -->
            <div class="flex items-center justify-between mb-4">
                <div class="flex items-center gap-2">
                    <button @click="confirmBulkDelete"
                        :disabled="!selectedIds.length"
                        :class="selectedIds.length ? 'opacity-100 cursor-pointer' : 'opacity-50 cursor-not-allowed'"
                        class="inline-flex items-center gap-2 px-3 py-1.5 text-sm font-medium text-red-600 bg-red-50 border border-red-200 rounded-md hover:bg-red-100 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-1 transition-colors">
                        <iconify-icon icon="solar:trash-bin-trash-linear" stroke-width="1.5"></iconify-icon>
                        Bulk Delete
                    </button>
                    <span v-if="selectedIds.length" class="text-sm text-gray-500 ml-2">
                        {{ selectedIds.length }} selected
                    </span>
                </div>
                <div class="relative">
                    <iconify-icon icon="solar:magnifer-linear" class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400" stroke-width="1.5"></iconify-icon>
                    <input v-model="searchQuery" type="text" placeholder="Search directory..."
                        class="pl-9 pr-4 py-1.5 text-sm border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-gray-900 focus:border-gray-900 w-64 shadow-sm placeholder:text-gray-400 bg-white">
                </div>
            </div>

            <!-- Table -->
            <div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50/50">
                            <tr>
                                <th class="px-6 py-3 text-left w-12">
                                    <input type="checkbox" class="custom-checkbox"
                                        :checked="allSelected"
                                        :indeterminate.prop="someSelected"
                                        @change="toggleSelectAll">
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-16">SN</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-24">ID</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-16">Photo</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-64">Full Name</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Full Address</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider w-24">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200 text-sm">

                            <!-- Loading -->
                            <tr v-if="loading">
                                <td colspan="7" class="px-6 py-12 text-center text-gray-400">
                                    <iconify-icon icon="solar:spinner-linear" class="text-3xl animate-spin block mx-auto mb-2"></iconify-icon>
                                    <p class="text-sm">Loading...</p>
                                </td>
                            </tr>

                            <!-- Empty -->
                            <tr v-else-if="!filteredPersons.length">
                                <td colspan="7" class="px-6 py-12 text-center text-gray-500">
                                    <iconify-icon icon="solar:users-group-rounded-linear" class="text-4xl text-gray-300 mb-3 block mx-auto"></iconify-icon>
                                    <p class="text-sm font-medium text-gray-900">No persons found</p>
                                    <p class="text-sm mt-1">{{ searchQuery ? 'Try a different search.' : 'Get started by adding a new person.' }}</p>
                                </td>
                            </tr>

                            <!-- Rows -->
                            <tr v-else v-for="(person, index) in filteredPersons" :key="person.id"
                                class="hover:bg-gray-50/50 transition-colors group">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <input type="checkbox" class="custom-checkbox"
                                        :value="person.id"
                                        v-model="selectedIds">
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-gray-500">{{ index + 1 }}</td>
                                <td class="px-6 py-4 whitespace-nowrap font-mono text-gray-600">{{ person.id }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="h-10 w-10 rounded-md border border-gray-200 overflow-hidden cursor-pointer hover:opacity-80 transition-opacity"
                                        @click="viewPhoto(person)">
                                        <img :src="`/storage/thumbs/${person.thumb}`"
                                            :alt="person.full_name"
                                            class="h-full w-full object-cover">
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap font-medium text-gray-900 max-w-48 truncate">{{ person.full_name }}</td>
                                <td class="px-6 py-4">
                                    <ExpandableAddress :text="person.full_address" />
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right">
                                    <div class="flex justify-end gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
                                        <button @click="openEditModal(person)"
                                            class="text-gray-500 hover:text-gray-900 p-1 rounded hover:bg-gray-100" title="Edit">
                                            <iconify-icon icon="solar:pen-linear" stroke-width="1.5" class="text-lg"></iconify-icon>
                                        </button>
                                        <button @click="confirmDelete(person)"
                                            class="text-gray-500 hover:text-red-600 p-1 rounded hover:bg-red-50" title="Delete">
                                            <iconify-icon icon="solar:trash-bin-trash-linear" stroke-width="1.5" class="text-lg"></iconify-icon>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Table Footer -->
                <div class="px-6 py-3 border-t border-gray-200 bg-gray-50/50 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3">
                    <span class="text-xs text-gray-500">
                        Showing {{ filteredPersons.length }} of {{ pagination.total }} entries
                    </span>
                    <div class="flex items-center gap-2">
                        <button
                            @click="gotoPage(pagination.currentPage - 1)"
                            :disabled="!hasPreviousPage"
                            class="px-3 py-1 text-xs font-medium rounded-md border transition-colors disabled:opacity-50 disabled:cursor-not-allowed bg-white border-gray-200 text-gray-700 hover:bg-gray-100"
                        >
                            Previous
                        </button>
                        <span class="text-xs text-gray-600">
                            Page {{ pagination.currentPage }} of {{ pagination.lastPage }}
                        </span>
                        <button
                            @click="gotoPage(pagination.currentPage + 1)"
                            :disabled="!hasNextPage"
                            class="px-3 py-1 text-xs font-medium rounded-md border transition-colors disabled:opacity-50 disabled:cursor-not-allowed bg-white border-gray-200 text-gray-700 hover:bg-gray-100"
                        >
                            Next
                        </button>
                    </div>
                </div>
            </div>
        </main>

        <!-- ── Modals ── -->
        <PersonModal
            v-if="showPersonModal"
            :mode="modalMode"
            :person="editingPerson"
            @close="closePersonModal"
            @saved="onPersonSaved"
        />

        <AuditLogModal
            v-if="showAuditModal"
            @close="showAuditModal = false"
        />

        <PhotoModal
            v-if="photoModalSrc"
            :src="photoModalSrc"
            @close="photoModalSrc = null"
        />

        <!-- Confirm Dialog -->
        <ConfirmDialog
            v-if="confirmDialog.show"
            :message="confirmDialog.message"
            @confirm="confirmDialog.onConfirm"
            @cancel="confirmDialog.show = false"
        />

        <!-- Toast -->
        <Toast :toast="toast" />
    </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import axios from 'axios'
import { logAudit } from '../audit.js'
import PersonModal      from '../components/PersonModal.vue'
import AuditLogModal    from '../components/AuditLogModal.vue'
import PhotoModal       from '../components/PhotoModal.vue'
import ExpandableAddress from '../components/ExpandableAddress.vue'
import ConfirmDialog    from '../components/ConfirmDialog.vue'
import Toast            from '../components/Toast.vue'

// ── State ──────────────────────────────────────────────────────────
const persons        = ref([])
const loading        = ref(false)
const searchQuery    = ref('')
const selectedIds    = ref([])
const pagination     = ref({ currentPage: 1, perPage: 10, lastPage: 1, total: 0 })
const showPersonModal = ref(false)
const showAuditModal  = ref(false)
const photoModalSrc   = ref(null)
const modalMode       = ref('add')
const editingPerson   = ref(null)
const toast           = ref({ show: false, message: '', type: 'success' })
const confirmDialog   = ref({ show: false, message: '', onConfirm: null })

// ── Computed ───────────────────────────────────────────────────────
const filteredPersons = computed(() => {
    if (!searchQuery.value.trim()) return persons.value
    const q = searchQuery.value.toLowerCase()
    return persons.value.filter(p =>
        p.full_name.toLowerCase().includes(q) ||
        p.full_address.toLowerCase().includes(q) ||
        p.id.includes(q)
    )
})

const allSelected  = computed(() =>
    persons.value.length > 0 && selectedIds.value.length === persons.value.length
)
const someSelected = computed(() =>
    selectedIds.value.length > 0 && selectedIds.value.length < persons.value.length
)
const hasPreviousPage = computed(() => pagination.value.currentPage > 1)
const hasNextPage = computed(() => pagination.value.currentPage < pagination.value.lastPage)

function gotoPage(page) {
    if (page < 1 || page > pagination.value.lastPage) return
    loadPersons(page)
}

// ── Methods ────────────────────────────────────────────────────────
async function loadPersons(page = 1) {
    loading.value = true
    try {
        const res = await axios.get('/persons', {
            params: {
                page,
                per_page: pagination.value.perPage,
            },
        })

        persons.value = res.data.data || []
        pagination.value = {
            currentPage: res.data.meta.current_page,
            perPage: res.data.meta.per_page,
            lastPage: res.data.meta.last_page,
            total: res.data.meta.total,
        }
        selectedIds.value = []
    } catch {
        showToast('Failed to load persons.', 'error')
    } finally {
        loading.value = false
    }
}

function toggleSelectAll(e) {
    selectedIds.value = e.target.checked ? persons.value.map(p => p.id) : []
}

function openAddModal() {
    modalMode.value    = 'add'
    editingPerson.value = null
    showPersonModal.value = true
}

function openEditModal(person) {
    modalMode.value     = 'edit'
    editingPerson.value = { ...person }
    showPersonModal.value = true
}

function openAuditLog() {
    showAuditModal.value = true
}

function closePersonModal() {
    showPersonModal.value = false
    editingPerson.value   = null
}

function viewPhoto(person) {
    photoModalSrc.value = `/storage/photos/${person.photo}`
}

async function onPersonSaved({ mode, person, oldPerson }) {
    if (mode === 'add') {
        await logAudit('Add', `New record added Name: ${person.full_name} ID: ${person.id}`)
        showToast('Person added successfully.')
        await loadPersons(1)
    } else {
        await logAudit('Edit', `Record updated from (${oldPerson.full_name}, ${oldPerson.full_address}) to (${person.full_name}, ${person.full_address}) ID: ${person.id}`)
        showToast('Person updated successfully.')
        await loadPersons(pagination.value.currentPage)
    }
    closePersonModal()
}

function confirmDelete(person) {
    confirmDialog.value = {
        show: true,
        message: `Are you sure you want to delete <strong>${person.full_name}</strong>? This action cannot be undone.`,
        onConfirm: () => deletePerson(person)
    }
}

async function deletePerson(person) {
    confirmDialog.value.show = false
    try {
        await axios.delete(`/persons/${person.id}`)
        await logAudit('Delete', `Record deleted Name: ${person.full_name} ID: ${person.id}`)
        showToast('Person deleted successfully.')
        await loadPersons(pagination.value.currentPage)
    } catch {
        showToast('Failed to delete person.', 'error')
    }
}

function confirmBulkDelete() {
    if (!selectedIds.value.length) return
    confirmDialog.value = {
        show: true,
        message: `Are you sure you want to delete <strong>${selectedIds.value.length} selected records</strong>? This action cannot be undone.`,
        onConfirm: () => bulkDelete()
    }
}

async function bulkDelete() {
    confirmDialog.value.show = false
    try {
        await axios.post('/persons/bulk-delete', { ids: selectedIds.value })
        const count = selectedIds.value.length
        await logAudit('Bulk Delete', `${count} records deleted`)
        selectedIds.value = []
        showToast(`${count} persons deleted successfully.`)
        await loadPersons(pagination.value.currentPage)
    } catch {
        showToast('Failed to bulk delete.', 'error')
    }
}

function showToast(message, type = 'success') {
    toast.value = { show: true, message, type }
    setTimeout(() => { toast.value.show = false }, 3500)
}

onMounted(loadPersons)
</script>