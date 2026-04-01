<template>
  <aside :class="[
    'fixed mt-16 flex flex-col lg:mt-0 top-0 px-5 left-0 bg-white dark:bg-gray-900 dark:border-gray-800 text-gray-900 h-screen transition-all duration-300 ease-in-out z-999 border-r border-gray-200',
    {
      'lg:w-[290px]': isExpanded || isMobileOpen || isHovered,
      'lg:w-[90px]': !isExpanded && !isHovered,
      'translate-x-0 w-[290px]': isMobileOpen,
      '-translate-x-full': !isMobileOpen,
      'lg:translate-x-0': true,
    },
  ]" @mouseenter="!isExpanded && (isHovered = true)" @mouseleave="isHovered = false">
    <div :class="[
      'py-8 flex',
      !isExpanded && !isHovered ? 'lg:justify-center' : 'justify-start',
    ]">
      <Link href="/dashboard" class="d-inline">
        <img v-if="isExpanded || isHovered || isMobileOpen" class="dark:hidden h-9"
          src="/resource/asfy-images/asfy-logo.png" alt="Logo" />
        <img v-if="isExpanded || isHovered || isMobileOpen" class="hidden dark:block h-9"
          src="/resource/asfy-images/asfy-logo.png" alt="Logo" />
        <img v-else src="/resource/asfy-images/asfy-logo.png" alt="Logo" width="32" height="32" />
      </Link>
    </div>
    <div class="flex flex-col overflow-y-auto duration-300 ease-linear no-scrollbar">
      <nav class="mb-6">
        <div class="flex flex-col gap-4">
          <div v-for="(menuGroup, groupIndex) in menuGroups" :key="groupIndex">
            <h2 :class="[
              'mb-4 text-xs uppercase flex leading-[20px] text-gray-400',
              !isExpanded && !isHovered
                ? 'lg:justify-center'
                : 'justify-start',
            ]">
              <template v-if="isExpanded || isHovered || isMobileOpen">
                {{ menuGroup.title }}
              </template>
              <HorizontalDots v-else />
            </h2>
            <ul class="flex flex-col gap-4">
              <template v-for="(item, index) in menuGroup.items" :key="item.name">

                <!-- LABEL tên công ty của trang chủ =)) -->
                <li v-if="item.name === 'Hành chính nhân sự'" class="menu-company-label" :class="{
                  'expanded': isExpanded || isHovered || isMobileOpen,
                  'collapsed': !(isExpanded || isHovered || isMobileOpen)
                }">
                  <div class="company-label-wrapper" v-if="isExpanded || isHovered || isMobileOpen">
                    <div class="company-label-content">
                      <span class="company-label-text">{{ user?.company?.name }}</span>
                    </div>
                  </div>
                  <div v-else class="company-mini-view">
                    <WarehouseIcon class="w-5 h-5 text-gray-400" />
                  </div>
                </li>

                <!-- MENU ITEM -->
                <li>
                  <!-- MENU CHA CÓ SUBMENU -->
                  <button v-if="item.subItems" @click="toggleSubmenu(groupIndex, index)" :class="[
                    'menu-item group w-full',
                    {
                      'menu-item-active': isSubmenuOpen(groupIndex, index),
                      'menu-item-inactive': !isSubmenuOpen(groupIndex, index),
                    },
                    !isExpanded && !isHovered
                      ? 'lg:justify-center'
                      : 'lg:justify-start',
                  ]">
                    <span :class="[
                      isSubmenuOpen(groupIndex, index)
                        ? 'menu-item-icon-active'
                        : 'menu-item-icon-inactive',
                    ]">
                      <component :is="getIconComponent(item.icon)" />
                    </span>

                    <span v-if="isExpanded || isHovered || isMobileOpen" class="menu-item-text">
                      {{ item.name }}
                    </span>

                    <ChevronDownIcon v-if="isExpanded || isHovered || isMobileOpen" :class="[
                      'ml-auto w-5 h-5 transition-transform duration-200',
                      {
                        'rotate-180 text-brand-500': isSubmenuOpen(
                          groupIndex,
                          index
                        ),
                      },
                    ]" />
                  </button>

                  <!-- MENU KHÔNG CÓ SUBMENU -->
                  <Link v-else-if="item.path" :href="item.path" :class="[
                    'menu-item group',
                    {
                      'menu-item-active': isActive(item.path, item.none_active || false),
                      'menu-item-inactive': !isActive(item.path, item.none_active || false),
                    },
                  ]">
                    <span :class="[
                      isActive(item.path, item.none_active || false)
                        ? 'menu-item-icon-active'
                        : 'menu-item-icon-inactive',
                    ]">
                      <component :is="getIconComponent(item.icon)" />
                    </span>

                    <span v-if="isExpanded || isHovered || isMobileOpen" class="menu-item-text">
                      {{ item.name }}
                    </span>
                  </Link>

                  <!-- SUBMENU -->
                  <transition @enter="startTransition" @after-enter="endTransition" @before-leave="startTransition"
                    @after-leave="endTransition">
                    <div v-show="isSubmenuOpen(groupIndex, index) &&
                      (isExpanded || isHovered || isMobileOpen)
                      ">
                      <ul class="mt-2 space-y-1 ml-9">
                        <li v-for="subItem in item.subItems" :key="subItem.name">
                          <Link :href="subItem.path" :class="[
                            'menu-dropdown-item',
                            {
                              'menu-dropdown-item-active': isActive(
                                subItem.path,
                                subItem.none_active || false
                              ),
                              'menu-dropdown-item-inactive': !isActive(
                                subItem.path,
                                subItem.none_active || false
                              ),
                            },
                          ]">
                            {{ subItem.name }}

                            <span class="flex items-center gap-1 ml-auto">
                              <span v-if="subItem.new" :class="[
                                'menu-dropdown-badge',
                                {
                                  'menu-dropdown-badge-active': isActive(
                                    subItem.path,
                                    subItem.none_active || false
                                  ),
                                  'menu-dropdown-badge-inactive': !isActive(
                                    subItem.path,
                                    subItem.none_active || false
                                  ),
                                },
                              ]">
                                new
                              </span>

                              <span v-if="subItem.pro" :class="[
                                'menu-dropdown-badge',
                                {
                                  'menu-dropdown-badge-active': isActive(
                                    subItem.path,
                                    subItem.none_active || false
                                  ),
                                  'menu-dropdown-badge-inactive': !isActive(
                                    subItem.path,
                                    subItem.none_active || false
                                  ),
                                },
                              ]">
                                pro
                              </span>
                            </span>
                          </Link>
                        </li>
                      </ul>
                    </div>
                  </transition>
                </li>

              </template>
            </ul>

          </div>
        </div>
      </nav>
    </div>
  </aside>
