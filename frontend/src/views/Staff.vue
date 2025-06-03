<script setup>
import { onMounted, ref } from 'vue'
import { Plus as PlusIcon } from 'lucide-vue-next'
import api from '@/services/api'
import EmployeeForm from '@/components/EmployeeForm.vue'

const employees = ref([])
const showModal = ref(false)

const fetchEmployees = async () => {
  try {
    const response = await api.get('/users')
    employees.value = response.data.filter(emp => emp.is_active)
  } catch (e) {
    console.error('Ошибка загрузки сотрудников', e)
  }
}

onMounted(fetchEmployees)

const openForm = () => {
  showModal.value = true
}

const handleFormSuccess = () => {
  showModal.value = false
  fetchEmployees()
}
</script>

<template>
  <div class="p-6">
    <h1 class="text-2xl font-bold mb-6">Сотрудники</h1>

    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-8">
      <!-- Кнопка добавления -->
      <button
        class="flex flex-col items-center justify-center border-2 border-dashed rounded-md border-gray-300 p-8"
        @click="openForm">
        <PlusIcon class="w-14 h-14 text-gray-400" />
        <span class="text-gray-500 mt-2 font-medium">Добавить сотрудника</span>
      </button>

      <!-- Карточки сотрудников -->
      <div v-for="employee in employees" :key="employee.id"
        class="p-4 flex flex-col items-center justify-center border-2 rounded-md hover:bg-blue-50 transition cursor-default">
        <img :src="getAvatar(employee.role.name)" width="80" class="rounded-full mb-2" />
        <span class="font-medium text-gray-800 text-center">{{ employee.name }}</span>
        <span class="text-gray-500 text-sm">{{ employee.email }}</span>
        <span class="text-xs text-gray-400 mt-1">{{ roleLabel(employee.role.name) }}</span>
      </div>
    </div>

    <!-- Модальное окно -->
    <div v-if="showModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 backdrop-blur-sm">
      <EmployeeForm :show="showModal" @saved="handleFormSuccess" @close="showModal = false" />
    </div>
  </div>
</template>

<script>
function getAvatar(role) {
  switch (role) {
    case 'admin': return '/avatar-admin.png'
    case 'driver': return '/avatar-driver.png'
    case 'warehouse_manager': return '/avatar-warehouse.png'
    default: return '/avatar.png'
  }
}

function roleLabel(role) {
  switch (role) {
    case 'admin': return 'Администратор'
    case 'driver': return 'Водитель'
    case 'warehouse_manager': return 'Заведующий складом'
    default: return 'Неизвестная роль'
  }
}
</script>
