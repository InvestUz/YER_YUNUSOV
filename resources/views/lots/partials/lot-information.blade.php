{{--
    File: resources/views/lots/partials/lot-information.blade.php
    Purpose: Display lot details and auction information in tables
--}}

<div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">

    {{-- ========================================
         LAND INFORMATION SECTION
         ======================================== --}}
    <div class="px-6 py-4 bg-gradient-to-r from-blue-50 to-blue-100 border-b border-blue-200">
        <h2 class="text-lg font-bold text-gray-900 flex items-center gap-2">
            <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7" />
            </svg>
            {{ $lot->unique_number ?? '-' }} ер участкасининг маълумотлари
        </h2>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <tbody class="divide-y divide-gray-200">

                {{-- District --}}
                <tr class="hover:bg-blue-50 transition-colors">
                    <td class="py-3 px-4 text-gray-600 font-medium w-1/3">Туман</td>
                    <td class="py-3 px-4 text-gray-900">{{ optional($lot->tuman)->name_uz ?? '-' }}</td>
                </tr>

                {{-- Address --}}
                <tr class="hover:bg-blue-50 transition-colors">
                    <td class="py-3 px-4 text-gray-600 font-medium">Ер манзили</td>
                    <td class="py-3 px-4 text-gray-900">{{ $lot->address ?? '-' }}</td>
                </tr>

                {{-- Land Area --}}
                <tr class="hover:bg-blue-50 transition-colors">
                    <td class="py-3 px-4 text-gray-600 font-medium">Ер майдони</td>
                    <td class="py-3 px-4 text-gray-900 font-semibold">
                        {{ $lot->land_area ? number_format($lot->land_area, 4) . ' га' : '-' }}
                    </td>
                </tr>

                {{-- Zone --}}
                <tr class="hover:bg-blue-50 transition-colors">
                    <td class="py-3 px-4 text-gray-600 font-medium">Зона</td>
                    <td class="py-3 px-4 text-gray-900">{{ $lot->zone ?? '-' }}</td>
                </tr>

                {{-- Master Plan Zone --}}
                <tr class="hover:bg-blue-50 transition-colors">
                    <td class="py-3 px-4 text-gray-600 font-medium">Бош режа бўйича жойлашув зонаси</td>
                    <td class="py-3 px-4 text-gray-900">{{ $lot->master_plan_zone ?? '-' }}</td>
                </tr>

            </tbody>
        </table>
    </div>

    {{-- ========================================
         AUCTION INFORMATION SECTION
         ======================================== --}}
    <div class="px-6 py-4 bg-gradient-to-r from-green-50 to-green-100 border-b border-green-200 mt-6">
        <h2 class="text-lg font-bold text-gray-900 flex items-center gap-2">
            <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            Аукцион маълумотлари
        </h2>
    </div>

    <table class="w-full text-sm">
        <tbody class="divide-y divide-gray-200">

            {{-- Lot Number --}}
            <tr class="hover:bg-green-50 transition-colors">
                <td class="py-3 px-4 text-gray-600 font-medium w-1/3">Лот рақами</td>
                <td class="py-3 px-4 text-gray-900">{{ $lot->lot_number ?? '-' }}</td>
            </tr>

            {{-- Initial Price (Highlighted) --}}
            <tr class="bg-blue-50 hover:bg-blue-100 transition-colors">
                <td class="py-3 px-4 text-gray-700 font-bold">Ғолиб аукционга тўлаган сумма</td>
                <td class="py-3 px-4 text-blue-700 font-bold text-base">
{{ $lot->contract?->initial_paid_amount ? number_format($lot->contract->initial_paid_amount, 2, '.', ' ') . ' UZS' : '-' }}
                </td>
            </tr>

            {{-- Auction Date --}}
            <tr class="hover:bg-green-50 transition-colors">
                <td class="py-3 px-4 text-gray-600 font-medium">Аукцион санаси</td>
                <td class="py-3 px-4 text-gray-900">
                    {{ $lot->auction_date ? $lot->auction_date->format('d.m.Y') : '-' }}
                </td>
            </tr>

            {{-- Sold Price (Highlighted) --}}
            <tr class="bg-green-50 hover:bg-green-100 transition-colors">
                <td class="py-3 px-4 text-gray-700 font-bold">Сотилган нархи</td>
                <td class="py-3 px-4 text-green-700 font-bold text-base">
                    {{ $lot->sold_price ? number_format($lot->sold_price, 2, '.', ' ') . ' UZS' : '-' }}
                </td>
            </tr>

            {{-- Winner Name --}}
            <tr class="hover:bg-green-50 transition-colors">
                <td class="py-3 px-4 text-gray-600 font-medium">Аукцион ғолиби</td>
                <td class="py-3 px-4 text-gray-900 font-semibold">{{ $lot->winner_name ?? '-' }}</td>
            </tr>

                        {{-- Winner Phone --}}
            <tr class="hover:bg-green-50 transition-colors">
                <td class="py-3 px-4 text-gray-600 font-medium">Аукцион холати</td>
                <td class="py-3 px-4 text-gray-900">{{ $lot->auction_type ?? '-' }}</td>
            </tr>

            {{-- Winner Phone --}}
            <tr class="hover:bg-green-50 transition-colors">
                <td class="py-3 px-4 text-gray-600 font-medium">Телефон рақами</td>
                <td class="py-3 px-4 text-gray-900">{{ $lot->winner_phone ?? '-' }}</td>
            </tr>

            {{-- Contract Status --}}
    {{-- Contract Status with Form --}}
