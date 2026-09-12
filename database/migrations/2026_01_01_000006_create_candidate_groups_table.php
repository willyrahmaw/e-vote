<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('candidate_groups', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('election_id')->constrained('elections')->cascadeOnDelete();
            $table->string('name');
            $table->string('number')->nullable();
            $table->string('logo')->nullable();
            $table->string('slogan')->nullable();
            $table->text('vision')->nullable();
            $table->longText('mission')->nullable();
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();

            $table->index(['election_id', 'is_active']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('candidate_groups');
    }
};
