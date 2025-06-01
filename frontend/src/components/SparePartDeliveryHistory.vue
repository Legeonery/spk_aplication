<script setup>
defineProps({
  deliveries: {
    type: Array,
    default: () => [],
  },
})
</script>

<template>
  <div>
    <div v-if="!deliveries.length" class="text-gray-500 text-center py-4">Нет поступлений.</div>

    <div v-else class="overflow-auto rounded-xl border shadow-sm">
      <table class="w-full text-sm text-left border-separate border-spacing-y-1">
        <thead class="bg-gray-50 text-gray-700 uppercase tracking-wider text-sm">
          <tr>
            <th class="px-4 py-3">Дата</th>
            <th class="px-4 py-3">Название</th>
            <th class="px-4 py-3">Артикул</th>
            <th class="px-4 py-3 text-right">Количество</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="d in deliveries" :key="d.id" class="bg-white hover:bg-blue-50 transition-all">
            <td class="px-4 py-2">{{ d.date }}</td>
            <td class="px-4 py-2">{{ d.spare_part?.name || '—' }}</td>
            <td class="px-4 py-2">{{ d.spare_part?.article || '—' }}</td>
            <td class="px-4 py-2 text-right">{{ d.quantity }}</td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</template>
