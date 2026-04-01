<template>
  <div v-show="totalPages > 1" class="pagination-wrapper border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">
    <div class="pagination-content">
      
      <!-- Controls bên trái -->
      <div class="controls-left">
        <!-- <div class="items-per-page">
          <span class="label">Số dòng:</span>
          <div class="select-wrapper">
            <select v-model="localPerPage" @change="onPerPageChange">
              <option v-for="n in [10, 25, 50, 100]" :key="n" :value="n">{{ n }}</option>
            </select>
          </div>
        </div> -->

        <div class="jump-to-page">
          <span class="info-text">Đang hiển thị</span>
          <span class="highlight">{{ meta.from }}–{{ meta.to }}</span>
          <span class="info-text">/ {{ meta.total }} kết quả</span>
        </div>
      </div>

      <!-- Pagination buttons -->
      <div class="pagination-buttons" v-if="totalPages > 1">
        <button class="nav-btn" :disabled="meta.current_page === 1" @click="emit(meta.current_page - 1)">
          <svg viewBox="0 0 24 24" fill="none"><path d="M15 19l-7-7 7-7" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
        </button>

        <div class="page-numbers">
          <!-- Trang đầu -->
          <button v-if="showFirst" class="page-btn" :class="{ active: meta.current_page === 1 }" @click="emit(1)">1</button>
          <span v-if="showStartDots" class="page-dots">•••</span>

          <!-- Trang giữa -->
          <button v-for="p in visiblePages" :key="p" class="page-btn" :class="{ active: meta.current_page === p }" @click="emit(p)">{{ p }}</button>

          <!-- Trang cuối -->
          <span v-if="showEndDots" class="page-dots">•••</span>
          <button v-if="showLast" class="page-btn" :class="{ active: meta.current_page === totalPages }" @click="emit(totalPages)">{{ totalPages }}</button>
        </div>

        <button class="nav-btn" :disabled="meta.current_page === totalPages" @click="emit(meta.current_page + 1)">
          <svg viewBox="0 0 24 24" fill="none"><path d="M9 5l7 7-7 7" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
        </button>
      </div>

      <div class="page-info-mobile">
        Trang <span class="current">{{ meta.current_page }}</span> / <span class="total">{{ totalPages }}</span>
      </div>

    </div>
  </div>
</template>

<script>
export default {
  name: 'Pagination',
  props: {
    // Chỉ cần 1 prop duy nhất — truyền thẳng object orders từ Laravel
    meta: {
      type: Object,
      required: true,
      // Shape: { total, per_page, current_page, last_page, from, to }
    },
    maxVisible: {
      type: Number,
      default: 5
    }
  },
  emits: ['page-change', 'per-page-change'],
  data() {
    return {
      localPerPage: this.meta.per_page
    }
  },
  computed: {
    totalPages() {
      // Dùng last_page của Laravel nếu có, fallback tự tính
      return this.meta.last_page ?? Math.ceil(this.meta.total / this.localPerPage)
    },
    visiblePages() {
      const half = Math.floor(this.maxVisible / 2)
      const cur = this.meta.current_page
      let start = Math.max(2, cur - half)
      let end = Math.min(this.totalPages - 1, cur + half)

      // Giữ đủ maxVisible trang
      if (end - start + 1 < this.maxVisible) {
        if (cur <= half + 1) end = Math.min(this.maxVisible, this.totalPages - 1)
        else start = Math.max(2, this.totalPages - this.maxVisible + 1)
      }

      return Array.from({ length: end - start + 1 }, (_, i) => start + i)
    },
    showFirst() { return !this.visiblePages.includes(1) },
    showLast()  { return !this.visiblePages.includes(this.totalPages) && this.totalPages > 1 },
    showStartDots() { return this.visiblePages[0] > 2 },
    showEndDots()   { return this.visiblePages.at(-1) < this.totalPages - 1 }
  },
  methods: {
    emit(page) {
      if (page < 1 || page > this.totalPages || page === this.meta.current_page) return
      this.$emit('page-change', page)
    },
    onPerPageChange() {
      this.$emit('per-page-change', this.localPerPage)
      this.$emit('page-change', 1)
    }
  },
  watch: {
    'meta.per_page'(val) { this.localPerPage = val }
  }
}
</script>

<style scoped>
.pagination-wrapper {
    border-radius: 0.25rem;
    padding: 12px;
}

.pagination-hungpv {
    border: none !important;
    padding: 0 !important;
    background: transparent !important;
}

@media (max-width: 768px) {
    .pagination-hungpv {
        padding: 0 !important;
    }
}


.pagination-info {
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: 14px;
    color: #666;
    margin-bottom: 20px;
    padding-bottom: 16px;
    border-bottom: 1px solid #f0f0f0;
}

.dark .pagination-info {
    color: #999;
    border-bottom-color: #2d2d2d;
}

.info-text {
    color: #888;
    font-size: 13px;
}

.dark .info-text {
    color: #666;
}

.highlight {
    color: #2563eb;
    font-weight: 600;
    font-size: 13px;
}

.dark .highlight {
    color: #3b82f6;
}

.pagination-content {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 16px;
}

.controls-left {
    display: flex;
    align-items: center;
    gap: 20px;
}

