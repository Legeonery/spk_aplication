<script setup>
const props = defineProps({
    requests: Array,
    statuses: Array,
    canEdit: Boolean,
    getAvailability: Function,
})
const emit = defineEmits(['update-status'])
</script>

<template>
    <table class="min-w-full text-sm border-separate border-spacing-y-1">
        <thead class="bg-gray-100 text-gray-600 uppercase text-xs">
            <tr>
                <th class="text-left px-4 py-2">Запчасть</th>
                <th class="text-left px-4 py-2">Кол-во</th>
                <th class="text-left px-4 py-2">ТС</th>
                <th class="text-left px-4 py-2">Примечание</th>
                <th class="text-left px-4 py-2">Статус</th>
                <th v-if="canEdit" class="text-left px-4 py-2">Действие</th>
            </tr>
        </thead>
        <tbody>
            <tr v-for="r in props.requests" :key="r.id" class="bg-white hover:bg-gray-50 transition">
                <td class="px-4 py-2">{{ r.spare_part?.name || r.custom_name }}</td>
                <td class="px-4 py-2">{{ r.quantity }}</td>
                <td class="px-4 py-2">{{ r.vehicle?.name }} ({{ r.vehicle?.number }})</td>
                <td class="px-4 py-2">{{ r.note || '—' }}</td>
                <td class="px-4 py-2 font-medium">{{ r.status }}</td>
                <td v-if="canEdit" class="px-4 py-2">
                    <!-- 1. Новая — запчасти НЕТ -->
                    <template v-if="r.status === 'Новая' && getAvailability(r.spare_part_id) < r.quantity">
                        <button @click="emit('update-status', r.id, 'Заказано')"
                            class="bg-yellow-400 hover:bg-yellow-500 text-white px-2 py-1 rounded text-sm">
                            Заказать
                        </button>
                        <button @click="emit('update-status', r.id, 'Отклонено')"
                            class="ml-2 bg-red-500 hover:bg-red-600 text-white px-2 py-1 rounded text-sm">
                            Отклонить
                        </button>
                    </template>

                    <!-- 2. Новая — запчасти ЕСТЬ -->
                    <template v-else-if="r.status === 'Новая' && getAvailability(r.spare_part_id) >= r.quantity">
                        <button @click="emit('update-status', r.id, 'Выдана')"
                            class="bg-green-500 hover:bg-green-600 text-white px-2 py-1 rounded text-sm">
                            Выдать
                        </button>
                        <button @click="emit('update-status', r.id, 'Отклонено')"
                            class="ml-2 bg-red-500 hover:bg-red-600 text-white px-2 py-1 rounded text-sm">
                            Отклонить
                        </button>
                    </template>

                    <!-- 3. Заказано -->
                    <template v-else-if="r.status === 'Заказано'">
                        <button @click="emit('update-status', r.id, 'Получено')"
                            class="bg-blue-500 hover:bg-blue-600 text-white px-2 py-1 rounded text-sm">
                            Подтвердить поступление
                        </button>
                        <button @click="emit('update-status', r.id, 'Отклонено')"
                            class="ml-2 bg-red-500 hover:bg-red-600 text-white px-2 py-1 rounded text-sm">
                            Отклонить
                        </button>
                    </template>

                    <!-- 4. Получено -->
                    <template v-else-if="r.status === 'Получено'">
                        <button @click="emit('update-status', r.id, 'Выдана')"
                            :disabled="getAvailability(r.spare_part_id) < r.quantity"
                            class="bg-green-500 hover:bg-green-600 text-white px-2 py-1 rounded text-sm disabled:opacity-50 disabled:cursor-not-allowed">
                            Выдать
                        </button>
                    </template>

                    <!-- 5. Выдана / Отклонено / Закрыта -->
                    <template v-else>
                        <span class="text-gray-500 text-sm italic">{{ r.status }}</span>
                    </template>
                </td>
            </tr>
        </tbody>
    </table>

    <p v-if="!requests.length" class="text-center text-gray-400 mt-4">Нет заявок</p>
</template>
