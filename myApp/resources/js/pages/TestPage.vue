<script setup lang="ts">
import { ref } from 'vue';
import { useApi } from '@/composables/useApi';

import Loader from '@/components/Loader.vue';


const users = ref<any[]>([]);
const loading = ref(false);
const { get } = useApi();

const SendRequest = async () => {
  loading.value = true;
  try {
    users.value = await get('/test-api');
  } catch (error) {
    console.error('API error:', error);
  } finally {
    loading.value = false;
  }
};
</script>

<template>
    <div class="p-4">
      <h1 class="text-xl font-bold mb-4">Test API Page</h1>
  
      <button
        @click="SendRequest"
        class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700"
      >
      SendRequest
      </button>
  
      <Loader v-if="loading" />
  
      <div v-if="users.length" class="mt-4">
        <ul>
          <li v-for="user in users" :key="user.id" class="border-b py-1">
            {{ user.name }} ({{ user.email }})
          </li>
        </ul>
      </div>
    </div>
  </template>
  
 
  