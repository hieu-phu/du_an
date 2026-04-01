<template>
    <div class="chart-container">
        <apexchart type="line" :height="computedHeight" :options="chartOptions" :series="series" />
    </div>
</template>

<script setup>
import { computed, ref, onMounted, onUnmounted } from 'vue'

const props = defineProps({
    height: {
        type: [String, Number],
        default: '100%'
    },
    series: {
        type: Array,
        required: true
    },
    dates: {
        type: Array,
        default: () => []
    }
})

const windowWidth = ref(window.innerWidth)

const updateWindowWidth = () => {
    windowWidth.value = window.innerWidth
}

onMounted(() => {
    window.addEventListener('resize', updateWindowWidth)
})

onUnmounted(() => {
    window.removeEventListener('resize', updateWindowWidth)
})

const computedHeight = computed(() => {
    if (typeof props.height === 'number') {
        return props.height
    }

    if (windowWidth.value < 640) {
        return 250
    } else if (windowWidth.value < 1024) {
        return 300
    } else {
        return 320
    }
})

const chartOptions = computed(() => {
    const categories = props.dates.map(date => {
        if (!date || typeof date !== 'string') return Date.now()

        const parts = date.split('/')
        if (parts.length < 2) return Date.now()

        const day = parseInt(parts[0])
        const month = parseInt(parts[1])

        const currentYear = new Date().getFullYear()
        return new Date(currentYear, month - 1, day).getTime()
    })

    const tickAmount = calculateSmartTickAmount(props.dates)

    return {
        chart: {
            height: '100%',
            type: 'line',
            zoom: {
                enabled: windowWidth.value > 768,
            },
            toolbar: {
                show: windowWidth.value > 640,
                tools: {
                    zoom: windowWidth.value > 768,
                    zoomin: windowWidth.value > 768,
                    zoomout: windowWidth.value > 768,
                    pan: windowWidth.value > 768,
                    reset: windowWidth.value > 768,
                    download: true
                }
            }
        },
        colors: ['#4CAF50', '#F44336', '#2196F3'],
        stroke: {
            curve: 'smooth',
            width: windowWidth.value < 640 ? 2 : 3
        },
        xaxis: {
            type: 'datetime',
            categories: categories,
            tickAmount: tickAmount,
            labels: {
                datetimeUTC: false,
                style: {
                    fontSize: windowWidth.value < 640 ? '10px' : '11px',
                    colors: '#666'
                },
                rotate: props.dates.length > 10 ? -45 : 0,
                hideOverlappingLabels: true,
                formatter: function (value, timestamp) {
                    if (timestamp) {
                        const date = new Date(timestamp)
                        const day = date.getDate()
                        const month = date.getMonth() + 1

                        if (windowWidth.value < 640) {
                            return `${day}/${month}`
                        }
                        return `${day}/${month}`
                    }
                    return value
                }
            }
        },
        yaxis: {
            forceNiceScale: true,
            labels: {
                formatter: function (value) {
                    // Chỉ hiển thị số chẵn (kiểm tra với dung sai nhỏ do floating point)
                    if (Math.abs(value % 2) > 0.01 && Math.abs(value % 2) < 1.99) {
                        return '';
                    }

                    // Format số gọn hơn trên mobile
                    if (windowWidth.value < 640 && value >= 1000) {
                        return (value / 1000).toFixed(0) + 'k'
                    }
                    return new Intl.NumberFormat('vi-VN').format(value)
                },
                style: {
                    fontSize: windowWidth.value < 640 ? '10px' : '11px'
                }
            },
            // Tự động điều chỉnh min/max thành số chẵn
            min: (min) => {
                return Math.floor(min / 2) * 2;
            },
            max: (max) => {
                return Math.ceil(max / 2) * 2;
            },
            tickAmount: windowWidth.value < 640 ? 5 : 8,
        },
        legend: {
            position: windowWidth.value < 640 ? 'bottom' : 'top',
            horizontalAlign: 'center',
            fontSize: windowWidth.value < 640 ? '12px' : '14px'
        },
        grid: {
            borderColor: '#f1f1f1',
            xaxis: {
                lines: {
                    show: windowWidth.value > 640
                }
            }
        },
        tooltip: {
            x: {
                format: 'dd/MM'
            },
            y: {
                formatter: function (value) {
                    return new Intl.NumberFormat('vi-VN').format(value)
                }
            },
            shared: true,
            intersect: false
        }
    }
})

const calculateSmartTickAmount = (dates) => {
    const dateCount = dates.length
    const isMobile = windowWidth.value < 640

    if (dateCount <= 0) return isMobile ? 5 : 10
    if (dateCount <= 7) {
        return dateCount - 1
    } else if (dateCount <= 14) {
        return Math.ceil(dateCount / (isMobile ? 3 : 2))
    } else if (dateCount <= 21) {
        return Math.ceil(dateCount / (isMobile ? 4 : 3))
    } else if (dateCount <= 31) {
        return Math.ceil(dateCount / (isMobile ? 5 : 4))
    } else {
        return isMobile ? 7 : 10
    }
}
</script>

<style scoped>
.chart-container {
    width: 100%;
    height: 100%;
    min-height: 250px;
    position: relative;
}

:deep(.apexcharts-canvas) {
    width: 100% !important;
}

:deep(.apexcharts-legend) {
    padding: 8px 0;
}
</style>