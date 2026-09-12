<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('settings', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('key')->unique();
            $table->longText('value')->nullable();
            $table->string('group')->default('general');
            $table->string('type')->default('string'); // string, text, boolean, integer, json
            $table->timestamps();
        });

        // Insert default initial settings
        $defaults = [
            [
                'id' => (string) \Illuminate\Support\Str::uuid(),
                'key' => 'app_name',
                'value' => 'E-Voting Terpadu',
                'group' => 'branding',
                'type' => 'string',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => (string) \Illuminate\Support\Str::uuid(),
                'key' => 'institution_name',
                'value' => 'Komisi Pemilihan Umum Mahasiswa / Institusi',
                'group' => 'branding',
                'type' => 'string',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => (string) \Illuminate\Support\Str::uuid(),
                'key' => 'app_tagline',
                'value' => 'Platform Pemilihan Fleksibel, Rahasia, dan Terpercaya',
                'group' => 'branding',
                'type' => 'string',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => (string) \Illuminate\Support\Str::uuid(),
                'key' => 'app_description',
                'value' => 'Sistem e-voting modern dengan enkripsi suara independen, zero-knowledge verification, dan double-vote protection.',
                'group' => 'branding',
                'type' => 'text',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => (string) \Illuminate\Support\Str::uuid(),
                'key' => 'contact_email',
                'value' => 'support@e-vote.local',
                'group' => 'contact',
                'type' => 'string',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => (string) \Illuminate\Support\Str::uuid(),
                'key' => 'contact_phone',
                'value' => '+62 812-3456-7890',
                'group' => 'contact',
                'type' => 'string',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => (string) \Illuminate\Support\Str::uuid(),
                'key' => 'institution_address',
                'value' => 'Gedung Pusat Administrasi & Kemahasiswaan, Lantai 2',
                'group' => 'contact',
                'type' => 'text',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => (string) \Illuminate\Support\Str::uuid(),
                'key' => 'footer_copyright',
                'value' => 'Sistem E-Voting Terdesentralisasi & Anonim.',
                'group' => 'branding',
                'type' => 'string',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => (string) \Illuminate\Support\Str::uuid(),
                'key' => 'enable_public_monitor',
                'value' => '1',
                'group' => 'features',
                'type' => 'boolean',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => (string) \Illuminate\Support\Str::uuid(),
                'key' => 'enable_ballot_verification',
                'value' => '1',
                'group' => 'features',
                'type' => 'boolean',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => (string) \Illuminate\Support\Str::uuid(),
                'key' => 'announcement_banner',
                'value' => '',
                'group' => 'features',
                'type' => 'text',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('settings')->insert($defaults);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};
