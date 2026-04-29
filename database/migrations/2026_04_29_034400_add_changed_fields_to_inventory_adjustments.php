<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('inventory_adjustments', function (Blueprint $table) {
            // Menyimpan field apa saja yang berubah, contoh: "nama", "deskripsi", "nama,deskripsi"
            $table->string('changed_fields')->nullable()->after('note');
        });
    }

    public function down(): void
    {
        Schema::table('inventory_adjustments', function (Blueprint $table) {
            $table->dropColumn('changed_fields');
        });
    }
};
