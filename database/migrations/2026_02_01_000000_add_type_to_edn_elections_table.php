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
        if (Schema::hasTable('edn_elections') && !Schema::hasColumn('edn_elections', 'type')) {
            Schema::table('edn_elections', function (Blueprint $table) {
                // Adding the 'type' column as a string, placed after the 'name' column
                $table->string('type', 100)->nullable()->after('name');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('edn_elections', 'type')) {
            Schema::table('edn_elections', function (Blueprint $table) {
                $table->dropColumn('type');
            });
        }
    }
};