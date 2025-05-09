<script setup lang="ts">
import { ref, reactive, watch, computed } from 'vue'
import { useApi } from '@/composables/useApi'
import { useToast } from 'vue-toastification'

const toast = useToast()

interface Address { country: string; city: string; post_code: string; street: string }
interface User { id: number; first_name: string; last_name: string; email: string; address: Address }

// Props & emits
const props = defineProps<{ user: User; editMode?: boolean }>()
const emit  = defineEmits<{
  (e: 'close'): void
  (e: 'updated', user: User): void
}>()

// Visibility & close
const visible = ref(true)
function handleClose() {
  visible.value = false
  setTimeout(() => emit('close'), 200)
}

// Form state for editing
const form = reactive({
  first_name: props.user.first_name,
  last_name:  props.user.last_name,
  email:      props.user.email,
  password:   '',
  country:    props.user.address.country,
  city:       props.user.address.city,
  post_code:  props.user.address.post_code,
  street:     props.user.address.street,
})

const errors     = reactive<Record<string,string>>({})
const processing = ref(false)
const api        = useApi<User>()


const original = {
  first_name: props.user.first_name,
  last_name:  props.user.last_name,
  email:      props.user.email,
  country:    props.user.address.country,
  city:       props.user.address.city,
  post_code:  props.user.address.post_code,
  street:     props.user.address.street,
}

const hasChanges = computed(() => {
  return (
    form.first_name !== original.first_name ||
    form.last_name  !== original.last_name  ||
    form.email      !== original.email      ||
    form.country    !== original.country    ||
    form.city       !== original.city       ||
    form.post_code  !== original.post_code  ||
    form.street     !== original.street     ||
    form.password.length > 0                // password counts as a change if non‑empty
  )
})

// Reset form if user prop changes
watch(() => props.user, u => {
  form.first_name = u.first_name
  form.last_name  = u.last_name
  form.email      = u.email
  form.password   = ''
  form.country    = u.address.country
  form.city       = u.address.city
  form.post_code  = u.address.post_code
  form.street     = u.address.street
  Object.keys(errors).forEach(k => delete errors[k])
})

// Save handler
async function save() {
  
  if (!hasChanges.value) {
    return
  }

  processing.value = true
  try {
    const updated = await api.put(`users/${props.user.id}`, form)
    toast.success('User details updated successfully')
    emit('updated', updated)
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
      <div v-if="visible" class="fixed inset-0 bg-[#000000b8] flex items-center justify-center z-50">
        <div class="bg-white rounded-lg shadow-lg w-full max-w-xl p-6 my-8 mx-4 max-h-[80vh] overflow-auto relative">
          <!-- Close button -->
          <button class="absolute top-3 right-3 text-gray-600 hover:text-gray-800 cursor-pointer"
            @click="handleClose">✕</button>

          <!-- Title -->
          <h2 class="text-xl font-semibold mb-4 text-center">
            {{ editMode ? 'Edit User' : 'User Details' }}
          </h2>

          <!-- Display mode -->
          <div v-if="!editMode" class="space-y-2 text-gray-700">
            <p><strong>First Name:</strong> {{ user.first_name }}</p>
            <p><strong>Last Name:</strong> {{ user.last_name }}</p>
            <p><strong>Email:</strong> {{ user.email }}</p>
            <p><strong>Country:</strong> {{ user.address.country }}</p>
            <p><strong>City:</strong> {{ user.address.city }}</p>
            <p><strong>Post Code:</strong> {{ user.address.post_code }}</p>
            <p><strong>Street:</strong> {{ user.address.street }}</p>
          </div>

          <!-- Edit mode form -->
          <form v-else @submit.prevent="save" class="space-y-4">
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

            <div class="grid grid-cols-2 gap-4">
              <div>
                <label class="block mb-1">Email</label>
                <input v-model="form.email" type="email" class="w-full border p-2" />
                <div v-if="errors.email" class="text-red-600">{{ errors.email }}</div>
              </div>
              <div>
                <label class="block mb-1">Password</label>
                <input v-model="form.password" type="password" class="w-full border p-2" placeholder="••••••••" />
                <div v-if="errors.password" class="text-red-600">{{ errors.password }}</div>
              </div>
            </div>

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

            <div class="text-right">
              <button type="submit" :disabled="processing || !hasChanges" class="px-4 py-2 rounded cursor-pointer"
                :class="{
                  'bg-blue-600 hover:bg-blue-700 text-white': hasChanges,
                  'bg-gray-300 text-gray-500 cursor-not-allowed': !hasChanges
                }">
                Save
              </button>
            </div>
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