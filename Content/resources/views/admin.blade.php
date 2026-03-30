{{-- admin.blade.php --}}
<?php
use Illuminate\Support\Facades\Session;
$userEmail = Session::get('user')['email'];
?>

@extends('master')
@section('content')
    <!-- Loading Overlay -->
    <div id="loadingOverlay" style="position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(255,255,255,0.95); z-index: 9999; display: flex; align-items: center; justify-content: center; flex-direction: column;">
        <div class="spinner-border text-primary" role="status" style="width: 4rem; height: 4rem;">
            <span class="visually-hidden">Loading...</span>
        </div>
        <p class="mt-3 fw-bold text-primary">Loading data from server...</p>
        <p class="text-muted">Please wait while we fetch all records</p>
    </div>

    <div id="plrAlert" class="alert alert-success alert-dismissible fade" role="alert"
        style="position: fixed; top: 20px; right: 20px; z-index: 1050;">
        <span id="plrAlertText"></span>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>

    {{-- Stats Cards --}}
    <div class="stats-container">
        <div id="mainStatsCard" class="stats-main-card">
            <div class="stats-grid">
                <div class="stat-item">
                    <div class="stat-icon stat-icon-primary"><i class="bi bi-file-earmark-text-fill"></i></div>
                    <div class="stat-content">
                        <div class="stat-label">Total Files Assigned</div>
                        <div class="stat-value" id="totalFilesAssigned">0</div>
                    </div>
                </div>
                <div class="stat-divider"></div>
                <div class="stat-item">
                    <div class="stat-icon stat-icon-success"><i class="bi bi-check-circle-fill"></i></div>
                    <div class="stat-content">
                        <div class="stat-label">Total Approved</div>
                        <div class="stat-value stat-value-success" id="totalApproved">0</div>
                    </div>
                </div>
                <div class="stat-divider"></div>
                <div class="stat-item">
                    <div class="stat-icon stat-icon-warning"><i class="bi bi-hourglass-split"></i></div>
                    <div class="stat-content">
                        <div class="stat-label">Total In Process</div>
                        <div class="stat-value stat-value-warning" id="totalInProcess">0</div>
                    </div>
                </div>
                <div class="stat-divider"></div>
                <div class="stat-item">
                    <div class="stat-icon stat-icon-danger"><i class="bi bi-exclamation-triangle-fill"></i></div>
                    <div class="stat-content">
                        <div class="stat-label">Total Pending</div>
                        <div class="stat-value stat-value-danger" id="totalPending">0</div>
                    </div>
                </div>
            </div>
        </div>

        <div id="leftPanel">
            <div id="appointmentStats">
                <div class="stat-card" data-days="7">
                    <div class="count">0</div><span class="label">7 Days</span>
                </div>
                <div class="stat-card" data-days="15">
                    <div class="count">0</div><span class="label">15 Days</span>
                </div>
                <div class="stat-card" data-days="30">
                    <div class="count">0</div><span class="label">30 Days</span>
                </div>
                <div class="stat-card" data-days="60">
                    <div class="count">0</div><span class="label">60 Days</span>
                </div>
                <div class="stat-card" data-days="90">
                    <div class="count">0</div><span class="label">90 Days</span>
                </div>
                <div class="stat-card" data-days="90plus">
                    <div class="count">0</div><span class="label">90+ Days</span>
                </div>
                <div class="stat-card" data-days="all">
                    <div class="count">0</div><span class="label">All</span>
                </div>
                <div class="stat-card stat-card-reset" id="resetCard">
                    <div class="count"><i class="bi bi-arrow-clockwise"></i></div><span class="label">Reset</span>
                </div>
            </div>
        </div>
    </div>

    <div class="content-body">
        <div class="container-fluid px-2 px-sm-3">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header border-0 pb-0 px-2 px-sm-3">
                            <div class="d-flex justify-content-between align-items-center">
                                <h4 style="font-family: 'Reddit Sans'; font-weight: 600; letter-spacing: 1px;"
                                    class="card-title mb-0 fs-5 fs-sm-4">Admin Dashboard</h4>
                                <small id="cacheIndicator" class="text-muted" style="display: none;">
                                    <i class="bi bi-database"></i> <span id="cacheStatus"></span>
                                </small>
                            </div>
                        </div>

                        <div class="card-body px-2 px-sm-3">
                            <!-- Desktop Table View -->
                            <div class="table-responsive">
                                <table id="filesTable"
                                    class="table table-bordered table-striped table-hover nowrap d-none d-md-table"
                                    style="width:100%">
                                    <thead>
                                        <tr>
                                            <th class="date-col">Actions</th>
                                            <th class="date-col">Status</th>
                                            <th class="date-col">Days Passed</th>
                                            <th class="date-col">Last Activity</th>
                                            <th class="text-left">Claim No</th>
                                            <th>Int_Date</th>
                                            <th class="text-left">Insured</th>
                                            <th class="text-left">Cause of Loss</th>
                                            <th class="text-left">Surveyor Name</th>
                                            <th>App_Date</th>
                                            <th class="text-right">GIS_Est_AMT</th>
                                            <th class="text-right">Reserve_AMT</th>
                                            <th class="text-left">City</th>
                                            <th>Policy Number</th>
                                            <th style="text-align: right!important;">Sum Insured</th>
                                            <th>Issue Date</th>
                                            <th>Expiry Date</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <!-- Data loaded via JavaScript -->
                                    </tbody>
                                </table>
                            </div>

                            <!-- Mobile Card View -->
                            <div class="d-md-none mobile-cards-container">
                                <div class="mobile-export-section mb-3">
                                    <div class="mobile-export-header mb-2">
                                        <h6 class="mb-0 fw-bold text-primary">Export Data</h6>
                                    </div>
                                    <div class="mobile-export-buttons">
                                        <button type="button" class="btn-mobile-export btn-copy" data-export="copy">
                                            <i class="fas fa-copy"></i><span>Copy</span>
                                        </button>
                                        <button type="button" class="btn-mobile-export btn-csv" data-export="csv">
                                            <i class="fas fa-file-csv"></i><span>CSV</span>
                                        </button>
                                        <button type="button" class="btn-mobile-export btn-excel" data-export="excel">
                                            <i class="fas fa-file-excel"></i><span>Excel</span>
                                        </button>
                                        <button type="button" class="btn-mobile-export btn-pdf" data-export="pdf">
                                            <i class="fas fa-file-pdf"></i><span>PDF</span>
                                        </button>
                                        <button type="button" class="btn-mobile-export btn-print" data-export="print">
                                            <i class="fas fa-print"></i><span>Print</span>
                                        </button>
                                    </div>
                                </div>

                                <!-- Mobile Controls -->
                                <div class="mobile-controls mb-3">
                                    <div class="row g-2">
                                        <!-- PLR Filter -->
                                        <div class="col-6">
                                            <label for="mobilePlrFilter" class="form-label fw-bold mb-1">Status:</label>
                                            <select id="mobilePlrFilter" class="form-select form-select-sm">
                                                <option value="all" selected>All Status</option>
                                                <option value="P/R Approved">P/R Approved</option>
                                                <option value="F/R Approved">F/R Approved</option>
                                                <option value="In process">In Process</option>
                                                <option value="Pending">Pending</option>
                                                <option value="Unapproved">Unapproved</option>
                                            </select>
                                        </div>

                                        <!-- Department Filter -->
                                        <div class="col-6">
                                            <label for="mobileDeptFilter" class="form-label fw-bold mb-1">Department:</label>
                                            <select id="mobileDeptFilter" class="form-select form-select-sm">
                                                <option value="" selected>All Departments</option>
                                                <option value="Fire">Fire</option>
                                                <option value="Marine">Marine</option>
                                                <option value="Motor">Motor</option>
                                                <option value="Miscellaneous">Miscellaneous</option>
                                            </select>
                                        </div>

                                        <!-- Surveyor Filter -->
                                        <div class="col-12">
                                            <label for="mobileSurveyorFilter" class="form-label fw-bold mb-1">Surveyor:</label>
                                            <div class="multi-select-wrapper">
                                                <button type="button" class="multi-select-button" id="mobileSurveyorMultiSelectBtn">
                                                    <span class="multi-select-text">Select Surveyors</span>
                                                    <i class="bi bi-chevron-down multi-select-arrow"></i>
                                                </button>
                                                <div class="multi-select-dropdown" id="mobileSurveyorMultiSelectDropdown">
                                                    <div class="multi-select-search">
                                                        <input type="text" id="mobileSurveyorSearchInput" placeholder="Search surveyors...">
                                                    </div>
                                                    <div class="multi-select-options" id="mobileSurveyorOptionsContainer">
                                                        <!-- Options loaded via JavaScript -->
                                                    </div>
                                                    <div class="multi-select-actions">
                                                        <button type="button" class="multi-select-filter-btn" id="mobileSurveyorFilterBtn">Filter</button>
                                                        <button type="button" class="multi-select-clear" id="mobileSurveyorClearAll">Clear All</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Search Bar -->
                                        <div class="col-12">
                                            <label for="mobileSearch" class="form-label fw-bold mb-1">Search:</label>
                                            <input type="text" id="mobileSearch" class="form-control form-control-sm" placeholder="Search claims...">
                                        </div>
                                    </div>
                                </div>

                                <div id="mobileCardsWrapper">
                                    <!-- Mobile cards loaded via JavaScript -->
                                </div>

                                <div id="mobileLoadMore" class="text-center mt-3" style="display: none;">
                                    <button class="btn btn-primary" id="loadMoreBtn">
                                        <i class="bi bi-arrow-down-circle me-2"></i>Load More
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Action Modal (View Modal) -->
    <div class="modal fade" id="actionModal" tabindex="-1" aria-labelledby="actionModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0" style="border-radius: 15px;">
                <div class="modal-header text-white border-0"
                    style="background: linear-gradient(135deg, #0066cc 0%, #0052a3 100%); border-radius: 15px 15px 0 0;">
                    <h5 class="modal-title fw-bold" id="actionModalLabel">
                        <i class="bi bi-gear-fill me-2"></i>Actions
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body p-4">
                    <div class="action-info-card mb-3 p-3"
                        style="background: #f8f9fa; border-radius: 10px; border-left: 4px solid #0066cc;">
                        <div class="row g-2">
                            <div class="col-12">
                                <strong class="text-primary">Claim No:</strong>
                                <span id="actionClaimNo">-</span>
                            </div>
                            <div class="col-12">
                                <strong class="text-primary">Insured:</strong>
                                <span id="actionInsuredName">-</span>
                            </div>
                            <div class="col-12">
                                <strong class="text-primary">Surveyor:</strong>
                                <span id="actionSurveyorName">-</span>
                            </div>
                        </div>
                    </div>

                    <div class="d-grid gap-2">
                        <button type="button" class="btn btn-info btn-lg text-white action-modal-btn" id="viewDocBtn">
                            <i class="bi bi-eye-fill me-2"></i>View Documents
                        </button>
                        <button type="button" class="btn btn-warning btn-lg text-white action-modal-btn" id="revisionPlrBtn">
                            <i class="bi bi-arrow-repeat me-2"></i>PLR Revision
                        </button>
                        <button type="button" class="btn btn-reminder btn-lg action-modal-btn" id="reminderBtn" style="display: none;">
                            <i class="bi bi-bell-fill me-2"></i>Send Reminder
                        </button>
                    </div>
                </div>

                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-outline-secondary rounded-pill px-4" data-bs-dismiss="modal">
                        <i class="bi bi-x-circle me-1"></i> Close
                    </button>
                </div>
            </div>
        </div>
    </div>

    
<!-- PLR Revision Modal -->
<div class="modal fade" id="revisionModal" tabindex="-1" aria-labelledby="revisionModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0" style="border-radius: 15px;">
            <div class="modal-header text-white border-0"
                style="background: linear-gradient(135deg, #007bff 0%, #0056b3 100%); border-radius: 15px 15px 0 0;">
                <h5 class="modal-title" id="revisionModalLabel">
                    <i class="bi bi-envelope-paper me-2"></i>Send PLR Revision Email
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body p-4">
                <form id="revisionEmailForm">
                    @csrf
                    <input type="hidden" name="uw_doc" id="uw_doc">

                    <div class="mb-3">
                        <label for="sender" class="form-label fw-semibold">
                            <i class="bi bi-person-circle me-1"></i>From:
                        </label>
                        <input type="email" name="sender" id="sender" class="form-control rounded-pill" 
                               value="{{ $userEmail }}" readonly required>
                        <small class="text-muted">Current logged-in user</small>
                    </div>

                    <div class="mb-3">
                        <label for="receiver" class="form-label fw-semibold">
                            <i class="bi bi-envelope me-1"></i>To:
                        </label>
                        <input type="email" name="receiver" id="receiver" class="form-control rounded-pill" required>
                        <small class="text-muted">Surveyor email (auto-populated)</small>
                    </div>
<div class="mb-3">
    <label class="form-label fw-semibold">
        <i class="bi bi-envelope-plus me-1"></i>CC:
    </label>
    
    <!-- Additional CCs -->
    <div class="input-group">
        <input type="text" name="cc" id="revision_cc" 
       class="form-control rounded-pill" 
       placeholder="Add additional CC email addresses">
    </div>
    <small class="text-muted">You will be CC'd Kindly! Add Additional Emails Separated with Commas</small>
</div>

                    <div class="mb-3">
                        <label for="sub" class="form-label fw-semibold">
                            <i class="bi bi-chat-text me-1"></i>Subject:
                        </label>
                        <input type="text" name="sub" id="sub" class="form-control rounded-pill" required>
                    </div>

                    <div class="mb-3">
                        <label for="body" class="form-label fw-semibold">
                            <i class="bi bi-card-text me-1"></i>Body:
                        </label>
                        <textarea name="body" id="body" rows="6" class="form-control" style="border-radius: 15px;" required></textarea>
                    </div>
                </form>
            </div>

            <div class="modal-footer border-0 p-4">
                <button type="button" id="sendRevisionBtn" class="btn btn-primary rounded-pill px-4">
                    <i class="bi bi-envelope-paper me-1"></i> Send Email
                </button>
                <button type="button" class="btn btn-outline-secondary rounded-pill px-4" data-bs-dismiss="modal">
                    <i class="bi bi-x-circle me-1"></i> Cancel
                </button>
            </div>
        </div>
    </div>
</div>

    <!-- Reminder Modal -->
    <div class="modal fade" id="reminderModal" tabindex="-1" aria-labelledby="reminderModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content border-0" style="border-radius: 15px;">
                <div class="modal-header text-white border-0"
                    style="background: linear-gradient(135deg, #007bff 0%, #0056b3 100%); border-radius: 15px 15px 0 0;">
                    <h5 class="modal-title" id="reminderModalLabel">
                        <i class="bi bi-bell me-2"></i>Send Reminder
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body p-4">
                    <div id="reminderMessage" style="display: none;"></div>

                    <form id="reminderEmailForm" method="POST" action="{{ route('reminder.send') }}">
                        @csrf
                        <input type="hidden" name="uw_doc" id="reminder_uw_doc">
                        <input type="hidden" name="receiver_role" value="surveyor">

                        <div class="mb-3">
                            <label for="reminder_sender" class="form-label fw-semibold">
                                <i class="bi bi-person-circle me-1"></i>From:
                            </label>
                            <input type="email" name="sender" id="reminder_sender" class="form-control rounded-pill"
                                value="{{ Auth::user()->email ?? '' }}" required>
                        </div>

                        <div class="mb-3">
                            <label for="receiver_email" class="form-label fw-semibold">
                                <i class="bi bi-envelope me-1"></i>To:
                            </label>
                            <input type="email" name="receiver_email" id="receiver_email" class="form-control rounded-pill" required>
                            <small class="text-muted">Surveyor: <span id="clientName"></span></small>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">
                                <i class="bi bi-envelope-plus me-1"></i>CC:
                            </label>
                            
                            <!-- Additional CCs -->
                            <div class="input-group">
                               <input type="text" name="cc" id="reminder_cc" 
                               class="form-control rounded-pill" 
                               placeholder="Add additional CC email addresses">
                            </div>
                            <small class="text-muted">You will be CC'd Kindly! Add Additional Emails Separated with Commas</small>
                        </div>

                        <div class="mb-3">
                            <label for="reminder_sub" class="form-label fw-semibold">
                                <i class="bi bi-chat-text me-1"></i>Subject:
                            </label>
                            <input type="text" name="sub" id="reminder_sub" class="form-control rounded-pill"
                                required>
                        </div>

                        <div class="mb-3">
                            <label for="reminder_body" class="form-label fw-semibold">
                                <i class="bi bi-card-text me-1"></i>Message:
                            </label>
                            <textarea name="body" id="reminder_body" rows="6" class="form-control" style="border-radius: 15px;"
                                required>Dear Surveyor,

This is a friendly reminder regarding your pending report submission for [CLIENT_NAME] (Document No: [DOCUMENT_NO]).

We kindly request you to submit the required documents at your earliest convenience to avoid any delays in processing.

If you have already submitted the documents, please disregard this message. For any queries, please feel free to contact us.

Thank you for your cooperation.</textarea>
                        </div>
                    </form>
                </div>

                <div class="modal-footer border-0 p-4">
                    <button type="button" id="sendReminderBtn" class="btn rounded-pill px-4">
                        <i class="bi bi-bell me-1"></i> <span>Send Reminder</span>
                    </button>
                    <button type="button" class="btn btn-outline-secondary rounded-pill px-4" data-bs-dismiss="modal">
                        <i class="bi bi-x-circle me-1"></i> Cancel
                    </button>
                </div>
            </div>
        </div>
    </div>

    
    {{-- View Report Modal (Updated with Approval Buttons and Remarks) --}}
