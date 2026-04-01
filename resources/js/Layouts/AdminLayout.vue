<template>
  <ThemeProvider>
    <SidebarProvider>
      <AdminLayoutContent>

        <Head :title="title"> </Head>
        <slot></slot>
      </AdminLayoutContent>
    </SidebarProvider>
  </ThemeProvider>
</template>

<script setup>
import { defineComponent, h } from 'vue'
import { defineProps, computed } from 'vue'

import ThemeProvider from '@/components/layout/ThemeProvider.vue'
import SidebarProvider from '@/components/layout/SidebarProvider.vue'
import AppSidebar from '@/components/layout/AppSidebar.vue'
import AppHeader from '@/components/layout/AppHeader.vue'
import Backdrop from '@/components/layout/Backdrop.vue'
import { useSidebar } from '@/composables/useSidebar'
import '../../css/main.css'
import { Head, usePage } from '@inertiajs/vue3'

const props = defineProps({
  title: {
    type: String,
    default: ''
  }
});
const page = usePage()
const title = computed(() => page.props.title || props.title || "");


const AdminLayoutContent = defineComponent({
  setup(props, { slots }) {
    const { isExpanded, isHovered } = useSidebar()

    return () => h('div', { class: 'min-h-screen dark:bg-gray-900' }, [
      h(AppSidebar),
      h(Backdrop),
      h('div', {
        class: [
          'flex flex-col min-h-screen transition-all duration-300 ease-in-out',
          isExpanded.value || isHovered.value ? 'lg:ml-[290px]' : 'lg:ml-[90px]'
        ]
      }, [
        h(AppHeader),
        h('div', {
          class: 'flex-1 p-4 mx-auto w-full max-w-(--breakpoint-3xl) md:p-6 overflow-x-auto bg-[#FAFBFD] dark:bg-gray-900'
        }, slots.default?.())
      ])
    ])
  }
})
</script>
