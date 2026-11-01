<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('branches', function (Blueprint $table) {
            $table->boolean('enable_delivery_cost')->default(false);
            $table->boolean('enable_rep_cut')->default(false);
        });

        DB::table('branches')->update([
            'enable_delivery_cost' => true,
            'enable_rep_cut' => true,
        ]);
    }

    public function down(): void
    {
        Schema::table('branches', function (Blueprint $table) {
            $table->dropColumn(['enable_delivery_cost', 'enable_rep_cut']);
        });
    }
};
