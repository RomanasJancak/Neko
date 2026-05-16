@extends('layouts.app')

@section('title', 'Role Hierarchy')

@section('content')
<div class="container">

    <div class="row mb-3">
        <div class="col-md-12">
            <h1>Role Hierarchy</h1>
            <p class="text-muted">A parent role inherits from (or encompasses) one or more child roles. A child role can have multiple parents.</p>
        </div>
    </div>

    {{-- Add relationship form --}}
    <div class="card mb-4">
        <div class="card-header">Add Relationship</div>
        <div class="card-body">
            <div id="formAlert" class="alert d-none" role="alert"></div>
            <div class="row g-3 align-items-end">
                <div class="col-md-4">
                    <label for="parentSelect" class="form-label">Parent Role</label>
                    <select id="parentSelect" class="form-select">
                        <option value="">— select —</option>
                        @foreach($roles as $role)
                            <option value="{{ $role->id }}">{{ $role->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <label for="childSelect" class="form-label">Child Role</label>
                    <select id="childSelect" class="form-select">
                        <option value="">— select —</option>
                        @foreach($roles as $role)
                            <option value="{{ $role->id }}">{{ $role->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <button id="addBtn" class="btn btn-primary w-100">
                        <i class="bi bi-plus-lg"></i> Add
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- Existing links table --}}
    <div class="card">
        <div class="card-header">Existing Relationships</div>
        <div class="card-body p-0">
            <table class="table table-bordered table-striped mb-0" id="hierarchyTable">
                <thead>
                    <tr>
                        <th>Parent Role</th>
                        <th>Child Role</th>
                        <th style="width:100px">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($links as $link)
                    <tr id="row-{{ $link->parent_role_id }}-{{ $link->child_role_id }}">
                        <td>{{ $link->parent_name }}</td>
                        <td>{{ $link->child_name }}</td>
                        <td>
                            <button class="btn btn-danger btn-sm delete-btn"
                                data-parent="{{ $link->parent_role_id }}"
                                data-child="{{ $link->child_role_id }}"
                                data-parent-name="{{ $link->parent_name }}"
                                data-child-name="{{ $link->child_name }}">
                                <i class="bi bi-trash"></i>
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr id="emptyRow">
                        <td colspan="3" class="text-center text-muted">No relationships defined yet.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- Delete confirm modal --}}
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteModalLabel">Confirm Delete</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                Remove relationship: <strong id="deleteDesc"></strong>?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" id="deleteConfirmBtn" class="btn btn-danger">Delete</button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    const storeUrl  = "{{ route('role.hierarchy.store') }}";

    // ── Helpers ─────────────────────────────────────────────────────────────────
    function showAlert(el, message, type = 'danger') {
        el.className = `alert alert-${type}`;
        el.textContent = message;
        el.classList.remove('d-none');
    }

    function hideAlert(el) {
        el.classList.add('d-none');
        el.textContent = '';
    }

    function removeEmptyRow() {
        const empty = document.getElementById('emptyRow');
        if (empty) empty.remove();
    }

    function appendRow(parentId, childId, parentName, childName) {
        removeEmptyRow();
        const tbody = document.querySelector('#hierarchyTable tbody');
        const tr = document.createElement('tr');
        tr.id = `row-${parentId}-${childId}`;
        tr.innerHTML = `
            <td>${parentName}</td>
            <td>${childName}</td>
            <td>
                <button class="btn btn-danger btn-sm delete-btn"
                    data-parent="${parentId}"
                    data-child="${childId}"
                    data-parent-name="${parentName}"
                    data-child-name="${childName}">
                    <i class="bi bi-trash"></i>
                </button>
            </td>`;
        tbody.appendChild(tr);
        bindDeleteBtn(tr.querySelector('.delete-btn'));
    }

    // ── Add ──────────────────────────────────────────────────────────────────────
    const formAlert   = document.getElementById('formAlert');
    const parentSelect = document.getElementById('parentSelect');
    const childSelect  = document.getElementById('childSelect');

    document.getElementById('addBtn').addEventListener('click', function () {
        hideAlert(formAlert);
        const parentId = parentSelect.value;
        const childId  = childSelect.value;

        if (!parentId || !childId) {
            showAlert(formAlert, 'Please select both a parent and a child role.');
            return;
        }
        if (parentId === childId) {
            showAlert(formAlert, 'A role cannot be its own parent.');
            return;
        }

        fetch(storeUrl, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json',
            },
            body: JSON.stringify({ parent_role_id: parentId, child_role_id: childId }),
        })
        .then(async res => {
            const data = await res.json();
            if (!res.ok) {
                const msg = data.errors
                    ? Object.values(data.errors).flat().join(' ')
                    : (data.message || 'An error occurred.');
                showAlert(formAlert, msg);
                return;
            }
            const parentName = parentSelect.options[parentSelect.selectedIndex].text;
            const childName  = childSelect.options[childSelect.selectedIndex].text;
            appendRow(parentId, childId, parentName, childName);
            parentSelect.value = '';
            childSelect.value  = '';
            showAlert(formAlert, 'Relationship added.', 'success');
        })
        .catch(() => showAlert(formAlert, 'Request failed.'));
    });

    // ── Delete ───────────────────────────────────────────────────────────────────
    let pendingParent = null;
    let pendingChild  = null;
    const deleteModal       = new bootstrap.Modal(document.getElementById('deleteModal'));
    const deleteConfirmBtn  = document.getElementById('deleteConfirmBtn');

    function bindDeleteBtn(btn) {
        btn.addEventListener('click', function () {
            pendingParent = btn.dataset.parent;
            pendingChild  = btn.dataset.child;
            document.getElementById('deleteDesc').textContent =
                `${btn.dataset.parentName} → ${btn.dataset.childName}`;
            deleteModal.show();
        });
    }

    document.querySelectorAll('.delete-btn').forEach(bindDeleteBtn);

    deleteConfirmBtn.addEventListener('click', function () {
        if (!pendingParent || !pendingChild) return;
        fetch(`/roles/hierarchy/${pendingParent}/${pendingChild}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json',
            },
        })
        .then(async res => {
            const data = await res.json();
            deleteModal.hide();
            if (!res.ok) {
                alert(data.message || 'Delete failed.');
                return;
            }
            const row = document.getElementById(`row-${pendingParent}-${pendingChild}`);
            if (row) row.remove();

            // Show empty state if table is now empty
            const tbody = document.querySelector('#hierarchyTable tbody');
            if (!tbody.querySelector('tr')) {
                const tr = document.createElement('tr');
                tr.id = 'emptyRow';
                tr.innerHTML = '<td colspan="3" class="text-center text-muted">No relationships defined yet.</td>';
                tbody.appendChild(tr);
            }
            pendingParent = null;
            pendingChild  = null;
        })
        .catch(() => { deleteModal.hide(); alert('Request failed.'); });
    });
});
</script>
@endsection
