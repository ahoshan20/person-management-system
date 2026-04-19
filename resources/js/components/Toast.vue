<template>
    <Teleport to="body">
        <Transition
            enter-active-class="transition-all duration-300 ease-out"
            enter-from-class="opacity-0 translate-y-4"
            enter-to-class="opacity-100 translate-y-0"
            leave-active-class="transition-all duration-200 ease-in"
            leave-from-class="opacity-100 translate-y-0"
            leave-to-class="opacity-0 translate-y-4"
        >
            <div v-if="toast.show"
                class="fixed top-6 right-6 z-[80] flex items-center gap-3 px-4 py-3 rounded-lg shadow-lg text-sm font-medium"
                :class="toast.type === 'error'
                    ? 'bg-red-600 text-white'
                    : 'bg-gray-900 text-white'">
                <iconify-icon
                    :icon="toast.type === 'error' ? 'solar:close-circle-linear' : 'solar:check-circle-linear'"
                    class="text-lg shrink-0"
                    stroke-width="1.5">
                </iconify-icon>
                {{ toast.message }}
            </div>
        </Transition>
    </Teleport>
</template>

<script setup>
defineProps({
    toast: {
        type: Object,
        default: () => ({ show: false, message: '', type: 'success' })
    }
})
</script>