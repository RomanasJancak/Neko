@extends('layouts.app')

@section('title', 'Permissions')

@section('content')
<div class="container">
    <div class="row mb-3">
        <div class="col-md-12">
            <h1>Permissions</h1>
        </div>
        <div class="col-md-12 mb-3">
            <button class="btn btn-secondary create-btn">Add new Permission</button>
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
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody id="permissionsTableBody">
                    @foreach($permissions as $permission)
                    <tr id="permission-{{ $permission->id }}">
                        <td>{{ $permission->id }}</td>
                        <td>{{ $permission->name }}</td>
                        <td>{{ $permission->guard_name }}</td>
                        <td>
                            <button class="btn btn-primary btn-sm edit-btn"
                                data-permissionid="{{ $permission->id }}"
                                data-name="{{ $permission->name }}">
                                <i class="bi bi-pen"></i>
                            </button>
                            <button class="btn btn-danger btn-sm delete-btn"
                                data-permissionid="{{ $permission->id }}"
                                data-name="{{ $permission->name }}">
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

<!-- Create/Edit Modal -->
<div class="modal fade" id="permissionModal" tabindex="-1" aria-labelledby="permissionModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="permissionModalLabel">Permission</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="permissionModalAlert" class="alert d-none" role="alert"></div>
                <form id="permissionForm">
                    @csrf
                    <div class="mb-3">
                        <label for="permissionNameField" class="form-label">Name</label>
                        <input type="text" class="form-control" id="permissionNameField" name="name" required>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" id="permissionSubmitBtn" class="btn btn-primary">Save</button>
            </div>
        </div>
    </div>
</div>

<!-- Delete Confirm Modal -->
<div class="modal fade" id="permissionDeleteModal" tabindex="-1" aria-labelledby="permissionDeleteModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="permissionDeleteModalLabel">Confirm Delete</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete permission <strong id="deletePermissionName"></strong>?</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" id="permissionDeleteConfirmBtn" class="btn btn-danger">Delete</button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const storeUrl = "{{ route('permission.store') }}";
    let currentMode = 'create';
    let currentPermissionId = null;

    function showModalAlert(message, type = 'danger') {
        const el = document.getElementById('permissionModalAlert');
        el.className = `alert alert-${type}`;
        el.textContent = message;
        el.classList.remove('d-none');
    }

    function clearModalAlert() {
        const el = document.getElementById('permissionModalAlert');
        el.classList.add('d-none');
        el.textContent = '';
    }

    // Open create modal
    document.querySelectorAll('.create-btn').forEach(btn => {
        btn.addEventListener('click', () => {
            currentMode = 'create';
            currentPermissionId = null;
            document.getElementById('permissionNameField').value = '';
            document.getElementById('permissionModalLabel').textContent = 'Add new Permission';
            document.getElementById('permissionSubmitBtn').textContent = 'Create';
            clearModalAlert();
            new bootstrap.Modal(document.getElementById('permissionModal')).show();
        });
    });

    // Open edit modal
    document.querySelectorAll('.edit-btn').forEach(btn => {
        btn.addEventListener('click', () => {
            currentMode = 'edit';
            currentPermissionId = btn.dataset.permissionid;
            document.getElementById('permissionNameField').value = btn.dataset.name;
            document.getElementById('permissionModalLabel').textContent = 'Edit Permission';
            document.getElementById('permissionSubmitBtn').textContent = 'Update';
            clearModalAlert();
            new bootstrap.Modal(document.getElementById('permissionModal')).show();
        });
    });

    // Submit create/edit
    document.getElementById('permissionSubmitBtn').addEventListener('click', function () {
        const name = document.getElementById('permissionNameField').value.trim();
        if (!name) {
            showModalAlert('Name is required.');
            return;
        }

        const fetchUrl = currentMode === 'create' ? storeUrl : `/permissions/${currentPermissionId}`;
        const method = currentMode === 'create' ? 'POST' : 'PATCH';

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
            bootstrap.Modal.getInstance(document.getElementById('permissionModal')).hide();
            location.reload();
        })
        .catch(() => showModalAlert('Request failed.'));
    });

    // Open delete confirm modal
    let deletePermissionId = null;
    document.querySelectorAll('.delete-btn').forEach(btn => {
        btn.addEventListener('click', () => {
            deletePermissionId = btn.dataset.permissionid;
            document.getElementById('deletePermissionName').textContent = btn.dataset.name;
            new bootstrap.Modal(document.getElementById('permissionDeleteModal')).show();
        });
    });

    // Confirm delete
    document.getElementById('permissionDeleteConfirmBtn').addEventListener('click', function () {
        if (!deletePermissionId) return;
        fetch(`/permissions/${deletePermissionId}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json',
            },
        })
        .then(async res => {
            const data = await res.json();
            if (!res.ok) {
                bootstrap.Modal.getInstance(document.getElementById('permissionDeleteModal')).hide();
                show_Error_Message({ message: data.message || 'Could not delete permission.' });
                return;
            }
            bootstrap.Modal.getInstance(document.getElementById('permissionDeleteModal')).hide();
            document.getElementById(`permission-${deletePermissionId}`)?.remove();
        })
        .catch(() => show_Error_Message({ message: 'Request failed.' }));
    });
});
</script>
@endsection
