<template>
  <header class="sticky top-0 z-40 border-b border-gray-200 bg-white/90 backdrop-blur dark:border-gray-800 dark:bg-gray-900/90">
    <div class="mx-auto flex w-full max-w-[1600px] items-center justify-between gap-3 px-4 py-3 md:px-6">
      <div class="flex min-w-0 items-center gap-3">
        <button
          @click="handleToggle"
          class="inline-flex h-11 w-11 items-center justify-center rounded-xl border border-gray-200 text-gray-600 transition hover:bg-gray-100 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-gray-800"
          type="button"
          aria-label="Toggle sidebar"
        >
          <svg v-if="isMobileOpen" class="h-5 w-5" viewBox="0 0 24 24" fill="none">
            <path d="M6 6L18 18M18 6L6 18" stroke="currentColor" stroke-width="2" stroke-linecap="round" />
          </svg>
          <svg v-else class="h-5 w-5" viewBox="0 0 24 24" fill="none">
            <path d="M4 7H20M4 12H14M4 17H20" stroke="currentColor" stroke-width="2" stroke-linecap="round" />
          </svg>
        </button>

        <HeaderLogo />

        <div class="hidden min-w-0 md:block">
          <div class="text-sm font-semibold text-gray-900 dark:text-white">
            {{ currentTitle }}
          </div>
          <div class="truncate text-xs text-gray-500 dark:text-gray-400">
            Khung giao dien dung chung cho toan bo hệ thống
          </div>
        </div>
      </div>

      <div class="flex items-center gap-2 sm:gap-3">
        <ThemeToggler />
        <NotificationMenu />
        <UserMenu />
      </div>
    </div>
  </header>
</template>

<script setup>
import { computed } from 'vue'
import { usePage } from '@inertiajs/vue3'
import { useSidebar } from '@/composables/useSidebar'
import ThemeToggler from '../common/ThemeToggler.vue'
import HeaderLogo from './header/HeaderLogo.vue'
import NotificationMenu from './header/NotificationMenu.vue'
import UserMenu from './header/UserMenu.vue'

const page = usePage()
const { toggleSidebar, toggleMobileSidebar, isMobileOpen } = useSidebar()

const currentTitle = computed(() => page.props.title || page.component?.split('/').at(-1) || 'Hệ thống')

const handleToggle = () => {
  if (window.innerWidth >= 1024) {
    toggleSidebar()
    return
  }

  toggleMobileSidebar()
}
</script>

