{{--
    File: resources/views/lots/partials/distribution-table.blade.php
    Purpose: Display payment distribution across different budgets from DATABASE
--}}

@if($lot->contract && $lot->contract->distributions->count() > 0)
@php
// Calculate totals for each category from database
$distributions = $lot->contract->distributions;

$cityBudget = $distributions->where('category', 'city_budget')->sum('allocated_amount');
$developmentFund = $distributions->where('category', 'development_fund')->sum('allocated_amount');
$shaykhontohurBudget = $distributions->where('category', 'shaykhontohur_budget')->sum('allocated_amount');
$newUzbekistan = $distributions->where('category', 'new_uzbekistan')->sum('allocated_amount');
$yangikhatyotTechnopark = $distributions->where('category', 'yangikhayot_technopark')->sum('allocated_amount');
$kszDirectorates = $distributions->where('category', 'ksz_directorates')->sum('allocated_amount');
$tashkentCityDirectorate = $distributions->where('category', 'tashkent_city_directorate')->sum('allocated_amount');
$districtBudgets = $distributions->where('category', 'district_budgets')->sum('allocated_amount');

$totalDistributed = $distributions->sum('allocated_amount');

// Group by year if distribution_date exists
$distributionsByYear = $distributions->groupBy(function($dist) {
return $dist->distribution_date ? $dist->distribution_date->format('Y') : 'Номаълум';
});
@endphp

