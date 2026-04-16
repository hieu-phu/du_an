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
            'status' => ['required', 'in:active,inactive,pending,blocked'],
            'avatar' => ['nullable', 'image', 'mimes:jpg,jpeg,png,gif', 'max:2048'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Vui long nhap ho va ten.',
            'email.required' => 'Vui long nhap email.',
            'email.unique' => 'Email da ton tai.',
            'email.regex' => 'Email phai dung dinh dang Gmail.',
            'phone.required' => 'Vui long nhap so dien thoai.',
            'phone.unique' => 'So dien thoai da ton tai.',
            'phone.regex' => 'So dien thoai khong dung dinh dang.',
            'hire_date.required' => 'Vui long chon ngay vao lam.',
            'base_salary.numeric' => 'Luong co ban phai la so.',
            'base_salary.min' => 'Luong co ban khong duoc am.',
            'date_of_birth.before' => 'Ngay sinh phai nho hon ngay vao lam.',
            'department_id.required' => 'Vui long chon phong ban.',
            'position_id.required' => 'Vui long chon chuc vu.',
            'employment_type.required' => 'Vui long chon loai nhan su.',
            'termination_date.required' => 'Nhan vien nghi viec phai co ngay nghi.',
            'termination_date.after_or_equal' => 'Ngay nghi phai lon hon hoac bang ngay vao lam.',
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
            'department_id' => 'phong ban',
            'position_id' => 'chuc vu',
            'base_salary' => 'luong co ban',
            'employment_status' => 'trang thai lam viec',
            'employment_type' => 'loai nhan su',
            'status' => 'trang thai tai khoan',
        ];
    }
}
