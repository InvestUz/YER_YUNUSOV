@if ($lot->contract)
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        {{-- Header with Add Button --}}
        <div class="bg-gradient-to-r from-blue-600 to-blue-700 text-white px-6 py-4 flex justify-between items-center">
            <div>
                <h2 class="font-bold text-lg flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                    </svg>
                    @if($lot->contract->payment_type === 'muddatsiz')
                        ТАҚСИМЛАНГАН СУММА
                    @else
                        ХИСОБОТ ДАВРИДА БАЖАРИЛИШИ БЕЛГИЛАНГАН МАЖБУРИЯТЛАР
                    @endif
                </h2>
                <p class="text-sm text-blue-100 mt-1">
                    @if($lot->contract->payment_type === 'muddatsiz')
                        Муддатсиз тўлов - Бир марталик тақсимот
                    @else
                        Тўлов графиги ва ҳисоботлар
                    @endif
                </p>
            </div>

            <div class="flex items-center gap-2">
                @if ($lot->contract->payment_type === 'muddatli')
                    <button onclick="openAddScheduleModal()"
                        class="bg-white text-blue-600 px-4 py-2 rounded-lg text-sm font-medium hover:bg-blue-50 transition-colors shadow-sm flex items-center gap-1">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        Қўшиш
                    </button>

                    {{-- Check if payment schedules exist AND first one has actual payment --}}
                    @if ($lot->contract->paymentSchedules->isNotEmpty() && $lot->contract->paymentSchedules->first()->actual_amount > 0)
                        <a href="{{ route('distributions.create', ['payment_schedule_id' => $lot->contract->paymentSchedules->first()->id]) }}"
                            class="bg-green-600 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-green-700 transition-colors shadow-sm flex items-center gap-1">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            Тақсимлаш
                        </a>
                    @endif

                @elseif($lot->contract->payment_type === 'muddatsiz')
                    {{-- Muddatsiz - Check if payment schedule exists (auto-created) --}}
                    @if ($lot->contract->paymentSchedules->isNotEmpty())
                        <a href="{{ route('distributions.create', ['payment_schedule_id' => $lot->contract->paymentSchedules->first()->id]) }}"
                            class="bg-green-600 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-green-700 transition-colors shadow-sm flex items-center gap-1">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            Тақсимлаш
                        </a>
                    @else
                        {{-- If payment schedule doesn't exist, show warning --}}
                        <div class="bg-yellow-100 text-yellow-800 px-4 py-2 rounded-lg text-xs font-medium">
                            Тўлов графиги топилмади
                        </div>
                    @endif
                @endif
            </div>
        </div>

        {{-- MUDDATSIZ: Show Distributable Amount Summary --}}
        @if($lot->contract->payment_type === 'muddatsiz')
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                    {{-- Total Paid --}}
                    <div class="bg-gradient-to-br from-blue-50 to-blue-100 rounded-lg p-5 border border-blue-200">
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-sm text-blue-700 font-medium">Тўланган сумма</span>
                            <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <p class="text-2xl font-bold text-blue-900">
                            {{ number_format($lot->paid_amount / 1000000, 1) }}М
                        </p>
                        <p class="text-xs text-blue-600 mt-1">{{ number_format($lot->paid_amount, 0, '.', ' ') }} сўм</p>
                    </div>

                    {{-- Discount (if applicable) --}}
                    @if($lot->qualifiesForDiscount())
                        <div class="bg-gradient-to-br from-yellow-50 to-yellow-100 rounded-lg p-5 border border-yellow-200">
                            <div class="flex items-center justify-between mb-2">
                                <span class="text-sm text-yellow-700 font-medium">Чегирма (20%)</span>
                                <svg class="w-5 h-5 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                                </svg>
                            </div>
                            <p class="text-2xl font-bold text-yellow-900">
                                {{ number_format($lot->discount / 1000000, 1) }}М
                            </p>
                            <p class="text-xs text-yellow-600 mt-1">{{ number_format($lot->discount, 0, '.', ' ') }} сўм</p>
                        </div>
                    @endif

                    {{-- Distributable Amount --}}
                    <div class="bg-gradient-to-br from-green-50 to-green-100 rounded-lg p-5 border-2 border-green-300 shadow-md">
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-sm text-green-700 font-medium">Тақсимланадиган сумма</span>
                            <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                        </div>
                        <p class="text-2xl font-bold text-green-900">
                            {{ number_format($lot->distributable_amount / 1000000, 1) }}М
                        </p>
                        <p class="text-xs text-green-600 mt-1">{{ number_format($lot->distributable_amount, 0, '.', ' ') }} сўм</p>
                    </div>
                </div>

                {{-- Calculation Breakdown --}}
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-4">
                    <h3 class="font-semibold text-blue-900 mb-3 flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                        </svg>
                        Ҳисоблаш формуласи
                    </h3>
                    <div class="space-y-2 text-sm text-blue-800">
                        @if($lot->qualifiesForDiscount())
                            <p>• Тўланған сумма: <strong>{{ number_format($lot->paid_amount, 0, '.', ' ') }} сўм</strong></p>
                            <p>• Чегирма (20%): <strong>-{{ number_format($lot->discount, 0, '.', ' ') }} сўм</strong></p>
                            <p>• Шартномадан (80%): <strong>{{ number_format($lot->incoming_amount, 0, '.', ' ') }} сўм</strong></p>
                            <p class="pt-2 border-t border-blue-300">• <strong>Тақсимланадиган сумма = Шартномадан 100%</strong></p>
                            <p class="text-xs text-blue-600 mt-1">
                                (10.09.2024 дан кейинги аукционлар учун 20% чегирма қўлланади)
                            </p>
                        @else
                            <p>• Тўланған сумма: <strong>{{ number_format($lot->paid_amount, 0, '.', ' ') }} сўм</strong></p>
                            <p class="pt-2 border-t border-blue-300">• <strong>Тақсимланадиган сумма = Тўланған сумма 100%</strong></p>
                        @endif
                    </div>
                </div>

                {{-- Distribution Status --}}
                @if($lot->contract->distributions->count() > 0)
                    <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                        <div class="flex items-center gap-2 text-green-800 mb-2">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                            <span class="font-semibold">Тақсимот амалга оширилган</span>
                        </div>
                        <p class="text-sm text-green-700">
                            Жами тақсимланган: <strong>{{ number_format($lot->contract->distributions->sum('allocated_amount'), 0, '.', ' ') }} сўм</strong>
                        </p>
                    </div>
                @else
                    <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                        <div class="flex items-center gap-2 text-yellow-800 mb-2">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                            </svg>
                            <span class="font-semibold">Тақсимот кутилмоқда</span>
                        </div>
                        <p class="text-sm text-yellow-700">
                            "Тақсимлаш" тугмасини босиб, пулни тақсимланг
                        </p>
                    </div>
                @endif
            </div>

        @else
            {{-- MUDDATLI: Show Payment Schedule Table --}}
            <div class="overflow-x-auto">
                <table class="min-w-full text-xs border-collapse">
                    <thead>
                        <tr class="bg-gray-100 text-gray-700">
                            <th rowspan="2" class="px-4 py-3 border font-semibold">№</th>
                            <th rowspan="2" class="px-4 py-3 border font-semibold">Сана</th>
                            <th colspan="3" class="px-4 py-3 border text-center font-semibold">Хисобот даврида (сўм)</th>
                            <th rowspan="2" class="px-4 py-3 border font-semibold">Амаллар</th>
                        </tr>
                        <tr class="bg-gray-50 text-gray-600">
                            <th class="px-4 py-2 border text-sm">График</th>
                            <th class="px-4 py-2 border text-sm">Амалда</th>
                            <th class="px-4 py-2 border text-sm">+/-</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($lot->contract->paymentSchedules->sortBy('planned_date') as $index => $schedule)
                            <tr class="hover:bg-blue-50 transition-colors {{ $schedule->actual_amount > 0 ? 'bg-green-50' : '' }}">
                                {{-- Row Number --}}
                                <td class="px-4 py-3 border text-center font-medium">{{ $index + 1 }}</td>

                                {{-- Planned Date --}}
                                <td class="px-4 py-3 border text-center">
                                    <span class="inline-flex items-center gap-1">
                                        <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                        </svg>
                                        {{ $schedule->planned_date->format('d.m.Y') }}
                                    </span>
                                </td>


                                {{-- Planned Amount --}}
                                <td class="px-4 py-3 border text-right font-medium text-gray-700">
                                    {{ number_format($schedule->planned_amount, 2, '.', ' ') }}
                                </td>

                                {{-- Actual Amount --}}
                                <td class="px-4 py-3 border text-right font-semibold {{ $schedule->actual_amount > 0 ? 'text-green-700 bg-green-100' : 'text-gray-500' }}">
                                    {{ number_format($schedule->actual_amount, 2, '.', ' ') }}
                                </td>

                                {{-- Difference --}}
                                <td class="px-4 py-3 border text-right font-bold {{ $schedule->difference > 0 ? 'text-green-600' : ($schedule->difference < 0 ? 'text-red-600' : 'text-gray-500') }}">
                                    {{ $schedule->difference != 0 ? number_format($schedule->difference, 0, '.', ' ') : '0' }}
                                </td>

                                {{-- Actions --}}
                                <td class="px-4 py-3 border text-center whitespace-nowrap">
                                    <button
                                        onclick="openPaymentModal({{ $schedule->id }}, '{{ $schedule->planned_date->format('Y-m-d') }}', {{ $schedule->planned_amount }}, '{{ $index + 1 }}', '{{ optional($schedule->deadline_date)->format('Y-m-d') }}')"
                                        class="inline-flex items-center gap-1 text-blue-600 hover:text-blue-800 hover:underline font-medium mr-2">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        Тўлов
                                    </button>

                                    @if ($schedule->actual_amount > 0)
                                        <a href="{{ route('distributions.create', ['payment_schedule_id' => $schedule->id]) }}"
                                            class="inline-flex items-center gap-1 text-green-600 hover:text-green-800 hover:underline font-medium">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                            </svg>
                                            Тақсимлаш
                                        </a>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-4 py-12 text-center text-gray-500">
                                    <svg class="w-16 h-16 mx-auto text-gray-400 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                            d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                    </svg>
                                    <p class="font-medium text-gray-700 mb-1">График яратилмаган</p>
                                    <p class="text-sm">Юқоридаги "+ Қўшиш" тугмасини босинг.</p>
                                </td>
                            </tr>
                        @endforelse

                        {{-- Total Row --}}
                        @if ($lot->contract->paymentSchedules->count() > 0)
                            <tr class="bg-gradient-to-r from-blue-100 to-blue-50 font-bold text-gray-900">
                                <td colspan="2" class="px-4 py-3 border text-right text-base">Жами:</td>
                                <td class="px-4 py-3 border text-right text-base">
                                    {{ number_format($lot->contract->paymentSchedules->sum('planned_amount'), 0, '.', ' ') }}
                                </td>
                                <td class="px-4 py-3 border text-right text-base text-green-700">
                                    {{ number_format($lot->contract->paymentSchedules->sum('actual_amount'), 0, '.', ' ') }}
                                </td>
                                <td class="px-4 py-3 border"></td>
                                <td class="px-4 py-3 border"></td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        @endif
    </div>
@endif
