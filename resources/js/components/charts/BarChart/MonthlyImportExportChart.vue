<!-- components/charts/MonthlyImportExportChart.vue -->
<template>
    <div class="chart-container">
        <apexchart type="bar" :height="computedHeight" :options="chartOptions" :series="series" />
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
    months: {
        type: Array,
        default: () => ['Tháng 1', 'Tháng 2', 'Tháng 3', 'Tháng 4', 'Tháng 5', 'Tháng 6', 'Tháng 7', 'Tháng 8', 'Tháng 9', 'Tháng 10', 'Tháng 11', 'Tháng 12']
    }
})

// Detect dark mode
const isDarkMode = ref(document.documentElement.classList.contains('dark'))

const windowWidth = ref(window.innerWidth)

const updateWindowWidth = () => {
    windowWidth.value = window.innerWidth
}

// Theo dõi thay đổi dark mode
const observer = new MutationObserver((mutations) => {
    mutations.forEach((mutation) => {
        if (mutation.attributeName === 'class') {
            isDarkMode.value = document.documentElement.classList.contains('dark')
        }
    })
})

onMounted(() => {
    window.addEventListener('resize', updateWindowWidth)
    observer.observe(document.documentElement, { attributes: true })
})

onUnmounted(() => {
    window.removeEventListener('resize', updateWindowWidth)
    observer.disconnect()
})

const computedHeight = computed(() => {
    if (typeof props.height === 'number') {
        return props.height
    }

    if (windowWidth.value < 640) {
        return 280
    } else if (windowWidth.value < 1024) {
        return 320
    } else {
        return 350
    }
})

const chartOptions = computed(() => {
    const isMobile = windowWidth.value < 640
    const isTablet = windowWidth.value < 1024
    const isDark = isDarkMode.value

    const textColor = isDark ? '#E5E7EB' : '#1F2937'
    const gridColor = isDark ? '#374151' : '#f1f1f1'
    const tooltipBg = isDark ? '#1F2937' : '#FFFFFF'
    const tooltipText = isDark ? '#E5E7EB' : '#1F2937'

    const formattedMonths = props.months.map(month => {
        if (isMobile) {
            return month.replace('Tháng ', 'T')
        }
        return month
    })

    return {
        chart: {
            type: 'bar',
            height: '100%',
            toolbar: {
                show: !isMobile,
                tools: {
                    zoom: !isMobile,
                    zoomin: !isMobile,
                    zoomout: !isMobile,
                    pan: !isMobile,
                    reset: !isMobile,
                    download: true
                }
            },
            animations: {
                enabled: !isMobile
            },
            background: 'transparent'
        },
        colors: ['#008FFB', '#00E396'],
        plotOptions: {
            bar: {
                borderRadius: isMobile ? 2 : 4,
                columnWidth: isMobile ? '50%' : '60%',
                horizontal: isMobile && props.months.length > 6,
                dataLabels: {
                    position: isMobile ? 'center' : 'top'
                }
            }
        },
        dataLabels: {
            enabled: !isMobile || props.months.length <= 6,
            formatter: function (val) {
                if (isMobile && val >= 1000) {
                    return (val / 1000).toFixed(0) + 'k'
                }
                return new Intl.NumberFormat('vi-VN', {
                    notation: 'compact',
                    maximumFractionDigits: 1
                }).format(val)
            },
            offsetY: isMobile ? 0 : -20,
            style: {
                fontSize: isMobile ? '10px' : '12px',
                colors: [textColor]
            }
        },
        xaxis: {
            categories: formattedMonths,
            labels: {
                style: {
                    fontSize: isMobile ? '10px' : '12px',
                    fontWeight: isMobile ? 'normal' : 'normal',
                    colors: Array(props.months.length).fill(textColor)
                },
                rotate: isMobile && props.months.length > 6 ? -45 : 0,
                hideOverlappingLabels: true,
                trim: true
            },
            axisTicks: {
                show: !isMobile,
                color: gridColor
            },
            axisBorder: {
                color: gridColor
            },
            title: {
                style: {
                    color: textColor
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

                    if (isMobile && value >= 1000) {
                        return (value / 1000).toFixed(0) + 'k'
                    }
                    return new Intl.NumberFormat('vi-VN', {
                        notation: 'compact',
                        maximumFractionDigits: 1
                    }).format(value)
                },
                style: {
                    fontSize: isMobile ? '10px' : '12px',
                    colors: [textColor]
                }
            },
            // Tự động điều chỉnh min/max thành số chẵn
            min: (min) => {
                return Math.floor(min / 2) * 2;
            },
            max: (max) => {
                return Math.ceil(max / 2) * 2;
            },
            tickAmount: isMobile ? 5 : 8,
            title: {
                style: {
                    color: textColor
                }
            }
        },
        grid: {
            borderColor: gridColor,
            xaxis: {
                lines: {
                    show: !isMobile
                }
            },
            yaxis: {
                lines: {
                    show: true
                }
            }
        },
        legend: {
            position: isMobile ? 'bottom' : 'top',
            horizontalAlign: 'center',
            fontSize: isMobile ? '12px' : '14px',
            labels: {
                colors: textColor
            },
            itemMargin: {
                horizontal: isMobile ? 10 : 20,
                vertical: isMobile ? 5 : 10
            }
        },
        tooltip: {
            theme: isDark ? 'dark' : 'light',
            y: {
                formatter: function (value) {
                    return new Intl.NumberFormat('vi-VN').format(value)
                }
            },
            style: {
                fontSize: isMobile ? '12px' : '14px',
                color: tooltipText
            },
            background: tooltipBg
        },
        responsive: [
            {
                breakpoint: 640,
                options: {
                    plotOptions: {
                        bar: {
                            columnWidth: '70%',
                            borderRadius: 3
                        }
                    },
                    dataLabels: {
                        enabled: props.months.length <= 4,
                        style: {
                            fontSize: '9px',
                            colors: [textColor]
                        }
                    },
                    xaxis: {
                        labels: {
                            style: {
                                fontSize: '9px',
                                colors: Array(props.months.length).fill(textColor)
                            },
                            maxHeight: 40
                        }
                    },
                    yaxis: {
                        tickAmount: 4,
                        labels: {
                            style: {
                                fontSize: '9px',
                                colors: [textColor]
                            }
                        }
                    },
                    legend: {
                        fontSize: '11px',
                        offsetY: 5,
                        labels: {
                            colors: textColor
                        }
                    }
                }
            }
        ]
    }
})
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
    flex-wrap: wrap;
    justify-content: center;
}

