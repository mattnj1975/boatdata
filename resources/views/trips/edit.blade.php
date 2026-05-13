@extends('layouts.app')

@section('content')
<div class="container py-4">

    <h1>Edit Trip #{{ $trip->id }}</h1>

    @if($errors->any())
        <div class="alert alert-danger">{{ $errors->first() }}</div>
    @endif

    <form method="POST" action="{{ route('trips.update', $trip) }}" class="card">
        @csrf
        @method('PUT')

        <div class="card-body">

            <div class="mb-3">
                <label class="form-label">Start Boatdata ID</label>
                <input type="number" name="start_boatdata_id" class="form-control"
                       value="{{ old('start_boatdata_id', $trip->start_boatdata_id) }}">
                <small class="text-muted">Changing this rewrites the trip start point and recalculates stats.</small>
            </div>

            <div class="mb-3">
                <label class="form-label">End Boatdata ID</label>
                <input type="number" name="end_boatdata_id" class="form-control"
                       value="{{ old('end_boatdata_id', $trip->end_boatdata_id) }}">
                <small class="text-muted">Must be greater than start ID.</small>
            </div>

            <div class="mb-3">
                <label class="form-label">Status</label>
                <select name="status" class="form-select">
                    @foreach(['auto', 'confirmed', 'edited', 'ignored'] as $status)
                        <option value="{{ $status }}" @selected(old('status', $trip->status) === $status)>
                            {{ ucfirst($status) }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="mb-3">
                <label class="form-label">Notes</label>
                <textarea name="notes" class="form-control" rows="4">{{ old('notes', $trip->notes) }}</textarea>
            </div>

            <button class="btn btn-primary">Save Trip</button>
            <a href="{{ route('trips.show', $trip) }}" class="btn btn-outline-secondary">Cancel</a>

        </div>
    </form>

</div>
@endsection