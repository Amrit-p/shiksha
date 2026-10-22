<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

return new class extends Migration
{
    /** Permissions a Manager gets. Users with `admin` keep full access regardless. */
    private const MANAGER_PERMISSIONS = ['manage_orders', 'manage_enquiries', 'manage_sales'];

    public function up(): void
    {
        if (! Schema::hasColumn('users', 'is_active')) {
            Schema::table('users', function (Blueprint $table) {
                $table->boolean('is_active')->default(true)->after('is_admin');
            });
        }

        foreach (array_merge(self::MANAGER_PERMISSIONS, ['admin', 'frontend_shop']) as $name) {
            Permission::firstOrCreate(['name' => $name, 'guard_name' => 'web']);
        }

        Role::firstOrCreate(['name' => 'Manager', 'guard_name' => 'web'])
            ->syncPermissions(self::MANAGER_PERMISSIONS);

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }

    public function down(): void
    {
        Role::where('name', 'Manager')->where('guard_name', 'web')->delete();
        Permission::whereIn('name', ['manage_orders', 'manage_enquiries'])->where('guard_name', 'web')->delete();
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        if (Schema::hasColumn('users', 'is_active')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('is_active');
            });
        }
    }
};
