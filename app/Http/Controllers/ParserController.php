<?php

namespace App\Http\Controllers;

use App\Models\Lot;
use App\Models\Tuman;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ParserController extends Controller
{
    private $baseUrl = 'https://e-auksion.uz';

    /**
     * Show parser interface
     */
    public function index()
    {
        return view('parser.index');
    }

    /**
     * Parse single lot by ID
     */
    public function parseSingleLot(Request $request)
    {
        $lotId = $request->input('lot_id');

        if (!$lotId) {
            return response()->json(['error' => 'lot_id required'], 400);
        }

        try {
            Log::info("=== Starting to parse lot: {$lotId} ===");

            $lotData = $this->fetchLotDetails($lotId);

            if (empty($lotData)) {
                Log::error("Failed to parse lot {$lotId} - empty data returned");
                return response()->json(['error' => 'Failed to parse lot'], 500);
            }

            Log::info("Lot data parsed successfully", $lotData);

            // Save to database
            $saved = $this->saveToDatabase([$lotData]);

            return response()->json([
                'success' => true,
                'lot' => $lotData,
                'saved' => $saved > 0
            ]);

        } catch (\Exception $e) {
            Log::error("Exception in parseSingleLot: " . $e->getMessage());
            Log::error("Stack trace: " . $e->getTraceAsString());

            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Fetch and parse lot detail page
     */
    private function fetchLotDetails($lotId)
    {
        $url = "{$this->baseUrl}/lot-view?lot_id={$lotId}";

        Log::info("Fetching URL: {$url}");

        $response = Http::timeout(30)->withHeaders([
            'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36',
            'Accept' => 'text/html,application/xhtml+xml',
        ])->get($url);

        if ($response->failed()) {
            Log::error("HTTP request failed for lot {$lotId}, status: " . $response->status());
            return [];
        }

        $html = $response->body();
        Log::info("HTML fetched, length: " . strlen($html));

        return $this->parseLotDetailPage($html, $lotId);
    }

    /**
     * Parse lot detail page
     */
    private function parseLotDetailPage($html, $lotId)
    {
        $lot = ['lot_number' => $lotId];

        Log::info("Starting to parse HTML for lot {$lotId}");

        try {
            libxml_use_internal_errors(true);
            $dom = new \DOMDocument();

            // Try to load HTML
            $loaded = $dom->loadHTML(mb_convert_encoding($html, 'HTML-ENTITIES', 'UTF-8'));

            if (!$loaded) {
                Log::error("Failed to load HTML into DOMDocument");
                $errors = libxml_get_errors();
                foreach ($errors as $error) {
                    Log::error("libxml error: " . $error->message);
                }
                libxml_clear_errors();
                return $lot;
            }

            libxml_clear_errors();

            $xpath = new \DOMXPath($dom);
            Log::info("DOMXPath created successfully");

            // Extract lot title/name - try multiple selectors
            $titleSelectors = [
                "//h1",
                "//div[contains(@class, 'lot-title')]",
                "//div[contains(@class, 'title')]"
            ];

            foreach ($titleSelectors as $selector) {
                $titleNode = $xpath->query($selector);
                if ($titleNode && $titleNode->length > 0) {
                    $lot['property_name'] = trim($titleNode->item(0)->textContent);
                    Log::info("Found title: " . $lot['property_name']);
                    break;
                }
            }

            // Extract prices and dates with better error handling
            $this->safeExtractInfoBlock($xpath, $lot, "Boshlang'ich narxi", 'initial_price', 'price');
            $this->safeExtractInfoBlock($xpath, $lot, "Zakalat puli", 'deposit_amount', 'price');
            $this->safeExtractInfoBlock($xpath, $lot, "Savdo vaqti", 'auction_date', 'date');

            // Extract from table
            $tableFields = [
                'land_area' => ['maydoni', 'площадь'],
                'object_type' => ['ob\'ektlar', 'объект'],
                'unique_number' => ['kadastr', 'кадастр'],
            ];

            foreach ($tableFields as $key => $keywords) {
                foreach ($keywords as $keyword) {
                    $value = $this->safeExtractTableField($xpath, $keyword);
                    if ($value) {
                        $lot[$key] = $value;
                        Log::info("Extracted {$key}: {$value}");
                        break;
                    }
                }
            }

            // Extract coordinates from map link
            $mapLinks = $xpath->query("//a[contains(@href, 'maps.google') or contains(@href, 'maps')]");
            Log::info("Found {$mapLinks->length} map links");

            if ($mapLinks && $mapLinks->length > 0) {
                $href = $mapLinks->item(0)->getAttribute('href');
                Log::info("Map link href: {$href}");

                if (preg_match('/q=([\d.]+),([\d.]+)/', $href, $matches)) {
                    $lot['latitude'] = $matches[1];
                    $lot['longitude'] = $matches[2];
                    $lot['location_url'] = $href;
                    Log::info("Extracted coordinates: {$matches[1]}, {$matches[2]}");
                }
            }

            Log::info("Parsed lot data", $lot);

        } catch (\Exception $e) {
            Log::error("Exception in parseLotDetailPage: " . $e->getMessage());
            Log::error("Stack trace: " . $e->getTraceAsString());
        }

        return $lot;
    }

    /**
     * Safely extract info block
     */
    private function safeExtractInfoBlock($xpath, &$lot, $label, $key, $type = 'text')
    {
        try {
            Log::info("Trying to extract {$key} with label: {$label}");

            // Try multiple query patterns
            $patterns = [
                "//p[contains(text(), '{$label}')]",
                "//div[contains(text(), '{$label}')]",
                "//span[contains(text(), '{$label}')]",
                "//label[contains(text(), '{$label}')]",
            ];

            foreach ($patterns as $pattern) {
                $labelNode = $xpath->query($pattern);

                if (!$labelNode || $labelNode->length === 0) {
                    continue;
                }

                Log::info("Found label node for {$key} using pattern: {$pattern}");

                // Try to find value in next sibling
                $valuePatterns = [
                    "./following-sibling::p[1]",
                    "./following-sibling::div[1]",
                    "./following-sibling::span[1]",
                    "./../following-sibling::*[1]",
                ];

                foreach ($valuePatterns as $valuePattern) {
                    $valueNode = $xpath->query($valuePattern, $labelNode->item(0));

                    if ($valueNode && $valueNode->length > 0) {
                        $value = trim($valueNode->item(0)->textContent);
                        Log::info("Found value for {$key}: {$value}");

                        if ($type === 'price') {
                            $cleaned = preg_replace('/[^\d.]/', '', $value);
                            $lot[$key] = $cleaned ? (float) $cleaned : null;
                            Log::info("Converted price {$key}: " . $lot[$key]);
                        } elseif ($type === 'date') {
                            if (preg_match('/(\d{2})\.(\d{2})\.(\d{4})\s+(\d{2}):(\d{2})/', $value, $matches)) {
                                try {
                                    $lot[$key] = Carbon::createFromFormat('d.m.Y H:i', "{$matches[1]}.{$matches[2]}.{$matches[3]} {$matches[4]}:{$matches[5]}");
                                    Log::info("Converted date {$key}: " . $lot[$key]);
                                } catch (\Exception $e) {
                                    Log::warning("Failed to parse date: {$value}");
                                }
                            }
                        } else {
                            $lot[$key] = $value;
                        }

                        return;
                    }
                }
            }

            Log::info("Could not extract {$key}");

        } catch (\Exception $e) {
            Log::error("Error in safeExtractInfoBlock for {$key}: " . $e->getMessage());
        }
    }

    /**
     * Safely extract table field
     */
    private function safeExtractTableField($xpath, $keyword)
    {
        try {
            Log::info("Trying to extract table field with keyword: {$keyword}");

            $patterns = [
                "//td[contains(translate(text(), 'ABCDEFGHIJKLMNOPQRSTUVWXYZАБВГДЕЁЖЗИЙКЛМНОПРСТУФХЦЧШЩЪЫЬЭЮЯ', 'abcdefghijklmnopqrstuvwxyzабвгдеёжзийклмнопрстуфхцчшщъыьэюя'), '{$keyword}')]/following-sibling::td[1]",
                "//th[contains(translate(text(), 'ABCDEFGHIJKLMNOPQRSTUVWXYZАБВГДЕЁЖЗИЙКЛМНОПРСТУФХЦЧШЩЪЫЬЭЮЯ', 'abcdefghijklmnopqrstuvwxyzабвгдеёжзийклмнопрстуфхцчшщъыьэюя'), '{$keyword}')]/following-sibling::td[1]",
                "//td[contains(text(), '{$keyword}')]/following-sibling::td[1]",
            ];

            foreach ($patterns as $pattern) {
                $node = $xpath->query($pattern);

                if ($node && $node->length > 0) {
                    $value = trim($node->item(0)->textContent);
                    Log::info("Found table value for {$keyword}: {$value}");
                    return $value;
                }
            }

            Log::info("Could not find table field for: {$keyword}");
            return null;

        } catch (\Exception $e) {
            Log::error("Error in safeExtractTableField for {$keyword}: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Save lots to database
     */
    private function saveToDatabase($lots)
    {
        $saved = 0;

        foreach ($lots as $lotData) {
            try {
                Log::info("Attempting to save lot: {$lotData['lot_number']}");

                DB::beginTransaction();

                // Get or create default tuman
                $tuman = Tuman::first();

                if (!$tuman) {
                    Log::info("No tuman found, creating default");
                    $tuman = Tuman::create([
                        'name_uz' => 'Тошкент шаҳар',
                        'name_ru' => 'Город Ташкент'
                    ]);
                }

                // Prepare lot record
                $lotRecord = [
                    'lot_number' => $lotData['lot_number'],
                    'tuman_id' => $tuman->id,
                    'address' => $lotData['address'] ?? $lotData['full_address'] ?? null,
                    'unique_number' => $lotData['unique_number'] ?? null,
                    'zone' => $lotData['zone'] ?? null,
                    'latitude' => $lotData['latitude'] ?? null,
                    'longitude' => $lotData['longitude'] ?? null,
                    'location_url' => $lotData['location_url'] ?? null,
                    'land_area' => isset($lotData['land_area']) ? $this->parseLandArea($lotData['land_area']) : null,
                    'object_type' => $lotData['object_type'] ?? null,
                    'initial_price' => $lotData['initial_price'] ?? 0,
                    'auction_date' => $lotData['auction_date'] ?? null,
                    'auction_type' => $lotData['auction_type'] ?? 'ochiq',
                    'lot_status' => 'active',
                    'payment_type' => 'muddatli',
                ];

                Log::info("Lot record prepared", $lotRecord);

                // Update or create
                $lot = Lot::updateOrCreate(
                    ['lot_number' => $lotData['lot_number']],
                    $lotRecord
                );

                Log::info("Lot saved with ID: " . $lot->id);

                DB::commit();
                $saved++;

            } catch (\Exception $e) {
                DB::rollBack();
                Log::error("Error saving lot {$lotData['lot_number']}: " . $e->getMessage());
                Log::error("Stack trace: " . $e->getTraceAsString());
            }
        }

        Log::info("Total lots saved: {$saved}");
        return $saved;
    }

    /**
     * Parse land area from string
     */
    private function parseLandArea($areaString)
    {
        if (!$areaString) {
            return null;
        }

        // Extract numeric value
        $cleaned = preg_replace('/[^\d.]/', '', $areaString);
        return $cleaned ? (float) $cleaned : null;
    }
}
