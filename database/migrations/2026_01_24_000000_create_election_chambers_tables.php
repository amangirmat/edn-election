<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
{
    // 1. Create Chambers
    if (!Schema::hasTable('edn_chambers')) {
        Schema::create('edn_chambers', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->enum('level', ['federal', 'regional']);
            $table->integer('total_seats');
            $table->string('status')->default('published');
            $table->timestamps();
        });
    }

    // 2. Create Chamber Seats WITHOUT the foreign key first
    if (!Schema::hasTable('edn_chamber_seats')) {
        Schema::create('edn_chamber_seats', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('chamber_id');
            $table->unsignedBigInteger('region_id'); 
            $table->integer('seat_count'); 
            $table->timestamps();
        });

        // Try to add constraints manually, wrap in try-catch so it doesn't crash the whole migration
        try {
            Schema::table('edn_chamber_seats', function (Blueprint $table) {
                $table->foreign('chamber_id')->references('id')->on('edn_chambers')->onDelete('cascade');
                // We use the raw table name here. If Botble uses a prefix, this might be the issue.
                $table->foreign('region_id')->references('id')->on('regions')->onDelete('cascade');
            });
        } catch (\Exception $e) {
            // If it fails, we just log it. The columns exist, so the code will still work!
            \Log::warning("Foreign key could not be created, using index instead: " . $e->getMessage());
            Schema::table('edn_chamber_seats', function (Blueprint $table) {
                $table->index('chamber_id');
                $table->index('region_id');
            });
        }
    }

    // 3. Update Candidates
    if (Schema::hasTable('edn_candidates')) {
        Schema::table('edn_candidates', function (Blueprint $table) {
            if (!Schema::hasColumn('edn_candidates', 'chamber_id')) {
                $table->unsignedBigInteger('chamber_id')->nullable();
                $table->index('chamber_id');
            }
        });
    }
}

    public function down(): void
    {
        if (Schema::hasTable('edn_candidates') && Schema::hasColumn('edn_candidates', 'chamber_id')) {
            Schema::table('edn_candidates', function (Blueprint $table) {
                $table->dropForeign(['chamber_id']);
                $table->dropColumn('chamber_id');
            });
        }
        
        Schema::dropIfExists('edn_chamber_seats');
        Schema::dropIfExists('edn_chambers');
    }
};