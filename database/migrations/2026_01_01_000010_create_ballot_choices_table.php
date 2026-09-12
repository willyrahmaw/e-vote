<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ballot_choices', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('ballot_id')->constrained('ballots')->cascadeOnDelete();
            $table->foreignUuid('position_id')->nullable()->constrained('election_positions')->nullOnDelete();
            $table->foreignUuid('candidate_entry_id')->nullable()->constrained('candidate_entries')->nullOnDelete();
            $table->foreignUuid('candidate_group_id')->nullable()->constrained('candidate_groups')->nullOnDelete();
            $table->uuid('option_id')->nullable();
            $table->boolean('is_abstain')->default(false);
            $table->timestamps();

            $table->index(['ballot_id', 'position_id']);
            $table->index(['candidate_entry_id']);
            $table->index(['candidate_group_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ballot_choices');
    }
};
