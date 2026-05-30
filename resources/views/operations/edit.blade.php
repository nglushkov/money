@php use App\Models\Enum\OperationType; @endphp

@extends('operations.create-update')

@section('title', 'Edit Operation' . ($operation->is_draft ? ' · Draft' : ''))

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

    <form id="op-form" action="{{ route('operations.update', $operation) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        {{-- Type toggle --}}
        <div class="mb-3">
            <div class="type-toggle">
                <input type="radio" class="btn-check" name="type" id="type-expense"
                       value="{{ OperationType::Expense->name }}"
                       @checked(old('type', $operation->type) == OperationType::Expense->name)>
                <label class="btn btn-outline-danger" for="type-expense">
                    <i class="bi bi-arrow-down me-1"></i>Expense
                </label>

                <input type="radio" class="btn-check" name="type" id="type-income"
                       value="{{ OperationType::Income->name }}"
                       @checked(old('type', $operation->type) == OperationType::Income->name)>
                <label class="btn btn-outline-success" for="type-income">
                    <i class="bi bi-arrow-up me-1"></i>Income
                </label>
            </div>
        </div>

        {{-- Amount --}}
        <div class="mb-3">
            <label class="form-label" for="amount">Amount</label>
            <input type="text" name="amount" id="amount" class="form-control input-amount"
                   required autofocus
                   value="{{ old('amount', App\Helpers\MoneyFormatter::getWithoutTrailingZeros($operation->amount)) }}"
                   placeholder="0.00">
        </div>

        {{-- Bill --}}
        <div class="mb-3">
            <label class="form-label" for="bill">Bill</label>
            <select name="bill_id" id="bill" class="form-control">
                @foreach($bills as $bill)
                    <option value="{{ $bill->id }}"
                        @selected(old('bill_id', $operation->bill_id) == $bill->id)>
                        {{ $bill->name_with_user }}
                    </option>
                @endforeach
            </select>
        </div>

        {{-- Category --}}
        <div class="mb-3">
            <label class="form-label" for="category">Category</label>
            <select name="category_id" id="category" class="form-control" required>
                <option value="">Select Category</option>
                @foreach($categories as $category)
                    <option value="{{ $category->id }}"
                        @selected(old('category_id', $operation->category_id) == $category->id)>
                        {{ $category->name }}
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
                        @selected(old('currency_id', $operation->currency_id) == $currency->id)>
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
                        @selected(old('place_id', $operation->place_id) == $place->id)>
                        {{ $place->name }}
                    </option>
                @endforeach
            </select>
        </div>

        {{-- Notes --}}
        <div class="mb-3">
            <label class="form-label" for="notes">Notes</label>
            <input type="text" name="notes" id="notes" class="form-control"
                   placeholder="Optional"
                   value="{{ old('notes', $operation->notes) }}">
        </div>

        {{-- Date --}}
        <div class="mb-3">
            <label class="form-label" for="date">Date</label>
            <input type="date" name="date" id="date" class="form-control" required
                   value="{{ old('date', $operation->date->format('Y-m-d')) }}">
        </div>

        {{-- Attachment --}}
        <div class="mb-4">
            <label class="form-label" for="attachment">Attachment</label>
            @if ($operation->attachment)
                <div class="mb-2 d-flex align-items-center gap-2">
                    <i class="bi bi-paperclip text-muted"></i>
                    <a href="{{ route('operations.get-attachment', $operation) }}" target="_blank" class="text-muted small">
                        {{ $operation->attachment }}
                    </a>
                    <a href="#" class="text-danger small ms-2"
                       onclick="event.preventDefault(); if (confirm('Delete attachment?')) { document.getElementById('delete-attachment').submit(); }">
                        <i class="bi bi-trash"></i>
                    </a>
                </div>
            @endif
            <input type="file" name="attachment" id="attachment" class="form-control">
        </div>

        <div class="d-flex gap-2">
            <button type="submit" class="btn btn-success flex-fill" style="font-weight:600;">
                <i class="bi bi-check-lg me-1"></i>Update
            </button>
            <a href="{{ route('home') }}" class="btn btn-outline-secondary">Cancel</a>
        </div>
    </form>

    <form action="{{ route('operations.delete-attachment', $operation) }}" id="delete-attachment" method="post" class="d-none">
        @method('DELETE')
        @csrf
        <input type="hidden" name="attachment" value="{{ $operation->attachment }}">
    </form>

@endsection
