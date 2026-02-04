@extends('layouts.app')

@section('style')
<style>
    .template-container {
        padding: 20px;
    }

    .search-section {
        margin-bottom: 20px;
        display: flex;
        gap: 15px;
        align-items: flex-end;
    }

    .search-input {
        flex: 1;
        min-width: 300px;
    }

    .action-buttons {
        display: flex;
        gap: 10px;
    }

    .templates-table {
        width: 100%;
        border-collapse: collapse;
        background: transparent;
        border-radius: 8px;
        overflow: hidden;
    }

    .templates-table thead {
        border-bottom: 2px solid rgba(255, 255, 255, 0.1);
    }

    .templates-table th {
        padding: 15px;
        text-align: left;
        font-weight: 600;
        color: #fff;
        background: rgba(0, 0, 0, 0.2);
    }

    .templates-table td {
        padding: 15px;
        color: rgba(255, 255, 255, 0.8);
        border-bottom: 1px solid rgba(255, 255, 255, 0.05);
    }

    .templates-table tbody tr:hover {
        background: rgba(255, 255, 255, 0.05);
        transition: background 0.2s;
    }

    .row-actions {
        display: flex;
        gap: 8px;
    }

    .btn-action {
        padding: 6px 12px;
        font-size: 13px;
        border: none;
        border-radius: 4px;
        cursor: pointer;
        transition: all 0.2s;
        display: inline-flex;
        align-items: center;
        gap: 5px;
    }

    .btn-view {
        background: #0d6efd;
        color: white;
    }

    .btn-view:hover {
        background: #0956ca;
    }

    .btn-jobs {
        background: #198754;
        color: white;
    }

    .btn-jobs:hover {
        background: #146c43;
    }

    .btn-delete {
        background: #dc3545;
        color: white;
    }

    .btn-delete:hover {
        background: #bb2d3b;
    }

    .pagination-section {
        margin-top: 20px;
        display: flex;
        justify-content: center;
        gap: 10px;
        align-items: center;
    }

    .pagination-info {
        color: rgba(255, 255, 255, 0.7);
        font-size: 14px;
    }

    .modal-backdrop {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.7);
        z-index: 999;
    }

    .modal-backdrop.active {
        display: block;
    }

    .modal {
        display: none;
        position: fixed;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        background: #212529;
        border: 1px solid rgba(255, 255, 255, 0.1);
        border-radius: 8px;
        box-shadow: 0 10px 40px rgba(0, 0, 0, 0.5);
        z-index: 1000;
        min-width: 600px;
        max-width: 90%;
        max-height: 90%;
        overflow-y: auto;
    }

    .modal.active {
        display: block;
    }

    .modal-header {
        padding: 20px;
        border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .modal-title {
        font-size: 20px;
        font-weight: 600;
        margin: 0;
        color: #fff;
    }

    .modal-close {
        background: none;
        border: none;
        font-size: 24px;
        cursor: pointer;
        color: rgba(255, 255, 255, 0.6);
    }

    .modal-close:hover {
        color: rgba(255, 255, 255, 0.9);
    }

    .modal-body {
        padding: 20px;
    }

    .modal-footer {
        padding: 15px 20px;
        border-top: 1px solid rgba(255, 255, 255, 0.1);
        display: flex;
        justify-content: flex-end;
        gap: 10px;
    }

    .empty-state {
        text-align: center;
        padding: 40px;
        color: rgba(255, 255, 255, 0.5);
    }

    .empty-state i {
        font-size: 48px;
        margin-bottom: 20px;
        opacity: 0.3;
    }

    .template-info-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 15px;
        margin-bottom: 20px;
    }

    .info-item {
        padding: 10px;
        background: rgba(255, 255, 255, 0.05);
        border: 1px solid rgba(255, 255, 255, 0.1);
        border-radius: 4px;
    }

    .info-label {
        font-size: 12px;
        color: rgba(255, 255, 255, 0.6);
        font-weight: 600;
        text-transform: uppercase;
        margin-bottom: 5px;
    }

    .info-value {
        font-size: 16px;
        color: #fff;
    }

    .loading {
        text-align: center;
        padding: 20px;
        color: rgba(255, 255, 255, 0.6);
    }

    .loading::after {
        content: '';
        display: inline-block;
        width: 20px;
        height: 20px;
        border-top: 3px solid #0d6efd;
        border-radius: 50%;
        animation: spin 1s linear infinite;
        margin-left: 10px;
    }

    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }

    .success-message {
        background: rgba(25, 135, 84, 0.2);
        color: #86efac;
        padding: 12px 15px;
        border: 1px solid rgba(25, 135, 84, 0.4);
        border-radius: 4px;
        margin-bottom: 15px;
        display: none;
    }

    .success-message.show {
        display: block;
    }

    .error-message {
        background: rgba(220, 53, 69, 0.2);
        color: #fca5a5;
        padding: 12px 15px;
        border: 1px solid rgba(220, 53, 69, 0.4);
        border-radius: 4px;
        margin-bottom: 15px;
        display: none;
    }

    .error-message.show {
        display: block;
    }

    /* Bootstrap dark theme form control overrides */
    .form-control, .form-select {
        background: #3a3f45;
        border: 1px solid rgba(255, 255, 255, 0.1);
        color: #fff;
    }

    .form-control:focus, .form-select:focus {
        background: #3a3f45;
        border-color: #0d6efd;
        color: #fff;
        box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
    }

    .form-label {
        color: rgba(255, 255, 255, 0.8);
    }

    .form-check-label {
        color: rgba(255, 255, 255, 0.8);
    }
</style>
@endsection

