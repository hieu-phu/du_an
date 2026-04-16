<?php

use App\Support\PositionCapability;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Bang danh muc capability nghiep vu co the gan cho chuc vu/user.
        Schema::create('position_capabilities', function (Blueprint $table) {
            $table->id(); // Khoa chinh capability.
            $table->string('code', 120)->unique(); // Ma capability duy nhat (vd: users.create).
            $table->string('name', 255); // Ten hien thi capability.
            $table->string('module', 100)->index(); // Module so huu capability.
            $table->string('description', 500)->nullable(); // Mo ta chi tiet capability.
            $table->boolean('is_system')->default(true); // Danh dau capability he thong mac dinh.
            $table->boolean('is_active')->default(true); // Danh dau capability con duoc su dung.
            $table->timestamps(); // created_at, updated_at.

            $table->comment('Bang danh muc capability theo module de phan quyen.');
        });

        // Bang pivot gan capability cho chuc vu.
        Schema::create('position_capability_position', function (Blueprint $table) {
            $table->unsignedBigInteger('position_id'); // ID chuc vu duoc gan.
            $table->unsignedBigInteger('capability_id'); // ID capability duoc cap cho chuc vu.
            $table->timestamps(); // created_at, updated_at.

            $table->primary(['position_id', 'capability_id'], 'position_capability_position_primary');
            $table->foreign('position_id')->references('id')->on('positions')->cascadeOnDelete();
            $table->foreign('capability_id')->references('id')->on('position_capabilities')->cascadeOnDelete();
            $table->comment('Bang pivot capability theo tung chuc vu.');
        });

        // Bang override capability theo tung user (allow/deny), ghi de len role mac dinh.
        Schema::create('user_position_capability_overrides', function (Blueprint $table) {
            $table->id(); // Khoa chinh override.
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete(); // User duoc override quyen.
            $table->foreignId('capability_id')->constrained('position_capabilities')->cascadeOnDelete(); // Capability bi override.
            $table->enum('effect', ['allow', 'deny']); // Hieu luc override: cap them hoac cam.
            $table->string('reason', 500)->nullable(); // Ly do override.
            $table->timestamp('expires_at')->nullable()->index(); // Han het hieu luc override.
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete(); // Nguoi tao override.
            $table->timestamps(); // created_at, updated_at.

            $table->unique(['user_id', 'capability_id'], 'user_capability_override_unique');
            $table->comment('Bang override capability theo user so voi quyen theo chuc vu.');
        });

        // Seed danh muc capability tu dinh nghia trong PositionCapability::definitions().
        $now = now();
        $definitions = PositionCapability::definitions();
        $rows = [];

        foreach ($definitions as $code => $meta) {
            $rows[] = [
                'code' => $code,
                'name' => (string) ($meta['name'] ?? $code),
                'module' => (string) ($meta['module'] ?? 'general'),
                'description' => (string) ($meta['description'] ?? ''),
                'is_system' => true,
                'is_active' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        if (!empty($rows)) {
            DB::table('position_capabilities')->insert($rows);
        }

        // Mapping capability code -> id de migrate du lieu JSON capabilities cu trong positions.
        $capabilityIdByCode = DB::table('position_capabilities')
            ->pluck('id', 'code')
            ->toArray();

        $pivotRows = [];
        $positions = DB::table('positions')
            ->select(['id', 'capabilities'])
            ->get();

        foreach ($positions as $position) {
            $rawCapabilities = json_decode((string) ($position->capabilities ?? '[]'), true);
            if (!is_array($rawCapabilities)) {
                continue;
            }

            foreach ($rawCapabilities as $code) {
                if (!is_string($code)) {
                    continue;
                }

                $capabilityId = $capabilityIdByCode[$code] ?? null;
                if (!$capabilityId) {
                    continue;
                }

                $pivotRows[] = [
                    'position_id' => (int) $position->id,
                    'capability_id' => (int) $capabilityId,
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }
        }

        if (!empty($pivotRows)) {
            // insertOrIgnore tranh loi duplicate neu du lieu da ton tai.
            DB::table('position_capability_position')->insertOrIgnore($pivotRows);
        }
    }

    public function down(): void
    {
        // Rollback theo thu tu nguoc phu thuoc khoa ngoai.
        Schema::dropIfExists('user_position_capability_overrides');
        Schema::dropIfExists('position_capability_position');
        Schema::dropIfExists('position_capabilities');
    }
};
