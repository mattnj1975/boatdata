<!doctype html>
<html>
<head>
    <title>Raw Boat Data</title>

    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body {
            background: radial-gradient(circle at top left, #12395a, #061521 45%, #02070c);
            color: #eaf6ff;
            min-height: 100vh;
        }

        .glass {
            background: rgba(255,255,255,0.08);
            border: 1px solid rgba(255,255,255,0.12);
            border-radius: 24px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.25);
            backdrop-filter: blur(12px);
        }

        .muted {
            color: rgba(234,246,255,0.65);
        }

        .table-dark {
            --bs-table-bg: transparent;
        }

        th {
            white-space: nowrap;
            color: #9ed8ff;
        }

        td {
            white-space: nowrap;
        }
    </style>
</head>

<body>

<div class="container-fluid py-4 px-4">

    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4">

        <div>
            <h1 class="fw-bold mb-1">
                Raw Data
            </h1>

            <div class="muted">
                {{ $deviceSettings->boatname ?? $mac }} · {{ $mac }} · Latest 20 rows
            </div>
        </div>

        <a href="{{ url('boat-stats/' . $mac) }}" class="btn btn-outline-light fw-bold">
            Dashboard
        </a>

    </div>

    <div class="glass p-4">

        <div class="table-responsive">

            <table class="table table-dark table-hover table-sm align-middle mb-0">

                <thead>
                    <tr>
                        @foreach($columns as $column)
                            <th>{{ $column }}</th>
                        @endforeach
                    </tr>
                </thead>

                <tbody>

                @forelse($rows as $row)

                    <tr>
                        @foreach($columns as $column)
                            <td>{{ $row->$column ?? '-' }}</td>
                        @endforeach
                    </tr>

                @empty

                    <tr>
                        <td colspan="{{ count($columns) }}" class="text-center muted py-4">
                            No data found.
                        </td>
                    </tr>

                @endforelse

                </tbody>

            </table>

        </div>

    </div>

</div>

</body>
</html>