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
        Schema::table('revenues', function (Blueprint $table) {
            $table->foreignId('representative_id')->nullable()->constrained('tree4s')->nullOnDelete();
            $table->decimal('rep_percentage', 5, 2)->default(0);
            $table->decimal('rep_cut_amount', 15, 2)->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('revenues', function (Blueprint $table) {
            $table->dropConstrainedForeignId('representative_id');
            $table->dropColumn(['rep_percentage', 'rep_cut_amount']);
        });
    }
};
