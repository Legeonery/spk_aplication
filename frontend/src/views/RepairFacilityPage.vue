<script setup>
import { ref, onMounted } from 'vue'
import api from '@/services/api'
import RepairVehicles from '@/components/repairfacility/RepairVehicles.vue'
import AvailableSpareParts from '@/components/repairfacility/AvailableSpareParts.vue'
import SparePartRequests from '@/components/repairfacility/SparePartRequests.vue'

const userRole = ref(null)
const activeTab = ref(null) // просто начальное значение
const tabs = ref([])

onMounted(async () => {
    const { data } = await api.post('/auth/me')
    userRole.value = data.role_id

    // 👇 тут правильно меняем значение, не ref
    activeTab.value = userRole.value === 3 ? 'repairs' : 'requests'

    tabs.value = [
        ...(userRole.value === 3 ? [{ key: 'repairs', label: '🛠️ ТС в ремонте' }] : []),
        { key: 'parts', label: '📦 Запчасти на складе' },
        ...(userRole.value === 1 ? [{ key: 'requests', label: '📝 Заявки на запчасти' }] : [])
    ]
})
</script>

<template>
    <div class="p-6 max-w-6xl mx-auto space-y-10">
        <h1 class="text-2xl font-bold mb-4">Ремонтная база</h1>

        <div class="flex gap-2 border-b">
            <button v-for="tab in tabs" :key="tab.key" @click="activeTab = tab.key" :class="[
                'px-4 py-2 text-sm font-medium rounded-t-md transition',
                activeTab === tab.key
                    ? 'bg-blue-600 text-white'
                    : 'bg-gray-100 hover:bg-gray-200 text-gray-600',
            ]">
                {{ tab.label }}
            </button>
        </div>

        <div class="pt-4 space-y-6">
            <div v-if="activeTab === 'repairs'" class="bg-white rounded-xl shadow p-6 border space-y-4">
                <RepairVehicles />
            </div>
            <div v-if="activeTab === 'parts'" class="bg-white rounded-xl shadow p-6 border space-y-4">
                <AvailableSpareParts />
            </div>
            <div v-if="activeTab === 'requests'" class="bg-white rounded-xl shadow p-6 border space-y-4">
                <SparePartRequests />
            </div>
        </div>
    </div>
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
