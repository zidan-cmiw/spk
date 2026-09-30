<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Periods
        Schema::create('periods', function (Blueprint $table) {
            $table->id();
            $table->string('nama', 100); // e.g. "Tahun Ajaran 2024/2025"
            $table->enum('semester', ['Ganjil', 'Genap'])->default('Ganjil');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // 2. Criteria
        Schema::create('criteria', function (Blueprint $table) {
            $table->id();
            $table->string('kode', 10)->unique(); // K1, K2, K3, K4, K5
            $table->string('nama', 100);
            $table->enum('tipe', ['benefit', 'cost'])->default('benefit');
            $table->decimal('bobot_default', 6, 4)->default(0.2000); // Jurnal default (40%, 20%, etc)
            $table->integer('urutan')->default(1);
            $table->text('deskripsi')->nullable();
            $table->timestamps();
        });

        // 3. Parameters (Sub-kriteria / Konversi rentang nilai ke skor 20-100)
        Schema::create('parameters', function (Blueprint $table) {
            $table->id();
            $table->foreignId('criteria_id')->constrained('criteria')->onDelete('cascade');
            $table->string('label', 150); // e.g. "Nilai 75 - 79", "Tanpa Keterangan = 0"
            $table->decimal('batas_min', 8, 2)->nullable();
            $table->decimal('batas_max', 8, 2)->nullable();
            $table->decimal('skor', 8, 2); // Skor parameter misal 20, 30, 40, 50, 60, 70, 80, 90, 100
            $table->timestamps();
        });

        // 4. Students (Alternatif)
        Schema::create('students', function (Blueprint $table) {
            $table->id();
            $table->string('kode', 10)->unique(); // A1, A2, A3, etc.
            $table->string('nis', 30)->nullable();
            $table->string('nama', 150);
            $table->string('kelas', 50)->default('XII RPL 1');
            $table->string('jurusan', 100)->default('Rekayasa Perangkat Lunak');
            $table->enum('jenis_kelamin', ['L', 'P'])->default('L');
            $table->timestamps();
        });

        // 5. AHP Sessions
        Schema::create('ahp_sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('period_id')->constrained('periods')->onDelete('cascade');
            $table->string('nama_sesi', 150);
            $table->text('deskripsi')->nullable();
            $table->decimal('lambda_max', 8, 4)->nullable();
            $table->decimal('ci', 8, 4)->nullable();
            $table->decimal('cr', 8, 4)->nullable();
            $table->boolean('is_consistent')->default(false);
            $table->boolean('is_active')->default(false);
            $table->timestamps();
        });

        // 6. AHP Comparisons (Matriks Berpasangan Saaty)
        Schema::create('ahp_comparisons', function (Blueprint $table) {
            $table->id();
            $table->foreignId('session_id')->constrained('ahp_sessions')->onDelete('cascade');
            $table->foreignId('criteria_a_id')->constrained('criteria')->onDelete('cascade');
            $table->foreignId('criteria_b_id')->constrained('criteria')->onDelete('cascade');
            $table->decimal('nilai', 8, 4); // Skala 1-9 atau 1/9-1
            $table->timestamps();

            $table->unique(['session_id', 'criteria_a_id', 'criteria_b_id'], 'session_crit_ab_unique');
        });

        // 7. Criteria Weights (Bobot hasil AHP per sesi)
        Schema::create('criteria_weights', function (Blueprint $table) {
            $table->id();
            $table->foreignId('session_id')->constrained('ahp_sessions')->onDelete('cascade');
            $table->foreignId('criteria_id')->constrained('criteria')->onDelete('cascade');
            $table->decimal('bobot', 8, 6);
            $table->timestamps();

            $table->unique(['session_id', 'criteria_id'], 'session_crit_unique');
        });

        // 8. Assessments (Penilaian Nilai Siswa)
        Schema::create('assessments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('students')->onDelete('cascade');
            $table->foreignId('criteria_id')->constrained('criteria')->onDelete('cascade');
            $table->foreignId('period_id')->constrained('periods')->onDelete('cascade');
            $table->string('nilai_asli', 100)->nullable();
            $table->decimal('skor_parameter', 8, 2); // Nilai skor parameter (Tabel 4)
            $table->timestamps();

            $table->unique(['student_id', 'criteria_id', 'period_id'], 'student_crit_period_unique');
        });

        // 9. Calculation Results (Hasil Perhitungan Nilai Akhir & Ranking SMART)
        Schema::create('calculation_results', function (Blueprint $table) {
            $table->id();
            $table->foreignId('period_id')->constrained('periods')->onDelete('cascade');
            $table->foreignId('session_id')->nullable()->constrained('ahp_sessions')->onDelete('set null');
            $table->foreignId('student_id')->constrained('students')->onDelete('cascade');
            $table->string('mode_bobot', 20)->default('ahp'); // 'ahp' or 'jurnal'
            $table->json('utility_scores')->nullable(); // JSON data skor utility tiap kriteria
            $table->decimal('nilai_akhir', 8, 4);
            $table->integer('ranking')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('calculation_results');
        Schema::dropIfExists('assessments');
        Schema::dropIfExists('criteria_weights');
        Schema::dropIfExists('ahp_comparisons');
        Schema::dropIfExists('ahp_sessions');
        Schema::dropIfExists('students');
        Schema::dropIfExists('parameters');
        Schema::dropIfExists('criteria');
        Schema::dropIfExists('periods');
    }
};
