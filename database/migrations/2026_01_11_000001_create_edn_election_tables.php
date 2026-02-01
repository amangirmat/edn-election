<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // 1. Elections Master Table
        Schema::create('edn_elections', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->enum('type', ['national', 'regional'])->default('national');
            $table->date('election_date');
            $table->string('status')->default('upcoming'); // upcoming, voting, counting, final
            $table->timestamps();
        });

        // 2. Regions (e.g., Oromia, Amhara, Sidama)
        Schema::create('edn_regions', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('code')->nullable(); // e.g., OR, AM, AA
            $table->timestamps();
        });

        // 3. Zones (e.g., East Shoa, North Gondar)
        Schema::create('edn_zones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('region_id')->constrained('edn_regions')->onDelete('cascade');
            $table->string('name');
            $table->timestamps();
        });

        // 4. Woredas (The lowest reporting unit)
        Schema::create('edn_woredas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('zone_id')->constrained('edn_zones')->onDelete('cascade');
            $table->string('name');
            $table->integer('total_voters')->default(0); // For turnout calculation
            $table->timestamps();
        });

        // 5. Political Parties
        Schema::create('edn_parties', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('abbreviation', 10);
            $table->string('logo')->nullable();
            $table->string('color', 7)->default('#333333'); // Hex for map coloring
            $table->timestamps();
        });

        // 6. Candidates
        Schema::create('edn_candidates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('party_id')->constrained('edn_parties')->onDelete('cascade');
            $table->string('name');
            $table->string('image')->nullable();
            $table->timestamps();
        });

        // 7. Results Engine (The "Big Data" table)
        Schema::create('edn_results', function (Blueprint $table) {
            $table->id();
            $table->foreignId('election_id')->constrained('edn_elections');
            $table->foreignId('woreda_id')->constrained('edn_woredas');
            $table->foreignId('candidate_id')->constrained('edn_candidates');
            $table->integer('votes_received')->default(0);
            $table->decimal('reporting_progress', 5, 2)->default(0); // 0 to 100%
            $table->timestamps();
            
            // Optimization for fast map queries
            $table->index(['election_id', 'woreda_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('edn_results');
        Schema::dropIfExists('edn_candidates');
        Schema::dropIfExists('edn_parties');
        Schema::dropIfExists('edn_woredas');
        Schema::dropIfExists('edn_zones');
        Schema::dropIfExists('edn_regions');
        Schema::dropIfExists('edn_elections');
    }
};