<tr class="hover:bg-green-50 transition-colors">
    <td class="py-3 px-4 text-gray-600 font-medium">Лот холати</td>
    <td class="py-3 px-4">
        <form action="{{ route('lots.updateStatus', $lot) }}" method="POST" id="statusForm">
            @csrf
            <div class="flex items-center gap-3">
                <select name="lot_status" id="lot_status"
                    class="flex-1 rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 text-sm">
                    <option value="">Танланг</option>
                    <option value="Аукцион/Танлов якунланди"
                        {{ $lot->lot_status == 'Аукцион/Танлов якунланди' ? 'selected' : '' }}>
                        Аукцион/Танлов якунланди
                    </option>
                    <option value="Ғолиб мол мулк қийматини тўлади"
                        {{ $lot->lot_status == 'Ғолиб мол мулк қийматини тўлади' ? 'selected' : '' }}>
                        Ғолиб мол мулк қийматини тўлади
                    </option>
                    <option value="Савдо/танлов натижаларини расмийлаштириш"
                        {{ $lot->lot_status == 'Савдо/танлов натижаларини расмийлаштириш' ? 'selected' : '' }}>
                        Савдо/танлов натижаларини расмийлаштириш
                    </option>
                    <option value="Ғолиб шартнома имзолашга розилик билдирди"
                        {{ $lot->lot_status == 'Ғолиб шартнома имзолашга розилик билдирди' ? 'selected' : '' }}>
                        Ғолиб шартнома имзолашга розилик билдирди
                    </option>
                    <option value="Иштирокчи розилигини кутиш жараёнида"
                        {{ $lot->lot_status == 'Иштирокчи розилигини кутиш жараёнида' ? 'selected' : '' }}>
                        Иштирокчи розилигини кутиш жараёнида
                    </option>
                    <option value="Буюртмачи розилигини кутиш жараёнида"
                        {{ $lot->lot_status == 'Буюртмачи розилигини кутиш жараёнида' ? 'selected' : '' }}>
                        Буюртмачи розилигини кутиш жараёнида
                    </option>
                    <option value="Иштирокчи ва Буюртмачи келишуви ёки Суд қарорини кутиш жараёнида"
                        {{ $lot->lot_status == 'Иштирокчи ва Буюртмачи келишуви ёки Суд қарорини кутиш жараёнида' ? 'selected' : '' }}>
                        Иштирокчи ва Буюртмачи келишуви ёки Суд қарорини кутиш жараёнида
                    </option>
                    <option value="Вактинча тухтатилди"
                        {{ $lot->lot_status == 'Вактинча тухтатилди' ? 'selected' : '' }}>
                        Вактинча тухтатилди
                    </option>
                    <option value="Низоли"
                        {{ $lot->lot_status == 'Низоли' ? 'selected' : '' }}>
                        Низоли
                    </option>
                    <option value="Захирадаги ғолибга таклиф"
                        {{ $lot->lot_status == 'Захирадаги ғолибга таклиф' ? 'selected' : '' }}>
                        Захирадаги ғолибга таклиф
                    </option>
                    <option value="Лот якунланди"
                        {{ $lot->lot_status == 'Лот якунланди' ? 'selected' : '' }}>
                        Лот якунланди
                    </option>
                </select>

                <button type="submit"
                    class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition-colors duration-150 ease-in-out focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                    <svg class="w-4 h-4 inline-block mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    Сақлаш
                </button>
            </div>
        </form>
    </td>
