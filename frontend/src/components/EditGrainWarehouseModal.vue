<script setup>
import { ref, watch } from 'vue'
import api from '@/services/api'

const props = defineProps({
    modelValue: Boolean,
    warehouse: Object,
})
const emit = defineEmits(['close', 'updated'])

const form = ref({
    name: '',
    area: null,
    max_historical_load: null,
})
const error = ref('')

watch(
    () => props.modelValue,
    (val) => {
        if (val && props.warehouse) {
            form.value.name = props.warehouse.name
            form.value.area = props.warehouse.area
            form.value.max_historical_load = props.warehouse.max_historical_load
        }
    },
    { immediate: true }
)

const save = async () => {
    error.value = ''
    if (!form.value.name || !form.value.area) {
        error.value = 'Заполните все обязательные поля.'
        return
    }

    try {
        await api.patch(`/warehouses/${props.warehouse.id}`, {
            name: form.value.name,
            area: form.value.area,
            max_historical_load: form.value.max_historical_load,
            type: props.warehouse.type
        })
        emit('updated')
        emit('close')
    } catch (err) {
        console.error('Ошибка при обновлении склада:', err)
        error.value = err.response?.data?.message || 'Ошибка при обновлении'
    }
}
</script>

<template>
    <transition name="fade">
        <div v-if="modelValue" class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 backdrop-blur-sm">
            <div class="bg-white p-6 rounded-xl max-w-md w-full shadow-xl relative animate-fade-in">
                <button class="absolute top-3 right-3 text-gray-500 hover:text-gray-700"
                    @click="$emit('close')">✖</button>
                <h2 class="text-xl font-bold mb-4">Редактировать зерновой склад</h2>

                <input v-model="form.name" placeholder="Название" class="w-full border rounded px-4 py-2 mb-2" />

                <div class="w-full border rounded px-4 py-2 mb-2 bg-gray-100 text-gray-600">
                    Тип: <strong>{{ props.warehouse.type }}</strong>
                </div>

                <input v-model.number="form.area" type="number" placeholder="Площадь (м²)"
                    class="w-full border rounded px-4 py-2 mb-2" />

                <input v-model.number="form.max_historical_load" type="number" placeholder="Макс. загрузка (т)"
                    class="w-full border rounded px-4 py-2 mb-2" />

                <p v-if="error" class="text-red-500 text-sm mt-1">{{ error }}</p>

                <div class="flex justify-end gap-2 mt-4">
                    <button @click="save" class="bg-yellow-500 text-white px-4 py-2 rounded">💾 Сохранить</button>
                    <button @click="$emit('close')" class="text-gray-500">Отмена</button>
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
