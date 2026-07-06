@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-11">
            <div class="card shadow-sm">
                <div class="card-header">Manage Colours</div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success">{{ session('success') }}</div>
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

                    <form action="{{ route('colours.store') }}" method="POST" class="row g-3 mb-4">
                        @csrf
                        <div class="col-md-3">
                            <label class="form-label" for="colour">Colour</label>
                            <input id="colour" type="text" name="colour" value="{{ old('colour', '#808080') }}" class="form-control" placeholder="#AABBCC" required>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label" for="taskable_type">Taskable Type</label>
                            <select id="taskable_type" name="taskable_type" class="form-select" required>
                                <option value="">Select taskable type</option>
                                @foreach($taskableTypes as $alias => $className)
                                    <option value="{{ $alias }}" @selected(old('taskable_type') === $alias)>{{ ucfirst($alias) }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label" for="taskable_id">Taskable ID</label>
                            <input id="taskable_id" type="number" min="1" name="taskable_id" value="{{ old('taskable_id') }}" class="form-control" required>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label" for="type">Type</label>
                            <input id="type" type="text" name="type" value="{{ old('type', 'main') }}" class="form-control" required>
                        </div>
                        <div class="col-md-2 d-flex align-items-end">
                            <button type="submit" class="btn btn-primary w-100">Create</button>
                        </div>
                    </form>

                    <div class="table-responsive">
                        <table class="table table-striped align-middle mb-0">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Colour</th>
                                    <th>Type</th>
                                    <th>Taskable</th>
                                    <th>Preview</th>
                                    <th class="text-end">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($colours as $colour)
                                    <tr>
                                        <td>{{ $colour->id }}</td>
                                        <td>{{ $colour->colour }}</td>
                                        <td>{{ $colour->type }}</td>
                                        <td>{{ ucfirst($colour->taskable_alias ?? 'unknown') }} #{{ $colour->taskable_id }}</td>
                                        <td>
                                            <span class="d-inline-block border rounded" style="width: 2.5rem; height: 1.5rem; background-color: {{ $colour->colour }};"></span>
                                        </td>
                                        <td>
                                            <form action="{{ route('colours.update', $colour) }}" method="POST" class="row g-2 justify-content-end">
                                                @csrf
                                                @method('PATCH')
                                                <div class="col-auto">
                                                    <input type="text" name="colour" value="{{ $colour->colour }}" class="form-control form-control-sm" required>
                                                </div>
                                                <div class="col-auto">
                                                    <select name="taskable_type" class="form-select form-select-sm" required>
                                                        @foreach($taskableTypes as $alias => $className)
                                                            <option value="{{ $alias }}" @selected($colour->taskable_alias === $alias)>{{ ucfirst($alias) }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <div class="col-auto">
                                                    <input type="number" min="1" name="taskable_id" value="{{ $colour->taskable_id }}" class="form-control form-control-sm" required>
                                                </div>
                                                <div class="col-auto">
                                                    <input type="text" name="type" value="{{ $colour->type }}" class="form-control form-control-sm" required>
                                                </div>
                                                <div class="col-auto">
                                                    <button type="submit" class="btn btn-sm btn-outline-primary">Save</button>
                                                </div>
                                            </form>
                                            <form action="{{ route('colours.destroy', $colour) }}" method="POST" class="text-end mt-2" onsubmit="return confirm('Delete this colour?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger">Delete</button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center text-muted">No colours found.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection