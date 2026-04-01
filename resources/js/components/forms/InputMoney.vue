<template>
  <div>
    <FormInput
      v-bind="$attrs"
      v-model="displayValue"
      @input="handleInput"
      @blur="handleBlur"
      :unit="props.unit"
      :class="props.class"
    />

    <p
      v-if="props.showText && moneyText" :class="props.class"
      class="mt-1.5 text-sm text-red-600 dark:text-blue-400 italic"
    >
      {{ moneyText }}
    </p>
  </div>
</template>

<script setup>
import { ref, watch, computed } from 'vue'
import FormInput from '@/components/ui/FormInput.vue'
import { formatMoney, unformatMoney } from '@/config/helpers.js'

// ❗ QUAN TRỌNG: tắt kế thừa attrs vào root
defineOptions({
  inheritAttrs: false
})

const props = defineProps({
  unit: {
    type: String,
    default: '₫'
  },
  showText: {
    type: Boolean,
    default: true
  },
  class: {
    type: String,
    default: ''
  }

})

// Model value - giá trị thực (số thuần)
const modelValue = defineModel()

// Display value - giá trị hiển thị (đã format)
const displayValue = ref('')

// Initialize display value
if (modelValue.value) {
  displayValue.value = formatMoney(modelValue.value)
}

// Watch model value changes from parent
watch(() => modelValue.value, (newValue) => {
  if (newValue !== undefined && newValue !== null) {
    displayValue.value = formatMoney(newValue)
  } else {
    displayValue.value = ''
  }
})

// Handle input event - format while typing
function handleInput(event) {
  const inputValue = event.target.value
  const numericValue = unformatMoney(inputValue)
  
  // Update model value (số thuần)
  modelValue.value = numericValue
  
  // Format and update display
  displayValue.value = formatMoney(numericValue)
}

// Handle blur event - final formatting
function handleBlur(event) {
  const numericValue = unformatMoney(event.target.value)
  modelValue.value = numericValue
  displayValue.value = formatMoney(numericValue)
}

// Computed property for money text
const moneyText = computed(() => {
  return numberToMoneyText(modelValue.value, props.unit)
})

function numberToMoneyText(value, currencySymbol = "₫") {
    if (value === null || value === undefined || value === "") return "";

    const numValue = typeof value === 'string' ? unformatMoney(value) : value;

    // Cấu hình cho từng symbol tiền tệ
    const currencyConfig = {
        "₫": { name: "đồng", hasDecimal: false },
        "$": { name: "đô la Mỹ", subName: "cent", hasDecimal: true },
        "€": { name: "euro", subName: "cent", hasDecimal: true },
        "£": { name: "bảng Anh", subName: "pence", hasDecimal: true },
        "¥": { name: "yên Nhật", hasDecimal: false },
        "元": { name: "nhân dân tệ", subName: "phân", hasDecimal: true },
        "₩": { name: "won Hàn Quốc", hasDecimal: false },
        "฿": { name: "bạt Thái Lan", subName: "satang", hasDecimal: true },
        "S$": { name: "đô la Singapore", subName: "cent", hasDecimal: true },
        "A$": { name: "đô la Úc", subName: "cent", hasDecimal: true },
        "CA$": { name: "đô la Canada", subName: "cent", hasDecimal: true },
        "HK$": { name: "đô la Hồng Kông", subName: "cent", hasDecimal: true },
        "₹": { name: "rupee Ấn Độ", subName: "paise", hasDecimal: true },
        "₽": { name: "rúp Nga", subName: "kopek", hasDecimal: true },
        "R$": { name: "real Brazil", subName: "centavo", hasDecimal: true },
        "CHF": { name: "franc Thụy Sĩ", subName: "centime", hasDecimal: true },
        "kr": { name: "krona Thụy Điển", subName: "öre", hasDecimal: true },
        "zł": { name: "zloty Ba Lan", subName: "grosz", hasDecimal: true },
    };

    const config = currencyConfig[currencySymbol] || {
        name: "đơn vị tiền tệ",
        hasDecimal: true
    };

    if (numValue === 0) return `Không ${config.name}`;
    if (numValue < 0) return "Số tiền không hợp lệ";
    if (numValue >= 1000000000000) return "Số quá lớn";

    const ones = ["", "một", "hai", "ba", "bốn", "năm", "sáu", "bảy", "tám", "chín"];

    function readBlock(num, hasHigherBlock = false) {
        if (num === 0) return "";

        let result = "";
        const hundred = Math.floor(num / 100);
        const ten = Math.floor((num % 100) / 10);
        const one = num % 10;

        if (hundred > 0) {
            result = ones[hundred] + " trăm";
        } else if (hasHigherBlock && (ten > 0 || one > 0)) {
            result = "không trăm";
        }

        if (ten === 0 && one === 0) {
            return result.trim();
        }

        if (ten === 0) {
            if (hundred > 0) {
                result += " linh " + ones[one];
            } else if (hasHigherBlock) {
                result += (result ? " " : "") + "linh " + ones[one];
            } else {
                result += ones[one];
            }
        } else if (ten === 1) {
            result += (result ? " " : "") + "mười";
            if (one === 5) {
                result += " lăm";
            } else if (one > 0) {
                result += " " + ones[one];
            }
        } else {
            result += (result ? " " : "") + ones[ten] + " mươi";

            if (one === 1) {
                result += " mốt";
            } else if (one === 4) {
                result += " tư";
            } else if (one === 5) {
                result += " lăm";
            } else if (one > 0) {
                result += " " + ones[one];
            }
        }

        return result.trim();
    }

    // Tách phần nguyên và phần thập phân
    const integerPart = Math.floor(numValue);
    const decimalPart = Math.round((numValue - integerPart) * 100);

    // Đọc phần nguyên
    const billion = Math.floor(integerPart / 1000000000);
    const million = Math.floor((integerPart % 1000000000) / 1000000);
    const thousand = Math.floor((integerPart % 1000000) / 1000);
    const remainder = integerPart % 1000;

    let words = "";

    if (billion > 0) {
        words = readBlock(billion, false) + " tỷ";
    }

    if (million > 0) {
        const millionText = readBlock(million, billion > 0);
        words += (words ? " " : "") + millionText + " triệu";
    } else if (billion > 0 && (thousand > 0 || remainder > 0)) {
        words += " không triệu";
    }

    if (thousand > 0) {
        const thousandText = readBlock(thousand, billion > 0 || million > 0);
        words += (words ? " " : "") + thousandText + " nghìn";
    } else if ((billion > 0 || million > 0) && remainder > 0) {
        words += " không nghìn";
    }

    if (remainder > 0) {
        const remainderText = readBlock(remainder, billion > 0 || million > 0 || thousand > 0);
        words += (words ? " " : "") + remainderText;
    }

    words = words.trim();

    // Xử lý phần thập phân cho ngoại tệ
    if (config.hasDecimal && decimalPart > 0) {
        const decimalText = readBlock(decimalPart, false);
        words = words.charAt(0).toUpperCase() + words.slice(1) +
            ` ${config.name} ${decimalText} ${config.subName || "cent"}.`;
    } else {
        words = words.charAt(0).toUpperCase() + words.slice(1) + ` ${config.name}.`;
    }

    return words;
}
</script>