</tr>


            {{-- Payment Type --}}
            <tr class="hover:bg-green-50 transition-colors">
                <td class="py-3 px-4 text-gray-600 font-medium">Тўлов тури</td>
                <td class="py-3 px-4 text-gray-900">
                    @if ($lot->payment_type === 'muddatli')
                        <span
                            class="inline-flex items-center px-3 py-1 bg-blue-100 text-blue-800 border border-blue-300 text-xs font-medium rounded-full">
                            <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z"
                                    clip-rule="evenodd" />
                            </svg>
                            бўлиб тўлаш
                        </span>
                    @elseif($lot->payment_type === 'muddatsiz')
                        <span
                            class="inline-flex items-center px-3 py-1 bg-gray-100 text-gray-800 border border-gray-300 text-xs font-medium rounded-full">
                            <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                <path
                                    d="M8.433 7.418c.155-.103.346-.196.567-.267v1.698a2.305 2.305 0 01-.567-.267C8.07 8.34 8 8.114 8 8c0-.114.07-.34.433-.582zM11 12.849v-1.698c.22.071.412.164.567.267.364.243.433.468.433.582 0 .114-.07.34-.433.582a2.305 2.305 0 01-.567.267z" />
                                <path fill-rule="evenodd"
                                    d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-13a1 1 0 10-2 0v.092a4.535 4.535 0 00-1.676.662C6.602 6.234 6 7.009 6 8c0 .99.602 1.765 1.324 2.246.48.32 1.054.545 1.676.662v1.941c-.391-.127-.68-.317-.843-.504a1 1 0 10-1.51 1.31c.562.649 1.413 1.076 2.353 1.253V15a1 1 0 102 0v-.092a4.535 4.535 0 001.676-.662C13.398 13.766 14 12.991 14 12c0-.99-.602-1.765-1.324-2.246A4.535 4.535 0 0011 9.092V7.151c.391.127.68.317.843.504a1 1 0 101.511-1.31c-.563-.649-1.413-1.076-2.354-1.253V5z"
                                    clip-rule="evenodd" />
                            </svg>
                            бир йўла
                        </span>
                    @else
                        <span class="text-gray-400">-</span>
                    @endif
                </td>
            </tr>

            {{-- Initial Paid Amount (Only show if exists and > 0) --}}
            @if ($lot->contract && $lot->contract->initial_paid_amount > 0)
                <tr class="bg-purple-50 hover:bg-purple-100 transition-colors border-t-2 border-purple-200">
                    <td class="py-3 px-4 text-purple-700 font-bold">
                        <div class="flex items-center gap-2">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                <path
                                    d="M8.433 7.418c.155-.103.346-.196.567-.267v1.698a2.305 2.305 0 01-.567-.267C8.07 8.34 8 8.114 8 8c0-.114.07-.34.433-.582zM11 12.849v-1.698c.22.071.412.164.567.267.364.243.433.468.433.582 0 .114-.07.34-.433.582a2.305 2.305 0 01-.567.267z" />
                                <path fill-rule="evenodd"
                                    d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-13a1 1 0 10-2 0v.092a4.535 4.535 0 00-1.676.662C6.602 6.234 6 7.009 6 8c0 .99.602 1.765 1.324 2.246.48.32 1.054.545 1.676.662v1.941c-.391-.127-.68-.317-.843-.504a1 1 0 10-1.51 1.31c.562.649 1.413 1.076 2.353 1.253V15a1 1 0 102 0v-.092a4.535 4.535 0 001.676-.662C13.398 13.766 14 12.991 14 12c0-.99-.602-1.765-1.324-2.246A4.535 4.535 0 0011 9.092V7.151c.391.127.68.317.843.504a1 1 0 101.511-1.31c-.563-.649-1.413-1.076-2.354-1.253V5z"
                                    clip-rule="evenodd" />
                            </svg>
                            Аввал тўланган сумма
                        </div>
                        <div class="text-xs text-purple-600 mt-1 font-normal">
                            (Шартнома имзолашдан олдин)
                        </div>
                    </td>
                    <td class="py-3 px-4">
                        <div class="text-purple-700 font-bold text-base">
{{ $lot->contract?->initial_paid_amount ? number_format($lot->contract->initial_paid_amount, 0, '.', ' ') . ' UZS' : '-' }}
                        </div>
                        @if ($lot->contract->initial_payment_date)
                            <div class="text-sm text-purple-600 mt-1">
                                Сана: {{ $lot->contract->initial_payment_date->format('d.m.Y') }}
                            </div>
                        @endif
                        <div class="mt-2">
                            <span
                                class="inline-flex items-center px-2 py-1 bg-purple-100 text-purple-800 border border-purple-300 text-xs font-medium rounded-full">
                                <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z"
                                        clip-rule="evenodd" />
                                </svg>
                                График жадвалига қўшилмаган
                            </span>
                        </div>
                    </td>
                </tr>
            @endif

        </tbody>
    </table>

</div>
