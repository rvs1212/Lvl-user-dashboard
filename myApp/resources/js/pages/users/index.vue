<script setup lang="ts">
import { ref, watch, onMounted, computed } from 'vue'
import { useApi } from '@/composables/useApi'
import Loader from '@/components/Loader.vue'
import NoData from '@/components/NoData.vue' 
import UserDetailModal from '../../components/UserDetailModal.vue'
import UserCreateModal from '@/components/UserCreateModal.vue'
import DeleteConfirmModal from '@/components/DeleteConfirmModal.vue'
import { useToast } from 'vue-toastification'
import { debounce } from 'lodash-es'



const toast = useToast()

interface Address {
  id: number
  country: string
  city: string
  post_code: string
  street: string
}

interface User {
  id: number
  first_name: string
  last_name: string
  email: string
  address: Address
}

interface PaginatedResponse {
  data: User[]
  current_page: number
  last_page: number
}

// Two API clients: one for lists, one for single users
const apiUsers = useApi<PaginatedResponse>()
const apiUser  = useApi<User>()

const users       = ref<User[]>([])
const loading     = ref(false)
const noData     = ref(false)
const searchTerm  = ref('')
const perPage     = ref(10)
const page        = ref(1)
const totalPages  = ref(1)

const startIndex = computed(() => (page.value - 1) * perPage.value + 1)

// Jump-to-page state
const gotoPage = ref(page.value);
const showCreate  = ref(false)
const isEditing   = ref(false)

// Modal state
const showModal    = ref(false)
const selectedUser = ref<User | null>(null)



async function fetchUsers() {
  loading.value = true
  noData.value  = false
  try {
    // Build params object; only include search if non-empty
    const params: Record<string, unknown> = {
      per_page: perPage.value,
      page:     page.value,
      sort_by:  sortBy.value,
      sort_direction: sortDirection.value,
    }
    if (searchTerm.value.trim() !== '') {
      params.search = searchTerm.value.trim()
    }
    const data = await apiUsers.get('/users', { params })

    users.value      = data.data
    page.value       = data.current_page
    totalPages.value = data.last_page
    noData.value     = data.data.length === 0
  } finally {
    loading.value = false
  }
}

const fetchUsersDebounced = debounce(() => {
  page.value = 1        // reset back to page 1 on a new search
  fetchUsers()
}, 300)


async function openModal(userId: number) {
  loading.value = true
  try {
    const user = await apiUser.get(`/users/${userId}`)
    selectedUser.value = user
    showModal.value    = true
  } finally {
    loading.value = false
  }
}

function goToPage() {
  const p = Math.max(1, Math.min(totalPages.value, gotoPage.value));
  page.value = p;
}

const sortBy = ref('id')
const sortDirection = ref<'asc' | 'desc'>('desc')

function toggleSort(column: string) {
  if (sortBy.value === column) {
    sortDirection.value = sortDirection.value === 'asc' ? 'desc' : 'asc'
  } else {
    sortBy.value = column
    sortDirection.value = 'asc'
  }
  fetchUsers()
}



// Re-fetch whenever search term, page, or perPage changes
//watch([searchTerm, perPage, page], fetchUsers)
watch([ perPage, page], fetchUsers)

watch(searchTerm, () => {
  fetchUsersDebounced()
})

watch(page, (newPage) => {
  gotoPage.value = newPage;
});

function openEditModal(userId: number) {
  isEditing.value = true
  openModal(userId)
}


// Deletion state
const showDelete = ref(false)
const userToDelete = ref<number|null>(null)

function deleteUser(userId: number) {
  userToDelete.value = userId
  showDelete.value   = true
}
async function onDeleteConfirm() {
  if (userToDelete.value === null) return
  
  try {
    await useApi().del(`users/${userToDelete.value}`)
    toast.success('User deleted')
    await fetchUsers()      // refresh the list
  } catch (err) {
    console.error(err)
    toast.error('Failed to delete user')
  } finally {
    showDelete.value    = false
    userToDelete.value  = null
  }
}

onMounted(fetchUsers)
</script>


