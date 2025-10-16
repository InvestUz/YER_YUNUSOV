@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
            {{ session('error') }}
        </div>
    @endif

    <!-- Шартнома маълумотлари -->
    <div class="bg-white shadow rounded-lg p-6 mb-6">
        <div class="flex justify-between items-start mb-4">
            <div>
                <h1 class="text-2xl font-bold">Шартнома № {{ $contract->contract_number }}</h1>
                <p class="text-gray-600">Сана: {{ $contract->contract_date->format('d.m.Y') }}</p>
            </div>
            <div class="flex gap-2">
                @if($contract->paymentSchedules->count() === 0)
                    <!-- Фақат график бўлмаса таҳрирлаш имкони -->
                    <a href="{{ route('contracts.edit', $contract) }}"
                       class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">
                        Таҳрирлаш
                    </a>
                @else
                    <!-- График бор бўлса статус ўзгартириш -->
                    <button onclick="document.getElementById('statusModal').classList.remove('hidden')"
                            class="bg-orange-500 text-white px-4 py-2 rounded hover:bg-orange-600">
                        Статус ўзгартириш
                    </button>
                @endif
                <a href="{{ route('contracts.index') }}"
                   class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600">
                    Орқага
                </a>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <h3 class="font-semibold text-gray-700">Лот маълумотлари</h3>
                <p class="text-sm"><strong>Рақам:</strong> {{ $contract->lot->lot_number }}</p>
                <p class="text-sm"><strong>Манзил:</strong> {{ $contract->lot->address }}</p>
            </div>

            <div>
                <h3 class="font-semibold text-gray-700">Шартнома маълумотлари</h3>
                <p class="text-sm"><strong>Тури:</strong>
                    <span class="px-2 py-1 text-xs rounded {{ $contract->isMuddatli() ? 'bg-blue-100 text-blue-800' : 'bg-green-100 text-green-800' }}">
                        {{ $contract->payment_type_label }}
                    </span>
                </p>
                <p class="text-sm"><strong>Ҳолат:</strong>
                    <span class="px-2 py-1 text-xs rounded
                        @if($contract->status === 'active') bg-yellow-100 text-yellow-800
                        @elseif($contract->status === 'completed') bg-green-100 text-green-800
                        @elseif($contract->status === 'cancelled') bg-red-100 text-red-800
                        @else bg-gray-100 text-gray-800 @endif">
                        {{ $contract->status_label }}
                    </span>
                </p>
                @if($contract->paymentSchedules->count() > 0)
                <p class="text-xs text-gray-500 mt-1">
                    <svg class="inline w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                    </svg>
                    Тўлов графиги мавжуд - таҳрирлаш чекланган
                </p>
                @endif
            </div>

            <div>
                <h3 class="font-semibold text-gray-700">Молиявий маълумотлар</h3>
                <p class="text-sm"><strong>Шартнома суммаси:</strong> {{ number_format($contract->contract_amount, 0, '.', ' ') }} сўм</p>
                <p class="text-sm"><strong>Тўланган:</strong> {{ number_format($contract->paid_amount, 0, '.', ' ') }} сўм</p>
                <p class="text-sm"><strong>Қолган:</strong> {{ number_format($contract->remaining_amount, 0, '.', ' ') }} сўм</p>
                <div class="mt-2">
                    <div class="w-full bg-gray-200 rounded-full h-4">
                        <div class="bg-blue-600 h-4 rounded-full" style="width: {{ $contract->payment_percentage }}%"></div>
                    </div>
                    <p class="text-xs text-gray-600 mt-1">{{ number_format($contract->payment_percentage, 1) }}% тўланди</p>
                </div>
            </div>

            <div>
                <h3 class="font-semibold text-gray-700">Тизим маълумотлари</h3>
                <p class="text-sm"><strong>Яратди:</strong> {{ $contract->creator?->name ?? '-' }}</p>
                <p class="text-sm"><strong>Яратилган:</strong> {{ $contract->created_at->format('d.m.Y H:i') }}</p>
                @if($contract->updater)
                <p class="text-sm"><strong>Янгиланган:</strong> {{ $contract->updater->name }} ({{ $contract->updated_at->format('d.m.Y H:i') }})</p>
                @endif
            </div>
        </div>

        @if($contract->note)
        <div class="mt-4">
            <h3 class="font-semibold text-gray-700">Изоҳ</h3>
            <p class="text-sm text-gray-600">{{ $contract->note }}</p>
        </div>
        @endif
    </div>

    <!-- Тўлов графиги (Муддатли учун) -->
    @if($contract->isMuddatli())
    <div class="bg-white shadow rounded-lg p-6 mb-6">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-xl font-bold">Тўлов графиги</h2>
            @if($contract->paymentSchedules->count() === 0)
            <button onclick="document.getElementById('scheduleModal').classList.remove('hidden')"
                    class="bg-green-500 text-white px-4 py-2 rounded hover:bg-green-600">
                График яратиш
            </button>
            @endif
        </div>

        @if($contract->paymentSchedules->count() > 0)
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">№</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Режалаштирилган сана</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Муддат</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Режалаштирилган</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Тўланган</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Фарқ</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Ҳолат</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Амаллар</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($contract->paymentSchedules as $schedule)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-2">{{ $schedule->payment_number }}</td>
                        <td class="px-4 py-2">{{ $schedule->planned_date->format('d.m.Y') }}</td>
                        <td class="px-4 py-2">{{ $schedule->deadline_date->format('d.m.Y') }}</td>
                        <td class="px-4 py-2">{{ number_format($schedule->planned_amount, 0, '.', ' ') }}</td>
                        <td class="px-4 py-2">{{ number_format($schedule->actual_amount, 0, '.', ' ') }}</td>
                        <td class="px-4 py-2">
                            <span class="{{ $schedule->difference >= 0 ? 'text-green-600' : 'text-red-600' }}">
                                {{ number_format($schedule->difference, 0, '.', ' ') }}
                            </span>
                        </td>
                        <td class="px-4 py-2">
                            <span class="px-2 py-1 text-xs rounded bg-{{ $schedule->status_color }}-100 text-{{ $schedule->status_color }}-800">
                                {{ $schedule->status_label }}
                            </span>
                        </td>
                        <td class="px-4 py-2">
                            @if($contract->status !== 'cancelled')
                            <button onclick="openPaymentModal({{ $schedule->id }}, '{{ $schedule->planned_date->format('Y-m-d') }}', {{ $schedule->actual_amount }})"
                                    class="text-blue-600 hover:text-blue-900 mr-2">Тўлов</button>
                            @if($schedule->actual_amount > 0)
                            <a href="{{ route('distributions.create', ['payment_schedule_id' => $schedule->id]) }}"
                               class="text-green-600 hover:text-green-900">Тақсимлаш</a>
                            @endif
                            @else
                            <span class="text-gray-400 text-sm">Бекор қилинган</span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <p class="text-gray-500 text-center py-4">Тўлов графиги яратилмаган</p>
        @endif
    </div>
    @endif

    <!-- Қўшимча келишувлар -->
    <div class="bg-white shadow rounded-lg p-6 mb-6">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-xl font-bold">Қўшимча келишувлар</h2>
            @if($contract->status !== 'cancelled')
            <a href="{{ route('additional-agreements.create', $contract) }}"
               class="bg-purple-500 text-white px-4 py-2 rounded hover:bg-purple-600">
                Қўшимча келишув қўшиш
            </a>
            @endif
        </div>

        @if($contract->additionalAgreements->count() > 0)
        <div class="space-y-4">
            @foreach($contract->additionalAgreements as $agreement)
            <div class="border rounded p-4">
                <div class="flex justify-between items-start">
                    <div>
                        <h3 class="font-semibold">№ {{ $agreement->agreement_number }}</h3>
                        <p class="text-sm text-gray-600">Сана: {{ $agreement->agreement_date->format('d.m.Y') }}</p>
                        <p class="text-sm">
                            Сумма:
                            <strong class="{{ $agreement->new_amount >= 0 ? 'text-green-600' : 'text-red-600' }}">
                                {{ $agreement->new_amount >= 0 ? '+' : '' }}{{ number_format($agreement->new_amount, 0, '.', ' ') }} сўм
                            </strong>
                        </p>
                        <p class="text-sm text-gray-600">Сабаб: {{ $agreement->reason }}</p>
                    </div>
                    <div>
                        <a href="{{ route('additional-agreements.show', $agreement) }}"
                           class="text-blue-600 hover:text-blue-900">Батафсил</a>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        @else
        <p class="text-gray-500 text-center py-4">Қўшимча келишувлар мавжуд эмас</p>
        @endif
    </div>

    <!-- Тақсимотлар -->
    <div class="bg-white shadow rounded-lg p-6">
        <h2 class="text-xl font-bold mb-4">Тақсимотлар</h2>

        @if($contract->distributions->count() > 0)
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Категория</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Сумма</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Сана</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Ҳолат</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Яратди</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($contract->distributions as $distribution)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-2">{{ $distribution->category_label }}</td>
                        <td class="px-4 py-2 font-semibold">{{ number_format($distribution->allocated_amount, 0, '.', ' ') }}</td>
                        <td class="px-4 py-2">{{ $distribution->distribution_date->format('d.m.Y') }}</td>
                        <td class="px-4 py-2">
                            <span class="px-2 py-1 text-xs rounded bg-{{ $distribution->status_color }}-100 text-{{ $distribution->status_color }}-800">
                                {{ $distribution->status_label }}
                            </span>
                        </td>
                        <td class="px-4 py-2 text-sm">{{ $distribution->creator?->name ?? '-' }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <p class="text-gray-500 text-center py-4">Тақсимотлар мавжуд эмас</p>
        @endif
    </div>
</div>

<!-- График яратиш модали -->
<div id="scheduleModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <h3 class="text-lg font-bold mb-4">Тўлов графиги яратиш</h3>
        <form action="{{ route('contracts.generate-schedule', $contract) }}" method="POST">
            @csrf
            <div class="mb-4">
                <label class="block text-sm font-medium mb-2">Даврийлик</label>
                <select name="frequency" required class="w-full border rounded px-3 py-2">
                    <option value="monthly">Ойлик</option>
                    <option value="quarterly">Чораклик</option>
                    <option value="yearly">Йиллик</option>
                </select>
            </div>
            <div class="mb-4">
                <label class="block text-sm font-medium mb-2">Бошланиш санаси</label>
                <input type="date" name="start_date" required class="w-full border rounded px-3 py-2">
            </div>
            <div class="mb-4">
                <label class="block text-sm font-medium mb-2">Тўловлар сони</label>
                <input type="number" name="number_of_payments" min="1" max="120" required class="w-full border rounded px-3 py-2">
            </div>
            <div class="flex justify-end gap-2">
                <button type="button" onclick="document.getElementById('scheduleModal').classList.add('hidden')"
                        class="px-4 py-2 bg-gray-500 text-white rounded hover:bg-gray-600">
                    Бекор қилиш
                </button>
                <button type="submit" class="px-4 py-2 bg-green-500 text-white rounded hover:bg-green-600">
                    Яратиш
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Статус ўзгартириш модали -->
<div id="statusModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <h3 class="text-lg font-bold mb-4">Шартнома статусини ўзгартириш</h3>
        <form action="{{ route('contracts.update', $contract) }}" method="POST">
            @csrf
            @method('PUT')
            <input type="hidden" name="status_only" value="1">

            <div class="mb-4">
                <label class="block text-sm font-medium mb-2">Статус</label>
                <select name="status" required class="w-full border rounded px-3 py-2">
                    <option value="draft" {{ $contract->status === 'draft' ? 'selected' : '' }}>Қоралама</option>
                    <option value="active" {{ $contract->status === 'active' ? 'selected' : '' }}>Фаол</option>
                    <option value="completed" {{ $contract->status === 'completed' ? 'selected' : '' }}>Якунланган</option>
                    <option value="cancelled" {{ $contract->status === 'cancelled' ? 'selected' : '' }}>Бекор қилинган</option>
                </select>
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium mb-2">Сабаби (мажбурий эмас)</label>
                <textarea name="status_reason" rows="3" class="w-full border rounded px-3 py-2" placeholder="Статус ўзгартириш сабабини кўрсатинг"></textarea>
            </div>

            <div class="bg-yellow-50 border-l-4 border-yellow-500 p-3 mb-4">
                <p class="text-xs text-yellow-700">
                    <strong>Диққат:</strong> Тўлов графиги мавжуд бўлган шартномаларни таҳрирлаш имкони йўқ. Фақат статусни ўзгартириш мумкин.
                </p>
            </div>

            <div class="flex justify-end gap-2">
                <button type="button" onclick="document.getElementById('statusModal').classList.add('hidden')"
                        class="px-4 py-2 bg-gray-500 text-white rounded hover:bg-gray-600">
                    Бекор қилиш
                </button>
                <button type="submit" class="px-4 py-2 bg-orange-500 text-white rounded hover:bg-orange-600">
                    Сақлаш
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Тўлов қўшиш модали -->
<div id="paymentModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <h3 class="text-lg font-bold mb-4">Тўлов қўшиш</h3>
        <form id="paymentForm" method="POST">
            @csrf
            @method('PUT')
            <div class="mb-4">
                <label class="block text-sm font-medium mb-2">Тўлов санаси</label>
                <input type="date" name="actual_date" id="paymentDate" required class="w-full border rounded px-3 py-2">
            </div>
            <div class="mb-4">
                <label class="block text-sm font-medium mb-2">Тўланган сумма</label>
                <input type="number" step="0.01" name="actual_amount" id="paymentAmount" required class="w-full border rounded px-3 py-2">
            </div>
            <div class="mb-4">
                <label class="block text-sm font-medium mb-2">Изоҳ</label>
                <textarea name="note" rows="2" class="w-full border rounded px-3 py-2"></textarea>
            </div>
            <div class="flex justify-end gap-2">
                <button type="button" onclick="document.getElementById('paymentModal').classList.add('hidden')"
                        class="px-4 py-2 bg-gray-500 text-white rounded hover:bg-gray-600">
                    Бекор қилиш
                </button>
                <button type="submit" class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600">
                    Сақлаш
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function openPaymentModal(scheduleId, plannedDate, currentAmount) {
    const form = document.getElementById('paymentForm');
    form.action = `/payment-schedules/${scheduleId}`;
    document.getElementById('paymentDate').value = plannedDate;
    document.getElementById('paymentAmount').value = currentAmount;
    document.getElementById('paymentModal').classList.remove('hidden');
}
</script>
@endsection
