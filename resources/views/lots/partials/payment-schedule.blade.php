@if($lot->contract)
<div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
    {{-- Header with Add Button --}}
    <div class="bg-gradient-to-r from-blue-600 to-blue-700 text-white px-6 py-4 flex justify-between items-center">
        <div>
            <h2 class="font-bold text-lg flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/>
                </svg>
                ХИСОБОТ ДАВРИДА БАЖАРИЛИШИ БЕЛГИЛАНГАН МАЖБУРИЯТЛАР
            </h2>
            <p class="text-sm text-blue-100 mt-1">Тўлов графиги ва ҳисоботлар</p>
        </div>

        @if($lot->contract->payment_type === 'muddatli')
        <button
            onclick="openAddScheduleModal()"
            class="bg-white text-blue-600 px-4 py-2 rounded-lg text-sm font-medium hover:bg-blue-50 transition-colors shadow-sm flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Қўшиш
        </button>
        @endif
    </div>

    {{-- Payment Schedule Table --}}
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
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                            {{ $schedule->planned_date->format('d.m.Y') }}
                        </span>
                    </td>

                    {{-- Planned Amount --}}
                    <td class="px-4 py-3 border text-right font-medium text-gray-700">
                        {{ number_format($schedule->planned_amount, 0, '.', ' ') }}
                    </td>

                    {{-- Actual Amount --}}
                    <td class="px-4 py-3 border text-right font-semibold
                        {{ $schedule->actual_amount > 0 ? 'text-green-700 bg-green-100' : 'text-gray-500' }}">
                        {{ number_format($schedule->actual_amount, 0, '.', ' ') }}
                    </td>

                    {{-- Difference --}}
                    <td class="px-4 py-3 border text-right font-bold
                        {{ $schedule->difference > 0 ? 'text-green-600' : ($schedule->difference < 0 ? 'text-red-600' : 'text-gray-500') }}">
                        {{ $schedule->difference != 0 ? number_format($schedule->difference, 0, '.', ' ') : '0' }}
                    </td>

                    {{-- Actions --}}
                    <td class="px-4 py-3 border text-center whitespace-nowrap">
                        <button
                            onclick="openPaymentModal({{ $schedule->id }}, '{{ $schedule->planned_date->format('Y-m-d') }}', {{ $schedule->planned_amount }}, '{{ $index + 1 }}', '{{ optional($schedule->deadline_date)->format('Y-m-d') }}')"
                            class="inline-flex items-center gap-1 text-blue-600 hover:text-blue-800 hover:underline font-medium mr-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            Тўлов
                        </button>

                        @if($schedule->actual_amount > 0)
                        <a href="{{ route('distributions.create', ['payment_schedule_id' => $schedule->id]) }}"
                           class="inline-flex items-center gap-1 text-green-600 hover:text-green-800 hover:underline font-medium">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
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
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        <p class="font-medium text-gray-700 mb-1">График яратилмаган</p>
                        @if($lot->contract->payment_type === 'muddatli')
                        <p class="text-sm">Юқоридаги "+ Қўшиш" тугмасини босинг.</p>
                        @endif
                    </td>
                </tr>
                @endforelse

                {{-- Total Row --}}
                @if($lot->contract->paymentSchedules->count() > 0)
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
</div>
@endif
