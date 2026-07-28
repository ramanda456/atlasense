<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Tabel Negara
        Schema::create('countries', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('official_name')->nullable();
            $table->string('code', 2)->unique();
            $table->string('cca3', 3)->nullable()->unique();
            $table->string('region')->nullable();
            $table->string('subregion')->nullable();
            $table->string('capital')->nullable();
            $table->string('currency_code', 5)->nullable();
            $table->string('currency_name')->nullable();
            $table->string('language')->nullable();
            $table->string('flag_url')->nullable();
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->unsignedBigInteger('population')->nullable();
            $table->timestamps();
        });

        // 2. Tabel Ekonomi Negara
        Schema::create('country_economics', function (Blueprint $table) {
            $table->id();
            $table->foreignId('country_id')->constrained('countries')->cascadeOnDelete();
            $table->year('year');
            $table->decimal('gdp', 20, 2)->nullable();
            $table->decimal('inflation', 10, 4)->nullable();
            $table->decimal('exports', 20, 2)->nullable();
            $table->decimal('imports', 20, 2)->nullable();
            $table->unsignedBigInteger('population')->nullable();
            $table->timestamps();
            $table->unique(['country_id', 'year']);
        });

        // 3. Tabel Data Cuaca
        Schema::create('weather_data', function (Blueprint $table) {
            $table->id();
            $table->foreignId('country_id')->constrained('countries')->cascadeOnDelete();
            $table->decimal('temperature', 8, 2)->nullable();
            $table->decimal('apparent_temperature', 8, 2)->nullable();
            $table->decimal('humidity', 8, 2)->nullable();
            $table->decimal('precipitation', 8, 2)->nullable();
            $table->decimal('precipitation_probability', 8, 2)->nullable();
            $table->decimal('wind_speed', 8, 2)->nullable();
            $table->decimal('wind_gust', 8, 2)->nullable();
            $table->integer('weather_code')->nullable();
            $table->string('condition')->nullable();
            $table->boolean('is_day')->nullable();
            $table->decimal('storm_risk', 8, 2)->default(0);
            $table->timestamp('observed_at')->nullable();
            $table->timestamps();
        });

        // 4. Tabel Kurs Mata Uang
        Schema::create('currency_rates', function (Blueprint $table) {
            $table->id();
            $table->string('base_currency', 5);
            $table->string('target_currency', 5);
            $table->decimal('rate', 18, 6);
            $table->decimal('change_percent', 10, 4)->default(0);
            $table->date('rate_date')->nullable();
            $table->string('source')->default('ExchangeRate-API');
            $table->timestamp('recorded_at')->nullable();
            $table->timestamps();
            $table->index(['base_currency', 'target_currency']);
        });

        // 5. Tabel Skor Risiko
        Schema::create('risk_scores', function (Blueprint $table) {
            $table->id();
            $table->foreignId('country_id')->constrained('countries')->cascadeOnDelete();
            $table->decimal('weather_score', 8, 2);
            $table->decimal('inflation_score', 8, 2);
            $table->decimal('currency_score', 8, 2);
            $table->decimal('news_score', 8, 2);
            $table->decimal('total_score', 8, 2);
            $table->string('risk_level');
            $table->timestamp('calculated_at')->nullable();
            $table->timestamps();
        });

        // 6. Tabel Komponen Risiko
        Schema::create('risk_components', function (Blueprint $table) {
            $table->id();
            $table->foreignId('risk_score_id')->constrained('risk_scores')->cascadeOnDelete();
            $table->string('component');
            $table->decimal('raw_value', 14, 4)->nullable();
            $table->decimal('normalized_score', 8, 2);
            $table->decimal('weight', 8, 2);
            $table->decimal('weighted_score', 8, 2);
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        // 7. Tabel Pelabuhan
        Schema::create('ports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('country_id')->nullable()->constrained('countries')->nullOnDelete();
            $table->string('name');
            $table->string('unlocode')->nullable()->unique();
            $table->string('wpi_number')->nullable()->index();
            $table->string('city')->nullable();
            $table->string('country_name');
            $table->decimal('latitude', 10, 7);
            $table->decimal('longitude', 10, 7);
            $table->string('port_type')->default('Pelabuhan Laut');
            $table->string('harbor_size')->nullable();
            $table->string('harbor_type')->nullable();
            $table->string('status')->default('Aktif');
            $table->string('data_source')->default('Manual');
            $table->timestamp('imported_at')->nullable();
            $table->timestamps();
        });

        // 8. Tabel Batch Impor Pelabuhan
        Schema::create('port_import_batches', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('filename');
            $table->string('source')->default('World Port Index');
            $table->unsignedInteger('total_rows')->default(0);
            $table->unsignedInteger('imported_rows')->default(0);
            $table->unsignedInteger('skipped_rows')->default(0);
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        // 9. Tabel Cache Berita
        Schema::create('news_cache', function (Blueprint $table) {
            $table->id();
            $table->foreignId('country_id')->nullable()->constrained('countries')->nullOnDelete();
            $table->string('title');
            $table->text('description')->nullable();
            $table->text('url')->nullable();
            $table->text('image_url')->nullable();
            $table->string('source')->nullable();
            $table->timestamp('published_at')->nullable();
            $table->string('sentiment')->default('Netral');
            $table->integer('positive_score')->default(0);
            $table->integer('negative_score')->default(0);
            $table->string('query')->nullable();
            $table->string('language', 10)->default('en');
            $table->timestamps();
        });

        // 10. Tabel Sentimen Berita Detail
        Schema::create('news_sentiments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('news_cache_id')->constrained('news_cache')->cascadeOnDelete();
            $table->string('sentiment');
            $table->integer('positive_count')->default(0);
            $table->integer('negative_count')->default(0);
            $table->integer('neutral_count')->default(0);
            $table->json('matched_positive')->nullable();
            $table->json('matched_negative')->nullable();
            $table->timestamps();
        });

        // 11. Tabel Daftar Pantauan
        Schema::create('watchlists', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('country_id')->constrained('countries')->cascadeOnDelete();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->unique(['user_id', 'country_id']);
        });

        // 12. Tabel Artikel Analisis
        Schema::create('articles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('excerpt')->nullable();
            $table->longText('content');
            $table->string('status')->default('draft');
            $table->timestamp('published_at')->nullable();
            $table->timestamps();
        });

        // 13. Tabel Kata Positif
        Schema::create('positive_words', function (Blueprint $table) {
            $table->id();
            $table->string('word')->unique();
            $table->timestamps();
        });

        // 14. Tabel Kata Negatif
        Schema::create('negative_words', function (Blueprint $table) {
            $table->id();
            $table->string('word')->unique();
            $table->timestamps();
        });

        // 15. Tabel Log API
        Schema::create('api_logs', function (Blueprint $table) {
            $table->id();
            $table->string('service');
            $table->text('endpoint');
            $table->string('method', 10)->default('GET');
            $table->integer('status_code')->nullable();
            $table->integer('response_time_ms')->nullable();
            $table->boolean('success')->default(false);
            $table->text('message')->nullable();
            $table->timestamp('requested_at')->nullable();
            $table->timestamps();
        });

        // 16. Tabel Perbandingan Negara
        Schema::create('country_comparisons', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('country_a_id')->constrained('countries')->cascadeOnDelete();
            $table->foreignId('country_b_id')->constrained('countries')->cascadeOnDelete();
            $table->json('result')->nullable();
            $table->timestamps();
        });

        // 17. Tabel Pengaturan Sistem
        Schema::create('system_settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->text('value')->nullable();
            $table->string('type')->default('string');
            $table->text('description')->nullable();
            $table->timestamps();
        });

        // 18. Tabel Notifikasi
        Schema::create('notifications', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('type');
            $table->morphs('notifiable');
            $table->text('data');
            $table->timestamp('read_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notifications');
        Schema::dropIfExists('system_settings');
        Schema::dropIfExists('country_comparisons');
        Schema::dropIfExists('api_logs');
        Schema::dropIfExists('negative_words');
        Schema::dropIfExists('positive_words');
        Schema::dropIfExists('articles');
        Schema::dropIfExists('watchlists');
        Schema::dropIfExists('news_sentiments');
        Schema::dropIfExists('news_cache');
        Schema::dropIfExists('port_import_batches');
        Schema::dropIfExists('ports');
        Schema::dropIfExists('risk_components');
        Schema::dropIfExists('risk_scores');
        Schema::dropIfExists('currency_rates');
        Schema::dropIfExists('weather_data');
        Schema::dropIfExists('country_economics');
        Schema::dropIfExists('countries');
    }
};
