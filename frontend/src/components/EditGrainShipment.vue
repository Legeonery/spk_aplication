<script setup>
import { ref, watch } from 'vue'
import api from '@/services/api'
const vehicles = ref([])
const drivers = ref([])


const props = defineProps({
  shipment: Object,
  show: Boolean,
  grains: Array
})
const emit = defineEmits(['close', 'success'])

const form = ref({
  grain_type_id: null,
  volume: null,
  shipment_date: '',
  vehicle_id: null,
  driver_id: null,
})
const error = ref('')
const loading = ref(false)

watch(() => props.show, async (val) => {
  if (val) {
    try {
      const [vRes, dRes] = await Promise.all([
        api.get('/vehicles'),
        api.get('/drivers')
      ])
      vehicles.value = vRes.data
      drivers.value = dRes.data

      // только после загрузки — устанавливаем значения
      if (props.shipment) {
        Object.assign(form.value, {
          grain_type_id: props.shipment.grain_type_id,
          volume: props.shipment.volume,
          shipment_date: props.shipment.shipment_date,
          vehicle_id: props.shipment.vehicle_id,
          driver_id: props.shipment.driver_id,
          id: props.shipment.id
        })
      }

      error.value = ''
    } catch (err) {
      console.error('Ошибка при загрузке транспорта или водителей', err)
    }
  }
})

function validateVolume() {
  const grain = props.grains.find(g => g.grain_type.id === form.value.grain_type_id)
  const available = grain?.amount ?? 0

  if (form.value.volume > available) {
    error.value = `Остаток по этой культуре: ${available} т. Уменьшите объём.`
    return false
  }

  error.value = ''
  return true
}

async function submit() {
  if (!validateVolume()) return

  loading.value = true
  try {
    await api.put(`/grain-shipments/${form.value.id}`, form.value)
    emit('success')
  } catch (e) {
    error.value = 'Ошибка при сохранении'
  } finally {
    loading.value = false
  }
}
</script>


<template>
  <div v-if="show" class="fixed inset-0 bg-black/50 flex items-center justify-center z-50">
    <div class="bg-white rounded-lg p-6 w-full max-w-md relative">
      <h2 class="text-lg font-bold mb-4">Редактировать отгрузку</h2>

      <div class="space-y-4">
        <select v-model="form.grain_type_id" class="w-full border rounded px-4 py-2">
          <option disabled value="">Выберите культуру</option>
          <option v-for="g in grains" :key="g.grain_type.id" :value="g.grain_type.id">
            {{ g.grain_type.name }}
          </option>
        </select>

        <input type="number" v-model="form.volume" @blur="validateVolume" class="w-full border rounded px-4 py-2"
          placeholder="Объём, т" />

        <input type="date" v-model="form.shipment_date" class="w-full border rounded px-4 py-2" />

        <select v-model="form.vehicle_id" class="w-full border px-3 py-2 rounded">
          <option disabled value="">Выберите транспорт</option>
          <option v-for="v in vehicles.filter(v => v.type === 'отгрузка' || v.type === 'универсальный')" :key="v.id"
            :value="v.id">
            {{ v.number }} ({{ v.type }})
          </option>
        </select>

        <select v-model="form.driver_id" class="w-full border px-3 py-2 rounded">
          <option value="">Выберите водителя</option>
          <option v-for="d in drivers" :key="d.id" :value="d.id">{{ d.name }}</option>
        </select>

        <p class="text-sm text-red-500" v-if="error">{{ error }}</p>

        <div class="flex justify-end gap-2">
          <button @click="submit" class="bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded"
            :disabled="loading">💾 Сохранить</button>
          <button @click="$emit('close')" class="text-gray-500 hover:underline">Отмена</button>
        </div>
      </div>
    </div>
  </div>
</template>
