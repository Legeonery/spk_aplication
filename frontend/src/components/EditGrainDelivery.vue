<script setup>
import { ref, watch } from 'vue'
import api from '@/services/api'

const props = defineProps({
  delivery: Object,
  show: Boolean,
})

const emit = defineEmits(['close', 'success'])

const form = ref({
  grain_type_id: '',
  volume: '',
  delivery_date: '',
  vehicle_id: '',
  driver_id: '',
  tare_weight: 0,
})

const error = ref('')
const grainTypes = ref([])
const vehicles = ref([])
const drivers = ref([])

const loadOptions = async () => {
  const [grainsRes, vehiclesRes, driversRes] = await Promise.all([
    api.get('/grain-types'),
    api.get('/vehicles'),
    api.get('/drivers'),
  ])
  grainTypes.value = grainsRes.data
  vehicles.value = vehiclesRes.data
  drivers.value = driversRes.data
}

watch(
  () => props.show,
  async (val) => {
    if (val && props.delivery) {
      await loadOptions()

      const tare = parseFloat(props.delivery.tare_weight || 0)
      const netto = parseFloat(props.delivery.volume || 0)

      form.value = {
        ...props.delivery,
        volume: tare + netto,
        tare_weight: tare,
      }

      error.value = ''
    }
  },
  { immediate: true },
)

watch(
  () => form.value.vehicle_id,
  (vehicleId) => {
    if (!vehicleId) return

    const vehicle = vehicles.value.find((v) => v.id === parseInt(vehicleId))
    const tare = parseFloat(vehicle?.latest_tare_measurement?.tare_weight || 0)
    form.value.tare_weight = tare
  },
)

const submit = async () => {
  error.value = ''

  const gross = Number(form.value.volume)
  const tare = Number(form.value.tare_weight || 0)

  console.log('➡️ gross:', gross, 'tare:', tare)

  if (isNaN(gross) || isNaN(tare)) {
    error.value = 'Укажите корректное значение объёма и тары'
    return
  }

  if (gross < tare) {
    error.value = `Вес Брутто (${gross} кг) не может быть меньше тары (${tare} кг)`
    return
  }

  const netto = gross - tare

  try {
    await api.put(`/grain-deliveries/${form.value.id}`, {
      grain_type_id: form.value.grain_type_id,
      delivery_date: form.value.delivery_date,
      vehicle_id: form.value.vehicle_id,
      driver_id: form.value.driver_id,
      volume: netto,
      tare_weight: tare,
    })

    emit('success')
    emit('close')
  } catch (err) {
    error.value = err.response?.data?.message || 'Ошибка при обновлении'
  }
}
</script>

<template>
  <transition name="fade">
    <div
      v-if="show"
      class="fixed inset-0 bg-black/40 flex items-center justify-center z-50 backdrop-blur-sm"
    >
      <div class="bg-white rounded-xl shadow p-6 w-full max-w-md relative animate-fade-in">
        <button class="absolute top-3 right-3" @click="$emit('close')">✖</button>
        <h2 class="text-xl font-bold mb-4">Редактировать поставку</h2>

        <div class="space-y-3">
          <select v-model="form.grain_type_id" class="w-full border px-3 py-2 rounded">
            <option value="">Выберите культуру</option>
            <option v-for="g in grainTypes" :key="g.id" :value="g.id">{{ g.name }}</option>
          </select>

          <input
            type="number"
            v-model.number="form.volume"
            placeholder="Объём (кг)"
            class="w-full border px-3 py-2 rounded"
          />
          <input type="date" v-model="form.delivery_date" class="w-full border px-3 py-2 rounded" />

          <select v-model="form.vehicle_id" class="w-full border px-3 py-2 rounded">
            <option value="">Выберите транспорт</option>
            <option v-for="v in vehicles" :key="v.id" :value="v.id">
              {{ v.number }} ({{ v.type }})
            </option>
          </select>

          <select v-model="form.driver_id" class="w-full border px-3 py-2 rounded">
            <option value="">Выберите водителя</option>
            <option v-for="d in drivers" :key="d.id" :value="d.id">{{ d.name }}</option>
          </select>

          <p v-if="error" class="text-red-500 text-sm">{{ error }}</p>

          <div class="flex justify-end mt-2">
            <button @click="submit" class="bg-blue-600 text-white px-4 py-2 rounded">
              💾 Сохранить
            </button>
          </div>
        </div>
      </div>
    </div>
  </transition>
</template>
