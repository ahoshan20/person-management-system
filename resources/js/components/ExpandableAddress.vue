<template>
    <div class="flex items-start gap-1">
        <span
            class="text-gray-600 max-w-md block address-text"
            :class="{ 'address-collapsed': !expanded }"
            :title="text"
        >
            {{ text }}
        </span>
        <button v-if="needsMore && !expanded"
            @click="expanded = true"
            class="text-gray-900 font-medium hover:underline text-xs whitespace-nowrap mt-0.5 shrink-0">
            More
        </button>
        <button v-if="expanded && needsMore"
            @click="expanded = false"
            class="text-gray-500 font-medium hover:underline text-xs whitespace-nowrap mt-0.5 shrink-0">
            Less
        </button>
    </div>
</template>

<script setup>
import { ref, computed } from 'vue'
const props = defineProps({ text: { type: String, default: '' } })
const expanded = ref(false)
const needsMore = computed(() => props.text?.length > 80)
</script>

<style scoped>
.address-text {
    word-break: break-word;
}
.address-collapsed {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
</style>