<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class StoreUserRequest extends BaseRequest
{
    protected function prepareForValidation(): void
    {
        if ($this->input('employment_status') === 'terminated') {
            $this->merge(['status' => 'blocked']);
        }
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email:rfc', 'max:255', 'regex:/^[A-Za-z0-9._%+-]+@gmail\.com$/i', 'unique:users,email'],
            'phone' => ['required', 'string', 'regex:/^(0|\+84)[0-9]{9,10}$/', 'max:20', 'unique:users,phone'],
            'password' => ['required', 'confirmed', Password::min(8)->mixedCase()->numbers()],
            'address' => ['nullable', 'string', 'max:500'],
            'province_id' => ['nullable', 'exists:provinces,id'],
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
            'status' => ['required', 'in:active,inactive,pending,blocked'],
            'avatar' => ['nullable', 'image', 'mimes:jpg,jpeg,png,gif', 'max:2048'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Vui lòng nhap ho va ten.',
            'email.required' => 'Vui lòng nhap email.',
            'email.unique' => 'Email da ton tai.',
            'email.regex' => 'Email phai dung dinh dang Gmail.',
            'phone.required' => 'Vui lòng nhap so dien thoai.',
            'phone.unique' => 'So dien thoai da ton tai.',
            'phone.regex' => 'So dien thoai không dung dinh dang.',
            'hire_date.required' => 'Vui lòng chọn ngay vao lam.',
            'base_salary.numeric' => 'Lương cơ bản phai la so.',
            'base_salary.min' => 'Lương cơ bản không duoc am.',
            'date_of_birth.before' => 'Ngay sinh phai nho hon ngay vao lam.',
            'department_id.required' => 'Vui lòng chọn phòng ban.',
            'position_id.required' => 'Vui lòng chọn chức vụ.',
            'employment_type.required' => 'Vui lòng chọn loai nhân sự.',
            'termination_date.required' => 'Nhân viên nghi viec phai co ngay nghi.',
            'termination_date.after_or_equal' => 'Ngày nghỉ phai lon hon hoac bang ngay vao lam.',
        ];
    }

    public function attributes(): array
    {
        return [
            'name' => 'ho va ten',
            'email' => 'email',
            'phone' => 'so dien thoai',
            'date_of_birth' => 'ngay sinh',
            'hire_date' => 'ngay vao lam',
            'termination_date' => 'ngay nghi viec',
            'department_id' => 'phòng ban',
            'position_id' => 'chức vụ',
            'base_salary' => 'lương cơ bản',
            'employment_status' => 'trạng thái lam viec',
            'employment_type' => 'loai nhân sự',
            'status' => 'trạng thái tài khoản',
        ];
    }
}


