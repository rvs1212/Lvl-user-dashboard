<script setup lang="ts">
  import { ref, watch } from 'vue'
  
  const props = defineProps<{
    show: boolean
  }>()
  
  const emit = defineEmits<{
    (e: 'cancel'): void
    (e: 'confirm'): void
  }>()
  
  // local visibility so we can animate
  const visible = ref(false)
  watch(() => props.show, val => {
    visible.value = val
  }, { immediate: true })
  
  function cancel() {
    visible.value = false
    setTimeout(() => emit('cancel'), 200)
  }
  
  function confirm() {
    visible.value = false
    setTimeout(() => emit('confirm'), 200)
  }
</script>

<template>
    <transition name="fade">
      <div
        v-if="visible"
        class="fixed inset-0 bg-[#000000b8] flex items-center justify-center z-50">
        <div class="bg-white rounded-lg shadow-lg w-full max-w-sm p-6 mx-4">
          <h3 class="text-xl font-semibold mb-4">Confirm Deletion</h3>
          <p class="mb-6">Are you sure you want to delete this user? This action cannot be undone.</p>
          <div class="flex justify-end space-x-2">
            <button @click="cancel" class="px-4 py-2 bg-gray-300 rounded hover:bg-gray-400 cursor-pointer">
              Cancel
            </button>
            <button @click="confirm" class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700 cursor-pointer">
              Delete
            </button>
          </div>
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
  