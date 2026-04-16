<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Bang danh muc cap tham quyen dung cho phan quyen theo cap bac.
        Schema::create('authority_levels', function (Blueprint $table) {
            $table->id(); // Khoa chinh cap tham quyen.
            $table->unsignedSmallInteger('rank')->unique(); // Thu tu cap bac (1 thap -> cao dan).
            $table->string('name', 120); // Ten cap bac de hien thi UI.
            $table->string('minimum_role', 20)->default('employee'); // Role toi thieu duoc phep su dung cap nay.
            $table->boolean('is_active')->default(true); // Danh dau cap bac con su dung.
            $table->timestamps(); // created_at, updated_at.

            $table->comment('Bang danh muc cac muc tham quyen theo thu bac.');
        });

        // Seed bo cap bac mac dinh cho he thong.
        DB::table('authority_levels')->insert([
            ['rank' => 1, 'name' => 'Muc 1 - Nhan vien', 'minimum_role' => 'employee', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['rank' => 2, 'name' => 'Muc 2 - To pho / Senior', 'minimum_role' => 'employee', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['rank' => 3, 'name' => 'Muc 3 - Truong nhom', 'minimum_role' => 'employee', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['rank' => 4, 'name' => 'Muc 4 - Truong phong', 'minimum_role' => 'hr', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['rank' => 5, 'name' => 'Muc 5 - Giam doc / Quan ly cao', 'minimum_role' => 'admin', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    public function down(): void
    {
        // Hoan tac bang danh muc cap tham quyen.
        Schema::dropIfExists('authority_levels');
    }
};
