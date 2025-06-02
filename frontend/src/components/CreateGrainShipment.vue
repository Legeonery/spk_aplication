<script setup>
import { ref, watch, onMounted } from 'vue'
import api from '@/services/api'

const props = defineProps({
    warehouseId: Number,
    show: Boolean
})

const emit = defineEmits(['close', 'success'])

const form = ref({
    grain_type_id: '',
    volume: '',
    shipment_date: new Date().toISOString().split('T')[0],
    vehicle_id: '',
    driver_id: ''
})

const grainTypes = ref([])
const grains = ref([]) // Остатки
const vehicles = ref([])
const drivers = ref([])
const error = ref('')

const loadOptions = async () => {
    try {
        const [typesRes, vehiclesRes, driversRes, stockRes] = await Promise.all([
            api.get('/grain-types'),
            api.get('/vehicles'),
            api.get('/drivers'),
            api.get(`/warehouses/${props.warehouseId}/grains`)
        ])
        grainTypes.value = typesRes.data
        vehicles.value = vehiclesRes.data
        drivers.value = driversRes.data
        grains.value = stockRes.data
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
            shipment_date: new Date().toISOString().split('T')[0],
            vehicle_id: '',
            driver_id: ''
        }
        error.value = ''
    }
})

function validateVolume() {
    const grain = grains.value.find(g => g.grain_type.id === parseInt(form.value.grain_type_id))
    const available = grain?.amount ?? 0
    const requested = parseFloat(form.value.volume || 0)

    if (requested > available) {
        error.value = `Недостаточно зерна на складе. Остаток: ${available} т`
        return false
    }

    error.value = ''
    return true
}

const submit = async () => {
    if (!validateVolume()) return

    try {
        await api.post('/grain-shipments', {
            ...form.value,
            warehouse_id: props.warehouseId
        })
        emit('success')
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
                <h2 class="text-xl font-bold mb-4">Добавить отгрузку</h2>

                <div class="space-y-4">
                    <select v-model="form.grain_type_id" class="w-full border rounded px-4 py-2">
                        <option disabled value="">Выберите культуру</option>
                        <option v-for="type in grainTypes" :key="type.id" :value="type.id">
                            {{ type.name }}
                        </option>
                    </select>

                    <input v-model="form.volume" @blur="validateVolume" type="number" min="0" placeholder="Объём (т)"
                        class="w-full border rounded px-4 py-2" />

                    <input v-model="form.shipment_date" type="date" class="w-full border rounded px-4 py-2" />

                    <select v-model="form.vehicle_id" class="w-full border rounded px-4 py-2">
                        <option disabled value="">Выберите транспорт</option>
                        <option v-for="v in vehicles.filter(v => v.type === 'отгрузка' || v.type === 'универсальный')"
                            :key="v.id" :value="v.id">
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
