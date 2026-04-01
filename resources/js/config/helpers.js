import { format } from "date-fns";
import { vi } from "date-fns/locale";

export const vat = 10;

/**Hàm có tác dụng nhận vào chuỗi số và trả về giá trị được format
 * Ví dụ: 1000000 => 1,000,000
 */
export function formatMoney(value, decimal = 2) {
    if (value === null || value === undefined || value === "") return "";

    let str = value.toString().trim();

    // Nếu có nhiều dấu . → coi là phân cách hàng nghìn (VD: 4.500.000)
    const dotCount = (str.match(/\./g) || []).length;
    if (dotCount > 1) {
        str = str.replace(/\./g, "");
    }

    // Chỉ giữ số, dấu - và dấu .
    str = str.replace(/[^0-9.-]/g, "");

    // Chỉ cho phép 1 dấu .
    const parts = str.split(".");
    if (parts.length > 2) {
        str = parts[0] + "." + parts.slice(1).join("");
    }

    let [integer, fraction] = str.split(".");

    // Format phần nguyên
    integer = integer.replace(/\B(?=(\d{3})+(?!\d))/g, ",");

    // Không có phần thập phân
    if (fraction === undefined) {
        return integer;
    }

    // Giới hạn số chữ số thập phân
    fraction = fraction.slice(0, decimal);

    // Nếu .00 thì bỏ
    if (/^0+$/.test(fraction)) {
        return integer;
    }

    return `${integer}.${fraction}`;
}

// helpers.js
export function unformatMoney(str) {
    if (!str) return 0;
    return Number(String(str).replace(/[^\d.-]/g, "")) || 0;
}

/**Hàm có tác dụng format tiền từ input và trả dữ liệu về input
 * <input type="text" @input="handleMoneyInput">
 */
export function handleMoneyInput(event) {
    let value = event.target.value;

    value = value.replace(/[^0-9]/g, "");

    event.target.value = formatMoney(value);
}
/**
 * Hàm có tác dụng bỏ format tiền
 * Ví dụ: "1,000,000" => "1000000"
 */
export function removeMoneyFormat(value) {
    if (!value && value !== 0) return "";

    let str = value.toString().trim();

    // Lưu dấu âm nếu có
    const isNegative = str.startsWith("-");

    // Loại bỏ dấu âm tạm thời để xử lý
    if (isNegative) {
        str = str.substring(1);
    }

    // Đếm số dấu .
    const dotCount = (str.match(/\./g) || []).length;

    if (dotCount > 1) {
        // Nhiều dấu . → phân cách hàng nghìn → xoá hết
        str = str.replace(/\./g, "");
    }

    // Giữ số và dấu .
    str = str.replace(/[^0-9.]/g, "");

    // Chỉ cho phép 1 dấu .
    const parts = str.split(".");
    if (parts.length > 2) {
        str = parts[0] + "." + parts.slice(1).join("");
    }

    // Thêm lại dấu âm nếu có
    if (isNegative && str !== "" && str !== "0") {
        str = "-" + str;
    }

    return str;
}
export function formatDateTime(datetimeString, formatStr = "dd/MM/yyyy HH:mm") {
    if (!datetimeString) return "";
    return format(new Date(datetimeString), formatStr, { locale: vi });
}

// sửa lỗi cũ
export function formatDate(datetimeString, formatStr = "dd/MM/yyyy") {
    if (
        !datetimeString ||
        datetimeString === "null" ||
        datetimeString === "undefined"
    ) {
        return "-";
    }

    try {
        const date = new Date(datetimeString);
        if (isNaN(date.getTime())) {
            return "-";
        }

        // Tự động convert format cũ sang mới
        const correctedFormat = formatStr
            .replace(/DD/g, "dd") // DD → dd
            .replace(/YYYY/g, "yyyy") // YYYY → yyyy
            .replace(/YY/g, "yy"); // YY → yy

        return format(date, correctedFormat, { locale: vi });
    } catch (error) {
        console.error("Format date error:", error);
        return "-";
    }
}

/**
 * @param {Array} data - mảng object dữ liệu
 * @param {Function | String | Array} label - cách tạo label
 * @param {String} value - key dùng làm value
 * Cách sử dụng
 * const userOptions = computed(() =>
    return mapToOptionsSelect(
        props.users,
        item => `${item.name} (${item.email})`,
        'id'
    )
)
 */
export function mapToOptionsSelect(data = [], label, value) {
    if (!Array.isArray(data)) return [];

    return data.map((item) => ({
        label:
            typeof label === "function"
                ? label(item)
                : Array.isArray(label)
                    ? label.map((key) => item[key]).join(" ")
                    : item[label],
        value: item[value],
    }));
}

