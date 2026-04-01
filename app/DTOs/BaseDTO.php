<?php

namespace App\DTOs;

use Illuminate\Http\Request;

abstract class BaseDTO
{
    /**
     * Khởi tạo DTO từ array dữ liệu.
     */
    public function __construct(array $parameters = [])
    {
        foreach ($parameters as $key => $value) {
            if (property_exists($this, $key)) {
                $this->$key = $value;
            }
        }
    }

    /**
     * Tạo DTO từ Laravel Request
     */
    public static function fromRequest(Request $request): static
    {
        return new static($request->validated());
    }

    /**
     * Tạo DTO từ một array (thường từ file cấu hình, db, etc)
     */
    public static function fromArray(array $data): static
    {
        return new static($data);
    }

    /**
     * Chuyển DTO thành mảng.
     */
    public function toArray(): array
    {
        return get_object_vars($this);
    }
}
