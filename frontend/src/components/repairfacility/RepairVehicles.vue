<script setup>
import { ref, onMounted } from 'vue'
import api from '@/services/api'

const repairs = ref([])
const requests = ref([])
const myVehicles = ref([])
const showForm = ref(false)

const newRequest = ref({
    custom_name: '',
    vehicle_id: '',
    quantity: 1,
    note: ''
})
const spareParts = ref([])
const me = ref(null)

async function loadData() {
    const [r, reqs, s, v, meRes] = await Promise.all([
        api.get('/repairs'),
        api.get('/spare-requests'),
        api.get('/warehouses/2/spare-parts'),
        api.get('/my-vehicles'),
        api.post('/auth/me')
    ])

    me.value = meRes.data

    requests.value = reqs.data.filter(r => r.user_id === me.value.id)

    repairs.value = r.data.filter(repair =>
        repair.vehicle &&
        repair.vehicle.driver_id === me.value.id &&
        repair.vehicle.status === 'на ремонте'
    )

    myVehicles.value = v.data
    spareParts.value = s.data.map(p => ({
        id: p.id,
        name: p.name,
        quantity: parseFloat(p.quantity)
    }))
}

function getRequestsForVehicle(vehicleId) {
    return requests.value.filter(r => r.vehicle_id === vehicleId)
}

async function createRequest() {
    const payload = { ...newRequest.value }

    if (!payload.custom_name) {
        alert('Укажите наименование запчасти')
        return
    }

    try {
        // 1. Обновление статуса ТС
        await api.patch(`/vehicles/${payload.vehicle_id}/status`, {
            status: 'на ремонте'
        })

        // 2. Добавление записи в repairs (если ещё нет)
        await api.post('/repairs', {
            vehicle_id: payload.vehicle_id,
            reason: payload.note || 'Заявка на запчасть'
        }).catch(err => {
            if (err.response?.status !== 422) throw err
        })

        // 3. Создание заявки
        await api.post('/spare-requests', payload)

        // (Дополнительно) 4. Можно уменьшить складской остаток вручную, если нужно:
        // await api.post(`/warehouses/2/spare-parts/usages`, {
        //   spare_part_id: найденныйId, quantity: payload.quantity, reason: 'По заявке'
        // })

        // 5. Обновление интерфейса
        await loadData()
        showForm.value = false
        newRequest.value = {
            custom_name: '',
            vehicle_id: '',
            quantity: 1,
            note: ''
        }
    } catch (err) {
        alert('Ошибка при создании заявки')
        console.error(err)
    }
}

onMounted(loadData)
</script>

<template>
    <div class="space-y-6">
        <h2 class="text-xl font-bold">ТС в ремонте</h2>

        <ul class="space-y-4">
            <li v-for="repair in repairs.filter(r => r.vehicle)" :key="repair.id"
                class="p-4 border rounded-xl bg-white shadow-sm">
                <div class="font-semibold">
                    {{ repair.vehicle.name }} ({{ repair.vehicle.number }})
                </div>
                <div class="text-gray-600 text-sm mb-2">Причина: {{ repair.reason }}</div>

                <div class="text-sm text-gray-800 mt-2">
                    <h4 class="font-medium mb-1">Заявки на запчасти:</h4>

                    <ul v-if="getRequestsForVehicle(repair.vehicle.id).length" class="space-y-1">
                        <li v-for="req in getRequestsForVehicle(repair.vehicle.id)" :key="req.id"
                            class="flex justify-between items-center gap-2">
                            <div>
                                🔧 <strong>{{ req.spare_part?.name || req.custom_name || 'Новая запчасть' }}</strong>
                                — {{ req.quantity }} шт
                                <span v-if="req.note" class="text-gray-400 italic">({{ req.note }})</span>
                            </div>
                            <div>
                                <span class="text-sm px-2 py-1 rounded" :class="{
                                    'bg-yellow-100 text-yellow-700': req.status === 'Новая',
                                    'bg-blue-100 text-blue-700': req.status === 'Получено',
                                    'bg-green-100 text-green-700': req.status === 'Выдана',
                                    'bg-red-100 text-red-600': req.status === 'Отклонено',
                                    'bg-gray-100 text-gray-600': req.status === 'Заказано'
                                }">
                                    {{ req.status }}
                                </span>
                            </div>
                        </li>
                    </ul>

                    <p v-else class="text-gray-400">Нет заявок.</p>
                </div>
            </li>
        </ul>

        <!-- Кнопка открытия модального окна -->
        <button @click="showForm = true" class="px-4 py-2 bg-blue-600 text-white rounded-lg">
            Создать заявку
        </button>

        <!-- Модальное окно -->
        <div v-if="showForm" class="fixed inset-0 bg-black/40 z-50 flex items-center justify-center">
            <form @submit.prevent="createRequest"
                class="bg-white p-6 rounded-xl w-full max-w-md shadow-lg space-y-4 relative animate-fade-in">
                <button @click="showForm = false" type="button"
                    class="absolute top-2 right-3 text-gray-500 hover:text-gray-700 text-lg">×</button>
                <h3 class="text-lg font-bold">Создание заявки</h3>

                <select v-model="newRequest.vehicle_id" required class="w-full border rounded p-2">
                    <option disabled value="">Выберите ТС</option>
                    <option v-for="v in myVehicles" :key="v.id" :value="v.id">
                        {{ v.name }} ({{ v.number }})
                    </option>
                </select>

                <input v-model="newRequest.custom_name" type="text" class="w-full border p-2 rounded"
                    placeholder="Наименование запчасти" required />

                <input v-model.number="newRequest.quantity" type="number" min="1" class="w-full border p-2 rounded"
                    placeholder="Количество" />

                <textarea v-model="newRequest.note" class="w-full border p-2 rounded"
                    placeholder="Примечание (необязательно)" />

                <div class="flex justify-end gap-2 pt-2">
                    <button type="button" @click="showForm = false" class="text-sm text-gray-600 underline">
                        Отмена
                    </button>
                    <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">
                        Отправить
                    </button>
                </div>
            </form>
        </div>
    </div>
</template>

<style scoped>
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
