<!-- components/settings/LicenseCategoriesTable.vue -->
<script setup>
import { ref, onMounted } from 'vue'
import api from '@/services/api'

const categories = ref([])

const fetchCategories = async () => {
  const { data } = await api.get('/license-categories')
  categories.value = data
}

onMounted(fetchCategories)
</script>

<template>
  <div>
    <div class="flex justify-between mb-4">
      <h2 class="text-lg font-semibold">Категории водительских прав</h2>
      <!-- future button: добавить -->
    </div>

    <table class="min-w-full border text-sm">
      <thead class="bg-gray-100">
        <tr>
          <th class="px-4 py-2 border text-left">ID</th>
          <th class="px-4 py-2 border text-left">Код</th>
          <th class="px-4 py-2 border text-left">Описание</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="cat in categories" :key="cat.id" class="hover:bg-gray-50">
          <td class="border px-4 py-2">{{ cat.id }}</td>
          <td class="border px-4 py-2">{{ cat.code }}</td>
          <td class="border px-4 py-2">{{ cat.description }}</td>
        </tr>
      </tbody>
    </table>
  </div>
</template>
