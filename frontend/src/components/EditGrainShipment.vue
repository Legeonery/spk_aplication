<script setup>
import { ref, watch, computed, watchEffect } from 'vue'
import api from '@/services/api'

const props = defineProps({
  shipment: Object,
  show: Boolean,
  grains: Array,
})

const emit = defineEmits(['close', 'success'])

const form = ref({
  grain_type_id: '',
  volume: '',
  shipment_date: '',
  vehicle_id: '',
  driver_id: '',
  tare_weight: 0,
  max_weight: null,
  id: null,
})

const error = ref('')
const loading = ref(false)
const vehicles = ref([])
const drivers = ref([])

watchEffect(async () => {
  if (!props.show || !props.shipment) return

  try {
    const [vRes, dRes] = await Promise.all([
      api.get('/vehicles'),
      api.get('/users', { params: { role: 'Водитель' } }),
    ])

    vehicles.value = vRes.data
    drivers.value = dRes.data

    const vehicle = vRes.data.find((v) => v.id === props.shipment.vehicle_id)
    const tare = Number(props.shipment.tare_weight)
    const netto = Number(props.shipment.volume || 0)

    form.value = {
      grain_type_id: props.shipment.grain_type_id,
      volume: netto + tare,
      shipment_date: props.shipment.shipment_date,
      vehicle_id: props.shipment.vehicle_id,
      driver_id: props.shipment.driver_id,
      tare_weight: tare,
      max_weight: vehicle?.max_weight || null,
      id: props.shipment.id,
    }

    error.value = ''
  } catch (err) {
    console.error('Ошибка загрузки транспорта или водителей', err)
  }
})

watch(
  () => form.value.vehicle_id,
  (vehicleId) => {
    if (form.value.id) return // 💥 не трогать tare_weight при редактировании
    const vehicle = vehicles.value.find((v) => v.id === Number(vehicleId))
    if (!vehicle) return
    form.value.tare_weight = Number(vehicle?.latest_tare_measurement?.tare_weight || 0)
    form.value.max_weight = vehicle.max_weight || null
  }
)

const selectedTareWeight = computed(() =>
  form.value.tare_weight !== null && !isNaN(form.value.tare_weight)
    ? form.value.tare_weight
    : null
)

function validateVolume() {
  const grain = props.grains.find((g) => g.grain_type.id === form.value.grain_type_id)
  const available = Number(grain?.amount ?? 0)

  const gross = Number(form.value.volume)
  const tare = Number(form.value.tare_weight)
  console.log(tare);

  if (isNaN(gross) || isNaN(tare)) return true // отложим валидацию, пока не заполнены поля

  const newNetto = gross - tare
  const oldNetto = Number(props.shipment?.volume || 0) // уже нетто из БД

  const delta = newNetto - oldNetto

  if (delta > 0 && delta > available) {
    error.value = `Недостаточно остатка: доступно ${available.toFixed(2)} кг, а вы хотите дополнительно отгрузить ${delta.toFixed(2)} кг.`
    return false
  }

  return true
}

async function submit() {
  const gross = Number(form.value.volume)
  const tare = Number(form.value.tare_weight)
  const netto = gross - tare

  if (isNaN(gross) || isNaN(tare)) {
    error.value = 'Укажите корректные значения объёма и тары'
    return
  }

  if (gross < tare) {
    error.value = `Вес брутто (${gross} кг) не может быть меньше тары (${tare} кг)`
    return
  }

  if (form.value.max_weight && gross > form.value.max_weight) {
    error.value = `Вес брутто (${gross} кг) превышает допустимый вес ТС (${form.value.max_weight} кг)`
    return
  }

  if (!validateVolume()) return

  try {
    loading.value = true
    await api.put(`/grain-shipments/${form.value.id}`, {
      grain_type_id: form.value.grain_type_id,
      shipment_date: form.value.shipment_date,
      vehicle_id: form.value.vehicle_id,
      driver_id: form.value.driver_id,
      volume: netto,
      tare_weight: tare,
    })
    emit('success')
    emit('close')
  } catch (err) {
    error.value = err.response?.data?.message || 'Ошибка при сохранении'
  } finally {
    loading.value = false
  }
}
</script>

<template>
  <transition name="fade">
    <div v-if="show" class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 backdrop-blur-sm">
      <div class="bg-white rounded-xl shadow-lg p-6 w-full max-w-md relative animate-fade-in">
        <button class="absolute top-3 right-3 text-gray-500 hover:text-gray-700" @click="$emit('close')">
          ✖
        </button>
        <h2 class="text-xl font-bold mb-4">Редактировать отгрузку</h2>

        <div class="space-y-4">
          <select v-model="form.grain_type_id" class="w-full border rounded px-4 py-2">
            <option disabled value="">Выберите культуру</option>
            <option v-for="g in grains" :key="g.grain_type.id" :value="g.grain_type.id">
              {{ g.grain_type.name }}
            </option>
          </select>

          <input v-model.number="form.volume" type="number" min="0" placeholder="Объём (кг)"
            class="w-full border rounded px-4 py-2" />

          <input v-model="form.shipment_date" type="date" class="w-full border rounded px-4 py-2" />

          <!-- Транспорт: readonly при редактировании -->
          <div v-if="form.id" class="w-full border rounded px-4 py-2 bg-gray-100 text-gray-600">
            Транспорт:
            <strong>
              {{
                vehicles.find((v) => v.id === form.vehicle_id)?.number
              }}
              ({{vehicles.find((v) => v.id === form.vehicle_id)?.type}})
            </strong>
          </div>

          <p v-if="selectedTareWeight !== null" class="text-sm text-gray-600">
            ⚖️ Тара ТС: <strong>{{ selectedTareWeight }} кг.</strong>
          </p>

          <select v-model="form.driver_id" class="w-full border rounded px-4 py-2">
            <option disabled value="">Выберите водителя</option>
            <option v-for="d in drivers" :key="d.id" :value="d.id">
              {{ d.name }}
            </option>
          </select>

          <p v-if="error" class="text-red-500 text-sm">{{ error }}</p>

          <div class="flex justify-end gap-2">
            <button @click="submit" class="bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded"
              :disabled="loading">
              💾 Сохранить
            </button>
            <button @click="$emit('close')" class="text-gray-500 hover:underline">Отмена</button>
          </div>
        </div>
      </div>
    </div>
  </transition>
</template>
