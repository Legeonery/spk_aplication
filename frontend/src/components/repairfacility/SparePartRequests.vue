<script setup>
import { ref, onMounted } from 'vue'
import api from '@/services/api'
import RequestTable from './RequestTable.vue'

const requests = ref([])
const userRole = ref(null)

const allStatuses = ['Новая', 'Заказано', 'Получено', 'Выдана', 'Отклонено']

const spareParts = ref([])

async function loadData() {
    const [res, me, parts] = await Promise.all([
        api.get('/spare-requests'),
        api.post('/auth/me'),
        api.get('/warehouses/2/spare-parts')
    ])

    requests.value = res.data

    spareParts.value = parts.data.map(p => ({
        id: p.id,
        name: p.name,
        quantity: parseFloat(p.quantity)
    }))

    userRole.value = me.data.role_id
}


function getAvailability(sparePartId) {
    const part = spareParts.value.find(p => p.id === sparePartId)
    return part ? part.quantity : 0
}

function filteredByStatus(statusList) {
    return requests.value.filter(r => statusList.includes(r.status))
}

async function updateStatus(requestId, newStatus) {
    await api.patch(`/spare-requests/${requestId}/status`, { status: newStatus })
    await loadData()
}

onMounted(loadData)
</script>

<template>
    <div class="space-y-8">
        <h2 class="text-xl font-bold">📝 Заявки на запчасти</h2>

        <div class="space-y-4">
            <h3 class="text-lg font-semibold text-blue-600">Открытые заявки</h3>
            <RequestTable :requests="filteredByStatus(['Новая', 'В работе', 'Заказано', 'Получено'])"
                :statuses="allStatuses" :can-edit="userRole === 1" :get-availability="getAvailability"
                @update-status="updateStatus" />
        </div>

        <div class="space-y-4">
            <h3 class="text-lg font-semibold text-gray-600">Закрытые заявки</h3>
            <RequestTable :requests="filteredByStatus(['Закрыта', 'Выдана', 'Отклонено'])" :statuses="allStatuses"
                :can-edit="userRole === 1" :get-availability="getAvailability" @update-status="updateStatus" />
        </div>
    </div>
</template>