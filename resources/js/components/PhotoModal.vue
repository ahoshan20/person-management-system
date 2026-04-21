<template>
    <Teleport to="body">
        <div class="fixed inset-0 z-[60]" role="dialog" aria-modal="true">
            <div class="fixed inset-0 bg-gray-900/90 backdrop-blur-md" @click="$emit('close')"></div>
            <div class="fixed inset-0 z-10 flex items-center justify-center p-4">
                <button @click="$emit('close')"
                    class="absolute top-4 right-4 text-white/70 hover:text-white transition-colors">
                    <iconify-icon icon="solar:close-circle-linear" class="text-3xl" stroke-width="1.5"></iconify-icon>
                </button>
                <div class="relative">
                    <img :src="src" alt="Full size photo"
                        class="max-w-full max-h-[85vh] object-contain rounded-lg shadow-2xl">
                </div>
            </div>
        </div>
    </Teleport>
</template>

<script setup>
defineProps({ src: String })
defineEmits(['close'])
</script>