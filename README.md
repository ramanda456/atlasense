# 🌐 AtlaSense — Global Supply Chain Risk Intelligence Platform

<p align="center">
  <img src="https://img.shields.io/badge/Laravel-11.x-FF2D20?style=for-the-badge&logo=laravel&logoColor=white" alt="Laravel 11">
  <img src="https://img.shields.io/badge/PHP-8.2%2B-777BB4?style=for-the-badge&logo=php&logoColor=white" alt="PHP 8.2">
  <img src="https://img.shields.io/badge/Bootstrap-5.3-7952B3?style=for-the-badge&logo=bootstrap&logoColor=white" alt="Bootstrap 5">
  <img src="https://img.shields.io/badge/MySQL-8.0-4479A1?style=for-the-badge&logo=mysql&logoColor=white" alt="MySQL">
  <img src="https://img.shields.io/badge/Leaflet-1.9-199900?style=for-the-badge&logo=leaflet&logoColor=white" alt="Leaflet.js">
  <img src="https://img.shields.io/badge/Chart.js-4.4-FF6384?style=for-the-badge&logo=chart.js&logoColor=white" alt="Chart.js">
</p>

---

## 📌 Ringkasan Proyek

**AtlaSense** adalah platform *Decision Support System* (DSS), *Data Engineering*, dan *Business Intelligence* berbasis web untuk mengelola, menganalisis, serta memantau risiko rantai pasok (*supply chain*) pengiriman barang global secara *real-time*.

Platform ini mengonsolidasikan indikator risiko makroekonomi, cuaca buruk, fluktuasi kurs mata uang, sentimen geopolitik media internasional, dan lokasi geospasial pelabuhan komersial dari **7 API / Sumber Data Eksternal** ke dalam satu *dashboard* terpadu.

---

## 🚀 Fitur Utama

1. **Global Country Dashboard (Dasbor Utama):** Konsolidasi indikator makroekonomi (GDP, inflasi, populasi), cuaca terkini, nilai tukar mata uang, dan akumulasi *Risk Score*.
2. **Weighted Risk Scoring Engine:** Perhitungan skor risiko otomatis berbasis multi-kriteria (0–100) dengan penalti dinamis mandiri (*badai, inflasi lonjakan, fluktuasi kurs*).
3. **Global Weather Monitoring:** Pemantauan parameter meteorologi real-time (suhu, curah hujan, kecepatan angin, kelembaban, risiko badai) via Open-Meteo API.
4. **Currency Impact Dashboard:** Analisis tren fluktuasi nilai tukar terhadap USD menggunakan **Chart.js** dan 4 kartu ringkasan nominal (*Kurs Terkini, Perubahan %, Kurs Tertinggi, Kurs Terendah*).
5. **News Intelligence & Lexicon Sentiment:** Penarikan artikel berita internasional dari GNews API dengan analisis sentimen leksikon otomatis (*Positive*, *Neutral*, *Negative*).
6. **Port Location Dashboard:** Pemetaan geospasial lokasi pelabuhan maritim komersial dunia pada peta interaktif **Leaflet.js** & **OpenStreetMap**.
7. **Data Visualization Dashboard:** Visualisasi grafik historis multi-tahun (Tren GDP, Tren Inflasi 10 Tahun, Tren Kurs, Tren Histori Risiko) berbasis Chart.js.
8. **Country Comparison Engine:** Pembandingan indikator logistik, ekonomi, dan cuaca antara 2 negara secara bersisian (*side-by-side*).
9. **Favorite Watchlist (Daftar Pantauan):** Penandaan bintang (⭐) untuk mengelola daftar negara favorit prioritas pengguna.
10. **Admin Command Center:** Panel manajemen akun pengguna, pengelolaan kamus kata leksikon sentimen, publikasi artikel analisis, impor dataset pelabuhan massal via CSV, dan audit log panggilan API.

---

## 🔌 Integrasi API & Sumber Data Eksternal

| Sumber Data / API | Fungsi & Penggunaan Data |
| :--- | :--- |
| **REST Countries API** | Data profil geospasial & ekonomi 250+ negara (ISO Code, populasi, ibu kota, bendera, mata uang). |
| **Open-Meteo API** | Data cuaca real-time (temperatur, kelembaban, hujan, kecepatan angin, risiko badai). |
| **World Bank API** | Data historis indikator makroekonomi (GDP dan inflasi 10 tahun terakhir). |
| **ExchangeRate API** | Data harian nilai tukar mata uang lokal terhadap USD. |
| **GNews API** | Artikel berita perdagangan & logistik internasional. |
| **World Port Index Dataset** | Dataset publik lokasi geospasial & status pelabuhan laut komersial dunia. |
| **OpenStreetMap Tile API** | Layer ubin peta geospasial interaktif global via Leaflet.js. |

