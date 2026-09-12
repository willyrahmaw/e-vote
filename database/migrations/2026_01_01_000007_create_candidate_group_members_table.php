<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('candidate_group_members', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('candidate_group_id')->constrained('candidate_groups')->cascadeOnDelete();
            $table->foreignUuid('candidate_id')->constrained('candidates')->cascadeOnDelete();
            $table->foreignUuid('position_id')->nullable()->constrained('election_positions')->nullOnDelete();
            $table->integer('sort_order')->default(0);
            $table->timestamps();

            $table->index(['candidate_group_id', 'sort_order']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('candidate_group_members');
    }
};
