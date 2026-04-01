<!-- components/charts/SaleOrderQuantity.vue -->
<template>
    <apexchart type="area" :height="height" :options="chartOptions" :series="series" />
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
    height: {
        type: [String, Number],
        default: '100%'
    },
    data: {
        type: Array,
        required: true
    }
})

const series = computed(() => [{
    name: 'Số đơn hàng',
    data: props.data
}])

// Hàm format ngày tháng tiếng Việt
const formatVietnameseDate = (timestamp) => {
    const date = new Date(timestamp)
    const day = date.getDate()
    const month = date.getMonth() + 1
    const year = date.getFullYear()

    const weekdays = ['Chủ nhật', 'Thứ hai', 'Thứ ba', 'Thứ tư', 'Thứ năm', 'Thứ sáu', 'Thứ bảy']
    const weekday = weekdays[date.getDay()]

    return `${weekday}, ${day} tháng ${month}, ${year}`
}

const formatShortDate = (timestamp) => {
    const date = new Date(timestamp)
    const day = date.getDate()
    const month = date.getMonth() + 1
    return `${day} thg ${month}`
}

const chartOptions = computed(() => ({
    chart: {
        type: 'area',
        height: '100%',
        zoom: {
            autoScaleYaxis: true
        },
        toolbar: {
            show: true,
            tools: {
                download: true,
                selection: true,
                zoom: true,
                zoomin: true,
                zoomout: true,
                pan: true,
                reset: true
            }
        },
        locales: [{
            name: 'vi',
            options: {
                months: ['Tháng 1', 'Tháng 2', 'Tháng 3', 'Tháng 4', 'Tháng 5', 'Tháng 6',
                    'Tháng 7', 'Tháng 8', 'Tháng 9', 'Tháng 10', 'Tháng 11', 'Tháng 12'],
                shortMonths: ['Th1', 'Th2', 'Th3', 'Th4', 'Th5', 'Th6',
                    'Th7', 'Th8', 'Th9', 'Th10', 'Th11', 'Th12'],
                days: ['Chủ nhật', 'Thứ hai', 'Thứ ba', 'Thứ tư', 'Thứ năm', 'Thứ sáu', 'Thứ bảy'],
                shortDays: ['CN', 'T2', 'T3', 'T4', 'T5', 'T6', 'T7'],
                toolbar: {
                    download: 'Tải xuống SVG',
                    selection: 'Chọn vùng',
                    selectionZoom: 'Phóng to vùng chọn',
                    zoomIn: 'Phóng to',
                    zoomOut: 'Thu nhỏ',
                    pan: 'Di chuyển',
                    reset: 'Đặt lại'
                }
            }
        }],
        defaultLocale: 'vi'
    },
    dataLabels: {
        enabled: false
    },
    markers: {
        size: 0,
        style: 'hollow'
    },
    stroke: {
        curve: 'smooth',
        width: 2
    },
    colors: ['#4CAF50'],
    fill: {
        type: 'gradient',
        gradient: {
            shadeIntensity: 1,
            opacityFrom: 0.7,
            opacityTo: 0.3,
            stops: [0, 100]
        }
    },
    xaxis: {
        type: 'datetime',
        labels: {
            style: {
                fontSize: '11px',
                colors: '#666'
            },
            datetimeUTC: false,
            formatter: function (value, timestamp) {
                return formatShortDate(timestamp)
            }
        }
    },
    yaxis: {
        stepSize: 1,
        labels: {
            formatter: (val) => Math.floor(val)
        },
        min: 0
    },

    grid: {
        borderColor: '#f1f1f1',
        strokeDashArray: 4
    },
    tooltip: {
        custom: function ({ series, seriesIndex, dataPointIndex, w }) {
            const value = series[seriesIndex][dataPointIndex]
            const timestamp = w.globals.seriesX[seriesIndex][dataPointIndex]
            const dateStr = formatVietnameseDate(timestamp)

            return `<div class="apexcharts-tooltip-custom" style="padding: 8px 12px;">
                <div style="font-weight: 600; margin-bottom: 4px; color: #333;">${dateStr}</div>
                <div style="color: #4CAF50; font-weight: 500;">
                    <span style="display: inline-block; width: 8px; height: 8px; border-radius: 50%; background: #4CAF50; margin-right: 6px;"></span>
                    ${value} đơn hàng
                </div>
            </div>`
        }
    },
    legend: {
        show: false
    }
}))
</script>
