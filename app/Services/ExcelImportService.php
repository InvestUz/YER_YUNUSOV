<?php

namespace App\Services;

use App\Models\Lot;
use App\Models\Tuman;
use App\Models\Mahalla;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\IOFactory;

class ExcelImportService
{
    public function importFromExcel($filePath)
    {
        $spreadsheet = IOFactory::load($filePath);
        $worksheet = $spreadsheet->getActiveSheet();
        $rows = $worksheet->toArray();

        // Skip header row
        $headers = array_shift($rows);

        $imported = 0;
        $errors = [];

        DB::beginTransaction();
        try {
            foreach ($rows as $index => $row) {
                try {
                    $this->importRow($row, $index + 2); // +2 because of header and 0-index
                    $imported++;
                } catch (\Exception $e) {
                    $errors[] = "Row " . ($index + 2) . ": " . $e->getMessage();
                }
            }

            if (count($errors) > 10) {
                DB::rollBack();
                return [
                    'success' => false,
                    'message' => 'Too many errors. Import cancelled.',
                    'errors' => array_slice($errors, 0, 10)
                ];
            }

            DB::commit();
            return [
                'success' => true,
                'imported' => $imported,
                'errors' => $errors
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            return [
                'success' => false,
                'message' => 'Import failed: ' . $e->getMessage(),
                'errors' => $errors
            ];
        }
    }

    private function importRow($row, $rowNumber)
    {
        // Map columns based on your Excel structure
        // Column indexes from the provided data
        $lotNumber = $row[1] ?? null; // Лот рақами
        $tumanName = $row[2] ?? null; // Туман
        $address = $row[3] ?? null; // Ер манзили
        $uniqueNumber = $row[4] ?? null; // Уникал рақами
        $zone = $row[5] ?? null; // Зона
        $locationUrl = $row[8] ?? null; // Локация
        $masterPlanZone = $row[9] ?? null; // Бош режа
        $yangiUzbekiston = $row[10] ?? null; // Янги Ўзбекистон
        $landArea = $this->parseNumber($row[11] ?? null); // Ер майдони
        $objectType = $row[12] ?? null;
        $objectTypeRu = $row[13] ?? null;
        $constructionArea = $this->parseNumber($row[14] ?? null);
        $investmentAmount = $this->parseNumber($row[15] ?? null);
        $initialPrice = $this->parseNumber($row[16] ?? null);
        $auctionDate = $this->parseDate($row[17] ?? null);
        $soldPrice = $this->parseNumber($row[18] ?? null);
        $winnerType = $row[19] ?? null;
        $winnerName = $row[20] ?? null;
        $winnerPhone = $row[21] ?? null;
        $paymentType = $this->parsePaymentType($row[22] ?? null);
        $basis = $row[23] ?? null;
        $auctionType = $this->parseAuctionType($row[24] ?? null);
        $lotStatus = $row[25] ?? 'active';
        $contractSigned = ($row[26] ?? '') === 'шартнома тузилган';
        $contractDate = $this->parseDate($row[27] ?? null);
        $contractNumber = $row[28] ?? null;
        $paidAmount = $this->parseNumber($row[29] ?? null);
        $transferredAmount = $this->parseNumber($row[30] ?? null);
        $discount = $this->parseNumber($row[31] ?? null);

        if (!$lotNumber || !$tumanName) {
            throw new \Exception("Missing required fields: lot_number or tuman");
        }

        // Find or create Tuman
        $tuman = Tuman::where('name_uz', 'like', "%{$tumanName}%")->first();
        if (!$tuman) {
            throw new \Exception("Tuman not found: {$tumanName}");
        }

        // Create or update lot
        $lot = Lot::updateOrCreate(
            ['lot_number' => $lotNumber],
            [
                'tuman_id' => $tuman->id,
                'address' => $address,
                'unique_number' => $uniqueNumber,
                'zone' => $zone,
                'location_url' => $locationUrl,
                'master_plan_zone' => $masterPlanZone,
                'yangi_uzbekiston' => $this->parseBool($yangiUzbekiston),
                'land_area' => $landArea,
                'object_type' => $objectType,
                'object_type_ru' => $objectTypeRu,
                'construction_area' => $constructionArea,
                'investment_amount' => $investmentAmount,
                'initial_price' => $initialPrice,
                'auction_date' => $auctionDate,
                'sold_price' => $soldPrice,
                'winner_type' => $winnerType,
                'winner_name' => $winnerName,
                'winner_phone' => $winnerPhone,
                'payment_type' => $paymentType,
                'basis' => $basis,
                'auction_type' => $auctionType,
                'lot_status' => $lotStatus,
                'contract_signed' => $contractSigned,
                'contract_date' => $contractDate,
                'contract_number' => $contractNumber,
                'paid_amount' => $paidAmount,
                'transferred_amount' => $transferredAmount ?? $soldPrice,
                'discount' => $discount,
            ]
        );

        // Auto-calculate
        $lot->autoCalculate();

        return $lot;
    }

    private function parseNumber($value)
    {
        if (empty($value)) return null;

        // Remove spaces and commas
        $cleaned = str_replace([' ', ','], ['', '.'], $value);
        return is_numeric($cleaned) ? floatval($cleaned) : null;
    }

    private function parseDate($value)
    {
        if (empty($value)) return null;

        try {
            // Try different date formats
            if (is_numeric($value)) {
                // Excel date serial number
                $unix = ($value - 25569) * 86400;
                return date('Y-m-d', $unix);
            }

            return date('Y-m-d', strtotime($value));
        } catch (\Exception $e) {
            return null;
        }
    }

    private function parseBool($value)
    {
        if (empty($value)) return false;

        $value = strtolower(trim($value));
        return in_array($value, ['янги ўзбекистон', 'yes', '1', 'true', 'ha']);
    }

    private function parsePaymentType($value)
    {
        if (empty($value)) return null;

        $value = strtolower(trim($value));
        return str_contains($value, 'муддатли эмас') ? 'muddatli_emas' : 'muddatli';
    }

    private function parseAuctionType($value)
    {
        if (empty($value)) return null;

        $value = strtolower(trim($value));
        return str_contains($value, 'ёпиқ') ? 'yopiq' : 'ochiq';
    }
}
