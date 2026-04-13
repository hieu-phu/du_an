<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $teams = config('permission.teams');
        $tableNames = config('permission.table_names');
        $columnNames = config('permission.column_names');
        $pivotRole = $columnNames['role_pivot_key'] ?? 'role_id';
        $pivotPermission = $columnNames['permission_pivot_key'] ?? 'permission_id';

        throw_if(empty($tableNames), Exception::class, 'Error: config/permission.php not loaded. Run [php artisan config:clear] and try again.');
        throw_if($teams && empty($columnNames['team_foreign_key'] ?? null), Exception::class, 'Error: team_foreign_key on config/permission.php not loaded. Run [php artisan config:clear] and try again.');

        // Bang dinh nghia quyen he thong.
        Schema::create($tableNames['permissions'], static function (Blueprint $table) {
            $table->bigIncrements('id'); // Khoa chinh cua quyen.
            $table->string('name'); // Ma/ten quyen, vi du: users.create.
            $table->string('guard_name'); // Guard ap dung, vi du: web, api.
            $table->text('description')->comment('Mo ta muc dich cua quyen.');
            $table->timestamps(); // created_at, updated_at.
            $table->unique(['name', 'guard_name']); // Moi quyen duy nhat trong tung guard.
            $table->comment('Bang luu danh sach quyen trong he thong.');
        });

        // Bang dinh nghia vai tro.
        Schema::create($tableNames['roles'], static function (Blueprint $table) use ($teams, $columnNames) {
            $table->bigIncrements('id'); // Khoa chinh cua vai tro.
            if ($teams || config('permission.testing')) {
                $table->unsignedBigInteger($columnNames['team_foreign_key'])->nullable(); // Team so huu vai tro neu bat team mode.
                $table->index($columnNames['team_foreign_key'], 'roles_team_foreign_key_index');
            }
            $table->string('name'); // Ten vai tro, vi du: admin, hr_manager.
            $table->string('guard_name'); // Guard ap dung cho vai tro.
            $table->text('description')->comment('Mo ta vai tro va pham vi su dung.');
            $table->timestamps(); // created_at, updated_at.
            if ($teams || config('permission.testing')) {
                $table->unique([$columnNames['team_foreign_key'], 'name', 'guard_name']); // Khong trung vai tro trong cung team + guard.
            } else {
                $table->unique(['name', 'guard_name']); // Khong trung vai tro trong cung guard.
            }
            $table->comment('Bang luu vai tro de phan quyen theo nhom.');
        });

        // Bang gan truc tiep quyen cho model.
        Schema::create($tableNames['model_has_permissions'], static function (Blueprint $table) use ($tableNames, $columnNames, $pivotPermission, $teams) {
            $table->unsignedBigInteger($pivotPermission); // ID quyen duoc gan.
            $table->string('model_type'); // Lop model nhan quyen, vi du App\\Models\\User.
            $table->unsignedBigInteger($columnNames['model_morph_key']); // ID cua model nhan quyen.
            $table->index([$columnNames['model_morph_key'], 'model_type'], 'model_has_permissions_model_id_model_type_index');

            $table->foreign($pivotPermission)
                ->references('id')
                ->on($tableNames['permissions'])
                ->onDelete('cascade');

            if ($teams) {
                $table->unsignedBigInteger($columnNames['team_foreign_key']); // Team scope cua quyen duoc gan.
                $table->index($columnNames['team_foreign_key'], 'model_has_permissions_team_foreign_key_index');

                $table->primary(
                    [$columnNames['team_foreign_key'], $pivotPermission, $columnNames['model_morph_key'], 'model_type'],
                    'model_has_permissions_permission_model_type_primary'
                );
            } else {
                $table->primary(
                    [$pivotPermission, $columnNames['model_morph_key'], 'model_type'],
                    'model_has_permissions_permission_model_type_primary'
                );
            }
            $table->comment('Bang pivot gan quyen truc tiep cho tung model.');
        });

        // Bang gan vai tro cho model.
        Schema::create($tableNames['model_has_roles'], static function (Blueprint $table) use ($tableNames, $columnNames, $pivotRole, $teams) {
            $table->unsignedBigInteger($pivotRole); // ID vai tro duoc gan.
            $table->string('model_type'); // Lop model nhan vai tro.
            $table->unsignedBigInteger($columnNames['model_morph_key']); // ID cua model nhan vai tro.
            $table->index([$columnNames['model_morph_key'], 'model_type'], 'model_has_roles_model_id_model_type_index');

            $table->foreign($pivotRole)
                ->references('id')
                ->on($tableNames['roles'])
                ->onDelete('cascade');

            if ($teams) {
                $table->unsignedBigInteger($columnNames['team_foreign_key']); // Team scope cua vai tro duoc gan.
                $table->index($columnNames['team_foreign_key'], 'model_has_roles_team_foreign_key_index');

                $table->primary(
                    [$columnNames['team_foreign_key'], $pivotRole, $columnNames['model_morph_key'], 'model_type'],
                    'model_has_roles_role_model_type_primary'
                );
            } else {
                $table->primary(
                    [$pivotRole, $columnNames['model_morph_key'], 'model_type'],
                    'model_has_roles_role_model_type_primary'
                );
            }
            $table->comment('Bang pivot gan vai tro cho tung model.');
        });

        // Bang xac dinh vai tro nao so huu nhung quyen nao.
        Schema::create($tableNames['role_has_permissions'], static function (Blueprint $table) use ($tableNames, $pivotRole, $pivotPermission) {
            $table->unsignedBigInteger($pivotPermission); // ID quyen.
            $table->unsignedBigInteger($pivotRole); // ID vai tro.

            $table->foreign($pivotPermission)
                ->references('id')
                ->on($tableNames['permissions'])
                ->onDelete('cascade');

            $table->foreign($pivotRole)
                ->references('id')
                ->on($tableNames['roles'])
                ->onDelete('cascade');

            $table->primary([$pivotPermission, $pivotRole], 'role_has_permissions_permission_id_role_id_primary'); // Moi cap quyen-vai tro chi ton tai mot lan.
            $table->comment('Bang pivot xac dinh quyen thuoc ve vai tro nao.');
        });

        app('cache')
            ->store(config('permission.cache.store') != 'default' ? config('permission.cache.store') : null)
            ->forget(config('permission.cache.key'));
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $tableNames = config('permission.table_names');

        throw_if(empty($tableNames), Exception::class, 'Error: config/permission.php not found and defaults could not be merged. Please publish the package configuration before proceeding, or drop the tables manually.');

        Schema::drop($tableNames['role_has_permissions']);
        Schema::drop($tableNames['model_has_roles']);
        Schema::drop($tableNames['model_has_permissions']);
        Schema::drop($tableNames['roles']);
        Schema::drop($tableNames['permissions']);
    }
};
