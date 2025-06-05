<script setup>
import { ref, onMounted } from 'vue'
import api from '@/services/api'

const parts = ref([])
const myVehicles = ref([])
const showRequestModal = ref(false)
const selectedPart = ref(null)
const me = ref(null)

const form = ref({
    vehicle_id: '',
    quantity: 1,
    note: ''
})

async function loadParts() {
    const [partRes, vehicleRes, meRes] = await Promise.all([
        api.get('/spare-parts/available'),
        api.get('/my-vehicles'),
        api.post('/auth/me')
    ])
    parts.value = partRes.data
    myVehicles.value = vehicleRes.data
    me.value = meRes.data
}

function openRequestForm(part) {
    selectedPart.value = part
    showRequestModal.value = true
}

async function submitRequest() {
    if (!form.value.vehicle_id || form.value.quantity < 1) {
        alert('Заполните все поля')
        return
    }

    const vehicleId = form.value.vehicle_id

    // 1. Обновляем статус ТС
    await api.patch(`/vehicles/${vehicleId}/status`, {
        status: 'на ремонте'
    })

    // 2. Добавляем запись в таблицу vehicle_repairs (если нет)
    await api.post('/repairs', {
        vehicle_id: vehicleId,
        reason: form.value.note || 'Ремонт по заявке на запчасть'
    }).catch(err => {
        // Если уже существует, Laravel вернёт 422 — пропускаем
        if (err.response?.status !== 422) throw err
    })

    await api.post('/spare-requests', {
        spare_part_id: selectedPart.value.id,
        vehicle_id: vehicleId,
        quantity: form.value.quantity,
        note: form.value.note,
        user_id: me.value.id
    })

    // Сброс формы и обновление
    showRequestModal.value = false
    form.value = { vehicle_id: '', quantity: 1, note: '' }
    await loadParts()
}

onMounted(loadParts)
</script>

<template>
    <div class="space-y-6">
        <h2 class="text-xl font-semibold">Имеющиеся запчасти</h2>

        <table class="min-w-full bg-white shadow rounded-xl overflow-hidden">
            <thead class="bg-gray-200 text-gray-700 text-sm uppercase">
                <tr>
                    <th class="text-left px-4 py-2">Название</th>
                    <th class="text-left px-4 py-2">Кол-во</th>
                    <th class="text-left px-4 py-2">Транспорт</th>
                    <th class="text-left px-4 py-2">Примечание</th>
                    <th class="text-left px-4 py-2">Статус</th>
                    <th class="text-left px-4 py-2">Действие</th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="p in parts" :key="p.id" class="border-b hover:bg-gray-50">
                    <td class="px-4 py-2">{{ p.name }}</td>
                    <td class="px-4 py-2">{{ p.quantity }}</td>
                    <td class="px-4 py-2">
                        <ul>
                            <li v-for="link in p.vehicles" :key="link.id">
                                {{ link.vehicle_name }} ({{ link.vehicle_number }})
                            </li>
                        </ul>
                    </td>
                    <td class="px-4 py-2">{{ p.notes }}</td>
                    <td class="px-4 py-2">
                        <span :class="p.quantity > 0 ? 'text-green-600 font-semibold' : 'text-red-500 font-semibold'">
                            {{ p.quantity > 0 ? 'Доступна' : 'Не доступна' }}
                        </span>
                    </td>
                    <td class="px-4 py-2">
                        <button @click="openRequestForm(p)"
                            class="bg-blue-600 hover:bg-blue-700 text-white px-3 py-1 rounded text-sm">
                            Запросить
                        </button>
                    </td>
                </tr>
            </tbody>
        </table>

        <!-- Модалка заявки -->
        <transition name="fade">
            <div v-if="showRequestModal"
                class="fixed inset-0 bg-black/30 backdrop-blur-sm flex items-center justify-center z-50">
                <div class="bg-white p-6 rounded-xl shadow-md w-full max-w-md space-y-4">
                    <h3 class="text-lg font-bold">Заявка на: {{ selectedPart?.name }}</h3>

                    <select v-model="form.vehicle_id" class="w-full border rounded p-2">
                        <option disabled value="">Выберите ТС</option>
                        <option v-for="v in myVehicles" :key="v.id" :value="v.id">
                            {{ v.name }} ({{ v.number }})
                        </option>
                    </select>

                    <input v-model.number="form.quantity" type="number" min="1" class="w-full border p-2 rounded"
                        placeholder="Количество" />

                    <textarea v-model="form.note" class="w-full border p-2 rounded" placeholder="Примечание" />

                    <div class="flex justify-end gap-2">
                        <button @click="showRequestModal = false" class="text-gray-500">Отмена</button>
                        <button @click="submitRequest" class="bg-green-600 text-white px-4 py-2 rounded">
                            Отправить
                        </button>
                    </div>
                </div>
            </div>
        </transition>
    </div>
</template>
