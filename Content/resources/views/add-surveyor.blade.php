@extends('master')
@section('content')

<!-- Alert for success/error messages -->
<div id="alertMessage" class="alert alert-dismissible fade" role="alert"
     style="position: fixed; top: 20px; right: 20px; z-index: 1050; display: none; max-width: 90%; width: auto;">
    <span id="alertText"></span>
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>

<div class="content-body">
    <div class="container-fluid px-2 px-sm-3">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header border-0 pb-0 px-2 px-sm-3 d-flex flex-column flex-sm-row justify-content-between align-items-start align-items-sm-center gap-2">
                        <h4 style="font-family: 'Reddit Sans'; font-weight: 600; letter-spacing: 1px;"
                            class="card-title mb-0 fs-6 fs-sm-5">
                            <i class="bi bi-people-fill me-2"></i>Surveyors Management
                        </h4>
                        <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal"
                                data-bs-target="#addSurveyorModal" style="min-width: 180px;">
                            <i class="bi bi-plus-circle me-1"></i>Add New Surveyor
                        </button>
                    </div>

                    <div class="card-body px-2 px-sm-3">
                        <!-- Desktop Table View -->
                        <div class="table-responsive">
                            <table id="surveyorsTable" class="table table-bordered table-striped table-hover nowrap"
                                   style="width:100%">
                                <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Mobile</th>
                                    <th>Code</th>
                                    <th>Created At</th>
                                    <th>Actions</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach ($surveyors as $surveyor)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $surveyor->name }}</td>
                                        <td>{{ $surveyor->email }}</td>
                                        <td>{{ $surveyor->mob_no ?? 'N/A' }}</td>
                                        <td>{{ $surveyor->code ?? 'N/A' }}</td>
                                        <td>{{ $surveyor->created_at ? date('d M Y', strtotime($surveyor->created_at)) : 'N/A' }}</td>
                                        <td>
                                            <div class="d-flex flex-column flex-sm-row gap-1 justify-content-center">
                                                <button type="button" class="btn btn-sm btn-info text-white edit-btn"
                                                        data-id="{{ $surveyor->id }}"
                                                        data-name="{{ $surveyor->name }}"
                                                        data-email="{{ $surveyor->email }}"
                                                        data-mobile="{{ $surveyor->mob_no ?? '' }}"
                                                        data-code="{{ $surveyor->code }}">
                                                    <i class="bi bi-pencil-square me-1"></i>Edit
                                                </button>
                                                <button type="button" class="btn btn-sm btn-warning text-white resend-btn"
                                                        data-id="{{ $surveyor->id }}"
                                                        data-name="{{ $surveyor->name }}"
                                                        data-email="{{ $surveyor->email }}">
                                                    <i class="bi bi-envelope-arrow-up me-1"></i>Resend
                                                </button>
                                                {{-- <button type="button" class="btn btn-sm btn-danger delete-btn"
                                                        data-id="{{ $surveyor->id }}"
                                                        data-name="{{ $surveyor->name }}">
                                                    <i class="bi bi-trash me-1"></i>Delete
                                                </button> --}}
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

   <!-- Add Surveyor Modal -->
