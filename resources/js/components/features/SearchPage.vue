<template>
    <div class="rounded-xl p-1 asfy_searchpage">
        <form @submit.prevent="submit" class="flex flex-wrap items-end gap-4 md:gap-6">

            <!-- LOOP FILTER ITEMS -->
            <div v-for="item in filters" :key="item.name" class="flex flex-col w-full md:w-auto md:min-w-[220px]">
                <!-- TEXT INPUT -->
                <input v-if="item.type === 'text'" v-model="values[item.name]" type="text"
                    :placeholder="item.placeholder || 'Nhập...'" class="h-11 px-4 rounded-sm border border-gray-300 dark:border-gray-600 
                 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100
                 focus:outline-none focus:ring-2 focus:ring-blue-500
                 transition-all duration-200 placeholder:text-gray-500 w-full" />

                <!-- SELECT với Multiselect -->
                <Multiselect v-if="item.type === 'select'" v-model="values[item.name]"
                    :options="formatOptions(item.options)" :searchable="true" :closeOnSelect="true"
                    :placeholder="item.placeholder || 'Chọn...'" :canClear="true" :canDeselect="true" mode="single"
                    valueProp="value" label="label" trackBy="label"
                    class="multiselect-custom w-full multiselect-custom dark:text-white placeholder:text-gray-400 dark:placeholder:text-gray-500" />

                <!-- DATE SINGLE -->
                <InputDate v-if="item.type === 'date'" class="custom_input" v-model="values[item.name]"
                    :placeholder="item.placeholder" />

                <!-- DATE RANGE -->
                <DateRangePicker v-if="item.type === 'date_range'" v-model="values[item.name]"
                    :placeholder="item.placeholder || 'Chọn khoảng thời gian'" :clearable="true"
                    :use-preset-ranges="true" :default-range="item.defaultRange || 'thisMonth'"
                    :auto-set-default-range="false" />

            </div>

            <!-- BUTTONS -->
            <div class="flex items-center gap-3 w-full md:w-auto md:ml-auto">
                <!-- Filter Button -->
                <Tooltip text="Lọc kết quả">
                    <button type="submit"
                        class="bg-brand-500 text-white rounded-sm hover:bg-brand-600 disabled:opacity-70 flex items-center justify-center gap-2 px-6 h-10">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 -960 960 960" class="w-5 h-5"
                            fill="currentColor">
                            <path
                                d="M440-160q-17 0-28.5-11.5T400-200v-240L168-736q-15-20-4.5-42t36.5-22h560q26 0 36.5 22t-4.5 42L560-440v240q0 17-11.5 28.5T520-160h-80Zm40-308 198-252H282l198 252Zm0 0Z" />
                        </svg>
                    </button>
                </Tooltip>

                <!-- Reset Button -->
                <Tooltip text="Xóa bộ lọc">
                    <button @click="reset" type="button" class="flex items-center justify-center px-6 h-10
                   rounded-sm border border-gray-300 dark:border-gray-600
                   bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-200
                   hover:bg-gray-100 dark:hover:bg-gray-700 active:scale-95
                   transition-all duration-200 shadow-sm w-full md:w-auto">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2"
                            stroke="currentColor" class="w-5 h-5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </Tooltip>
            </div>
        </form>
    </div>
</template>

<script setup>
import { reactive, onMounted, onBeforeUnmount, watch } from "vue";
import Multiselect from '@vueform/multiselect'
import '@vueform/multiselect/themes/default.css'
import Tooltip from "@/components/ui/Tooltip.vue";
import InputDate from '@/components/forms/InputDate.vue'
import DateRangePicker from '@/components/forms/Datepicker.vue'

const props = defineProps({
    filters: {
        type: Array,
        default: () => []
    },
    defaultParams: {
        type: Object,
        default: () => ({})
    }
});

const emit = defineEmits(["filter"]);

const values = reactive({});
let isInitializing = true;
let debounceTimer = null;
let skipFirstAutoEmit = true;