@section('content')
<div class="template-container">
    <!-- Header -->
    <div style="margin-bottom: 30px;">
        <h1 class="text-light" style="margin: 0 0 10px 0;">Job Templates</h1>
        <p class="text-muted" style="margin: 0;">Create and manage job templates for batch job creation</p>
    </div>

    <!-- Search and Actions -->
    <div class="search-section">
        <div class="search-input">
            <input type="text" id="search-input" class="form-control" placeholder="Search by ID or Name...">
        </div>
        <div class="action-buttons">
            <button id="btn-create-template" class="btn btn-success">
                <i class="fas fa-plus"></i> Create Template
            </button>
        </div>
    </div>

    <!-- Success/Error Messages -->
    <div id="success-message" class="success-message"></div>
    <div id="error-message" class="error-message"></div>

    <!-- Templates Table -->
    <div id="templates-container" class="loading">
        Loading templates...
    </div>

    <!-- Pagination -->
    <div id="pagination-container" class="pagination-section"></div>
</div>

<!-- Template Info Modal -->
<div id="modal-backdrop" class="modal-backdrop"></div>
<div id="template-modal" class="modal">
    <div class="modal-header">
        <h2 class="modal-title">Edit Template</h2>
        <button class="modal-close" id="modal-close-btn">&times;</button>
    </div>
    <div class="modal-body text-light" id="modal-body-content">
        <!-- Content loaded here -->
    </div>
    <div class="modal-footer">
        <button id="modal-delete-btn" class="btn btn-danger" onclick="handleDeleteTemplate(selectedTemplateId)" style="margin-right: auto;">Delete</button>
        <button id="modal-close-footer-btn" class="btn btn-secondary">Cancel</button>
        <button id="modal-save-btn" class="btn btn-success" onclick="handleUpdateTemplate()">Save Changes</button>
    </div>
</div>

<!-- Create Template Modal -->
<div id="create-template-modal" class="modal">
    <div class="modal-header">
        <h2 class="modal-title">Create New Template</h2>
        <button class="modal-close" id="create-template-close-btn">&times;</button>
    </div>
    <div class="modal-body text-light">
        <form id="create-template-form">
            <div class="form-group mb-3">
                <label for="template-name" class="form-label">Template Name *</label>
                <input type="text" id="template-name" class="form-control" placeholder="e.g., Morning Route" required>
            </div>

            <div class="form-group mb-3">
                <label for="template-client" class="form-label">Client *</label>
                <select id="template-client" class="form-control form-select" required>
                    <option value="">Select a client...</option>
                </select>
            </div>

            <div class="form-group mb-3">
                <label for="template-pickup-address" class="form-label">Pickup Address</label>
                <select id="template-pickup-address" class="form-control form-select">
                    <option value="">Select an address...</option>
                </select>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                <div class="form-group mb-3">
                    <label for="template-pickup-begin" class="form-label">Pickup Time Begin</label>
                    <input type="time" id="template-pickup-begin" class="form-control">
                </div>

                <div class="form-group mb-3">
                    <label for="template-pickup-end" class="form-label">Pickup Time End</label>
                    <input type="time" id="template-pickup-end" class="form-control">
                </div>
            </div>

            <p class="text-muted" style="font-size: 14px; margin-top: 15px;">
                <strong>Note:</strong> You can add pickup/dropoff details after creating the template by editing it.
            </p>
        </form>
    </div>
    <div class="modal-footer">
        <button id="create-template-cancel-btn" class="btn btn-secondary">Cancel</button>
        <button id="create-template-submit-btn" class="btn btn-success">Create Template</button>
    </div>
</div>

<!-- Create Jobs Modal -->
<div id="create-jobs-modal" class="modal">
    <div class="modal-header">
        <h2 class="modal-title">Create Jobs from Template</h2>
        <button class="modal-close" id="create-jobs-close-btn">&times;</button>
    </div>
    <div class="modal-body text-light">
        <div class="template-info-grid">
            <div class="info-item">
                <div class="info-label">Template ID</div>
                <div class="info-value" id="create-jobs-template-id">-</div>
            </div>
            <div class="info-item">
                <div class="info-label">Template Name</div>
                <div class="info-value" id="create-jobs-template-name">-</div>
            </div>
        </div>

        <form id="create-jobs-form">
            <div class="form-group mb-3">
                <label for="start-date" class="form-label">Start Date</label>
                <input type="date" id="start-date" class="form-control" required>
            </div>

            <div class="form-group mb-3">
                <label for="end-date" class="form-label">End Date</label>
                <input type="date" id="end-date" class="form-control" required>
            </div>

            <div class="form-group mb-3">
                <label class="form-label">Days of Week</label>
                <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 10px;">
                    @php
                        $days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
                    @endphp
                    @foreach ($days as $day)
                        <div class="form-check">
                            <input class="form-check-input day-checkbox" type="checkbox" value="{{ $day }}" id="day-{{ strtolower($day) }}">
                            <label class="form-check-label" for="day-{{ strtolower($day) }}">
                                {{ $day }}
                            </label>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">Summary</label>
                <div style="padding: 10px; background: rgba(255, 255, 255, 0.05); border: 1px solid rgba(255, 255, 255, 0.1); border-radius: 4px; font-size: 14px; color: rgba(255, 255, 255, 0.8);">
                    <span id="jobs-summary">Select dates and days above</span>
                </div>
            </div>
        </form>
    </div>
    <div class="modal-footer">
        <button id="create-jobs-cancel-btn" class="btn btn-secondary">Cancel</button>
        <button id="create-jobs-submit-btn" class="btn btn-success">Create Jobs</button>
    </div>
</div>

@endsection

@push('scripts')
@vite('resources/js/jobtemplate/index.js')
@endpush
