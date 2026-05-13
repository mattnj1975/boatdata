<div class="table-responsive">
    <table class="table table-sm trip-table">
        <thead>
            <tr>
                <th>Time</th>
                <th>SOG</th>
                <th>SPD</th>
                <th>COG</th>
            </tr>
        </thead>
        <tbody>
            @forelse($points as $p)
                <tr class="{{ $p->id == $boundaryId ? 'boundary-row' : '' }}">
                    <td>{{ \Carbon\Carbon::parse($p->datetime)->format('H:i:s') }}</td>
                    <td>{{ $p->sog !== null ? number_format($p->sog, 1) : '-' }}</td>
                    <td>{{ $p->spd !== null ? number_format($p->spd, 1) : '-' }}</td>
                    <td>{{ $p->cog !== null ? $p->cog : '-' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" class="trip-muted">No sampled points found.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>