<template>
  <div class="paginater-table-wrapper" :class="{ 'reset': header }">
    <div v-if="header" class="p-4 px-5 border-b border-gray-100 dark:border-gray-700">
      <div class="flex items-center justify-between">
        <div>
          <h2 class="text-base md:text-lg font-medium text-gray-900 dark:text-white">{{ header.title }}</h2>
          <p class="text-text-sub text-sm text-gray-500 dark:text-gray-400">
            <span>{{ header.subtitle ?? '' }}</span>
          </p>
        </div>
        <div v-if="header.link" class="relative z-10">
          <Link :href="header.link"
            class="text-sm text-blue-600 dark:text-blue-400 hover:text-blue-700 dark:hover:text-blue-300 font-medium flex items-center gap-1 cursor-pointer transition-colors">
          Xem tất cả
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
          </svg>
          </Link>
        </div>
      </div>
    </div>

    <DataTable :columns="columns" :data="data" :loading="loading" :showIndex="showIndex" :indexOffset="indexOffset"
      v-bind="$attrs">
      <template v-for="slot in Object.keys($slots)" :key="slot" #[slot]="scope">
        <slot :name="slot" v-bind="scope || {}" />
      </template>

      <template v-if="showPagination" #paginator>
        <Pagination theme="hungpv" :totalItems="computedTotalItems" :itemsPerPage="perPage" :currentPage="currentPage"
          :maxVisiblePages="maxVisiblePages" :doingShow="computedDoingShow" @page-change="handlePageChange"
          @items-per-page-change="handlePerPageChange" />
      </template>
    </DataTable>
  </div>
</template>

<script>
import DataTable from '@/components/tables/DataTable.vue'
import Pagination from '@/components/Pagination.vue'
import { Link } from '@inertiajs/vue3'

export default {
  name: 'PaginaterTable',
  components: {
    DataTable,
    Pagination,
    Link
  },
  props: {
    header: {
      type: Object,
      default: () => { }
    },
    columns: {
      type: Array,
      default: () => []
    },
    data: {
      type: Array,
      default: () => []
    },
    loading: {
      type: Boolean,
      default: false
    },
    showIndex: {
      type: Boolean,
      default: false
    },
    indexOffset: {
      type: Number,
      default: 0
    },
    // Pagination props
    currentPage: {
      type: Number,
      default: 1
    },
    totalItems: {
      type: Number,
      default: 0
    },
    perPage: {
      type: Number,
      default: 10
    },
    maxVisiblePages: {
      type: Number,
      default: 5
    },
    doingShow: {
      type: Number,
      default: 0
    },
    showPagination: {
      type: Boolean,
      default: true
    },
    res: {
      type: Object,
      default: {}
    }
  },
  computed: {
    computedDoingShow() {
      const { from, to } = this.res || {}
      return from && to ? `${from} - ${to}` : ''
    },
    computedTotalItems() {
      return this.res?.total || this.totalItems
    }
  },
  emits: ['page-change', 'items-per-page-change'],
  methods: {
    handlePageChange(page) {
      this.$emit('page-change', page)
    },
    handlePerPageChange(perPage) {
      this.$emit('items-per-page-change', perPage)
    }
  }
}
</script>

<style scoped>
.reset {
  background-color: white;
  border-radius: 0.75rem;
  border: 1px solid rgb(243 244 246);
  overflow: hidden;
  box-shadow: 0 5px 10px rgba(151, 164, 175, 0.05);
}

.dark .reset {
  background-color: rgb(31 41 55);
  border-color: rgb(55 65 81);
  box-shadow: 0 5px 10px rgba(0, 0, 0, 0.3);
}

.reset .config-table {
  border-radius: 0;
  border: none;
  box-shadow: none;
}
</style>