// Format options cho Multiselect
const formatOptions = (options) => {
    if (!options || !Array.isArray(options)) return [];

    if (options.length > 0 && typeof options[0] === 'object' && 'value' in options[0]) {
        return options;
    }

    if (options.length > 0 && typeof options[0] === 'object') {
        return options.map(opt => ({
            value: opt.id || opt.value,
            label: opt.name || opt.label || opt.title
        }));
    }

    return options.map(opt => ({
        value: opt,
        label: opt
    }));
};

// Helper function để parse date range từ URL
const parseDateRangeFromUrl = (dateRangeString) => {
    if (!dateRangeString || typeof dateRangeString !== 'string') return null;
    const dates = dateRangeString.split(',');
    if (dates.length !== 2) return null;
    return dates;
};

// Handle date range change
const handleDateRangeChange = (fieldName, value) => {
    values[fieldName] = value;
};

const updateUrlParams = (params) => {
    const url = new URL(window.location.href);
    const searchParams = new URLSearchParams(url.search);

    const keysToDelete = [];
    for (const key of searchParams.keys()) {
        if (!(key in props.defaultParams)) {
            keysToDelete.push(key);
        }
    }
    keysToDelete.forEach(key => searchParams.delete(key));

    Object.keys(params).forEach(key => {
        const value = params[key];

        if (value !== '' && value !== null && value !== undefined) {
            searchParams.set(key, value);
        } else if (!(key in props.defaultParams)) {
            searchParams.delete(key);
        }
    });

    const newUrl = `${url.pathname}?${searchParams.toString()}`;
    window.history.pushState({}, '', newUrl);
};

const readUrlParams = () => {
    const params = new URLSearchParams(window.location.search);
    const result = {};

    for (const [key, value] of params.entries()) {
        result[key] = value;
    }

    // Nếu có from_date và to_date trong URL, tạo date_range
    if (result.from_date && result.to_date) {
        result.date_range = [result.from_date, result.to_date];
    }

    return result;
};


const defaultDateRange = () => {
    const now = new Date()
    const start = new Date(now.getFullYear(), now.getMonth(), 1)
    const end = new Date(now.getFullYear(), now.getMonth() + 1, 0)

    // Format theo yyyy-MM-dd
    const formatDate = (date) => {
        const year = date.getFullYear();
        const month = String(date.getMonth() + 1).padStart(2, '0');
        const day = String(date.getDate()).padStart(2, '0');
        return `${year}-${month}-${day}`;
    };

    return [formatDate(start), formatDate(end)];
}

const initializeValues = () => {
    const urlParams = readUrlParams();
    const today = new Date();

    props.filters.forEach((item) => {
        if (item.type === "date_range") {
            const shouldInitialize = item.useDefaultValue !== false;

            if (urlParams.from_date && urlParams.to_date) {
                // Kiểm tra xem ngày có vượt quá hiện tại không
                const toDate = new Date(urlParams.to_date);
                if (toDate > today) {
                    values[item.name] = null; // Reset nếu ngày tương lai
                } else {
                    values[item.name] = [urlParams.from_date, urlParams.to_date];
                }
            } else if (urlParams[item.name]) {
                values[item.name] = parseDateRangeFromUrl(urlParams[item.name]);
            } else if (props.defaultParams[item.name]) {
                values[item.name] = props.defaultParams[item.name];
            } else if (item.value) {
                values[item.name] = item.value;
            } else if (shouldInitialize) {
                values[item.name] = null;
            } else {
                values[item.name] = null;
            }
        } else if (item.type === "date") {
            // Kiểm tra date đơn
            const dateValue = urlParams[item.name] ?? props.defaultParams[item.name] ?? item.value ?? '';
            if (dateValue) {
                const date = new Date(dateValue);
                if (date > today) {
                    values[item.name] = ''; // Reset nếu ngày tương lai
                } else {
                    values[item.name] = dateValue;
                }
            } else {
                values[item.name] = '';
            }
        } else {
            values[item.name] = urlParams[item.name] ?? props.defaultParams[item.name] ?? item.value ?? '';
        }
    });

    Object.keys(props.defaultParams).forEach(key => {
        if (!(key in values)) {
            values[key] = props.defaultParams[key];
        }
    });
};


onMounted(() => {
    initializeValues();
    isInitializing = false;
});

