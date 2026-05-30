@php use App\Models\Enum\OperationType; @endphp

@extends('operations.create-update')

@section('title', 'Create Operation')

@section('form')

@if ($errors->any())
    <div class="alert alert-danger mb-3">
        <ul class="mb-0 ps-3">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div x-data="splitForm()">
<form autocomplete="off" id="op-form" action="{{ route('operations.store') }}" method="POST" enctype="multipart/form-data">
    @csrf
    <input type="hidden" name="split_mode" :value="splitMode ? 1 : 0">

    {{-- Type --}}
    <div class="mb-3">
        <div class="type-toggle">
            <input type="radio" class="btn-check" name="type" id="type-expense"
                   value="{{ OperationType::Expense->name }}"
                   @checked(old('type', OperationType::Expense->name) == OperationType::Expense->name)>
            <label class="btn btn-outline-danger" for="type-expense">
                <i class="bi bi-arrow-down me-1"></i>Expense
            </label>
            <input type="radio" class="btn-check" name="type" id="type-income"
                   value="{{ OperationType::Income->name }}"
                   @checked(old('type') == OperationType::Income->name)>
            <label class="btn btn-outline-success" for="type-income">
                <i class="bi bi-arrow-up me-1"></i>Income
            </label>
        </div>
    </div>

    {{-- Amount --}}
    <div class="mb-3">
        <div class="d-flex align-items-center justify-content-between mb-1">
            <label class="form-label mb-0" for="amount">Amount</label>
            <button type="button" dusk="split-toggle"
                    class="split-toggle-btn" :class="{ active: splitMode }"
                    @click="splitMode = !splitMode">
                <i class="bi bi-scissors me-1"></i>Split
            </button>
        </div>
        <input type="text" autocomplete="off" name="amount" id="amount" class="form-control input-amount"
               required autofocus x-model="totalAmount" placeholder="0.00">
    </div>

    {{-- Normal: single category --}}
    <div x-show="!splitMode">
        <div class="mb-3">
            <label class="form-label" for="category">Category</label>
            <select name="category_id" id="category" class="form-control"
                    :required="!splitMode">
                <option value="">Select Category</option>
                @foreach($categories as $category)
                    <option value="{{ $category->id }}"
                        @selected(old('category_id', $plannedExpense->category_id ?? '') == $category->id)>
                        {{ $category->name }}
                    </option>
                @endforeach
            </select>
        </div>
    </div>

    {{-- Split: dynamic rows --}}
    <div x-show="splitMode" x-cloak dusk="split-section">
        <label class="form-label">Split by Category</label>
        <div class="split-section">
            <template x-for="(row, i) in rows" :key="row.id">
                <div class="split-row" :dusk="'split-row-' + i">
                    <div class="split-cat">
                        <select data-split-select
                                :name="'splits[' + i + '][category_id]'"
                                :dusk="'split-cat-' + i">
                            <option value="">Category</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}">{{ $category->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="split-amt">
                        <input type="number" autocomplete="off" step="0.01" min="0"
                               :name="'splits[' + i + '][amount]'"
                               :dusk="'split-amt-' + i"
                               x-model="row.amount"
                               class="form-control" placeholder="0.00">
                    </div>
                    <button type="button" class="split-rest"
                            :dusk="'split-rest-' + i"
                            x-show="remaining > 0.005"
                            @click="fillRemaining(i)"
                            title="Fill remaining">
                        = <span x-text="remaining.toFixed(2)"></span>
                    </button>
                    <button type="button" class="split-del"
                            :dusk="'split-del-' + i"
                            x-show="rows.length > 1"
                            @click="removeRow(i)">×</button>
                </div>
            </template>

            <button type="button" class="btn btn-link btn-sm ps-0 text-muted"
                    dusk="split-add-row" @click="addRow()">
                <i class="bi bi-plus-lg me-1"></i>Add row
            </button>

            <div class="split-stats">
                <span class="stat-label">Split:</span>
                <span class="stat-val" dusk="split-total"
                      :class="isBalanced ? 'stat-ok' : 'stat-warn'"
                      x-text="splitTotal.toFixed(2)"></span>
                <span class="stat-label">/ Total:</span>
                <span class="stat-val" x-text="(parseFloat(totalAmount) || 0).toFixed(2)"></span>
                <template x-if="isBalanced">
                    <span class="stat-ok" dusk="split-balanced"><i class="bi bi-check-circle-fill"></i></span>
                </template>
                <template x-if="!isBalanced">
                    <span class="stat-warn" dusk="split-remaining">
                        Remaining: <span x-text="remaining.toFixed(2)"></span>
                    </span>
                </template>
            </div>
        </div>
    </div>

    {{-- Bill --}}
    <div class="mb-3 mt-3">
        <label class="form-label" for="bill">Bill</label>
        <select name="bill_id" id="bill" class="form-control" required>
            <option value="">Select Bill</option>
            @foreach($bills as $bill)
                <option value="{{ $bill->id }}"
                    @selected(old('bill_id', $plannedExpense->bill_id ?? '') == $bill->id)>
                    {{ $bill->name_with_user }}
                </option>
            @endforeach
        </select>
    </div>

    {{-- Currency --}}
    <div class="mb-3">
        <label class="form-label" for="currency">Currency</label>
        <select name="currency_id" id="currency" class="form-control">
            @foreach($currencies as $currency)
                <option value="{{ $currency->id }}"
                    @selected(old('currency_id', $plannedExpense->currency_id ?? '') == $currency->id)>
                    {{ $currency->name }}
                </option>
            @endforeach
        </select>
    </div>

    {{-- Place --}}
    <div class="mb-3">
        <label class="form-label" for="place">Place</label>
        <select name="place_id" id="place" class="form-control" required>
            <option value="">Select Place</option>
            @foreach($places as $place)
                <option value="{{ $place->id }}"
                    @selected(old('place_id', $plannedExpense->place_id ?? '') == $place->id)>
                    {{ $place->name }}
                </option>
            @endforeach
        </select>
    </div>

    {{-- Notes --}}
    <div class="mb-3">
        <label class="form-label" for="notes">Notes</label>
        <input type="text" autocomplete="off" name="notes" id="notes" class="form-control" placeholder="Optional"
               value="{{ old('notes', $plannedExpense->notes ?? '') }}">
    </div>

    {{-- Date --}}
    <div class="mb-3">
        <label class="form-label" for="date">Date</label>
        <input type="date" name="date" id="date" class="form-control" required
               value="{{ old('date', date('Y-m-d')) }}">
    </div>

    {{-- Attachment (normal mode only) --}}
    <div class="mb-4" x-show="!splitMode">
        <label class="form-label" for="attachment">Attachment</label>
        <input type="file" name="attachment" id="attachment" class="form-control">
    </div>

    <div class="d-flex gap-2">
        <button type="submit" dusk="submit" class="btn btn-success flex-fill" style="font-weight:600;">
            <i class="bi bi-check-lg me-1"></i>Create
        </button>
        <a href="{{ route('home') }}" class="btn btn-outline-secondary">Cancel</a>
    </div>
