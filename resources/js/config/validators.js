// =============================
//  CÁC RULE MẶC ĐỊNH
// =============================
const ruleFunctions = {
    required(value) {
        return value !== null && value !== undefined && value.toString().trim() !== "";
    },

    numeric(value) {
        return /^[0-9]+$/.test(value);
    },

    min(value, param) {
        return Number(value) >= Number(param);
    },

    max(value, param) {
        return Number(value) <= Number(param);
    },

    email(value) {
        return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(value);
    },
    phone(value) {
        return /^[0-9]{9,12}$/.test(value);
    }
};


// =============================
//  TẠO MESSAGE TỰ ĐỘNG
// =============================
function getErrorMessage(fieldLabel, ruleName, param = null) {
    const messages = {
        required: `${fieldLabel} không được bỏ trống`,
        numeric: `${fieldLabel} phải là số`,
        min: `${fieldLabel} phải lớn hơn hoặc bằng ${param}`,
        max: `${fieldLabel} phải nhỏ hơn hoặc bằng ${param}`,
        email: `${fieldLabel} không đúng định dạng email`,
        phone: `${fieldLabel} không đúng định dạng số điện thoại`,
    };

    return messages[ruleName] || `${fieldLabel} không hợp lệ`;
}


// =============================
//  HÀM CHÍNH: validate(data, rules, messages)
//  Trả về object lỗi
// =============================
export function validate(data, rules, fieldLabels = {}) {
    let errors = {};

    for (let field in rules) {
        let value = data[field];
        let fieldRules = rules[field];
        let label = fieldLabels[field] || field;

        for (let rule of fieldRules) {
            let ruleName = rule;
            let param = null;

            // Nếu rule dạng "min:5"
            if (rule.includes(":")) {
                const parts = rule.split(":");
                ruleName = parts[0];
                param = parts[1];
            }

            const validateFn = ruleFunctions[ruleName];

            if (!validateFn) {
                console.warn(`⚠ Rule ${ruleName} chưa được định nghĩa!`);
                continue;
            }

            const passed = validateFn(value, param);

            if (!passed) {
                errors[field] = [getErrorMessage(label, ruleName, param)];
                break; // dừng validate field này, tránh lỗi chồng nhau
            }
        }
    }

    return errors;
}
