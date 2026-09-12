<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('candidate_entries', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('election_id')->constrained('elections')->cascadeOnDelete();
            $table->foreignUuid('position_id')->constrained('election_positions')->cascadeOnDelete();
            $table->foreignUuid('candidate_id')->constrained('candidates')->cascadeOnDelete();
            $table->string('number')->nullable();
            $table->string('slogan')->nullable();
            $table->text('vision')->nullable();
            $table->longText('mission')->nullable();
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['election_id', 'position_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('candidate_entries');
    }
};
