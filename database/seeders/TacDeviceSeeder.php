<?php

declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TacDeviceSeeder extends Seeder
{
    public function run(): void
    {
        $csvPath = database_path('data/tac_database.csv');

        if (! file_exists($csvPath)) {
            $this->command->error("Arquivo CSV não encontrado: {$csvPath}");
            return;
        }

        $handle = fopen($csvPath, 'r');
        if ($handle === false) {
            $this->command->error("Não foi possível abrir o arquivo CSV.");
            return;
        }

        $header = fgetcsv($handle);
        if ($header === false) {
            fclose($handle);
            return;
        }

        $batch = [];
        $seen = [];
        $batchSize = 500;
        $total = 0;
        $now = now();

        DB::table('tac_devices')->truncate();

        while (($row = fgetcsv($handle)) !== false) {
            if (count($row) < 3 || empty(trim($row[0]))) {
                continue;
            }

            $tac = trim($row[0]);
            if (isset($seen[$tac])) {
                continue;
            }
            $seen[$tac] = true;

            $batch[] = [
                'tac' => $tac,
                'brand' => trim($row[1]),
                'model' => trim($row[2]),
                'device_type' => isset($row[3]) ? trim($row[3]) : null,
                'created_at' => $now,
                'updated_at' => $now,
            ];

            if (count($batch) >= $batchSize) {
                DB::table('tac_devices')->insert($batch);
                $total += count($batch);
                $batch = [];
            }
        }

        if (! empty($batch)) {
            DB::table('tac_devices')->insert($batch);
            $total += count($batch);
        }

        fclose($handle);

        $this->command->info("Importados {$total} registros TAC com sucesso.");
    }
}
