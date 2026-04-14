<template>
  <div class="mb-5 rounded-2xl border border-gray-200 bg-white px-4 py-4 shadow-sm dark:border-gray-800 dark:bg-gray-900 md:px-5">
    <div class="flex flex-col gap-2 md:flex-row md:items-center md:justify-between">
      <div>
        <h1 class="text-xl font-semibold text-gray-900 dark:text-white md:text-2xl">
          {{ title }}
        </h1>
      </div>

      <nav aria-label="Breadcrumb">
        <ol class="flex flex-wrap items-center gap-2 text-sm text-gray-500 dark:text-gray-400">
          <li>
            <Link class="transition hover:text-gray-900 dark:hover:text-white" href="/dashboard">Dashboard</Link>
          </li>
          <li v-for="(item, index) in normalizedItems" :key="`${item.text}-${index}`" class="flex items-center gap-2">
            <span>/</span>
            <Link
              v-if="item.link && index < normalizedItems.length - 1"
              :href="item.link"
              class="transition hover:text-gray-900 dark:hover:text-white"
            >
              {{ item.text }}
            </Link>
            <span v-else class="font-medium text-gray-700 dark:text-gray-200">
              {{ item.text }}
            </span>
          </li>
        </ol>
      </nav>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue'
import { Link } from '@inertiajs/vue3'

const props = defineProps({
  title: {
    type: String,
    required: true,
  },
  items: {
    type: Array,
    default: () => [],
  },
})

const normalizedItems = computed(() =>
  props.items.map((item) => {
    if (typeof item === 'string') {
      return { text: item, link: null }
    }

    return {
      text: item.text ?? item.title ?? '',
      link: item.link ?? null,
    }
  }),
)
</script>