<div class="modal fade" id="addSurveyorModal" tabindex="-1" aria-labelledby="addSurveyorModalLabel"
     aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content border-0" style="border-radius: 15px;">
            <div class="modal-header text-white border-0"
                 style="background: linear-gradient(135deg, #007bff 0%, #0056b3 100%); border-radius: 15px 15px 0 0;">
                <h5 class="modal-title fs-6" id="addSurveyorModalLabel">
                    <i class="bi bi-person-plus me-2"></i>Add New Surveyor
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
            </div>

            <div class="modal-body p-3 p-sm-4">
                <form id="addSurveyorForm">
                    @csrf
                    <div class="mb-3">
                        <label for="add_name" class="form-label fw-semibold small">
                            <i class="bi bi-person me-1"></i>Name:
                        </label>
                        <input type="text" name="name" id="add_name" class="form-control form-control-sm" required>
                    </div>

                    <div class="mb-3">
                        <label for="add_email" class="form-label fw-semibold small">
                            <i class="bi bi-envelope me-1"></i>Email:
                        </label>
                        <input type="email" name="email" id="add_email" class="form-control form-control-sm" required>
                    </div>

                    <div class="mb-3">
                        <label for="add_mobile" class="form-label fw-semibold small">
                            <i class="bi bi-telephone me-1"></i>Mobile:
                        </label>
                        <input type="text" name="mob_no" id="add_mobile" class="form-control form-control-sm" required
                               pattern="^[0-9]{10,15}$"
                               title="Enter valid mobile number">
                    </div>

                    <div class="mb-3">
                        <label for="add_code" class="form-label fw-semibold small">
                            <i class="bi bi-key me-1"></i>Code:
                        </label>
                        <input type="text" name="code" id="add_code" class="form-control form-control-sm">
                    </div>

                    <div class="alert alert-info d-flex align-items-start" style="font-size: 13px;">
                        <i class="bi bi-info-circle-fill me-2 mt-1"></i>
                        <div>
                            <strong>Auto-Generated Password:</strong><br>
                            A secure password will be automatically generated and sent to the surveyor's email address along with login instructions.
                        </div>
                    </div>
                </form>
            </div>

            <div class="modal-footer border-0 p-3 p-sm-4 flex-column flex-sm-row gap-2">
                <button type="button" id="saveSurveyorBtn" class="btn btn-primary rounded-pill px-4 w-100 w-sm-auto order-2 order-sm-1">
                    <i class="bi bi-save me-1"></i> Save Surveyor
                </button>
                <button type="button" class="btn btn-outline-secondary rounded-pill px-4 w-100 w-sm-auto order-1 order-sm-2" data-bs-dismiss="modal">
                    <i class="bi bi-x-circle me-1"></i> Cancel
                </button>
            </div>
        </div>
    </div>
