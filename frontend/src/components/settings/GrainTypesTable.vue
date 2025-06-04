<script setup>
import { ref, onMounted } from 'vue'
import api from '@/services/api'
import { toast } from 'vue-sonner'

const grainTypes = ref([])
const newGrain = ref('')
const showAddModal = ref(false)
const deletingId = ref(null)

const fetchGrainTypes = async () => {
  const { data } = await api.get('/grain-types')
  grainTypes.value = data
}

const addGrainType = async () => {
  try {
    const { data } = await api.post('/grain-types', { name: newGrain.value })
    grainTypes.value.push(data)
    toast.success('Культура добавлена')
    newGrain.value = ''
    showAddModal.value = false
  } catch (e) {
    toast.error('Ошибка при добавлении')
  }
}

const deleteGrainType = async (id) => {
  if (!confirm('Удалить культуру?')) return
  try {
    await api.delete(`/grain-types/${id}`)
    grainTypes.value = grainTypes.value.filter(item => item.id !== id)
    toast.success('Культура удалена')
  } catch (e) {
    if (e.response?.status === 409) {
      toast.error('Нельзя удалить: используется в других таблицах')
    } else {
      toast.error('Ошибка при удалении')
    }
  }
}

onMounted(fetchGrainTypes)
</script>

<template>
  <div>
    <div class="flex justify-between mb-4">
      <h2 class="text-lg font-semibold">Виды зерна</h2>
      <button
        @click="showAddModal = true"
        class="bg-green-600 hover:bg-green-700 text-white px-4 py-1 rounded text-sm"
      >
        + Добавить
      </button>
    </div>

    <table class="min-w-full border text-sm">
      <thead class="bg-gray-100">
        <tr>
          <th class="px-4 py-2 border text-left">ID</th>
          <th class="px-4 py-2 border text-left">Название</th>
          <th class="px-4 py-2 border text-left">Действия</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="type in grainTypes" :key="type.id" class="hover:bg-gray-50">
          <td class="border px-4 py-2">{{ type.id }}</td>
          <td class="border px-4 py-2">{{ type.name }}</td>
          <td class="border px-4 py-2">
            <button
              @click="deleteGrainType(type.id)"
              class="text-red-600 hover:underline text-xs"
            >
              Удалить
            </button>
          </td>
        </tr>
      </tbody>
    </table>

    <!-- Модальное окно добавления -->
    <div
      v-if="showAddModal"
      class="fixed inset-0 bg-black/40 backdrop-blur-sm bg-opacity-40 flex items-center justify-center z-50"
    >
      <div class="bg-white p-6 rounded shadow-md w-full max-w-sm">
        <h3 class="text-lg font-bold mb-4">Добавить культуру</h3>
        <input
          v-model="newGrain"
          placeholder="Название"
          class="w-full border px-3 py-2 rounded mb-4"
        />
        <div class="flex justify-end gap-2">
          <button @click="showAddModal = false" class="text-sm text-gray-600">Отмена</button>
          <button
            @click="addGrainType"
            class="bg-green-600 text-white px-4 py-1 rounded text-sm"
          >
            Добавить
          </button>
        </div>
      </div>
    </div>
  </div>
</template>
