<template>
  <div class="w-full h-full">
    <VueApexCharts v-if="hasData" :key="chartKey" type="area" height="100%" :options="chartOptions"
      :series="chartSeries" />
    <div v-else class="flex items-center justify-center h-full">
      <div class="text-center">
        <svg class="w-16 h-16 mx-auto text-gray-400 dark:text-gray-500 mb-4" fill="none" stroke="currentColor"
          viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
            d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
        </svg>
        <p class="text-gray-500 dark:text-gray-400 font-medium">Không có dữ liệu trong khoảng thời gian này</p>
        <p class="text-sm text-gray-400 dark:text-gray-500 mt-2">Vui lòng chọn khoảng thời gian khác</p>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue'
import VueApexCharts from 'vue3-apexcharts'

const props = defineProps({
  series: {
    type: Array,
    required: true,
    default: () => []
  },
  minDate: {
    type: Number,
    required: false
  },
  maxDate: {
    type: Number,
    required: false
  },
  formatMoney: {
    type: Function,
    required: true
  }
})

const hasData = computed(() => {
  return props.series &&
    props.series.length > 0 &&
    props.series.some(s => s.data && s.data.length > 0)
})

const chartSeries = computed(() => {
  if (!hasData.value) return []
  return props.series
})

const chartKey = computed(() => {
  return JSON.stringify([props.minDate, props.maxDate, props.series])
})

// Hàm format số ngắn gọn (1K, 1M, 1B)
function formatShortNumber(value) {
  if (value == null || value == undefined || value == 0) return '0'

  const absValue = Math.abs(value)
  const sign = value < 0 ? '-' : ''

  if (absValue >= 1000000000) {
    return sign + (absValue / 1000000000).toFixed(1) + ' tỉ'
  } else if (absValue >= 1000000) {
    return sign + (absValue / 1000000).toFixed(1) + ' triệu'
  } else if (absValue >= 1000) {
    return sign + (absValue / 1000).toFixed(0) + ' nghìn'
  }

  return sign + Math.round(absValue).toString()
}

// Hàm tính nice number - làm tròn số về dạng đẹp (1, 2, 5, 10, 20, 50, 100...)
function getNiceNumber(value, round) {
  const exp = Math.floor(Math.log10(Math.abs(value)))
  const fraction = value / Math.pow(10, exp)
  let niceFraction

  if (round) {
    if (fraction < 1.5) niceFraction = 1
    else if (fraction < 3) niceFraction = 2
    else if (fraction < 7) niceFraction = 5
    else niceFraction = 10
  } else {
    if (fraction <= 1) niceFraction = 1
    else if (fraction <= 2) niceFraction = 2
    else if (fraction <= 5) niceFraction = 5
    else niceFraction = 10
  }

  return niceFraction * Math.pow(10, exp)
}

// Tính toán trục Y thông minh - tự động chia đều
function calculateYAxis(data) {
  const allValues = data.flatMap(s => s.data.map(d => d[1] || 0))

  if (allValues.length == 0) {
    return { min: 0, max: 100, tickAmount: 5 }
  }

  let dataMin = Math.min(...allValues)
  let dataMax = Math.max(...allValues)

  // Nếu tất cả giá trị = 0
  if (dataMin == 0 && dataMax == 0) {
    return { min: 0, max: 100, tickAmount: 5 }
  }

  // Tính range
  let range = dataMax - dataMin

  // Nếu range quá nhỏ (tất cả giá trị gần như bằng nhau)
  if (range < Math.abs(dataMax) * 0.01 || range == 0) {
    const center = dataMax
    const padding = Math.abs(center) * 0.2 || 100
    dataMin = center - padding
    dataMax = center + padding
    range = dataMax - dataMin
  }

  // Tính nice range với padding 20%
  const niceRange = getNiceNumber(range * 1.2, false)

  // Tính tick spacing - khoảng cách giữa các vạch
  const roughTickCount = 6 // Mục tiêu ~6 vạch
  const tickSpacing = getNiceNumber(niceRange / roughTickCount, true)

  // Tính min và max nice - làm tròn về bội số của tickSpacing
  const niceMin = Math.floor(dataMin / tickSpacing) * tickSpacing
  const niceMax = Math.ceil(dataMax / tickSpacing) * tickSpacing

  // Tính số tick thực tế
  const tickAmount = Math.round((niceMax - niceMin) / tickSpacing)

  return {
    min: niceMin,
    max: niceMax,
    tickAmount: Math.min(Math.max(tickAmount, 5), 10) // Giới hạn 5-10 ticks
  }
}

