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
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Shared\Date;

class ExcelDataSeeder extends Seeder
{
    private $tumanMap = [];
    private $mahallaMap = [];

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
        $this->command->info("✓ Data imported successfully!");
    }

    private function createRegionsAndTumans()
    {
        $this->command->info("Creating regions and districts...");

        // Create Tashkent region
        $tashkent = Region::create([
            'name_uz' => 'Тошкент шаҳри',
            'name_ru' => 'Город Ташкент',
        ]);

        // Create districts
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
        }

        $this->command->info("✓ Created " . count($tumanNames) . " districts");
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

        $imported = 0;
        $errors = 0;

        $progressBar = $this->command->getOutput()->createProgressBar(count($rows));
        $progressBar->start();

        foreach ($rows as $index => $row) {
            try {
                // Skip empty rows
                if (empty($row[1])) {
                    continue;
                }

                $this->importRow($row, $index + 2);
                $imported++;
            } catch (\Exception $e) {
                $errors++;
                $this->command->error("\nRow " . ($index + 2) . ": " . $e->getMessage());
            }

            $progressBar->advance();
        }

        $progressBar->finish();
        $this->command->newLine(2);
        $this->command->info("Imported: {$imported} lots");
        if ($errors > 0) {
            $this->command->warn("Errors: {$errors}");
        }
    }

    private function importRow($row, $rowNumber)
    {
        // Parse lot number (remove commas)
        $lotNumber = $this->cleanNumber($row[1]);
        if (!$lotNumber) {
            throw new \Exception("Missing lot number");
        }

        // Get tuman
        $tumanName = trim($row[2]);
        $tumanId = $this->findTumanId($tumanName);
        if (!$tumanId) {
            throw new \Exception("Tuman not found: {$tumanName}");
        }

        // Get or create mahalla
        $mahallaName = trim($row[3]);
        $mahallaId = null;
        if ($mahallaName) {
            $mahallaId = $this->findOrCreateMahalla($mahallaName, $tumanId);
        }

        // Parse dates
        $auctionDate = $this->parseDate($row[17]);
        $contractDate = $this->parseDate($row[27]);

        // Create lot
        $lot = Lot::create([
            'lot_number' => $lotNumber,
            'tuman_id' => $tumanId,
            'mahalla_id' => $mahallaId,
            'address' => $row[3] ?? null,
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

        // Import distributions (columns 36-43)
        $this->importDistributions($lot, $row);

        // Import payment schedules (columns from 44 onwards)
        $this->importPaymentSchedules($lot, $row);

        return $lot;
    }

    private function importDistributions($lot, $row)
    {
        // Columns 36-39: Allocated amounts
        $allocatedAmounts = [
            'local_budget' => $this->parseDecimal($row[36] ?? 0),
            'development_fund' => $this->parseDecimal($row[37] ?? 0),
            'new_uzbekistan' => $this->parseDecimal($row[38] ?? 0),
            'district_authority' => $this->parseDecimal($row[39] ?? 0),
        ];

        // Columns 40-43: Distributed amounts (already distributed)
        $distributedAmounts = [
            'local_budget' => $this->parseDecimal($row[40] ?? 0),
            'development_fund' => $this->parseDecimal($row[41] ?? 0),
            'new_uzbekistan' => $this->parseDecimal($row[42] ?? 0),
            'district_authority' => $this->parseDecimal($row[43] ?? 0),
        ];

        // Columns 44-47: Remaining amounts
        $remainingAmounts = [
            'local_budget' => $this->parseDecimal($row[44] ?? 0),
            'development_fund' => $this->parseDecimal($row[45] ?? 0),
            'new_uzbekistan' => $this->parseDecimal($row[46] ?? 0),
            'district_authority' => $this->parseDecimal($row[47] ?? 0),
        ];

        foreach ($allocatedAmounts as $category => $amount) {
            if ($amount > 0 || $distributedAmounts[$category] > 0 || $remainingAmounts[$category] > 0) {
                Distribution::create([
                    'lot_id' => $lot->id,
                    'category' => $category,
                    'allocated_amount' => $distributedAmounts[$category] ?? 0,
                    'remaining_amount' => $remainingAmounts[$category] ?? 0,
                ]);
            }
        }
    }

    private function importPaymentSchedules($lot, $row)
    {
        // Payment schedules start from column 49 (index 48)
        // Years: 2024, 2025, 2026, 2027, 2028, 2029
        // Each year has 12 months

        $years = [2024, 2025, 2026, 2027, 2028, 2029];
        $months = ['август', 'сентябрь', 'октябрь', 'ноябрь', 'декабрь',
                   'январь', 'февраль', 'март', 'аперль', 'май', 'июнь', 'июль'];

        $colIndex = 49; // Starting column for payments (after contract amount column 48)

        foreach ($years as $year) {
            foreach ($months as $monthIndex => $month) {
                if (isset($row[$colIndex])) {
                    $amount = $this->parseDecimal($row[$colIndex]);

                    if ($amount > 0) {
                        // Determine actual month number
                        $monthNum = ($monthIndex + 8) % 12;
                        if ($monthNum == 0) $monthNum = 12;

                        // Adjust year if month wraps around
                        $actualYear = $year;
                        if ($monthIndex < 5) {
                            $actualYear = $year - 1;
                        }

                        try {
                            PaymentSchedule::create([
                                'lot_id' => $lot->id,
                                'year' => $actualYear,
                                'month' => $month,
                                'payment_date' => date('Y-m-d', strtotime("{$actualYear}-{$monthNum}-01")),
                                'planned_amount' => $amount,
                                'actual_amount' => 0,
                                'difference' => -$amount,
                                'payment_frequency' => 'monthly',
                            ]);
                        } catch (\Exception $e) {
                            // Skip invalid dates
                        }
                    }
                }
                $colIndex++;
            }
        }
    }

    // Helper methods
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

    private function findOrCreateMahalla($mahallaName, $tumanId)
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
        return $mahalla->id;
    }

    private function cleanNumber($value)
    {
        if (empty($value)) return null;
        // Remove commas and spaces
        return str_replace([',', ' '], '', $value);
    }

    private function parseDecimal($value)
    {
        if (empty($value)) return 0;

        // Remove commas and spaces
        $cleaned = str_replace([',', ' '], ['', ''], $value);

        return is_numeric($cleaned) ? floatval($cleaned) : 0;
    }

    private function parseDate($value)
    {
        if (empty($value)) return null;

        try {
            // Check if it's an Excel date serial number
            if (is_numeric($value)) {
                return Date::excelToDateTimeObject($value)->format('Y-m-d');
            }

            // Try parsing as string date
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

// Helper function for transliteration
function transliterate($text) {
    $cyrillic = ['а', 'б', 'в', 'г', 'д', 'е', 'ё', 'ж', 'з', 'и', 'й', 'к', 'л', 'м', 'н', 'о', 'п', 'р', 'с', 'т', 'у', 'ф', 'х', 'ц', 'ч', 'ш', 'щ', 'ъ', 'ы', 'ь', 'э', 'ю', 'я', 'ў', 'қ', 'ғ', 'ҳ'];
    $latin = ['a', 'b', 'v', 'g', 'd', 'e', 'yo', 'zh', 'z', 'i', 'y', 'k', 'l', 'm', 'n', 'o', 'p', 'r', 's', 't', 'u', 'f', 'h', 'ts', 'ch', 'sh', 'sch', '', 'y', '', 'e', 'yu', 'ya', 'o', 'q', 'g', 'h'];

    return str_replace($cyrillic, $latin, mb_strtolower($text));
}
