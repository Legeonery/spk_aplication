<script setup>
import { ref, watch, onMounted, computed } from 'vue'
import api from '@/services/api'
import Multiselect from 'vue-multiselect'

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
  reason: '',
})

const parts = ref([])
const selectedPart = ref(null)
const error = ref('')

watch(() => props.show, async (val) => {
  if (val) {
    resetForm()
    await loadParts()
  }
})

const resetForm = () => {
  form.value = {
    name: '',
    article: '',
    quantity: null,
    date: new Date().toISOString().split('T')[0],
    reason: '',
  }
  selectedPart.value = null
  error.value = ''
}

const loadParts = async () => {
  try {
    const res = await api.get(`/warehouses/${props.warehouseId}/spare-parts`)
    parts.value = Array.isArray(res.data) ? res.data : res.data.data
  } catch (err) {
    console.error('Ошибка при загрузке запчастей:', err)
  }
}

const onPartSelect = (part) => {
  if (part) {
    form.value.name = part.name
    form.value.article = part.article
    if (form.value.quantity > part.quantity) {
      error.value = `На складе доступно только ${part.quantity} шт.`
    } else {
      error.value = ''
    }
  }
}

const validateQuantity = () => {
  if (selectedPart.value && form.value.quantity > selectedPart.value.quantity) {
    error.value = `Доступно только ${selectedPart.value.quantity} шт.`
    return false
  }
  return true
}

const save = async () => {
  error.value = ''
  if (!form.value.name || !form.value.quantity || !form.value.date || !form.value.reason) {
    error.value = 'Заполните все обязательные поля.'
    return
  }

  if (!validateQuantity()) return

  try {
    await api.post(`/warehouses/${props.warehouseId}/spare-parts/usages`, form.value)
    emit('success')
    emit('close')
  } catch (err) {
    console.error(err)
    error.value = 'Ошибка при сохранении. Попробуйте позже.'
  }
}

onMounted(() => {
  loadParts()
})
</script>

<template>
  <transition name="fade">
    <div v-if="show" class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 backdrop-blur-sm">
      <div class="bg-white rounded-xl shadow-lg p-6 w-full max-w-md relative animate-fade-in">
        <button class="absolute top-3 right-3 text-gray-500 hover:text-gray-700" @click="$emit('close')">✖</button>
        <h2 class="text-xl font-bold mb-4">Списание запчасти</h2>

        <div class="space-y-4">
          <Multiselect v-model="selectedPart" :options="parts" label="name" track-by="id" :searchable="true"
            placeholder="Выберите запчасть"
            :custom-label="(option) => `${option.name} (${option.article || '—'}) — Остаток: ${option.quantity}`"
            @update:modelValue="onPartSelect" />

          <input v-model="form.article" type="hidden" placeholder="Артикул" class="w-full border rounded px-4 py-2" />
          <input v-model.number="form.quantity" @blur="validateQuantity" type="number" min="0" placeholder="Количество"
            class="w-full border rounded px-4 py-2" />
          <input v-model="form.date" type="date" class="w-full border rounded px-4 py-2" />
          <input v-model="form.reason" type="text" placeholder="Причина списания"
            class="w-full border rounded px-4 py-2" />

          <p v-if="error" class="text-red-500 text-sm">{{ error }}</p>

          <div class="flex justify-end gap-2">
            <button @click="save" class="bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded">
              💾 Списать
            </button>
            <button @click="$emit('close')" class="text-gray-600 underline">Отмена</button>
          </div>
        </div>
      </div>
    </div>
  </transition>
</template>

<style scoped>
@import "vue-multiselect/dist/vue-multiselect.min.css";

.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.3s ease;
}

.fade-enter-from,
.fade-leave-to {
  opacity: 0;
}
</style>
