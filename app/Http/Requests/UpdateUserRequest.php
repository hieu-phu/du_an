<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class UpdateUserRequest extends BaseRequest
{
    protected function prepareForValidation(): void
    {
        if ($this->input('employment_status') === 'terminated') {
            $this->merge(['status' => 'blocked']);
        }

        if (!$this->filled('role_name')) {
            $this->merge(['role_name' => 'employee']);
        }
    }

    public function rules(): array
    {
        $userId = $this->route('user')?->id ?? $this->route('user');
        $allowedRoles = auth()->user()?->hasRole('admin')
            ? ['admin', 'hr', 'employee']
            : ['hr', 'employee'];

        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email:rfc', 'max:255', 'regex:/^[A-Za-z0-9._%+-]+@gmail\.com$/i', "unique:users,email,{$userId}"],
            'phone' => ['required', 'string', 'regex:/^(0|\+84)[0-9]{9,10}$/', 'max:20', "unique:users,phone,{$userId}"],
            'password' => ['nullable', 'confirmed', Password::min(8)->mixedCase()->numbers()],
            'address' => ['nullable', 'string', 'max:500'],
            'province_id' => ['nullable', 'exists:provinces,id'],
            'district_id' => ['nullable', 'exists:districts,id'],
            'ward_id' => ['nullable', 'exists:wards,id'],
            'address_line' => ['nullable', 'string', 'max:255'],
            'date_of_birth' => ['nullable', 'date', 'before:today', 'before:hire_date'],
            'hire_date' => ['required', 'date'],
            'termination_date' => ['nullable', 'date', 'after_or_equal:hire_date', Rule::requiredIf(fn () => $this->input('employment_status') === 'terminated')],
            'department_id' => ['required', 'exists:departments,id'],
            'position_id' => ['required', 'exists:positions,id'],
            'base_salary' => ['nullable', 'numeric', 'min:0'],
            'employment_status' => ['required', 'in:active,inactive,terminated'],
            'employment_type' => ['required', 'in:probation,official,intern,collaborator'],
            'role_name' => ['required', Rule::in($allowedRoles)],
            'status' => ['required', 'in:active,inactive,pending,blocked'],
            'avatar' => ['nullable', 'image', 'mimes:jpg,jpeg,png,gif', 'max:2048'],
        ];
    }

    public function messages(): array
    {
        return [
            'email.unique' => 'Email đã tồn tại.',
            'phone.unique' => 'Số điện thoại đã tồn tại.',
            'base_salary.numeric' => 'Lương cơ bản phải là số.',
            'base_salary.min' => 'Lương cơ bản không được âm.',
            'date_of_birth.before' => 'Ngày sinh phải nhỏ hơn ngày vào làm.',
            'department_id.required' => 'Vui lòng chọn phòng ban.',
            'position_id.required' => 'Vui lòng chọn chức vụ.',
            'employment_type.required' => 'Vui lòng chọn loại nhân sự.',
            'role_name.required' => 'Vui lòng chọn quyền tài khoản.',
            'termination_date.required' => 'Nhân viên nghỉ việc phải có ngày nghỉ.',
            'termination_date.after_or_equal' => 'Ngày nghỉ phải lớn hơn hoặc bằng ngày vào làm.',
        ];
    }
}