// QUAN TRỌNG: Biến yAxisConfig thành computed để tự động update khi props.series thay đổi
const yAxisConfig = computed(() => calculateYAxis(props.series))

const chartOptions = computed(() => {
  // Lấy tất cả timestamps và sắp xếp
  const allTimestamps = props.series.flatMap(s => s.data.map(d => d[0]))
  const uniqueTimestamps = [...new Set(allTimestamps)].sort((a, b) => a - b)

  return {
    chart: {
      type: 'area',
      height: '100%',
      zoom: { enabled: false },
      fontFamily: 'Lexend, system-ui, -apple-system, sans-serif',
      toolbar: { show: false },
    },
    colors: ['#3b82f6', '#ef4444'],
    dataLabels: { enabled: false },
    stroke: { curve: 'smooth', width: 2.5 },
    fill: {
      type: 'gradient',
      gradient: {
        shadeIntensity: 1,
        opacityFrom: 0.45,
        opacityTo: 0.05,
        stops: [0, 90, 100],
      },
    },
    grid: {
      borderColor: '#e5e7eb',
      strokeDashArray: 3,
      xaxis: { lines: { show: true } },
      yaxis: { lines: { show: true } },
      padding: { top: 10, right: 15, bottom: 0, left: 15 },
    },
    xaxis: {
      type: 'datetime',
      labels: {
        show: true,
        rotate: -45,
        rotateAlways: true,
        hideOverlappingLabels: false,
        trim: false,
        style: {
          colors: '#6b7280',
          fontSize: '11px',
          fontFamily: 'Lexend, system-ui, -apple-system, sans-serif',
          fontWeight: 500,
        },
        formatter: function (value, timestamp) {
          const date = new Date(timestamp)
          const d = date.getDate().toString().padStart(2, '0')
          const m = (date.getMonth() + 1).toString().padStart(2, '0')
          return `${d}/${m}`
        },
        datetimeFormatter: {
          year: 'yyyy',
          month: 'MM/yy',
          day: 'dd/MM',
          hour: 'HH:mm'
        },
      },
      axisBorder: { show: true, color: '#d1d5db' },
      axisTicks: { show: true, color: '#d1d5db', height: 6 },
    },
    yaxis: {
      min: yAxisConfig.value.min,
      max: yAxisConfig.value.max,
      tickAmount: yAxisConfig.value.tickAmount,
      forceNiceScale: false,
      axisBorder: {
        show: true,
        color: '#4b5563',
        width: 1
      },

      axisTicks: {
        show: true,
        color: '#4b5563',
        width: 6
      },

      labels: {
        formatter: formatShortNumber,
        style: {
          colors: '#374151',
          fontSize: '12px',
          fontWeight: 600,
          fontFamily: 'Lexend, system-ui, -apple-system, sans-serif',
        },
        minWidth: 80,
        offsetX: -5,
      },
    },
    tooltip: {
      shared: true,
      intersect: false,
      custom: function ({ series, seriesIndex, dataPointIndex, w }) {
        const date = new Date(w.globals.seriesX[seriesIndex][dataPointIndex])
        const d = date.getDate().toString().padStart(2, '0')
        const m = (date.getMonth() + 1).toString().padStart(2, '0')
        const y = date.getFullYear()

        // Kiểm tra dark mode
        const isDark = document.documentElement.classList.contains('dark')
        const bgColor = isDark ? '#1f2937' : '#ffffff'
        const titleBg = isDark ? '#111827' : '#f9fafb'
        const titleColor = isDark ? '#ffffff' : '#111827'
        const labelColor = isDark ? '#e5e7eb' : '#6b7280'
        const valueColor = isDark ? '#ffffff' : '#111827'
        const borderColor = isDark ? '#374151' : '#e5e7eb'

        let tooltipHtml = `
          <div style="background: ${bgColor}; border: 1px solid ${borderColor}; border-radius: 0.5rem; padding: 0; font-family: 'Lexend', sans-serif; min-width: 200px;">
            <div style="background: ${titleBg}; border-bottom: 1px solid ${borderColor}; padding: 8px 12px; font-weight: 600; font-size: 13px; color: ${titleColor};">
              Ngày ${d}/${m}/${y}
            </div>
            <div style="padding: 8px 12px;">
        `

        // Lặp qua từng series
        w.globals.seriesNames.forEach((name, idx) => {
          const value = series[idx][dataPointIndex] || 0
          const color = w.globals.colors[idx]
          const formattedValue = props.formatMoney(value) + ' đ'

          tooltipHtml += `
            <div style="display: flex; align-items: center; padding: 5px 0;">
              <span style="width: 12px; height: 12px; background: ${color}; border-radius: 50%; margin-right: 8px;"></span>
              <span style="color: ${labelColor}; font-weight: 500; margin-right: 8px;">${name}:</span>
              <span style="color: ${valueColor}; font-weight: 700; margin-left: auto;">${formattedValue}</span>
            </div>
          `
        })

        tooltipHtml += `
            </div>
          </div>
        `

        return tooltipHtml
      }
    },
    legend: {
      position: 'bottom',
      offsetY: 10,
      fontSize: '13px',
      fontFamily: 'Lexend, system-ui, -apple-system, sans-serif',
      fontWeight: 500,
    },
    annotations: yAxisConfig.value.min < 0 ? {
      yaxis: [{
        y: 0,
        borderColor: '#9ca3af',
        borderWidth: 2,
        strokeDashArray: 0,
        label: {
          text: '0',
          position: 'left',
          style: {
            color: '#6b7280',
            background: '#f3f4f6',
            fontSize: '11px',
            fontFamily: 'Lexend, system-ui, -apple-system, sans-serif',
          }
        }
      }]
    } : {},
  }
})
</script>

