<script setup>
import { ref, onMounted, computed, watchEffect } from 'vue'
import Multiselect from 'vue-multiselect'
import 'vue-multiselect/dist/vue-multiselect.min.css'
import api from '@/services/api'

const props = defineProps({
    show: Boolean,
    user: Object, // редактируемый пользователь, если передан
})

const emit = defineEmits(['saved', 'close'])

const roles = ref([])
const categories = ref([])

const form = ref({
    name: '',
    email: '',
    password: '',
    role: null,
    categoryObjects: [],
})

const isEditMode = computed(() => !!props.user?.id)
const isSubmitting = ref(false)

watchEffect(() => {
    if (props.show && props.user) {
        const user = props.user
        form.value = {
            name: user.name,
            email: user.email,
            password: '',
            role: roles.value.find(r => r.name === user.role?.name) || null,
            categoryObjects: user.driver?.license_categories || [],
        }
    } else if (props.show && !props.user) {
        form.value = {
            name: '',
            email: '',
            password: '',
            role: null,
            categoryObjects: [],
        }
    }
})

const submit = async () => {
    isSubmitting.value = true

    try {
        const payload = {
            name: form.value.name,
            email: form.value.email,
            password: form.value.password || undefined,
            role: form.value.role?.name ?? '',
            categories: form.value.role?.name === 'Водитель'
                ? form.value.categoryObjects.map(c => c.code).join(',')
                : null,
        }

        if (isEditMode.value) {
            await api.put(`/users/${props.user.id}`, payload)
        } else {
            await api.post('/users', payload)
        }

        emit('saved')
    } catch (e) {
        console.error('Ошибка при сохранении сотрудника:', e)
        if (e.response?.data?.errors) {
            console.table(e.response.data.errors)
        }
    } finally {
        isSubmitting.value = false
    }
}

onMounted(async () => {
    const [rolesRes, categoriesRes] = await Promise.all([
        api.get('/roles'),
        api.get('/license-categories'),
    ])
    roles.value = rolesRes.data
    categories.value = categoriesRes.data

    if (props.userToEdit) {
        form.value.name = props.userToEdit.name
        form.value.email = props.userToEdit.email
        form.value.role = roles.value.find(r => r.name === props.userToEdit.role.name)

        // если водитель — проставляем его категории
        if (form.value.role?.name === 'Водитель' && props.userToEdit.driver?.license_categories) {
            const codes = props.userToEdit.driver.license_categories.map(c => c.code)
            form.value.categoryObjects = categories.value.filter(cat => codes.includes(cat.code))
        }
    }
})

</script>

<template>
    <div class="bg-white rounded-xl p-6 shadow-xl max-w-md w-full">
        <h2 class="text-lg font-bold mb-4">
            {{ isEditMode ? 'Редактировать сотрудника' : 'Добавить сотрудника' }}
        </h2>

        <form @submit.prevent="submit" class="space-y-4">
            <div>
                <label class="block text-sm font-medium text-gray-700">ФИО</label>
                <input v-model="form.name" type="text" class="w-full border rounded p-2" required />
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">Email (логин)</label>
                <input v-model="form.email" type="email" class="w-full border rounded p-2" required />
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">Пароль</label>
                <input v-model="form.password" :required="!isEditMode" type="password"
                    class="w-full border rounded p-2" />
                <small v-if="isEditMode" class="text-gray-500">Оставьте пустым, чтобы не менять</small>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">Роль</label>
                <Multiselect v-model="form.role" :options="roles" placeholder="Выберите роль" label="name"
                    track-by="id" />
            </div>

            <div v-if="form.role?.name === 'Водитель'">
                <label class="block text-sm font-medium text-gray-700">Категории прав</label>
                <Multiselect v-model="form.categoryObjects" :options="categories" :multiple="true"
                    placeholder="Выберите категории" label="code" track-by="id" />
            </div>

            <div class="flex justify-end gap-2 pt-2">
                <button type="button" @click="emit('close')" class="px-4 py-2 bg-gray-300 rounded hover:bg-gray-400">
                    Отмена
                </button>
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700"
                    :disabled="isSubmitting">
                    Сохранить
                </button>
            </div>
        </form>
    </div>
</template>
