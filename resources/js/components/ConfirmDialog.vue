<template>
    <Teleport to="body">
        <div class="fixed inset-0 z-[70]" role="dialog" aria-modal="true">
            <div class="fixed inset-0 bg-gray-900/40 backdrop-blur-sm"></div>
            <div class="fixed inset-0 z-10 flex items-center justify-center p-4">
                <div class="bg-white rounded-xl shadow-2xl max-w-sm w-full p-6">

                    <!-- Icon -->
                    <div class="flex items-center justify-center w-12 h-12 rounded-full bg-red-50 border border-red-100 mx-auto mb-4">
                        <iconify-icon icon="solar:danger-triangle-linear" class="text-2xl text-red-600" stroke-width="1.5"></iconify-icon>
                    </div>

                    <!-- Message -->
                    <h3 class="text-center text-base font-semibold text-gray-900 mb-2">Confirm Action</h3>
                    <p class="text-center text-sm text-gray-500 mb-6" v-html="message"></p>

                    <!-- Buttons -->
                    <div class="flex gap-3">
                        <button @click="$emit('cancel')"
                            class="flex-1 px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-gray-900 focus:ring-offset-1 transition-colors">
                            Cancel
                        </button>
                        <button @click="$emit('confirm')"
                            class="flex-1 px-4 py-2 text-sm font-medium text-white bg-red-600 border border-transparent rounded-md hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-600 focus:ring-offset-1 transition-colors">
                            Yes, Delete
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </Teleport>
</template>

<script setup>
defineProps({ message: String })
defineEmits(['confirm', 'cancel'])
</script>