</template>

<script setup>
import { ref, computed } from "vue";
import { Link, usePage } from '@inertiajs/vue3'

import {
  ChevronDownIcon,
  HorizontalDots,
  WarehouseIcon,
} from "../../icons";
import { useSidebar } from "@/composables/useSidebar";
const { isExpanded, isMobileOpen, isHovered, openSubmenu } = useSidebar();
const page = usePage();
const user = computed(() => page.props.auth?.user)

const companies = computed(() => page.props.auth.companies || [])
const menuGroups = computed(() => page.props.auth.menuItems || []);
const isActive = (path, noneActive = false) => {
  if (noneActive) {
    return false;
  }
  const currentUrl = page.url.split('?')[0];
  const pathClean = new URL(path, window.location.origin).pathname;
  return currentUrl === pathClean || currentUrl.startsWith(pathClean + '/');
};

const toggleSubmenu = (groupIndex, itemIndex) => {
  const key = `${groupIndex}-${itemIndex}`;
  openSubmenu.value = openSubmenu.value === key ? null : key;
};
const isAnySubmenuRouteActive = computed(() => {
  return menuGroups.value.some((group) =>
    group.items.some(
      (item) =>
        item.subItems && item.subItems.some((subItem) => isActive(subItem.path, subItem.none_active || false))
    )
  );
});
const iconModules = import.meta.glob('../../icons/*.vue', { eager: true });
const getIconComponent = (iconName) => {
  // Tìm module tương ứng với tên icon
  const modulePath = `../../icons/${iconName}.vue`;

  if (iconModules[modulePath]) {
    return iconModules[modulePath].default;
  }

  // Fallback nếu không tìm thấy icon
  console.warn(`Icon ${iconName} not found`);
  return null;
};

const isSubmenuOpen = (groupIndex, itemIndex) => {
  const key = `${groupIndex}-${itemIndex}`;
  return (
    openSubmenu.value === key ||
    (isAnySubmenuRouteActive.value &&
      menuGroups.value[groupIndex].items[itemIndex].subItems?.some((subItem) =>
        isActive(subItem.path)
      ))
  );
};

const startTransition = (el) => {
  el.style.height = "auto";
  const height = el.scrollHeight;
  el.style.height = "0px";
  el.offsetHeight; // force reflow
  el.style.height = height + "px";
};

const endTransition = (el) => {
  el.style.height = "";
};
const activeCompany = computed(() => {
  return companies.value.find(c => c.is_active === 1)
})
</script>
<style>
.menu-company-label {
  position: relative;
  margin-top: 16px;
  margin-bottom: 4px;
}

.menu-company-label.expanded::before {
  content: '';
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  height: 1px;
  background: linear-gradient(90deg,
      transparent 0%,
      rgba(209, 213, 219, 0.6) 20%,
      rgba(209, 213, 219, 0.8) 50%,
      rgba(209, 213, 219, 0.6) 80%,
      transparent 100%);
}

.company-label-wrapper {
  display: flex;
  align-items: center;
  width: 100%;
  padding: 0 8px;
  margin-top: 12px;
}


.company-label-content {
  display: flex;
  align-items: center;
  gap: 8px;
  padding: 0 12px;
}

.company-label-text {
  font-size: 0.75rem;
  font-weight: 600;
  color: #6b7280;
  letter-spacing: 0.05em;
  text-transform: uppercase;
  padding: 4px 8px;
  background-color: rgba(249, 250, 251, 0.7);
  border-radius: 4px;
}

.company-mini-view {
  display: flex;
  justify-content: center;
  padding: 12px 0 8px 0;
  position: relative;
}

.company-mini-view::before {
  content: '';
  position: absolute;
  top: 0;
  left: 8px;
  right: 8px;
  height: 1px;
  background: linear-gradient(90deg,
      transparent 0%,
      rgba(209, 213, 219, 0.6) 20%,
      rgba(209, 213, 219, 0.8) 50%,
      rgba(209, 213, 219, 0.6) 80%,
      transparent 100%);
}
</style>