<div class="modal fade" id="viewReportModal" tabindex="-1" aria-labelledby="viewReportModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-fullscreen-sm-down">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 18px;">
            <div class="modal-header text-white border-0"
                style="background: linear-gradient(135deg, #0062cc 0%, #004085 100%); border-radius: 18px 18px 0 0;">
                <div>
                    <h5 class="modal-title fw-semibold fs-5 mb-1" id="viewReportModalLabel">
                        <i class="bi bi-file-earmark-text me-2"></i> Uploaded Documents
                    </h5>
                    <div class="text-white-50 small" id="modalClaimInfo">
                        <span id="modalClaimNo"></span> | <span id="modalInsuredName"></span> | <span id="modalSurveyorName"></span>
                    </div>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body p-3">
                <div class="table-responsive mb-4">
                    <table class="table table-hover table-striped align-middle text-center rounded-3 overflow-hidden mb-0">
                        <thead class="bg-light text-secondary">
                            <tr>
                                <th class="py-3">📅 Date</th>
                                <th class="py-3">📝 Surveyor Remarks</th>
                               <th class="py-3">✔️ Approval Remarks</th>
                                <th class="py-3">⚡ Actions</th>
                            </tr>
                        </thead>
                        <tbody id="documentTableBody" class="small"></tbody>
                    </table>
                </div>
                <div id="docError" class="alert alert-danger mt-3 py-2 px-3 small d-none">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i> Error loading documents.
                </div>

                <!-- Approval Section -->
                <div class="approval-section mt-4 p-3 border rounded" style="background: #f8f9fa;">
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <div class="d-flex gap-2">
                                <button type="button" class="btn btn-success" id="modalApproveBtn">
                                    <i class="bi bi-check-circle-fill me-1"></i> Approve 
                                </button>
                                <button type="button" class="btn btn-danger" id="modalUnapproveBtn">
                                    <i class="bi bi-x-circle-fill me-1"></i> Unapprove 
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Add New Remarks Section -->
                    <div class="mb-3">
                        <label for="approvalRemarks" class="form-label fw-semibold">
                            <i class="bi bi-chat-text me-1"></i>Add New Approval Remarks:
                        </label>
                        <textarea id="approvalRemarks" rows="3" class="form-control" placeholder="Enter new approval remarks..."></textarea>
                    </div>

                    <div class="d-flex justify-content-between align-items-center">
                        <button type="button" id="saveRemarksBtn" class="btn btn-primary">
                            <i class="bi bi-save me-1"></i> Save Remarks
                        </button>
                        <input type="hidden" id="currentDocNo">
                    </div>
                </div>
            </div>

            <div class="modal-footer border-0 p-4">
                <button type="button" id="downloadAllZip" data-doc="" class="btn rounded-pill px-4"
                    style="background: linear-gradient(135deg, #0066cc 0%, #0052a3 100%); color: white; border: none;">
                    <i class="bi bi-file-zip me-1"></i> Download All as ZIP
                </button>
                <button type="button" class="btn btn-outline-secondary rounded-pill px-4" data-bs-dismiss="modal">
                    <i class="bi bi-x-circle me-1"></i> Cancel
                </button>
            </div>
        </div>
    </div>
</div>

    <!-- WhatsApp Floating Button -->
    <a href="https://wa.me/9242111245000?text=Hello!%20%0AWelcome%20to%20Atlas%20Insurance%20Claim%20Management.%0A%0AKindly%20provide%20your%20claim%20reference%20or%20policy%20number."
        class="whatsapp-float" target="_blank" rel="noopener noreferrer" aria-label="Chat on WhatsApp">
        <i class="bi bi-whatsapp"></i>
    </a>

    {{-- <!-- Floating Email Log Button -->
    <div id="emailLogBtn" class="floating-email-btn">
        <i class="bi bi-envelope-open"></i>
        <span class="btn-text">Document Logs</span>
    </div> --}}

  <!-- NEW: Slide-out Panel for Document-Specific Email Logs -->
<div id="emailLogPanel" class="email-log-panel">
    <div class="panel-header sticky-header">
        <h5 class="panel-title">
            <i class="bi bi-envelope-open me-2"></i>
            <span id="panelDocumentTitle">Document Logs</span>
        </h5>
        <button id="closeEmailPanel" class="btn-close-panel" aria-label="Close panel">
            <i class="bi bi-x-lg"></i>
        </button>
    </div>
    <div class="panel-body">
        <!-- Document Info Card -->
        <div class="document-info-card mb-3 p-3" style="background: #f8f9fa; border-radius: 8px; border-left: 4px solid #0062cc;">
            <div class="row">
                <div class="col-12">
                    <strong class="text-primary">Document:</strong>
                    <span id="panelDocNo">-</span>
                </div>
                <div class="col-12">
                    <strong class="text-primary">Insured:</strong>
                    <span id="panelClientName">-</span>
                </div>
                <div class="col-12">
                    <strong class="text-primary">Total Logs:</strong>
                    <span id="panelLogCount" class="badge bg-primary">0</span>
                </div>
            </div>
        </div>
        
        <div id="loadingSpinner" class="loading-spinner">
            <div class="spinner"></div>
            <p>Loading email logs...</p>
        </div>
        
        <div class="table-container">
            <table class="email-logs-table" id="emailLogsTable">
                <thead class="sticky-thead">
                    <tr>
                        <th>SR#</th>
                        <th>Date & Time</th>
                        <th>Route/Type</th>
                        <th>Sender</th>
                        <th>Receiver</th>
                        <th>CC</th>
                        <th>Subject</th>
                    </tr>
                </thead>
                <tbody id="emailLogsTableBody">
                    <!-- Populated by JavaScript -->
                </tbody>
            </table>
        </div>
        
        <div id="emptyState" class="empty-state" style="display: none;">
            <i class="bi bi-envelope-slash"></i>
            <p>No email logs found for this document</p>
        </div>
    </div>
</div>

    {{-- <!-- EXISTING: Slide-out Panel for All Email Logs (Keep this for the floating button) -->
    <div id="allEmailLogPanel" class="email-log-panel" style="z-index: 1061;">
        <div class="panel-header">
            <h5 class="panel-title">
                <i class="bi bi-envelope-open me-2"></i>All Email Logs
            </h5>
            <button id="closeAllEmailPanel" class="btn-close-panel" aria-label="Close panel">
                <i class="bi bi-x-lg"></i>
            </button>
        </div>
        <div class="panel-body">
            <div id="allLoadingSpinner" class="loading-spinner">
                <div class="spinner"></div>
                <p>Loading all email logs...</p>
            </div>
            <table class="email-logs-table" id="allEmailLogsTable">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Document</th>
                        <th>Sender</th>
                        <th>Receiver</th>
                        <th>Subject</th>
                        <th>Route</th>
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody id="allEmailLogsTableBody">
                    <!-- Populated by JavaScript -->
                </tbody>
            </table>
            <div id="allEmptyState" class="empty-state" style="display: none;">
                <i class="bi bi-inbox"></i>
                <p>No email logs found</p>
            </div>
        </div>
    </div> --}}