export function getParamsURL() {
    const paramsObject = Object.fromEntries(
        new URLSearchParams(window.location.search),
    );
    return paramsObject;
}
export function removeParamFromURL(key) {
    const url = new URL(window.location.href)
    const params = new URLSearchParams(url.search)

    params.delete(key)

    const newUrl = params.toString()
        ? `${url.pathname}?${params.toString()}`
        : url.pathname

    window.history.pushState({}, '', newUrl)
}
export const isEmptyObject = (obj) =>
    obj && typeof obj === "object" && Object.keys(obj).length === 0;

export function formatDisplayNumber(value) {
    if (value === null || value === undefined || value === "") return "";

    const number = Number(value);
    if (isNaN(number)) return "";

    return number
        .toString()
        .replace(/\.0+$/, "") // bỏ .00, .000
        .replace(/(\.\d*[1-9])0+$/, "$1"); // bỏ số 0 dư
}

export function formatTimeAgo(datetimeString) {
    if (!datetimeString) return "";

    const date = new Date(datetimeString);
    const now = new Date();
    const diffMs = now - date;
    const diffSecs = Math.floor(diffMs / 1000);
    const diffMins = Math.floor(diffSecs / 60);
    const diffHours = Math.floor(diffMins / 60);
    const diffDays = Math.floor(diffHours / 24);
    const diffMonths = Math.floor(diffDays / 30);
    const diffYears = Math.floor(diffDays / 365);

    // Xử lý thời gian trong tương lai
    if (diffMs < 0) {
        return "vừa xong";
    }

    // Vừa xong (dưới 1 phút)
    if (diffSecs < 60) {
        return "vừa xong";
    }

    // Phút trước
    if (diffMins < 60) {
        return `${diffMins} phút trước`;
    }

    // Giờ trước
    if (diffHours < 24) {
        return `${diffHours} giờ trước`;
    }

    // Ngày trước
    if (diffDays < 30) {
        return `${diffDays} ngày trước`;
    }

    // Tháng trước
    if (diffMonths < 12) {
        return `${diffMonths} tháng trước`;
    }

    // Năm trước
    return `${diffYears} năm trước`;
}

export function numberToMoneyText(value) {
    if (value === null || value === undefined || value === "") return "";

    const numValue = unformatMoney(value);

    if (numValue === 0) return "Không đồng";
    if (numValue < 0) return "Số tiền không hợp lệ";
    if (numValue >= 1000000000000) return "Số quá lớn";

    const ones = ["", "một", "hai", "ba", "bốn", "năm", "sáu", "bảy", "tám", "chín"];
    function readBlock(num, hasHigherBlock = false) {
        if (num === 0) return "";

        let result = "";
        const hundred = Math.floor(num / 100);
        const ten = Math.floor((num % 100) / 10);
        const one = num % 10;

        // Đọc hàng trăm
        if (hundred > 0) {
            result = ones[hundred] + " trăm";
        } else if (hasHigherBlock && (ten > 0 || one > 0)) {
            result = "không trăm";
        }

        // Đọc hàng chục và đơn vị
        if (ten === 0 && one === 0) {
            return result.trim();
        }

        if (ten === 0) {
            // Chục = 0, có đơn vị
            if (hundred > 0) {
                // Có trăm trong khối này → dùng "linh"
                result += " linh " + ones[one];
            } else if (hasHigherBlock) {
                // Không có trăm, nhưng có khối cao hơn → dùng "linh"
                result += (result ? " " : "") + "linh " + ones[one];
            } else {
                // Số đơn thuần (5, 9...) → không dùng "linh"
                result += ones[one];
            }
        } else if (ten === 1) {
            // Chục = 1
            result += (result ? " " : "") + "mười";
            if (one === 5) {
                result += " lăm";
            } else if (one > 0) {
                result += " " + ones[one];
            }
        } else {
            // Chục >= 2
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

    // Tách các hàng
    const billion = Math.floor(numValue / 1000000000);
    const million = Math.floor((numValue % 1000000000) / 1000000);
    const thousand = Math.floor((numValue % 1000000) / 1000);
    const remainder = numValue % 1000;

    let words = "";

    // Đọc hàng tỷ
    if (billion > 0) {
        words = readBlock(billion, false) + " tỷ";
    }

    // Đọc hàng triệu
    if (million > 0) {
        const millionText = readBlock(million, billion > 0);
        words += (words ? " " : "") + millionText + " triệu";
    } else if (billion > 0 && (thousand > 0 || remainder > 0)) {
        words += " không triệu";
    }

    // Đọc hàng nghìn
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

    // Viết hoa chữ cái đầu và thêm "đồng"
    words = words.trim();
    words = words.charAt(0).toUpperCase() + words.slice(1) + " đồng.";

    return words;
}