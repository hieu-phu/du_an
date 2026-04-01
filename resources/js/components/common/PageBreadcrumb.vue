<template>
  <div class="flex flex-wrap items-center justify-between gap-3 mb-3">
    <h2 class="text-xl uppercase font-medium text-gray-800 dark:text-white/90">
      {{ title }}
    </h2>
    <nav>
      <ol class="flex items-center gap-1.5">
        <li>
          <Link
            class="inline-flex items-center gap-1.5 text-sm text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300"
            href="/">
            Trang chủ
            <svg class="stroke-current" width="17" height="16" viewBox="0 0 17 16" fill="none"
              xmlns="http://www.w3.org/2000/svg">
              <path d="M6.0765 12.667L10.2432 8.50033L6.0765 4.33366" stroke="" stroke-width="1.2"
                stroke-linecap="round" stroke-linejoin="round" />
            </svg>
          </Link>
        </li>
        <li v-for="(item, index) in items" :key="index" class="flex items-center gap-1.5">
          <span v-if="index < items.length - 1" class="text-sm text-gray-500 dark:text-gray-400">
            <Link v-show="item.link"
              class="inline-flex items-center gap-1.5 text-sm text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300"
              :href="item.link"> {{ item.text }}</Link>
            <svg class="stroke-current inline ml-1.5" width="17" height="16" viewBox="0 0 17 16" fill="none"
              xmlns="http://www.w3.org/2000/svg">
              <path d="M6.0765 12.667L10.2432 8.50033L6.0765 4.33366" stroke="" stroke-width="1.2"
                stroke-linecap="round" stroke-linejoin="round" />
            </svg>
          </span>
          <span v-else class="text-sm text-gray-800 dark:text-white/90">
            {{ item.text }}
          </span>
        </li>
      </ol>
    </nav>
  </div>
</template>

<script setup lang="ts">
import { Link } from '@inertiajs/vue3'

interface BreadcrumbProps {
  title: string
  items?: string[]
}

defineProps<BreadcrumbProps>()
</script>
