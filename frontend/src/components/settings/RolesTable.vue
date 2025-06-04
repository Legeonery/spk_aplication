<script setup>
import { ref, onMounted } from 'vue'
import api from '@/services/api'

const roles = ref([])

const fetchRoles = async () => {
  const { data } = await api.get('/roles')
  roles.value = data
}

onMounted(fetchRoles)
</script>

<template>
  <div>
    <div class="flex justify-between mb-4">
      <h2 class="text-lg font-semibold">Роли пользователей</h2>
      <!-- future button: добавить -->
    </div>

    <table class="min-w-full border text-sm">
      <thead class="bg-gray-100">
        <tr>
          <th class="px-4 py-2 border text-left">ID</th>
          <th class="px-4 py-2 border text-left">Название</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="role in roles" :key="role.id" class="hover:bg-gray-50">
          <td class="border px-4 py-2">{{ role.id }}</td>
          <td class="border px-4 py-2">{{ role.name }}</td>
        </tr>
      </tbody>
    </table>
  </div>
</template>
