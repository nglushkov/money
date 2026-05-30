@extends('layouts.app')

@section('title', 'Edit ' . $bill->name)

@section('content')

<div style="margin-bottom:.75rem;">
    <a href="{{ route('bills.show', $bill) }}" style="color:var(--c-muted);font-size:.875rem;text-decoration:none;">
        <i class="bi bi-arrow-left me-1"></i>{{ $bill->name }}
    </a>
</div>

<div class="form-card" style="max-width:36rem;margin:0 auto;">
    <div style="font-size:.72rem;font-weight:700;text-transform:uppercase;letter-spacing:.06em;color:var(--c-muted);margin-bottom:1rem;">Edit Bill</div>

    @if ($errors->any())
        <div class="alert alert-danger mb-3">
            @foreach ($errors->all() as $error)<div>{{ $error }}</div>@endforeach
        </div>
    @endif

    <form autocomplete="off" action="{{ route('bills.update', $bill->id) }}" method="POST">
        @csrf @method('PUT')

        <div class="mb-3">
            <label class="form-label" for="name">Name</label>
            <input type="text" autocomplete="off" name="name" id="name" class="form-control" value="{{ old('name', $bill->name) }}" required autofocus>
        </div>

        <div class="mb-3">
            <label class="form-label" for="notes">Notes</label>
            <input type="text" autocomplete="off" name="notes" id="notes" class="form-control" value="{{ old('notes', $bill->notes) }}">
        </div>

        <div class="mb-3">
            <label class="form-label" for="user_id">Owner</label>
            <select name="user_id" id="user_id" class="form-control">
                <option value="">Common (shared)</option>
                @foreach($users as $user)
                    <option value="{{ $user->id }}" @selected(old('user_id', $bill->user_id) == $user->id)>{{ $user->name }}</option>
                @endforeach
            </select>
        </div>

        <div class="mb-3">
            <div class="form-check">
                <input type="checkbox" name="is_crypto" id="is_crypto" class="form-check-input" value="1"
                       {{ old('is_crypto', $bill->is_crypto) ? 'checked' : '' }}>
                <label class="form-check-label" for="is_crypto">Crypto bill</label>
            </div>
        </div>

        <div style="border-top:1px solid var(--c-border);padding-top:1rem;margin-top:1rem;">
            <div style="font-size:.72rem;font-weight:700;text-transform:uppercase;letter-spacing:.06em;color:var(--c-muted);margin-bottom:.75rem;">Initial balance</div>
            @foreach($currencies as $currency)
                <div class="mb-2" style="display:flex;align-items:center;gap:.75rem;">
                    <label style="width:3.5rem;font-size:.85rem;font-weight:600;color:var(--c-muted);margin:0;">{{ $currency->name }}</label>
                    <input type="text" autocomplete="off" name="amount[{{ $currency->id }}]"
                           class="form-control form-control-sm" style="max-width:12rem;"
                           value="{{ old('amount.' . $currency->id, money_input($bill->currenciesInitial->find($currency->id)->pivot->amount ?? 0)) }}">
                </div>
            @endforeach
        </div>

        <div style="display:flex;gap:.5rem;margin-top:1.25rem;padding-top:1rem;border-top:1px solid var(--c-border);">
            <button type="submit" class="btn btn-success">Save</button>
            <a href="{{ route('bills.show', $bill) }}" class="btn btn-outline-secondary">Cancel</a>
        </div>
    </form>
</div>

@endsection
