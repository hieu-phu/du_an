<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rules\Password;

class UpdateUserRequest extends BaseRequest
{
    public function rules(): array
    {
        $userId = $this->route('user')?->id ?? $this->route('user');

        return [
            'name'     => ['required', 'string', 'max:255'],
            'email'    => ['required', 'string', 'email', 'max:255', "unique:users,email,{$userId}"],
            'phone'    => ['required', 'string', 'max:20', "unique:users,phone,{$userId}"],
            'password' => ['nullable', 'confirmed', Password::min(8)->mixedCase()->numbers()],
            'address'  => ['nullable', 'string', 'max:500'],
            'status'   => ['required', 'in:active,inactive,pending,blocked'],
            'avatar'   => ['nullable', 'image', 'mimes:jpg,jpeg,png,gif', 'max:2048'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required'      => 'Vui lòng nhập họ tên.',
            'email.required'     => 'Vui lòng nhập email.',
            'email.unique'       => 'Email đã được sử dụng bởi tài khoản khác.',
            'phone.required'     => 'Vui lòng nhập số điện thoại.',
            'phone.unique'       => 'Số điện thoại đã được sử dụng bởi tài khoản khác.',
            'password.confirmed' => 'Xác nhận mật khẩu không khớp.',
            'password.min'       => 'Mật khẩu phải có ít nhất 8 ký tự.',
            'password.mixed'     => 'Mật khẩu phải chứa cả chữ hoa và chữ thường.',
            'password.numbers'   => 'Mật khẩu phải chứa ít nhất một chữ số.',
            'address.max'        => 'Địa chỉ không được vượt quá 500 ký tự.',
            'status.required'    => 'Vui lòng chọn trạng thái.',
            'status.in'          => 'Trạng thái không hợp lệ.',
            'avatar.image'       => 'Ảnh đại diện phải là định dạng hình ảnh.',
            'avatar.mimes'       => 'Ảnh đại diện chỉ chấp nhận: jpg, jpeg, png, gif.',
            'avatar.max'         => 'Ảnh đại diện không được vượt quá 2MB.',
        ];
    }
}
