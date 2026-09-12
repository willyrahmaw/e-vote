<?php

namespace Database\Seeders;

use App\Actions\Voting\SubmitBallotAction;
use App\DTOs\Voting\BallotChoiceData;
use App\DTOs\Voting\SubmitBallotData;
use App\Enums\ElectionStatus;
use App\Enums\ResultVisibility;
use App\Enums\UserRole;
use App\Models\Candidate;
use App\Models\CandidateEntry;
use App\Models\CandidateGroup;
use App\Models\CandidateGroupMember;
use App\Models\Election;
use App\Models\ElectionPosition;
use App\Models\ElectionVoter;
use App\Models\Organization;
use App\Models\User;
use App\Services\Voting\BallotValidationService;
use App\Services\Voting\VotingService;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Create Organization
        $org = Organization::create([
            'name' => 'Universitas Indonesia Mandiri',
            'slug' => 'universitas-indonesia-mandiri',
            'description' => 'Organisasi Kampus Terpadu untuk Pemilihan Raya Mahasiswa.',
            'address' => 'Jl. Pendidikan No. 45, Jakarta Pusat',
            'is_active' => true,
        ]);

        // 2. Create Core Admin & Voter
        $admin = User::create([
            'organization_id' => $org->id,
            'name' => 'Administrator E-Voting',
            'email' => 'admin@example.com',
            'identifier' => 'ADM-001',
            'password' => Hash::make('password'),
            'role' => UserRole::Admin,
            'is_active' => true,
        ]);

        $demoVoter = User::create([
            'organization_id' => $org->id,
            'name' => 'Budi Pemilih',
            'email' => 'voter@example.com',
            'identifier' => 'MHS-2026001',
            'password' => Hash::make('password'),
            'role' => UserRole::Voter,
            'is_active' => true,
        ]);

        // Additional Voters
        $voters = [$demoVoter];
        for ($i = 2; $i <= 25; $i++) {
            $voters[] = User::create([
                'organization_id' => $org->id,
                'name' => fake()->name(),
                'email' => "voter{$i}@example.com",
                'identifier' => 'MHS-2026' . str_pad((string) $i, 3, '0', STR_PAD_LEFT),
                'password' => Hash::make('password'),
                'role' => UserRole::Voter,
                'is_active' => true,
            ]);
        }

        // 3. Create Election 1 (Paslon - Pemilihan BEM 2026)
        $electionBem = Election::create([
            'organization_id' => $org->id,
            'created_by' => $admin->id,
            'name' => 'Pemilihan Raya Presiden & Wakil Presiden Mahasiswa 2026/2027',
            'slug' => 'pemira-presma-wapresma-2026',
            'description' => 'Pemilihan Ketua dan Wakil Ketua Badan Eksekutif Mahasiswa Universitas Indonesia Mandiri Periode 2026-2027.',
            'instructions' => 'Pilih salah satu pasangan calon secara bijak. Keputusan tidak dapat diubah setelah Anda mengirimkan surat suara.',
            'start_at' => now()->subHours(4),
            'end_at' => now()->addDays(2),
            'status' => ElectionStatus::Active,
            'result_visibility' => ResultVisibility::Live,
            'is_public' => true,
            'is_live_result_enabled' => true,
            'allow_abstain' => true,
        ]);

        // Candidates for Election 1 (Paslon)
        $c1 = Candidate::create([
            'organization_id' => $org->id,
            'name' => 'Ahmad Pratama',
            'identifier' => 'CAND-01A',
            'bio' => 'Mahasiswa Teknik Informatika Semester 6 - Ketua Himpunan & Inovator Digital',
            'photo' => 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=400&auto=format&fit=crop&q=80',
        ]);
        $c2 = Candidate::create([
            'organization_id' => $org->id,
            'name' => 'Budi Santoso',
            'identifier' => 'CAND-01B',
            'bio' => 'Mahasiswa Ilmu Komunikasi Semester 6 - Aktivis Advokasi & Debater Nasional',
            'photo' => 'https://images.unsplash.com/photo-1500648767791-00dcc994a43e?w=400&auto=format&fit=crop&q=80',
        ]);
        $c3 = Candidate::create([
            'organization_id' => $org->id,
            'name' => 'Citra Lestari',
            'identifier' => 'CAND-02A',
            'bio' => 'Mahasiswa Manajemen Bisnis Semester 6 - Peraih Mahasiswa Berprestasi 2025',
            'photo' => 'https://images.unsplash.com/photo-1573496359142-b8d87734a5a2?w=400&auto=format&fit=crop&q=80',
        ]);
        $c4 = Candidate::create([
            'organization_id' => $org->id,
            'name' => 'Doni Setiawan',
            'identifier' => 'CAND-02B',
            'bio' => 'Mahasiswa Hukum Semester 6 - Koordinator Forum Kajian Keadilan Sosial',
            'photo' => 'https://images.unsplash.com/photo-1519085360753-af0119f7cbe7?w=400&auto=format&fit=crop&q=80',
        ]);
        $c5 = Candidate::create([
            'organization_id' => $org->id,
            'name' => 'Eko Prasetyo',
            'identifier' => 'CAND-03A',
            'bio' => 'Mahasiswa Kedokteran Semester 6 - Relawan Tim Medis Bencana Nasional',
            'photo' => 'https://images.unsplash.com/photo-1506794778202-cad84cf45f1d?w=400&auto=format&fit=crop&q=80',
        ]);
        $c6 = Candidate::create([
            'organization_id' => $org->id,
            'name' => 'Fitri Handayani',
            'identifier' => 'CAND-03B',
            'bio' => 'Mahasiswa Psikologi Semester 6 - Penggagas Kampanye Konseling Mahasiswa',
            'photo' => 'https://images.unsplash.com/photo-1580489944761-15a19d654956?w=400&auto=format&fit=crop&q=80',
        ]);

        // Groups / Paslon
        $group1 = CandidateGroup::create([
            'election_id' => $electionBem->id,
            'name' => 'Ahmad & Budi',
            'number' => '01',
            'slogan' => 'Bersama Mengabdi, Membawa Solusi Transformatif',
            'vision' => 'Mewujudkan BEM yang inklusif, adaptif, dan berintegritas tinggi dalam era digital.',
            'mission' => "1. Meningkatkan advokasi kesejahteraan mahasiswa.\n2. Mengembangkan ekosistem riset & inovasi kampus.\n3. Memperkuat kolaborasi multidisipliner dan kebangsaan.",
            'is_active' => true,
        ]);
        CandidateGroupMember::create(['candidate_group_id' => $group1->id, 'candidate_id' => $c1->id, 'sort_order' => 1]);
        CandidateGroupMember::create(['candidate_group_id' => $group1->id, 'candidate_id' => $c2->id, 'sort_order' => 2]);

        $group2 = CandidateGroup::create([
            'election_id' => $electionBem->id,
            'name' => 'Citra & Doni',
            'number' => '02',
            'slogan' => 'Harmoni, Sinergi, Aksi Nyata untuk Kampus Unggul',
            'vision' => 'Menjadikan universitas sebagai episentrum pergerakan pemuda yang berwawasan global.',
            'mission' => "1. Optimalisasi sarana prasarana mahasiswa.\n2. Pelatihan kepemimpinan internasional.\n3. Kemitraan strategis dengan dunia industri.",
            'is_active' => true,
        ]);
        CandidateGroupMember::create(['candidate_group_id' => $group2->id, 'candidate_id' => $c3->id, 'sort_order' => 1]);
        CandidateGroupMember::create(['candidate_group_id' => $group2->id, 'candidate_id' => $c4->id, 'sort_order' => 2]);

        $group3 = CandidateGroup::create([
            'election_id' => $electionBem->id,
            'name' => 'Eko & Fitri',
            'number' => '03',
            'slogan' => 'Transparan, Progresif, Peduli Sesama',
            'vision' => 'Organisasi kemahasiswaan yang berbasis transparansi anggaran dan keberpihakan sosial.',
            'mission' => "1. Dashboard transparansi kas terbuka.\n2. Gerakan kampus hijau dan zero waste.\n3. Ruang konseling kesehatan mental gratis.",
            'is_active' => true,
        ]);
        CandidateGroupMember::create(['candidate_group_id' => $group3->id, 'candidate_id' => $c5->id, 'sort_order' => 1]);
        CandidateGroupMember::create(['candidate_group_id' => $group3->id, 'candidate_id' => $c6->id, 'sort_order' => 2]);

        // Register Voters for Election 1
        foreach ($voters as $voterUser) {
            ElectionVoter::create([
                'election_id' => $electionBem->id,
                'user_id' => $voterUser->id,
                'is_eligible' => true,
                'has_voted' => false,
            ]);
        }

        // Simulate ballots for some voters to test live stats & charts
        $submitAction = new SubmitBallotAction(new VotingService(), new BallotValidationService());
        $groups = [$group1, $group2, $group3];

        // Let 15 voters cast votes (voter 2 to 16), leaving demoVoter unvoted
        for ($i = 1; $i <= 15; $i++) {
            $userToVote = $voters[$i];
            $selectedGroup = $groups[$i % 3];

            $submitAction->execute(
                user: $userToVote,
                election: $electionBem,
                data: SubmitBallotData::fromArray([
                    'choices' => [
                        ['candidate_group_id' => $selectedGroup->id],
                    ],
                ]),
                ipAddress: '127.0.0.1',
                userAgent: 'Mozilla/5.0 Seeder Simulator'
            );
        }

        // 4. Create Election 2 (Multi-Position Election)
        $electionDpm = Election::create([
            'organization_id' => $org->id,
            'created_by' => $admin->id,
            'name' => 'Pemilihan Dewan Perwakilan Mahasiswa (DPM) 2026',
            'slug' => 'pemilihan-dpm-2026',
            'description' => 'Pemilihan Perwakilan Fraksi dan Komisi Legislatif Kampus.',
            'instructions' => 'Pilih 1 kandidat pada masing-masing posisi jabatan.',
            'start_at' => now()->subHour(),
            'end_at' => now()->addDays(3),
            'status' => ElectionStatus::Active,
            'result_visibility' => ResultVisibility::AfterVote,
            'is_public' => true,
            'is_live_result_enabled' => true,
            'allow_abstain' => true,
        ]);

        $posKetua = ElectionPosition::create([
            'election_id' => $electionDpm->id,
            'name' => 'Ketua Dewan Perwakilan',
            'description' => 'Pimpinan sidang dan perwakilan umum legislatif mahasiswa.',
            'min_choices' => 1,
            'max_choices' => 1,
            'is_required' => true,
            'sort_order' => 1,
        ]);

        $posSekretaris = ElectionPosition::create([
            'election_id' => $electionDpm->id,
            'name' => 'Sekretaris Jenderal DPM',
            'description' => 'Penanggung jawab administrasi dan risalah sidang.',
            'min_choices' => 1,
            'max_choices' => 1,
            'is_required' => true,
            'sort_order' => 2,
        ]);

        $c7 = Candidate::create([
            'organization_id' => $org->id,
            'name' => 'Rian Hidayat',
            'identifier' => 'CAND-04',
            'bio' => 'Delegasi Fakultas Teknik - Pegiat Aspirasi Sarana Kampus',
            'photo' => 'https://images.unsplash.com/photo-1492562080023-ab3db95bfbce?w=400&auto=format&fit=crop&q=80',
        ]);
        $c8 = Candidate::create([
            'organization_id' => $org->id,
            'name' => 'Siti Nurhaliza',
            'identifier' => 'CAND-05',
            'bio' => 'Delegasi Fakultas Ekonomi - Pelopor Transparansi Anggaran',
            'photo' => 'https://images.unsplash.com/photo-1544005313-94ddf0286df2?w=400&auto=format&fit=crop&q=80',
        ]);
        $c9 = Candidate::create([
            'organization_id' => $org->id,
            'name' => 'Kevin Sanjaya',
            'identifier' => 'CAND-06',
            'bio' => 'Delegasi Fakultas Hukum - Peneliti Kebijakan Publik',
            'photo' => 'https://images.unsplash.com/photo-1522075469751-3a6694fb2f61?w=400&auto=format&fit=crop&q=80',
        ]);
        $c10 = Candidate::create([
            'organization_id' => $org->id,
            'name' => 'Maya Anggraini',
            'identifier' => 'CAND-07',
            'bio' => 'Delegasi Fakultas Kedokteran - Koordinator Layanan Tanggap Kesehatan',
            'photo' => 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?w=400&auto=format&fit=crop&q=80',
        ]);

        CandidateEntry::create([
            'election_id' => $electionDpm->id,
            'position_id' => $posKetua->id,
            'candidate_id' => $c7->id,
            'number' => '01',
            'slogan' => 'Legislasi yang Berpihak pada Mahasiswa',
            'vision' => 'Dewan perwakilan yang aspiratif dan mengawasi dengan kritis objektif.',
            'mission' => 'Mengawal transparansi kampus dan UKT berkeadilan.',
            'is_active' => true,
        ]);

        CandidateEntry::create([
            'election_id' => $electionDpm->id,
            'position_id' => $posKetua->id,
            'candidate_id' => $c8->id,
            'number' => '02',
            'slogan' => 'Inovasi Tata Kelola Sidang',
            'vision' => 'Parlemen mahasiswa modern dan partisipatif.',
            'mission' => 'Membuka akses live streaming rapat parlemen ke publik.',
            'is_active' => true,
        ]);

        CandidateEntry::create([
            'election_id' => $electionDpm->id,
            'position_id' => $posSekretaris->id,
            'candidate_id' => $c9->id,
            'number' => '01',
            'slogan' => 'Administrasi Cepat dan Rapi',
            'vision' => 'Digitalisasi berkas dan risalah peraturan kemahasiswaan.',
            'mission' => 'Dokumentasi arsip satu pintu berbasis cloud.',
            'is_active' => true,
        ]);

        CandidateEntry::create([
            'election_id' => $electionDpm->id,
            'position_id' => $posSekretaris->id,
            'candidate_id' => $c10->id,
            'number' => '02',
            'slogan' => 'Kolaborasi Terbuka',
            'vision' => 'Sekretariat responsif untuk aspirasi fakultas.',
            'mission' => 'Kanal laporan aspirasi online 24 jam.',
            'is_active' => true,
        ]);

        // Register Voters for Election 2
        foreach ($voters as $voterUser) {
            ElectionVoter::create([
                'election_id' => $electionDpm->id,
                'user_id' => $voterUser->id,
                'is_eligible' => true,
                'has_voted' => false,
            ]);
        }
    }
}
