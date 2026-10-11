<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Flag the base currency (id 1, hidden from the currency management
     * list) so it can be preselected in every currency dropdown.
     */
    public function up(): void
    {
        Schema::table('curenceys', function ($table) {
            $table->boolean('is_base')->default(false);
        });

        DB::table('curenceys')->where('id', 1)->update(['is_base' => true]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('curenceys', function ($table) {
            $table->dropColumn('is_base');
        });
    }
};
