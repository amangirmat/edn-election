<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('edn_candidate_woreda', function (Blueprint $table) {
            $table->id();
            $table->foreignId('election_id')->constrained('edn_elections')->onDelete('cascade');
            $table->foreignId('candidate_id')->constrained('edn_candidates')->onDelete('cascade');
            $table->foreignId('woreda_id')->constrained('edn_woredas')->onDelete('cascade');
            $table->timestamps();
            
            // Ensures a candidate isn't assigned to the same woreda twice in one election
            $table->unique(['election_id', 'candidate_id', 'woreda_id'], 'unique_assignment');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('edn_candidate_woreda');
    }
};