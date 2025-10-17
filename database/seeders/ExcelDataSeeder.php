<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Region;
use App\Models\Tuman;
use App\Models\Mahalla;
use App\Models\Lot;
use App\Models\Distribution;
use App\Models\PaymentSchedule;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Shared\Date;

class ExcelDataSeeder extends Seeder
{
    private $tumanMap = [];
    private $mahallaMap = [];
    private $stats = [
        'total_rows' => 0,
        'imported' => 0,
        'errors' => 0,
        'tumans' => [],
        'mahallas' => [],
        'unique_mahallas' => [],
        'addresses' => []
    ];

    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        // Clear existing data
        PaymentSchedule::truncate();
        Distribution::truncate();
        Lot::truncate();
        Mahalla::truncate();
        User::truncate();
        Tuman::truncate();
        Region::truncate();

        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // Create base structure
        $this->createRegionsAndTumans();
        $this->createUsers();

        // Import from Excel
        $excelPath = public_path('Аукционда_сотилган_ерлар_06_10_2025+baza_ROYXAT (2).xlsx');

        if (!file_exists($excelPath)) {
            $this->command->error("Excel file not found: {$excelPath}");
            $this->command->info("Please update the path in ExcelDataSeeder.php");
            return;
        }

        $this->command->info("Reading Excel file...");
        $this->importFromExcel($excelPath);