</div>

    <!-- Edit Surveyor Modal -->
    <div class="modal fade" id="editSurveyorModal" tabindex="-1" aria-labelledby="editSurveyorModalLabel"
         aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content border-0" style="border-radius: 15px;">
                <div class="modal-header text-white border-0"
                     style="background: linear-gradient(135deg, #17a2b8 0%, #117a8b 100%); border-radius: 15px 15px 0 0;">
                    <h5 class="modal-title fs-6" id="editSurveyorModalLabel">
                        <i class="bi bi-pencil-square me-2"></i>Edit Surveyor
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                            aria-label="Close"></button>
                </div>

                <div class="modal-body p-3 p-sm-4">
                    <form id="editSurveyorForm">
                        @csrf
                        <input type="hidden" name="id" id="edit_id">

                        <div class="mb-3">
                            <label for="edit_name" class="form-label fw-semibold small">
                                <i class="bi bi-person me-1"></i>Name:
                            </label>
                            <input type="text" name="name" id="edit_name" class="form-control form-control-sm" required>
                        </div>

                        <div class="mb-3">
                            <label for="edit_email" class="form-label fw-semibold small">
                                <i class="bi bi-envelope me-1"></i>Email:
                            </label>
                            <input type="email" name="email" id="edit_email" class="form-control form-control-sm" required>
                        </div>

                        <div class="mb-3">
                            <label for="edit_mobile" class="form-label fw-semibold small">
                                <i class="bi bi-telephone me-1"></i>Mobile:
                            </label>
                            <input type="text" name="mob_no" id="edit_mobile" class="form-control form-control-sm" required
                                   pattern="^[0-9]{10,15}$"
                                   title="Enter valid mobile number">
                        </div>

                        <div class="mb-3">
                            <label for="edit_code" class="form-label fw-semibold small">
                                <i class="bi bi-key me-1"></i>Code:
                            </label>
                            <input type="text" name="code" id="edit_code" class="form-control form-control-sm">
                        </div>
                    </form>
                </div>

                <div class="modal-footer border-0 p-3 p-sm-4 flex-column flex-sm-row gap-2">
                    <button type="button" id="updateSurveyorBtn" class="btn btn-info text-white rounded-pill px-4 w-100 w-sm-auto order-2 order-sm-1">
                        <i class="bi bi-save me-1"></i> Update Surveyor
                    </button>
                    <button type="button" class="btn btn-outline-secondary rounded-pill px-4 w-100 w-sm-auto order-1 order-sm-2" data-bs-dismiss="modal">
                        <i class="bi bi-x-circle me-1"></i> Cancel
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Confirmation Modal for Resend Email -->
    <div class="modal fade" id="resendConfirmModal" tabindex="-1" aria-labelledby="resendConfirmModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0" style="border-radius: 15px;">
                <div class="modal-header text-white border-0"
                     style="background: linear-gradient(135deg, #0062cc 0%, #004085 100%);  border-radius: 15px 15px 0 0;">
                    <h5 class="modal-title fs-6" id="resendConfirmModalLabel">
                        <i class="bi bi-envelope-exclamation me-2"></i>Confirm Resend Email
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                            aria-label="Close"></button>
                </div>
                
                <div class="modal-body p-4 text-center">
                    <div class="mb-3">
                        <i class="bi bi-envelope-paper text-warning" style="font-size: 3rem;"></i>
                    </div>
                    <h5 class="mb-3" id="resendConfirmText"></h5>
                    <p class="text-muted mb-0">
                        
                    </p>
                </div>
                
                <div class="modal-footer border-0 p-3 p-sm-4 flex-column flex-sm-row gap-2">
                    <button type="button" id="confirmResendBtn" class="btn btn-primary text-white rounded-pill px-4 w-100 w-sm-auto order-2 order-sm-1">
                        <i class="bi bi-envelope-arrow-up me-1"></i> Yes, Resend Email
                    </button>
                    <button type="button" class="btn btn-outline-secondary rounded-pill px-4 w-100 w-sm-auto order-1 order-sm-2" data-bs-dismiss="modal">
                        <i class="bi bi-x-circle me-1"></i> Cancel
                    </button>
                </div>
            </div>
        </div>
    </div>

    <style>
        /* Table Styles */
        #surveyorsTable thead tr th,
        table.dataTable thead th {
            background-color: #f8f9fa !important;
            color: #212529 !important;
            font-family: 'Calibri';
            text-align: center;
            font-size: 13px;
        }

        #surveyorsTable tbody tr td {
            vertical-align: middle;
            padding: 12px 8px;
            font-size: 13px;
            text-align: center;
        }

        #surveyorsTable tbody tr:nth-child(even) {
            background-color: #f8f9fa;
        }

        #surveyorsTable tbody tr:hover {
            background-color: #d1e7fd !important;
        }

        /* Button Styles */
        .btn-warning {
            background: linear-gradient(135deg, #ffc107 0%, #e0a800 100%) !important;
            border: none !important;
            color: #212529 !important;
        }

        .btn-warning:hover {
            background: linear-gradient(135deg, #e0a800 0%, #d39e00 100%) !important;
            color: #212529 !important;
        }

        /* Responsive and Button Styles */
        .dt-buttons {
            margin-bottom: 15px;
            float: right;
        }

        .dataTables_filter {
            float: left !important;
            text-align: left !important;
        }

        .dataTables_filter label {
            color: black !important;
            font-weight: 500;
        }

        .dataTables_filter input {
            margin-left: 0.5em;
            border: 1px solid #ced4da;
            border-radius: 4px;
            padding: 5px 10px;
        }

        .dt-buttons .btn {
            padding: 6px 12px;
            font-size: 13px;
            margin-right: 5px;
        }

        .btn-info {
            background: linear-gradient(135deg, #17a2b8 0%, #138496 100%) !important;
            border: none !important;
        }

        .btn-danger {
            background: linear-gradient(135deg, #dc3545 0%, #c82333 100%) !important;
            border: none !important;
        }

        .modal-content {
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2);
        }

        /* Mobile Responsive Styles */
        @media (max-width: 576px) {
            #surveyorsTable {
                font-size: 11px;
            }
            
            #surveyorsTable thead tr th {
                font-size: 11px;
                padding: 8px 4px;
            }
            
            #surveyorsTable tbody tr td {
                font-size: 11px;
                padding: 8px 4px;
            }
            
            .btn-sm {
                font-size: 11px;
                padding: 4px 8px;
            }
            
            .dt-buttons .btn {
                font-size: 11px;
                padding: 4px 8px;
                margin-right: 3px;
                margin-bottom: 5px;
            }
            
            .modal-body {
                max-height: 60vh;
                overflow-y: auto;
            }
            
            #alertMessage {
                font-size: 13px;
                right: 10px;
                top: 10px;
            }

            .btn-primary[data-bs-target="#addSurveyorModal"] {
                width: 100% !important;
                min-width: auto !important;
            }
            
            /* Mobile responsive for resend button */
            .resend-btn .bi {
                margin-right: 0 !important;
            }
            
            .resend-btn span:not(.bi) {
                display: none;
            }
            
            .resend-btn::after {
                content: "Resend";
                display: inline;
            }
            
            .resend-btn {
                min-width: 65px;
            }
        }

        @media (max-width: 768px) {
            .dt-buttons {
                float: none;
                text-align: center;
                margin-bottom: 10px;
            }
            
            .dataTables_filter {
                float: none !important;
                text-align: center !important;
                margin-bottom: 10px;
            }
        }
    </style>

    <script>
        $(document).ready(function () {
            var table = $('#surveyorsTable').DataTable({
                paging: false,
                ordering: true,
                responsive: true,
                pageLength: 25,
                dom: '<"row"<"col-sm-12 col-md-6"f><"col-sm-12 col-md-6 text-end"B>>rtip',
                buttons: [
                    {extend: 'copy', text: '<i class="fas fa-copy me-1"></i>Copy', className: 'btn btn-sm btn-secondary'},
                    {extend: 'csv', text: '<i class="fas fa-file-csv me-1"></i>CSV', className: 'btn btn-sm btn-success'},
                    {extend: 'excel', text: '<i class="fas fa-file-excel me-1"></i>Excel', className: 'btn btn-sm btn-success'},
                    {extend: 'pdf', text: '<i class="fas fa-file-pdf me-1"></i>PDF', className: 'btn btn-sm btn-danger'},
                    {extend: 'print', text: '<i class="fas fa-print me-1"></i>Print', className: 'btn btn-sm btn-primary'}
                ],
                language: {
                    search: "",
                    searchPlaceholder: "Search..."
                }
            });

            function showAlert(message, type) {
                var alertEl = $('#alertMessage');
                var alertText = $('#alertText');
                alertEl.removeClass('alert-success alert-danger alert-info alert-warning');
                alertEl.addClass('alert-' + type);
                alertText.text(message);
                alertEl.fadeIn().addClass('show');
                setTimeout(function () {
                    alertEl.fadeOut().removeClass('show');
                }, 10000);
            }

            // Add Surveyor
            $('#saveSurveyorBtn').on('click', function() {
                var $btn = $(this);
                var form = $('#addSurveyorForm')[0];

                if (!form.checkValidity()) {
                    form.reportValidity();
                    return;
                }

                $btn.prop('disabled', true).html(
                    '<span class="spinner-border spinner-border-sm me-1"></span>Saving...');

                $.ajax({
                    url: '{{ url("/admin/store-surveyor") }}',
                    method: 'POST',
                    data: $('#addSurveyorForm').serialize(),
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        if (response.success) {
                            showAlert('Surveyor added successfully! Welcome email sent with credentials.', 'success');
                            $('#addSurveyorModal').modal('hide');
                            $('#addSurveyorForm')[0].reset();
                            setTimeout(function() {
                                location.reload();
                            }, 2000);
                        } else {
                            showAlert(response.message || 'Failed to add surveyor', 'danger');
                        }
                    },
                    error: function(xhr) {
                        var message = 'Error adding surveyor';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            message = xhr.responseJSON.message;
                        } else if (xhr.responseJSON && xhr.responseJSON.errors) {
                            var errors = xhr.responseJSON.errors;
                            message = Object.values(errors).flat().join(', ');
                        }
                        showAlert(message, 'danger');
                    },
                    complete: function() {
                        $btn.prop('disabled', false).html(
                            '<i class="bi bi-save me-1"></i> Save Surveyor');
                    }
                });
            });

            // Edit Button Click
            $(document).on('click', '.edit-btn', function() {
                var id = $(this).data('id');
                var name = $(this).data('name');
                var email = $(this).data('email');
                var mobile = $(this).data('mobile');
                var code = $(this).data('code');

                $('#edit_id').val(id);
                $('#edit_name').val(name);
                $('#edit_email').val(email);
                $('#edit_mobile').val(mobile);
                $('#edit_code').val(code);

                $('#editSurveyorModal').modal('show');
            });

            // Update Surveyor
            $('#updateSurveyorBtn').on('click', function() {
                var $btn = $(this);
                var form = $('#editSurveyorForm')[0];

                if (!form.checkValidity()) {
                    form.reportValidity();
                    return;
                }

                $btn.prop('disabled', true).html(
                    '<span class="spinner-border spinner-border-sm me-1"></span>Updating...');

                var id = $('#edit_id').val();
                var formData = $('#editSurveyorForm').serialize();

                $.ajax({
                    url: '{{ url("/admin/update-surveyor") }}/' + id,
                    method: 'POST',
                    data: formData,
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        if (response.success) {
                            showAlert('Surveyor updated successfully!', 'success');
                            $('#editSurveyorModal').modal('hide');
                            setTimeout(function() {
                                location.reload();
                            }, 1500);
                        } else {
                            showAlert(response.message || 'Failed to update surveyor', 'danger');
                        }
                    },
                    error: function(xhr) {
                        var message = 'Error updating surveyor';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            message = xhr.responseJSON.message;
                        } else if (xhr.responseJSON && xhr.responseJSON.errors) {
                            var errors = xhr.responseJSON.errors;
                            message = Object.values(errors).flat().join(', ');
                        } else if (xhr.status === 404) {
                            message = 'Surveyor not found or invalid route';
                        } else if (xhr.status === 500) {
                            message = 'Server error. Please check the console for details.';
                        }
                        showAlert(message, 'danger');
                    },
                    complete: function() {
                        $btn.prop('disabled', false).html(
                            '<i class="bi bi-save me-1"></i> Update Surveyor');
                    }
                });
            });

            // Resend Welcome Email - Bootstrap Modal Confirmation
            $(document).on('click', '.resend-btn', function() {
                var id = $(this).data('id');
                var name = $(this).data('name');
                var email = $(this).data('email');
                
                // Store data for later use
                $('#confirmResendBtn').data('id', id);
                $('#confirmResendBtn').data('name', name);
                $('#confirmResendBtn').data('email', email);
                $('#confirmResendBtn').data('original-btn', $(this));
                
                // Update modal text
                $('#resendConfirmText').text('Resend welcome email to "' + name + '" (' + email + ')?');
                
                // Show confirmation modal
                $('#resendConfirmModal').modal('show');
            });

            // Handle confirmation button click
            $('#confirmResendBtn').on('click', function() {
                var id = $(this).data('id');
                var name = $(this).data('name');
                var email = $(this).data('email');
                var $originalBtn = $(this).data('original-btn');
                
                var $btn = $(this);
                var $originalButton = $originalBtn;
                var originalText = $originalButton.html();
                
                // Close the confirmation modal
                $('#resendConfirmModal').modal('hide');
                
                // Show loading on original button
                $originalButton.prop('disabled', true).html(
                    '<span class="spinner-border spinner-border-sm me-1"></span>Sending...');
                
                $.ajax({
                    url: '{{ url("/admin/resend-welcome-email") }}/' + id,
                    method: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        if (response.success) {
                            showAlert('Welcome email resent successfully to ' + email, 'success');
                        } else {
                            showAlert(response.message || 'Failed to resend email', 'danger');
                        }
                    },
                    error: function(xhr) {
                        var message = 'Error resending email';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            message = xhr.responseJSON.message;
                        } else if (xhr.responseJSON && xhr.responseJSON.errors) {
                            var errors = xhr.responseJSON.errors;
                            message = Object.values(errors).flat().join(', ');
                        } else if (xhr.status === 404) {
                            message = 'Surveyor not found';
                        } else if (xhr.status === 500) {
                            message = 'Server error. Please try again.';
                        }
                        showAlert(message, 'danger');
                    },
                    complete: function() {
                        setTimeout(function() {
                            $originalButton.prop('disabled', false).html(originalText);
                        }, 2000);
                    }
                });
            });

            // Reset modals on close
            $('#addSurveyorModal').on('hidden.bs.modal', function() {
                $('#addSurveyorForm')[0].reset();
            });

            $('#editSurveyorModal').on('hidden.bs.modal', function() {
                $('#editSurveyorForm')[0].reset();
            });
            
            $('#resendConfirmModal').on('hidden.bs.modal', function() {
                // Clear stored data when modal is closed
                $('#confirmResendBtn').removeData('id');
                $('#confirmResendBtn').removeData('name');
                $('#confirmResendBtn').removeData('email');
                $('#confirmResendBtn').removeData('original-btn');
            });
        });
    </script>
@endsection