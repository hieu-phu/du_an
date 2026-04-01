<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rules\Password;

class StoreUserRequest extends BaseRequest
{
    public function rules(): array
    {
        return [
            'name'     => ['required', 'string', 'max:255'],
            'email'    => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'phone'    => ['required', 'string', 'max:20', 'unique:users,phone'],
            'password' => ['required', 'confirmed', Password::min(8)->mixedCase()->numbers()],
            'address'  => ['nullable', 'string', 'max:500'],
            'status'   => ['required', 'in:active,inactive,pending,blocked'],
            'avatar'   => ['nullable', 'image', 'mimes:jpg,jpeg,png,gif', 'max:2048'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required'       => 'Vui lòng nhập họ tên.',
            'name.max'            => 'Họ tên không được vượt quá 255 ký tự.',
            'email.required'      => 'Vui lòng nhập email.',
            'email.email'         => 'Email không đúng định dạng.',
            'email.max'           => 'Email không được vượt quá 255 ký tự.',
            'email.unique'        => 'Email đã tồn tại.',
            'phone.required'      => 'Vui lòng nhập số điện thoại.',
            'phone.max'           => 'Số điện thoại không được vượt quá 20 ký tự.',
            'phone.unique'        => 'Số điện thoại đã tồn tại.',
            'password.required'   => 'Vui lòng nhập mật khẩu.',
            'password.confirmed'  => 'Xác nhận mật khẩu không khớp.',
            'password.min'        => 'Mật khẩu phải chứa ít nhất :min ký tự.',
            'password.mixed'      => 'Mật khẩu phải chứa ít nhất 1 chữ hoa và 1 chữ thường.',
            'password.numbers'    => 'Mật khẩu phải chứa ít nhất 1 chữ số.',
            'address.max'         => 'Địa chỉ không được vượt quá 500 ký tự.',
            'status.required'     => 'Vui lòng chọn trạng thái.',
            'status.in'           => 'Trạng thái không hợp lệ.',
            'avatar.image'        => 'Ảnh đại diện phải là định dạng hình ảnh.',
            'avatar.mimes'        => 'Ảnh đại diện chỉ chấp nhận: jpg, jpeg, png, gif.',
            'avatar.max'          => 'Ảnh đại diện không được vượt quá 2MB.',
        ];
    }
}
