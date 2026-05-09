@extends('layouts.app')

@section('title', 'Roles and Permissions')

@section('content')
    <h1>Roles and Permissions</h1>

    @if(!count($editablePermissionIds))
        <div class="alert alert-warning">You do not have permission to edit role permissions.</div>
    @endif

    <div class="table-responsive">
        <table class="table table-bordered table-sm" id="permissions-matrix-table">
            <thead class="table-light" style="position: sticky; top: 0; z-index: 1;">
                <tr>
                    <th>Permission</th>
                    @foreach($roles as $role)
                        <th>
                            <div class="d-flex align-items-center gap-2 flex-nowrap">
                                <span>{{ $role->name }}</span>
                                <button
                                    type="button"
                                    class="btn btn-sm btn-primary save-role-btn d-none"
                                    data-role-id="{{ $role->id }}"
                                    data-update-url="{{ route('role.updatePermissions', $role) }}"
                                    aria-label="Save permissions for {{ $role->name }}"
                                >Save</button>
                            </div>
                        </th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @foreach($permissions as $permission)
                    <tr>
                        <td>{{ $permission->name }}</td>
                        @foreach($roles as $role)
                            @php($canEdit = in_array($permission->id, $editablePermissionIds))
                            <td class="text-center">
                                <input
                                    type="checkbox"
                                    class="role-permission-checkbox form-check-input"
                                    data-role-id="{{ $role->id }}"
                                    data-permission-id="{{ $permission->id }}"
                                    @checked($role->permissions->contains($permission))
                                    @disabled(!$canEdit)
                                    aria-label="{{ $permission->name }} for {{ $role->name }}"
                                >
                            </td>
                        @endforeach
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    // Record initial checked state per role column.
    const initialState = {};
    document.querySelectorAll('.role-permission-checkbox').forEach(function (cb) {
        const roleId = cb.dataset.roleId;
        if (!initialState[roleId]) initialState[roleId] = {};
        initialState[roleId][cb.dataset.permissionId] = cb.checked;
    });

    function hasColumnChanges(roleId) {
        return Object.entries(initialState[roleId]).some(function ([permId, origChecked]) {
            const cb = document.querySelector(
                '.role-permission-checkbox[data-role-id="' + roleId + '"][data-permission-id="' + permId + '"]'
            );
            return cb && cb.checked !== origChecked;
        });
    }

    // Show/hide Save button when a checkbox changes.
    document.querySelectorAll('.role-permission-checkbox').forEach(function (cb) {
        cb.addEventListener('change', function () {
            const roleId = cb.dataset.roleId;
            const saveBtn = document.querySelector('.save-role-btn[data-role-id="' + roleId + '"]');
            if (saveBtn) {
                saveBtn.classList.toggle('d-none', !hasColumnChanges(roleId));
            }
        });
    });

    // Save button PATCH handler.
    document.querySelectorAll('.save-role-btn').forEach(function (btn) {
        btn.addEventListener('click', async function () {
            const roleId = btn.dataset.roleId;
            const url    = btn.dataset.updateUrl;

            const checkedIds = Array.from(
                document.querySelectorAll('.role-permission-checkbox[data-role-id="' + roleId + '"]:checked')
            ).map(function (cb) { return parseInt(cb.dataset.permissionId, 10); });

            btn.disabled    = true;
            btn.textContent = 'Saving…';

            try {
                const response = await fetch(url, {
                    method: 'PATCH',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN':  csrfToken,
                        'Accept':        'application/json',
                    },
                    body: JSON.stringify({ permissions: checkedIds }),
                });

                const data = await response.json();

                if (response.ok) {
                    // Commit new state as baseline so the button hides.
                    document.querySelectorAll('.role-permission-checkbox[data-role-id="' + roleId + '"]').forEach(function (cb) {
                        initialState[roleId][cb.dataset.permissionId] = cb.checked;
                    });
                    btn.classList.add('d-none');
                    btn.textContent = 'Save';
                    btn.disabled    = false;
                } else {
                    alert(data.message || 'Failed to save permissions.');
                    btn.textContent = 'Save';
                    btn.disabled    = false;
                }
            } catch (e) {
                alert('An error occurred while saving.');
                btn.textContent = 'Save';
                btn.disabled    = false;
            }
        });
    });
});
</script>
@endpush
