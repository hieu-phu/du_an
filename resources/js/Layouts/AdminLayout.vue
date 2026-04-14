<template>
  <ThemeProvider>
    <SidebarProvider>
      <Head :title="title" />
      <AdminLayoutContent>
        <slot />
      </AdminLayoutContent>
    </SidebarProvider>
  </ThemeProvider>
</template>

<script setup>
import { computed, defineComponent, h } from 'vue'
import { Head, usePage } from '@inertiajs/vue3'
import ThemeProvider from '@/components/layout/ThemeProvider.vue'
import SidebarProvider from '@/components/layout/SidebarProvider.vue'
import AppFlashAlerts from '@/components/layout/AppFlashAlerts.vue'
import AppSidebar from '@/components/layout/AppSidebar.vue'
import AppHeader from '@/components/layout/AppHeader.vue'
import Backdrop from '@/components/layout/Backdrop.vue'
import { useSidebar } from '@/composables/useSidebar'
import '../../css/main.css'

const props = defineProps({
  title: {
    type: String,
    default: '',
  },
})

const page = usePage()
const title = computed(() => page.props.title || props.title || '')

const AdminLayoutContent = defineComponent({
  setup(_, { slots }) {
    const { isExpanded, isHovered } = useSidebar()

    return () =>
      h('div', { class: 'min-h-screen bg-[#F6F8FC] dark:bg-gray-950' }, [
        h(AppSidebar),
        h(Backdrop),
        h(
          'div',
          {
            class: [
              'flex min-h-screen flex-col transition-all duration-300 ease-in-out',
              isExpanded.value || isHovered.value ? 'lg:ml-[290px]' : 'lg:ml-[90px]',
            ],
          },
          [
            h(AppHeader),
            h('main', { class: 'flex-1' }, [
              h(
                'div',
                {
                  class:
                    'mx-auto flex w-full max-w-[1600px] flex-1 flex-col px-4 py-4 md:px-6 md:py-6',
                },
                [
                  h(AppFlashAlerts),
                  h(
                    'div',
                    {
                      class:
                        'min-w-0 flex-1 overflow-x-auto rounded-2xl',
                    },
                    slots.default?.(),
                  ),
                ],
              ),
            ]),
          ],
        ),
      ])
  },
})
</script>
