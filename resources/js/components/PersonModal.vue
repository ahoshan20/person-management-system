<template>
    <Teleport to="body">
        <div class="fixed inset-0 z-50" role="dialog" aria-modal="true">
            <!-- Backdrop -->
            <div class="fixed inset-0 bg-gray-900/40 backdrop-blur-sm transition-opacity"
                @click="$emit('close')">
            </div>

            <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
                <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
                    <div class="relative transform overflow-hidden rounded-xl bg-white text-left shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-lg">

                        <!-- Header -->
                        <div class="flex items-center justify-between border-b border-gray-100 px-6 py-4">
                            <h3 class="text-lg font-semibold tracking-tight text-gray-900">
                                {{ mode === 'add' ? 'Add Person' : 'Edit Person' }}
                            </h3>
                            <button @click="$emit('close')"
                                class="text-gray-400 hover:text-gray-500 rounded-md hover:bg-gray-100 p-1 transition-colors">
                                <iconify-icon icon="solar:close-circle-linear" class="text-xl" stroke-width="1.5"></iconify-icon>
                            </button>
                        </div>

                        <!-- Form -->
                        <div class="px-6 py-5 space-y-5">

                            <!-- Full Name -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                    Full Name <span class="text-red-500">*</span>
                                </label>
                                <input
                                    v-model="form.full_name"
                                    type="text"
                                    maxlength="150"
                                    placeholder="e.g. John Doe"
                                    :class="errors.full_name ? 'border-red-400 focus:ring-red-500 focus:border-red-500' : 'border-gray-300 focus:border-gray-900 focus:ring-gray-900'"
                                    class="block w-full rounded-md border py-2 px-3 text-sm text-gray-900 shadow-sm focus:outline-none focus:ring-1 placeholder:text-gray-400"
                                    @input="clearError('full_name')"
                                >
                                <div class="flex justify-between mt-1">
                                    <p class="text-xs text-red-500">{{ errors.full_name?.[0] }}</p>
                                    <p class="text-xs text-gray-400">{{ form.full_name.length }}/150</p>
                                </div>
                            </div>

                            <!-- Full Address -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                    Full Address <span class="text-red-500">*</span>
                                </label>
                                <textarea
                                    v-model="form.full_address"
                                    rows="3"
                                    maxlength="500"
                                    placeholder="Enter full address..."
                                    :class="errors.full_address ? 'border-red-400 focus:ring-red-500 focus:border-red-500' : 'border-gray-300 focus:border-gray-900 focus:ring-gray-900'"
                                    class="block w-full rounded-md border py-2 px-3 text-sm text-gray-900 shadow-sm focus:outline-none focus:ring-1 placeholder:text-gray-400 resize-none"
                                    @input="clearError('full_address')"
                                ></textarea>
                                <div class="flex justify-between mt-1">
                                    <p class="text-xs text-red-500">{{ errors.full_address?.[0] }}</p>
                                    <p class="text-xs text-gray-400">{{ form.full_address.length }}/500</p>
                                </div>
                            </div>

                            <!-- Photo -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                    Photo <span class="text-red-500">*</span>
                                    <span v-if="mode === 'edit'" class="text-xs font-normal text-gray-400 ml-1">(leave empty to keep current)</span>
                                </label>

                                <!-- Preview -->
                                <div v-if="photoPreview" class="mb-3 relative inline-block">
                                    <img :src="photoPreview" class="h-24 w-24 rounded-lg object-cover border border-gray-200">
                                    <button @click="clearPhoto"
                                        class="absolute -top-2 -right-2 bg-red-500 text-white rounded-full w-5 h-5 flex items-center justify-center hover:bg-red-600 transition-colors">
                                        <iconify-icon icon="solar:close-linear" class="text-xs"></iconify-icon>
                                    </button>
                                </div>

                                <!-- Dropzone -->
                                <div v-if="!photoPreview"
                                    class="flex justify-center rounded-lg border border-dashed px-6 py-8 hover:bg-gray-50 transition-colors relative cursor-pointer"
                                    :class="[
                                        dragOver ? 'border-gray-900 bg-gray-50' : (errors.photo ? 'border-red-400' : 'border-gray-300'),
                                    ]"
                                    @dragover.prevent="dragOver = true"
                                    @dragleave.prevent="dragOver = false"
                                    @drop.prevent="onDrop">

                                    <input type="file" accept=".jpg,.jpeg,.png"
                                        class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10"
                                        @change="onFileChange">

                                    <div class="text-center">
                                        <div class="mx-auto h-10 w-10 text-gray-400 flex items-center justify-center bg-white rounded-full border border-gray-200 shadow-sm mb-3">
                                            <iconify-icon icon="solar:gallery-upload-linear" class="text-xl" stroke-width="1.5"></iconify-icon>
                                        </div>
                                        <div class="flex text-sm text-gray-600 justify-center">
                                            <span class="font-medium text-gray-900 hover:underline">Select a file</span>
                                            <p class="pl-1">or drag and drop</p>
                                        </div>
                                        <p class="text-xs text-gray-500 mt-1">JPG, JPEG, PNG • Max 2MB • 600×600px</p>
                                    </div>
                                </div>

                                <p v-if="errors.photo" class="text-xs text-red-500 mt-1">{{ errors.photo?.[0] }}</p>

                                <!-- Progress Bar -->
                                <div v-if="uploadProgress > 0 && uploadProgress < 100" class="mt-3">
                                    <div class="flex items-center justify-between text-xs mb-1.5">
                                        <div class="flex items-center gap-1.5 text-gray-700 font-medium">
                                            <iconify-icon icon="solar:file-check-linear" class="text-gray-500"></iconify-icon>
                                            <span class="truncate max-w-[200px]">{{ selectedFileName }}</span>
                                        </div>
                                        <span class="text-gray-500">{{ uploadProgress }}%</span>
                                    </div>
                                    <div class="w-full bg-gray-100 rounded-full h-1.5 overflow-hidden">
                                        <div class="bg-gray-900 h-1.5 rounded-full transition-all duration-300 ease-out"
                                            :style="{ width: uploadProgress + '%' }"></div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Footer -->
                        <div class="bg-gray-50/50 px-6 py-4 flex items-center justify-end gap-3 border-t border-gray-100">
                            <button @click="$emit('close')"
                                class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-gray-900 focus:ring-offset-1 transition-colors">
                                Cancel
                            </button>
                            <button @click="submit" :disabled="submitting"
                                class="px-4 py-2 text-sm font-medium text-white bg-gray-900 border border-transparent rounded-md shadow-sm hover:bg-gray-800 focus:outline-none focus:ring-2 focus:ring-gray-900 focus:ring-offset-1 transition-colors flex items-center gap-2 disabled:opacity-60 disabled:cursor-not-allowed">
                                <iconify-icon v-if="submitting" icon="solar:spinner-linear" class="animate-spin"></iconify-icon>
                                {{ submitting ? 'Saving...' : 'Submit Information' }}
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </Teleport>
</template>

