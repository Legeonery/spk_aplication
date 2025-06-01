<script setup>
import { ref, watch } from 'vue'
import api from '@/services/api'

const props = defineProps({
  warehouseId: Number,
  show: Boolean,
})
const emit = defineEmits(['close', 'success'])

const form = ref({
  name: '',
  article: '',
  quantity: null,
  date: new Date().toISOString().split('T')[0],
})
const error = ref('')

watch(
  () => props.show,
  (val) => {
    if (val) resetForm()
  },
)

const resetForm = () => {
  form.value = {
    name: '',
    article: '',
    quantity: null,
    date: new Date().toISOString().split('T')[0],
  }
  error.value = ''
}

const save = async () => {
  console.log('Сохраняем поступление...', form.value)

  error.value = ''
  if (!form.value.name || !form.value.quantity || !form.value.date) {
    error.value = 'Заполните все обязательные поля.'
    return
  }

  try {
    await api.post(`/warehouses/${props.warehouseId}/spare-parts/deliveries`, form.value)
    emit('close') // Закрываем модалку
    emit('success') // Если в родителе нужна доп. реакция
  } catch (err) {
    console.error('Ошибка при сохранении:', err)
    if (err.response?.data?.message) {
      error.value = err.response.data.message
    } else {
      error.value = 'Ошибка при сохранении. Попробуйте позже.'
    }
  }
}
</script>

<template>
  <transition name="fade">
    <div
      v-if="show"
      class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 backdrop-blur-sm"
    >
      <form
        @submit.prevent="save"
        class="bg-white rounded-xl shadow-lg p-6 w-full max-w-md relative animate-fade-in"
      >
        <button
          type="button"
          class="absolute top-3 right-3 text-gray-500 hover:text-gray-700"
          @click="$emit('close')"
        >
          ✖
        </button>
        <h2 class="text-xl font-bold mb-4">Добавление поступления запчасти</h2>

        <div class="space-y-4">
          <input
            v-model="form.name"
            type="text"
            placeholder="Название запчасти"
            class="w-full border rounded px-4 py-2"
          />
          <input
            v-model="form.article"
            type="text"
            placeholder="Артикул (необязательно)"
            class="w-full border rounded px-4 py-2"
          />
          <input
            v-model.number="form.quantity"
            type="number"
            placeholder="Количество"
            class="w-full border rounded px-4 py-2"
          />
          <input v-model="form.date" type="date" class="w-full border rounded px-4 py-2" />

          <p class="text-red-500 text-sm" v-if="error">{{ error }}</p>

          <div class="flex justify-end gap-2">
            <button
              type="submit"
              class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded"
            >
              💾 Сохранить
            </button>
            <button type="button" @click="$emit('close')" class="text-gray-600 underline">
              Отмена
            </button>
          </div>
        </div>
      </form>
    </div>
  </transition>
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
</style>
