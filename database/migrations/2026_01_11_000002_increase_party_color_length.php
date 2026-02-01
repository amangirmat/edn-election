<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('edn_parties', function (Blueprint $table) {
            // Changing to 50 characters to safely support HEX, RGB, or RGBA strings
            $table->string('color', 50)->change();
        });
    }

    public function down(): void
    {
        Schema::table('edn_parties', function (Blueprint $table) {
            $table->string('color', 7)->change();
        });
    }
};