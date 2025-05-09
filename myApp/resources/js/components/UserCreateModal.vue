<script setup lang="ts">
import { reactive, ref, watch } from 'vue'
import { useApi } from '@/composables/useApi'
import { useToast } from 'vue-toastification'

const toast = useToast()

interface Form {
    first_name: string
    last_name: string
    email: string
    password: string
    country: string
    city: string
    post_code: string
    street: string
}

// Props & emits
const emit = defineEmits<{
    (e: 'created'): void
    (e: 'close'): void
}>()

// Visibility control
const visible = ref(false)
watch(
    () => true,
    () => {
        visible.value = true
    },
    { immediate: true }
)

// Form state
const form = reactive<Form>({
    first_name: '',
    last_name: '',
    email: '',
    password: '',
    country: '',
    city: '',
    post_code: '',
    street: '',
})

const errors = reactive<Partial<Record<keyof Form, string>>>({})
const processing = ref(false)
const api = useApi()

function handleClose() {
    visible.value = false
    setTimeout(() => emit('close'), 200)
}

async function submit() {
    processing.value = true
    // clear old errors
    Object.keys(errors).forEach((k) => delete errors[k as keyof Form])

    try {
        await api.post('users', form)
        toast.success('User created successfully')
        emit('created')
        handleClose()
    } catch (err: any) {
        if (err.response?.status === 422) {
            Object.assign(errors, err.response.data.errors)
        } else {
            console.error(err)
        }
    } finally {
        processing.value = false
    }
}
</script>




<template>
    <transition name="fade">
        <div v-if="visible" class="fixed inset-0 bg-[#000000b8] flex items-center justify-center z-50"
            >
            <div
                class=" bg-white rounded-lg shadow-lg w-full max-w-lg p-6 pt-10 pb-10 my-8 mx-4 relative max-h-[80vh] overflow-auto ">

                <!-- Close button -->
                <button class="absolute top-3 right-3 text-gray-600 hover:text-gray-800 cursor-pointer" @click="handleClose">✕</button>

                <h2 class="text-xl font-semibold mb-4 text-center">Add New User</h2>

                <form @submit.prevent="submit">
                    <fieldset :disabled="processing" class="space-y-6">
                        <!-- Row 1: First & Last Name -->
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block mb-1">First Name</label>
                                <input v-model="form.first_name" type="text" class="w-full border p-2" />
                                <div v-if="errors.first_name" class="text-red-600">{{ errors.first_name }}</div>
                            </div>
                            <div>
                                <label class="block mb-1">Last Name</label>
                                <input v-model="form.last_name" type="text" class="w-full border p-2" />
                                <div v-if="errors.last_name" class="text-red-600">{{ errors.last_name }}</div>
                            </div>
                        </div>

                        <!-- Row 2: Email & Password -->
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block mb-1">Email</label>
                                <input v-model="form.email" type="email" class="w-full border p-2" />
                                <div v-if="errors.email" class="text-red-600">{{ errors.email }}</div>
                            </div>
                            <div>
                                <label class="block mb-1">Password</label>
                                <input v-model="form.password" type="password" class="w-full border p-2" />
                                <div v-if="errors.password" class="text-red-600">{{ errors.password }}</div>
                            </div>
                        </div>

                        <!-- Row 3: Country & City -->
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block mb-1">Country</label>
                                <input v-model="form.country" type="text" class="w-full border p-2" />
                                <div v-if="errors.country" class="text-red-600">{{ errors.country }}</div>
                            </div>
                            <div>
                                <label class="block mb-1">City</label>
                                <input v-model="form.city" type="text" class="w-full border p-2" />
                                <div v-if="errors.city" class="text-red-600">{{ errors.city }}</div>
                            </div>
                        </div>

                        <!-- Row 4: Post Code & Street -->
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block mb-1">Post Code</label>
                                <input v-model="form.post_code" type="text" class="w-full border p-2" />
                                <div v-if="errors.post_code" class="text-red-600">{{ errors.post_code }}</div>
                            </div>
                            <div>
                                <label class="block mb-1">Street</label>
                                <input v-model="form.street" type="text" class="w-full border p-2" />
                                <div v-if="errors.street" class="text-red-600">{{ errors.street }}</div>
                            </div>
                        </div>

                        <!-- Submit button -->
                        <div class="text-right">
                            <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700 cursor-pointer">
                                Create
                            </button>
                        </div>
                    </fieldset>
                </form>

            </div>
        </div>
    </transition>
</template>


<style scoped>
.fade-enter-active,
.fade-leave-active {
    transition: opacity 0.2s ease;
}

.fade-enter-from,
.fade-leave-to {
    opacity: 0;
}
</style>