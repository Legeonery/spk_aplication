<script setup>
import { ref, onMounted, watch, computed } from 'vue'
import api from '@/services/api'
import { X as XIcon } from 'lucide-vue-next'

const usageOptions = ref([])
const vehicleKinds = ref([])
const drivers = ref([])

const props = defineProps({ show: Boolean })
const emit = defineEmits(['close', 'saved'])

const form = ref({
  name: '',
  number: '',
  vehicle_kind_id: null,
  type: '',
  max_weight: null,
  status: 'на ходу',
  driver_id: null,
  is_available: true,
  notes: '',
  repair_reason: '',
})

const showTypeField = ref(false)
const showRepairReason = ref(false)
const error = ref('')
const loading = ref(false)

const resetForm = () => {
  Object.assign(form.value, {
    name: '',
    number: '',
    vehicle_kind_id: null,
    type: '',
    max_weight: null,
    status: 'на ходу',
    driver_id: null,
    is_available: true,
    notes: '',
    repair_reason: '',
  })
  error.value = ''
}

onMounted(async () => {
  try {
    const [kindRes, driverRes] = await Promise.all([api.get('/vehicle-kinds'), api.get('/drivers')])
    vehicleKinds.value = kindRes.data
    drivers.value = driverRes.data
  } catch (err) {
    console.error('Ошибка при загрузке данных', err)
  }
})

watch(
  () => props.show,
  (val) => {
    if (val) resetForm()
  },
)

watch(
  () => form.value.vehicle_kind_id,
  (id) => {
    const kind = vehicleKinds.value.find((k) => k.id === id)
    const name = kind?.name?.toLowerCase()

    if (name === 'трактор') {
      usageOptions.value = ['работа в поле', 'привоз', 'отгрузка', 'универсальный']
      form.value.type = ''
      showTypeField.value = true
    } else if (name === 'камаз') {
      usageOptions.value = ['привоз', 'отгрузка', 'универсальный']
      form.value.type = ''
      showTypeField.value = true
    } else {
      form.value.type = 'работа в поле'
      usageOptions.value = [] // лишнее, можно опустить
      showTypeField.value = false // 👈 не показываем вовсе
    }
  },
)

watch(
  () => form.value.status,
  (status) => {
    showRepairReason.value = status === 'на ремонте'
  },
)

const validateForm = () => {
  if (!form.value.number) return (error.value = 'Введите гос. номер')
  if (!form.value.vehicle_kind_id) return (error.value = 'Выберите тип ТС')
  if (showTypeField.value && !form.value.type)
    return (error.value = 'Выберите тип (привоз/отгрузка)')
  if (!form.value.max_weight || form.value.max_weight <= 0)
    return (error.value = 'Введите корректный вес')
  if (form.value.status === 'на ремонте' && !form.value.repair_reason)
    return (error.value = 'Укажите причину ремонта')
  error.value = ''
  return true
}

const submit = async () => {
  if (!validateForm()) return

  loading.value = true
  try {
    await api.post('/vehicles', form.value)
    emit('saved')
  } catch (e) {
    error.value = 'Ошибка при сохранении'
  } finally {
    loading.value = false
  }
}
const showMaxWeight = computed(() => {
  return ['привоз', 'отгрузка', 'универсальный'].includes(form.value.type)
})
watch(
  () => form.value.type,
  (val) => {
    if (val === 'работа в поле') {
      form.value.max_weight = null
    }
  },
)
</script>

<template>
  <transition name="fade">
    <div
      v-if="show"
      class="fixed inset-0 z-50 flex items-center justify-center bg-black/30 backdrop-blur-sm"
    >
      <div class="bg-white rounded-xl shadow-xl p-6 w-full max-w-md relative animate-fade-in">
        <button class="absolute top-3 right-3" @click="$emit('close')">
          <XIcon class="w-5 h-5 text-gray-500 hover:text-gray-700" />
        </button>

        <h2 class="text-2xl font-bold mb-4 text-gray-800">Добавить транспорт</h2>

        <div class="space-y-4">
          <!-- Гос. номер -->
          <input
            v-model="form.number"
            type="text"
            placeholder="Введите гос. номер (например, А123БВ 163)"
            class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring focus:border-blue-400"
          />
          <input
            v-model="form.name"
            placeholder="Введите название ТС (например, Камаз №2)"
            class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring focus:border-blue-400"
          />
          <!-- Тип ТС -->
          <select
            v-model="form.vehicle_kind_id"
            class="w-full border border-gray-300 rounded-lg px-4 py-2 bg-white focus:outline-none focus:ring focus:border-blue-400"
          >
            <option disabled value="">Выберите тип транспортного средства</option>
            <option v-for="k in vehicleKinds" :key="k.id" :value="k.id">{{ k.name }}</option>
          </select>

          <!-- Тип (привоз/отгрузка/универсальный) -->
          <select
            v-if="showTypeField"
            v-model="form.type"
            class="w-full border border-gray-300 rounded-lg px-4 py-2 bg-white focus:outline-none focus:ring focus:border-blue-400"
          >
            <option disabled value="">Выберите тип использования транспорта</option>
            <option v-for="opt in usageOptions" :key="opt" :value="opt">{{ opt }}</option>
          </select>

          <!-- Максимальный вес -->
          <input
            v-if="showMaxWeight"
            type="number"
            v-model="form.max_weight"
            placeholder="Введите максимальный вес в килограммах (например, 15000)"
            class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring focus:border-blue-400"
          />

          <!-- Статус -->
          <select
            v-model="form.status"
            class="w-full border border-gray-300 rounded-lg px-4 py-2 bg-white focus:outline-none focus:ring focus:border-blue-400"
          >
            <option disabled value="">Выберите текущий статус ТС</option>
            <option value="на ходу">На ходу</option>
            <option value="на ремонте">На ремонте</option>
            <option value="не на ходу">Неактивно</option>
          </select>

          <!-- Причина ремонта -->
          <input
            v-if="showRepairReason"
            v-model="form.repair_reason"
            placeholder="Укажите причину ремонта"
            class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring focus:border-blue-400"
          />

          <!-- Водитель -->
          <select
            v-model="form.driver_id"
            class="w-full border border-gray-300 rounded-lg px-4 py-2 bg-white focus:outline-none focus:ring focus:border-blue-400"
          >
            <option disabled value="">Выберите ответственного водителя</option>
            <option v-for="d in drivers" :key="d.id" :value="d.id">{{ d.name }}</option>
          </select>

          <!-- Примечание -->
          <textarea
            v-model="form.notes"
            placeholder="Дополнительная информация или примечание"
            class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring focus:border-blue-400"
          />

          <p class="text-red-500 text-sm" v-if="error">{{ error }}</p>

          <div class="flex justify-end">
            <button
              @click="submit"
              class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2 rounded-lg transition"
              :disabled="loading"
            >
              Сохранить
            </button>
          </div>
        </div>
      </div>
    </div>
  </transition>
</template>
<style lang="css" scoped>
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
