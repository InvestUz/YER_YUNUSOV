<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use PhpOffice\PhpSpreadsheet\IOFactory;

class UpdateLotAuctionTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $filePath = public_path('test_lot_auction_type_data.xlsx');

        if (!file_exists($filePath)) {
            $this->command->error("File not found: {$filePath}");
            return;
        }

        try {
            // Load the Excel file
            $spreadsheet = IOFactory::load($filePath);
            $worksheet = $spreadsheet->getActiveSheet();
            $rows = $worksheet->toArray();

            // Remove header row
            $header = array_shift($rows);

            $updated = 0;
            $notFound = 0;
            $errors = 0;

            $this->command->info("Processing " . count($rows) . " rows...");

            foreach ($rows as $index => $row) {
                try {
                    // Get lot number and auction type from the row
                    $lotNumber = trim($row[0] ?? '');
                    $auctionTypeRaw = trim($row[1] ?? '');

                    // Skip empty rows
                    if (empty($lotNumber) || empty($auctionTypeRaw)) {
                        continue;
                    }

                    // Convert auction type to lowercase Latin
                    // "Ёпиқ аукцион" -> "yopiq"
                    // "Очиқ аукцион" -> "ochiq"
                    $auctionType = null;
                    if (mb_stripos($auctionTypeRaw, 'Ёпиқ') !== false ||
                        mb_stripos($auctionTypeRaw, 'yopiq') !== false) {
                        $auctionType = 'yopiq';
                    } elseif (mb_stripos($auctionTypeRaw, 'Очиқ') !== false ||
                              mb_stripos($auctionTypeRaw, 'ochiq') !== false) {
                        $auctionType = 'ochiq';
                    }

                    if (!$auctionType) {
                        $this->command->warn("Row " . ($index + 2) . ": Unknown auction type '{$auctionTypeRaw}'");
                        $errors++;
                        continue;
                    }

                    // Update the lot
                    $affectedRows = DB::table('lots')
                        ->where('lot_number', $lotNumber)
                        ->update([
                            'auction_type' => $auctionType,
                            'updated_at' => now()
                        ]);

                    if ($affectedRows > 0) {
                        $updated++;
                        $this->command->info("✓ Updated lot {$lotNumber} -> {$auctionType}");
                    } else {
                        $notFound++;
                        $this->command->warn("✗ Lot {$lotNumber} not found in database");
                    }

                } catch (\Exception $e) {
                    $errors++;
                    $this->command->error("Error processing row " . ($index + 2) . ": " . $e->getMessage());
                    Log::error("Seeder error on row " . ($index + 2), [
                        'row' => $row,
                        'error' => $e->getMessage()
                    ]);
                }
            }

            // Summary
            $this->command->newLine();
            $this->command->info("=== Update Summary ===");
            $this->command->info("✓ Successfully updated: {$updated}");
            $this->command->warn("✗ Lots not found: {$notFound}");
            $this->command->error("✗ Errors: {$errors}");

        } catch (\Exception $e) {
            $this->command->error('Fatal error: ' . $e->getMessage());
            Log::error('UpdateLotAuctionTypeSeeder failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }
    }
}
