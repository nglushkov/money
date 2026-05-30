@extends('layouts.app')

@section('title', 'Обменник')

@section('content')
<div class="row justify-content-md-center">
    <div class="col-md-7">
        <div class="card p-3 mt-3">
            <h5 class="card-title mb-3">Обменник</h5>
            <div class="card-body">

                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{!! $error !!}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('exchanger.store') }}" method="POST"
                      x-data="exchangerForm()">
                    @csrf

                    {{-- Shared fields --}}
                    <div class="row g-2 mb-3">
                        <div class="col-auto">
                            <label class="form-label">Дата</label>
                            <input type="date" name="date" class="form-control"
                                   value="{{ old('date', date('Y-m-d')) }}" required>
                        </div>
                        <div class="col">
                            <label class="form-label">Обменник</label>
                            <select name="place_id" class="form-select">
                                <option value="">— не указан —</option>
                                @foreach ($places as $place)
                                    <option value="{{ $place->id }}" @selected(old('place_id') == $place->id)>
                                        {{ $place->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="row g-2 mb-4">
                        <div class="col">
                            <label class="form-label">Крипто-счёт</label>
                            <select name="from_bill_id" class="form-select" x-model="fromBillId" required>
                                @foreach ($cryptoBills as $bill)
                                    <option value="{{ $bill->id }}" @selected(old('from_bill_id') == $bill->id)>
                                        {{ $bill->name_with_user }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-auto">
                            <label class="form-label">Из валюты</label>
                            <select name="from_currency_id" class="form-select" required
                                    @change="fromCurrencyName = $event.target.selectedOptions[0]?.dataset?.name ?? ''">
                                @foreach ($cryptoCurrencies as $currency)
                                    <option value="{{ $currency->id }}"
                                            data-name="{{ $currency->name }}"
                                            @selected(old('from_currency_id', $defaultCryptoCurrency->id) == $currency->id)>
                                        {{ $currency->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    {{-- Dynamic rows --}}
                    <template x-for="(row, index) in rows" :key="index">
                        <div class="border rounded p-3 mb-3 position-relative">
                            <div class="row g-2 align-items-end">
                                <div class="col-auto">
                                    <label class="form-label" x-text="`Отдал, ${fromCurrencyName}`"></label>
                                    <input type="text"
                                           :name="`rows[${index}][from_amount]`"
                                           x-model="row.from_amount"
                                           @input="calcRate(index)"
                                           class="form-control"
                                           style="width:130px"
                                           placeholder="0"
                                           :dusk="`row-from-amount-${index}`"
                                           required>
                                </div>
                                <div class="col-auto">
                                    <label class="form-label">Получил</label>
                                    <input type="text"
                                           :name="`rows[${index}][amount]`"
                                           x-model="row.amount"
                                           @input="calcRate(index)"
                                           class="form-control"
                                           style="width:130px"
                                           placeholder="0"
                                           :dusk="`row-amount-${index}`"
                                           required>
                                </div>
                                <div class="col-auto">
                                    <label class="form-label">Валюта</label>
                                    <select :name="`rows[${index}][currency_id]`"
                                            x-model="row.currency_id"
                                            class="form-select"
                                            :dusk="`row-currency-${index}`"
                                            required>
                                        <option value="">—</option>
                                        @foreach ($fiatCurrencies as $currency)
                                            <option value="{{ $currency->id }}">{{ $currency->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col">
                                    <label class="form-label">На счёт</label>
                                    <select :name="`rows[${index}][bill_id]`"
                                            x-model="row.bill_id"
                                            class="form-select"
                                            :dusk="`row-bill-${index}`"
                                            required>
                                        <option value="">—</option>
                                        @foreach ($targetBills as $bill)
                                            <option value="{{ $bill->id }}">{{ $bill->name_with_user }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-auto" x-show="rows.length > 1">
                                    <button type="button"
                                            class="btn btn-outline-danger btn-sm"
                                            @click="removeRow(index)"
                                            :dusk="`row-remove-${index}`">×</button>
                                </div>
                            </div>
                            <div class="mt-2 text-muted small"
                                 x-show="row.rate"
                                 x-text="`Курс: 1 ${fromCurrencyName} = ${row.rate}`"
                                 dusk="rate-display">
                            </div>
                        </div>
                    </template>

                    <button type="button" class="btn btn-outline-secondary btn-sm mb-3"
                            @click="addRow()" dusk="add-row">
                        + Добавить обмен
                    </button>

                    <div class="row g-2 mb-3">
                        <div class="col">
                            <label class="form-label">Примечание</label>
                            <input type="text" name="notes" class="form-control"
                                   value="{{ old('notes') }}" placeholder="необязательно">
                        </div>
                    </div>

                    <hr>
                    <button type="submit" class="btn btn-primary" dusk="submit">Создать</button>
                    <a href="{{ route('exchanges.index') }}" class="btn btn-secondary">Отмена</a>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
function exchangerForm() {
    return {
        rows: [{ from_amount: '', amount: '', currency_id: '', bill_id: '', rate: '' }],
        fromCurrencyName: '{{ $defaultCryptoCurrency->name }}',

        addRow() {
            this.rows.push({ from_amount: '', amount: '', currency_id: '', bill_id: '', rate: '' });
        },

        removeRow(index) {
            this.rows.splice(index, 1);
        },

        calcRate(index) {
            const row = this.rows[index];
            const from = parseFloat(row.from_amount);
            const to   = parseFloat(row.amount);
            row.rate = (from > 0 && to > 0) ? (to / from).toLocaleString('en', { maximumFractionDigits: 2 }) : '';
        },
    };
}
</script>
@endsection