        // Display comprehensive statistics
        $this->displayStatistics();
    }

    private function createRegionsAndTumans()
    {
        $this->command->info("Creating regions and districts...");

        // Create Tashkent region
        $tashkent = Region::create([
            'name_uz' => 'Тошкент шаҳри',
            'name_ru' => 'Город Ташкент',
        ]);

        // Create districts with proper mapping
        $tumanNames = [
            'Бектемир тумани' => 'Бектемирский район',
            'Мирзо Улуғбек тумани' => 'Мирзо-Улугбекский район',
            'Миробод тумани' => 'Мирабадский район',
            'Олмазор тумани' => 'Алмазарский район',
            'Сирғали тумани' => 'Сергелийский район',
            'Учтепа тумани' => 'Учтепинский район',
            'Чилонзор тумани' => 'Чиланзарский район',
            'Шайхонтоҳур тумани' => 'Шайхантахурский район',
            'Юнусобод тумани' => 'Юнусабадский район',
            'Яккасарой тумани' => 'Яккасарайский район',
            'Янги ҳаёт тумани' => 'Янгихаятский район',
            'Яшнобод тумани' => 'Яшнабадский район',
        ];

        foreach ($tumanNames as $nameUz => $nameRu) {
            $tuman = Tuman::create([
                'region_id' => $tashkent->id,
                'name_uz' => $nameUz,
                'name_ru' => $nameRu,
            ]);

            $this->tumanMap[$nameUz] = $tuman->id;
            $this->stats['tumans'][$nameUz] = 0; // Initialize counter
        }

        $this->command->info("✓ Created " . count($tumanNames) . " districts");
        Log::info("Districts created", ['tumans' => $this->tumanMap]);
    }

    private function createUsers()
    {
        $this->command->info("Creating users...");

        // Admin
        User::create([
            'name' => 'Admin User',
            'email' => 'admin@toshkentinvest.uz',
            'password' => Hash::make('admin123'),
            'role' => 'admin',
            'is_active' => true,
        ]);

        // District users
        foreach ($this->tumanMap as $tumanName => $tumanId) {
            $email = str_replace(' ', '', strtolower(transliterate($tumanName))) . '@toshkentinvest.uz';
            User::create([
                'name' => $tumanName . ' ходими',
                'email' => $email,
                'password' => Hash::make('password'),
                'role' => 'district_user',
                'tuman_id' => $tumanId,
                'is_active' => true,
            ]);
        }

        // Viewer
        User::create([
            'name' => 'Viewer User',
            'email' => 'viewer@toshkentinvest.uz',
            'password' => Hash::make('viewer123'),
            'role' => 'viewer',
            'is_active' => true,
        ]);

        $this->command->info("✓ Created users");
    }

    private function importFromExcel($filePath)
    {
        $spreadsheet = IOFactory::load($filePath);
        $worksheet = $spreadsheet->getActiveSheet();
        $rows = $worksheet->toArray();

        // Remove header row
        array_shift($rows);

        $this->stats['total_rows'] = count($rows);

        $progressBar = $this->command->getOutput()->createProgressBar(count($rows));
        $progressBar->start();

        foreach ($rows as $index => $row) {
            try {
                // Skip empty rows
                if (empty($row[1])) {
                    continue;
                }

                $this->importRow($row, $index + 2);
                $this->stats['imported']++;
            } catch (\Exception $e) {
                $this->stats['errors']++;
                $errorMsg = "Row " . ($index + 2) . ": " . $e->getMessage();
                $this->command->error("\n" . $errorMsg);
                Log::error($errorMsg, [
                    'row_number' => $index + 2,
                    'lot_number' => $row[1] ?? 'N/A',
                    'tuman' => $row[2] ?? 'N/A',
                    'address' => $row[3] ?? 'N/A'
                ]);
            }

            $progressBar->advance();
        }

        $progressBar->finish();
        $this->command->newLine(2);
    }

    private function importRow($row, $rowNumber)
    {
        // Parse lot number (remove commas)
        $lotNumber = $this->cleanNumber($row[1]);
        if (!$lotNumber) {
            throw new \Exception("Missing lot number");
        }

        // Get tuman (Column 2)
        $tumanName = trim($row[2]);
        $tumanId = $this->findTumanId($tumanName);
        if (!$tumanId) {
            throw new \Exception("Tuman not found: {$tumanName}");
        }

        // Count lots per tuman
        $this->stats['tumans'][$tumanName] = ($this->stats['tumans'][$tumanName] ?? 0) + 1;

        // Get FULL ADDRESS from column 3 (this is the address, NOT mahalla name)
        $fullAddress = trim($row[3]);

        // Extract mahalla name from address
        // Pattern: "SomeName MFY" or "SomeName MFҲ" or just the address
        $mahallaName = $this->extractMahallaName($fullAddress);

        $mahallaId = null;
        if ($mahallaName) {
            $mahallaId = $this->findOrCreateMahalla($mahallaName, $tumanId, $tumanName);
        }

        // Store address separately
        if (!isset($this->stats['addresses'][$tumanName])) {
            $this->stats['addresses'][$tumanName] = [];
        }
        if (!in_array($fullAddress, $this->stats['addresses'][$tumanName])) {
            $this->stats['addresses'][$tumanName][] = $fullAddress;
        }

        // Parse dates
        $auctionDate = $this->parseDate($row[17]);
        $contractDate = $this->parseDate($row[27]);

        // Create lot
        $lot = Lot::create([
            'lot_number' => $lotNumber,
            'tuman_id' => $tumanId,
            'mahalla_id' => $mahallaId,
            'address' => $fullAddress, // Store full address here
            'unique_number' => $row[4] ?? null,
            'zone' => $row[5] ?? null,
            'latitude' => $row[6] ?? null,
            'longitude' => $row[7] ?? null,
            'location_url' => $row[8] ?? null,
            'master_plan_zone' => $row[9] ?? null,
            'yangi_uzbekiston' => $this->parseBool($row[10]),
            'land_area' => $this->parseDecimal($row[11]),
            'object_type' => $row[12] ?? null,
            'object_type_ru' => $row[13] ?? null,
            'construction_area' => $this->parseDecimal($row[14]),
            'investment_amount' => $this->parseDecimal($row[15]),
            'initial_price' => $this->parseDecimal($row[16]),
            'auction_date' => $auctionDate,
            'sold_price' => $this->parseDecimal($row[18]),
            'winner_type' => $row[19] ?? null,
            'winner_name' => $row[20] ?? null,
            'winner_phone' => $row[21] ?? null,
            'payment_type' => $this->parsePaymentType($row[22]),
            'basis' => $row[23] ?? null,
            'auction_type' => $this->parseAuctionType($row[24]),
            'lot_status' => $row[25] ?? 'active',
            'contract_signed' => $this->parseContractSigned($row[26]),
            'contract_date' => $contractDate,
            'contract_number' => $row[28] ?? null,
            'paid_amount' => $this->parseDecimal($row[29]),
            'transferred_amount' => $this->parseDecimal($row[30]),
            'discount' => $this->parseDecimal($row[31]),
            'auction_fee' => $this->parseDecimal($row[32]),
            'incoming_amount' => $this->parseDecimal($row[33]),
            'davaktiv_amount' => $this->parseDecimal($row[34]),
            'auction_expenses' => $this->parseDecimal($row[35]),
        ]);

        // Auto-calculate if sold price exists
        if ($lot->sold_price && !$lot->auction_fee) {
            $lot->autoCalculate();
            $lot->save();
        }

        // Import distributions
        $this->importDistributions($lot, $row);

        // Import payment schedules
        $this->importPaymentSchedules($lot, $row);

        // Log successful import
        Log::info("Lot imported successfully", [
            'lot_number' => $lotNumber,
            'tuman' => $tumanName,
            'mahalla' => $mahallaName,
            'address' => $fullAddress,
            'row' => $rowNumber
        ]);

        return $lot;
    }

    /**
     * Extract mahalla name from full address
     */
    private function extractMahallaName($address)
    {
        if (empty($address)) {
            return null;
        }

        // Pattern 1: "Name MFY" or "Name MFҲ"
        if (preg_match('/^(.+?)\s+MF[YҲ]/iu', $address, $matches)) {
            return trim($matches[1]) . ' MFY';
        }

        // Pattern 2: Just return the address as mahalla name
        // This ensures we capture all unique locations
        return $address;
    }

    private function importDistributions($lot, $row)
    {
        // Check if lot has a contract first
        if (!$lot->contract) {
            // Create a contract automatically if distributions exist
            $hasDistributions = false;
            $categories = [
                'local_budget' => [36, 40, 44],
                'development_fund' => [37, 41, 45],
                'new_uzbekistan' => [38, 42, 46],
                'district_authority' => [39, 43, 47],
            ];

            foreach ($categories as $category => $indices) {
                $allocatedAmount = $this->parseDecimal($row[$indices[0]] ?? 0);
                $distributedAmount = $this->parseDecimal($row[$indices[1]] ?? 0);

                if ($allocatedAmount > 0 || $distributedAmount > 0) {
                    $hasDistributions = true;
                    break;
                }
            }

            if ($hasDistributions && $lot->contract_signed) {
                // Auto-create contract for this lot
                $contract = \App\Models\Contract::create([
                    'lot_id' => $lot->id,
                    'contract_number' => $lot->contract_number ?? 'AUTO-' . $lot->lot_number,
                    'contract_date' => $lot->contract_date ?? $lot->auction_date ?? now(),
                    'contract_amount' => $lot->sold_price ?? 0,
                    'paid_amount' => $lot->paid_amount ?? 0,
                    'payment_type' => $lot->payment_type ?? 'muddatsiz',
                    'status' => 'active',
                    'created_by' => 1, // Admin user
                    'updated_by' => 1,
                ]);
            } else {
                return; // No contract and no distributions, skip
            }
        }

        $contract = $lot->contract;

        // Now import distributions linked to contract
        $categories = [
            'local_budget' => [36, 40, 44],
            'development_fund' => [37, 41, 45],
            'new_uzbekistan' => [38, 42, 46],
            'district_authority' => [39, 43, 47],
        ];

        foreach ($categories as $category => $indices) {
            $allocatedAmount = $this->parseDecimal($row[$indices[0]] ?? 0);
            $distributedAmount = $this->parseDecimal($row[$indices[1]] ?? 0);
            $remainingAmount = $this->parseDecimal($row[$indices[2]] ?? 0);

            if ($allocatedAmount > 0 || $distributedAmount > 0 || $remainingAmount > 0) {
                \App\Models\Distribution::create([
                    'contract_id' => $contract->id, // Use contract_id, not lot_id
                    'payment_schedule_id' => null,
                    'category' => $category,
                    'allocated_amount' => $distributedAmount, // Already distributed amount
                    'distribution_date' => $lot->contract_date ?? now(),
                    'status' => $distributedAmount > 0 ? 'distributed' : 'pending',
                    'note' => "Импортировано из Excel",
                    'created_by' => 1,
                    'updated_by' => 1,
                ]);
            }
        }
    }

    private function importPaymentSchedules($lot, $row)
    {
        // Only create payment schedules for installment payment types
        if ($lot->payment_type !== 'muddatli') {
            return;
        }

        // Check if lot has a contract
        if (!$lot->contract && $lot->contract_signed) {
            // Auto-create contract
            $contract = \App\Models\Contract::create([
                'lot_id' => $lot->id,
                'contract_number' => $lot->contract_number ?? 'AUTO-' . $lot->lot_number,
                'contract_date' => $lot->contract_date ?? $lot->auction_date ?? now(),
                'contract_amount' => $lot->sold_price ?? 0,
                'paid_amount' => $lot->paid_amount ?? 0,
                'payment_type' => 'muddatli',
                'status' => 'active',
                'created_by' => 1,
                'updated_by' => 1,
            ]);
        }

        if (!$lot->contract) {
            return; // Still no contract, skip
        }

        $contract = $lot->contract;

        // Payment schedules start from column 49 (index 48)
        $years = [2024, 2025, 2026, 2027, 2028, 2029];
        $monthsRu = [
            'август',
            'сентябрь',
            'октябрь',
            'ноябрь',
            'декабрь',
            'январь',
            'февраль',
            'март',
            'аперль',
            'май',
            'июнь',
            'июль'
        ];

        $colIndex = 49;
        $paymentNumber = 1;

        foreach ($years as $yearIndex => $year) {
            foreach ($monthsRu as $monthIndex => $monthName) {
                if (!isset($row[$colIndex])) {
                    $colIndex++;
                    continue;
                }

                $amount = $this->parseDecimal($row[$colIndex]);

                if ($amount > 0) {
                    // Calculate correct month number (1-12)
                    $monthNum = ($monthIndex < 5) ? ($monthIndex + 8) : ($monthIndex - 4);
                    $actualYear = ($monthIndex < 5) ? $year : ($year + 1);

                    try {
                        $plannedDate = date('Y-m-d', strtotime("{$actualYear}-{$monthNum}-01"));
                        $deadlineDate = date('Y-m-d', strtotime("{$actualYear}-{$monthNum}-10"));

                        \App\Models\PaymentSchedule::create([
                            'contract_id' => $contract->id,
                            'payment_number' => $paymentNumber++,
                            'planned_date' => $plannedDate,
                            'deadline_date' => $deadlineDate,
                            'planned_amount' => $amount,
                            'actual_amount' => 0,
                            'difference' => -$amount,
                            'status' => 'pending',
                            'created_by' => 1,
                            'updated_by' => 1,
                        ]);
                    } catch (\Exception $e) {
                        Log::warning("Failed to create payment schedule", [
                            'lot_id' => $lot->id,
                            'contract_id' => $contract->id,
                            'year' => $actualYear,
                            'month' => $monthName,
                            'error' => $e->getMessage()
                        ]);
                    }
                }
                $colIndex++;
            }
        }
    }
    private function findTumanId($tumanName)
    {
        $tumanName = trim($tumanName);

        // Direct match
        if (isset($this->tumanMap[$tumanName])) {
            return $this->tumanMap[$tumanName];
        }

        // Fuzzy match
        foreach ($this->tumanMap as $name => $id) {
            if (stripos($name, $tumanName) !== false || stripos($tumanName, $name) !== false) {
                return $id;
            }
        }

        return null;
    }

    private function findOrCreateMahalla($mahallaName, $tumanId, $tumanName)
    {
        $key = $tumanId . '_' . $mahallaName;

        if (isset($this->mahallaMap[$key])) {
            return $this->mahallaMap[$key];
        }

        $mahalla = Mahalla::firstOrCreate(
            ['name' => $mahallaName, 'tuman_id' => $tumanId],
            ['name_ru' => $mahallaName]
        );

        $this->mahallaMap[$key] = $mahalla->id;

        // Track unique mahallas per tuman
        if (!isset($this->stats['unique_mahallas'][$tumanName])) {
            $this->stats['unique_mahallas'][$tumanName] = [];
        }
        if (!in_array($mahallaName, $this->stats['unique_mahallas'][$tumanName])) {
            $this->stats['unique_mahallas'][$tumanName][] = $mahallaName;
        }

        return $mahalla->id;
    }

    private function displayStatistics()
    {
        $this->command->newLine();
        $this->command->info("═══════════════════════════════════════════════════════");
        $this->command->info("                 IMPORT STATISTICS                      ");
        $this->command->info("═══════════════════════════════════════════════════════");

        $this->command->info("Total Rows: {$this->stats['total_rows']}");
        $this->command->info("Successfully Imported: {$this->stats['imported']}");
        $this->command->warn("Errors: {$this->stats['errors']}");

        $this->command->newLine();
        $this->command->info("LOTS BY DISTRICT:");
        $this->command->info("───────────────────────────────────────────────────────");

        $totalLots = 0;
        foreach ($this->stats['tumans'] as $tuman => $count) {
            $this->command->info(sprintf("  %-35s : %d lots", $tuman, $count));
            $totalLots += $count;
        }

        $this->command->newLine();
        $this->command->info("MAHALLAS (NEIGHBORHOODS) BY DISTRICT:");
        $this->command->info("───────────────────────────────────────────────────────");

        foreach ($this->stats['unique_mahallas'] as $tuman => $mahallas) {
            $this->command->info(sprintf("  %-35s : %d mahallas", $tuman, count($mahallas)));
            foreach ($mahallas as $mahalla) {
                $this->command->line("    • " . $mahalla);
            }
        }

        $this->command->newLine();
        $this->command->info("UNIQUE ADDRESSES BY DISTRICT:");
        $this->command->info("───────────────────────────────────────────────────────");

        foreach ($this->stats['addresses'] as $tuman => $addresses) {
            $this->command->info(sprintf("  %-35s : %d addresses", $tuman, count($addresses)));
        }

        $this->command->newLine();
        $this->command->info("═══════════════════════════════════════════════════════");

        // Log complete JSON statistics
        Log::info("Import completed", [
            'summary' => [
                'total_rows' => $this->stats['total_rows'],
                'imported' => $this->stats['imported'],
                'errors' => $this->stats['errors'],
                'total_lots' => $totalLots,
                'total_mahallas' => array_sum(array_map('count', $this->stats['unique_mahallas'])),
                'total_addresses' => array_sum(array_map('count', $this->stats['addresses']))
            ],
            'lots_by_district' => $this->stats['tumans'],
            'mahallas_by_district' => array_map('count', $this->stats['unique_mahallas']),
            'mahallas_detail' => $this->stats['unique_mahallas'],
            'addresses_by_district' => array_map('count', $this->stats['addresses']),
            'addresses_detail' => $this->stats['addresses']
        ]);

        $this->command->info("✓ Data imported successfully!");
        $this->command->info("✓ Check storage/logs/laravel.log for detailed JSON output");
    }

    // Helper methods
    private function cleanNumber($value)
    {
        if (empty($value)) return null;
        return str_replace([',', ' '], '', $value);
    }

    private function parseDecimal($value)
    {
        if (empty($value)) return 0;
        $cleaned = str_replace([',', ' '], ['', ''], $value);
        return is_numeric($cleaned) ? floatval($cleaned) : 0;
    }

    private function parseDate($value)
    {
        if (empty($value)) return null;

        try {
            if (is_numeric($value)) {
                return Date::excelToDateTimeObject($value)->format('Y-m-d');
            }

            $timestamp = strtotime($value);
            if ($timestamp !== false) {
                return date('Y-m-d', $timestamp);
            }

            return null;
        } catch (\Exception $e) {
            return null;
        }
    }

    private function parseBool($value)
    {
        if (empty($value)) return false;
        $value = strtolower(trim($value));
        return in_array($value, ['янги ўзбекистон', 'yes', '1', 'true', 'ha', '+']);
    }

    private function parsePaymentType($value)
    {
        if (empty($value)) return null;
        $value = strtolower(trim($value));

        if (strpos($value, 'муддатли эмас') !== false) {
            return 'muddatli_emas';
        }

        if (strpos($value, 'муддатли') !== false) {
            return 'muddatli';
        }

        return null;
    }

    private function parseAuctionType($value)
    {
        if (empty($value)) return null;
        $value = strtolower(trim($value));

        if (strpos($value, 'ёпиқ') !== false) {
            return 'yopiq';
        }

        if (strpos($value, 'очиқ') !== false) {
            return 'ochiq';
        }

        return null;
    }

    private function parseContractSigned($value)
    {
        if (empty($value)) return false;
        $value = strtolower(trim($value));
        return strpos($value, 'шартнома тузилган') !== false;
    }
}

function transliterate($text)
{
    $cyrillic = ['а', 'б', 'в', 'г', 'д', 'е', 'ё', 'ж', 'з', 'и', 'й', 'к', 'л', 'м', 'н', 'о', 'п', 'р', 'с', 'т', 'у', 'ф', 'х', 'ц', 'ч', 'ш', 'щ', 'ъ', 'ы', 'ь', 'э', 'ю', 'я', 'ў', 'қ', 'ғ', 'ҳ'];
    $latin = ['a', 'b', 'v', 'g', 'd', 'e', 'yo', 'zh', 'z', 'i', 'y', 'k', 'l', 'm', 'n', 'o', 'p', 'r', 's', 't', 'u', 'f', 'h', 'ts', 'ch', 'sh', 'sch', '', 'y', '', 'e', 'yu', 'ya', 'o', 'q', 'g', 'h'];
    return str_replace($cyrillic, $latin, mb_strtolower($text));
}
