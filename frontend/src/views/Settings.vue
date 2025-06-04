<script setup>
import { ref } from 'vue'
import GrainTypesTable from '@/components/settings/GrainTypesTable.vue'
import LicenseCategoriesTable from '@/components/settings/LicenseCategoriesTable.vue'
import RolesTable from '@/components/settings/RolesTable.vue'
import VehicleKindsTable from '@/components/settings/VehicleKindsTable.vue'

const tabs = [
  { key: 'grain_types', label: 'Виды зерна' },
  { key: 'license_categories', label: 'Категории прав' },
  { key: 'roles', label: 'Роли' },
  { key: 'vehicle_kinds', label: 'Типы ТС' },
]

const currentTab = ref('grain_types')
</script>

<template>
  <div class="p-6">
    <h1 class="text-2xl font-bold mb-6">Настройки</h1>

    <div class="flex gap-4 mb-6 border-b">
      <button
        v-for="tab in tabs"
        :key="tab.key"
        @click="currentTab = tab.key"
        :class="[
          'pb-2 px-4 text-sm font-medium',
          currentTab === tab.key
            ? 'border-b-2 border-black text-black'
            : 'text-gray-500 hover:text-black'
        ]"
      >
        {{ tab.label }}
      </button>
    </div>

    <div>
      <GrainTypesTable v-if="currentTab === 'grain_types'" />
      <LicenseCategoriesTable v-else-if="currentTab === 'license_categories'" />
      <RolesTable v-else-if="currentTab === 'roles'" />
      <VehicleKindsTable v-else-if="currentTab === 'vehicle_kinds'" />
      <div v-else>Контент для {{ currentTab }}</div>
    </div>
  </div>
</template>