<div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
    {{-- Header --}}
    <div class="bg-gradient-to-r from-purple-600 to-purple-700 text-white px-6 py-4 flex justify-between items-center">
        <div>
            <h2 class="font-bold text-lg flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                </svg>
                ТАҚСИМОТ ЖАДВАЛИ
            </h2>
            <p class="text-sm text-purple-100 mt-1">Тўловларнинг бюджетлар бўйича тақсимланиши</p>
        </div>

        {{-- Add Distribution Button --}}
        @if(Auth::check() && (Auth::user()->role === 'admin' || Auth::user()->tuman_id === $lot->tuman_id))
        <a href="{{ route('distributions.create', ['contract_id' => $lot->contract->id]) }}"
            class="bg-white text-purple-600 px-4 py-2 rounded-lg text-sm font-medium hover:bg-purple-50 transition-colors shadow-sm flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
            Тақсимлаш
        </a>
        @endif
    </div>

    {{-- Distribution Table --}}
    <div class="overflow-x-auto">
        <table class="min-w-full text-xs border-collapse">
            <thead>
                <tr class="bg-gray-100 text-gray-700">
                    <th rowspan="2" class="px-4 py-3 border font-semibold text-center">№</th>
                    <th rowspan="2" class="px-4 py-3 border font-semibold text-center min-w-[100px]">Йил/Сана</th>
                    <th rowspan="2" class="px-4 py-3 border font-semibold text-center">Жами тақсимланган</th>
                    <th colspan="8" class="px-4 py-3 border text-center font-semibold bg-blue-50">Тақсимот бўйича</th>
                    <th rowspan="2" class="px-4 py-3 border font-semibold text-center">Ҳолат</th>
                </tr>
                <tr class="bg-gray-50 text-gray-600">
                    <th class="px-3 py-2 border text-xs">Тошкент ш. бюджети</th>
                    <th class="px-3 py-2 border text-xs">Жамғарма</th>
                    <th class="px-3 py-2 border text-xs">Шайхонтоҳур т. бюджети</th>
                    <th class="px-3 py-2 border text-xs">Янги Ўзбекистон</th>
                    <th class="px-3 py-2 border text-xs">Янгиҳаёт технопарк</th>
                    <th class="px-3 py-2 border text-xs">КСЗ дирекциялари</th>
                    <th class="px-3 py-2 border text-xs">Тошкент сити</th>
                    <th class="px-3 py-2 border text-xs">Туманлар бюджети</th>
                </tr>
            </thead>
            <tbody>
                {{-- Total Row --}}
                <tr class="bg-gradient-to-r from-blue-50 to-blue-100 font-bold text-gray-900">
                    <td class="px-4 py-3 border text-center" colspan="2">ЖАМИ</td>
                    <td class="px-4 py-3 border text-right text-blue-700">
                        {{ number_format($totalDistributed, 0, '.', ' ') }}
                    </td>
                    <td class="px-4 py-3 border text-right">{{ number_format($cityBudget, 0, '.', ' ') }}</td>
                    <td class="px-4 py-3 border text-right">{{ number_format($developmentFund, 0, '.', ' ') }}</td>
                    <td class="px-4 py-3 border text-right">{{ number_format($shaykhontohurBudget, 0, '.', ' ') }}</td>
                    <td class="px-4 py-3 border text-right">{{ number_format($newUzbekistan, 0, '.', ' ') }}</td>
                    <td class="px-4 py-3 border text-right">{{ number_format($yangikhatyotTechnopark, 0, '.', ' ') }}</td>
                    <td class="px-4 py-3 border text-right">{{ number_format($kszDirectorates, 0, '.', ' ') }}</td>
                    <td class="px-4 py-3 border text-right">{{ number_format($tashkentCityDirectorate, 0, '.', ' ') }}</td>
                    <td class="px-4 py-3 border text-right">{{ number_format($districtBudgets, 0, '.', ' ') }}</td>
                    <td class="px-4 py-3 border text-center">-</td>
                </tr>

                {{-- Individual Distribution Rows --}}
                @forelse($distributions->sortBy('distribution_date') as $index => $distribution)
                <tr class="hover:bg-blue-50 transition-colors {{ $distribution->status === 'distributed' ? 'bg-green-50' : '' }}">
                    <td class="px-4 py-3 border text-center font-medium">{{ $index + 1 }}</td>
                    <td class="px-4 py-3 border text-center">
                        {{ $distribution->distribution_date ? $distribution->distribution_date->format('d.m.Y') : '-' }}
                    </td>
                    <td class="px-4 py-3 border text-right font-semibold">
                        {{ number_format($distribution->allocated_amount, 0, '.', ' ') }}
                    </td>

                    {{-- Show amount in the correct category column --}}
                    <td class="px-4 py-3 border text-right {{ $distribution->category === 'city_budget' ? 'bg-blue-100 font-semibold' : '' }}">
                        {{ $distribution->category === 'city_budget' ? number_format($distribution->allocated_amount, 0, '.', ' ') : '-' }}
                    </td>
                    <td class="px-4 py-3 border text-right {{ $distribution->category === 'development_fund' ? 'bg-blue-100 font-semibold' : '' }}">
                        {{ $distribution->category === 'development_fund' ? number_format($distribution->allocated_amount, 0, '.', ' ') : '-' }}
                    </td>
                    <td class="px-4 py-3 border text-right {{ $distribution->category === 'shaykhontohur_budget' ? 'bg-blue-100 font-semibold' : '' }}">
                        {{ $distribution->category === 'shaykhontohur_budget' ? number_format($distribution->allocated_amount, 0, '.', ' ') : '-' }}
                    </td>
                    <td class="px-4 py-3 border text-right {{ $distribution->category === 'new_uzbekistan' ? 'bg-blue-100 font-semibold' : '' }}">
                        {{ $distribution->category === 'new_uzbekistan' ? number_format($distribution->allocated_amount, 0, '.', ' ') : '-' }}
                    </td>
                    <td class="px-4 py-3 border text-right {{ $distribution->category === 'yangikhayot_technopark' ? 'bg-blue-100 font-semibold' : '' }}">
                        {{ $distribution->category === 'yangikhayot_technopark' ? number_format($distribution->allocated_amount, 0, '.', ' ') : '-' }}
                    </td>
                    <td class="px-4 py-3 border text-right {{ $distribution->category === 'ksz_directorates' ? 'bg-blue-100 font-semibold' : '' }}">
                        {{ $distribution->category === 'ksz_directorates' ? number_format($distribution->allocated_amount, 0, '.', ' ') : '-' }}
                    </td>
                    <td class="px-4 py-3 border text-right {{ $distribution->category === 'tashkent_city_directorate' ? 'bg-blue-100 font-semibold' : '' }}">
                        {{ $distribution->category === 'tashkent_city_directorate' ? number_format($distribution->allocated_amount, 0, '.', ' ') : '-' }}
                    </td>
                    <td class="px-4 py-3 border text-right {{ $distribution->category === 'district_budgets' ? 'bg-blue-100 font-semibold' : '' }}">
                        {{ $distribution->category === 'district_budgets' ? number_format($distribution->allocated_amount, 0, '.', ' ') : '-' }}
                    </td>

                    {{-- Status Badge --}}
                    <td class="px-4 py-3 border text-center">
                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium
                            {{ $distribution->status === 'distributed' ? 'bg-green-100 text-green-800' : 
                               ($distribution->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') }}">
                            {{ $distribution->status_label }}
                        </span>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="12" class="px-4 py-8 text-center text-gray-500">
                        <svg class="w-16 h-16 mx-auto text-gray-400 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        <p class="font-medium text-gray-700 mb-1">Тақсимот маълумотлари топилмади</p>
                        @if(Auth::check() && (Auth::user()->role === 'admin' || Auth::user()->tuman_id === $lot->tuman_id))
                        <p class="text-sm">Юқоридаги "Тақсимлаш" тугмасини босинг.</p>
                        @endif
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Statistics Footer --}}
    <div class="px-6 py-4 bg-gradient-to-r from-gray-50 to-gray-100 border-t border-gray-200">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
            <div class="flex items-center justify-between p-3 bg-white rounded-lg border border-gray-200">
                <span class="text-xs text-gray-600">Шартнома суммаси:</span>
                <span class="text-sm font-bold text-gray-900">{{ number_format($lot->contract->contract_amount, 0, '.', ' ') }} сўм</span>
            </div>
            <div class="flex items-center justify-between p-3 bg-white rounded-lg border border-gray-200">
                <span class="text-xs text-gray-600">Жами тақсимланган:</span>
                <span class="text-sm font-bold text-blue-700">{{ number_format($totalDistributed, 0, '.', ' ') }} сўм</span>
            </div>
            <div class="flex items-center justify-between p-3 bg-white rounded-lg border border-gray-200">
                <span class="text-xs text-gray-600">Тақсимланмаган:</span>
                <span class="text-sm font-bold text-orange-700">{{ number_format($lot->contract->contract_amount - $totalDistributed, 0, '.', ' ') }} сўм</span>
            </div>
        </div>

        <div class="flex items-start gap-2 text-xs text-gray-600">
            <svg class="w-4 h-4 text-blue-500 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
            </svg>
            <div>
                <p class="font-medium text-gray-700 mb-1">Эслатма:</p>
                <p>Барча маблағлар сўмда кўрсатилган. Тақсимот Тошкент шаҳар ҳокимлиги қарори асосида амалга оширилади.</p>
            </div>
        </div>
    </div>
</div>
@endif