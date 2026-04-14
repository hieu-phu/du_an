<template>
  <aside
    :class="[
      'fixed left-0 top-0 z-50 flex h-screen flex-col border-r border-gray-200 bg-white text-gray-900 transition-all duration-300 ease-in-out dark:border-gray-800 dark:bg-gray-900',
      {
        'w-[290px] translate-x-0': isMobileOpen,
        'w-[290px] lg:w-[290px]': isExpanded || isHovered,
        'lg:w-[92px]': !isExpanded && !isHovered,
        '-translate-x-full lg:translate-x-0': !isMobileOpen,
      },
    ]"
    @mouseenter="setIsHovered(true)"
    @mouseleave="setIsHovered(false)"
  >
    <div class="flex h-[73px] items-center border-b border-gray-200 px-4 dark:border-gray-800">
      <Link href="/dashboard" class="flex items-center gap-3">
        <img src="/resource/asfy-images/asfy-logo.png" alt="Logo" class="h-9 w-9 flex-shrink-0 rounded-lg object-contain" />
        <div v-if="showLabel" class="min-w-0">
          <div class="truncate text-sm font-semibold text-gray-900 dark:text-white">HRM System</div>
          <div class="truncate text-xs text-gray-500 dark:text-gray-400">System Navigation</div>
        </div>
      </Link>
    </div>

    <div class="flex-1 overflow-y-auto px-3 py-3">
      <nav v-if="showLabel" class="space-y-2">
        <section
          v-for="(group, groupIndex) in menuGroups"
          :key="`${group.title}-${groupIndex}`"
          class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm dark:border-gray-700 dark:bg-gray-900"
        >
          <button
            type="button"
            @click="toggleGroup(group.title)"
            class="flex w-full items-center gap-3 px-3 py-3 text-left transition hover:bg-gray-50 dark:hover:bg-gray-800/70"
          >
            <span class="text-[12px] font-bold uppercase tracking-[0.16em] text-gray-700 dark:text-gray-200">
              {{ group.title }}
            </span>
            <span class="ml-auto rounded-full border border-gray-200 bg-gray-50 px-2 py-0.5 text-xs font-bold text-gray-700 shadow-sm dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100">
              {{ group.items.length }}
            </span>
            <ChevronDownIcon
              class="h-4 w-4 text-gray-700 transition-transform dark:text-gray-200"
              :class="{ 'rotate-180': isGroupOpen(group) }"
            />
          </button>

          <transition
            enter-active-class="transition-all duration-200 ease-out"
            enter-from-class="opacity-0 -translate-y-1"
            enter-to-class="opacity-100 translate-y-0"
            leave-active-class="transition-all duration-150 ease-in"
            leave-from-class="opacity-100 translate-y-0"
            leave-to-class="opacity-0 -translate-y-1"
          >
            <ul v-show="isGroupOpen(group)" class="space-y-1 px-2 pb-2">
              <li v-for="item in group.items" :key="item.path">
                <Link
                  :href="item.path"
                  :class="[
                    'group flex items-center gap-3 rounded-xl px-3 py-2 text-sm font-medium transition-colors',
                    isItemActive(item)
                      ? 'bg-blue-50 text-blue-700 shadow-sm dark:bg-blue-900/30 dark:text-blue-300'
                      : 'text-gray-600 hover:bg-white hover:text-gray-900 dark:text-gray-300 dark:hover:bg-gray-800 dark:hover:text-white',
                  ]"
                >
                  <span
                    :class="[
                      'flex h-8 w-8 flex-shrink-0 items-center justify-center rounded-xl transition-colors',
                      isItemActive(item)
                        ? 'bg-blue-100 text-blue-700 dark:bg-blue-900/40 dark:text-blue-300'
                        : 'bg-gray-100 text-gray-500 group-hover:bg-gray-200 group-hover:text-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:group-hover:bg-gray-700 dark:group-hover:text-white',
                    ]"
                  >
                    <component :is="getIconComponent(item.icon)" class="h-4.5 w-4.5" />
                  </span>

                  <span class="truncate">{{ item.name }}</span>
                </Link>
              </li>
            </ul>
          </transition>
        </section>
      </nav>

      <nav v-else class="space-y-2">
        <Link
          v-for="item in flatMenuItems"
          :key="item.path"
          :href="item.path"
          :class="[
            'group flex items-center justify-center rounded-2xl px-2 py-2.5 transition-colors',
            isItemActive(item)
              ? 'bg-blue-50 text-blue-700 dark:bg-blue-900/30 dark:text-blue-300'
              : 'text-gray-500 hover:bg-gray-100 hover:text-gray-900 dark:text-gray-400 dark:hover:bg-gray-800 dark:hover:text-white',
          ]"
        >
          <span
            :class="[
              'flex h-10 w-10 items-center justify-center rounded-xl transition-colors',
              isItemActive(item)
                ? 'bg-blue-100 text-blue-700 dark:bg-blue-900/40 dark:text-blue-300'
                : 'bg-gray-100 text-gray-500 group-hover:bg-gray-200 group-hover:text-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:group-hover:bg-gray-700 dark:group-hover:text-white',
            ]"
          >
            <component :is="getIconComponent(item.icon)" class="h-5 w-5" />
          </span>
        </Link>
      </nav>
    </div>
  </aside>
</template>

<script setup>
import { computed, ref, watch } from 'vue'
import { Link, usePage } from '@inertiajs/vue3'
import { ChevronDownIcon } from '@/icons'
import { useSidebar } from '@/composables/useSidebar'

const { isExpanded, isMobileOpen, isHovered, setIsHovered } = useSidebar()
const page = usePage()

const menuGroups = computed(() => page.props.auth?.menuItems || [])
const flatMenuItems = computed(() => menuGroups.value.flatMap((group) => group.items || []))
const showLabel = computed(() => isExpanded.value || isHovered.value || isMobileOpen.value)
const openGroupTitle = ref(null)

const iconModules = import.meta.glob('../../icons/*.vue', { eager: true })

const getIconComponent = (iconName) => {
  const modulePath = `../../icons/${iconName}.vue`
  return iconModules[modulePath]?.default || iconModules['../../icons/GridIcon.vue']?.default || null
}

const isItemActive = (item) => {
  const currentUrl = page.url.split('?')[0]
  const itemPath = new URL(item.path, window.location.origin).pathname

  if (item.exact) {
    return currentUrl === itemPath
  }

  return currentUrl === itemPath || currentUrl.startsWith(`${itemPath}/`)
}

const activeGroupTitle = computed(() => {
  const group = menuGroups.value.find((menuGroup) => menuGroup.items?.some((item) => isItemActive(item)))
  return group?.title || menuGroups.value[0]?.title || null
})

const isGroupOpen = (group) => openGroupTitle.value === group.title

const toggleGroup = (groupTitle) => {
  openGroupTitle.value = openGroupTitle.value === groupTitle ? null : groupTitle
}

watch(
  [menuGroups, activeGroupTitle],
  ([groups, activeTitle]) => {
    if (!groups.length) {
      openGroupTitle.value = null
      return
    }

    if (!openGroupTitle.value || !groups.some((group) => group.title === openGroupTitle.value) || activeTitle) {
      openGroupTitle.value = activeTitle
    }
  },
  { immediate: true },
)
</script>
