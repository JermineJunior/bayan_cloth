<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('product_units', function (Blueprint $table) {
            if (!Schema::hasColumn('product_units', 'rep_cut')) {
                $table->decimal('rep_cut', 15, 2)->default(0);
            }
        });
    }

    public function down(): void
    {
        Schema::table('product_units', function (Blueprint $table) {
            if (Schema::hasColumn('product_units', 'rep_cut')) {
                $table->dropColumn('rep_cut');
            }
        });
    }
};
