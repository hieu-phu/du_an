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
      <nav v-if="showLabel" class="space-y-1">
        <section
          v-for="(group, groupIndex) in menuGroups"
          :key="`${group.title}-${groupIndex}`"
          class="overflow-hidden border-b border-gray-100 last:border-0 dark:border-gray-800"
        >
          <button
            type="button"
            @click="toggleGroup(group.title)"
            class="flex w-full items-center gap-3 px-4 py-3.5 text-left transition hover:bg-gray-50/80 dark:hover:bg-gray-800/40"
          >
            <span class="text-[11px] font-bold uppercase tracking-[0.2em] text-gray-500 dark:text-gray-400">
              {{ group.title }}
            </span>
            <span class="ml-auto"></span>
            <ChevronDownIcon
              class="h-3.5 w-3.5 text-gray-400 transition-transform duration-300"
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
                  <span
                    v-if="Number(item.notification_count || 0) > 0"
                    class="ml-auto flex h-5 min-w-[20px] items-center justify-center rounded-full bg-rose-100 px-1.5 text-[10px] font-bold text-rose-700 dark:bg-rose-900/40 dark:text-rose-300"
                  >
                    {{ formatBadgeCount(item.notification_count) }}
                  </span>
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
import { computed, onMounted, onUnmounted, ref, watch } from 'vue'
import { Link, usePage } from '@inertiajs/vue3'
import { ChevronDownIcon } from '@/icons'
import { useSidebar } from '@/composables/useSidebar'

const { isExpanded, isMobileOpen, isHovered, setIsHovered } = useSidebar()
const page = usePage()

const notificationCounts = ref(page.props.auth?.notification_counts || {})
const menuGroups = computed(() => (page.props.auth?.menuItems || []).map((group) => ({
  ...group,
  items: (group.items || []).map((item) => ({
    ...item,
    notification_count: notificationCountForItem(item),
  })),
})))
const flatMenuItems = computed(() => menuGroups.value.flatMap((group) => group.items || []))
const showLabel = computed(() => isExpanded.value || isHovered.value || isMobileOpen.value)
const openGroupTitle = ref(null)

const iconModules = import.meta.glob('../../icons/*.vue', { eager: true })

const getIconComponent = (iconName) => {
  const modulePath = `../../icons/${iconName}.vue`
  return iconModules[modulePath]?.default || iconModules['../../icons/GridIcon.vue']?.default || null
}

const notificationCountForItem = (item) => {
  const paths = item.notification_paths || []

  if (paths.length) {
    return paths.reduce((total, path) => total + Number(notificationCounts.value?.[`path:${path}`] || 0), 0)
  }

  const categories = item.notification_categories || []
  return categories.reduce((total, category) => total + Number(notificationCounts.value?.[category] || 0), 0)
}

const formatBadgeCount = (value) => {
  const count = Number(value || 0)
  return count > 99 ? '99+' : String(count)
}

const handleNotificationCountsUpdated = (event) => {
  notificationCounts.value = event.detail || {}
}

const handleNotificationReceived = (event) => {
  const category = event.detail?.category || 'general'
  const path = resolveNotificationPath(event.detail)
  notificationCounts.value = {
    ...notificationCounts.value,
    all: Number(notificationCounts.value?.all || 0) + 1,
    [category]: Number(notificationCounts.value?.[category] || 0) + 1,
    ...(path
      ? {
          [`path:${path}`]: Number(notificationCounts.value?.[`path:${path}`] || 0) + 1,
        }
      : {}),
  }
}

const resolveNotificationPath = (notification) => {
  const rawPath = notification?.url_link || notification?.data?.action_url

  if (!rawPath) {
    return null
  }

  try {
    return new URL(rawPath, window.location.origin).pathname
  } catch {
    return null
  }
}

onMounted(() => {
  window.addEventListener('notification-counts-updated', handleNotificationCountsUpdated)
  window.addEventListener('notification-received', handleNotificationReceived)
})

onUnmounted(() => {
  window.removeEventListener('notification-counts-updated', handleNotificationCountsUpdated)
  window.removeEventListener('notification-received', handleNotificationReceived)
})

watch(
  () => page.props.auth?.notification_counts,
  (counts) => {
    notificationCounts.value = counts || {}
  },
)

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
