<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('election_voters', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('election_id')->constrained('elections')->cascadeOnDelete();
            $table->foreignUuid('user_id')->constrained('users')->cascadeOnDelete();
            $table->boolean('is_eligible')->default(true);
            $table->boolean('has_voted')->default(false);
            $table->dateTime('voted_at')->nullable();
            $table->timestamps();

            $table->unique(['election_id', 'user_id']);
            $table->index(['election_id', 'has_voted', 'is_eligible']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('election_voters');
    }
};
