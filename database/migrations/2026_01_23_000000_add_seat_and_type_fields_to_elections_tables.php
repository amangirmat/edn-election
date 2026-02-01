<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // Update edn_elections table
        Schema::table('edn_elections', function (Blueprint $table) {
            if (!Schema::hasColumn('edn_elections', 'type')) {
                $table->string('type')->default('parliamentary')->after('status');
            }
            if (!Schema::hasColumn('edn_elections', 'chamber')) {
                $table->string('chamber')->nullable()->after('type');
            }
            if (!Schema::hasColumn('edn_elections', 'total_seats')) {
                $table->integer('total_seats')->default(0)->after('chamber');
            }
            if (!Schema::hasColumn('edn_elections', 'majority_mark')) {
                $table->integer('majority_mark')->default(0)->after('total_seats');
            }
        });

        // Update edn_election_results table
        Schema::table('edn_election_results', function (Blueprint $table) {
            if (!Schema::hasColumn('edn_election_results', 'is_winner')) {
                $table->boolean('is_winner')->default(false)->after('votes_count');
            }
        });
    }

    public function down(): void
    {
        // Standard down method
        Schema::table('edn_elections', function (Blueprint $table) {
            $table->dropColumn(['type', 'chamber', 'total_seats', 'majority_mark']);
        });
        Schema::table('edn_election_results', function (Blueprint $table) {
            $table->dropColumn('is_winner');
        });
    }
};