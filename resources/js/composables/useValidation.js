export const useValidation = () => {

    // === CÁC VALIDATOR CƠ BẢN ===

    /**
     * Validate email
     * @param {string} email
     * @param {object} options
     * @param {boolean} options.required - Có bắt buộc không (mặc định: true)
     * @param {number} options.maxLength - Độ dài tối đa (mặc định: 255)
     * @returns {string} Thông báo lỗi, rỗng nếu hợp lệ
     */
    const validateEmail = (email, options = {}) => {
        const { required = true, maxLength = 255, strict = false } = options;

        if (required && (!email || email.trim() === '')) {
            return 'Email là bắt buộc';
        }

        if (!required && (!email || email.trim() === '')) {
            return '';
        }

        // Validate nếu có giá trị
        if (email && email.trim() !== '') {
            const trimmedEmail = email.trim();

            const simpleRegex = /^[a-zA-Z0-9.]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;

            const strictRegex = /^[a-zA-Z0-9](?:\.(?!\.)|[a-zA-Z0-9])*[a-zA-Z0-9]@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;

            const selectedRegex = strict ? strictRegex : simpleRegex;

            if (!selectedRegex.test(trimmedEmail)) {
                return strict
                    ? 'Email không đúng định dạng'
                    : 'Email không hợp lệ. Chỉ cho phép chữ cái, số và dấu chấm (.)';
            }

            if (maxLength && trimmedEmail.length > maxLength) {
                return `Email không được quá ${maxLength} ký tự`;
            }
        }

        return '';
    };

    /**
     * Validate số điện thoại Việt Nam
     * @param {string} phone
     * @param {object} options
     * @param {boolean} options.required - Có bắt buộc không (mặc định: true)
     * @param {number} options.minLength - Độ dài tối thiểu (chữ số, mặc định: 10)
     * @param {number} options.maxLength - Độ dài tối đa (chữ số, mặc định: 11)
     * @returns {string} Thông báo lỗi, rỗng nếu hợp lệ
     */
    const validatePhone = (phone, options = {}) => {
        const { required = true, minLength = 10, maxLength = 11 } = options

        if (required) {
            const requiredError = validateRequired(phone, 'Số điện thoại')
            if (requiredError) return requiredError
        }

        // Nếu không bắt buộc và phone rỗng => pass
        if (!required && (!phone || phone.trim() === '')) {
            return ''
        }

        if (phone && phone.trim() !== '') {
            // Regex cho phép các ký tự đặc biệt thông thường
            const phoneRegex = /^(0[0-9]{9,10}|[\d\s\-\+\(\)]+)$/

            const digitsOnly = phone.replace(/\D/g, '')

            if (!phoneRegex.test(phone)) {
                return 'Số điện thoại không hợp lệ'
            }

            if (digitsOnly.length < minLength) {
                return `Số điện thoại phải có ít nhất ${minLength} số`
            }

            if (maxLength && digitsOnly.length > maxLength) {
                return `Số điện thoại không được quá ${maxLength} số`
            }

            // Kiểm tra đầu số Việt Nam
            const validPrefixes = [
                '09', '03', '05', '07', '08', '02',
                '84', '+84'
            ]

            const hasValidPrefix = validPrefixes.some(prefix =>
                digitsOnly.startsWith(prefix) ||
                phone.startsWith(prefix) ||
                phone.startsWith(`+${prefix}`)
            )

            if (!hasValidPrefix) {
                return 'Số điện thoại không phải đầu số Việt Nam hợp lệ'
            }
        }

        return ''
    }

    /**
     * Validate trường bắt buộc
     * @param {any} value
     * @param {string} fieldName - Tên trường hiển thị trong lỗi
     * @returns {string} Thông báo lỗi, rỗng nếu hợp lệ
     */
    const validateRequired = (value, fieldName = 'Trường này') => {
        if (value === null || value === undefined || value.toString().trim() === '') {
            return `${fieldName} là bắt buộc`
        }
        return ''
    }

    /**
     * Validate độ dài tối đa
     * @param {string} value
     * @param {number} max
     * @param {string} fieldName - Tên trường hiển thị trong lỗi
     * @returns {string} Thông báo lỗi, rỗng nếu hợp lệ
     */
    const validateMaxLength = (value, max, fieldName = 'Trường này') => {
        if (value && value.length > max) {
            return `${fieldName} không được quá ${max} ký tự`
        }
        return ''
    }

    /**
     * Validate độ dài tối thiểu
     * @param {string} value
     * @param {number} min
     * @param {string} fieldName - Tên trường hiển thị trong lỗi
     * @returns {string} Thông báo lỗi, rỗng nếu hợp lệ
     */
    const validateMinLength = (value, min, fieldName = 'Trường này') => {
        if (value && value.length < min) {
            return `${fieldName} phải có ít nhất ${min} ký tự`
        }
        return ''
    }

    /**
     * Validate số (number)
     * @param {any} value
     * @param {object} options
     * @param {boolean} options.required - Có bắt buộc không
     * @param {number} options.min - Giá trị tối thiểu
     * @param {number} options.max - Giá trị tối đa
     * @returns {string} Thông báo lỗi, rỗng nếu hợp lệ
     */
    const validateNumber = (value, options = {}) => {
        const { required = true, min, max, fieldName = 'Trường này' } = options

        if (required) {
            const requiredError = validateRequired(value, fieldName)
            if (requiredError) return requiredError
        }

        if (!required && (value === null || value === undefined || value === '')) {
            return ''
        }

        const num = Number(value)
        if (isNaN(num)) {
            return `${fieldName} phải là số`
        }

        if (min !== undefined && num < min) {
            return `${fieldName} phải lớn hơn hoặc bằng ${min}`
        }

        if (max !== undefined && num > max) {
            return `${fieldName} phải nhỏ hơn hoặc bằng ${max}`
        }

        return ''
    }

    /**
     * Validate URL
     * @param {string} url
     * @param {object} options
     * @param {boolean} options.required - Có bắt buộc không
     * @returns {string} Thông báo lỗi, rỗng nếu hợp lệ
     */
    const validateUrl = (url, options = {}) => {
        const { required = true, fieldName = 'URL' } = options

        if (required) {
            const requiredError = validateRequired(url, fieldName)
            if (requiredError) return requiredError
        }

        if (!required && (!url || url.trim() === '')) {
            return ''
        }

        if (url && url.trim() !== '') {
            try {
                new URL(url)
            } catch {
                return `${fieldName} không hợp lệ`
            }
        }

        return ''
    }

    /**
     * Validate mật khẩu
     * @param {string} password
     * @param {object} options
     * @param {boolean} options.required - Có bắt buộc không
     * @param {number} options.minLength - Độ dài tối thiểu (mặc định: 8)
     * @param {boolean} options.requireUppercase - Yêu cầu chữ hoa
     * @param {boolean} options.requireLowercase - Yêu cầu chữ thường
     * @param {boolean} options.requireNumber - Yêu cầu số
     * @param {boolean} options.requireSpecialChar - Yêu cầu ký tự đặc biệt
     * @returns {string} Thông báo lỗi, rỗng nếu hợp lệ
     */
    const validatePassword = (password, options = {}) => {
        const {
            required = true,
            minLength = 8,
            requireUppercase = true,
            requireLowercase = true,
            requireNumber = true,
            requireSpecialChar = true,
            fieldName = 'Mật khẩu'
        } = options

        if (required) {
            const requiredError = validateRequired(password, fieldName)
            if (requiredError) return requiredError
        }

        if (!required && (!password || password.trim() === '')) {
            return ''
        }

        if (password) {
            if (password.length < minLength) {
                return `${fieldName} phải có ít nhất ${minLength} ký tự`
            }

            if (requireUppercase && !/[A-Z]/.test(password)) {
                return `${fieldName} phải có ít nhất một chữ cái viết hoa`
            }

            if (requireLowercase && !/[a-z]/.test(password)) {
                return `${fieldName} phải có ít nhất một chữ cái viết thường`
            }

            if (requireNumber && !/\d/.test(password)) {
                return `${fieldName} phải có ít nhất một chữ số`
            }

            if (requireSpecialChar && !/[!@#$%^&*()_+\-=\[\]{};':"\\|,.<>\/?]/.test(password)) {
                return `${fieldName} phải có ít nhất một ký tự đặc biệt`
            }
        }

        return ''
    }

    /**
     * Validate xác nhận mật khẩu
     * @param {string} confirmPassword
     * @param {string} originalPassword
     * @param {string} fieldName - Tên trường hiển thị trong lỗi
     * @returns {string} Thông báo lỗi, rỗng nếu hợp lệ
     */
    const validateConfirmPassword = (confirmPassword, originalPassword, fieldName = 'Xác nhận mật khẩu') => {
        const requiredError = validateRequired(confirmPassword, fieldName)
        if (requiredError) return requiredError

        if (confirmPassword !== originalPassword) {
            return `${fieldName} không khớp với mật khẩu`
        }

        return ''
    }

    /**
     * Hàm tổng hợp - Validate nhiều rules cùng lúc
     * @param {any} value - Giá trị cần validate
     * @param {Array} validators - Mảng các hàm validator
     * @returns {string} Thông báo lỗi đầu tiên, rỗng nếu hợp lệ
     */
    const validateWithRules = (value, validators = []) => {
        for (const validator of validators) {
            const error = validator(value)
            if (error) return error
        }
        return ''
    }

    /**
     * Validate form với schema định nghĩa sẵn
     * @param {object} formData - Dữ liệu form
     * @param {object} schema - Schema validation: { fieldName: validatorFunction }
     * @returns {object} { valid: boolean, errors: object }
     */
    const validateForm = (formData, schema) => {
        const errors = {}

        Object.keys(schema).forEach(field => {
            const validator = schema[field]
            const value = formData[field]

            if (typeof validator === 'function') {
                const error = validator(value)
                if (error) errors[field] = error
            } else if (Array.isArray(validator)) {
                // Mảng các validator
                for (const v of validator) {
                    const error = v(value)
                    if (error) {
                        errors[field] = error
                        break
                    }
                }
            }
        })

        return {
            valid: Object.keys(errors).length === 0,
            errors
        }
    }

    return {
        // Các hàm validator cơ bản
        validateEmail,
        validatePhone,
        validateRequired,
        validateMaxLength,
        validateMinLength,
        validateNumber,
        validateUrl,
        validatePassword,
        validateConfirmPassword,

        // Hàm tổng hợp
        validateWithRules,
        validateForm
    }
}