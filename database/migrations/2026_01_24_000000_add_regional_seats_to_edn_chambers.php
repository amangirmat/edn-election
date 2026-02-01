<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('edn_chambers', function (Blueprint $table) {
            // We use text so it can store the JSON from the repeater
            if (!Schema::hasColumn('edn_chambers', 'regional_seats')) {
                $table->text('regional_seats')->nullable()->after('total_seats');
            }
        });
    }

    public function down(): void
    {
        Schema::table('edn_chambers', function (Blueprint $table) {
            if (Schema::hasColumn('edn_chambers', 'regional_seats')) {
                $table->dropColumn('regional_seats');
            }
        });
    }
};