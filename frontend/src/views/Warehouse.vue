<script setup>
import { ref, onMounted } from 'vue'
import api from '@/services/api'
import { Plus as PlusIcon, X as XIcon } from 'lucide-vue-next'

const warehouses = ref([])
const showModal = ref(false)
const form = ref({
  name: '',
  type: '',
  area: null,
  max_historical_load: null,
})
const error = ref('')

const fetchWarehouses = async () => {
  try {
    const response = await api.get('/warehouses')
    warehouses.value = response.data.data
  } catch (err) {
    console.error('Ошибка при загрузке складов:', err)
  }
}

const createWarehouse = async () => {
  error.value = ''

  if (!form.value.name || !form.value.type) {
    error.value = 'Пожалуйста, заполните наименование и выберите тип склада.'
    return
  }

  try {
    await api.post('/warehouses', form.value)
    showModal.value = false
    form.value.name = ''
    form.value.type = ''
    form.value.area = null
    form.value.max_historical_load = null
    const response = await api.get('/warehouses')
    warehouses.value = response.data.data
  } catch (err) {
    error.value = 'Ошибка при создании склада. Попробуйте позже.'
    console.error(err)
  }
}
onMounted(fetchWarehouses)
</script>

<template>
  <div class="p-6">
    <h1 class="text-2xl font-bold mb-6">Складские помещения</h1>

    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-8">
      <!-- Кнопка добавления склада -->
      <button
        @click="showModal = true"
        class="flex flex-col items-center justify-center border-2 border-dashed rounded-md border-gray-300 p-8 hover:bg-blue-50"
      >
        <PlusIcon class="w-12 h-12 text-gray-400" />
        <span class="text-gray-500 mt-2">Добавить склад</span>
      </button>

      <!-- Список складов -->
      <router-link
        v-for="warehouse in warehouses"
        :key="warehouse.id"
        :to="`/warehouse/${warehouse.id}`"
        class="p-4 flex flex-col items-center justify-center border-2 rounded-md shadow-sm hover:bg-gray-50 transition"
      >
        <img src="../../public/wheat (1).png" width="120px" />
        <span class="text-gray-800 mt-2 font-medium">{{ warehouse.name }}</span>
        <span class="text-gray-500 text-sm">Тип: {{ warehouse.type }}</span>
        <span class="text-gray-500 text-sm">Площадь: {{ warehouse.area }} м²</span>
        <span class="text-gray-500 text-sm">
          Макс. загрузка (тонн): {{ warehouse.max_historical_load }}
        </span>
      </router-link>
    </div>

    <!-- Модальное окно -->
    <div v-if="showModal" class="fixed inset-0 flex items-center justify-center z-50">
      <div class="bg-white p-6 rounded-lg shadow-lg w-full max-w-md relative">
        <button class="absolute top-3 right-3" @click="showModal = false">
          <XIcon class="w-5 h-5 text-gray-500" />
        </button>
        <h2 class="text-xl font-bold mb-4">Новый склад</h2>

        <div class="space-y-4">
          <input
            v-model="form.name"
            type="text"
            placeholder="Наименование"
            class="w-full border rounded px-3 py-2"
          />
          <select v-model="form.type" class="w-full border rounded px-3 py-2 bg-white">
            <option disabled value="">Выберите тип склада</option>
            <option value="Зерновой">Зерновой</option>
            <option value="Склад запчастей">Склад запчастей</option>
            <option value="Другое">Другое</option>
          </select>
          <input
            v-model="form.area"
            type="number"
            placeholder="Площадь"
            class="w-full border rounded px-3 py-2"
          />
          <input
            v-model="form.max_historical_load"
            type="number"
            placeholder="Макс. загрузка"
            class="w-full border rounded px-3 py-2"
          />

          <p class="text-red-500 text-sm" v-if="error">{{ error }}</p>

          <div class="flex justify-end">
            <button
              @click="createWarehouse"
              class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded"
            >
              Сохранить
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<style scoped>
button {
  transition: all 0.4s ease;
}
@media (hover: hover) {
  button:hover {
    background: hsl(204, 70%, 53%, 0.2);
  }
}
</style>
