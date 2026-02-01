<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('edn_election_results', function (Blueprint $table) {
            $table->id();
            // Link to the specific Election event
            $table->foreignId('election_id')->constrained('edn_elections')->onDelete('cascade');
            // Link to where the votes happened
            $table->foreignId('woreda_id')->constrained('edn_woredas')->onDelete('cascade');
            // Link to who got the votes
            $table->foreignId('candidate_id')->constrained('edn_candidates')->onDelete('cascade');
            
            $table->integer('votes_count')->default(0);
            $table->timestamps();

            // Prevent duplicate entries: One candidate should only have one result per Woreda per Election
            $table->unique(['election_id', 'woreda_id', 'candidate_id'], 'unique_result_index');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('edn_election_results');
    }
};