<style>
    /* Action Column Button Styling - UPDATED */
    .action-buttons-container {
        display: flex;
        gap: 4px;
        flex-wrap: nowrap;
        min-width: 140px;
    }
    

    .action-buttons-container .btn {
        padding: 4px 8px;
        font-size: 12px;
        min-width: 32px;
        height: 32px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 4px;
        transition: all 0.2s ease;
    }

    .action-buttons-container .btn:hover {
        transform: translateY(-1px);
        box-shadow: 0 2px 5px rgba(0,0,0,0.2);
    }

    .view-btn {
        background: linear-gradient(135deg, #17a2b8 0%, #138496 100%) !important;
        border: none !important;
        color: white !important;
    }

    .revision-btn {
        background: linear-gradient(135deg, #f0ad4e 0%, #ec971f 100%) !important;
        border: none !important;
        color: white !important;
    }

    .reminder-btn {
        background: linear-gradient(135deg, #FFBF00 0%, #e0a800 100%) !important;
        border: none !important;
        color: white !important;
    }

    .email-log-btn {
        background: linear-gradient(135deg, #6c757d 0%, #495057 100%) !important;
        border: none !important;
        color: white !important;
    }

    /* Document Info Card in Panel */
    .document-info-card {
        background: #f8f9fa !important;
        border-radius: 8px !important;
        border-left: 4px solid #0062cc !important;
        padding: 12px !important;
        margin-bottom: 15px !important;
        box-shadow: 0 1px 3px rgba(0,0,0,0.1);
    }

    .document-info-card .row {
        margin: 0;
    }

    .document-info-card .col-12 {
        padding: 4px 0;
        font-size: 13px;
    }

    .document-info-card strong {
        font-size: 12px;
        min-width: 60px;
        display: inline-block;
    }

    /* Route Badge Styles */
    .route-badge {
        display: inline-block;
        padding: 4px 8px;
        border-radius: 4px;
        font-size: 11px;
        font-weight: 600;
        color: white !important;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .route-reminder {
        background: linear-gradient(135deg, #007bff 0%, #0056b3 100%) !important;
    }

    .route-plrrevision {
        background: linear-gradient(135deg, #ffc107 0%, #e0a800 100%) !important;
        color: #212529 !important;
    }

    .route-other {
        background: linear-gradient(135deg, #6c757d 0%, #495057 100%) !important;
    }

    /* Action Column Button Styling */
    .action-buttons-container {
        display: flex;
        gap: 4px;
        flex-wrap: nowrap;
        min-width: 120px;
    }

    .action-buttons-container .btn {
        padding: 4px 8px;
        font-size: 12px;
        min-width: 32px;
        height: 32px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 4px;
        transition: all 0.2s ease;
    }

    .action-buttons-container .btn:hover {
        transform: translateY(-1px);
        box-shadow: 0 2px 5px rgba(0,0,0,0.2);
    }

    .view-btn {
        background: linear-gradient(135deg, #17a2b8 0%, #138496 100%) !important;
        border: none !important;
        color: white !important;
    }

    .revision-btn {
        background: linear-gradient(135deg, #f0ad4e 0%, #ec971f 100%) !important;
        border: none !important;
        color: white !important;
    }

    .reminder-btn {
        background: linear-gradient(135deg, #FFBF00 0%, #e0a800 100%) !important;
        border: none !important;
        color: white !important;
    }

    /* Mobile Action Buttons */
    .mobile-card .btn {
        padding: 6px 12px;
        font-size: 13px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .mobile-card .action-buttons-container {
        display: flex;
        gap: 8px;
        justify-content: flex-end;
        margin-top: 10px;
    }

    /* Approval Section in View Modal */
    .approval-section {
        border: 1px solid #dee2e6;
        border-radius: 8px;
        background: #f8f9fa;
    }

    .approval-section h6 {
        color: #0062cc;
        border-bottom: 2px solid #0062cc;
        padding-bottom: 8px;
    }

    #modalApproveBtn, #modalUnapproveBtn {
        min-width: 150px;
    }

    #saveRemarksBtn {
        min-width: 150px;
    }

    /* Desktop Filter Styling */
    .dt-surveyor-filter {
        position: relative;
        z-index: 2000 !important;
    }

    .filters-wrapper {
        position: relative;
        z-index: 1500 !important;
    }

    .desktop-controls-wrapper {
        position: relative;
        z-index: 1000 !important;
    }

    /* Multi-select styling */
    .multi-select-wrapper {
        position: relative !important;
        display: inline-block;
        width: 100%;
        z-index: 2100 !important;
    }

    .multi-select-button {
        width: 100%;
        padding: 6px 30px 6px 10px;
        border: 1px solid #ced4da;
        border-radius: 4px;
        background: white;
        cursor: pointer;
        font-size: 13px;
        text-align: left;
        display: flex;
        align-items: center;
        justify-content: space-between;
        transition: all 0.2s ease;
        position: relative;
        z-index: 2;
    }

    .multi-select-button:hover {
        border-color: #0066cc;
        box-shadow: 0 0 0 0.2rem rgba(0, 102, 204, 0.15);
    }

    .multi-select-button.active {
        border-color: #0066cc;
        box-shadow: 0 0 0 0.2rem rgba(0, 102, 204, 0.25);
    }

    .multi-select-text {
        flex: 1;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .multi-select-arrow {
        margin-left: 8px;
        transition: transform 0.2s ease;
        flex-shrink: 0;
    }

    .multi-select-button.active .multi-select-arrow {
        transform: rotate(180deg);
    }

    .multi-select-dropdown {
        position: absolute !important;
        top: 100% !important;
        left: 0 !important;
        right: 0 !important;
        width: 100% !important;
        max-height: 300px;
        background: white !important;
        border: 1px solid #ced4da !important;
        border-radius: 6px !important;
        box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2) !important;
        z-index: 999999 !important;
        display: none !important;
        overflow: visible !important;
        margin-top: 4px !important;
    }

    .multi-select-dropdown.show {
        display: block !important;
        visibility: visible !important;
        opacity: 1 !important;
    }

    .multi-select-search {
        padding: 8px 10px;
        border-bottom: 1px solid #e9ecef;
        position: sticky;
        top: 0;
        background: white;
        z-index: 1;
    }

    .multi-select-search input {
        width: 100%;
        padding: 6px 10px;
        border: 1px solid #ced4da;
        border-radius: 4px;
        font-size: 12px;
    }

    .multi-select-search input:focus {
        outline: none;
        border-color: #0066cc;
        box-shadow: 0 0 0 0.2rem rgba(0, 102, 204, 0.15);
    }

    .multi-select-options {
        max-height: 200px;
        overflow-y: auto;
    }

    .multi-select-option {
        padding: 8px 12px;
        cursor: pointer;
        display: flex;
        align-items: center;
        font-size: 13px;
        transition: background 0.15s ease;
    }

    .multi-select-option:hover {
        background: #f8f9fa;
    }

    .multi-select-option input[type="checkbox"] {
        margin-right: 10px;
        width: 16px;
        height: 16px;
        cursor: pointer;
    }

    .multi-select-option label {
        flex: 1;
        cursor: pointer;
        margin: 0;
    }

    .multi-select-actions {
        padding: 8px 10px;
        border-top: 1px solid #e9ecef;
        display: flex;
        gap: 8px;
        background: #f8f9fa;
    }

    .multi-select-actions button {
        flex: 1;
        padding: 6px 12px;
        border: none;
        border-radius: 4px;
        font-size: 12px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.2s ease;
    }

    .multi-select-filter-btn {
        background: #0066cc !important;
        color: white !important;
    }

    .multi-select-filter-btn:hover {
        background: #0052a3 !important;
    }

    .multi-select-clear {
        background: #dc3545 !important;
        color: white !important;
    }

    .multi-select-clear:hover {
        background: #c82333 !important;
    }

    .multi-select-badge {
        display: inline-block;
        background: #0066cc;
        color: white;
        padding: 2px 8px;
        border-radius: 12px;
        font-size: 11px;
        font-weight: 600;
        margin-left: 5px;
    }

    /* DataTable Styling */
    #filesTable_wrapper {
        overflow: visible !important;
    }

    #filesTable thead tr th,
    table.dataTable thead th {
        background-color: #f8f9fa !important;
        color: #212529 !important;
        font-family: 'Calibri';
        text-align: center;
    }

    #filesTable tbody tr td {
        vertical-align: middle;
        padding: 8px 4px;
        font-size: 12px;
    }

    #filesTable tbody tr:nth-child(even) {
        background-color: #f8f9fa;
    }

    #filesTable tbody tr:hover {
        background-color: #d1e7fd !important;
    }

    div.dataTables_scrollBody {
        max-height: 400px !important;
        overflow-y: scroll !important;
        border: 1px solid #ddd;
    }

    .dt-buttons {
        margin-left: auto;
        display: flex;
        gap: 4px;
        flex-wrap: wrap;
    }

    .dt-buttons .btn {
        padding: 4px 8px;
        font-size: 12px;
        height: 32px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 4px;
        transition: all 0.2s ease;
        text-decoration: none;
    }

    .desktop-controls-wrapper {
        display: flex;
        align-items: center;
        gap: 10px;
        margin-bottom: 15px;
        flex-wrap: wrap;
    }

    .dt-search-wrapper {
        flex: 0 0 auto;
        min-width: 200px;
        margin-right: auto;
    }

    .dt-plr-filter,
    .dt-dept-filter,
    .dt-surveyor-filter {
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .dt-plr-filter select,
    .dt-dept-filter select {
        padding: 4px 8px;
        border-radius: 4px;
        border: 1px solid #ced4da;
        font-size: 13px;
    }

    #filesTable_filter label {
        font-size: 15px;
        font-weight: 700;
        color: #000000 !important;
        font-family: Calibri, sans-serif !important;
    }

    /* Stats Container */
    .content-body {
        margin-top: -40px;
    }

    .stats-container {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 20px;
        margin-top: 80px;
        margin-bottom: 20px;
        padding: 0 20px;
        flex-wrap: wrap;
        margin-left: 70px;
        width: auto;
        box-sizing: border-box;
    }

    .stats-main-card {
        flex: 1;
        min-width: 0;
        background: #ffffff;
        border-radius: 12px;
        padding: 20px;
        box-shadow: 0 3px 10px rgba(0, 0, 0, 0.08);
        border: 1px solid #e9ecef;
        box-sizing: border-box;
        width: 100%;
        margin: 0 auto;
    }

    .stats-grid {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 20px;
        flex-wrap: nowrap;
    }

    .stat-item {
        display: flex;
        align-items: center;
        gap: 12px;
        flex: 1;
        min-width: 0;
    }

    .stat-icon {
        width: 48px;
        height: 48px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 22px;
        flex-shrink: 0;
        transition: transform 0.2s ease;
    }

    .stat-icon-primary {
        background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);
        box-shadow: 0 4px 10px rgba(0, 123, 255, 0.3);
    }

    .stat-icon-success {
        background: linear-gradient(135deg, #28a745 0%, #218838 100%);
        box-shadow: 0 4px 10px rgba(40, 167, 69, 0.3);
    }

    .stat-icon-warning {
        background: linear-gradient(135deg, #ffc107 0%, #e0a800 100%);
        box-shadow: 0 4px 10px rgba(255, 193, 7, 0.3);
    }

    .stat-icon-danger {
        background: linear-gradient(135deg, #dc3545 0%, #c82333 100%);
        box-shadow: 0 4px 10px rgba(220, 53, 69, 0.3);
    }

    .stat-item:hover .stat-icon {
        transform: scale(1.05);
    }

    .stat-content {
        flex: 1;
        min-width: 0;
    }

    .stat-label {
        font-size: 11px;
        color: #6c757d;
        font-weight: 600;
        margin-bottom: 4px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        line-height: 1.2;
    }

    .stat-value {
        font-size: 20px;
        font-weight: 700;
        color: #007bff;
        line-height: 1.2;
        word-break: break-word;
    }

    .stat-value-success {
        color: #28a745;
    }

    .stat-value-warning {
        color: #ffc107;
    }

    .stat-value-danger {
        color: #dc3545;
    }

    .stat-divider {
        width: 1px;
        height: 48px;
        background: linear-gradient(to bottom, transparent, #dee2e6, transparent);
        flex-shrink: 0;
    }

    #leftPanel {
        width: auto;
        max-width: 550px;
        padding: 8px;
        display: flex !important;
        justify-content: center;
        background: #f5f7fa;
        border-radius: 12px;
        box-shadow: 0 3px 10px rgba(172, 168, 168, 0.08);
        position: relative;
        z-index: 10;
        visibility: visible !important;
        opacity: 1 !important;
        flex-shrink: 0;
        box-sizing: border-box;
        margin: 0 auto;
    }

    #appointmentStats {
        display: flex !important;
        flex-direction: row;
        gap: 6px;
        flex-wrap: nowrap;
        align-items: center;
        justify-content: space-between;
        width: 100%;
        visibility: visible !important;
        opacity: 1 !important;
    }

    .stat-card {
        width: 65px;
        height: 65px;
        background: #ffffff;
        border-radius: 8px;
        border-left: 3px solid #007bff;
        display: flex !important;
        flex-direction: column;
        justify-content: center;
        align-items: center;
        box-shadow: 0 2px 6px rgba(0, 0, 0, 0.08);
        cursor: pointer;
        transition: transform 0.2s ease, box-shadow 0.2s ease;
        font-family: 'Segoe UI', 'Roboto', sans-serif;
        text-align: center;
        padding: 4px;
        flex: 1;
        max-width: 75px;
        min-width: 50px;
        position: relative;
        visibility: visible !important;
        opacity: 1 !important;
    }

    .stat-card-reset {
        border-left-color: #28a745;
    }

    .stat-card-reset .count {
        font-size: 1.4em;
    }

    .stat-card:hover {
        background: #e9f2ff;
        transform: translateY(-1px);
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.12);
    }

    .stat-card-reset:hover {
        background: #d4edda;
    }

    .stat-card.active {
        transform: translateY(-2px);
        box-shadow: 0 6px 12px rgba(0, 0, 0, 0.15);
        border-left-width: 4px;
    }

    .stat-card .count {
        font-size: 1.2em;
        font-weight: 700;
        color: #333333;
        line-height: 1.1;
        margin-bottom: 3px;
        font-family: 'calibri';
    }

    .stat-card .label {
        font-size: 0.7em;
        color: #555555;
        text-align: center;
        line-height: 1.1;
        font-weight: 500;
        font-family: 'calibri';
    }

    .mobile-export-section {
        background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
        border-radius: 12px;
        padding: 15px;
        border: 1px solid #dee2e6;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        margin-bottom: 20px !important;
    }

    .mobile-export-header {
        text-align: center;
        border-bottom: 1px solid #dee2e6;
        padding-bottom: 8px;
        margin-bottom: 12px;
    }

    .mobile-export-buttons {
        display: flex !important;
        flex-wrap: wrap;
        justify-content: center;
        align-items: center;
        gap: 8px;
        width: 100%;
    }

    .btn-mobile-export {
        display: flex !important;
        visibility: visible !important;
        opacity: 1 !important;
        position: relative !important;
        z-index: 1000 !important;
        align-items: center;
        justify-content: center;
        flex-direction: column;
        gap: 4px;
        padding: 12px 8px !important;
        font-size: 11px !important;
        font-weight: 600;
        border-radius: 8px;
        border: 2px solid;
        transition: all 0.3s ease;
        min-width: 65px;
        height: 60px;
        cursor: pointer;
        text-align: center;
        background: #fff;
        box-shadow: 0 2px 6px rgba(0, 0, 0, 0.1);
    }

    .btn-mobile-export i {
        font-size: 16px !important;
        display: block !important;
    }

    .btn-mobile-export span {
        font-size: 11px !important;
        font-weight: 600;
        display: block !important;
    }

    .btn-mobile-export:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
    }

    .btn-mobile-export:active {
        transform: translateY(0);
        box-shadow: 0 2px 6px rgba(0, 0, 0, 0.1);
    }

    .btn-copy {
        border-color: #6c757d !important;
        color: #6c757d !important;
    }

    .btn-copy:hover {
        background-color: #6c757d !important;
        color: white !important;
    }

    .btn-csv {
        border-color: #28a745 !important;
        color: #28a745 !important;
    }

    .btn-csv:hover {
        background-color: #28a745 !important;
        color: white !important;
    }

    .btn-excel {
        border-color: #17a2b8 !important;
        color: #17a2b8 !important;
    }

    .btn-excel:hover {
        background-color: #17a2b8 !important;
        color: white !important;
    }

    .btn-pdf {
        border-color: #dc3545 !important;
        color: #dc3545 !important;
    }

    .btn-pdf:hover {
        background-color: #dc3545 !important;
        color: white !important;
    }

    .btn-print {
        border-color: #007bff !important;
        color: #007bff !important;
    }

    .btn-print:hover {
        background-color: #007bff !important;
        color: white !important;
    }

    .mobile-card.hidden {
        display: none;
    }

    .mobile-controls {
        display: none;
    }

    .dt-buttons .btn.btn-outline-secondary {
        background-color: #6c757d !important;
        border-color: #6c757d !important;
        color: #fff !important;
    }

    .dt-buttons .btn.btn-outline-success {
        background-color: #28a745 !important;
        border-color: #28a745 !important;
        color: #fff !important;
    }

    .dt-buttons .btn.btn-outline-danger {
        background-color: #dc3545 !important;
        border-color: #dc3545 !important;
        color: #fff !important;
    }

    .dt-buttons .btn.btn-outline-primary {
        background-color: #007bff !important;
        border-color: #007bff !important;
        color: #fff !important;
    }

    .btn-info {
        background: linear-gradient(135deg, #17a2b8 0%, #138496 100%) !important;
        border: none !important;
        color: #fff !important;
    }

    .btn-success {
        background: linear-gradient(135deg, #0066cc 0%, #0052a3 100%) !important;
        border: none !important;
        color: #fff !important;
    }

    .btn-warning {
        background: linear-gradient(135deg, #f0ad4e 0%, #ec971f 100%) !important;
        border: none !important;
        color: #fff !important;
    }

    .btn-danger {
        background: linear-gradient(135deg, #dc3545 0%, #c82333 100%) !important;
        border: none !important;
        color: #fff !important;
    }

    .btn-reminder {
        background: linear-gradient(135deg, #FFBF00 0%, #e0a800 100%) !important;
        border: none !important;
        color: #fff !important;
    }

    @media (min-width: 768px) {
        .mobile-controls,
        .mobile-export-section,
        .mobile-cards-container {
            display: none !important;
        }
    }

    @media (max-width: 767.98px) {
        .desktop-controls-wrapper,
        .dt-search-wrapper,
        .dt-plr-filter,
        .dt-dept-filter,
        .dt-buttons,
        .filters-wrapper,
        .filters-row,
        #filesTable_wrapper {
            display: none !important;
        }

        .mobile-controls {
            display: block !important;
            background: #fff;
            border-radius: 10px;
            padding: 12px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            margin-bottom: 15px;
        }

        .mobile-export-section {
            display: block !important;
        }

        .mobile-cards-container {
            display: block !important;
        }

        .mobile-controls .form-label {
            font-size: 13px;
            font-weight: 600;
            color: #333;
            margin-bottom: 4px;
        }

        .mobile-controls .form-select,
        .mobile-controls .form-control {
            font-size: 13px;
            padding: 6px 8px;
            border-radius: 6px;
            border: 1px solid #ccc;
        }

        .mobile-controls .row {
            margin: 0;
        }

        .mobile-controls .col-4 {
            padding: 0 5px;
        }

        .mobile-controls .col-12 {
            margin-top: 10px;
            padding: 0 5px;
        }

        .stats-container {
            flex-direction: column;
            align-items: stretch;
            margin-top: 60px;
            margin-left: 0 !important;
            margin-right: 0 !important;
            padding: 0 10px !important;
            width: 100% !important;
        }

        .stats-main-card {
            width: 100%;
            margin: 0 0 15px 0;
            box-sizing: border-box;
            padding: 15px;
        }

        #leftPanel {
            width: 100% !important;
            max-width: 100% !important;
            margin: 0 !important;
            box-sizing: border-box;
            padding: 10px;
        }

        .stats-grid {
            flex-wrap: wrap;
            gap: 12px;
        }

        .stat-item {
            flex: 1 1 calc(50% - 12px);
            min-width: 200px;
        }

        .stat-divider {
            display: none;
        }
    }

    @media (max-width: 768px) {
        .stats-container {
            padding: 0 10px !important;
            margin-top: 50px;
            margin-left: 0 !important;
            margin-right: 0 !important;
            width: 100% !important;
            box-sizing: border-box !important;
        }

        .stats-main-card {
            padding: 12px;
            width: 100% !important;
            max-width: 100% !important;
            margin: 0 0 10px 0 !important;
            box-sizing: border-box !important;
        }

        .stat-item {
            flex: 1 1 100%;
            padding: 10px;
            background: #f8f9fa;
            border-radius: 8px;
            border-left: 3px solid;
            margin-top: 8px;
        }

        .stat-item:first-child {
            margin-top: 0;
        }

        .stat-item:nth-child(1) {
            border-left-color: #007bff;
        }

        .stat-item:nth-child(3) {
            border-left-color: #28a745;
        }

        .stat-item:nth-child(5) {
            border-left-color: #ffc107;
        }

        .stat-item:nth-child(7) {
            border-left-color: #dc3545;
        }

        .stat-icon {
            width: 40px;
            height: 40px;
            font-size: 18px;
        }

        .stat-value {
            font-size: 16px;
        }

        .stat-label {
            font-size: 10px;
        }

        #leftPanel {
            width: 100% !important;
            max-width: 100% !important;
            margin: 0 !important;
            padding: 8px 5px !important;
            box-sizing: border-box !important;
            overflow-x: auto;
            overflow-y: hidden;
            -webkit-overflow-scrolling: touch;
        }

        #appointmentStats {
            gap: 4px;
            min-width: min-content;
            padding: 2px;
        }

        .stat-card {
            width: 52px !important;
            height: 52px !important;
            max-width: 52px !important;
            min-width: 52px !important;
            flex-shrink: 0;
            padding: 4px 2px !important;
        }

        .stat-card .count {
            font-size: 1em !important;
            margin-bottom: 2px !important;
        }

        .stat-card .label {
            font-size: 0.65em !important;
            line-height: 1 !important;
            white-space: nowrap;
        }
    }

    @media (max-width: 575.98px) {
        .stats-container {
            padding: 0 5px !important;
            margin-top: 40px;
        }

        .stats-main-card {
            padding: 10px;
            margin-bottom: 8px !important;
        }

        .stat-item {
            padding: 8px;
        }

        .stat-label {
            font-size: 9px !important;
        }

        .stat-value {
            font-size: 14px !important;
        }

        #leftPanel {
            padding: 6px 3px !important;
            margin: 0 !important;
        }

        #appointmentStats {
            gap: 3px;
        }

        .stat-card {
            width: 48px !important;
            height: 48px !important;
            max-width: 48px !important;
            min-width: 48px !important;
        }

        .stat-card .count {
            font-size: 0.9em !important;
        }

        .stat-card .label {
            font-size: 0.6em !important;
        }
    }

    @media (max-width: 768px) and (orientation: landscape) {
        .stats-container {
            margin-top: 30px !important;
        }

        .stats-main-card {
            padding: 10px !important;
        }

        .stat-item {
            flex: 1 1 calc(50% - 8px);
        }
    }

    .mobile-controls {
        display: none;
        background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
        border-radius: 12px;
        padding: 15px;
        border: 1px solid #dee2e6;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        margin-bottom: 20px !important;
    }

    .mobile-controls .form-label {
        font-size: 14px !important;
        font-weight: 600 !important;
        color: #000000 !important;
        font-family: Calibri, sans-serif !important;
        margin-bottom: 6px !important;
        white-space: nowrap;
    }

    .mobile-controls .form-select,
    .mobile-controls .form-control {
        font-size: 13px;
        padding: 6px 8px;
        border-radius: 6px;
        border: 1px solid #ccc;
        background-color: #fff;
    }

    .mobile-controls .row {
        margin: 0 !important;
    }

    .mobile-controls .col-4 {
        flex: 0 0 33.3333%;
        max-width: 33.3333%;
        padding: 0 5px;
    }

    .mobile-controls .col-12 {
        margin-top: 10px;
        padding: 0 5px;
    }

    @media (max-width: 767.98px) {
        .mobile-controls {
            display: block !important;
        }

        .desktop-controls-wrapper,
        .dt-buttons {
            display: none !important;
        }
    }

    @media (max-width: 420px) {
        .mobile-controls .col-4 {
            flex: 0 0 100%;
            max-width: 100%;
            margin-bottom: 10px;
        }

        .mobile-controls .col-4:last-of-type {
            margin-bottom: 0;
        }
    }

    #sendReminderBtn {
        background: linear-gradient(135deg, #0066cc 0%, #0052a3 100%);
        border: none;
        color: #fff;
        transition: all 0.2s ease-in-out;
    }

    #sendReminderBtn:hover {
        background: linear-gradient(135deg, #0052a3 0%, #003d7a 100%);
    }

    #sendReminderBtn:active {
        background-color: #000066;
        border-color: #000066;
        color: #fff;
    }

    #sendReminderBtn:focus {
        outline: none;
        box-shadow: 0 0 0 0.25rem rgba(0, 0, 136, 0.5);
    }

    #sendRevisionBtn {
        background: linear-gradient(135deg, #0066cc 0%, #0052a3 100%);
        border: none;
        color: #fff;
        transition: all 0.2s ease-in-out;
    }

    #sendRevisionBtn:hover {
        background: linear-gradient(135deg, #0052a3 0%, #003d7a 100%);
    }

    #sendRevisionBtn:active {
        background-color: #000066;
        border-color: #000066;
        color: #fff;
    }

    #sendRevisionBtn:focus {
        outline: none;
        box-shadow: 0 0 0 0.25rem rgba(0, 0, 136, 0.5);
    }

    /* WHATSAPP FLOATING BUTTON STYLES */
    .whatsapp-float {
        position: fixed;
        width: 60px;
        height: 60px;
        bottom: 30px;
        right: 30px;
        background-color: #25D366;
        color: #ffffff;
        border-radius: 50%;
        text-align: center;
        box-shadow: 0 4px 12px rgba(37, 211, 102, 0.4);
        z-index: 10000;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.3s ease-in-out;
        cursor: pointer;
        text-decoration: none;
    }

    .whatsapp-float i {
        font-size: 32px;
        color: #ffffff;
        transition: transform 0.3s ease-in-out;
    }

    .whatsapp-float:hover {
        transform: scale(1.1);
        box-shadow: 0 6px 20px rgba(37, 211, 102, 0.6);
        background-color: #128C7E;
    }

    .whatsapp-float:hover i {
        transform: rotate(10deg) scale(1.1);
    }

    .whatsapp-float:active {
        transform: scale(0.95);
        box-shadow: 0 2px 8px rgba(37, 211, 102, 0.3);
    }

    @keyframes pulse {
        0% {
            box-shadow: 0 0 0 0 rgba(37, 211, 102, 0.7);
        }
        70% {
            box-shadow: 0 0 0 15px rgba(37, 211, 102, 0);
        }
        100% {
            box-shadow: 0 0 0 0 rgba(37, 211, 102, 0);
        }
    }

    .whatsapp-float::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        border-radius: 50%;
        animation: pulse 2s infinite;
    }

    @keyframes slideInFromRight {
        0% {
            transform: translateX(100px);
            opacity: 0;
        }
        100% {
            transform: translateX(0);
            opacity: 1;
        }
    }

    .whatsapp-float {
        animation: slideInFromRight 0.5s ease-out;
    }

    .whatsapp-float:focus {
        outline: 3px solid #FFD700;
        outline-offset: 3px;
    }

    @media print {
        .whatsapp-float {
            display: none !important;
        }
    }

    @media (prefers-reduced-motion: reduce) {
        .whatsapp-float,
        .whatsapp-float i {
            transition: none;
        }
        
        .whatsapp-float::before {
            animation: none;
        }
    }

    /* Email Log Button */
  
    .floating-email-btn {
        position: fixed;
        right: 0;
        top: 50%;
        transform: translateY(-50%);
        background: linear-gradient(135deg, #0062cc 0%, #004085 100%);
        color: white;
        padding: 14px 8px;
        font-weight: 700;
        border-radius: 8px 0 0 8px;
        cursor: pointer;
        z-index: 1050;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        gap: 0;
        box-shadow: -2px 4px 12px rgba(0, 98, 204, 0.3);
        transition: all 0.3s ease;
        font-size: 11px;
        user-select: none;
        writing-mode: vertical-rl;
        text-orientation: mixed;
        width: 38px;
        min-height: 110px;
        letter-spacing: 1px;
    }

    .floating-email-btn:hover {
        background: linear-gradient(135deg, #004085 0%, #002d5c 100%);
        padding-right: 10px;
        padding-left: 6px;
        box-shadow: -4px 6px 16px rgba(0, 98, 204, 0.4);
    }

    .floating-email-btn i {
        display: none !important;
    }

    .floating-email-btn .btn-text {
        writing-mode: vertical-rl;
        text-orientation: mixed;
        font-size: 10px;
        font-weight: 700;
        letter-spacing: 1px;
    }

    /* SLIDE-OUT PANEL */
.email-log-panel {
    position: fixed;
    top: 0;
    right: -1200px; /* Very wide to avoid horizontal scroll */
    width: 1200px; /* Extra wide width */
    height: 100vh;
    background: white;
    box-shadow: -4px 0 16px rgba(0, 0, 0, 0.15);
    z-index: 1060;
    display: flex;
    flex-direction: column;
    transition: right 0.4s cubic-bezier(0.4, 0, 0.2, 1);
    border-left: 3px solid #0062cc;
}

    .email-log-panel.open {
        right: 0;
    }

    .panel-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        background: linear-gradient(135deg, #0062cc 0%, #004085 100%);
        color: white;
        padding: 18px 20px;
        border-bottom: 2px solid #004085;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    }

    .panel-title {
        margin: 0;
        font-size: 18px;
        font-weight: 700;
        display: flex;
        align-items: center;
        gap: 10px;
        letter-spacing: 0.5px;
    }

    .panel-title i {
        font-size: 22px;
    }

    .btn-close-panel {
        background: rgba(255, 255, 255, 0.2);
        border: none;
        color: white;
        width: 36px;
        height: 36px;
        border-radius: 6px;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.2s ease;
        font-size: 20px;
    }

    .btn-close-panel:hover {
        background: rgba(255, 255, 255, 0.3);
        transform: rotate(90deg);
    }

    /* PANEL BODY */
    .panel-body {
        flex: 1;
        overflow-y: auto;
        padding: 20px;
        background: #f8f9fa;
    }

    .loading-spinner {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        height: 300px;
        gap: 16px;
    }

    .spinner {
        width: 50px;
        height: 50px;
        border: 4px solid #e9ecef;
        border-top: 4px solid #0062cc;
        border-radius: 50%;
        animation: spin 1s linear infinite;
    }

    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }

    .loading-spinner p {
        color: #6c757d;
        font-size: 14px;
        font-weight: 500;
    }

    /* EMAIL LOGS TABLE */
    .email-logs-table {
        width: 100%;
        border-collapse: collapse;
        background: white;
        border-radius: 8px;
        overflow: hidden;
        box-shadow: 0 2px 6px rgba(0, 0, 0, 0.08);
        table-layout: auto;
    }

    .email-logs-table thead {
        background: linear-gradient(135deg, #f1f3f5 0%, #e9ecef 100%);
        border-bottom: 2px solid #0062cc;
        position: sticky;
        top: 0;
    }

    .email-logs-table th {
        padding: 14px 12px;
        text-align: left;
        font-size: 11px;
        font-weight: 700;
        color: #0062cc;
        text-transform: uppercase;
        letter-spacing: 0.8px;
        white-space: nowrap;
        border-right: 1px solid #dee2e6;
    }

    .email-logs-table th:last-child {
        border-right: none;
    }

    .email-logs-table tbody tr {
        border-bottom: 1px solid #dee2e6;
        transition: background-color 0.2s ease;
    }

    .email-logs-table tbody tr:hover {
        background-color: rgba(0, 98, 204, 0.05);
    }

    .email-logs-table tbody tr:last-child {
        border-bottom: none;
    }

    .email-logs-table td {
        padding: 13px 12px;
        font-size: 12px;
        color: #212529;
        word-break: break-word;
        white-space: normal;
        border-right: 1px solid #f0f0f0;
    }

    .email-logs-table td:last-child {
        border-right: none;
    }

    .email-logs-table {
        table-layout: auto;
        word-break: break-word;
    }

    .email-logs-table td {
        padding: 13px 10px;
        font-size: 12px;
        color: #212529;
        word-wrap: break-word;
        white-space: normal;
        border-right: 1px solid #f0f0f0;
    }

    .email-logs-table td:last-child {
        border-right: none;
    }

    .email-logs-table td:nth-child(1) {
        font-weight: 700;
        color: #0062cc;
        text-align: center;
        width: auto;
        min-width: 50px;
    }

    .email-logs-table td:nth-child(2) {
        background-color: rgba(0, 98, 204, 0.05);
        font-weight: 600;
        color: #0062cc;
        width: auto;
        min-width: 100px;
    }

    .email-logs-table td:nth-child(3) {
        width: auto;
        min-width: 120px;
    }

    .email-logs-table td:nth-child(4) {
        width: auto;
        min-width: 120px;
    }

    .email-logs-table td:nth-child(5) {
        width: auto;
        min-width: 140px;
    }

    .email-logs-table td:nth-child(6) {
        background-color: rgba(0, 98, 204, 0.08);
        text-align: center;
        width: auto;
        min-width: 100px;
    }

    .email-logs-table td:nth-child(7) {
        width: auto;
        min-width: 130px;
    }
    /**/
    .email-logs-table td:nth-child(8) {
    width: 300px !important;  /* Increased width */
    min-width: 300px !important;
    max-width: 400px !important;
    word-wrap: break-word;
    white-space: normal;
    line-height: 1.4;
}

.email-logs-table td .details-cell {
    display: inline-block;
    max-width: 100%;
    cursor: help;
    position: relative;
}

/* Tooltip container */
.tooltip-wrapper {
    position: relative;
    display: inline-block;
    width: 100%;
}

/* Tooltip text */
.tooltip-wrapper .full-text-tooltip {
    visibility: hidden;
    background-color: rgba(0, 0, 0, 0.9);
    color: #fff;
    text-align: left;
    padding: 12px;
    border-radius: 6px;
    position: absolute;
    z-index: 9999;
    bottom: 125%; /* Position above the text */
    left: 50%;
    transform: translateX(-50%);
    white-space: normal;
    width: 400px;
    max-width: 400px;
    word-wrap: break-word;
    font-size: 12px;
    line-height: 1.4;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    opacity: 0;
    transition: opacity 0.3s, visibility 0.3s;
}

/* Tooltip arrow */
.tooltip-wrapper .full-text-tooltip::after {
    content: "";
    position: absolute;
    top: 100%;
    left: 50%;
    transform: translateX(-50%);
    border-width: 5px;
    border-style: solid;
    border-color: rgba(0, 0, 0, 0.9) transparent transparent transparent;
}

/* Show tooltip on hover */
.tooltip-wrapper:hover .full-text-tooltip {
    visibility: visible;
    opacity: 1;
}

/* For better mobile experience */
@media (max-width: 768px) {
    .email-logs-table td:nth-child(8) {
        width: 200px !important;
        min-width: 200px !important;
        max-width: 250px !important;
    }
    
    .tooltip-wrapper .full-text-tooltip {
        width: 300px;
        left: 0;
        transform: none;
    }
}
/**/
    .route-badge {
        display: inline-block;
        padding: 5px 10px;
        border-radius: 4px;
        font-size: 11px;
        font-weight: 700;
        text-transform: capitalize;
        background: linear-gradient(135deg, #0062cc 0%, #004085 100%);
        color: white;
        white-space: nowrap;
    }

    .empty-state {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        height: 300px;
        color: #6c757d;
        gap: 12px;
    }

    .empty-state i {
        font-size: 48px;
        color: #0062cc;
        opacity: 0.5;
    }

    .empty-state p {
        margin: 0;
        font-size: 14px;
        font-weight: 500;
    }

    .panel-body::-webkit-scrollbar {
        width: 8px;
    }

    .panel-body::-webkit-scrollbar-track {
        background: #f1f3f5;
    }

    .panel-body::-webkit-scrollbar-thumb {
        background: linear-gradient(135deg, #0062cc 0%, #004085 100%);
        border-radius: 4px;
    }

    .panel-body::-webkit-scrollbar-thumb:hover {
        background: linear-gradient(135deg, #004085 0%, #002d5c 100%);
    }

    /* RESPONSIVE DESIGN */
    @media (max-width: 768px) {
        .floating-email-btn {
            padding: 12px 6px;
            font-size: 9px;
            width: 36px;
            min-height: 100px;
        }

        .email-log-panel {
            width: 100%;
            right: -100%;
        }

        .panel-header {
            padding: 14px 16px;
        }

        .panel-body {
            padding: 15px;
        }

        .email-logs-table th,
        .email-logs-table td {
            padding: 10px 8px;
            font-size: 11px;
        }

       
    }

    @media (max-width: 480px) {
        .floating-email-btn {
            left: auto;
            right: 0;
            border-radius: 8px 0 0 8px;
            padding: 10px 8px;
        }

        .email-log-panel {
            width: 100%;
            left: auto;
            right: -100%;
        }

        .email-log-panel.open {
            left: auto;
            right: 0;
        }

        .panel-title {
            font-size: 16px;
        }

        .btn-close-panel {
            width: 32px;
            height: 32px;
        }
    }

    @media print {
        .floating-email-btn,
        .email-log-panel {
            display: none !important;
        }
    }

    .route-badge {
        display: inline-block;
        padding: 5px 10px;
        border-radius: 4px;
        font-size: 11px;
        font-weight: 700;
        background: linear-gradient(135deg, #0062cc 0%, #004085 100%);
        color: white;
    }

    .empty-state {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        height: 300px;
        color: #6c757d;
    }

    .empty-state i {
        font-size: 48px;
        color: #0062cc;
        opacity: 0.5;
    }

    /* Responsive Styles */
    @media (min-width: 768px) {
        .mobile-controls,
        .mobile-export-section,
        .mobile-cards-container {
            display: none !important;
        }
    }

    @media (max-width: 767.98px) {
        .desktop-controls-wrapper,
        .dt-search-wrapper,
        .dt-plr-filter,
        .dt-dept-filter,
        .dt-surveyor-filter,
        .dt-buttons,
        #filesTable_wrapper {
            display: none !important;
        }

        .mobile-controls {
            display: block !important;
        }

        .mobile-export-section {
            display: block !important;
        }

        .mobile-cards-container {
            display: block !important;
        }

        .stats-container {
            flex-direction: column;
            align-items: stretch;
            margin-top: 60px;
            margin-left: 0 !important;
            padding: 0 10px !important;
            width: 100% !important;
        }

        .stats-main-card {
            width: 100%;
            margin: 0 0 15px 0;
            padding: 15px;
        }

        #leftPanel {
            width: 100% !important;
            max-width: 100% !important;
            margin: 0 !important;
            padding: 10px;
        }

        .stats-grid {
            flex-wrap: wrap;
            gap: 12px;
        }

        .stat-item {
            flex: 1 1 calc(50% - 12px);
            min-width: 200px;
        }

        .stat-divider {
            display: none;
        }

        #appointmentStats {
            gap: 4px;
            overflow-x: auto;
        }

        .stat-card {
            width: 52px !important;
            height: 52px !important;
            max-width: 52px !important;
            min-width: 52px !important;
        }

        .email-log-panel {
            width: 100%;
            right: -100%;
        }

        .whatsapp-float {
            width: 50px;
            height: 50px;
            bottom: 20px;
            right: 20px;
        }

        .whatsapp-float i {
            font-size: 28px;
        }
    }

    @media (max-width: 575.98px) {
        .stats-container {
            padding: 0 5px !important;
            margin-top: 40px;
        }

        .stats-main-card {
            padding: 10px;
        }

        .stat-card {
            width: 48px !important;
            height: 48px !important;
        }
    }
    .route-statuschange {
    background: linear-gradient(135deg, #6610f2 0%, #520dc2 100%) !important;
}

.route-fileupload {
    background: linear-gradient(135deg, #28a745 0%, #218838 100%) !important;
}

.route-approval {
    background: linear-gradient(135deg, #17a2b8 0%, #138496 100%) !important;
}

.route-reminder {
    background: linear-gradient(135deg, #007bff 0%, #0056b3 100%) !important;
}

.route-plrrevision {
    background: linear-gradient(135deg, #ffc107 0%, #e0a800 100%) !important;
    color: #212529 !important;
}

.route-other {
    background: linear-gradient(135deg, #6c757d 0%, #495057 100%) !important;
}
/* Add these styles to your existing CSS */

/* Last Activity column styling */
#filesTable tbody tr td:nth-child(4) { /* 4th column = Last Activity */
    min-width: 140px;
    text-align: center;
}

.last-activity-container {
    display: inline-block;
    text-align: center;
    cursor: help;
}

/* Time badges */
.badge.bg-success { 
    background: linear-gradient(135deg, #28a745 0%, #218838 100%) !important; 
    border: 1px solid #1e7e34;
}
.badge.bg-info { 
    background: linear-gradient(135deg, #17a2b8 0%, #138496 100%) !important; 
    border: 1px solid #117a8b;
}
.badge.bg-warning { 
    background: linear-gradient(135deg, #ffc107 0%, #e0a800 100%) !important; 
    border: 1px solid #d39e00;
}
.badge.bg-danger { 
    background: linear-gradient(135deg, #dc3545 0%, #c82333 100%) !important; 
    border: 1px solid #bd2130;
}
.badge.bg-secondary { 
    background: linear-gradient(135deg, #6c757d 0%, #495057 100%) !important; 
    border: 1px solid #3d4348;
}

/* Mobile view adjustment */
.mobile-card .table-sm td:nth-child(2) {
    text-align: right;
}

/* For the Last Activity row in mobile */
.mobile-card .table-sm tr td:first-child {
    font-weight: 600;
    color: #495057;
    width: 40%;
}

.mobile-card .table-sm tr td:last-child {
    width: 60%;
}

/* Make badges look better on mobile */
@media (max-width: 767.98px) {
    .mobile-card .badge {
        font-size: 11px;
        padding: 4px 8px;
    }
    
    .mobile-card .bi-clock-history {
        font-size: 12px;
    }
}
/* Just sticky table header */
.email-log-panel .email-logs-table thead {
    position: sticky;
    top: 0;
    z-index: 100;
    background: #f8f9fa;
}

.email-log-panel .email-logs-table {
    max-height: 400px; /* Or whatever height you prefer */
    overflow-y: auto;
    display: block;
}
/* Active page - current page */
.dataTables_wrapper .dataTables_paginate .paginate_button.current {
    background-color: #0066cc !important;
    border-color: #0066cc !important;
    color: white !important;
}

/* Hover on active page */
.dataTables_wrapper .dataTables_paginate .paginate_button.current:hover {
    background-color: #0052a3 !important; /* Darker blue on hover */
    border-color: #0052a3 !important;
    color: white !important; /* Keep text white */
}

/* Hover on inactive pages */
.dataTables_wrapper .dataTables_paginate .paginate_button:hover {
    background-color: #e9ecef !important;
    border-color: #dee2e6 !important;
    color: #0066cc !important; /* Blue text on hover */
}

/* For Bootstrap pagination (if used) */
.pagination .page-item.active .page-link {
    background-color: #0066cc !important;
    border-color: #0066cc !important;
    color: white !important;
}

.pagination .page-item.active .page-link:hover {
    background-color: #0052a3 !important;
    border-color: #0052a3 !important;
    color: white !important;
}

.pagination .page-item .page-link:hover {
    background-color: #e9ecef !important;
    color: #0066cc !important;
}
    
</style>
 <script>
    $(document).ready(function() {
        // Global variables
        var table;
        var isMobile = window.innerWidth < 768;
        var currentDocNo = null;
        var currentClaimData = {};
        var mobileOffset = 0;
        var mobileLimit = 20;
        var mobileHasMore = true;
        var allData = [];
        var emails = [];
        var currentFilters = {
            plr: 'all',
            dept: '',
            surveyor: [],
            search: '',
            days: ''
        };

        // Helper function to get email by surveyor code
        function getEmailBySurveyorCode(surveyorCode) {
            if (!surveyorCode) return '';
            
            const codeToMatch = surveyorCode.toString().trim();
            // console.log('Looking for email with code:', codeToMatch, 'in emails array:', emails);
            
            // First, try exact match
            let emailRecord = emails.find(email => {
                if (!email.code) return false;
                const emailCode = email.code ? email.code.toString().trim() : '';
                // console.log(`Comparing: "${emailCode}" === "${codeToMatch}"`);
                return emailCode === codeToMatch;
            });
            
            // If not found, try removing leading zeros
            if (!emailRecord && codeToMatch.startsWith('0')) {
                const codeWithoutZeros = codeToMatch.replace(/^0+/, '');
                // console.log('Trying without leading zeros:', codeWithoutZeros);
                emailRecord = emails.find(email => {
                    if (!email.code) return false;
                    const emailCode = email.code ? email.code.toString().trim() : '';
                    return emailCode === codeWithoutZeros;
                });
            }
            
            // If still not found, try numeric comparison
            if (!emailRecord) {
                const numericCode = parseInt(codeToMatch);
                if (!isNaN(numericCode)) {
                    // console.log('Trying numeric comparison:', numericCode);
                    emailRecord = emails.find(email => {
                        if (!email.code) return false;
                        const emailCodeNum = parseInt(email.code);
                        return emailCodeNum === numericCode;
                    });
                }
            }
            
            const result = emailRecord ? (emailRecord.email || '') : '';
            // console.log('Email lookup result:', result);
            return result;
        }

        // Unified email logging function
        function logEmailToDatabase(emailData) {
            $.ajax({
                url: '{{ route("reminder.debugAllLogs") }}',
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    uw_doc: emailData.uw_doc || '',
                    sender: emailData.sender || '',
                    receiver: emailData.receiver || '',
                    cc_email: emailData.cc_email || '',
                    sub: emailData.sub || '',
                    route: emailData.route || '',
                    body: emailData.body || '',
                    curdatetime: new Date().toISOString()
                },
                success: function(response) {
                    // console.log('Email logged to database:', response);
                },
                error: function(xhr) {
                    console.error('Failed to log email:', xhr.responseText);
                }
            });
        }

        // Function to automatically log status changes
        function logStatusChangeToDatabase(docNo, action, reportType, remarks = '') {
            // console.log('Logging status change:', { docNo, action, reportType, remarks });
            
            $.ajax({
                url: '{{ route("reminder.debugAllLogs") }}',
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    uw_doc: docNo,
                    sender: '{{ $userEmail }}',
                    receiver: 'System',
                    cc_email: '',
                    sub: 'Status Changed: ' + action + ' - ' + reportType,
                    route: 'Status Change',
                    body: reportType + ' ' + action + ' for document ' + docNo + 
                          (remarks ? '. Remarks: ' + remarks : ''),
                    curdatetime: new Date().toISOString()
                },
                success: function(response) {
                    // console.log('Status change logged to database:', response);
                },
                error: function(xhr) {
                    console.error('Failed to log status change:', xhr.responseText);
                }
            });
        }

        // Load all data once on page load
        function loadAllData() {
            // console.log('Fetching data from API...');
            
            $.ajax({
                url: '{{ route("admin.getFiles") }}',
                method: 'GET',
                timeout: 60000,
                success: function(response) {
                    // console.log('Data fetched successfully:', response.total, 'records');
                    // console.log('Emails loaded from backend:', response.emails);
                    
                    if (response.success && response.data) {
                        allData = response.data;
                        emails = response.emails || [];

                        // Hide loading overlay
                        $('#loadingOverlay').fadeOut(300);
                        
                        // Initialize everything IN THE CORRECT ORDER
                        updateMainStats();
                        updateAppointmentStats();
                        
                        // CRITICAL: Populate surveyors AFTER data is loaded
                        populateSurveyorOptions();
                        
                        if (isMobile) {
                            initMobileView();
                        } else {
                            initDesktopTable();
                        }
                        
                        showToast('Data loaded successfully! (' + allData.length + ' records)', 'success');
                    } else {
                        $('#loadingOverlay').fadeOut();
                        showToast('Failed to load data', 'error');
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Error loading data:', error);
                    $('#loadingOverlay').fadeOut();
                    showToast('Error loading data from server', 'error');
                }
            });
        }

        // Generate PLR badge
        function generatePLRBadge(plr_final, rep_tag) {
            const isFinalReport = rep_tag === 'F/R';
            const isPrelimReport = rep_tag === 'P/R';
            
            // Handle approved cases
            if (plr_final === 'Y') {
                if (isFinalReport) {
                    return '<span class="badge bg-success"><i class="bi bi-check-circle me-1"></i> Approved (Final)</span>';
                } else if (isPrelimReport) {
                    return '<span class="badge bg-success"><i class="bi bi-check-circle me-1"></i> Approved (PLR)</span>';
                } else {
                    return '<span class="badge bg-success"><i class="bi bi-check-circle me-1"></i> Approved (PLR)</span>';
                }
            }
            
            // Handle unapproved cases
            else if (plr_final === 'N') {
                if (isFinalReport) {
                    return '<span class="badge bg-danger"><i class="bi bi-x-circle me-1"></i> F/R-App-Pending</span>';
                } else if (isPrelimReport) {
                    return '<span class="badge bg-danger"><i class="bi bi-x-circle me-1"></i> P/R-App-Pending</span>';
                } else {
                    return '<span class="badge bg-danger"><i class="bi bi-x-circle me-1"></i> P/R-App-Pending</span>';
                }
            }
            
            // Handle other statuses
            else if (plr_final === 'I') {
                return '<span class="badge bg-primary"><i class="bi bi-hourglass-split me-1"></i> In Process</span>';
            }
            else if (plr_final === 'R') {
                return '<span class="badge bg-warning text-dark"><i class="bi bi-bell me-1"></i> Reminder Sent</span>';
            }
            else if (plr_final === 'V') {
                return '<span class="badge bg-info"><i class="bi bi-arrow-repeat me-1"></i> Revision Requested</span>';
            }
            
            // Handle null/undefined/empty
            else if (plr_final === undefined || plr_final === null || plr_final === '') {
                return '<span class="badge bg-secondary"><i class="bi bi-clock me-1"></i> Not Uploaded</span>';
            }
            
            // Fallback
            return '<span class="badge bg-dark">Unknown Status</span>';
        }

        // Update main statistics cards
        function updateMainStats() {
            const total = allData.length;
            
            const approvedPLR = allData.filter(item => 
                item.plr_final === 'Y' && item.rep_tag === 'P/R'
            ).length;
            
            const approvedFinal = allData.filter(item => 
                item.plr_final === 'Y' && item.rep_tag === 'F/R'
            ).length;
            
            const totalApproved = approvedPLR + approvedFinal;
            
            const unapproved = allData.filter(item => item.plr_final === 'N').length;
            
            const inProcess = allData.filter(item => 
                item.plr_final === 'I' || item.plr_final === 'R' || item.plr_final === 'V'
            ).length;
            
            const pending = allData.filter(item => 
                !item.rep_tag && (item.plr_final === undefined || item.plr_final === null)
            ).length;

            $('#totalFilesAssigned').text(total);
            $('#totalApproved').text(totalApproved);
            $('#totalInProcess').text(inProcess);
            $('#totalPending').text(pending);
        }

        // Update appointment stats
        function updateAppointmentStats() {
            const days7 = allData.filter(item => item.days_passed && item.days_passed <= 7).length;
            const days15 = allData.filter(item => item.days_passed && item.days_passed <= 15).length;
            const days30 = allData.filter(item => item.days_passed && item.days_passed <= 30).length;
            const days60 = allData.filter(item => item.days_passed && item.days_passed <= 60).length;
            const days90 = allData.filter(item => item.days_passed && item.days_passed <= 90).length;
            const days90plus = allData.filter(item => item.days_passed && item.days_passed > 90).length;

            $('#appointmentStats .stat-card[data-days="7"] .count').text(days7);
            $('#appointmentStats .stat-card[data-days="15"] .count').text(days15);
            $('#appointmentStats .stat-card[data-days="30"] .count').text(days30);
            $('#appointmentStats .stat-card[data-days="60"] .count').text(days60);
            $('#appointmentStats .stat-card[data-days="90"] .count').text(days90);
            $('#appointmentStats .stat-card[data-days="90plus"] .count').text(days90plus);
            $('#appointmentStats .stat-card[data-days="all"] .count').text(allData.length);
        }

        // Client-side filtering
        function applyFilters() {
            let filtered = [...allData];

            // PLR Status filter
            if (currentFilters.plr !== 'all') {
                filtered = filtered.filter(item => {
                    const plr_final = item.plr_final;
                    const rep_tag = item.rep_tag;
                    
                    if (currentFilters.plr === 'P/R Approved') {
                        return plr_final === 'Y' && rep_tag === 'P/R';
                    }
                    
                    if (currentFilters.plr === 'F/R Approved') {
                        return plr_final === 'Y' && rep_tag === 'F/R';
                    }
                    
                    if (currentFilters.plr === 'In process') {
                        return plr_final === 'I' || plr_final === 'R' || plr_final === 'V';
                    }
                    
                    if (currentFilters.plr === 'Unapproved') {
                        return plr_final === 'N';
                    }
                    
                    if (currentFilters.plr === 'Pending') {
                        return !rep_tag && (plr_final === undefined || plr_final === null);
                    }
                    
                    return true;
                });
            }

            // Department filter
            if (currentFilters.dept) {
                filtered = filtered.filter(item => item.department === currentFilters.dept);
            }

            // Surveyor filter
            if (currentFilters.surveyor.length > 0) {
                filtered = filtered.filter(item => currentFilters.surveyor.includes(item.surveyor_name));
            }

            // Days filter
            if (currentFilters.days && currentFilters.days !== 'all') {
                filtered = filtered.filter(item => {
                    const daysPassed = item.days_passed;
                    if (!daysPassed || daysPassed === 'N/A') return false;
                    
                    if (currentFilters.days === '90plus') {
                        return daysPassed > 90;
                    } else {
                        const daysNum = parseInt(currentFilters.days);
                        return daysPassed <= daysNum;
                    }
                });
            }

            // Search filter
            if (currentFilters.search) {
                const searchTerm = currentFilters.search.toLowerCase();
                filtered = filtered.filter(item => {
                    const searchableFields = [
                        item.doc_no,
                        item.client_name,
                        item.surveyor_name,
                        item.policy_number,
                        item.city,
                        item.department,
                        item.loss_description
                    ].filter(field => field && field !== 'NA').join(' ').toLowerCase();
                    
                    return searchableFields.includes(searchTerm);
                });
            }

            // SORT BY LAST ACTIVITY (NEWEST FIRST)
            filtered.sort((a, b) => {
                try {
                    // Handle null/undefined/invalid dates
                    const dateA = a.last_activity;
                    const dateB = b.last_activity;
                    
                    // If both are invalid, keep original order
                    if ((!dateA || dateA === '0000-00-00 00:00:00' || dateA === 'null') && 
                        (!dateB || dateB === '0000-00-00 00:00:00' || dateB === 'null')) {
                        return 0;
                    }
                    
                    // If A is invalid, push it to bottom
                    if (!dateA || dateA === '0000-00-00 00:00:00' || dateA === 'null') {
                        return 1;
                    }
                    
                    // If B is invalid, keep A at top
                    if (!dateB || dateB === '0000-00-00 00:00:00' || dateB === 'null') {
                        return -1;
                    }
                    
                    // Parse dates for comparison
                    const timeA = new Date(dateA).getTime();
                    const timeB = new Date(dateB).getTime();
                    
                    // Return comparison for descending order (newest first)
                    return timeB - timeA;
                } catch (e) {
                    console.error('Error sorting by last activity:', e);
                    return 0;
                }
            });

            return filtered;
        }

        // Initialize Desktop DataTable
        function initDesktopTable() {
            const filteredData = applyFilters();
            
            table = $('#filesTable').DataTable({
                data: filteredData,
                columns: [
                    {
                        data: null,
                        orderable: false,
                        render: function(data, type, row) {
                            return `
                                <div class="action-buttons-container">
                                    <button class="btn btn-sm btn-info view-btn" 
                                            data-doc="${row.doc_no}" 
                                            data-client="${row.client_name}" 
                                            data-surveyor="${row.surveyor_name}"
                                            title="View Documents">
                                        <i class="bi bi-eye-fill"></i>
                                    </button>
                                    <button class="btn btn-sm btn-warning revision-btn" 
                                            data-doc="${row.doc_no}" 
                                            data-client="${row.client_name}"
                                            title="PLR Revision">
                                        <i class="bi bi-arrow-repeat"></i>
                                    </button>
                                    <button class="btn btn-sm btn-reminder reminder-btn" 
                                            data-doc="${row.doc_no}" 
                                            data-client="${row.client_name}"
                                            title="Send Reminder">
                                        <i class="bi bi-bell-fill"></i>
                                    </button>
                                    <button class="btn btn-sm btn-secondary email-log-btn" 
                                            data-doc="${row.doc_no}" 
                                            data-client="${row.client_name}"
                                            title="View Document Logss">
                                        <i class="bi bi-envelope-open"></i>
                                    </button>
                                </div>
                            `;
                        }
                    },
                    {
                        data: null,
                        render: function(data, type, row) {
                            return generatePLRBadge(row.plr_final, row.rep_tag);
                        }
                    },
                    {
                        data: 'days_passed',
                        render: function(data) {
                            if (data && data !== 'N/A') {
                                var badgeClass = data > 90 ? 'bg-danger' : (data > 60 ? 'bg-warning text-dark' : 'bg-info');
                                return '<span class="badge ' + badgeClass + '">' + data + ' days</span>';
                            }
                            return '<span class="badge bg-secondary">N/A</span>';
                        }
                    },
                    {
                        data: 'last_activity',
                        className: "text-center",
                        render: function(data) {
                            if (!data || data === '0000-00-00 00:00:00' || data === 'null') {
                                return '<span class="badge bg-secondary">Never</span>';
                            }
                            
                            try {
                                const date = new Date(data);
                                if (isNaN(date.getTime())) {
                                    return '<span class="badge bg-secondary">Invalid Date</span>';
                                }
                                
                                // Convert to Pakistan time (UTC+5)
                                const pakistanTime = new Date(date.getTime() + (5 * 60 * 60 * 1000));
                                
                                // Manual formatting for consistent display
                                const months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
                                const day = pakistanTime.getDate();
                                const month = months[pakistanTime.getMonth()];
                                
                                // 12-hour format with AM/PM
                                let hours = pakistanTime.getHours();
                                const minutes = pakistanTime.getMinutes().toString().padStart(2, '0');
                                const ampm = hours >= 12 ? 'PM' : 'AM';
                                
                                // Convert to 12-hour format
                                hours = hours % 12;
                                hours = hours ? hours : 12; // 0 should be 12
                                
                                // Format hours with leading zero for single digit
                                const formattedHours = hours.toString().padStart(2, '0');
                                
                                const displayTime = `${month} ${day}, ${formattedHours}:${minutes} ${ampm}`;
                                
                                // Calculate time difference
                                const now = new Date();
                                const diffMs = now - date;
                                const diffHours = Math.floor(diffMs / (1000 * 60 * 60));
                                
                                // Color coding
                                let badgeClass = 'bg-success';
                                if (diffHours >= 24 && diffHours < 48) {
                                    badgeClass = 'bg-info';
                                } else if (diffHours >= 48 && diffHours < 72) {
                                    badgeClass = 'bg-warning text-dark';
                                } else if (diffHours >= 72) {
                                    badgeClass = 'bg-danger';
                                }
                                
                                // Full date for tooltip (12-hour format)
                                const fullHours = hours.toString().padStart(2, '0');
                                const fullDate = `${month} ${day}, ${pakistanTime.getFullYear()} at ${fullHours}:${minutes} ${ampm}`;
                                
                                return `
                                    <span class="badge ${badgeClass}" title="${fullDate}">
                                        <i class="bi bi-clock me-1"></i>
                                        ${displayTime}
                                    </span>
                                `;
                                
                            } catch (error) {
                                console.error('Error parsing date:', data, error);
                                return '<span class="badge bg-secondary">Error</span>';
                            }
                        },
                        type: 'date', // Tell DataTables this is a date column
                        orderSequence: ['desc', 'asc'] // Default to descending (newest first)
                    },
                    { data: 'doc_no' },
                    { data: 'intimation_date' },
                    { data: 'client_name' },
                    {data:  'loss_description'},
                    { data: 'surveyor_name' },
                    { data: 'appointment_date' },
                    {
                        data: 'loss_claimed',
                        className: "text-right", 
                        render: function(data) {
                            if (data && !isNaN(data) && data !== 'NA') {
                                return parseFloat(data).toLocaleString('en-US', {
                                    minimumFractionDigits: 0,
                                    maximumFractionDigits: 2
                                });
                            }
                            return data || 'NA';
                        }
                    },
                    {
                        data: 'estimate_amount',
                        className: "text-right", 
                        render: function(data) {
                            if (data && !isNaN(data) && data !== 'NA') {
                                return parseFloat(data).toLocaleString('en-US', {
                                    minimumFractionDigits: 0,
                                    maximumFractionDigits: 2
                                });
                            }
                            return data || 'NA';
                        }
                    },
                    { data: 'city' },
                    { data: 'policy_number' },
                    {
                        data: 'sum_insured',
                        className: "text-right",
                        render: function(data) {
                            return data && !isNaN(data) ? parseFloat(data).toLocaleString('en-US', {
                                minimumFractionDigits: 0,
                                maximumFractionDigits: 2
                            }) : 'N/A';
                        }
                    },
                    { data: 'issue_date' },
                    { data: 'expiry_date' },
                ],
                paging: true,
                pageLength: 50,
                lengthMenu: [[25, 50, 100, 250], [25, 50, 100, 250]],
                ordering: true,
                responsive: false,
                scrollX: true,
                scrollY: '400px',
                scrollCollapse: false,
                dom: '<"desktop-controls-wrapper d-flex justify-content-between align-items-center mb-2"<"dt-search-wrapper flex-shrink-0 me-auto"f><"filters-wrapper d-flex justify-content-center flex-grow-1"<"dt-plr-filter mx-2"<"plr-filter-inner">><"dt-dept-filter mx-2"<"dept-filter-inner">><"dt-surveyor-filter mx-2"<"surveyor-filter-inner">>><"dt-buttons flex-shrink-0"B>>rtip',
                buttons: [
                    {
                        extend: 'copy',
                        text: '<i class="fas fa-copy me-1"></i>Copy',
                        className: 'btn btn-sm btn-outline-secondary',
                        exportOptions: { columns: ':visible:not(:eq(0))' }
                    },
                    {
                        extend: 'csv',
                        text: '<i class="fas fa-file-csv me-1"></i>CSV',
                        className: 'btn btn-sm btn-outline-success',
                        exportOptions: { columns: ':visible:not(:eq(0))' }
                    },
                    {
                        extend: 'excel',
                        text: '<i class="fas fa-file-excel me-1"></i>Excel',
                        className: 'btn btn-sm btn-outline-success',
                        exportOptions: { columns: ':visible:not(:eq(0))' }
                    },
                    {
                        extend: 'pdfHtml5',
                        text: '<i class="fas fa-file-pdf me-1"></i>PDF',
                        className: 'btn btn-sm btn-outline-danger',
                        orientation: 'landscape',
                        exportOptions: { columns: ':visible:not(:eq(0))' }
                    },
                    {
                        extend: 'print',
                        text: '<i class="fas fa-print me-1"></i>Print',
                        className: 'btn btn-sm btn-outline-primary',
                        exportOptions: { columns: ':visible:not(:eq(0))' }
                    }
                ],
                columnDefs: [{
                    targets: "_all",
                    className: "text-nowrap"
                }],
                order: [[3, 'desc']], // Column index 3 is Last Activity, sort descending by default
                initComplete: function() {
                    
                    // PLR Filter
                    $('.plr-filter-inner').html(
                        '<div class="d-flex align-items-center"><label for="plrFilter" style="font-size: 15px; font-weight: 700; color:#000000 !important; font-family: Calibri, sans-serif !important;" class="me-2 fw-bold mb-0">Status:</label><select id="plrFilter" class="form-select form-select-sm" style="width:140px;"><option value="all">All Status</option><option value="P/R Approved">P/R Approved</option><option value="F/R Approved">F/R Approved</option><option value="In process">In Process</option><option value="Pending">Pending</option><option value="Unapproved">Unapproved</option></select></div>'
                    );

                    // Department Filter
                    $('.dept-filter-inner').html(
                        '<div class="d-flex align-items-center"><label for="deptFilter" style="font-size: 15px; font-weight: 700; color:#000000 !important; font-family: Calibri, sans-serif !important;" class="me-2 fw-bold mb-0">Department:</label><select id="deptFilter" class="form-select form-select-sm" style="width:120px;"><option value="">All</option><option value="Fire">Fire</option><option value="Marine">Marine</option><option value="Motor">Motor</option><option value="Miscellaneous">Miscellaneous</option></select></div>'
                    );

                    // Surveyor Filter with multi-select
                    var surveyorHTML = `
                        <div class="d-flex align-items-center" style="position: relative; z-index: 2000;">
                            <label style="font-size: 15px; font-weight: 700; color:#000000 !important; font-family: Calibri, sans-serif !important;" class="me-2 fw-bold mb-0">Surveyor:</label>
                            <div class="multi-select-wrapper" style="width:200px; position: relative;">
                                <button type="button" class="multi-select-button" id="desktopSurveyorMultiSelectBtn" style="position: relative; z-index: 2;">
                                    <span class="multi-select-text">Select Surveyors</span>
                                    <i class="bi bi-chevron-down multi-select-arrow"></i>
                                </button>
                                <div class="multi-select-dropdown" id="desktopSurveyorMultiSelectDropdown" style="position: absolute !important; z-index: 999999 !important;">
                                    <div class="multi-select-search">
                                        <input type="text" id="desktopSurveyorSearchInput" placeholder="Search surveyors...">
                                    </div>
                                    <div class="multi-select-options" id="desktopSurveyorOptionsContainer"></div>
                                    <div class="multi-select-actions">
                                        <button type="button" class="multi-select-filter-btn" id="desktopSurveyorFilterBtn">Filter</button>
                                        <button type="button" class="multi-select-clear" id="desktopSurveyorClearAll">Clear All</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    `;
                    $('.surveyor-filter-inner').html(surveyorHTML);

                    // CRITICAL: Re-populate surveyors AFTER the HTML is inserted
                    populateSurveyorOptions();

                    // Initialize desktop multi-select
                    initDesktopMultiSelect();

                    // Set up filter handlers
                    $('#plrFilter, #deptFilter').on('change', function() {
                        updateDesktopFilters();
                    });
                }
            });
        }

        // Update desktop filters
        function updateDesktopFilters() {
            currentFilters.plr = $('#plrFilter').val() || 'all';
            currentFilters.dept = $('#deptFilter').val() || '';
            currentFilters.search = table ? table.search() : '';
            
            refreshDesktopTable();
        }

        // Refresh desktop table
        function refreshDesktopTable() {
            if (!table) return;
            
            const filtered = applyFilters();
            
            // Clear and repopulate the table completely
            table.clear();
            if (filtered.length > 0) {
                table.rows.add(filtered);
            }
            table.draw();
        }

        // Initialize Mobile View
        function initMobileView() {
            mobileOffset = 0;
            mobileHasMore = true;
            loadMobileCards();
            initMobileMultiSelect();
        }

        // Generate mobile card
        function generateMobileCard(file) {
            var plrBadge = generatePLRBadge(file.plr_final, file.rep_tag);
            
            var daysPassed = file.days_passed || 'N/A';
            var badgeClass = daysPassed !== 'N/A' && daysPassed > 90 ? 'bg-danger' : (daysPassed > 60 ? 'bg-warning text-dark' : 'bg-info');
            
            // LAST ACTIVITY
            let lastActivityHtml = '<span class="badge bg-secondary">Never</span>';
            if (file.last_activity && file.last_activity !== '0000-00-00 00:00:00') {
                try {
                    const date = new Date(file.last_activity);
                    if (!isNaN(date.getTime())) {
                        // Convert to Pakistan time
                        const pakistanTime = new Date(date.getTime() + (5 * 60 * 60 * 1000));
                        
                        const months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
                        const day = pakistanTime.getDate();
                        const month = months[pakistanTime.getMonth()];
                        
                        // 12-hour format with AM/PM
                        let hours = pakistanTime.getHours();
                        const minutes = pakistanTime.getMinutes().toString().padStart(2, '0');
                        const ampm = hours >= 12 ? 'PM' : 'AM';
                        
                        // Convert to 12-hour format
                        hours = hours % 12;
                        hours = hours ? hours : 12; // 0 should be 12
                        
                        // Format hours with leading zero
                        const formattedHours = hours.toString().padStart(2, '0');
                        
                        const displayTime = `${month} ${day}, ${formattedHours}:${minutes} ${ampm}`;
                        
                        const now = new Date();
                        const diffMs = now - date;
                        const diffHours = Math.floor(diffMs / (1000 * 60 * 60));
                        
                        let badgeClass = 'bg-success';
                        if (diffHours >= 24 && diffHours < 48) {
                            badgeClass = 'bg-info';
                        } else if (diffHours >= 48 && diffHours < 72) {
                            badgeClass = 'bg-warning text-dark';
                        } else if (diffHours >= 72) {
                            badgeClass = 'bg-danger';
                        }
                        
                        lastActivityHtml = `<span class="badge ${badgeClass}">${displayTime}</span>`;
                    }
                } catch (error) {
                    console.error('Error parsing last activity:', file.last_activity, error);
                }
            }
            
            return `
                <div class="mobile-card mb-3">
                    <div class="card shadow-sm">
                        <div class="card-body">
                            <div class="mb-3 text-end">
                                ${plrBadge}
                            </div>
                            <table class="table table-sm mb-3">
                                <tr><td><strong>Claim No:</strong></td><td>${file.doc_no}</td></tr>
                                <tr><td><strong>Client Name:</strong></td><td>${file.client_name || 'N/A'}</td></tr>
                                <tr><td><strong>Surveyor Name:</strong></td><td>${file.surveyor_name || 'N/A'}</td></tr>
                                <tr><td><strong>Intimation Date:</strong></td><td>${file.intimation_date || 'N/A'}</td></tr>
                                <tr><td><strong>Appointment Date:</strong></td><td>${file.appointment_date || 'N/A'}</td></tr>
                                <tr><td><strong>Days Passed:</strong></td><td>${daysPassed !== 'N/A' ? '<span class="badge ' + badgeClass + '">' + daysPassed + ' days</span>' : '<span class="badge bg-secondary">N/A</span>'}</td></tr>
                                <tr><td><strong><i class="bi bi-clock-history me-1"></i>Last Activity:</strong></td><td>${lastActivityHtml}</td></tr>
                                <tr><td><strong>City:</strong></td><td>${file.city || 'N/A'}</td></tr>
                                <tr><td><strong>Policy Number:</strong></td><td>${file.policy_number || 'N/A'}</td></tr>
                                <tr><td><strong>Issue Date:</strong></td><td>${file.issue_date || 'N/A'}</td></tr>
                                <tr><td><strong>Expiry Date:</strong></td><td>${file.expiry_date || 'N/A'}</td></tr>
                                <tr><td><strong>Sum Insured:</strong></td><td>${file.sum_insured || 'N/A'}</td></tr>
                                <tr><td><strong>Department:</strong></td><td>${file.department || 'N/A'}</td></tr>
                                <tr><td><strong>Loss Description:</strong></td><td>${file.loss_description || 'N/A'}</td></tr>
                                <tr><td><strong>Loss Claimed:</strong></td><td>${file.loss_claimed && !isNaN(file.loss_claimed) ? parseFloat(file.loss_claimed).toLocaleString('en-US', {minimumFractionDigits: 0, maximumFractionDigits: 2}) : file.loss_claimed || 'NA'}</td></tr>
                                <tr><td><strong>Estimate Amount:</strong></td><td>${file.estimate_amount && !isNaN(file.estimate_amount) ? parseFloat(file.estimate_amount).toLocaleString('en-US', {minimumFractionDigits: 0, maximumFractionDigits: 2}) : file.estimate_amount || 'NA'}</td></tr>
                            </table>
                            <div class="action-buttons-container">
                                <button class="btn btn-sm btn-info view-btn" 
                                        data-doc="${file.doc_no}" 
                                        data-client="${file.client_name}" 
                                        data-surveyor="${file.surveyor_name}"
                                        title="View Documents">
                                    <i class="bi bi-eye-fill me-1"></i> View
                                </button>
                                <button class="btn btn-sm btn-warning revision-btn" 
                                        data-doc="${file.doc_no}" 
                                        data-client="${file.client_name}"
                                        title="PLR Revision">
                                    <i class="bi bi-arrow-repeat me-1"></i> Revision
                                </button>
                                <button class="btn btn-sm btn-reminder reminder-btn" 
                                        data-doc="${file.doc_no}" 
                                        data-client="${file.client_name}"
                                        title="Send Reminder">
                                    <i class="bi bi-bell-fill me-1"></i> Reminder
                                </button>
                                <button class="btn btn-sm btn-secondary email-log-btn" 
                                        data-doc="${file.doc_no}" 
                                        data-client="${file.client_name}"
                                        title="View Email Logs">
                                    <i class="bi bi-envelope-open me-1"></i> Logs
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            `;
        }

        // Load mobile cards
        function loadMobileCards(append = false) {
            if (!append) {
                mobileOffset = 0;
                mobileHasMore = true;
                $('#mobileCardsWrapper').empty();
                $('#mobileLoadMore').show();
            }

            if (!mobileHasMore) return;

            const filtered = applyFilters();
            const startIndex = mobileOffset;
            const endIndex = mobileOffset + mobileLimit;
            const paginatedData = filtered.slice(startIndex, endIndex);

            if (paginatedData.length > 0) {
                paginatedData.forEach(function(file) {
                    $('#mobileCardsWrapper').append(generateMobileCard(file));
                });
                
                mobileOffset += paginatedData.length;
                mobileHasMore = endIndex < filtered.length;

                if (mobileHasMore) {
                    $('#mobileLoadMore').show();
                } else {
                    $('#mobileLoadMore').hide();
                }
                
                // Update the counter in load more button
                const remaining = filtered.length - mobileOffset;
                if (remaining > 0) {
                    $('#loadMoreBtn').html(`<i class="bi bi-arrow-down-circle me-2"></i>Load More (${remaining} remaining)`);
                } else {
                    $('#loadMoreBtn').html('<i class="bi bi-arrow-down-circle me-2"></i>Load More');
                }
            } else {
                if (!append) {
                    $('#mobileCardsWrapper').html('<div class="alert alert-info text-center">No files found.</div>');
                }
                mobileHasMore = false;
                $('#mobileLoadMore').hide();
            }
        }

        // Populate surveyor options
        function populateSurveyorOptions() {
            if (!allData || allData.length === 0) {
                console.error('No data available to populate surveyors');
                return;
            }
            
            const surveyors = [...new Set(allData
                .map(item => item.surveyor_name)
                .filter(name => name && name !== 'NA' && name !== '' && name !== null && name !== 'null')
                .sort())];

            const desktopContainer = $('#desktopSurveyorOptionsContainer');
            const mobileContainer = $('#mobileSurveyorOptionsContainer');

            desktopContainer.empty();
            mobileContainer.empty();

            if (surveyors.length === 0) {
                const noData = '<div class="multi-select-option" style="text-align: center; color: #6c757d; padding: 15px;">No surveyors found</div>';
                desktopContainer.html(noData);
                mobileContainer.html(noData);
                // console.log('No surveyors to display');
                return;
            }

            surveyors.forEach((surveyor, index) => {
                const desktopOption = `
                    <div class="multi-select-option">
                        <input type="checkbox" id="desktop_surveyor_${index}" value="${surveyor}" class="desktop-surveyor-checkbox">
                        <label for="desktop_surveyor_${index}">${surveyor}</label>
                    </div>
                `;
                const mobileOption = `
                    <div class="multi-select-option">
                        <input type="checkbox" id="mobile_surveyor_${index}" value="${surveyor}" class="mobile-surveyor-checkbox">
                        <label for="mobile_surveyor_${index}">${surveyor}</label>
                    </div>
                `;
                desktopContainer.append(desktopOption);
                mobileContainer.append(mobileOption);
            });
        }

        // Initialize desktop multi-select
        function initDesktopMultiSelect() {
            const btn = $('#desktopSurveyorMultiSelectBtn');
            const dropdown = $('#desktopSurveyorMultiSelectDropdown');
            const searchInput = $('#desktopSurveyorSearchInput');

            // Toggle dropdown
            btn.off('click').on('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                
                // Close other dropdowns
                $('.multi-select-dropdown').not(dropdown).removeClass('show');
                $('.multi-select-button').not(btn).removeClass('active');
                
                // Toggle this dropdown
                const isShowing = dropdown.hasClass('show');
                
                if (isShowing) {
                    dropdown.removeClass('show');
                    btn.removeClass('active');
                } else {
                    dropdown.addClass('show');
                    btn.addClass('active');
                }
            });

            // Search functionality
            searchInput.off('input').on('input', function() {
                const searchTerm = $(this).val().toLowerCase();
                $('#desktopSurveyorOptionsContainer .multi-select-option').each(function() {
                    const text = $(this).find('label').text().toLowerCase();
                    $(this).toggle(text.includes(searchTerm));
                });
            });

            // Filter button
            $('#desktopSurveyorFilterBtn').off('click').on('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                
                const selected = [];
                $('.desktop-surveyor-checkbox:checked').each(function() {
                    selected.push($(this).val());
                });
                
                currentFilters.surveyor = selected;
                updateSurveyorButtonText('desktop', selected);
                
                dropdown.removeClass('show');
                btn.removeClass('active');
                
                refreshDesktopTable();
            });

            // Clear All button
            $('#desktopSurveyorClearAll').off('click').on('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                $('.desktop-surveyor-checkbox').prop('checked', false);
                updateSurveyorButtonText('desktop', []);
            });
        }

        // Initialize mobile multi-select
        function initMobileMultiSelect() {
            const btn = $('#mobileSurveyorMultiSelectBtn');
            const dropdown = $('#mobileSurveyorMultiSelectDropdown');
            const searchInput = $('#mobileSurveyorSearchInput');

            // Toggle dropdown
            btn.off('click').on('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                
                dropdown.toggleClass('show');
                btn.toggleClass('active');
            });

            // Search functionality
            searchInput.off('input').on('input', function() {
                const searchTerm = $(this).val().toLowerCase();
                $('#mobileSurveyorOptionsContainer .multi-select-option').each(function() {
                    const text = $(this).find('label').text().toLowerCase();
                    $(this).toggle(text.includes(searchTerm));
                });
            });

            // Filter button
            $('#mobileSurveyorFilterBtn').off('click').on('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                
                const selected = [];
                $('.mobile-surveyor-checkbox:checked').each(function() {
                    selected.push($(this).val());
                });
                
                currentFilters.surveyor = selected;
                updateSurveyorButtonText('mobile', selected);
                
                dropdown.removeClass('show');
                btn.removeClass('active');
                
                loadMobileCards(false);
            });

            // Clear All button
            $('#mobileSurveyorClearAll').off('click').on('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                $('.mobile-surveyor-checkbox').prop('checked', false);
            });
        }

        // Update surveyor button text
        function updateSurveyorButtonText(type, selected) {
            const btnText = $(`#${type}SurveyorMultiSelectBtn .multi-select-text`);
            if (selected.length === 0) {
                btnText.text('Select Surveyors');
            } else if (selected.length === 1) {
                btnText.html(`${selected[0]} <span class="multi-select-badge">1</span>`);
            } else {
                btnText.html(`${selected.length} Surveyors <span class="multi-select-badge">${selected.length}</span>`);
            }
        }

        // Function to load documents with approval remarks
        function loadDocumentsWithApprovalRemarks(docNo) {
            // console.log('Loading documents for doc:', docNo);
            
            // Clear previous content
            $('#documentTableBody').html('<tr><td colspan="4" class="text-center"><div class="spinner-border spinner-border-sm text-primary me-2"></div>Loading documents...</td></tr>');
            
            // First, load the documents
            $.ajax({
                url: '{{ url('documents') }}/' + docNo + '/files',
                method: 'GET',
                success: function(documents) {
                    // console.log('Documents loaded:', documents);
                    
                    $('#documentTableBody').empty();
                    if (!documents || documents.length === 0) {
                        $('#documentTableBody').html('<tr><td colspan="4" class="text-center">No documents uploaded yet.</td></tr>');
                    } else {
                        // Now fetch approval remarks from filestab table
                        $.ajax({
                            url: '{{ route("admin.getApprovalData") }}',
                            method: 'GET',
                            data: { doc_no: docNo },
                            success: function(approvalData) {
                                // console.log('Approval data received:', approvalData);
                                
                                if (!approvalData.success) {
                                    console.error('Failed to get approval data:', approvalData.message);
                                    showDocumentsWithDefaultRemarks(documents);
                                    return;
                                }
                                
                                // Get approval remarks from the response
                                const approvalRemarks = approvalData.app_rem || '';
                                // console.log('Approval remarks raw:', approvalRemarks);
                                
                                // Parse remarks into an array
                                let remarksArray = [];
                                if (approvalRemarks && approvalRemarks.trim() !== '') {
                                    // Split by newline first, then by comma if no newlines
                                    if (approvalRemarks.includes('\n')) {
                                        remarksArray = approvalRemarks.split('\n')
                                            .map(r => r.trim())
                                            .filter(r => r !== '');
                                    } else if (approvalRemarks.includes(',')) {
                                        remarksArray = approvalRemarks.split(',')
                                            .map(r => r.trim())
                                            .filter(r => r !== '');
                                    } else {
                                        remarksArray = [approvalRemarks.trim()];
                                    }
                                }
                                
                                // console.log('Parsed remarks:', remarksArray);
                                
                                // Display documents with remarks
                                displayDocumentsSimple(documents, remarksArray);
                            },
                            error: function(xhr, status, error) {
                                console.error('Error loading approval data:', error);
                                // console.log('XHR status:', status);
                                // console.log('XHR response:', xhr.responseText);
                                
                                // Show documents with default remarks
                                showDocumentsWithDefaultRemarks(documents);
                            }
                        });
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Error loading documents:', error);
                    $('#documentTableBody').html('<tr><td colspan="4" class="text-center text-danger">Error loading documents. Please try again.</td></tr>');
                }
            });
        }

        function displayDocumentsSimple(documents, remarksArray) {
            // console.log('Displaying documents with simple remarks:', remarksArray.length, 'remarks');
            
            if (remarksArray.length === 0) {
                // No remarks available
                documents.forEach(function(doc, index) {
                    $('#documentTableBody').append(
                        '<tr>' +
                        '<td class="align-middle">' + (doc.date ?? '-') + '</td>' +
                        '<td class="align-middle">' + (doc.remarks ?? '-') + '</td>' +
                        '<td class="align-middle"><span class="text-muted">No approval remarks</span></td>' +
                        '<td class="align-middle">' +
                        '<a href="' + doc.url + '" target="_blank" class="btn btn-sm text-white me-2 rounded-pill" style="background-color: #000088;">' +
                        '<i class="bi bi-box-arrow-up-right me-1"></i>Open</a>' +
                        '<a href="' + doc.url + '" download class="btn btn-sm text-white me-2 rounded-pill" style="background-color: #28A745;">' +
                        '<i class="bi bi-download me-1"></i>Download</a>' +
                        '</td>' +
                        '</tr>'
                    );
                });
            } else {
                // We have remarks - show them chronologically
                documents.forEach(function(doc, index) {
                    let remarkToShow = '';
                    
                    // Show the latest remark first, then previous ones
                    if (remarksArray.length > 0) {
                        // For the first row, show the latest remark
                        if (index === 0) {
                            remarkToShow = remarksArray[remarksArray.length - 1];
                        } 
                        // For subsequent rows, show previous remarks in reverse chronological order
                        else if (index <= remarksArray.length) {
                            const remarkIndex = remarksArray.length - index - 1;
                            if (remarkIndex >= 0) {
                                remarkToShow = remarksArray[remarkIndex];
                            }
                        }
                    }
                    
                    // Extract timestamp if present for cleaner display
                    let displayRemark = remarkToShow;
                    if (displayRemark) {
                        // Remove timestamp brackets for cleaner display
                        displayRemark = displayRemark.replace(/^\[(.*?)\]\s*/, '');
                        
                        // Truncate if too long
                        if (displayRemark.length > 50) {
                            displayRemark = displayRemark.substring(0, 50) + '...';
                        }
                    }
                    
                    $('#documentTableBody').append(
                        '<tr>' +
                        '<td class="align-middle">' + (doc.date ?? '-') + '</td>' +
                        '<td class="align-middle">' + (doc.remarks ?? '-') + '</td>' +
                        '<td class="align-middle">' +
                        (remarkToShow ? 
                            '<span class="approval-remark-simple" title="' + remarkToShow.replace(/"/g, '&quot;') + '">' + 
                            displayRemark + 
                            '</span>' : 
                            '<span class="text-muted">-</span>'
                        ) +
                        '</td>' +
                        '<td class="align-middle">' +
                        '<a href="' + doc.url + '" target="_blank" class="btn btn-sm text-white me-2 rounded-pill" style="background-color: #000088;">' +
                        '<i class="bi bi-box-arrow-up-right me-1"></i>Open</a>' +
                        '<a href="' + doc.url + '" download class="btn btn-sm text-white me-2 rounded-pill" style="background-color: #28A745;">' +
                        '<i class="bi bi-download me-1"></i>Download</a>' +
                        '</td>' +
                        '</tr>'
                    );
                });
            }
        }

        // Helper function to show documents with default remarks
        function showDocumentsWithDefaultRemarks(documents) {
            // console.log('Showing documents with default remarks');
            
            documents.forEach(function(doc, index) {
                $('#documentTableBody').append(
                    '<tr>' +
                    '<td class="align-middle">' + (doc.date ?? '-') + '</td>' +
                    '<td class="align-middle">' + (doc.remarks ?? '-') + '</td>' +
                    '<td class="align-middle"><span class="text-muted">No remarks available</span></td>' +
                    '<td class="align-middle">' +
                    '<a href="' + doc.url + '" target="_blank" class="btn btn-sm text-white me-2 rounded-pill" style="background-color: #000088;">' +
                    '<i class="bi bi-box-arrow-up-right me-1"></i>Open</a>' +
                    '<a href="' + doc.url + '" download class="btn btn-sm text-white me-2 rounded-pill" style="background-color: #28A745;">' +
                    '<i class="bi bi-download me-1"></i>Download</a>' +
                    '</td>' +
                    '</tr>'
                );
            });
        }

       
       
//  loading approval status
function loadApprovalStatus(docNo) {
    // console.log('=== LOADING APPROVAL STATUS FOR DOCUMENT:', docNo, '===');
    
    $.ajax({
        url: '{{ route("admin.getApprovalData") }}',
        method: 'GET',
        data: { doc_no: docNo },
        success: function(response) {
            console.log('Approval data response:', response);
            
            if (response.success) {
                $('#currentDocNo').val(docNo);
                
                let hasUploads = response.has_uploads;
                let currentStatus = response.plr_final;
                let reportType = response.rep_tag;
                let isInitialState = response.is_initial_state;
                
                // console.log('Document:', docNo);
                // console.log('Has uploads:', hasUploads);
                // console.log('Current plr_final:', currentStatus, 'type:', typeof currentStatus);
                // console.log('Report type:', reportType);
                
                // CRITICAL: If no uploads, HIDE both buttons
                if (!hasUploads) {
                    console.log('No uploads - HIDING both buttons');
                    $('#currentApprovalStatus').text('Status: No Documents Uploaded');
                    $('#modalApproveBtn').hide();
                    $('#modalUnapproveBtn').hide();
                    $('#saveRemarksBtn').hide();  // Also hide save remarks button
                    $('#approvalRemarks').prop('disabled', true);
                    return;
                }
                
                // Show save remarks button and enable textarea since there are uploads
                $('#saveRemarksBtn').show();
                $('#approvalRemarks').prop('disabled', false);
                
                // Determine which buttons to show based on status
                if (currentStatus === 'Y') {
                    // Already approved - show only Unapprove button
                    console.log('Case: Approved - showing Unapprove only');
                    $('#currentApprovalStatus').text('Status: Approved');
                    $('#modalApproveBtn').hide();
                    $('#modalUnapproveBtn').show();
                    
                } else if (currentStatus === 'N') {
                    // Unapproved (after admin action) - show only Approve button
                    console.log('Case: Unapproved (after admin action) - showing Approve only');
                    $('#currentApprovalStatus').text('Status: Unapproved');
                    $('#modalApproveBtn').show();
                    $('#modalUnapproveBtn').hide();
                    
                } else if (currentStatus === null || isInitialState) {
                    // Initial state (first upload, no admin decision yet) - show BOTH buttons
                    console.log('Case: Initial state - showing BOTH buttons');
                    $('#currentApprovalStatus').text('Status: Pending Review');
                    $('#modalApproveBtn').show();
                    $('#modalUnapproveBtn').show();
                    
                } else if (currentStatus === 'I' || currentStatus === 'R' || currentStatus === 'V') {
                    // In Process, Reminder Sent, Revision Requested - show BOTH buttons
                    let statusText = '';
                    if (currentStatus === 'I') statusText = 'In Process';
                    else if (currentStatus === 'R') statusText = 'Reminder Sent';
                    else if (currentStatus === 'V') statusText = 'Revision Requested';
                    
                    console.log('Case: ' + statusText + ' - showing BOTH buttons');
                    $('#currentApprovalStatus').text('Status: ' + statusText);
                    $('#modalApproveBtn').show();
                    $('#modalUnapproveBtn').show();
                    
                } else {
                    // Fallback - show both buttons
                    console.log('Case: Fallback - showing BOTH buttons');
                    $('#currentApprovalStatus').text('Status: ' + (currentStatus || 'Pending Review'));
                    $('#modalApproveBtn').show();
                    $('#modalUnapproveBtn').show();
                }
                
                $('#approvalRemarks').val('');
            } else {
                console.log('Response not successful:', response.message);
                // On error, hide both buttons for safety
                $('#modalApproveBtn').hide();
                $('#modalUnapproveBtn').hide();
                $('#saveRemarksBtn').hide();
                $('#currentApprovalStatus').text('Status: Unable to load data');
            }
        },
        error: function(xhr, status, error) {
            console.error('Error loading approval data:', error);
            // On error, hide both buttons for safety
            $('#modalApproveBtn').hide();
            $('#modalUnapproveBtn').hide();
            $('#saveRemarksBtn').hide();
            $('#currentApprovalStatus').text('Status: Error loading data');
        }
    });
}
        // APPROVE PLR FUNCTION
function approvePLR(docNo) {
    // Find the file data first to get report type
    const fileIndex = allData.findIndex(item => item.doc_no === docNo);
    if (fileIndex === -1) {
        showToast('File not found!', 'error');
        return;
    }
    
    const fileData = allData[fileIndex];
    const reportType = fileData.rep_tag === 'F/R' ? 'Final Report' : 'Preliminary Report';
    
    // Show initial toast
    showToast(`Processing ${reportType} approval...`, 'info');
    
    $.ajax({
        url: '{{ route("documents.approve", ["doc" => "DOCNO"]) }}'.replace('DOCNO', docNo),
        type: 'POST',
        data: { 
            _token: '{{ csrf_token() }}'
        },
        success: function(response) {
            if (response.success) {
                // Update status to 'Y' (Approved)
                allData[fileIndex].plr_final = 'Y';
                
                // Log status change
                logStatusChangeToDatabase(docNo, 'Approved', reportType);
                
                // Show SUCCESS toast
                setTimeout(() => {
                    showToast(`${reportType} approved for ${docNo}`, 'success');
                }, 100);
                
                // Refresh ALL views immediately
                updateMainStats();
                updateAppointmentStats();
                
                if (table) {
                    const filtered = applyFilters();
                    table.clear();
                    table.rows.add(filtered);
                    table.draw();
                }
                
                if (isMobile) {
                    mobileOffset = 0;
                    mobileHasMore = true;
                    loadMobileCards(false);
                }
                
                // Update approval status in view modal if open
                if ($('#viewReportModal').hasClass('show')) {
                    $('#currentApprovalStatus').text('Status: Approved');
                    $('#modalApproveBtn').hide();  // Hide Approve button
                    $('#modalUnapproveBtn').show(); // Show Unapprove button
                }
            } else {
                setTimeout(() => {
                    showToast('Failed: ' + response.message, 'error');
                }, 100);
            }
        },
        error: function(xhr, status, error) {
            console.error('Approve PLR Error:', xhr.responseText);
            setTimeout(() => {
                showToast('Server error. Failed to approve.', 'error');
            }, 100);
        }
    });
}

// UNAPPROVE PLR FUNCTION
function unapprovePLR(docNo) {
    if (!confirm('Are you sure you want to unapprove this report?')) return;

    $.ajax({
        url: '{{ route("admin.unapprove-plr") }}',
        type: 'POST',
        data: {
            _token: '{{ csrf_token() }}',
            doc_no: docNo
        },
        beforeSend: function() {
            showToast('Processing unapproval...', 'info');
        },
        success: function(response) {
            if (response.success) {
                // Find the file in allData array
                const fileIndex = allData.findIndex(item => item.doc_no === docNo);
                if (fileIndex !== -1) {
                    // Update status to 'N' (Unapproved)
                    allData[fileIndex].plr_final = 'N';
                }
                
                // Try to log status change, but don't let it break the flow
                try {
                    logStatusChangeToDatabase(docNo, 'Unapproved', 'Report');
                } catch (error) {
                    console.warn('Failed to log status change:', error);
                    // Continue with the rest of the success flow
                }
                
                showToast('Report unapproved for ' + docNo, 'success');
                
                // Refresh ALL views immediately
                updateMainStats();
                updateAppointmentStats();
                
                if (table) {
                    const filtered = applyFilters();
                    table.clear();
                    table.rows.add(filtered);
                    table.draw();
                }
                
                if (isMobile) {
                    mobileOffset = 0;
                    mobileHasMore = true;
                    loadMobileCards(false);
                }
                
                // Update approval status in view modal if open
                if ($('#viewReportModal').hasClass('show')) {
                    $('#currentApprovalStatus').text('Status: Unapproved');
                    $('#modalApproveBtn').show();   // Show Approve button
                    $('#modalUnapproveBtn').hide();  // Hide Unapprove button
                }
            } else {
                showToast('Failed: ' + response.message, 'error');
            }
        },
        error: function(xhr, status, error) {
            console.error('Unapprove error:', xhr.responseText);
            showToast('Server error. Failed to unapprove.', 'error');
        }
    });
}

        // Mark document as In Process with action type
        function markAsInProcess(docNo, action = 'I') {
            $.ajax({
                url: '{{ route("admin.mark-in-process") }}',
                type: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    doc_no: docNo,
                    action: action
                },
                beforeSend: function() {
                    showToast('Updating status...', 'info');
                },
                success: function(response) {
                    if (response.success) {
                        const reportType = response.report_type === 'P/R' ? 'Preliminary Report' : 'Final Report';
                        const statusName = action === 'R' ? 'Reminder Sent' : 
                                         action === 'V' ? 'Revision Requested' : 'In Process';
                         logStatusChangeToDatabase(docNo, statusName, reportType);
                        showToast(`${statusName} for ${reportType}`, 'success');
                        
                        // Update the specific item in allData array
                        const fileIndex = allData.findIndex(item => item.doc_no === docNo);
                        if (fileIndex !== -1) {
                            allData[fileIndex].plr_final = action;
                        }
                        
                        // Refresh ALL views immediately
                        updateMainStats();
                        updateAppointmentStats();
                        
                        if (table) {
                            const filtered = applyFilters();
                            table.clear();
                            table.rows.add(filtered);
                            table.draw();
                        }
                        
                        if (isMobile) {
                            mobileOffset = 0;
                            mobileHasMore = true;
                            loadMobileCards(false);
                        }
                        
                        // Update approval status in view modal if open
                        if ($('#viewReportModal').hasClass('show')) {
                            if (action === 'R') {
                                $('#currentApprovalStatus').text('Status: Reminder Sent');
                            } else if (action === 'V') {
                                $('#currentApprovalStatus').text('Status: Revision Requested');
                            } else if (action === 'I') {
                                $('#currentApprovalStatus').text('Status: In Process');
                            }
                        }
                    } else {
                        showToast('Failed to update status: ' + response.message, 'error');
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Mark as In Process error:', xhr.responseText);
                    showToast('Email Sent Successfully!', 'success');
                }
            });
        }

        // Toast notification function
        function showToast(message, type) {
            var bgColor = type === 'success' ? '#28a745' : type === 'error' ? '#dc3545' : type === 'info' ? '#17a2b8' : '#6c757d';
            var icon = type === 'success' ? 'fa-check-circle' : type === 'error' ? 'fa-exclamation-circle' : 'fa-info-circle';
            var toast = $('<div class="custom-toast" style="position: fixed; top: 20px; right: 20px; background: ' + bgColor +
                '; color: white; padding: 12px 20px; border-radius: 6px; box-shadow: 0 4px 12px rgba(0,0,0,0.3); z-index: 9999; font-size: 14px; font-weight: 500; animation: slideIn 0.3s;"><i class="fas ' +
                icon + ' me-2"></i>' + message + '</div>');
            $('body').append(toast);
            setTimeout(function() {
                toast.fadeOut(300, function() {
                    $(this).remove();
                });
            }, 3000);
        }

        // Toast animation styles
        if (!$('#customToastStyles').length) {
            $('<style id="customToastStyles">@keyframes slideIn { from { transform: translateX(100%); opacity: 0; } to { transform: translateX(0); opacity: 1; } }</style>').appendTo('head');
        }

        // View button handler
        $(document).on('click', '.view-btn', function(e) {
            e.stopPropagation();
            const docNo = $(this).data('doc');
            currentDocNo = docNo; // Set the global variable
            
            currentClaimData = {
                doc_no: docNo,
                client_name: $(this).data('client'),
                surveyor_name: $(this).data('surveyor')
            };
            
            // Clear modal fields first
            $('#modalClaimNo').text('Claim: ' + docNo);
            $('#modalInsuredName').text('Insured: ' + currentClaimData.client_name);
            $('#modalSurveyorName').text('Surveyor: ' + currentClaimData.surveyor_name);
            
            // Clear the approval remarks textarea
            $('#approvalRemarks').val('');
            
            $('#documentTableBody').html('<tr><td colspan="4">Loading...</td></tr>');
            $('#docError').hide();
            
            // Show modal first
            $('#viewReportModal').modal('show');
            
            // Set the download button data-doc attribute
            $('#downloadAllZip').data('doc', docNo);
            
            // Then load data
            loadDocumentsWithApprovalRemarks(docNo);
            loadApprovalStatus(docNo);
        });

        // Clear the approval remarks textarea when modal opens
        $('#viewReportModal').on('show.bs.modal', function() {
            // Clear the "Add New Approval Remarks" textarea
            $('#approvalRemarks').val('');
        });

        // Revision button handler - UPDATED WITH EMAIL MATCHING
        $(document).on('click', '.revision-btn', function(e) {
            e.stopPropagation();
            const docNo = $(this).data('doc');
            const clientName = $(this).data('client') || 'Unknown Client';
            
            // Find the file data from allData array
            const fileData = allData.find(item => item.doc_no === docNo);
            
            if (fileData) {
                // Get surveyor code from file data
                const surveyorCode = fileData.surveyor_code;
                // console.log('Revision - Surveyor Code:', surveyorCode);
                // console.log('File Data:', fileData);
                
                // Get email from users table using helper function
                let surveyorEmail = getEmailBySurveyorCode(surveyorCode);
                
                if (!surveyorEmail) {
                  
                    
                }
                
                // Set document number
                $('#uw_doc').val(docNo);
                
                // Set receiver (surveyor email from users table)
                $('#receiver').val(surveyorEmail);
                // console.log('Receiver field set to:', surveyorEmail);
                
                // Set subject with Insured name and document number
                $('#sub').val('PLR Revision Required - ' + clientName + ' (Document #' + docNo + ')');
                
                // Clear CC field
                $('#revision_cc').val('');
                
                // Clear body
                $('#body').val('');
                
                // Show modal
                $('#revisionModal').modal('show');
                
                // console.log('Modal opened with:', {
                //     docNo: docNo,
                //     receiver: surveyorEmail,
                //     surveyorCode: surveyorCode
                // });
            } else {
                showToast('File data not found', 'error');
            }
        });

        // Reminder button handler - UPDATED WITH EMAIL MATCHING
        $(document).on('click', '.reminder-btn', function(e) {
            e.stopPropagation();
            const docNo = $(this).data('doc');
            const clientName = $(this).data('client') || 'Unknown Client';
            
            // Find the file data from allData array
            const fileData = allData.find(item => item.doc_no === docNo);
            
            let surveyorEmail = '';
            let surveyorName = 'Surveyor';
            
            if (fileData) {
                // Get surveyor code from file data
                const surveyorCode = fileData.surveyor_code;
                // console.log('Reminder - Surveyor Code:', surveyorCode);
                
                // Get email from users table
                surveyorEmail = getEmailBySurveyorCode(surveyorCode);
                surveyorName = fileData.surveyor_name || 'Surveyor';
                
                if (!surveyorEmail) {
                    // console.warn('No email found for surveyor code:', surveyorCode);
                }
            }
            
            if (!surveyorEmail) {
                // showToast('Warning: No email found for this surveyor. Please add email manually.', 'warning');
            }
            
            $('#reminder_uw_doc').val(docNo);
            $('#clientName').text(surveyorName);
            
            // Set subject with Insured name
            $('#reminder_sub').val('Reminder: Pending Report Submission for ' + clientName + ' (Document #' + docNo + ')');
            
            // Reset and set fresh reminder message with Insured name
            $('#reminder_body').val(`Dear Surveyor,

This is a friendly reminder regarding your pending report submission for ${clientName} (Document No: ${docNo}).

We kindly request you to submit the required documents at your earliest convenience to avoid any delays in processing.

If you have already submitted the documents, please disregard this message. For any queries, please feel free to contact us.

Thank you for your cooperation.`);
            
            // Set receiver email
            $('#receiver_email').val(surveyorEmail);
            
            $('#reminderModal').modal('show');
        });

        // Reminder Modal show event - UPDATED
        $('#reminderModal').on('show.bs.modal', function() {
            // Get current values
            const docNo = $('#reminder_uw_doc').val();
            
            if (docNo) {
                // Find file data
                const fileData = allData.find(item => item.doc_no === docNo);
                if (fileData) {
                    // Get email from users table
                    const surveyorEmail = getEmailBySurveyorCode(fileData.surveyor_code);
                    const surveyorName = fileData.surveyor_name || 'Surveyor';
                    
                    // Set email
                    $('#receiver_email').val(surveyorEmail);
                    
                    // Set client name
                    $('#clientName').text(surveyorName);
                    
                    // Set subject
                    const clientName = fileData.client_name || 'Unknown Client';
                    $('#reminder_sub').val('Reminder: Pending Report Submission for ' + clientName + ' (Document #' + docNo + ')');
                    
                    // Set reminder message
                    $('#reminder_body').val(`Dear Surveyor,

This is a friendly reminder regarding your pending report submission for ${clientName} (Document No: ${docNo}).

We kindly request you to submit the required documents at your earliest convenience to avoid any delays in processing.

If you have already submitted the documents, please disregard this message. For any queries, please feel free to contact us.

Thank you for your cooperation.`);
                }
            }
            
            // Set default sender
            $('#reminder_sender').val('{{ $userEmail }}');
        });

        // Revision Modal show event - UPDATED
        $('#revisionModal').on('show.bs.modal', function() {
            // Get current values
            const docNo = $('#uw_doc').val();
            
            if (docNo) {
                // Re-lookup email in case it wasn't set properly
                const fileData = allData.find(item => item.doc_no === docNo);
                if (fileData) {
                    // Get email from users table
                    const surveyorEmail = getEmailBySurveyorCode(fileData.surveyor_code);
                    
                    if (surveyorEmail) {
                        $('#receiver').val(surveyorEmail);
                        // console.log('Modal show - email set:', surveyorEmail);
                    }
                    
                    // Set subject with Insured name
                    const clientName = fileData.client_name || 'Unknown Client';
                    $('#sub').val('PLR Revision Required - ' + clientName + ' (Document #' + docNo + ')');
                }
            }
            
            // Set default sender
            $('#sender').val('{{ $userEmail }}');
        });

        // Send Revision Email
        $('#sendRevisionBtn').click(function() {
            if (!$('#sender').val() || !$('#receiver').val() || !$('#sub').val() || !$('#body').val()) {
                showToast('Please fill in all required fields.', 'error');
                return;
            }

            var $btn = $(this);
            $btn.prop('disabled', true).html('<div class="spinner-border spinner-border-sm me-1"></div>Sending...');

            // Get all form data including CC
            var formData = $('#revisionEmailForm').serialize();
            var docNo = $('#uw_doc').val();

            $.ajax({
                url: '/Surveyor/documents/' + docNo + '/send-revision',
                method: 'POST',
                data: formData,
                success: function(response) {
                    // console.log('Revision response:', response);
                    if (response.success) {
                        logEmailToDatabase({
                            uw_doc: docNo,
                            sender: $('#sender').val(),
                            receiver: $('#receiver').val(),
                            cc_email: $('#revision_cc').val(),
                            sub: $('#sub').val(),
                            route: 'PLR Revision',
                            body: $('#body').val()
                        });

                        $('#revisionModal').modal('hide');
                        $('#revisionEmailForm')[0].reset();
                        
                        // Get report type for message
                        const fileData = allData.find(item => item.doc_no === docNo);
                        const reportType = fileData.rep_tag === 'F/R' ? 'Final Report' : 'Preliminary Report';
                        showToast(`Revision requested for ${reportType}`, 'success');
                        
                        // AUTO-CHANGE STATUS: Set to 'V' (Revision Requested)
                        markAsInProcess(docNo, 'V');
                    } else {
                        showToast('Failed to send revision email: ' + (response.message || ''), 'error');
                    }
                },
                error: function(xhr) {
                    console.error('Revision error:', xhr.responseText);
                    showToast('Failed to send revision email: ' + (xhr.responseJSON?.message || 'Server error'), 'error');
                },
                complete: function() {
                    $btn.prop('disabled', false).html('<i class="bi bi-envelope-paper me-1"></i> Send Email');
                }
            });
        });

        // Send Reminder
        $('#sendReminderBtn').on('click', function() {
            if (!$('#reminderEmailForm')[0].checkValidity()) {
                $('#reminderEmailForm')[0].reportValidity();
                return;
            }

            var $btn = $(this);
            $btn.prop('disabled', true).find('span').text('Sending...');

            // Get form data including CC field
            var formData = $('#reminderEmailForm').serialize();
            var docNo = $('#reminder_uw_doc').val();
            
            $.ajax({
                url: $('#reminderEmailForm').attr('action'),
                method: 'POST',
                data: formData,
                success: (response) => {
                    // console.log('Reminder response:', response);
                    
                    // Get report type for message
                    const fileData = allData.find(item => item.doc_no === docNo);
                    const reportType = fileData.rep_tag === 'F/R' ? 'Final Report' : 'Preliminary Report';
                    
                    $('#reminderMessage').removeClass('alert-danger').addClass('alert alert-success').html(
                        `<i class="bi bi-check-circle me-2"></i>Reminder sent for ${reportType}!`
                    ).show();
                    
                    // Reset the form for next time
                    $('#reminderEmailForm')[0].reset();
                    
                    // AUTO-CHANGE STATUS: Set to 'R' (Reminder Sent)
                    markAsInProcess(docNo, 'R');
                    
                    setTimeout(() => {
                        $('#reminderModal').modal('hide');
                        $('#reminderMessage').hide().empty();
                    }, 2000);
                },
                error: (xhr) => {
                    console.error('Reminder error:', xhr.responseText);
                    $('#reminderMessage').removeClass('alert-success').addClass('alert alert-danger').html(
                        '<i class="bi bi-exclamation-triangle me-2"></i>' + (xhr.responseJSON?.message || 'Failed to send reminder')
                    ).show();
                },
                complete: () => {
                    $btn.prop('disabled', false).find('span').text('Send Reminder');
                }
            });
        });

        // Mobile filter handlers
        $('#mobilePlrFilter, #mobileDeptFilter').on('change', function() {
            currentFilters.plr = $('#mobilePlrFilter').val();
            currentFilters.dept = $('#mobileDeptFilter').val();
            loadMobileCards(false);
        });

        $('#mobileSearch').on('input', function() {
            clearTimeout(window.mobileSearchTimeout);
            window.mobileSearchTimeout = setTimeout(() => {
                currentFilters.search = $(this).val();
                loadMobileCards(false);
            }, 500);
        });

        // Load more button
        $('#loadMoreBtn').on('click', function() {
            $(this).html('<span class="spinner-border spinner-border-sm me-2"></span>Loading...').prop('disabled', true);
            loadMobileCards(true);
            setTimeout(() => {
                $(this).html('<i class="bi bi-arrow-down-circle me-2"></i>Load More').prop('disabled', false);
            }, 500);
        });

        // Day cards click handler
        $('#appointmentStats').on('click', '.stat-card:not(.stat-card-reset)', function() {
            var selectedDays = $(this).data('days');
            $('.stat-card').removeClass('active');
            $(this).addClass('active');

            currentFilters.days = selectedDays;

            if (isMobile) {
                loadMobileCards(false);
            } else {
                refreshDesktopTable();
            }

            showToast('Showing records for ' + $(this).find('.label').text(), 'info');
        });

        // Reset card handler
        $('#resetCard').on('click', function() {
            $('.stat-card').removeClass('active');
            
            currentFilters = {
                plr: 'all',
                dept: '',
                surveyor: [],
                search: '',
                days: ''
            };

            if (isMobile) {
                $('#mobilePlrFilter').val('all');
                $('#mobileDeptFilter').val('');
                $('.mobile-surveyor-checkbox').prop('checked', false);
                $('#mobileSearch').val('');
                $('#mobileSurveyorMultiSelectBtn .multi-select-text').text('Select Surveyors');
                loadMobileCards(false);
            } else {
                $('#plrFilter').val('all');
                $('#deptFilter').val('');
                $('.desktop-surveyor-checkbox').prop('checked', false);
                $('#desktopSurveyorMultiSelectBtn .multi-select-text').text('Select Surveyors');
                if (table) {
                    table.search('').draw();
                }
                refreshDesktopTable();
            }

            showToast('Filters reset', 'success');
        });

        // Close dropdowns when clicking outside
        $(document).on('click', function(e) {
            if (!$(e.target).closest('.multi-select-wrapper').length) {
                $('.multi-select-dropdown').removeClass('show');
                $('.multi-select-button').removeClass('active');
            }
        });

        // Download All ZIP handler
        $('#downloadAllZip').on('click', function() {
            const docNo = $(this).data('doc');
            if (!docNo) {
                showToast('No document selected or missing document number.', 'error');
                return;
            }

            const $btn = $(this);
            $btn.prop('disabled', true).html('<i class="bi bi-hourglass-split me-1"></i> Processing...');

            $.ajax({
                url: '{{ url('documents') }}/' + docNo + '/download-zip',
                method: 'GET',
                xhrFields: {
                    responseType: 'blob'
                },
                success: function(blob) {
                    if (blob.size === 0) {
                        showToast('No files found for this document.', 'error');
                        return;
                    }

                    const link = document.createElement('a');
                    link.href = window.URL.createObjectURL(blob);
                    link.download = 'documents_' + docNo + '.zip';
                    link.click();
                    showToast('ZIP downloaded successfully!', 'success');
                },
                error: function(xhr) {
                    const msg = xhr.responseJSON?.error || 'Failed to download ZIP.';
                    showToast(msg, 'error');
                },
                complete: function() {
                    $btn.prop('disabled', false).html('<i class="bi bi-file-zip me-1"></i> Download All as ZIP');
                }
            });
        });

        // Modal Approve Button
        $('#modalApproveBtn').on('click', function() {
            const docNo = $('#currentDocNo').val();
            if (docNo) {
                approvePLR(docNo);
            } else {
                showToast('No document selected', 'error');
            }
        });

        // Modal Unapprove Button
        $('#modalUnapproveBtn').on('click', function() {
            const docNo = $('#currentDocNo').val();
            if (docNo) {
                unapprovePLR(docNo);
            } else {
                showToast('No document selected', 'error');
            }
        });

        // Save Approval Remarks Button
        $('#saveRemarksBtn').on('click', function() {
            const docNo = $('#currentDocNo').val();
            const app_rem = $('#approvalRemarks').val().trim();
            
            if (!docNo) {
                showToast('No document selected', 'error');
                return;
            }
            
            if (!app_rem) {
                showToast('Please enter approval remarks', 'error');
                return;
            }
            
            const $btn = $(this);
            $btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1"></span> Saving...');
            
            $.ajax({
                url: '{{ route("admin.saveRemarks") }}',
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    uw_doc: docNo,
                    app_rem: app_rem
                },
                success: function(response) {
                    // console.log('Save response:', response);
                    
                    if (response.success) {
                        logStatusChangeToDatabase(docNo, 'Remarks Added', 'All Reports', app_rem);
                        showToast('Approval remarks saved successfully!', 'success');
                        
                        // IMPORTANT: Clear the textarea after saving
                        $('#approvalRemarks').val('');
                        
                        // IMMEDIATELY refresh the approval remarks display
                        if (currentDocNo) {
                            loadDocumentsWithApprovalRemarks(currentDocNo);
                        }
                        
                        // Also refresh the approval status
                        loadApprovalStatus(docNo);
                        
                    } else {
                        showToast('Failed to save remarks: ' + (response.message || 'Unknown error'), 'error');
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Save error:', xhr.responseText);
                    let errorMsg = 'Error saving remarks';
                    try {
                        const response = JSON.parse(xhr.responseText);
                        errorMsg = response.message || errorMsg;
                    } catch (e) {
                        errorMsg = xhr.statusText || errorMsg;
                    }
                    showToast(errorMsg, 'error');
                },
                complete: function() {
                    $btn.prop('disabled', false).html('<i class="bi bi-save me-1"></i> Save Remarks');
                }
            });
        });

        // Email Log Button Handler
        $(document).on('click', '.email-log-btn', function(e) {
            e.stopPropagation();
            const docNo = $(this).data('doc');
            const clientName = $(this).data('client') || 'Unknown Client';
            
            // Set panel title and document info
            $('#panelDocumentTitle').text('Document Logs - ' + docNo);
            $('#panelDocNo').text(docNo);
            $('#panelClientName').text(clientName);
            $('#panelLogCount').text('0').removeClass('bg-success bg-warning bg-danger');
            
            // Clear previous logs
            $('#emailLogsTableBody').empty();
            $('#emptyState').hide();
            $('#loadingSpinner').show();
            
            // Open the panel
            $('#emailLogPanel').addClass('open');
            
            // Close the all logs panel if open
            $('#allEmailLogPanel').removeClass('open');
            
            // Load document-specific logs
            loadDocumentEmailLogs(docNo);
        });

        // Function to load document specific email logs
        function loadDocumentEmailLogs(docNo) {
            // console.log('Loading document logs for:', docNo);
            
            const url = '/Surveyor/email-logs/' + docNo;
            
            $.ajax({
                url: url,
                method: 'GET',
                success: function(response) {
                    // console.log('Response received:', response);
                    
                    $('#loadingSpinner').hide();
                    
                    // Update document info card
                    $('#panelDocNo').text(docNo);
                    
                    // Get client name from current data
                    const fileData = allData.find(item => item.doc_no === docNo);
                    if (fileData) {
                        $('#panelClientName').text(fileData.client_name || 'Unknown Client');
                    }
                    
                    if (response.success && response.logs && response.logs.length > 0) {
                        // console.log('Found logs:', response.logs.length);
                        
                        // Update log count
                        const logCount = response.logs.length;
                        $('#panelLogCount').text(logCount).addClass('bg-primary');
                        
                        // Clear previous rows
                        $('#emailLogsTableBody').empty();
                        
                        // Update table headers
                        $('#emailLogsTable thead tr').html(`
                            <th>SR#</th>
                            <th>Date & Time</th>
                            <th>Activity Type</th>
                            <th>Report Type</th>
                            <th>Status</th>
                            <th>From</th>
                            <th>To</th>
                            <th>Details & Sur_Remarks</th>
                        `);
                        
                        // Populate logs table
                        response.logs.forEach((log, index) => {
                            // Determine route badge class
                            let routeClass = 'route-other';
                            let routeText = log.route || 'Activity';
                            let icon = '';
                            
                            if (log.type === 'current_status') {
                                routeClass = 'route-statuschange';
                                routeText = 'Current Status';
                                icon = '<i class="bi bi-info-circle me-1"></i>';
                            } else if (log.type === 'email') {
                                if (log.route.includes('Reminder')) {
                                    routeClass = 'route-reminder';
                                    routeText = 'Reminder';
                                    icon = '<i class="bi bi-bell me-1"></i>';
                                } else if (log.route.includes('Revision')) {
                                    routeClass = 'route-plrrevision';
                                    routeText = 'Revision';
                                    icon = '<i class="bi bi-arrow-repeat me-1"></i>';
                                } else if (log.route.includes('Approval')) {
                                    routeClass = 'route-approval';
                                    routeText = 'Approval';
                                    icon = '<i class="bi bi-check-circle me-1"></i>';
                                } else {
                                    routeClass = 'route-email';
                                    routeText = 'Email';
                                    icon = '<i class="bi bi-envelope me-1"></i>';
                                }
                            }
                            
                            // Format sender/receiver
                            let sender = log.sender || 'System';
                            let receiver = log.receiver || 'System';
                            
                            // Get report type
                            let repTag = log.rep_tag_display || '-';
                            
                            // Get PLR final status
                            let plrFinalDisplay = '-';
                            if (log.plr_final) {
                                const plrFinal = log.plr_final;
                                if (plrFinal === 'Y') {
                                    plrFinalDisplay = '<span class="badge bg-success">Approved</span>';
                                } else if (plrFinal === 'N') {
                                    plrFinalDisplay = '<span class="badge bg-danger">Pending</span>';
                                } else if (plrFinal === 'I') {
                                    plrFinalDisplay = '<span class="badge bg-primary">In Process</span>';
                                } else if (plrFinal === 'R') {
                                    plrFinalDisplay = '<span class="badge bg-warning">Reminder Sent</span>';
                                } else if (plrFinal === 'V') {
                                    plrFinalDisplay = '<span class="badge bg-info">Revision Requested</span>';
                                } else {
                                    plrFinalDisplay = plrFinal;
                                }
                            } else if (log.type === 'current_status' && !log.plr_final) {
                                plrFinalDisplay = '<span class="badge bg-secondary">Not Reviewed</span>';
                            }
                            
                            // Determine display text
                            let displayText = log.body || log.sub || 'No details available';
                            displayText = displayText.trim();
                            
                            // Clean up display
                            if (displayText.includes('(Status:')) {
                                displayText = displayText.replace('(Status:', '<br><small class="text-muted">Status:').replace(')', '</small>');
                            }
                            
                            const row = `
                                <tr>
                                    <td>${index + 1}</td>
                                    <td>${formatDate(log.curdatetime)}</td>
                                    <td><span class="route-badge ${routeClass}">${icon}${routeText}</span></td>
                                    <td>${repTag}</td>
                                    <td>${plrFinalDisplay}</td>
                                    <td>${truncateText(sender, 15)}</td>
                                    <td>${truncateText(receiver, 15)}</td>
                                    <td title="${escapeHtml(displayText.replace(/<br>/g, '\n').replace(/<small[^>]*>|<\/small>/g, ''))}">
                                        <div style="max-width: 250px;">${displayText}</div>
                                    </td>
                                </tr>
                            `;
                            $('#emailLogsTableBody').append(row);
                        });
                        
                        // Show info message
                        $('#emptyState').hide();
                        
                    } else {
                        // console.log('No logs found');
                        $('#emptyState').html(`
                            <div class="text-center p-3">
                                <i class="bi bi-envelope-slash text-muted mb-2" style="font-size: 48px;"></i>
                                <p>No activity recorded for this document</p>
                            </div>
                        `).show();
                        $('#panelLogCount').text('0').addClass('bg-secondary');
                    }
                },
                error: function(xhr, status, error) {
                    console.error('AJAX Error:', error);
                    $('#loadingSpinner').hide();
                    $('#emptyState').show();
                    
                    let errorMsg = 'Error loading logs';
                    if (xhr.status === 404) {
                        errorMsg = 'URL not found.';
                    } else if (xhr.responseText) {
                        try {
                            const errorData = JSON.parse(xhr.responseText);
                            errorMsg = errorData.message || xhr.statusText;
                        } catch (e) {
                            errorMsg = xhr.statusText;
                        }
                    }
                    
                    $('#emptyState p').text(errorMsg);
                }
            });
        }

        // Helper functions for email logs
        function formatCC(ccValue) {
            if (!ccValue || ccValue === 'null' || ccValue === 'undefined' || ccValue === 'N/A' || ccValue === '') {
                return '';
            }
            return truncateText(ccValue, 15);
        }

        function escapeHtml(text) {
            if (!text) return '';
            return text
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;')
                .replace(/'/g, '&#039;');
        }

        // Replace the existing formatDate function with this updated version:
        function formatDate(dateString) {
            if (!dateString || dateString === '0000-00-00 00:00:00' || dateString === 'null') {
                return '';
            }
            
            try {
                let date = new Date(dateString);
                
                if (isNaN(date.getTime())) {
                    const parts = dateString.split(/[- :]/);
                    if (parts.length >= 5) {
                        date = new Date(parts[0], parts[1] - 1, parts[2], parts[3], parts[4], parts[5] || 0);
                    }
                }
                
                if (isNaN(date.getTime())) {
                    return '';
                }
                
                // Convert to Pakistan time (UTC+5)
                const pakistanTime = new Date(date.getTime() + (5 * 60 * 60 * 1000));
                
                const months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
                const day = pakistanTime.getDate();
                const month = months[pakistanTime.getMonth()];
                const year = pakistanTime.getFullYear();
                
                // 12-hour format with AM/PM
                let hours = pakistanTime.getHours();
                const minutes = pakistanTime.getMinutes().toString().padStart(2, '0');
                const ampm = hours >= 12 ? 'PM' : 'AM';
                
                // Convert to 12-hour format
                hours = hours % 12;
                hours = hours ? hours : 12; // 0 should be 12
                
                // Format hours with leading zero for single digit
                const formattedHours = hours.toString().padStart(2, '0');
                
                // Format: "Jan 26, 2025 02:30 PM"
                return `${month} ${day}, ${year} ${formattedHours}:${minutes} ${ampm}`;
                
            } catch (e) {
                console.error('Error formatting date:', dateString, e);
                return '';
            }
        }

        function truncateText(text, length) {
            if (!text || text === 'undefined' || text === 'null' || text === '' || text === 'N/A') {
                return '';
            }
            
            const textStr = String(text).trim();
            
            if (textStr.length > length) {
                return textStr.substring(0, length) + '...';
            }
            return textStr;
        }

        // Floating button for all email logs
        $('#emailLogBtn').on('click', function(e) {
            e.preventDefault();
            $('#allEmailLogPanel').addClass('open');
            $('#emailLogPanel').removeClass('open');
            loadAllEmailLogs();
        });

        // Close panel handlers
        $('#closeEmailPanel').on('click', function(e) {
            e.preventDefault();
            $('#emailLogPanel').removeClass('open');
        });

        $('#closeAllEmailPanel').on('click', function(e) {
            e.preventDefault();
            $('#allEmailLogPanel').removeClass('open');
        });

        // Close panels when clicking outside
        $(document).on('click', function(event) {
            if (!$(event.target).closest('.email-log-panel').length && 
                !$(event.target).closest('#emailLogBtn').length && 
                !$(event.target).closest('.email-log-btn').length) {
                $('.email-log-panel').removeClass('open');
            }
        });

        // Function to load all email logs (for the floating button)
        function loadAllEmailLogs() {
            $('#allLoadingSpinner').show();
            $('#allEmptyState').hide();
            $('#allEmailLogsTableBody').empty();

            fetch('{{ route('reminder.debugAllLogs') }}')
                .then(response => response.json())
                .then(data => {
                    $('#allLoadingSpinner').hide();
                    let logs = data.all_logs || [];

                    if (logs.length === 0) {
                        $('#allEmptyState').show();
                        return;
                    }

                    logs.forEach((log, index) => {
                        const row = document.createElement('tr');
                        const formattedDate = formatDate(log.curdatetime || log.created_at);

                        row.innerHTML = `
                            <td>${index + 1}</td>
                            <td>${log.uw_doc || 'N/A'}</td>
                            <td>${truncateText(log.sender || 'N/A', 20)}</td>
                            <td>${truncateText(log.receiver || 'N/A', 20)}</td>
                            <td>${truncateText(log.sub || 'N/A', 25)}</td>
                            <td><span class="route-badge">${log.route || 'N/A'}</span></td>
                            <td>${formattedDate}</td>
                        `;
                        $('#allEmailLogsTableBody').append(row);
                    });
                })
                .catch(error => {
                    $('#allLoadingSpinner').hide();
                    $('#allEmptyState').show();
                    console.error('Error loading all email logs:', error);
                });
        }

        // Handle window resize
        var resizeTimeout;
        $(window).on('resize', function() {
            clearTimeout(resizeTimeout);
            resizeTimeout = setTimeout(function() {
                var nowMobile = window.innerWidth < 768;
                if (isMobile !== nowMobile) {
                    location.reload();
                }
            }, 250);
        });

        // Mobile export buttons
        $(document).on('click', '.btn-mobile-export', function() {
            const exportType = $(this).data('export');
            
            if (!table) {
                showToast('Table not initialized', 'error');
                return;
            }

            // Trigger the corresponding DataTable button
            if (exportType === 'copy') {
                table.button('.buttons-copy').trigger();
            } else if (exportType === 'csv') {
                table.button('.buttons-csv').trigger();
            } else if (exportType === 'excel') {
                table.button('.buttons-excel').trigger();
            } else if (exportType === 'pdf') {
                table.button('.buttons-pdf').trigger();
            } else if (exportType === 'print') {
                table.button('.buttons-print').trigger();
            }
            
            showToast('Export ' + exportType.toUpperCase() + ' initiated', 'info');
        });

        // Initialize the application
        loadAllData();
    });
</script>

    
@endsection