const submit = () => {
    const filterValues = { ...props.defaultParams, ...values };

    const cleanedValues = Object.entries(filterValues).reduce((acc, [key, value]) => {
        // Xử lý date range - tách thành from_date và to_date
        if (Array.isArray(value) && value.length === 2) {
            // Nếu field name là date_range, tách thành from_date và to_date
            if (key === 'date_range') {
                acc['from_date'] = value[0];
                acc['to_date'] = value[1];
            } else {
                // Nếu có custom name khác, giữ nguyên array
                acc[key] = value;
            }
        } else if (value !== '' && value !== null && value !== undefined) {
            acc[key] = value;
        }
        return acc;
    }, {});

    updateUrlParams(cleanedValues);
    emit("filter", cleanedValues);
};

const emitDebouncedFilter = () => {
    const filterValues = { ...props.defaultParams, ...values };

    const cleanedValues = Object.entries(filterValues).reduce((acc, [key, value]) => {
        if (Array.isArray(value) && value.length === 2) {
            if (key === 'date_range') {
                acc['from_date'] = value[0];
                acc['to_date'] = value[1];
            } else {
                acc[key] = value;
            }
        } else if (value !== '' && value !== null && value !== undefined) {
            acc[key] = value;
        }
        return acc;
    }, {});

    updateUrlParams(cleanedValues);
    emit("filter", cleanedValues);
};

// watch(
//     values,
//     () => {
//         if (values.date || values.date_range) return;
//         if (isInitializing) return;
//         if (skipFirstAutoEmit) {
//             skipFirstAutoEmit = false;
//             return;
//         }
//         if (debounceTimer) clearTimeout(debounceTimer);
//         debounceTimer = setTimeout(() => {
//             emitDebouncedFilter();
//         }, 1200);
//     },
//     { deep: true }
// );

onBeforeUnmount(() => {
    if (debounceTimer) clearTimeout(debounceTimer);
});


const reset = () => {
    for (let key in values) {
        if (key in props.defaultParams) {
            values[key] = props.defaultParams[key];
        } else {
            // Check nếu là date_range thì set về default
            const filter = props.filters.find(f => f.name === key);
            if (filter && filter.type === 'date_range') {
                values[key] = defaultDateRange();
            } else {
                values[key] = "";
            }
        }
    }

    updateUrlParams(props.defaultParams);
    emit("filter", { ...props.defaultParams });
};

onMounted(() => {
    let listOptions = document.querySelectorAll('.multiselect-custom .multiselect-options');
    if (listOptions.length > 0) {
        listOptions.forEach(option => {
            option.classList.add('dark:bg-gray-950');
        });
    }
})
</script>

<style>
/* Đảm bảo style thích ứng với dark mode */
.multiselect-custom {
    --ms-ring-width: 1.5px;
    --ms-ring-color: #3b82f6;
    --ms-option-bg-pointed: #eff6ff;
    --ms-option-bg-selected: #3b82f6;
    --ms-option-bg-selected-pointed: #2563eb;
    --ms-option-color-pointed: #1e40af;
    --ms-radius: 0.25rem;
    --ms-bg: #ffffff;
    --ms-border-color: #d1d5db;
    --ms-text-color: #111827;
    --ms-placeholder-color: #6b7280;
}

/* Dark mode - theo theme của ứng dụng */
.dark .multiselect-custom {
    --ms-bg: #1f2937;
    --ms-border-color: #374151;
    --ms-text-color: #e5e7eb;
    --ms-placeholder-color: #9ca3af;
    --ms-option-bg: #1f2937;
    --ms-option-bg-pointed: #374151;
    --ms-option-bg-selected: #3b82f6;
    --ms-option-bg-selected-pointed: #2563eb;
    --ms-option-color: #e5e7eb;
    --ms-option-color-pointed: #ffffff;
    --ms-option-color-selected: #ffffff;
    --ms-clear-color: #9ca3af;
    --ms-clear-color-hover: #ef4444;
    --ms-spinner-color: #3b82f6;
}

.dark .multiselect-custom .multiselect-options {
    background: #111827;
}

/* Custom input date styles */
.custom_input input.py-3 {
    padding-block: 7px;
    border-radius: 0.25rem;
}

.dark .custom_input input {
    background: #1f2937;
    border-color: #374151;
    color: #e5e7eb;
}
</style>
