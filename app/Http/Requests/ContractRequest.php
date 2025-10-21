<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ContractRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        $contractId = $this->route('contract') ? $this->route('contract')->id : null;

        return [
            'lot_id' => 'required|exists:lots,id',
            'contract_number' => 'required|string' . $contractId,
            'contract_date' => 'required|date',
            'payment_type' => 'required|in:muddatli,muddatsiz',
            'contract_amount' => 'required|numeric|min:0',
            'note' => 'nullable|string',
            'status' => 'sometimes|in:active,completed,cancelled',
        ];
    }

    public function messages()
    {
        return [
            'lot_id.required' => 'Лотни танлаш шарт',
            'lot_id.exists' => 'Танланган лот топилмади',
            'contract_number.required' => 'Шартнома рақами киритилиши шарт',
            'contract_number.unique' => 'Бу рақамли шартнома аллақачон мавжуд',
            'contract_date.required' => 'Шартнома санаси киритилиши шарт',
            'contract_date.date' => 'Тўғри сана форматини киритинг',
            'payment_type.required' => 'Тўлов турини танланг',
            'payment_type.in' => 'Тўлов тури нотўғри',
            'contract_amount.required' => 'Шартнома суммасини киритинг',
            'contract_amount.numeric' => 'Сумма рақам бўлиши керак',
            'contract_amount.min' => 'Сумма 0 дан кичик бўлиши мумкин эмас',
        ];
    }
}
