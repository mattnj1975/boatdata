<div class="col-md-6">
    <div class="card h-100">
        <div class="card-body">
            <label class="form-label fw-bold">{{ $label }}</label>

            <div class="input-group">
                <input type="number"
                       step="0.01"
                       name="{{ $name }}"
                       class="form-control"
                       value="{{ old($name, $value) }}">

                <span class="input-group-text">{{ $suffix }}</span>
            </div>

            <p class="text-muted mt-2 mb-0">
                {{ $help }}
            </p>
        </div>
    </div>
</div>