<style scoped>
/* Light mode tooltip */
:deep(.apexcharts-tooltip) {
  background: white !important;
  border: 1px solid #e5e7eb !important;
  box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05) !important;
  border-radius: 0.5rem !important;
  padding: 10px !important;
  font-family: 'Lexend', system-ui, -apple-system, sans-serif !important;
}

:deep(.apexcharts-tooltip-title) {
  background: #f9fafb !important;
  border-bottom: 1px solid #e5e7eb !important;
  font-weight: 600 !important;
  padding: 8px 12px !important;
  margin-bottom: 8px !important;
  font-family: 'Lexend', system-ui, -apple-system, sans-serif !important;
  font-size: 13px !important;
  color: #111827 !important;
}

:deep(.apexcharts-tooltip-series-group) {
  padding: 5px 12px !important;
}

:deep(.apexcharts-tooltip-text-y-label) {
  font-weight: 500 !important;
  margin-right: 8px !important;
  font-family: 'Lexend', system-ui, -apple-system, sans-serif !important;
  color: #6b7280 !important;
}

:deep(.apexcharts-tooltip-text-y-value) {
  font-weight: 700 !important;
  color: #111827 !important;
  font-family: 'Lexend', system-ui, -apple-system, sans-serif !important;
}

:deep(.apexcharts-tooltip-marker) {
  margin-right: 8px !important;
}

:deep(.apexcharts-legend-text) {
  font-family: 'Lexend', system-ui, -apple-system, sans-serif !important;
}

:deep(.apexcharts-xaxistooltip),
:deep(.apexcharts-yaxistooltip) {
  font-family: 'Lexend', system-ui, -apple-system, sans-serif !important;
}

/* Dark mode tooltip - Force tất cả text màu trắng */
.dark :deep(.apexcharts-tooltip) {
  background: #1f2937 !important;
  border: 1px solid #374151 !important;
  box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.3), 0 4px 6px -2px rgba(0, 0, 0, 0.2) !important;
  color: #ffffff !important;
}

.dark :deep(.apexcharts-tooltip-title) {
  background: #111827 !important;
  border-bottom: 1px solid #374151 !important;
  color: #ffffff !important;
}

.dark :deep(.apexcharts-tooltip *) {
  color: #ffffff !important;
}

.dark :deep(.apexcharts-tooltip-text-y-value) {
  color: #ffffff !important;
  font-weight: 700 !important;
}

.dark :deep(.apexcharts-tooltip-text-y-label) {
  color: #e5e7eb !important;
}

.dark :deep(.apexcharts-tooltip-text) {
  color: #ffffff !important;
}

.dark :deep(.apexcharts-tooltip-y-group) {
  color: #ffffff !important;
}

.dark :deep(.apexcharts-tooltip .apexcharts-tooltip-text-y-value) {
  color: #ffffff !important;
  font-weight: 700 !important;
}

.dark :deep(.apexcharts-tooltip .apexcharts-tooltip-text-y-label) {
  color: #e5e7eb !important;
}

.dark :deep(.apexcharts-tooltip-series-group .apexcharts-tooltip-text-y-value) {
  color: #ffffff !important;
  font-weight: 800 !important;
}

.dark :deep(.apexcharts-tooltip-series-group .apexcharts-tooltip-text-y-label) {
  color: #e5e7eb !important;
  font-weight: 500 !important;
}

.dark :deep(div.apexcharts-tooltip-text-y-value) {
  color: #ffffff !important;
}

.dark :deep(span.apexcharts-tooltip-text-y-value) {
  color: #ffffff !important;
}
</style>