@extends('layouts.app')

@section('title', 'SQL Dump Tools')

@section('content')
<div class="container">
    <h2 class="mb-4">SQL Dump Tools</h2>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="row g-4">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">Create SQL Dump</div>
                <div class="card-body">
                    <form method="POST" action="{{ route('setting.sqlDump.create') }}">
                        @csrf
                        <div class="mb-3">
                            <label for="name" class="form-label">Dump Name (optional)</label>
                            <input type="text" class="form-control" id="name" name="name" placeholder="db_restore_custom_name">
                        </div>
                        <div class="mb-3">
                            <label for="chunk_size_kb" class="form-label">Chunk Size (KB)</label>
                            <input type="number" class="form-control" id="chunk_size_kb" name="chunk_size_kb" value="1024" min="1" max="10240">
                            <small class="text-muted">Default 1024 KB = 1 MB per file.</small>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Table Selection</label>
                            <div class="table-responsive border rounded p-2" style="max-height: 300px; overflow-y: auto;">
                                <table class="table table-sm align-middle mb-0">
                                    <thead>
                                        <tr>
                                            <th>Table</th>
                                            <th>Include in Dump</th>
                                            <th>Include Data</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($tables as $table)
                                            @php
                                                $isRestricted = $table['restricted'];
                                                $isSelected = in_array($table['name'], $selectedTables, true);
                                                $includeData = in_array($table['name'], $includeDataTables, true);
                                            @endphp
                                            <tr>
                                                <td>
                                                    {{ $table['name'] }}
                                                    @if($isRestricted)
                                                        <span class="badge bg-warning text-dark">blocked</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    <input
                                                        type="checkbox"
                                                        name="selected_tables[]"
                                                        value="{{ $table['name'] }}"
                                                        {{ $isSelected ? 'checked' : '' }}
                                                        {{ $isRestricted ? 'disabled' : '' }}
                                                        data-table-toggle="{{ $table['name'] }}"
                                                    >
                                                </td>
                                                <td>
                                                    <input
                                                        type="checkbox"
                                                        name="include_data_tables[]"
                                                        value="{{ $table['name'] }}"
                                                        {{ $includeData ? 'checked' : '' }}
                                                        {{ $isRestricted ? 'disabled' : '' }}
                                                        data-table-data="{{ $table['name'] }}"
                                                    >
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            <small class="text-muted">`users` table is hard-blocked and cannot be dumped/restored.</small>
                        </div>
                        <button type="submit" class="btn btn-primary">Generate Dump</button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card">
                <div class="card-header">Upload and Restore SQL</div>
                <div class="card-body">
                    <form method="POST" action="{{ route('setting.sqlDump.upload') }}" enctype="multipart/form-data">
                        @csrf
                        <div class="mb-3">
                            <label for="sql_file" class="form-label">SQL File</label>
                            <input type="file" class="form-control" id="sql_file" name="sql_file" accept=".sql,.txt" required>
                            <small class="text-muted">Maximum size: 50 MB.</small>
                        </div>
                        <button type="submit" class="btn btn-danger" onclick="return confirm('This will execute SQL statements on your current database. Continue?')">Upload and Restore</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="card mt-4">
        <div class="card-header">Available SQL Dump Files</div>
        <div class="card-body">
            @if(empty($files))
                <p class="mb-0 text-muted">No SQL dump files found.</p>
            @else
                <div class="table-responsive">
                    <table class="table table-sm align-middle">
                        <thead>
                            <tr>
                                <th>File</th>
                                <th>Size</th>
                                <th>Modified</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($files as $file)
                                <tr>
                                    <td>{{ $file['name'] }}</td>
                                    <td>{{ number_format($file['size_bytes']) }} bytes</td>
                                    <td>{{ date('Y-m-d H:i:s', $file['modified_at']) }}</td>
                                    <td>
                                        <a class="btn btn-sm btn-outline-primary" href="{{ route('setting.sqlDump.download', ['fileName' => $file['name']]) }}">Download</a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
</div>

<script>
document.querySelectorAll('[data-table-toggle]').forEach(function (toggle) {
    var tableName = toggle.getAttribute('data-table-toggle');
    var dataToggle = document.querySelector('[data-table-data="' + tableName + '"]');

    var syncDataToggle = function () {
        if (!dataToggle) {
            return;
        }

        dataToggle.disabled = !toggle.checked;
        if (!toggle.checked) {
            dataToggle.checked = false;
        }
    };

    syncDataToggle();
    toggle.addEventListener('change', syncDataToggle);
});
</script>
@endsection
