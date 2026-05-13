<div class="table-responsive">
    <table class="table table-sm trip-table">
        <thead>
            <tr>
                <th>Time</th>
                <th>SOG</th>
                <th>SPD</th>
                <th>COG</th>
                <th>Lat</th>
                <th>Lon</th>
            </tr>
        </thead>
        <tbody>
            @forelse($points as $p)
                <tr>
                    <td>{{ \Carbon\Carbon::parse($p->datetime)->format('H:i:s') }}</td>
                    <td>{{ $p->sog !== null ? number_format($p->sog, 1) : '-' }}</td>
                    <td>{{ $p->spd !== null ? number_format($p->spd, 1) : '-' }}</td>
                    <td>{{ $p->cog !== null ? $p->cog : '-' }}</td>
                    <td>{{ $p->latdec }}</td>
                    <td>{{ $p->londec }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="text-muted">No sampled points found.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>