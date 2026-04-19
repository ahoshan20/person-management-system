<template>
    <Teleport to="body">
        <div class="fixed inset-0 z-50" role="dialog" aria-modal="true">
            <!-- Backdrop -->
            <div class="fixed inset-0 bg-gray-900/40 backdrop-blur-sm" @click="$emit('close')"></div>

            <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
                <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
                    <div class="relative transform overflow-hidden rounded-xl bg-white text-left shadow-2xl sm:my-8 w-full max-w-4xl flex flex-col max-h-[85vh]">

                        <!-- Header -->
                        <div class="flex items-center justify-between border-b border-gray-100 px-6 py-4 shrink-0">
                            <div class="flex items-center gap-2">
                                <iconify-icon icon="solar:history-linear" class="text-xl text-gray-500" stroke-width="1.5"></iconify-icon>
                                <h3 class="text-lg font-semibold tracking-tight text-gray-900">Audit Log</h3>
                            </div>
                            <button @click="$emit('close')"
                                class="text-gray-400 hover:text-gray-500 rounded-md hover:bg-gray-100 p-1 transition-colors">
                                <iconify-icon icon="solar:close-circle-linear" class="text-xl" stroke-width="1.5"></iconify-icon>
                            </button>
                        </div>

                        <!-- Table -->
                        <div class="overflow-y-auto flex-1">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50/80 sticky top-0 backdrop-blur-sm z-10">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-16">SN</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-36">Action Name</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Detail</th>
                                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider w-44">Date &amp; Time</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-100 text-sm text-gray-600">

                                    <!-- Loading -->
                                    <tr v-if="loading">
                                        <td colspan="4" class="px-6 py-10 text-center text-gray-400">
                                            <iconify-icon icon="solar:spinner-linear" class="text-2xl animate-spin block mx-auto mb-1"></iconify-icon>
                                            Loading logs...
                                        </td>
                                    </tr>

                                    <!-- Empty -->
                                    <tr v-else-if="!logs.length">
                                        <td colspan="4" class="px-6 py-10 text-center text-gray-500">
                                            <iconify-icon icon="solar:history-linear" class="text-3xl text-gray-300 block mx-auto mb-2"></iconify-icon>
                                            No audit logs yet.
                                        </td>
                                    </tr>

                                    <!-- Rows -->
                                    <tr v-else v-for="(log, index) in logs" :key="log.sn">
                                        <td class="px-6 py-4 whitespace-nowrap text-gray-500">{{ index + 1 }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span :class="badgeClass(log.action)"
                                                class="inline-flex items-center rounded-full px-2 py-1 text-xs font-medium ring-1 ring-inset">
                                                {{ log.action }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 leading-relaxed" v-html="formatDetail(log)"></td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-gray-500 text-xs">
                                            {{ log.datetime }}
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </Teleport>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { getAuditLogs } from '../audit.js'

defineEmits(['close'])

const logs    = ref([])
const loading = ref(true)

onMounted(async () => {
    try {
        logs.value = await getAuditLogs()
    } finally {
        loading.value = false
    }
})

function badgeClass(action) {
    const map = {
        'Add':          'bg-green-50 text-green-700 ring-green-600/20',
        'Edit':         'bg-yellow-50 text-yellow-800 ring-yellow-600/20',
        'Delete':       'bg-red-50 text-red-700 ring-red-600/20',
        'Photo Update': 'bg-blue-50 text-blue-700 ring-blue-600/20',
        'Bulk Delete':  'bg-orange-50 text-orange-700 ring-orange-600/20',
    }
    return map[action] ?? 'bg-gray-50 text-gray-700 ring-gray-600/20'
}

function formatDetail(log) {
    // Highlight names and IDs in detail text
    return log.detail
        .replace(/(Name:\s*)([\w\s]+)/g, '$1<span class="font-medium text-gray-900">$2</span>')
        .replace(/(ID:\s*)(\d+)/g, '<span class="text-gray-400 ml-1">ID: $2</span>')
}
</script>