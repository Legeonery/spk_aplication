<script setup>
import { ref, watch } from 'vue'
import api from '@/services/api'

const props = defineProps({
    modelValue: Boolean,
    warehouse: Object,
})
const emit = defineEmits(['close', 'updated'])

const form = ref({ name: '', area: null })

watch(
    () => props.modelValue,
    (val) => {
        if (val && props.warehouse) {
            form.value = {
                name: props.warehouse.name,
                area: props.warehouse.area,
                type: props.warehouse.type,
            }
        }
    },
    { immediate: true }
)


const save = async () => {
    try {
        await api.patch(`/warehouses/${props.warehouse.id}`, form.value)
        emit('updated')
        emit('close')
    } catch (err) {
        console.error('Ошибка при обновлении', err)
    }
}
</script>

<template>
    <div v-if="modelValue" class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 backdrop-blur-sm">
        <div class="bg-white p-6 rounded-xl max-w-md w-full shadow-xl">
            <h2 class="text-xl font-bold mb-4">Редактировать склад</h2>

            <input v-model="form.name" placeholder="Название" class="w-full border rounded px-4 py-2 mb-2" />

            <!-- Тип только для отображения -->
            <div class="w-full border rounded px-4 py-2 mb-2 bg-gray-100 text-gray-600">
                Тип: <strong>{{ props.warehouse.type }}</strong>
            </div>

            <input v-model.number="form.area" type="number" placeholder="Площадь"
                class="w-full border rounded px-4 py-2 mb-2" />

            <div class="flex justify-end gap-2 mt-4">
                <button @click="save" class="bg-yellow-500 text-white px-4 py-2 rounded">💾 Сохранить</button>
                <button @click="$emit('close')" class="text-gray-500">Отмена</button>
            </div>
        </div>
    </div>
</template>