.items-per-page,
.jump-to-page {
    display: flex;
    align-items: center;
    gap: 8px;
}

.label {
    font-size: 13px;
    color: #666;
    white-space: nowrap;
}

.dark .label {
    color: #999;
}

.select-wrapper {
    position: relative;
    min-width: 80px;
}

select {
    width: 100%;
    padding: 8px 30px 8px 12px;
    border: 1px solid #e0e0e0;
    border-radius: 8px;
    background: white;
    color: #333;
    font-size: 13px;
    cursor: pointer;
    outline: none;
    appearance: none;
    transition: all 0.2s;
}

.dark select {
    background: #2a2a2a;
    border-color: #3d3d3d;
    color: #ddd;
}

select:hover {
    border-color: #c0c0c0;
}

.dark select:hover {
    border-color: #555;
}

select:focus {
    border-color: #2563eb;
    box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
}

.dark select:focus {
    border-color: #3b82f6;
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.2);
}

.select-arrow {
    position: absolute;
    right: 10px;
    top: 50%;
    transform: translateY(-50%);
    width: 14px;
    height: 14px;
    color: #666;
    pointer-events: none;
}

.dark .select-arrow {
    color: #999;
}

.input-wrapper {
    position: relative;
    width: 120px;
}

.input-wrapper input {
    width: 100%;
    padding: 8px 40px 8px 12px;
    border: 1px solid #e0e0e0;
    border-radius: 8px;
    background: white;
    color: #333;
    font-size: 13px;
    outline: none;
    transition: all 0.2s;
}

.dark .input-wrapper input {
    background: #2a2a2a;
    border-color: #3d3d3d;
    color: #ddd;
}

.input-wrapper input:hover {
    border-color: #c0c0c0;
}

.dark .input-wrapper input:hover {
    border-color: #555;
}

.input-wrapper input:focus {
    border-color: #2563eb;
    box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
}

.dark .input-wrapper input:focus {
    border-color: #3b82f6;
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.2);
}

.input-wrapper input::placeholder {
    color: #aaa;
}

.dark .input-wrapper input::placeholder {
    color: #666;
}

.jump-btn {
    position: absolute;
    right: 4px;
    top: 50%;
    transform: translateY(-50%);
    width: 28px;
    height: 28px;
    border-radius: 6px;
    background: transparent;
    color: #666;
    border: none;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.2s;
}

.jump-btn:hover {
    background: rgba(37, 99, 235, 0.1);
    color: #2563eb;
}

.dark .jump-btn {
    color: #999;
}

.dark .jump-btn:hover {
    background: rgba(59, 130, 246, 0.2);
    color: #3b82f6;
}

.jump-btn svg {
    width: 16px;
    height: 16px;
}

.pagination-buttons {
    display: flex;
    align-items: center;
    gap: 4px;
}

.nav-btn {
    width: 36px;
    height: 36px;
    border-radius: 8px;
    border: 1px solid #e0e0e0;
    background: white;
    color: #666;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.2s;
}

.dark .nav-btn {
    background: #2a2a2a;
    border-color: #3d3d3d;
    color: #999;
}

.nav-btn:hover:not(:disabled) {
    background: #f5f5f5;
    border-color: #c0c0c0;
    color: #333;
}

.dark .nav-btn:hover:not(:disabled) {
    background: #3a3a3a;
    border-color: #555;
    color: #ddd;
}

.nav-btn:disabled {
    opacity: 0.4;
    cursor: not-allowed;
}

.nav-btn svg {
    width: 18px;
    height: 18px;
}

.page-numbers {
    display: flex;
    align-items: center;
    gap: 4px;
    margin: 0 8px;
}

.page-btn {
    min-width: 36px;
    height: 36px;
    padding: 0 4px;
    border-radius: 8px;
    border: 1px solid transparent;
    background: transparent;
    color: #666;
    font-size: 13px;
    font-weight: 500;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.2s;
}

.dark .page-btn {
    color: #999;
}

.page-btn:hover:not(.active) {
    background: #f5f5f5;
    color: #333;
}

.dark .page-btn:hover:not(.active) {
    background: #3a3a3a;
    color: #ddd;
}

.page-btn.active {
    background: #2563eb;
    color: white;
    font-weight: 600;
    box-shadow: 0 2px 4px rgba(37, 99, 235, 0.2);
}

.dark .page-btn.active {
    background: #3b82f6;
}

.page-dots {
    width: 36px;
    height: 36px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #aaa;
    font-size: 12px;
    letter-spacing: 2px;
}

.dark .page-dots {
    color: #666;
}

.page-info-mobile {
    display: none;
    font-size: 13px;
    color: #666;
    align-items: center;
    gap: 4px;
}

.dark .page-info-mobile {
    color: #999;
}

.page-info-mobile .current {
    color: #2563eb;
    font-weight: 600;
}

.dark .page-info-mobile .current {
    color: #3b82f6;
}

.page-info-mobile .total {
    color: #333;
    font-weight: 500;
}

.dark .page-info-mobile .total {
    color: #ddd;
}

input[type="number"]::-webkit-inner-spin-button,
input[type="number"]::-webkit-outer-spin-button {
    -webkit-appearance: none;
    margin: 0;
}

input[type="number"] {
    -moz-appearance: textfield;
    appearance: textfield;
}
</style>