<script setup>
import { ref, reactive, onMounted } from 'vue'
import axios from 'axios'

const props = defineProps({
    mode:   { type: String, default: 'add' },  // 'add' | 'edit'
    person: { type: Object, default: null }
})
const emit = defineEmits(['close', 'saved'])

// ── State ──────────────────────────────────────────────────────────
const form = reactive({ full_name: '', full_address: '' })
const errors         = reactive({})
const photoFile      = ref(null)
const photoPreview   = ref(null)
const dragOver       = ref(false)
const submitting     = ref(false)
const uploadProgress = ref(0)
const selectedFileName = ref('')

// ── Init (edit mode) ──────────────────────────────────────────────
onMounted(() => {
    if (props.mode === 'edit' && props.person) {
        form.full_name    = props.person.full_name
        form.full_address = props.person.full_address
        photoPreview.value = `/storage/thumbs/${props.person.thumb}`
    }
})

// ── Helpers ────────────────────────────────────────────────────────
function clearError(field) { delete errors[field] }

function clearPhoto() {
    photoFile.value    = null
    photoPreview.value = props.mode === 'edit' && props.person
        ? `/storage/thumbs/${props.person.thumb}`
        : null
    uploadProgress.value  = 0
    selectedFileName.value = ''
    delete errors.photo
}

// ── Client-side photo validation ───────────────────────────────────
function validatePhotoClient(file) {
    return new Promise((resolve, reject) => {
        const allowed = ['image/jpeg', 'image/jpg', 'image/png']
        if (!allowed.includes(file.type)) {
            return reject('Photo must be JPG/JPEG/PNG format.')
        }
        if (file.size > 2 * 1024 * 1024) {
            return reject('Photo must not exceed 2MB.')
        }
        const img = new Image()
        const url = URL.createObjectURL(file)
        img.onload = () => {
            URL.revokeObjectURL(url)
            if (img.width !== 600 || img.height !== 600) {
                return reject('Photo must be exactly 600×600 pixels.')
            }
            resolve()
        }
        img.onerror = () => reject('Could not read the image file.')
        img.src = url
    })
}

async function handleFile(file) {
    delete errors.photo
    try {
        await validatePhotoClient(file)
        photoFile.value       = file
        selectedFileName.value = file.name
        photoPreview.value    = URL.createObjectURL(file)
        uploadProgress.value  = 0
    } catch (msg) {
        errors.photo = [msg]
        photoFile.value = null
    }
}

function onFileChange(e) {
    if (e.target.files[0]) handleFile(e.target.files[0])
}
function onDrop(e) {
    dragOver.value = false
    if (e.dataTransfer.files[0]) handleFile(e.dataTransfer.files[0])
}

// ── Submit ────────────────────────────────────────────────────────
async function submit() {
    // Clear previous errors
    Object.keys(errors).forEach(k => delete errors[k])

    // Client-side required check
    let hasError = false
    if (!form.full_name.trim()) {
        errors.full_name = ['Full Name is required.']; hasError = true
    }
    if (!form.full_address.trim()) {
        errors.full_address = ['Full Address is required.']; hasError = true
    }
    if (props.mode === 'add' && !photoFile.value) {
        errors.photo = ['Photo is required.']; hasError = true
    }
    if (hasError) return

    const fd = new FormData()
    fd.append('full_name',    form.full_name.trim())
    fd.append('full_address', form.full_address.trim())
    if (photoFile.value) fd.append('photo', photoFile.value)

    submitting.value     = true
    uploadProgress.value = 0

    try {
        let res
        const config = {
            headers: { 'Content-Type': 'multipart/form-data' },
            onUploadProgress: e => {
                uploadProgress.value = Math.round((e.loaded / e.total) * 100)
            }
        }

        if (props.mode === 'add') {
            res = await axios.post('/persons', fd, config)
        } else {
            // Laravel doesn't support PUT with FormData, use POST with _method
            fd.append('_method', 'POST')
            res = await axios.post(`/persons/${props.person.id}`, fd, config)
        }

        emit('saved', {
            mode:      props.mode,
            person:    res.data.person,
            oldPerson: props.person
        })
    } catch (err) {
        if (err.response?.data?.errors) {
            Object.assign(errors, err.response.data.errors)
        } else {
            errors.general = ['An unexpected error occurred. Please try again.']
        }
        uploadProgress.value = 0
    } finally {
        submitting.value = false
    }
}
</script>