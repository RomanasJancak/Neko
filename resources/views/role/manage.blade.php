@extends('layouts.app')

@section('title', 'Roles')

@section('content')
<div class="container">
    <div class="row mb-3">
        <div class="col-md-12">
            <h1>Roles</h1>
        </div>
        <div class="col-md-12 mb-3">
            <button class="btn btn-secondary create-btn">Add new Role</button>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Guard</th>
                        <th>Users</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody id="rolesTableBody">
                    @foreach($roles as $role)
                    <tr id="role-{{ $role->id }}">
                        <td>{{ $role->id }}</td>
                        <td>{{ $role->name }}</td>
                        <td>{{ $role->guard_name }}</td>
                        <td>{{ $role->users_count }}</td>
                        <td>
                            <button class="btn btn-primary btn-sm edit-btn"
                                data-roleid="{{ $role->id }}"
                                data-name="{{ $role->name }}">
                                <i class="bi bi-pen"></i>
                            </button>
                            <button class="btn btn-danger btn-sm delete-btn"
                                data-roleid="{{ $role->id }}"
                                data-name="{{ $role->name }}"
                                data-users="{{ $role->users_count }}"
                                @if($role->users_count > 0)
                                    disabled
                                    title="Cannot delete: {{ $role->users_count }} user(s) assigned"
                                @endif>
                                <i class="bi bi-trash"></i>
                            </button>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal -->
<div class="modal fade" id="roleModal" tabindex="-1" aria-labelledby="roleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="roleModalLabel">Role</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="roleModalAlert" class="alert d-none" role="alert"></div>
                <form id="roleForm">
                    @csrf
                    <input type="hidden" id="roleId" name="roleid" value="">
                    <div class="mb-3">
                        <label for="roleNameField" class="form-label">Name</label>
                        <input type="text" class="form-control" id="roleNameField" name="name" required>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" id="roleSubmitBtn" class="btn btn-primary">Save</button>
            </div>
        </div>
    </div>
</div>

<!-- Delete Confirm Modal -->
<div class="modal fade" id="roleDeleteModal" tabindex="-1" aria-labelledby="roleDeleteModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="roleDeleteModalLabel">Confirm Delete</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete role <strong id="deleteRoleName"></strong>?</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" id="roleDeleteConfirmBtn" class="btn btn-danger">Delete</button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const storeUrl = "{{ route('role.store') }}";
    let currentMode = 'create';
    let currentRoleId = null;

    function showModalAlert(message, type = 'danger') {
        const el = document.getElementById('roleModalAlert');
        el.className = `alert alert-${type}`;
        el.textContent = message;
        el.classList.remove('d-none');
    }

    function clearModalAlert() {
        const el = document.getElementById('roleModalAlert');
        el.classList.add('d-none');
        el.textContent = '';
    }

    // Open create modal
    document.querySelectorAll('.create-btn').forEach(btn => {
        btn.addEventListener('click', () => {
            currentMode = 'create';
            currentRoleId = null;
            document.getElementById('roleId').value = '';
            document.getElementById('roleNameField').value = '';
            document.getElementById('roleModalLabel').textContent = 'Add new Role';
            document.getElementById('roleSubmitBtn').textContent = 'Create';
            clearModalAlert();
            new bootstrap.Modal(document.getElementById('roleModal')).show();
        });
    });

    // Open edit modal
    document.querySelectorAll('.edit-btn').forEach(btn => {
        btn.addEventListener('click', () => {
            currentMode = 'edit';
            currentRoleId = btn.dataset.roleid;
            document.getElementById('roleId').value = currentRoleId;
            document.getElementById('roleNameField').value = btn.dataset.name;
            document.getElementById('roleModalLabel').textContent = 'Edit Role';
            document.getElementById('roleSubmitBtn').textContent = 'Update';
            clearModalAlert();
            new bootstrap.Modal(document.getElementById('roleModal')).show();
        });
    });

    // Submit create/edit
    document.getElementById('roleSubmitBtn').addEventListener('click', function () {
        const name = document.getElementById('roleNameField').value.trim();
        if (!name) {
            showModalAlert('Name is required.');
            return;
        }

        const url = currentMode === 'create'
            ? storeUrl
            : storeUrl.replace('/roles', '/roles/' + currentRoleId).replace(/\/roles$/, '/roles/' + currentRoleId);

        let fetchUrl, method;
        if (currentMode === 'create') {
            fetchUrl = storeUrl;
            method = 'POST';
        } else {
            fetchUrl = `/roles/${currentRoleId}`;
            method = 'PATCH';
        }

        fetch(fetchUrl, {
            method: method,
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json',
            },
            body: JSON.stringify({ name: name }),
        })
        .then(async res => {
            const data = await res.json();
            if (!res.ok) {
                const msg = data.errors ? Object.values(data.errors).flat().join(' ') : (data.message || 'An error occurred.');
                showModalAlert(msg);
                return;
            }
            bootstrap.Modal.getInstance(document.getElementById('roleModal')).hide();
            location.reload();
        })
        .catch(() => showModalAlert('Request failed.'));
    });

    // Open delete confirm modal
    let deleteRoleId = null;
    document.querySelectorAll('.delete-btn').forEach(btn => {
        btn.addEventListener('click', () => {
            deleteRoleId = btn.dataset.roleid;
            document.getElementById('deleteRoleName').textContent = btn.dataset.name;
            new bootstrap.Modal(document.getElementById('roleDeleteModal')).show();
        });
    });

    // Confirm delete
    document.getElementById('roleDeleteConfirmBtn').addEventListener('click', function () {
        if (!deleteRoleId) return;
        fetch(`/roles/${deleteRoleId}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json',
            },
        })
        .then(async res => {
            const data = await res.json();
            if (!res.ok) {
                bootstrap.Modal.getInstance(document.getElementById('roleDeleteModal')).hide();
                show_Error_Message({ message: data.message || 'Could not delete role.' });
                return;
            }
            bootstrap.Modal.getInstance(document.getElementById('roleDeleteModal')).hide();
            document.getElementById(`role-${deleteRoleId}`)?.remove();
        })
        .catch(() => show_Error_Message({ message: 'Request failed.' }));
    });
});
</script>
@endsection
