<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ballots', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('election_id')->constrained('elections')->cascadeOnDelete();
            $table->string('token_hash')->unique();
            $table->dateTime('submitted_at')->index();
            $table->timestamps();

            $table->index(['election_id', 'submitted_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ballots');
    }
};
