<!-- components/charts/RevenueComparisonChart.vue -->
<template>
    <apexchart type="area" :height="height" :options="chartOptions" :series="series" />
</template>

<script setup>
import { computed } from 'vue'
import { formatMoney } from '@/config/helpers.js'

const props = defineProps({
    height: {
        type: [String, Number],
        default: '100%'
    },
    plannedData: {
        type: Array,
        required: true,
        default: () => []
    },
    actualData: {
        type: Array,
        required: true,
        default: () => []
    },
    currencySymbol: {
        type: String,
        default: 'VNĐ'
    }
})

const series = computed(() => [
    {
        name: 'Doanh thu kế hoạch',
        data: props.plannedData
    },
    {
        name: 'Doanh thu thực tế',
        data: props.actualData
    }
])

// Format số tiền
// const formatMoney = (value) => {
//     if (value === 0) return '0'
//     if (value >= 1000000000) return (value / 1000000000).toFixed(1) + ' tỷ'
//     if (value >= 1000000) return (value / 1000000).toFixed(1) + ' tr'
//     if (value >= 1000) return (value / 1000).toFixed(1) + 'k'
//     return value.toFixed(0)
// }

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
        width: [3, 3]
    },
    colors: ['#3B82F6', '#10B981'],
    fill: {
        type: 'gradient',
        gradient: {
            shadeIntensity: 1,
            opacityFrom: [0.5, 0.5],
            opacityTo: [0.1, 0.1],
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
            formatter: function(value, timestamp) {
                return formatShortDate(timestamp)
            }
        }
    },
    yaxis: {
        labels: {
            style: {
                fontSize: '11px',
                colors: '#666'
            },
            formatter: function(value) {
                return `${formatMoney(value)} ${props.currencySymbol}`
            }
        }
    },
    grid: {
        borderColor: '#f1f1f1',
        strokeDashArray: 4
    },
    tooltip: {
        custom: function({ series, seriesIndex, dataPointIndex, w }) {
            const plannedValue = series[0][dataPointIndex]
            const actualValue = series[1][dataPointIndex]
            const timestamp = w.globals.seriesX[seriesIndex][dataPointIndex]
            const dateStr = formatVietnameseDate(timestamp)
            
            const difference = actualValue - plannedValue
            const differencePercent = plannedValue > 0 
                ? ((difference / plannedValue) * 100).toFixed(1)
                : 0
            
            return `<div class="apexcharts-tooltip-custom" style="padding: 10px 14px; min-width: 240px;">
                <div style="font-weight: 600; margin-bottom: 8px; color: #333; font-size: 13px;">${dateStr}</div>
                
                <div style="margin-bottom: 6px;">
                    <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 2px;">
                        <div style="display: flex; align-items: center;">
                            <span style="display: inline-block; width: 10px; height: 10px; border-radius: 2px; background: #3B82F6; margin-right: 8px;"></span>
                            <span style="color: #666; font-size: 12px;">Kế hoạch:</span>
                        </div>
                        <span style="color: #3B82F6; font-weight: 600; font-size: 13px;">${formatMoney(plannedValue)} ${props.currencySymbol}</span>
                    </div>
                </div>
                
                <div style="margin-bottom: 6px;">
                    <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 2px;">
                        <div style="display: flex; align-items: center;">
                            <span style="display: inline-block; width: 10px; height: 10px; border-radius: 2px; background: #10B981; margin-right: 8px;"></span>
                            <span style="color: #666; font-size: 12px;">Thực tế:</span>
                        </div>
                        <span style="color: #10B981; font-weight: 600; font-size: 13px;">${formatMoney(actualValue)} ${props.currencySymbol}</span>
                    </div>
                </div>
            
            </div>`
        }
    },
    legend: {
        show: true,
        position: 'top',
        horizontalAlign: 'left',
        fontSize: '13px',
        fontWeight: 500,
        markers: {
            width: 12,
            height: 12,
            radius: 2
        },
        itemMargin: {
            horizontal: 16,
            vertical: 0
        }
    }
}))
</script>
