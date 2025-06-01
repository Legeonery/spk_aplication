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

  // Очищаем загрузку, если тип не зерновой
  const dataToSend = { ...form.value }
  if (form.value.type !== 'зерновой') {
    dataToSend.max_historical_load = null
  }

  try {
    await api.post('/warehouses', dataToSend)
    showModal.value = false
    form.value = {
      name: '',
      type: '',
      area: null,
      max_historical_load: null,
    }
    const response = await api.get('/warehouses')
    warehouses.value = response.data.data
  } catch (err) {
    error.value = 'Ошибка при создании склада. Попробуйте позже.'
    console.error(err)
  }
}
const getWarehouseLink = (warehouse) => {
  const type = warehouse.type.toLowerCase()
  if (type === 'склад запчастей') {
    return `/warehouse-spare-parts/${warehouse.id}`
  }
  return `/warehouse/${warehouse.id}`
}
onMounted(fetchWarehouses)
</script>

<template>
  <div class="p-6">
    <h1 class="text-3xl font-semibold mb-8">Складские помещения</h1>

    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-6">
      <!-- Кнопка добавления склада -->
      <button
        @click="showModal = true"
        class="flex flex-col items-center justify-center border-2 border-dashed rounded-2xl border-gray-300 p-6 hover:bg-blue-50 hover:border-blue-400 transition"
      >
        <PlusIcon class="w-10 h-10 text-gray-400" />
        <span class="text-gray-600 mt-2 font-medium">Добавить склад</span>
      </button>

      <!-- Список складов -->
      <router-link
        v-for="warehouse in warehouses"
        :key="warehouse.id"
        :to="getWarehouseLink(warehouse)"
        class="p-4 flex flex-col items-center justify-center border rounded-xl shadow hover:shadow-md hover:bg-gray-50 transition"
      >
        <img
          :src="
            warehouse.type === 'зерновой'
              ? '../../public/wheat (1).png'
              : '../../public/wheat_component.png'
          "
          width="100"
          class="mb-3"
        />

        <span class="text-gray-800 text-lg font-semibold">{{ warehouse.name }}</span>

        <span class="text-gray-500 text-sm flex items-center gap-1">
          {{ warehouse.type }}
        </span>

        <span class="text-gray-500 text-sm">Площадь: {{ warehouse.area }} м²</span>

        <span
          v-if="warehouse.type === 'зерновой' && warehouse.max_historical_load > 0"
          class="text-gray-500 text-sm"
        >
          Макс. загрузка: {{ warehouse.max_historical_load }} т
        </span>
      </router-link>
    </div>

    <!-- Модальное окно -->
    <transition name="fade">
      <div
        v-if="showModal"
        class="fixed inset-0 z-50 flex items-center justify-center bg-black/30 backdrop-blur-sm"
      >
        <div class="bg-white rounded-xl shadow-xl p-6 w-full max-w-md relative animate-fade-in">
          <button class="absolute top-3 right-3" @click="showModal = false">
            <XIcon class="w-5 h-5 text-gray-500 hover:text-gray-700" />
          </button>

          <h2 class="text-2xl font-bold mb-4 text-gray-800">Создание склада</h2>

          <div class="space-y-4">
            <input
              v-model="form.name"
              type="text"
              placeholder="Наименование склада"
              class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring focus:border-blue-400"
            />

            <select
              v-model="form.type"
              class="w-full border border-gray-300 rounded-lg px-4 py-2 bg-white focus:outline-none focus:ring focus:border-blue-400"
            >
              <option disabled value="">Выберите тип склада</option>
              <option value="Зерновой">Зерновой</option>
              <option value="Склад запчастей">Склад запчастей</option>
              <option value="Другое">Другое</option>
            </select>

            <input
              v-model="form.area"
              type="number"
              placeholder="Площадь (м²)"
              class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring focus:border-blue-400"
            />

            <input
              v-if="form.type === 'Зерновой'"
              v-model="form.max_historical_load"
              type="number"
              placeholder="Макс. загрузка (тонн)"
              class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring focus:border-blue-400"
            />

            <p class="text-red-500 text-sm" v-if="error">{{ error }}</p>

            <div class="flex justify-end">
              <button
                @click="createWarehouse"
                class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2 rounded-lg transition"
              >
                Сохранить
              </button>
            </div>
          </div>
        </div>
      </div>
    </transition>
  </div>
</template>

<style scoped>
.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.3s ease;
}

.fade-enter-from,
.fade-leave-to {
  opacity: 0;
}

.animate-fade-in {
  animation: fadeIn 0.3s ease-out;
}

@keyframes fadeIn {
  from {
    opacity: 0;
    transform: scale(0.95);
  }

  to {
    opacity: 1;
    transform: scale(1);
  }
}
</style>
