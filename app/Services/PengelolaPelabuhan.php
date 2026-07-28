<?php

namespace App\Services;

use App\Models\Negara;
use App\Models\Pelabuhan;
use App\Models\BatchImporPelabuhan;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;
use RuntimeException;

class PengelolaPelabuhan
{
    /**
     * Memproses impor data pelabuhan dari file CSV
     */
    public function impor(UploadedFile|string $file, ?int $userId = null): BatchImporPelabuhan
    {
        $path = $file instanceof UploadedFile ? $file->getRealPath() : $file;
        $filename = $file instanceof UploadedFile ? $file->getClientOriginalName() : basename($file);

        if (!$path || !is_readable($path)) {
            throw new RuntimeException('File CSV tidak dapat dibaca atau tidak ditemukan.');
        }

        $handle = fopen($path, 'r');
        if (!$handle) {
            throw new RuntimeException('Gagal membuka file CSV.');
        }

        $rawHeaders = fgetcsv($handle);
        if (!$rawHeaders) {
            fclose($handle);
            throw new RuntimeException('Header file CSV tidak ditemukan.');
        }

        $headers = array_map([$this, 'normalisasiHeader'], $rawHeaders);
        $total = $imported = $skipped = 0;

        while (($values = fgetcsv($handle)) !== false) {
            $total++;
            $values = array_pad($values, count($headers), null);
            $row = array_combine($headers, array_slice($values, 0, count($headers)));

            $name = $this->firstVal($row, ['main_port_name', 'port_name', 'name', 'harbor_name']);
            $countryName = $this->firstVal($row, ['country', 'country_name', 'nation']);
            $latitude = $this->koordinat($row, 'lat');
            $longitude = $this->koordinat($row, 'lon');

            if (!$name || !$countryName || $latitude === null || $longitude === null) {
                $skipped++;
                continue;
            }

            $countryCode = strtoupper((string) $this->firstVal($row, ['country_code', 'iso2', 'country_alpha_2']));
            $country = $countryCode ? Negara::where('code', $countryCode)->first() : null;
            $country ??= Negara::where('name', 'like', '%' . $countryName . '%')->first();

            $unlocode = $this->firstVal($row, ['un_locode', 'unlocode', 'locode']);
            $wpiNumber = $this->firstVal($row, ['world_port_index_number', 'wpi_number', 'index_no', 'index_number']);
            $identity = $unlocode ? ['unlocode' => strtoupper($unlocode)] : ($wpiNumber ? ['wpi_number' => $wpiNumber] : ['name' => $name, 'country_name' => $countryName]);

            Pelabuhan::updateOrCreate($identity, [
                'country_id' => $country?->id,
                'name' => $name,
                'unlocode' => $unlocode ? strtoupper($unlocode) : null,
                'wpi_number' => $wpiNumber,
                'city' => $this->firstVal($row, ['city', 'province', 'region_name']),
                'country_name' => $countryName,
                'latitude' => $latitude,
                'longitude' => $longitude,
                'port_type' => $this->firstVal($row, ['port_type', 'facility']) ?: 'Pelabuhan Laut',
                'harbor_size' => $this->firstVal($row, ['harbor_size', 'harbour_size']),
                'harbor_type' => $this->firstVal($row, ['harbor_type', 'harbour_type']),
                'status' => 'Aktif',
                'data_source' => 'World Port Index',
                'imported_at' => now(),
            ]);
            $imported++;
        }

        fclose($handle);

        return BatchImporPelabuhan::create([
            'user_id' => $userId,
            'filename' => $filename,
            'source' => 'World Port Index',
            'total_rows' => $total,
            'imported_rows' => $imported,
            'skipped_rows' => $skipped,
            'notes' => 'Pengelola Pelabuhan mengimpor World Port Index dengan variasi nama kolom CSV.',
        ]);
    }

    private function normalisasiHeader(?string $header): string
    {
        return Str::of((string) $header)->lower()->ascii()->replaceMatches('/[^a-z0-9]+/', '_')->trim('_')->toString();
    }

    private function firstVal(array $row, array $keys): ?string
    {
        foreach ($keys as $key) {
            $value = trim((string) ($row[$key] ?? ''));
            if ($value !== '') {
                return $value;
            }
        }
        return null;
    }

    private function koordinat(array $row, string $type): ?float
    {
        $keys = $type === 'lat'
            ? ['latitude', 'lat', 'latitude_decimal', 'y']
            : ['longitude', 'lon', 'lng', 'longitude_decimal', 'x'];
        $value = $this->firstVal($row, $keys);
        if ($value !== null && is_numeric(str_replace(',', '.', $value))) {
            return (float) str_replace(',', '.', $value);
        }

        $degree = $this->firstVal($row, $type === 'lat' ? ['latitude_degrees', 'lat_deg'] : ['longitude_degrees', 'lon_deg']);
        $minute = $this->firstVal($row, $type === 'lat' ? ['latitude_minutes', 'lat_min'] : ['longitude_minutes', 'lon_min']);
        $hemisphere = strtoupper((string) $this->firstVal($row, $type === 'lat' ? ['latitude_hemisphere', 'lat_hem'] : ['longitude_hemisphere', 'lon_hem']));

        if ($degree !== null && is_numeric($degree)) {
            $decimal = abs((float) $degree) + ((float) ($minute ?: 0) / 60);
            if (in_array($hemisphere, ['S', 'W'], true) || (float) $degree < 0) {
                $decimal *= -1;
            }
            return $decimal;
        }

        return null;
    }
}