:deep(.apexcharts-text) {
    fill: v-bind('isDarkMode ? "#E5E7EB" : "#1F2937"');
}

:deep(.apexcharts-title-text) {
    fill: v-bind('isDarkMode ? "#E5E7EB" : "#1F2937"');
}

:deep(.apexcharts-legend-text) {
    color: v-bind('isDarkMode ? "#E5E7EB" : "#1F2937"') !important;
}

:deep(.apexcharts-gridline) {
    stroke: v-bind('isDarkMode ? "#374151" : "#f1f1f1"');
}

:deep(.apexcharts-xaxis line),
:deep(.apexcharts-yaxis line) {
    stroke: v-bind('isDarkMode ? "#4B5563" : "#E5E7EB"');
}

:deep(.apexcharts-tooltip) {
    background: v-bind('isDarkMode ? "#1F2937" : "#FFFFFF"') !important;
    border: 1px solid v-bind('isDarkMode ? "#374151" : "#E5E7EB"') !important;
    color: v-bind('isDarkMode ? "#E5E7EB" : "#1F2937"') !important;
}

:deep(.apexcharts-tooltip-title) {
    background: v-bind('isDarkMode ? "#111827" : "#F3F4F6"') !important;
    border-bottom: 1px solid v-bind('isDarkMode ? "#374151" : "#E5E7EB"') !important;
    color: v-bind('isDarkMode ? "#E5E7EB" : "#1F2937"') !important;
}

@media (max-width: 640px) {
    :deep(.apexcharts-xaxis-label) {
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        max-width: 40px;
    }

    :deep(.apexcharts-toolbar) {
        right: 5px !important;
        top: 5px !important;
    }
}
</style>