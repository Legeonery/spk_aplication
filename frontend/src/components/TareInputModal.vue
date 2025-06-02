<script setup>
import { ref, onMounted, watch } from 'vue'
import api from '@/services/api'
import { X as XIcon } from 'lucide-vue-next'

const tareExists = ref(false)

const props = defineProps({
    show: Boolean
})
const emit = defineEmits(['close', 'saved'])

const tareWeight = ref(null)
const selectedVehicleId = ref(null)
const vehicles = ref([])
const error = ref('')
const loading = ref(false)

// Загрузка транспорта, подходящего для замера тары
const fetchEligibleVehicles = async () => {
    try {
        const res = await api.get('/vehicles')
        vehicles.value = res.data.filter(v =>
            ['привоз', 'отгрузка', 'универсальный'].includes(v.type) && !v.is_available
        )
    } catch (e) {
        console.error('Ошибка загрузки транспорта', e)
    }
}

watch(() => props.show, (val) => {
    if (val) {
        tareWeight.value = null
        selectedVehicleId.value = null
        error.value = ''
        fetchEligibleVehicles()
    }
})

const submit = async () => {
    error.value = ''
    if (!selectedVehicleId.value) return error.value = 'Выберите ТС'
    if (!tareWeight.value || tareWeight.value <= 0) {
        return error.value = 'Введите корректный вес тары'
    }

    loading.value = true
    try {
        await api.post('/tare-measurements', {
            vehicle_id: selectedVehicleId.value,
            tare_weight: tareWeight.value
        })
        emit('saved')
    } catch (e) {
        error.value = 'Ошибка при сохранении тары'
    } finally {
        loading.value = false
    }
}

watch(selectedVehicleId, async (id) => {
    if (!id) return tareExists.value = false

    try {
        const res = await api.get('/tare-measurements/check', { params: { vehicle_id: id } })
        tareExists.value = res.data.exists
    } catch (e) {
        console.error('Ошибка при проверке тары', e)
        tareExists.value = false
    }
})
</script>

<template>
    <transition name="fade">
        <div v-if="show" class="fixed inset-0 z-50 bg-black/30 backdrop-blur-sm flex items-center justify-center">
            <div class="bg-white p-6 rounded-xl w-full max-w-md shadow-xl relative animate-fade-in">
                <button class="absolute top-3 right-3" @click="$emit('close')">
                    <XIcon class="w-5 h-5 text-gray-500 hover:text-gray-700" />
                </button>

                <h2 class="text-xl font-bold mb-4">Ввод тары</h2>

                <div class="space-y-4">
                    <select v-model="selectedVehicleId"
                        class="w-full border border-gray-300 rounded-lg px-4 py-2 bg-white focus:outline-none focus:ring focus:border-blue-400">
                        <option disabled value="">Выберите транспортное средство</option>
                        <option v-for="v in vehicles" :key="v.id" :value="v.id">
                            {{ v.vehicle_kind?.name || 'ТС' }} — {{ v.number }}
                        </option>
                    </select>
                    <p v-if="tareExists" class="text-sm text-yellow-600">
                        ⚠️ У этого ТС уже есть замер тары. Новый замер обновит существующий.
                    </p>
                    <input type="number" v-model="tareWeight" placeholder="Введите вес тары, т"
                        class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring focus:border-blue-400" />

                    <p class="text-sm text-red-500" v-if="error">{{ error }}</p>

                    <div class="flex justify-end">
                        <button @click="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded"
                            :disabled="loading">
                            Сохранить
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
</style>