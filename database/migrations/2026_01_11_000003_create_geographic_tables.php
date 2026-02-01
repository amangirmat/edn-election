<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Regions
        Schema::create('edn_regions', function (Blueprint $table) {
            $table->id();
            $table->string('name', 120);
            $table->string('status', 60)->default('published');
            $table->timestamps();
        });

        // 2. Zones
        Schema::create('edn_zones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('region_id')->constrained('edn_regions')->onDelete('cascade');
            $table->string('name', 120);
            $table->timestamps();
        });

        // 3. Woredas
        Schema::create('edn_woredas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('zone_id')->constrained('edn_zones')->onDelete('cascade');
            $table->string('name', 120);
            $table->integer('total_voters')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('edn_woredas');
        Schema::dropIfExists('edn_zones');
        Schema::dropIfExists('edn_regions');
    }
};