---

## 🧮 Formula Risk Scoring Engine

Tingkat risiko rantai pasok (0 - 100) dihitung menggunakan metode pembobotan multi-kriteria:

Total Risk Score = (0.25 × Skor Cuaca) + (0.25 × Skor Inflasi) + (0.30 × Skor Berita) + (0.20 × Skor Kurs) + Penalti Dinamis

### Aturan Penalti Dinamis:
* **Badai / Angin Kencang (> 25 km/jam):** Penalti +10 poin pada risiko cuaca.
* **Lonjakan Kurs Signifikan (> 2% harian):** Penalti +15 poin pada risiko nilai tukar.
* **Sentimen Negatif Dominan Berita:** Penalti +15 poin pada risiko berita.

### Kategori Risiko:
* 🟢 **Low Risk (Aman):** Score kurang dari 40
* 🟡 **Medium Risk (Waspada):** Score 40 sampai 70
* 🔴 **High Risk (Bahaya):** Score lebih dari 70

---

## 📂 Struktur Basis Data

Database `atlasense` terdiri dari **27 tabel** (15 tabel data bisnis inti + 12 tabel utilitas Laravel):

* **Tabel Bisnis Inti (15):** `users`, `countries`, `ports`, `watchlists`, `articles`, `positive_words`, `negative_words`, `news_cache`, `news_sentiments`, `weather_data`, `currency_rates`, `risk_scores`, `risk_components`, `country_comparisons`, `port_import_batches`.
* **Tabel Utilitas Laravel (12):** `migrations`, `sessions`, `cache`, `cache_locks`, `jobs`, `job_batches`, `failed_jobs`, `notifications`, `password_reset_tokens`, `api_logs`, `system_settings`.

---

## 📡 REST API Internal

AtlaSense menyediakan 5 endpoint internal REST API yang mengembalikan format JSON (`200 OK`):

```http
GET /api/countries    # Mengembalikan daftar 250+ negara, kode ISO, & koordinat.
GET /api/risk         # Mengembalikan hasil kalkulasi Weighted Risk Model seluruh negara.
GET /api/ports        # Mengembalikan data lokasi pelabuhan maritim komersial.
GET /api/news         # Mengembalikan artikel berita logistik & label sentimen leksikon.
GET /api/currency     # Mengembalikan data nilai tukar mata uang real-time terhadap USD.
```

---

## 💻 Panduan Instalasi & Pengoperasian Lokal

### 1. Prasyarat Sistem
* PHP >= 8.2
* Composer
* MySQL / XAMPP Database Server
* Node.js & NPM (Opsional)

### 2. Langkah Instalasi

```bash
# 1. Clone repositori ini
git clone https://github.com/ramanda456/atlasense.git
cd atlasense

# 2. Install dependensi composer
composer install

# 3. Buat file environment .env
cp .env.example .env

# 4. Generate Application Key
php artisan key:generate

# 5. Konfigurasi Database di file .env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=atlasense
DB_USERNAME=root
DB_PASSWORD=

# 6. Jalankan Migrasi & Seeder Database
php artisan migrate --seed

# 7. Jalankan Server Lokal
php artisan serve
```

Aplikasi dapat diakses melalui browser di: `http://localhost:8000`

---

## 🔑 Kredensial Akun Pengujian (Demo)

| Role | Email | Password |
| :--- | :--- | :--- |
| **Administrator** | `admin@atlasense.com` | `admin123` |
| **Pengguna Standard** | `user@atlasense.com` | `user123` |

---

## 🛠️ Perintah Artisan Custom (Data Sync Command)

```bash
php artisan sync:all        # Sinkronisasi seluruh data API sekaligus
php artisan sync:negara     # Sinkronisasi data negara dari REST Countries API
php artisan sync:cuaca      # Sinkronisasi data cuaca dari Open-Meteo API
php artisan sync:ekonomi    # Sinkronisasi data makroekonomi dari World Bank API
php artisan sync:kurs       # Sinkronisasi data kurs mata uang dari ExchangeRate API
php artisan sync:berita     # Sinkronisasi berita dari GNews API & kalkulasi sentimen
php artisan import:pelabuhan # Import dataset pelabuhan komersial
```

---

## 📄 Lisensi
Hak Cipta © 2026 **AtlaSense Team**. Dikembangkan untuk Proyek Final Tugas Akhir / Skripsi.
