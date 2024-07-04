<!-- resources/views/roles/index.blade.php -->
@extends('layouts.app')

@section('title', 'Roles and Permissions')
@section('style')
<style>
    .highlight {
        background-color: #f0f8ff 
        !important
        ; /* Light blue background for highlighted cells */
    }
    .table-bordered th,
    .table-bordered td {
        transition: background-color 0.3s ease; /* Smooth transition for highlighting */
    }
</style>
@endsection    
@section('content')
    <h1>Roles and Permissions</h1>
    <div class="table-responsive" >
        <table class="table table-bordered" id="rolesTable">
            <thead class="table-light" style="position: sticky; top: 0; z-index: 1;">
                <tr>
                    <th>Permission / Role</th>
                    @foreach($roles as $role)
                        <th>{{ $role->name }}</th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @foreach($permissions as $permission)
                    <tr>
                        <td class="roleName-cell">{{ $permission->name }}</td>
                        @foreach($roles as $role)
                            <td>
                                @if($role->permissions->contains($permission))
                                    <span class="text-success">Yes</span>
                                @else
                                    <span class="text-danger">No</span>
                                @endif
                            </td>
                        @endforeach
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const table = document.getElementById('rolesTable');

        table.addEventListener('click', function (e) {
            if (e.target.tagName === 'TD' || e.target.tagName === 'SPAN') {
                const cell = e.target.closest('td');
                const row = cell.parentNode;
                const columnIndex = Array.from(row.children).indexOf(cell);

                // Remove previous highlights
                table.querySelectorAll('.highlight').forEach(el => el.classList.remove('highlight'));

                // Highlight the clicked row
                row.querySelectorAll('td').forEach(td => td.classList.add('highlight'));
                

                // Highlight the clicked column
                table.querySelectorAll('tr').forEach(tr => {
                    const td = tr.children[columnIndex];
                    if (td) {
                        td.classList.add('highlight');
                    }
                });
            }
        });
    });
</script>

@endsection