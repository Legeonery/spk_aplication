<script setup>
import { ref, watch } from 'vue'
import api from '@/services/api'

const props = defineProps({
  shipment: Object,
  show: Boolean
})

const emit = defineEmits(['close', 'success'])

const form = ref({ ...props.shipment })
const error = ref('')
const grainTypes = ref([])
const vehicles = ref([])
const drivers = ref([])

watch(() => props.show, async (val) => {
  if (val && props.shipment) {
    form.value = { ...props.shipment }
    const [grainsRes, vehiclesRes, driversRes] = await Promise.all([
      api.get('/grain-types'),
      api.get('/vehicles'),
      api.get('/drivers')
    ])
    grainTypes.value = grainsRes.data
    vehicles.value = vehiclesRes.data.filter(v => v.type === 'отгрузка' || v.type === 'универсальный')
    drivers.value = driversRes.data
    error.value = ''
  }
})

const submit = async () => {
  try {
    await api.put(`/grain-shipments/${form.value.id}`, form.value)
    emit('success')
    emit('close')
  } catch (err) {
    error.value = err.response?.data?.message || 'Ошибка при обновлении'
  }
}
</script>

<template>
  <transition name="fade">
    <div v-if="show" class="fixed inset-0 bg-black/40 flex items-center justify-center z-50 backdrop-blur-sm">
      <div class="bg-white rounded-xl shadow p-6 w-full max-w-md relative animate-fade-in">
        <button class="absolute top-3 right-3" @click="$emit('close')">✖</button>
        <h2 class="text-xl font-bold mb-4">Редактировать отгрузку</h2>

        <div class="space-y-3">
          <select v-model="form.grain_type_id" class="w-full border px-3 py-2 rounded">
            <option value="">Выберите культуру</option>
            <option v-for="g in grainTypes" :key="g.id" :value="g.id">{{ g.name }}</option>
          </select>

          <input type="number" v-model="form.volume" placeholder="Объём (т)" class="w-full border px-3 py-2 rounded" />
          <input type="date" v-model="form.shipment_date" class="w-full border px-3 py-2 rounded" />

          <select v-model="form.vehicle_id" class="w-full border px-3 py-2 rounded">
            <option value="">Выберите транспорт</option>
            <option v-for="v in vehicles" :key="v.id" :value="v.id">{{ v.number }} ({{ v.type }})</option>
          </select>

          <select v-model="form.driver_id" class="w-full border px-3 py-2 rounded">
            <option value="">Выберите водителя</option>
            <option v-for="d in drivers" :key="d.id" :value="d.id">{{ d.name }}</option>
          </select>

          <p v-if="error" class="text-red-500 text-sm">{{ error }}</p>

          <div class="flex justify-end mt-2">
            <button @click="submit" class="bg-purple-600 text-white px-4 py-2 rounded">💾 Сохранить</button>
          </div>
        </div>
      </div>
    </div>
  </transition>
</template>
