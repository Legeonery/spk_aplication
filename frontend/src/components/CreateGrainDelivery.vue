<script setup>
import { ref, watch } from 'vue'
import api from '@/services/api'
import { onMounted } from 'vue'

const props = defineProps({
    warehouseId: Number,
    show: Boolean
})

const emit = defineEmits(['close', 'success'])

const form = ref({
    grain_type_id: '',
    volume: '',
    delivery_date: '',
    vehicle_id: '',
    driver_id: ''
})

const grainTypes = ref([])
const vehicles = ref([])
const drivers = ref([])
const error = ref('')

const loadOptions = async () => {
    try {
        const grainsRes = await api.get('/grain-types')
        const vehiclesRes = await api.get('/vehicles')
        const driversRes = await api.get('/drivers')

        grainTypes.value = grainsRes.data
        vehicles.value = vehiclesRes.data
        drivers.value = driversRes.data
    } catch (err) {
        console.error('Ошибка при загрузке данных:', err)
    }
}
watch(() => props.show, (val) => {
    if (val) {
        loadOptions()
        form.value = {
            grain_type_id: '',
            volume: '',
            delivery_date: new Date().toISOString().split('T')[0],
            vehicle_id: '',
            driver_id: ''
        }
        error.value = ''
    }
})

const submit = async () => {
    error.value = ''
    try {
        const res = await api.post('/grain-deliveries', {
            ...form.value,
            warehouse_id: props.warehouseId
        })
        emit('success', res.data) // передаём данные в родительский компонент
        emit('close')
    } catch (err) {
        error.value = err.response?.data?.message || 'Ошибка при сохранении'
    }
}
onMounted(() => {
    loadOptions()
})
</script>

<template>
    <transition name="fade">
        <div v-if="show" class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 backdrop-blur-sm">
            <div class="bg-white rounded-xl shadow-lg p-6 w-full max-w-md relative animate-fade-in">
                <button class="absolute top-3 right-3 text-gray-500 hover:text-gray-700"
                    @click="$emit('close')">✖</button>
                <h2 class="text-xl font-bold mb-4">Добавить поставку</h2>
                <div class="space-y-4">
                    <select v-model="form.grain_type_id" class="w-full border rounded px-4 py-2">
                        <option disabled value="">Выберите культуру</option>
                        <option v-for="type in grainTypes" :key="type.id" :value="type.id">
                            {{ type.name }}
                        </option>
                    </select>

                    <input v-model="form.volume" type="number" min="0" placeholder="Объём (т)"
                        class="w-full border rounded px-4 py-2" />

                    <input v-model="form.delivery_date" type="date" class="w-full border rounded px-4 py-2" />

                    <select v-model="form.vehicle_id" class="w-full border rounded px-4     py-2">
                        <option disabled value="">Выберите транспорт</option>
                        <option v-for="v in vehicles.filter(v =>
                            ['привоз', 'универсальный'].includes(v.type) && v.latest_tare_measurement
                        )" :key="v.id" :value="v.id">
                            {{ v.number }} ({{ v.type }})
                        </option>
                    </select>

                    <select v-model="form.driver_id" class="w-full border rounded px-4 py-2">
                        <option disabled value="">Выберите водителя</option>
                        <option v-for="d in drivers" :key="d.id" :value="d.id">
                            {{ d.name }}
                        </option>
                    </select>

                    <p v-if="error" class="text-red-500 text-sm">{{ error }}</p>

                    <div class="flex justify-end gap-2">
                        <button @click="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded">
                            💾 Сохранить
                        </button>
                    </div>
                </div>
            </div>
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