<template>
  <div class="p-4">
    <div class="p-4 flex justify-center items-center">
      <h6 class="text-2xl font-bold mb-4">User Directory</h6>
    </div>

    <!-- Top bar: Add User on left, search controls on right -->
    <div class="mb-4 flex items-center justify-between">
      <!-- Add User button -->
      <button @click="showCreate = true"
        class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700 cursor-pointer">
        Add User
      </button>

      <!-- Search + per-page + Go -->
      <div class="flex space-x-2">
        <input v-model="searchTerm" @keyup.enter="fetchUsers" placeholder="Search..."
          class="border rounded p-2 flex-1" />
        <select v-model.number="perPage" class="border rounded p-2">
          <option :value="10">10</option>
          <option :value="15">15</option>
          <option :value="20">20</option>
          <option :value="50">50</option>
        </select>
        <button @click="fetchUsers" class="px-4 py-2 bg-blue-600 text-white rounded cursor-pointer">Go
        </button>
      </div>
    </div>

    <!-- Loading indicator or content -->
    <div v-if="loading">
      <Loader />
    </div>

    <div v-else-if="noData">
      <NoData />
    </div>

    <div v-else>
      <div class="w-full max-h-[60vh] overflow-y-auto border border-gray-200">
        <table class="w-full table-auto border-collapse">
          <thead class="sticky top-0 bg-gray-100 z-10">
            <tr>
              <th class="border p-2 cursor-pointer" @click="toggleSort('id')">SL No.
                <span v-if="sortBy === 'id'">
                  <i :class="sortDirection === 'asc' ? 'fa fa-arrow-up' : 'fa fa-arrow-down'"></i>
                </span>
              </th>
              <th class="border p-2 cursor-pointer" @click="toggleSort('first_name')">First Name
                <span v-if="sortBy === 'first_name'">
                  <i :class="sortDirection === 'asc' ? 'fa fa-arrow-up' : 'fa fa-arrow-down'"></i>
                </span>
              </th>
              <th class="border p-2">Last Name</th>
              <th class="border p-2">Email</th>
              <th class="border p-2">City</th>
              <th class="border p-2">Country</th>
              <th class="border p-2">Actions</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="(u, index) in users" :key="u.id" @click="openModal(u.id)"
              class="hover:bg-gray-100 cursor-pointer">
              <td class="border p-2">{{ startIndex + index }}</td>
              <td class="border p-2">{{ u.first_name }}</td>
              <td class="border p-2">{{ u.last_name }}</td>
              <td class="border p-2">{{ u.email }}</td>
              <td class="border p-2">{{ u.address.city }}</td>
              <td class="border p-2">{{ u.address.country }}</td>
              <td class="border p-2">
                <div class="flex items-center justify-center space-x-4">
                  <i class="fa fa-pencil cursor-pointer hover:text-blue-600" @click.stop="openEditModal(u.id)"></i>
                  <i class="fa fa-trash cursor-pointer hover:text-red-600" @click.stop="deleteUser(u.id)"></i>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Pagination controls -->
      <div class="mt-4 flex justify-center space-x-2">
        <button :disabled="page <= 1" @click="page--"
          class="px-3 py-1 border rounded cursor-pointer hover:text-blue-600">
          Prev
        </button>
        <span>Page {{ page }} of {{ totalPages }}</span>
        <button :disabled="page >= totalPages" @click="page++"
          class="px-3 py-1 border rounded cursor-pointer hover:text-red-600">
          Next
        </button>

        <input type="number" v-model.number="gotoPage" :min="1" :max="totalPages" @keyup.enter="goToPage"
          class="w-16 text-center border rounded p-1" />
        <button @click="goToPage" class="px-3 py-1 border rounded cursor-pointer hover:text-green-700">
          Go
        </button>
      </div>
    </div>

    <!-- Modals -->
    <UserDetailModal v-if="showModal" :user="selectedUser!" :editMode="isEditing"
      @close="showModal = false; isEditing = false"
      @updated="() => { fetchUsers(); showModal = false; isEditing = false; }" />

    <UserCreateModal v-if="showCreate" @created="() => { showCreate = false; fetchUsers(); }"
      @close="showCreate = false" />

    <DeleteConfirmModal :show="showDelete" @cancel="showDelete = false; userToDelete = null"
      @confirm="onDeleteConfirm" />
  </div>
</template>
