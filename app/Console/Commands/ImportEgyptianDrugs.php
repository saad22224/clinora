<?php

namespace App\Console\Commands;

use App\Models\Drug;
use Illuminate\Console\Command;

class ImportEgyptianDrugs extends Command
{
    /**
     * Execute the console command.
     */
    protected $signature = 'drugs:import';

    protected $description = "Import Egyptian drugs from egyptian-drugs.json into database''Import Egyptian drugs from egyptian-drugs.json into database";

    public function handle()
    {
        $path = public_path('egyptian-drugs.json');
        if (! file_exists($path)) {
            $this->error("File egyptian-drugs.json not found at: {$path}");

            return Command::FAILURE;
        }

        $this->info('Reading and parsing egyptian-drugs.json...');
        $content = file_get_contents($path);
        $drugs = json_decode($content, true);
        $slicedcontent = array_slice($drugs, 0, 30000);

        if (! is_array($slicedcontent)) {
            $this->error('Failed to parse JSON content or file is empty.');

            return Command::FAILURE;
        }

        $count = Drug::count();
        if ($count > 0) {
            $this->warn("Table already contains {$count} drugs. Clearing existing records...");
            Drug::truncate();
        }

        $total = count($slicedcontent);
        $this->info("Importing {$total} drugs into database...");
        $bar = $this->output->createProgressBar($total);
        $bar->start();

        $chunkSize = 1000;
        $chunks = array_chunk($slicedcontent, $chunkSize);
        $now = now();

        foreach ($chunks as $chunk) {
            $records = [];
            foreach ($chunk as $item) {
                $records[] = [
                    'commercial_name_en' => ! empty($item['commercial_name_en']) ? mb_substr(trim($item['commercial_name_en']), 0, 255) : 'Unknown',
                    'commercial_name_ar' => ! empty($item['commercial_name_ar']) ? mb_substr(trim($item['commercial_name_ar']), 0, 255) : null,
                    'scientific_name' => ! empty($item['scientific_name']) ? trim($item['scientific_name']) : null,
                    'manufacturer' => ! empty($item['manufacturer']) ? mb_substr(trim($item['manufacturer']), 0, 255) : null,
                    'drug_class' => ! empty($item['drug_class']) ? mb_substr(trim($item['drug_class']), 0, 255) : null,
                    'route' => ! empty($item['route']) ? mb_substr(trim($item['route']), 0, 100) : null,
                    'price_egp' => isset($item['price_egp']) && is_numeric($item['price_egp']) ? (float) $item['price_egp'] : null,
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }
            Drug::insert($records);
            $bar->advance(count($chunk));
        }

        $bar->finish();
        $this->newLine();
        $this->info("Successfully imported {$total} drugs!");

        return Command::SUCCESS;
    }
}
