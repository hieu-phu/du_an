<template>
  <div ref="chartContainer"></div>
</template>

<script setup>
import { ref, onMounted, onBeforeUnmount, watch, nextTick } from 'vue'
import ApexCharts from 'apexcharts'
import { formatMoney } from '@/config/helpers.js'

const props = defineProps({
  data: {
    type: Object,
    required: true,
    default: () => ({ labels: [], datasets: [] })
  },
  options: {
    type: Object,
    default: () => ({})
  },
  currency: {
    type: String,
    default: ''
  }
})

const chartContainer = ref(null)
let chartInstance = null

// Chuyển đổi data từ Chart.js format sang ApexCharts format
const convertToApexFormat = () => {
  const { labels, datasets } = props.data

  // Chuyển đổi datasets thành series
  const series = datasets.map(dataset => ({
    name: dataset.label || 'Series',
    data: dataset.data || []
  }))

  // Lấy title từ Chart.js options nếu có
  const chartJsTitle = props.options?.plugins?.title?.text || ''
  const chartJsSubtitle = props.options?.plugins?.subtitle?.text || ''

  // Tạo options cho ApexCharts (KHÔNG merge với props.options để tránh conflict)
  const apexOptions = {
    chart: {
      type: 'area',
      height: 350,
      zoom: {
        enabled: false
      },
      toolbar: {
        show: true
      },
      animations: {
        enabled: true
      }
    },
    dataLabels: {
      enabled: false
    },
    stroke: {
      curve: 'straight',
      width: 2
    },
    title: {
      text: chartJsTitle,
      align: 'left',
      style: {
        fontSize: '16px',
        fontWeight: 'bold'
      }
    },
    subtitle: {
      text: chartJsSubtitle,
      align: 'left'
    },
    labels: labels || [],
    xaxis: {
      type: labels && labels[0] instanceof Date ? 'datetime' : 'category',
      labels: {
        rotate: -45,
        rotateAlways: false
      }
    },
    yaxis: {
      opposite: false,
      labels: {
        formatter: (value) => {
          if (typeof value !== 'number') return value
          return `${formatMoney(value)} ${props.currency}`
        },
        style: {
          fontSize: '14px', 
          fontWeight: 600,
        }
      }
    },
    legend: {
      horizontalAlign: 'left',
      position: 'top'
    },
    fill: {
      type: 'gradient',
      gradient: {
        shadeIntensity: 1,
        opacityFrom: 0.7,
        opacityTo: 0.3,
        stops: [0, 90, 100]
      }
    },
    tooltip: {
      enabled: true,
      shared: true,
      intersect: false
    },
    grid: {
      show: true,
      borderColor: '#e0e0e0',
      strokeDashArray: 0,
      position: 'back'
    }
  }

  return { series, options: apexOptions }
}

const renderChart = async () => {
  if (!chartContainer.value) return

  // Destroy chart cũ nếu tồn tại
  if (chartInstance) {
    chartInstance.destroy()
    chartInstance = null
  }

  await nextTick()

  try {
    const { series, options } = convertToApexFormat()

    chartInstance = new ApexCharts(chartContainer.value, {
      series,
      ...options
    })

    await chartInstance.render()
  } catch (error) {
    console.error('Error rendering chart:', error)
  }
}

onMounted(() => {
  renderChart()
})

watch(
  () => [props.data, props.options],
  () => {
    renderChart()
  },
  { deep: true }
)

onBeforeUnmount(() => {
  if (chartInstance) {
    chartInstance.destroy()
  }
})
</script>

<style scoped>
div {
  width: 100%;
  height: 100%;
  min-height: 350px;
}
</style>