</form>
</div>

<style>[x-cloak] { display: none !important; }</style>

<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('splitForm', () => ({
        splitMode: {!! old('split_mode') ? 'true' : 'false' !!},
        totalAmount: {!! json_encode(old('amount', money_input($plannedExpense->amount ?? null) ?? '')) !!},
        rows: [{ id: 0, amount: '' }],
        nextId: 1,

        init() {
            this.$nextTick(() => this._initSelects());
        },
        _initSelects() {
            document.querySelectorAll('[data-split-select]').forEach(el => {
                if (!el.tomselect) new TomSelect(el, { allowEmptyOption: true });
            });
        },

        get splitTotal() {
            return this.rows.reduce((s, r) => s + (parseFloat(r.amount) || 0), 0);
        },
        get remaining() {
            return Math.round(((parseFloat(this.totalAmount) || 0) - this.splitTotal) * 100) / 100;
        },
        get isBalanced() {
            return Math.abs(this.remaining) < 0.01;
        },
        addRow() {
            this.rows.push({ id: this.nextId++, amount: '' });
            this.$nextTick(() => this._initSelects());
        },
        removeRow(i) {
            if (this.rows.length > 1) this.rows.splice(i, 1);
        },
        fillRemaining(i) {
            if (this.remaining > 0) this.rows[i].amount = this.remaining.toFixed(2);
        }
    }));
});
</script>

@endsection
