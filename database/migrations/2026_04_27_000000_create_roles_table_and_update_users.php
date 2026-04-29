<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('roles', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->json('permissions')->nullable();
            $table->timestamps();
        });

        $allPermissions = json_encode([
            'product view', 'product create', 'product edit', 'product delete', "master product",
            'order view', 'order edit', 'order delete',
            'user view', 'user create', 'user edit', 'user delete',
            'role view', 'role create', 'role edit', 'role delete'
        ]);

        $adminPermissions = json_encode([
            'product view', 'product create', 'product edit', 'product delete',
            'order view', 'order edit', 'order delete'
        ]);

        $userPermissions = json_encode([
            'product view'
        ]);

        DB::table('roles')->insert([
            ['name' => 'Super Admin', 'permissions' => $allPermissions, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Admin', 'permissions' => $adminPermissions, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'User', 'permissions' => $userPermissions, 'created_at' => now(), 'updated_at' => now()],
        ]);

        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('role_id')->nullable()->after('id')->constrained('roles')->nullOnDelete();
        });

        $superAdminRole = DB::table('roles')->where('name', 'Super Admin')->first();
        $adminRole = DB::table('roles')->where('name', 'Admin')->first();
        $userRole = DB::table('roles')->where('name', 'User')->first();

        DB::table('users')->where('role', 'super_admin')->update(['role_id' => $superAdminRole->id ?? null]);
        DB::table('users')->where('role', 'admin')->update(['role_id' => $adminRole->id ?? null]);
        DB::table('users')->where('role', 'user')->update(['role_id' => $userRole->id ?? null]);

        DB::table('users')->whereNull('role_id')->update(['role_id' => $userRole->id ?? null]);

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('role');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->enum('role', ['super_admin', 'admin', 'user'])->default('user')->after('password');
        });

        $superAdminRole = DB::table('roles')->where('name', 'Super Admin')->first();
        $adminRole = DB::table('roles')->where('name', 'Admin')->first();
        $userRole = DB::table('roles')->where('name', 'User')->first();

        if ($superAdminRole) {
            DB::table('users')->where('role_id', $superAdminRole->id)->update(['role' => 'super_admin']);
        }
        if ($adminRole) {
            DB::table('users')->where('role_id', $adminRole->id)->update(['role' => 'admin']);
        }
        if ($userRole) {
            DB::table('users')->where('role_id', $userRole->id)->update(['role' => 'user']);
        }

        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['role_id']);
            $table->dropColumn('role_id');
        });

        Schema::dropIfExists('roles');
    }
};
