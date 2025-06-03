<script setup>
import { ref, watch } from 'vue'
import api from '@/services/api'

defineProps({
  show: Boolean,
})

const emit = defineEmits(['saved', 'close'])

const form = ref({
  name: '',
  email: '',
  password: '',
  role: '',
  categories: '',
})

const isSubmitting = ref(false)

const submit = async () => {
  isSubmitting.value = true

  try {
    const payload = {
      name: form.value.name,
      email: form.value.email,
      password: form.value.password,
      role: form.value.role,
      categories: form.value.role === 'driver' ? form.value.categories : null,
    }

    await api.post('/users', payload)
    emit('saved')
  } catch (e) {
    console.error('Ошибка при добавлении сотрудника:', e)
  } finally {
    isSubmitting.value = false
  }
}
</script>

<template>
  <div class="bg-white rounded-xl p-6 shadow-xl max-w-md w-full">
    <h2 class="text-lg font-bold mb-4">Добавить сотрудника</h2>

    <form @submit.prevent="submit" class="space-y-4">
      <div>
        <label class="block text-sm font-medium text-gray-700">ФИО</label>
        <input v-model="form.name" type="text" class="w-full border rounded p-2" required />
      </div>

      <div>
        <label class="block text-sm font-medium text-gray-700">Email (логин)</label>
        <input v-model="form.email" type="email" class="w-full border rounded p-2" required />
      </div>

      <div>
        <label class="block text-sm font-medium text-gray-700">Пароль</label>
        <input v-model="form.password" type="password" class="w-full border rounded p-2" required />
      </div>

      <div>
        <label class="block text-sm font-medium text-gray-700">Роль</label>
        <select v-model="form.role" class="w-full border rounded p-2" required>
          <option disabled value="">Выберите роль</option>
          <option value="admin">Администратор</option>
          <option value="warehouse_manager">Заведующий складом</option>
          <option value="driver">Водитель</option>
        </select>
      </div>

      <div v-if="form.role === 'driver'">
        <label class="block text-sm font-medium text-gray-700">Категории прав (напр. B,C,E)</label>
        <input v-model="form.categories" type="text" class="w-full border rounded p-2" />
      </div>

      <div class="flex justify-end gap-2 pt-2">
        <button type="button" @click="emit('close')" class="px-4 py-2 bg-gray-300 rounded hover:bg-gray-400">
          Отмена
        </button>
        <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700" :disabled="isSubmitting">
          Сохранить
        </button>
      </div>
    </form>
  </div>
</template>
