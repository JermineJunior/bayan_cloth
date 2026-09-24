<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('tree4s', function (Blueprint $table) {
            $table->foreignId('representative_id')->nullable()->constrained('tree4s')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tree4s', function (Blueprint $table) {
            $table->dropColumn('representative_id');
        });
    }
};
