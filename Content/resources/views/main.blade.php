@extends('master')
@section('content')

    @if ($shouldShowPasswordAlert)
        <script>
            alert("Password expires in few days. Please Change your password.");
        </script>
    @endif
    

    <!-- Stats Container - Day Stats Only on the Right -->
    <div class="stats-container">
        <!-- Empty left side (spacer) -->
        <div class="stats-left-spacer"></div>

        <!-- Day Stats Cards (Right) -->
        <div id="leftPanel">
            <div id="appointmentStats">
                <div class="stat-card" data-days="7">
                    <div class="count">0</div>
                    <span class="label">7 Days</span>
                </div>
                <div class="stat-card" data-days="15">
                    <div class="count">0</div>
                    <span class="label">15 Days</span>
                </div>
                <div class="stat-card" data-days="30">
                    <div class="count">0</div>
                    <span class="label">30 Days</span>
                </div>
                <div class="stat-card" data-days="60">
                    <div class="count">0</div>
                    <span class="label">60 Days</span>
                </div>
                <div class="stat-card" data-days="90">
                    <div class="count">0</div>
                    <span class="label">90 Days</span>
                </div>
                <div class="stat-card" data-days="90plus">
                    <div class="count">0</div>
                    <span class="label">90+ Days</span>
                </div>
                <div class="stat-card" data-days="all">
                    <div class="count">0</div>
                    <span class="label">All</span>
                </div>
                <div class="stat-card stat-card-reset" id="resetCard">
                    <div class="count"><i class="bi bi-arrow-clockwise"></i></div>
                    <span class="label">Reset</span>
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
                            <div class="d-flex flex-column flex-sm-row justify-content-between align-items-start align-items-sm-center w-100">
                                <div class="d-flex align-items-center gap-3">
                                    <h4 style="font-family: 'Reddit Sans'; font-weight: 600; letter-spacing: 1px;"
                                        class="card-title mb-2 mb-sm-0 fs-5 fs-sm-4">
                                        Claims Management
                                    </h4>
                                </div>
                                @if (isset($claims[0]['surveyor_name']))
                                    <h5 style="font-family: cursive !important; font-size: 3rem; color: rgb(3, 26, 153); font-weight: 600;"
                                        class="mb-0 fs-6 fs-sm-5 ">
                                        {{ $claims[0]['surveyor_name'] }}
                                    </h5>
                                @endif
                            </div>
                        </div>

                        <div class="card-body px-2 px-sm-3">
                            @if (empty($claims) || isset($claims['error']))
                                <div class="alert {{ isset($claims['error']) ? 'alert-danger' : 'alert-info' }} text-center">
                                    <p class="mb-0">{{ $claims['error'] ?? 'No Claims Found.' }}</p>
                                </div>
                            @else
                                <!-- Desktop Table View -->
                                <div class="table-responsive d-none d-md-block">
                                    <table id="claimsTable" class="table table-bordered table-striped table-hover nowrap"
                                        style="width:100%">
                                        <thead>
                                            <tr>
                                                <th class="date-col">Action</th>
                                                <th class="text-left">PLR Sts</th>
                                                <th class="text-left">Claim No</th>
                                                <th class="date-col">Int_Date</th>
                                                <th class="date-col">App_Date</th>
                                                <th class="text-left">Insured</th>
                                                <th class="date-col">Mobile No</th>
                                                <th class="text-right">Sum Insured</th>
                                                <th class="text-left">Dep</th>
                                                <th class="text-left">Cause of Loss</th>
                                                <th class="text-right">Est_Amt</th>
                                                <th class="text-left">E-mail Address</th>
                                                <th class="text-left">City</th>
                                                <th class="text-right">Policy Number</th>
                                                <th class="date-col">Issue Date</th>
                                                <th class="date-col">Expiry Date</th>
                                            </tr>
                                        </thead>
                                        
                                        <tbody>
                                           
                                            @foreach ($claims as $claim)
                                            
                                                @php
                                                
                                                    $plrStatusDetail = $claim['plr_status_detail'] ?? 'Pending PLR (Doc Not Uploaded)';
                                                    $isPrOverdue = $claim['is_pr_overdue'] ?? false;
                                                    $daysSincePrUpload = $claim['days_since_pr_upload'] ?? 0;
                                                    $prUploadDate = $claim['pr_upload_date'] ?? null;
                                                    
                                                    $reportType = $claim['report_type'] ?? 'P/R';
                                                    $plrStatus = $claim['plr_status'] ?? 'Pending';
                                                    
                                                    $overdueDays = 0;
                                                    $shouldHighlight = false;
                                                    
                                                    if ($isPrOverdue && $plrStatus !== 'Approved') {
                                                        $overdueDays = $daysSincePrUpload > 2 ? $daysSincePrUpload - 2 : 0;
                                                        $shouldHighlight = true;
                                                    }
                                                    
                                                    $badgeClass = 'bg-secondary';
                                                    
                                                    if ($plrStatus === 'Approved') {
                                                        $badgeClass = 'bg-success';
                                                    } elseif ($isPrOverdue) {
                                                        $badgeClass = 'bg-danger';
                                                    } elseif (strpos($plrStatusDetail, 'Pending P/R Approval') !== false) {
                                                        $badgeClass = 'bg-warning text-dark';
                                                    } elseif (strpos($plrStatusDetail, 'Pending F/R') !== false) {
                                                        $badgeClass = 'bg-info text-dark';
                                                    } elseif (strpos($plrStatusDetail, 'Pending PLR') !== false) {
                                                        $badgeClass = 'bg-secondary';
                                                    }
                                                @endphp
                                                
                                                <tr data-status="{{ $claim['status'] ?? 'pending' }}"
                                                    data-plr-status-detail="{{ $plrStatusDetail }}"
                                                    data-dept="{{ $claim['department'] ?? '' }}"
                                                    data-report-type="{{ $reportType }}"
                                                    data-plr-status="{{ $plrStatus }}"
                                                    data-is-pr-overdue="{{ $isPrOverdue ? 'true' : 'false' }}"
                                                    data-days-since-pr-upload="{{ $daysSincePrUpload }}"
                                                    class="{{ $shouldHighlight ? 'overdue-row' : '' }}"
                                                    @if($shouldHighlight) data-overdue-days="{{ $overdueDays }}" @endif>
                                                    <td>
                                                        <div class="d-flex flex-column flex-sm-row gap-1">
                                                            @if (isset($claim['document_no']))
                                                                <a href="{{ route('upload.document.form', $claim['document_no']) }}"
                                                                    class="btn btn-sm text-white upload-btn {{ $reportType === 'F/R' ? 'btn-success' : 'btn-primary' }}"
                                                                    style="display:inline-block; min-width:10px; text-align:center;"
                                                                    data-plr-status="{{ $plrStatus }}" 
                                                                    data-report-type="{{ $reportType }}" 
                                                                    target="_blank"
                                                                    title="{{ $reportType === 'F/R' ? 'Upload Final Report' : 'Upload Preliminary Report' }}">
                                                                    {{ $reportType }}
                                                                    <i class="bi {{ $reportType === 'F/R' ? 'bi-file-earmark-check' : 'bi-file-earmark-arrow-up' }}"></i>
                                                                </a>
                                                            @endif

                                                            @if (isset($claim['document_no']))
                                                                <button type="button"
                                                                    class="btn btn-info btn-sm text-white"
                                                                    style="display:inline-block; min-width:10px; text-align:center;"
                                                                    data-bs-toggle="modal" data-bs-target="#viewReportModal"
                                                                    data-doc="{{ $claim['document_no'] }}"
                                                                    title="View Uploaded Documents">
                                                                    <i class="fa-solid fa-eye" style="color: #f5f7f9;"></i>
                                                                </button>
                                                            @endif

                                                            @if (isset($claim['document_no']) || isset($claim['email_address']) || !empty($claim['email_address']))
                                                                <button type="button"
                                                                    class="btn btn-success btn-sm text-white"
                                                                    style="display:inline-block; min-width:10px; text-align:center; border: none;"
                                                                    data-bs-toggle="modal" data-bs-target="#reminderModal"
                                                                    data-uwdoc="{{ $claim['document_no'] }}"
                                                                    data-client-email="{{ $claim['email_address'] }}"
                                                                    data-client-name="{{ $claim['client_name'] ?? 'N/A' }}"
                                                                    title="Send Email Reminder">
                                                                    <i class="bi bi-bell"></i>
                                                                </button>
                                                            @endif

                                                            @if (isset($claim['mobile_no']) && !empty($claim['mobile_no']) && $claim['mobile_no'] !== 'N/A')
                                                                <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $claim['mobile_no']) }}?text=Hello%20{{ urlencode($claim['client_name'] ?? '') }},%0A%0AThis%20is%20regarding%20your%20claim%20{{ urlencode($claim['document_no'] ?? '') }}.%0A%0AKindly%20provide%20the%20pending%20documents%20at%20your%20earliest%20convenience."
                                                                    class="btn btn-sm text-white"
                                                                    style="display:inline-block; min-width:10px; text-align:center; background-color: #25D366; border: none;"
                                                                    target="_blank" rel="noopener noreferrer"
                                                                    title="Send WhatsApp Message">
                                                                    <i class="bi bi-whatsapp"></i>
                                                                </a>
                                                            @endif

                                                            @if (!isset($claim['document_no']))
                                                                <span class="btn btn-secondary btn-sm disabled"
                                                                    style="display:inline-block; min-width:100px; text-align:center;"
                                                                    title="No document number available">
                                                                    No Document
                                                                </span>
                                                            @endif
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <span class="badge {{ $badgeClass }}"
                                                              data-plr-status-detail="{{ $plrStatusDetail }}"
                                                              data-report-type="{{ $reportType }}"
                                                              data-plr-status="{{ $plrStatus }}"
                                                              data-document-exists="{{ !empty($claim['document_no']) ? 'true' : 'false' }}"
                                                              data-days-since-pr-upload="{{ $daysSincePrUpload }}"
                                                              data-is-pr-overdue="{{ $isPrOverdue ? 'true' : 'false' }}"
                                                              data-pr-upload-date="{{ $prUploadDate }}"
                                                              title="{{ $plrStatusDetail }}">
                                                            {{ $plrStatusDetail }}
                                                            @if($shouldHighlight)
                                                                <i class="bi bi-exclamation-triangle ms-1"></i>
                                                                <small class="ms-1">(+{{ $overdueDays }})</small>
                                                            @endif
                                                        </span>
                                                    </td>
                                                    <td>{{ $claim['document_no'] ?? 'N/A' }}</td>
                                                    <td>{{ $claim['intimation_date'] ?? 'N/A' }}</td>
                                                    <td>{{ $claim['appointment_date'] ?? 'N/A' }}</td>
                                                    <td>{{ $claim['client_name'] ?? 'N/A' }}</td>
                                                    <td>{{ $claim['mobile_no'] ?? 'N/A' }}</td>
                                                    <td>{{ isset($claim['sum_insured']) ? number_format((float)$claim['sum_insured'], 0) : 'N/A' }}</td>
                                                    <td>{{ $claim['department'] ?? 'N/A' }}</td>
                                                    <td>{{ $claim['loss_description'] ?? 'N/A' }}</td>
                                                    <td>{{ isset($claim['estimate_amount']) ? number_format($claim['estimate_amount'], 0) : 'N/A' }}</td>
                                                    <td>{{ $claim['email_address'] ?? 'N/A' }}</td>
                                                    <td>{{ $claim['city'] ?? 'N/A' }}</td>
                                                    <td>{{ $claim['policy_number'] ?? 'N/A' }}</td>
                                                    <td>{{ $claim['issue_date'] ?? 'N/A' }}</td>
                                                    <td>{{ $claim['expiry_date'] ?? 'N/A' }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>

                                <!-- Mobile View -->
                                <div class="d-block d-md-none" id="mobileView">
                                    <!-- Mobile Export Buttons - Always Visible -->
                                    <div class="mobile-export-section mb-3">
                                        <div class="mobile-export-header mb-2">
                                            <h6 class="mb-0 fw-bold text-primary">Export Data</h6>
                                        </div>
                                        <div class="mobile-export-buttons">
                                            <button type="button" class="btn-mobile-export btn-excel"
                                                data-export="excel"
                                                title="Export to Excel">
                                                <i class="fas fa-file-excel"></i>
                                                <span>Excel</span>
                                            </button>
                                            <button type="button" class="btn-mobile-export btn-pdf" data-export="pdf"
                                                title="Export to PDF">
                                                <i class="fas fa-file-pdf"></i>
                                                <span>PDF</span>
                                            </button>
                                        </div>
                                    </div>

                                    <!-- Mobile Controls -->
                                    <div class="mobile-controls mb-3">
                                        <div class="row g-2">
                                            <!-- Year Filter for Mobile -->
                                            <div class="col-12">
                                                <label for="mobileYearFilter" class="form-label fw-bold mb-1">Year:</label>
                                                <select id="mobileYearFilter" class="form-select form-select-sm" title="Select Year">
                                                    <option value="2025" {{ $selectedYear == '2025' ? 'selected' : '' }}>2025</option>
                                                    <option value="2026" {{ $selectedYear == '2026' ? 'selected' : '' }}>2026</option>
                                                </select>
                                            </div>
                                            <div class="col-6">
                                                <label for="mobilePlrFilter" class="form-label fw-bold mb-1">PLR Status:</label>
                                                <select id="mobilePlrFilter" class="form-select form-select-sm" title="Filter by PLR Status">
                                                    <option value="all" selected>All PLR</option>
                                                    <option value="pending_doc">Pending PLR (Doc Not Uploaded)</option>
                                                    <option value="pending_pr_approval">Pending P/R Approval</option>
                                                    <option value="pending_fr">Pending F/R Upload</option>
                                                    <option value="pending_fr_approval">Pending F/R Approval</option>
                                                    <option value="approved">Approved</option>
                                                </select>
                                            </div>
                                            <div class="col-6">
                                                <label for="mobileDeptFilter" class="form-label fw-bold mb-1">Department:</label>
                                                <select id="mobileDeptFilter" class="form-select form-select-sm" title="Filter by Department">
                                                    <option value="" selected>All Departments</option>
                                                    <option value="Fire">Fire</option>
                                                    <option value="Marine">Marine</option>
                                                    <option value="Motor">Motor</option>
                                                    <option value="Miscellaneous">Miscellaneous</option>
                                                </select>
                                            </div>
                                            <div class="col-12">
                                                <label for="mobileSearch" class="form-label fw-bold mb-1">Search:</label>
                                                <input type="text" id="mobileSearch"
                                                    class="form-control form-control-sm" placeholder="Search claims..."
                                                    title="Search claims by any keyword">
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Mobile Cards -->
                                    <div id="mobileCardsContainer">
                                        @foreach ($claims as $index => $claim)
                                            @php
                                                $plrStatusDetail = $claim['plr_status_detail'] ?? 'Pending PLR (Doc Not Uploaded)';
                                                $isPrOverdue = $claim['is_pr_overdue'] ?? false;
                                                $daysSincePrUpload = $claim['days_since_pr_upload'] ?? 0;
                                                $prUploadDate = $claim['pr_upload_date'] ?? null;
                                                $reportType = $claim['report_type'] ?? 'P/R';
                                                $plrStatus = $claim['plr_status'] ?? 'Pending';
                                                
                                                $overdueDays = 0;
                                                $shouldHighlight = false;
                                                
                                                if ($isPrOverdue && $plrStatus !== 'Approved') {
                                                    $overdueDays = $daysSincePrUpload > 2 ? $daysSincePrUpload - 2 : 0;
                                                    $shouldHighlight = true;
                                                }
                                                
                                                $badgeClass = 'bg-secondary';
                                                
                                                if ($plrStatus === 'Approved') {
                                                    $badgeClass = 'bg-success';
                                                } elseif ($isPrOverdue) {
                                                    $badgeClass = 'bg-danger';
                                                } elseif (strpos($plrStatusDetail, 'Pending P/R Approval') !== false) {
                                                    $badgeClass = 'bg-warning text-dark';
                                                } elseif (strpos($plrStatusDetail, 'Pending F/R') !== false) {
                                                    $badgeClass = 'bg-info text-dark';
                                                } elseif (strpos($plrStatusDetail, 'Pending PLR') !== false) {
                                                    $badgeClass = 'bg-secondary';
                                                }
                                            @endphp
                                            
                                            <div class="mobile-claim-card mb-3"
                                                data-status="{{ $claim['status'] ?? 'pending' }}"
                                                data-plr-status-detail="{{ $plrStatusDetail }}"
                                                data-dept="{{ $claim['department'] ?? '' }}"
                                                data-report-type="{{ $reportType }}"
                                                data-plr-status="{{ $plrStatus }}"
                                                data-is-pr-overdue="{{ $isPrOverdue ? 'true' : 'false' }}"
                                                data-days-since-pr-upload="{{ $daysSincePrUpload }}"
                                                data-index="{{ $index }}"
                                                @if($shouldHighlight) data-overdue-days="{{ $overdueDays }}" @endif
                                                class="{{ $shouldHighlight ? 'overdue-mobile-card' : '' }}">
                                                <div class="card border-0 shadow-sm">
                                                    <div class="card-body p-3">
                                                        <div class="row g-2">
                                                            <div class="col-12">
                                                                <div class="d-flex justify-content-between align-items-start">
                                                                    <h6 class="mb-1 text-primary fw-bold">
                                                                        {{ $claim['client_name'] ?? 'N/A' }}
                                                                    </h6>
                                                                    <div class="d-flex flex-column gap-1">
                                                                        <span class="badge {{ $badgeClass }}"
                                                                            data-days-since-pr-upload="{{ $daysSincePrUpload }}"
                                                                            data-is-pr-overdue="{{ $isPrOverdue ? 'true' : 'false' }}"
                                                                            data-pr-upload-date="{{ $prUploadDate }}"
                                                                            data-plr-status="{{ $plrStatus }}"
                                                                            title="{{ $plrStatusDetail }}">
                                                                            Status: {{ $plrStatusDetail }}
                                                                            @if($shouldHighlight)
                                                                                <i class="bi bi-exclamation-triangle ms-1"></i>
                                                                                <small class="ms-1">(+{{ $overdueDays }})</small>
                                                                            @endif
                                                                        </span>
                                                                        <span class="badge bg-info text-dark" title="Department">
                                                                            Department: {{ $claim['department'] ?? 'N/A' }}
                                                                        </span>
                                                                    </div>
                                                                </div>
                                                            </div>

                                                            <div class="col-12">
                                                                <div class="row g-1 text-sm">
                                                                    <div class="col-6">
                                                                        <small class="text-muted d-block">Claim No:</small>
                                                                        <small class="fw-medium">{{ $claim['document_no'] ?? 'N/A' }}</small>
                                                                    </div>
                                                                    <div class="col-6">
                                                                        <small class="text-muted d-block">Mobile No:</small>
                                                                        <small class="fw-medium">{{ $claim['mobile_no'] ?? 'N/A' }}</small>
                                                                    </div>
                                                                    <div class="col-6">
                                                                        <small class="text-muted d-block">Intimation Date:</small>
                                                                        <small class="fw-medium">{{ $claim['intimation_date'] ?? 'N/A' }}</small>
                                                                    </div>
                                                                    <div class="col-6">
                                                                        <small class="text-muted d-block">Settlement Date:</small>
                                                                        <small class="fw-medium">{{ $claim['SETTLEMENT_DATE'] ?? 'N/A' }}</small>
                                                                    </div>
                                                                    <div class="col-6">
                                                                        <small class="text-muted d-block">Sum Insured:</small>
                                                                        <small class="fw-medium">
                                                                            {{ isset($claim['sum_insured']) ? number_format($claim['sum_insured'], 0) : 'N/A' }}
                                                                        </small>
                                                                    </div>
                                                                    <div class="col-6">
                                                                        <small class="text-muted d-block">Estimate Amount:</small>
                                                                        <small class="fw-medium">
                                                                            {{ isset($claim['estimate_amount']) ? number_format((float)$claim['estimate_amount'], 0) : 'N/A' }}
                                                                        </small>
                                                                    </div>
                                                                    <div class="col-6">
                                                                        <small class="text-muted d-block">Email:</small>
                                                                        <small class="fw-medium text-break">{{ $claim['email_address'] ?? 'N/A' }}</small>
                                                                    </div>
                                                                    <div class="col-6">
                                                                        <small class="text-muted d-block">Appointment Date:</small>
                                                                        <small class="fw-medium">{{ $claim['appointment_date'] ?? 'N/A' }}</small>
                                                                    </div>
                                                                    <div class="col-6">
                                                                        <small class="text-muted d-block">City:</small>
                                                                        <small class="fw-medium">{{ $claim['city'] ?? 'N/A' }}</small>
                                                                    </div>
                                                                    <div class="col-6">
                                                                        <small class="text-muted d-block">Policy Number:</small>
                                                                        <small class="fw-medium">{{ $claim['policy_number'] ?? 'N/A' }}</small>
                                                                    </div>
                                                                    <div class="col-6">
                                                                        <small class="text-muted d-block">Issue Date:</small>
                                                                        <small class="fw-medium">{{ $claim['issue_date'] ?? 'N/A' }}</small>
                                                                    </div>
                                                                    <div class="col-6">
                                                                        <small class="text-muted d-block">Expiry Date:</small>
                                                                        <small class="fw-medium">{{ $claim['expiry_date'] ?? 'N/A' }}</small>
                                                                    </div>
                                                                    <div class="col-12">
                                                                        <small class="text-muted d-block">Cause of Loss:</small>
                                                                        <small class="fw-medium">{{ $claim['loss_description'] ?? 'N/A' }}</small>
                                                                    </div>
                                                                </div>
                                                            </div>

                                                            <div class="col-12 mt-2">
                                                                <div class="d-flex gap-2 flex-wrap">
                                                                    @if (isset($claim['document_no']))
                                                                        <a href="{{ route('upload.document.form', $claim['document_no']) }}"
                                                                            class="btn btn-sm text-white upload-btn {{ $reportType === 'F/R' ? 'btn-success' : 'btn-primary' }}"
                                                                            style="display:inline-block; min-width:100px; text-align:center;"
                                                                            data-plr-status="{{ $plrStatus }}" 
                                                                            data-report-type="{{ $reportType }}" 
                                                                            target="_blank"
                                                                            title="{{ $reportType === 'F/R' ? 'Upload Final Report' : 'Upload Preliminary Report' }}">
                                                                            {{ $reportType }}
                                                                            <i class="bi {{ $reportType === 'F/R' ? 'bi-file-earmark-check' : 'bi-file-earmark-arrow-up' }}"></i>
                                                                        </a>
                                                                        <button type="button"
                                                                            class="btn btn-info btn-sm flex-fill"
                                                                            data-bs-toggle="modal"
                                                                            data-bs-target="#viewReportModal"
                                                                            data-doc="{{ $claim['document_no'] }}"
                                                                            title="View Uploaded Documents">
                                                                            <i class="fa-solid fa-eye" style="color: #f5f7f9; width:10px"></i>
                                                                        </button>

                                                                        @if (isset($claim['document_no']) || isset($claim['email_address']) || !empty($claim['email_address']))
                                                                            <button type="button"
                                                                                class="btn btn-success btn-sm flex-fill"
                                                                                data-bs-toggle="modal"
                                                                                data-bs-target="#reminderModal"
                                                                                data-uwdoc="{{ $claim['document_no'] }}"
                                                                                data-client-email="{{ $claim['email_address'] }}"
                                                                                data-client-name="{{ $claim['client_name'] ?? 'N/A' }}"
                                                                                title="Send Email Reminder">
                                                                                <i class="bi bi-bell"></i>
                                                                            </button>
                                                                        @endif
                                                                    @else
                                                                        <span class="btn btn-secondary btn-sm flex-fill disabled"
                                                                            title="No document number available">
                                                                            No Document
                                                                        </span>
                                                                    @endif
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Hidden table for mobile export functionality -->
    <div style="position: absolute; left: -9999px; top: -9999px; visibility: hidden;">
        <table id="hiddenExportTable" class="table">
            <thead>
                <tr>
                    <th>Client Name</th>
                    <th>Claim No</th>
                    <th>Mobile No</th>
                    <th>Intimation Date</th>
                    <th>Settlement Date</th>
                    <th>Sum Insured</th>
                    <th>Department</th>
                    <th>Loss Description</th>
                    <th>Estimate Amount</th>
                    <th>E-mail Address</th>
                    <th>Appointment Date</th>
                    <th>City</th>
                    <th>Policy Number</th>
                    <th>Issue Date</th>
                    <th>Expiry Date</th>
                    <th>Status</th>
                    <th>PLR Status</th>
                    <th>PLR Status Detail</th>
                    <th>Report Type</th>
                    <th>P/R Upload Date</th>
                    <th>Days Since P/R Upload</th>
                    <th>Overdue Days</th>
                    <th>Is P/R Overdue</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($claims as $claim)
                    @php
                        $daysSincePrUpload = $claim['days_since_pr_upload'] ?? 0;
                        $plrStatus = $claim['plr_status'] ?? 'Pending';
                        $isPrOverdue = $claim['is_pr_overdue'] ?? false;
                        
                        $overdueDays = 0;
                        if ($isPrOverdue && $plrStatus !== 'Approved') {
                            $overdueDays = $daysSincePrUpload > 2 ? $daysSincePrUpload - 2 : 0;
                        }
                    @endphp
                    <tr>
                        <td>{{ $claim['client_name'] ?? 'N/A' }}</td>
                        <td>{{ $claim['document_no'] ?? 'N/A' }}</td>
                        <td>{{ $claim['mobile_no'] ?? 'N/A' }}</td>
                        <td>{{ $claim['intimation_date'] ?? 'N/A' }}</td>
                        <td>{{ $claim['SETTLEMENT_DATE'] ?? 'N/A' }}</td>
                        <td>{{ isset($claim['sum_insured']) ? number_format($claim['sum_insured'], 0) : 'N/A' }}</td>
                        <td>{{ $claim['department'] ?? 'N/A' }}</td>
                        <td>{{ $claim['loss_description'] ?? 'N/A' }}</td>
                        <td>{{ isset($claim['estimate_amount']) ? number_format($claim['estimate_amount'], 0) : 'N/A' }}</td>
                        <td>{{ $claim['email_address'] ?? 'N/A' }}</td>
                        <td>{{ $claim['appointment_date'] ?? 'N/A' }}</td>
                        <td>{{ $claim['city'] ?? 'N/A' }}</td>
                        <td>{{ $claim['policy_number'] ?? 'N/A' }}</td>
                        <td>{{ $claim['issue_date'] ?? 'N/A' }}</td>
                        <td>{{ $claim['expiry_date'] ?? 'N/A' }}</td>
                        <td>{{ $claim['status'] ?? 'pending' }}</td>
                        <td>{{ $plrStatus }}</td>
                        <td>{{ $claim['plr_status_detail'] ?? 'Pending PLR (Doc Not Uploaded)' }}</td>
                        <td>{{ $claim['report_type'] ?? 'P/R' }}</td>
                        {{-- <td>{{ $claim['pr_upload_date'] ? date('d-M-Y', strtotime($claim['pr_upload_date'])) : 'N/A' }}</td> --}}
                        <td>
{{ data_get($claim, 'pr_upload_date') 
    ? date('d-M-Y', strtotime(data_get($claim, 'pr_upload_date'))) 
    : 'N/A' }}
</td> <!--UPDATE DUE TO Liaquat@iconsurveyors.com login issue--->
                        <td>{{ $daysSincePrUpload }}</td>
                        <td>{{ $overdueDays }}</td>
                        <td>{{ $isPrOverdue ? 'Yes' : 'No' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- View Report Modal -->
    <div class="modal fade" id="viewReportModal" tabindex="-1" aria-labelledby="viewReportModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered modal-fullscreen-sm-down">
            <div class="modal-content border-0 shadow-lg" style="border-radius: 18px;">
                <div class="modal-header text-white border-0"
                    style="background: linear-gradient(135deg, #0062cc 0%, #004085 100%); border-radius: 18px 18px 0 0;">
                    <h5 class="modal-title fw-semibold fs-5" id="viewReportModalLabel">
                        <i class="bi bi-file-earmark-text me-2"></i> Uploaded Documents
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>

                <div class="modal-body p-3">
                    <div class="table-responsive">
                        <table class="table table-hover table-striped align-middle text-center rounded-3 overflow-hidden mb-0">
                            <thead class="bg-light text-secondary">
                                <tr>
                                    <th class="py-3">📅 Date</th>
                                    <th class="py-3">📝 Remarks</th>
                                    <th class="py-3">⚡ Actions</th>
                                </tr>
                            </thead>
                            <tbody id="documentTableBody" class="small"></tbody>
                        </table>
                    </div>
                    <div id="docError" class="alert alert-danger mt-3 py-2 px-3 small d-none">
                        <i class="bi bi-exclamation-triangle-fill me-2"></i> Error loading documents.
                    </div>
                </div>

                <div class="modal-footer border-0 p-4">
                    <button type="button" id="downloadAllZip" data-doc="" class="btn rounded-pill px-4"
                        style="background: linear-gradient(135deg, #0066cc 0%, #0052a3 100%); color: white; border: none;"
                        title="Download all documents as ZIP file">
                        <i class="bi bi-file-zip me-1"></i> Download All as ZIP
                    </button>
                    <button type="button" class="btn btn-outline-secondary rounded-pill px-4" data-bs-dismiss="modal"
                        title="Close modal">
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
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close" title="Close modal"></button>
                </div>

                <div class="modal-body p-4">
                    <div id="reminderMessage" style="display: none;"></div>

                    <form id="reminderEmailForm" method="POST" action="{{ route('reminder.send') }}">
                        @csrf
                        <input type="hidden" name="uw_doc" id="uw_doc">
                        <input type="hidden" name="receiver_role" value="client">

                        <div class="mb-3">
                            <label for="sender" class="form-label fw-semibold">
                                <i class="bi bi-person-circle me-1"></i>From:
                            </label>
                            <input type="email" name="sender" id="sender" class="form-control rounded-pill"
                                value="{{ Auth::user()->email ?? '' }}"
                                title="Your email address">
                        </div>

                        <div class="mb-3">
                            <label for="receiver_email" class="form-label fw-semibold">
                                <i class="bi bi-envelope me-1"></i>To:
                            </label>
                            <input type="email" name="receiver_email" id="receiver_email"
                                class="form-control rounded-pill" required
                                title="Recipient email address">
                            <small class="text-muted">Client: <span id="clientName"></span></small>
                        </div>

                        <div class="mb-3">
                            <label for="sub" class="form-label fw-semibold">
                                <i class="bi bi-chat-text me-1"></i>Subject:
                            </label>
                            <input type="text" name="sub" id="sub" class="form-control rounded-pill"
                                value="Reminder: Pending Report Submission" required
                                title="Email subject">
                        </div>

                        <div class="mb-3">
                            <label for="body" class="form-label fw-semibold">
                                <i class="bi bi-card-text me-1"></i>Message:
                            </label>
                            <textarea name="body" id="body" rows="6" class="form-control" style="border-radius: 15px;" required
                                title="Email message content">Dear Client,
This is a friendly reminder regarding your pending report submission for Document No: [DOCUMENT_NO].

We kindly request you to submit the required documents at your earliest convenience to avoid any delays in processing.

If you have already submitted the documents, please disregard this message. For any queries, please feel free to contact us.

Thank you for your cooperation.</textarea>
                        </div>
                    </form>
                </div>

                <div class="modal-footer border-0 p-4">
                    <button type="button" id="sendReminderBtn" class="btn rounded-pill px-4"
                        title="Send reminder email">
                        <i class="bi bi-bell me-1"></i> <span>Send Reminder</span>
                    </button>
                    <button type="button" class="btn btn-outline-secondary rounded-pill px-4" data-bs-dismiss="modal"
                        title="Close without sending">
                        <i class="bi bi-x-circle me-1"></i> Cancel
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- WhatsApp Floating Button -->
    <a href="https://wa.me/9242111245000?text=Hello!%20%0AWelcome%20to%20Atlas%20Insurance%20Claim%20Management.%0A%0AKindly%20provide%20your%20claim%20reference%20or%20policy%20number."
        class="whatsapp-float" target="_blank" rel="noopener noreferrer" aria-label="Chat on WhatsApp"
        title="Chat with us on WhatsApp">
        <i class="bi bi-whatsapp"></i>
    </a>

    <style>
        .content-body {
            margin-top: -40px;
        }

        .stats-container {
            display: flex;
            justify-content: flex-end;
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

        .stats-left-spacer {
            flex: 1;
            min-width: 0;
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
            margin: 0;
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

        /* Overdue row styling */
        .overdue-row {
            background-color: rgba(220, 53, 69, 0.1) !important;
            border-left: 5px solid #dc3545 !important;
        }

        .overdue-row:hover {
            background-color: rgba(220, 53, 69, 0.15) !important;
        }

        .overdue-row[data-overdue-days="1"] {
            border-left-color: #ffc107 !important;
            background-color: rgba(255, 193, 7, 0.1) !important;
        }

        .overdue-row[data-overdue-days="2"],
        .overdue-row[data-overdue-days="3"] {
            border-left-color: #fd7e14 !important;
            background-color: rgba(253, 126, 20, 0.1) !important;
        }

        .overdue-row[data-overdue-days="4"],
        .overdue-row[data-overdue-days="5"] {
            border-left-color: #dc3545 !important;
            background-color: rgba(220, 53, 69, 0.1) !important;
        }

        .overdue-row[data-overdue-days] {
            border-left-color: #6610f2 !important;
            background-color: rgba(102, 16, 242, 0.1) !important;
        }

        .overdue-mobile-card .card {
            background-color: rgba(220, 53, 69, 0.1) !important;
            border-left: 5px solid #dc3545 !important;
        }

        .overdue-mobile-card[data-overdue-days="1"] .card {
            border-left-color: #ffc107 !important;
            background-color: rgba(255, 193, 7, 0.1) !important;
        }

        .overdue-mobile-card[data-overdue-days="2"] .card,
        .overdue-mobile-card[data-overdue-days="3"] .card {
            border-left-color: #fd7e14 !important;
            background-color: rgba(253, 126, 20, 0.1) !important;
        }

        .overdue-mobile-card[data-overdue-days="4"] .card,
        .overdue-mobile-card[data-overdue-days="5"] .card {
            border-left-color: #dc3545 !important;
            background-color: rgba(220, 53, 69, 0.1) !important;
        }

        .overdue-mobile-card[data-overdue-days] .card {
            border-left-color: #6610f2 !important;
            background-color: rgba(102, 16, 242, 0.1) !important;
        }

        .overdue-mobile-card .card:hover {
            background-color: rgba(220, 53, 69, 0.15) !important;
        }

        /* Year filter styling */
        .year-filter-desktop {
            background: #f8f9fa;
            padding: 4px 8px;
            border-radius: 4px;
            border: 1px solid #dee2e6;
        }

        .year-filter-desktop label {
            margin-bottom: 0 !important;
            white-space: nowrap;
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

        /* Table styling */
        #claimsTable thead tr th,
        table.dataTable thead th {
            background-color: #f8f9fa !important;
            color: #212529 !important;
            text-align: center;
            font-family: 'Calibri';
        }

        #claimsTable tbody tr td {
            text-align: center;
            vertical-align: middle;
            padding: 8px 4px;
            font-size: 12px;
        }

        /* Column alignments */
        #claimsTable thead th:nth-child(1),
        #claimsTable tbody td:nth-child(1) {
            text-align: center;
        }

        #claimsTable thead th:nth-child(2),
        #claimsTable tbody td:nth-child(2) {
            text-align: center;
        }

        #claimsTable thead th:nth-child(3),
        #claimsTable tbody td:nth-child(3) {
            text-align: left;
        }

        #claimsTable thead th:nth-child(4),
        #claimsTable tbody td:nth-child(4) {
            text-align: center;
            width: 120px;
        }

        #claimsTable thead th:nth-child(5),
        #claimsTable tbody td:nth-child(5) {
            text-align: center;
            width: 120px;
        }

        #claimsTable thead th:nth-child(6),
        #claimsTable tbody td:nth-child(6) {
            text-align: left;
        }

        #claimsTable thead th:nth-child(7),
        #claimsTable tbody td:nth-child(7) {
            text-align: center;
            width: 120px;
        }

        #claimsTable thead th:nth-child(8),
        #claimsTable tbody td:nth-child(8) {
            text-align: right;
            width: 120px;
        }

        #claimsTable thead th:nth-child(9),
        #claimsTable tbody td:nth-child(9) {
            text-align: left;
        }

        #claimsTable thead th:nth-child(10),
        #claimsTable tbody td:nth-child(10) {
            text-align: left;
        }

        #claimsTable thead th:nth-child(11),
        #claimsTable tbody td:nth-child(11) {
            text-align: left;
            width: 120px;
        }

        #claimsTable thead th:nth-child(12),
        #claimsTable tbody td:nth-child(12) {
            text-align: left;
            width: 120px;
        }

        #claimsTable thead th:nth-child(13),
        #claimsTable tbody td:nth-child(13) {
            text-align: left;
        }

        #claimsTable thead th:nth-child(14),
        #claimsTable tbody td:nth-child(14) {
            text-align: center;
            width: 120px;
        }

        #claimsTable thead th:nth-child(15),
        #claimsTable tbody td:nth-child(15) {
            text-align: left;
        }

        #claimsTable thead th:nth-child(16),
        #claimsTable tbody td:nth-child(16) {
            text-align: right;
        }

        #claimsTable thead th:nth-child(17),
        #claimsTable tbody td:nth-child(17) {
            text-align: center;
            width: 120px;
        }

        #claimsTable thead th:nth-child(18),
        #claimsTable tbody td:nth-child(18) {
            text-align: center;
            width: 120px;
        }

        #claimsTable tbody tr:nth-child(even) {
            background-color: #f8f9fa;
        }

        #claimsTable tbody tr:hover {
            background-color: #d1e7fd !important;
        }

        div.dataTables_scrollBody {
            max-height: 400px !important;
            overflow-y: scroll !important;
            border: 1px solid #ddd;
            border-radius: 4px;
        }

        .dt-buttons {
            margin-left: auto;
            display: flex !important;
            visibility: visible !important;
            opacity: 1 !important;
            gap: 4px;
            flex-wrap: wrap;
        }

        .dt-buttons .btn {
            padding: 4px 8px;
            font-size: 12px;
            height: 32px;
            display: flex !important;
            visibility: visible !important;
            opacity: 1 !important;
            align-items: center;
            justify-content: center;
            border-radius: 4px;
            transition: all 0.2s ease;
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

        /* Mobile card styling */
        .mobile-claim-card {
            opacity: 1;
            transition: opacity 0.3s ease;
        }

        .mobile-claim-card.hidden {
            display: none;
        }

        .mobile-claim-card .card {
            border-radius: 10px;
            border-left: 4px solid #007bff;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .mobile-claim-card .card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15) !important;
        }

        .mobile-claim-card[data-status="pending"] .card {
            border-left-color: #ffc107;
        }

        .mobile-claim-card[data-status="done"] .card {
            border-left-color: #28a745;
        }

        .mobile-controls {
            background: #f8f9fa;
            border-radius: 8px;
            padding: 12px;
            border: 1px solid #dee2e6;
        }

        .mobile-controls label {
            font-size: 14px !important;
            font-weight: 600 !important;
            color: #333333 !important;
            margin-bottom: 6px !important;
        }

        .text-sm {
            font-size: 0.85rem;
        }

        .dataTables_filter {
            text-align: left !important;
        }

        .dataTables_filter label {
            margin: 0;
            color: #000000 !important;
            justify-content: flex-start;
            font-family: Calibri, sans-serif;
            font-size: 14px;
            font-weight: 600;
        }

        /* Button styling */
        #sendReminderBtn {
            background: linear-gradient(135deg, #0066cc 0%, #0052a3 100%);
            border: none;
            color: #fff;
            transition: all 0.2s ease-in-out;
        }

        #sendReminderBtn:hover {
            background: linear-gradient(135deg, #0052a3 0%, #003d7a 100%);
        }

        .upload-btn.btn-primary {
            background: linear-gradient(135deg, #0066cc 0%, #0052a3 100%) !important;
            border: none !important;
            box-shadow: 0 2px 8px rgba(0, 102, 204, 0.3);
        }

        .upload-btn.btn-primary:hover {
            background: linear-gradient(135deg, #0052a3 0%, #003d7a 100%) !important;
            box-shadow: 0 4px 12px rgba(0, 102, 204, 0.4);
        }

        .upload-btn.btn-success {
            background: linear-gradient(135deg, #28a745 0%, #218838 100%) !important;
            border: none !important;
        }

        .btn-info {
            background: linear-gradient(135deg, #17a2b8 0%, #138496 100%) !important;
            border: none !important;
        }

        .btn-success {
            background: linear-gradient(135deg, #0066cc 0%, #0052a3 100%) !important;
            border: none !important;
        }

        /* ===== WHATSAPP FLOATING BUTTON STYLES ===== */
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

        /* Responsive styles */
        @media (max-width: 768px) {
            .container-fluid {
                padding-left: 0 !important;
                padding-right: 0 !important;
                overflow-x: hidden !important;
            }

            .stats-container {
                flex-direction: column;
                padding: 0 15px !important;
                margin-top: 50px;
                margin-left: 0 !important;
                margin-right: 0 !important;
                width: 100% !important;
                box-sizing: border-box !important;
                justify-content: center;
                gap: 15px;
            }

            .stats-left-spacer {
                display: none;
            }

            #leftPanel {
                width: calc(100% - 30px) !important;
                margin: 0 auto !important;
                padding: 6px;
                box-sizing: border-box !important;
                justify-content: center;
            }

            #appointmentStats {
                gap: 3px;
            }

            .stat-card {
                width: 48px !important;
                height: 48px !important;
                max-width: 52px;
                min-width: 45px;
            }

            .stat-card .count {
                font-size: 0.95em !important;
            }

            .stat-card .label {
                font-size: 0.58em !important;
            }

            .year-filter-desktop {
                display: none !important;
            }

            .mobile-export-section {
                padding: 12px;
                margin-bottom: 15px !important;
            }

            .mobile-export-buttons {
                gap: 6px;
            }

            .btn-mobile-export {
                min-width: 58px;
                height: 55px;
                padding: 10px 6px !important;
                font-size: 10px !important;
            }

            .btn-mobile-export i {
                font-size: 14px !important;
            }

            .btn-mobile-export span {
                font-size: 10px !important;
            }

            .whatsapp-float {
                width: 50px;
                height: 50px;
                bottom: 20px;
                right: 20px;
            }
        }

        @media (min-width: 769px) {
            #mobileYearFilter {
                display: none !important;
            }
        }

        @media (max-width: 480px) {
            .stats-container {
                margin-left: 0 !important;
                margin-right: 0 !important;
                padding: 0 12px !important;
                width: 100% !important;
                margin-top: 40px;
            }

            #leftPanel {
                margin: 0 auto !important;
                padding: 4px;
                width: calc(100% - 24px) !important;
                max-width: calc(100% - 24px) !important;
            }

            .stat-card {
                width: 44px !important;
                height: 44px !important;
                max-width: 48px;
                min-width: 42px;
            }

            .stat-card .count {
                font-size: 0.85em !important;
            }

            .stat-card .label {
                font-size: 0.52em !important;
            }

            .mobile-export-section {
                padding: 10px;
            }

            .btn-mobile-export {
                min-width: 52px;
                height: 50px;
                padding: 8px 4px !important;
            }

            .btn-mobile-export i {
                font-size: 13px !important;
            }

            .btn-mobile-export span {
                font-size: 9px !important;
            }

            .whatsapp-float {
                width: 45px;
                height: 45px;
                bottom: 15px;
                right: 15px;
            }
        }

        @media (max-width: 360px) {
            .stats-container {
                padding: 0 10px !important;
                margin-left: 0 !important;
                margin-right: 0 !important;
                width: 100% !important;
            }

            .stat-card {
                width: 40px !important;
                height: 40px !important;
                max-width: 44px;
                min-width: 38px;
            }

            #leftPanel {
                width: calc(100% - 20px) !important;
                max-width: calc(100% - 20px) !important;
                margin: 0 auto !important;
            }

            .btn-mobile-export {
                min-width: 48px;
                height: 45px;
                padding: 6px 3px !important;
            }

            .btn-mobile-export i {
                font-size: 12px !important;
            }

            .btn-mobile-export span {
                font-size: 8px !important;
            }
        }

        /* Adjust desktop controls to accommodate year filter */
        .desktop-controls-wrapper {
            align-items: center !important;
            gap: 10px;
            flex-wrap: wrap;
        }

        .dt-year-filter {
            margin-right: 10px;
        }

        .dt-plr-filter {
            margin-right: 10px;
        }
        

      
    </style>

    <script>
        $(document).ready(function() {
            var table;
            var hiddenTable;
            var isMobile = window.innerWidth < 768;
            var appointmentDateColumnIndex = null;
            var selectedYear = "{{ $selectedYear }}";

            function findAppointmentDateColumn() {
                if (appointmentDateColumnIndex !== null) return appointmentDateColumnIndex;

                if (isMobile) return null;

                var headers = $('#claimsTable thead th').map(function() {
                    return $(this).text().trim().toUpperCase();
                }).get();

                var possibleHeaders = ['APP_DATE', 'APPOINTMENT DATE', 'APPOINTMENT', 'APPT DATE',
                    'APPOINTMENT_DATE', 'APP_Date'
                ];

                for (var i = 0; i < headers.length; i++) {
                    for (var j = 0; j < possibleHeaders.length; j++) {
                        if (headers[i].includes(possibleHeaders[j].toUpperCase())) {
                            appointmentDateColumnIndex = i;
                            return appointmentDateColumnIndex;
                        }
                    }
                }

                appointmentDateColumnIndex = 4;
                return appointmentDateColumnIndex;
            }

            function initializeHiddenTable() {
                if (hiddenTable) {
                    hiddenTable.destroy();
                }

                hiddenTable = $('#hiddenExportTable').DataTable({
                    paging: false,
                    searching: false,
                    ordering: false,
                    info: false,
                    dom: 'Bfrtip',
                    buttons: [
                        {
                            extend: 'excel',
                            exportOptions: {
                                columns: ':visible:not(:first-child):not(:nth-child(2))'
                            },
                            filename: 'Claims_Export_' + selectedYear + '_' + new Date().toISOString().slice(0, 10)
                        },
                        {
                            extend: 'pdf',
                            exportOptions: {
                                columns: ':visible:not(:first-child):not(:nth-child(2))'
                            },
                            filename: 'Claims_Export_' + selectedYear + '_' + new Date().toISOString().slice(0, 10),
                            orientation: 'landscape',
                            pageSize: 'A4'
                        }
                    ],
                    language: {
                        emptyTable: "No data available in table"
                    }
                });
            }

            function initializeDesktopTable() {
                if (table) {
                    table.destroy();
                }

                table = $('#claimsTable').DataTable({
                    paging: false,
                    ordering: true,
                    responsive: false,
                    scrollX: true,
                    scrollY: getScrollHeight(),
                    scrollCollapse: false,
                    dom: '<"desktop-controls-wrapper d-flex align-items-center mb-2"\
                                <"dt-search-wrapper flex-grow-1 text-start"f>\
                                <"dt-year-filter flex-grow-1 text-center"<"year-filter-inner">>\
                                <"dt-plr-filter flex-grow-1 text-center"<"plr-filter-inner">>\
                                <"dt-dept-filter flex-grow-1 text-center"<"dept-filter-inner">>\
                                <"dt-buttons flex-grow-1 d-flex justify-content-end"B>>rtip',
                    buttons: [
                        {
                            extend: 'excel',
                            className: 'btn btn-sm btn-success',
                            text: '<i class="fas fa-file-excel"></i> Excel',
                            exportOptions: {
                                columns: ':visible:not(:first-child):not(:nth-child(2))'
                            },
                            filename: 'Claims_Export_' + selectedYear + '_' + new Date().toISOString().slice(0, 10),
                            title: 'Export to Excel'
                        },
                        {
                            extend: 'pdf',
                            className: 'btn btn-sm btn-danger',
                            text: '<i class="fas fa-file-pdf"></i> PDF',
                            orientation: 'landscape',
                            pageSize: 'A4',
                            filename: 'Claims_Export_' + selectedYear + '_' + new Date().toISOString().slice(0, 10),
                            exportOptions: {
                                columns: function(idx, data, node) {
                                    return idx !== 0 && idx !== 1;
                                }
                            },
                            title: 'Export to PDF'
                        }
                    ],
                    columnDefs: [{
                        targets: "_all",
                        className: "text-nowrap"
                    }],
                    language: {
                        emptyTable: "No claims found",
                        zeroRecords: "No matching records found"
                    },
                    initComplete: function() {
                        var yearFilterHtml = `
                            <div class="d-inline-flex align-items-center">
                                <label for="yearFilter" class="me-2 fw-bold mb-0 text-nowrap" style="font-size: 14px; font-weight: 700; color:#000000 !important; font-family: Calibri, sans-serif !important;">Year:</label>
                                <select id="yearFilter" class="form-select form-select-sm" style="width: 80px; font-size: 12px;" title="Select year">
                                    <option value="2025" ${selectedYear == '2025' ? 'selected' : ''}>2025</option>
                                    <option value="2026" ${selectedYear == '2026' ? 'selected' : ''}>2026</option>
                                </select>
                            </div>
                        `;
                        $('.year-filter-inner').html(yearFilterHtml);

                        var plrFilterHtml = `
                            <div class="d-inline-flex align-items-center">
                                <label for="plrFilter" class="me-2 fw-bold mb-0 text-nowrap" style="font-size: 14px; font-weight: 700; color:#000000 !important; font-family: Calibri, sans-serif !important;">PLR Status:</label>
                                <select id="plrFilter" class="form-select form-select-sm" style="width: 180px; font-size: 11px;" title="Filter by PLR status">
                                    <option value="all" selected>All</option>
                                    <option value="pending_doc">Pending PLR Upload</option> 
                                    <option value="pending_pr_approval">Pending P/R Approval</option>
                                    <option value="pending_fr">Pending F/R Upload</option>
                                    <option value="pending_fr_approval">Pending F/R Approval</option>
                                    <option value="approved">Approved</option>
                                </select>
                            </div>
                        `;
                        $('.plr-filter-inner').html(plrFilterHtml);

                        var deptFilterHtml = `
                            <div class="d-inline-flex align-items-center">
                                <label for="deptFilter" class="me-2 fw-bold mb-0 text-nowrap" style="font-size: 14px; font-weight: 700; color:#000000 !important; font-family: Calibri, sans-serif !important;">Department:</label>
                                <select id="deptFilter" class="form-select form-select-sm" style="width: 120px; font-size: 11px;" title="Filter by department">
                                    <option value="">All</option>
                                    <option value="Fire">Fire</option>
                                    <option value="Marine">Marine</option>
                                    <option value="Motor">Motor</option>
                                    <option value="Miscellaneous">Miscellaneous</option>
                                </select>
                            </div>`;
                        $('.dept-filter-inner').html(deptFilterHtml);

                        findAppointmentDateColumn();

                        setTimeout(function() {
                            applyDesktopFilters();
                            if (table) {
                                table.columns.adjust().draw();
                            }
                        }, 100);
                    }
                });
            }

            // Year filter change handler for desktop
            $(document).on('change', '#yearFilter', function() {
                var selectedYear = $(this).val();
                window.location.href = '{{ url()->current() }}?year=' + selectedYear;
            });

            // Year filter change handler for mobile
            $('#mobileYearFilter').on('change', function() {
                var selectedYear = $(this).val();
                window.location.href = '{{ url()->current() }}?year=' + selectedYear;
            });

            $('.btn-mobile-export').on('click', function() {
                var exportType = $(this).data('export');
                $(this).addClass('loading').prop('disabled', true);
                var originalHtml = $(this).html();
                $(this).html('<i class="fas fa-spinner fa-spin"></i><span>Processing...</span>');

                updateHiddenTableData();

                setTimeout(() => {
                    try {
                        switch (exportType) {
                            case 'excel':
                                hiddenTable.button('.buttons-excel').trigger();
                                showToast('Excel file downloaded!', 'success');
                                break;
                            case 'pdf':
                                hiddenTable.button('.buttons-pdf').trigger();
                                showToast('PDF file downloaded!', 'success');
                                break;
                        }
                    } catch (error) {
                        console.error('Export error:', error);
                        showToast('Export failed. Please try again.', 'error');
                    }
                    $(this).removeClass('loading').prop('disabled', false).html(originalHtml);
                }, 500);
            });

            function updateHiddenTableData() {
                var visibleData = [];

                $('.mobile-claim-card:not(.hidden)').each(function() {
                    var $card = $(this);
                    var daysSincePrUpload = parseInt($card.data('days-since-pr-upload') || 0);
                    var plrStatus = $card.data('plr-status') || 'Pending';
                    var isPrOverdue = $card.data('is-pr-overdue') === 'true';
                    
                    var overdueDays = 0;
                    if (isPrOverdue && plrStatus !== 'Approved') {
                        overdueDays = daysSincePrUpload > 2 ? daysSincePrUpload - 2 : 0;
                    }
                    
                    var cardData = {
                        client_name: $card.find('.text-primary.fw-bold').text().trim(),
                        document_no: $card.find('small:contains("Claim")').next('small').text().trim(),
                        mobile_no: $card.find('small:contains("Mobile")').next('small').text().trim(),
                        intimation_date: $card.find('small:contains("Intimation")').next('small').text().trim(),
                        settlement_date: $card.find('small:contains("Settlement")').next('small').text().trim(),
                        sum_insured: $card.find('small:contains("Sum")').next('small').text().trim(),
                        department: $card.data('dept') || 'N/A',
                        loss_description: $card.find('small:contains("Loss Description")').next('small').text().trim(),
                        estimate_amount: $card.find('small:contains("Estimate")').next('small').text().trim(),
                        email: $card.find('small:contains("Email")').next('small').text().trim(),
                        appointment_date: $card.find('small:contains("Appointment")').next('small').text().trim(),
                        city: $card.find('small:contains("City")').next('small').text().trim(),
                        policy_number: $card.find('small:contains("Policy")').next('small').text().trim(),
                        issue_date: $card.find('small:contains("Issue")').next('small').text().trim(),
                        expiry_date: $card.find('small:contains("Expiry")').next('small').text().trim(),
                        status: $card.data('status') || 'pending',
                        plr_status: plrStatus,
                        plr_status_detail: $card.find('.badge').text().replace('PLR: ', '').trim(),
                        report_type: $card.data('report-type') || 'P/R',
                        pr_upload_date: $card.find('.badge').data('pr-upload-date') || 'N/A',
                        days_since_pr_upload: daysSincePrUpload,
                        overdue_days: overdueDays,
                        is_pr_overdue: isPrOverdue ? 'Yes' : 'No'
                    };

                    visibleData.push(cardData);
                });

                hiddenTable.clear();
                visibleData.forEach(function(row) {
                    hiddenTable.row.add([
                        row.client_name,
                        row.document_no,
                        row.mobile_no,
                        row.intimation_date,
                        row.settlement_date,
                        row.sum_insured,
                        row.department,
                        row.loss_description,
                        row.estimate_amount,
                        row.email,
                        row.appointment_date,
                        row.city,
                        row.policy_number,
                        row.issue_date,
                        row.expiry_date,
                        row.status,
                        row.plr_status,
                        row.plr_status_detail,
                        row.report_type,
                        row.pr_upload_date,
                        row.days_since_pr_upload,
                        row.overdue_days,
                        row.is_pr_overdue
                    ]);
                });
                hiddenTable.draw();
            }

            function showToast(message, type) {
                var bgColor = type === 'success' ? '#28a745' :
                    type === 'error' ? '#dc3545' :
                    type === 'info' ? '#17a2b8' : '#6c757d';

                var toast = $(`
                    <div class="toast-notification" style="
                        position: fixed;
                        top: 20px;
                        right: 20px;
                        background: ${bgColor};
                        color: white;
                        padding: 12px 20px;
                        border-radius: 6px;
                        box-shadow: 0 4px 12px rgba(0,0,0,0.3);
                        z-index: 9999;
                        font-size: 14px;
                        font-weight: 500;
                        max-width: 300px;
                        animation: slideInRight 0.3s ease-out;
                    ">
                        <i class="fas ${type === 'success' ? 'fa-check-circle' : 
                                      type === 'error' ? 'fa-exclamation-circle' : 
                                      type === 'info' ? 'fa-info-circle' : 'fa-bell'} me-2"></i>
                        ${message}
                    </div>
                `);

                $('body').append(toast);
                setTimeout(function() {
                    toast.fadeOut(300, function() {
                        $(this).remove();
                    });
                }, 3000);
            }

            $('<style>').prop('type', 'text/css').html(`
                @keyframes slideInRight {
                    from { transform: translateX(100%); opacity: 0; }
                    to { transform: translateX(0); opacity: 1; }
                }
            `).appendTo('head');

            function getScrollHeight() {
                if (window.innerWidth <= 360) return "250px";
                if (window.innerWidth <= 480) return "280px";
                if (window.innerWidth <= 768) return "350px";
                return "400px";
            }

            function parseDDMMMYY(dateStr) {
                if (!dateStr || dateStr === 'N/A') return null;
                var parts = dateStr.split('-');
                if (parts.length !== 3) return null;

                var day = parseInt(parts[0], 10);
                var monthStr = parts[1].toUpperCase();
                var year = parseInt(parts[2], 10);
                if (year < 100) year += year < 50 ? 2000 : 1900;

                var monthMap = {
                    JAN: 0,
                    FEB: 1,
                    MAR: 2,
                    APR: 3,
                    MAY: 4,
                    JUN: 5,
                    JUL: 6,
                    AUG: 7,
                    SEP: 8,
                    OCT: 9,
                    NOV: 10,
                    DEC: 11
                };
                var month = monthMap[monthStr];
                if (month === undefined) return null;

                return new Date(year, month, day);
            }

            function updateAppointmentStats() {
                var today = new Date();
                var counts = {
                    "7": 0,
                    "15": 0,
                    "30": 0,
                    "60": 0,
                    "90": 0,
                    "90plus": 0,
                    "all": 0
                };

                if (isMobile) {
                    $('.mobile-claim-card').each(function() {
                        var $card = $(this);
                        var cardStatus = $card.data('status') || 'pending';
                        if (cardStatus !== 'pending') return;

                        var appDateStr = $card.find('small:contains("Appointment")').next('small').text();
                        var appDate = parseDDMMMYY(appDateStr);
                        if (!appDate) return;

                        var diffDays = Math.floor((today - appDate) / (1000 * 60 * 60 * 24));
                        counts["all"]++;
                        if (diffDays <= 7) counts["7"]++;
                        if (diffDays <= 15) counts["15"]++;
                        if (diffDays <= 30) counts["30"]++;
                        if (diffDays <= 60) counts["60"]++;
                        if (diffDays <= 90) counts["90"]++;
                        if (diffDays > 90) counts["90plus"]++;
                    });
                } else if (table) {
                    var appDateColIndex = findAppointmentDateColumn();

                    table.rows().every(function() {
                        var rowStatus = $(this.node()).data('status') || 'pending';
                        if (rowStatus !== 'pending') return;

                        var rowData = this.data();
                        var appDateStr = rowData[appDateColIndex];
                        var appDate = parseDDMMMYY(appDateStr);
                        if (!appDate) return;

                        var diffDays = Math.floor((today - appDate) / (1000 * 60 * 60 * 24));
                        counts["all"]++;
                        if (diffDays <= 7) counts["7"]++;
                        if (diffDays <= 15) counts["15"]++;
                        if (diffDays <= 30) counts["30"]++;
                        if (diffDays <= 60) counts["60"]++;
                        if (diffDays <= 90) counts["90"]++;
                        if (diffDays > 90) counts["90plus"]++;
                    });
                }

                $('#appointmentStats .stat-card:not(.stat-card-reset)').each(function() {
                    var days = $(this).data('days');
                    $(this).find('.count').text(counts[days]);
                });
            }

            $('#appointmentStats').on('click', '.stat-card:not(.stat-card-reset)', function() {
                var selectedDays = $(this).data('days');
                $('.stat-card').removeClass('active');
                $(this).addClass('active');

                var filteredCount = 0;
                if (isMobile) {
                    filteredCount = filterMobileCardsByDate(selectedDays);
                } else if (table) {
                    filteredCount = filterTableByDate(selectedDays);
                }

                var dayLabel = $(this).find('.label').text();
                showToast(`Showing ${filteredCount} record(s) for ${dayLabel}`, 'info');
            });

            $('#resetCard').on('click', function() {
                $('.stat-card').removeClass('active');

                if (isMobile) {
                    $('#mobilePlrFilter').val('all');
                    $('#mobileDeptFilter').val('');
                    $('#mobileSearch').val('');
                    applyMobileFilters();

                    var visibleCount = $('.mobile-claim-card:not(.hidden)').length;
                    showToast(`Filters reset. Showing ${visibleCount} record(s)`, 'success');
                } else if (table) {
                    $('#plrFilter').val('all');
                    $('#deptFilter').val('');
                    table.search('').draw();
                    applyDesktopFilters();

                    var visibleCount = table.rows({
                        filter: 'applied'
                    }).count();
                    showToast(`Filters reset. Showing ${visibleCount} record(s)`, 'success');
                }

                updateAppointmentStats();
            });

            function filterMobileCardsByDate(selectedDays) {
                var today = new Date();
                var visibleCount = 0;

                $('.mobile-claim-card').each(function() {
                    var $card = $(this);
                    var cardStatus = $card.data('status') || 'pending';
                    if (cardStatus !== 'pending') {
                        $card.addClass('hidden');
                        return;
                    }

                    var appDateStr = $card.find('small:contains("Appointment")').next('small').text();
                    var appDate = parseDDMMMYY(appDateStr);
                    if (!appDate) {
                        $card.addClass('hidden');
                        return;
                    }

                    var diffDays = Math.floor((today - appDate) / (1000 * 60 * 60 * 24));
                    var shouldShow = false;
                    if (selectedDays === 'all') {
                        shouldShow = true;
                    } else if (selectedDays === '90plus') {
                        shouldShow = diffDays > 90;
                    } else {
                        shouldShow = diffDays <= parseInt(selectedDays);
                    }

                    if (shouldShow) {
                        $card.removeClass('hidden');
                        visibleCount++;
                    } else {
                        $card.addClass('hidden');
                    }
                });

                return visibleCount;
            }

            function filterTableByDate(selectedDays) {
                var appDateColIndex = findAppointmentDateColumn();

                $.fn.dataTable.ext.search = $.fn.dataTable.ext.search.filter(function(fn) {
                    return fn.toString().indexOf('appDateStr') === -1;
                });

                $.fn.dataTable.ext.search.push(function(settings, data, dataIndex) {
                    var rowStatus = $(table.row(dataIndex).node()).data('status') || 'pending';
                    if (rowStatus !== 'pending') return false;
                    if (selectedDays === 'all') return true;

                    var appDateStr = data[appDateColIndex];
                    var appDate = parseDDMMMYY(appDateStr);
                    if (!appDate) return false;

                    var today = new Date();
                    var diffDays = Math.floor((today - appDate) / (1000 * 60 * 60 * 24));
                    if (selectedDays === '90plus') return diffDays > 90;
                    return diffDays <= parseInt(selectedDays);
                });

                table.draw();
                return table.rows({
                    filter: 'applied'
                }).count();
            }

            $('#mobilePlrFilter, #mobileDeptFilter').on('change', function() {
                applyMobileFilters();
            });

            $('#mobileSearch').on('input', function() {
                applyMobileFilters();
            });

            function applyMobileFilters() {
                var selectedPlrStatus = ($('#mobilePlrFilter').val() || '').toLowerCase().trim();
                var selectedDept = ($('#mobileDeptFilter').val() || '').toLowerCase().trim();
                var searchTerm = ($('#mobileSearch').val() || '').toLowerCase().trim();

                $('.mobile-claim-card').each(function() {
                    var $card = $(this);
                    var cardPlrStatusDetail = ($card.find('.badge').text() || '').toLowerCase().trim().replace('plr: ', '');
                    var cardDept = ($card.data('dept') || '').toLowerCase().trim();
                    var cardText = $card.text().toLowerCase();
                    var cardPlrStatus = $card.data('plr-status') || 'Pending';
                    
                    var plrMatch = false;
                    
                    switch(selectedPlrStatus) {
                        case 'all':
                            plrMatch = true;
                            break;
                        case 'pending_doc':
                            plrMatch = cardPlrStatusDetail.includes('pending plr') && !cardPlrStatusDetail.includes('approval');
                            break;
                        case 'pending_pr_approval':
                            plrMatch = cardPlrStatusDetail.includes('pending p/r approval');
                            break;
                        case 'pending_fr':
                            plrMatch = cardPlrStatusDetail.includes('pending f/r') && !cardPlrStatusDetail.includes('approval');
                            break;
                        case 'pending_fr_approval':
                            plrMatch = cardPlrStatusDetail.includes('pending f/r approval');
                            break;
                        case 'approved':
                            plrMatch = cardPlrStatus === 'Approved';
                            break;
                        default:
                            plrMatch = true;
                    }
                    
                    var deptMatch = (selectedDept === '' || cardDept === selectedDept);
                    var searchMatch = (searchTerm === '' || cardText.includes(searchTerm));

                    if (plrMatch && deptMatch && searchMatch) {
                        $card.removeClass('hidden');
                    } else {
                        $card.addClass('hidden');
                    }
                });
            }

            $(document).on('change', '#plrFilter, #deptFilter', function() {
                applyDesktopFilters();
            });

            function applyDesktopFilters() {
                if (!table) return;

                var selectedPlrStatus = $('#plrFilter').val();
                var selectedDept = $('#deptFilter').val();

                $.fn.dataTable.ext.search = [];

                $.fn.dataTable.ext.search.push(function(settings, data, dataIndex) {
                    var $row = $(table.row(dataIndex).node());
                    var rowDept = $row.data('dept') || '';
                    var rowPlrStatus = $row.data('plr-status') || 'Pending';
                    
                    var plrStatusDetail = $row.find('td:nth-child(2) .badge').data('plr-status-detail') || '';
                    
                    var plrMatch = false;
                    
                    switch(selectedPlrStatus) {
                        case 'all':
                            plrMatch = true;
                            break;
                        case 'pending_doc':
                            plrMatch = plrStatusDetail.includes('Pending PLR') && !plrStatusDetail.includes('Approval');
                            break;
                        case 'pending_pr_approval':
                            plrMatch = plrStatusDetail.includes('Pending P/R Approval');
                            break;
                        case 'pending_fr':
                            plrMatch = plrStatusDetail.includes('Pending F/R') && !plrStatusDetail.includes('Approval');
                            break;
                        case 'pending_fr_approval':
                            plrMatch = plrStatusDetail.includes('Pending F/R Approval');
                            break;
                        case 'approved':
                            plrMatch = rowPlrStatus === 'Approved';
                            break;
                        default:
                            plrMatch = true;
                    }
                    
                    var deptMatch = (selectedDept === '' || rowDept === selectedDept);
                    
                    return plrMatch && deptMatch;
                });

                table.draw();
                updateAppointmentStats();
                $('.stat-card').removeClass('active');
            }

            function initializeTooltips() {
                // Initialize Bootstrap tooltips if needed
                var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
                var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
                    return new bootstrap.Tooltip(tooltipTriggerEl);
                });
            }

            $('#downloadAllZip').on('click', function() {
                const docNo = $(this).data('doc');
                if (!docNo) {
                    showToast('No document selected or missing document number.', 'error');
                    return;
                }

                const $btn = $(this);
                $btn.prop('disabled', true).html(
                    '<i class="bi bi-hourglass-split me-1"></i> Processing...');

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
                        $btn.prop('disabled', false).html(
                            '<i class="bi bi-file-zip me-1"></i> Download All as ZIP');
                    }
                });
            });

            $('#viewReportModal').on('show.bs.modal', function(event) {
                var button = $(event.relatedTarget);
                var docNo = button.data('doc');
                var tbody = $('#documentTableBody');
                var errorBox = $('#docError');

                $('#downloadAllZip').data('doc', docNo);

                tbody.html('<tr><td colspan="3">Loading...</td></tr>');
                errorBox.hide();

                $.ajax({
                    url: '{{ url('documents') }}/' + docNo + '/files',
                    method: 'GET',
                    success: function(data) {
                        tbody.empty();
                        if (!data || data.length === 0) {
                            tbody.html(
                                '<tr><td colspan="3">No documents uploaded yet.</td></tr>');
                        } else {
                            data.forEach(function(doc) {
                                tbody.append(
                                    '<tr>' +
                                    '<td>' + (doc.date ?? '-') + '</td>' +
                                    '<td>' + (doc.remarks ?? '-') + '</td>' +
                                    '<td>' +
                                    '<a href="' + doc.url +
                                    '" target="_blank" class="btn btn-sm text-white me-2 rounded-pill" style="background-color: #000088;" title="Open document">' +
                                    '<i class="bi bi-box-arrow-up-right me-1"></i>Open</a>' +
                                    '<a href="' + doc.url +
                                    '" download class="btn btn-sm text-white me-2 rounded-pill" style="background-color: #28A745;" title="Download document">' +
                                    '<i class="bi bi-download me-1"></i>Download</a>' +
                                    '</td>' +
                                    '</tr>'
                                );
                            });
                        }
                    },
                    error: function() {
                        tbody.html('');
                        errorBox.show();
                    }
                });
            });

            $('#reminderModal').on('show.bs.modal', function(event) {
                var button = $(event.relatedTarget);
                var uwdoc = button.data('uwdoc');
                var clientEmail = button.data('client-email');
                var clientName = button.data('client-name');

                $('#uw_doc').val(uwdoc);
                $('#receiver_email').val(clientEmail);
                $('#clientName').text(clientName);

                var bodyText = $('#body').val();
                bodyText = bodyText.replace('[DOCUMENT_NO]', uwdoc);
                $('#body').val(bodyText);

                $('#reminderMessage').hide().removeClass('alert-success alert-danger');
            });

            $('#sendReminderBtn').on('click', function() {
                var $btn = $(this);
                var $form = $('#reminderEmailForm');
                var $message = $('#reminderMessage');

                if (!$form[0].checkValidity()) {
                    $form[0].reportValidity();
                    return;
                }

                $btn.prop('disabled', true);
                $btn.find('span').text('Sending...');
                $btn.find('i').removeClass('bi-bell').addClass('bi-hourglass-split');

                $.ajax({
                    url: $form.attr('action'),
                    method: 'POST',
                    data: $form.serialize(),
                    success: function(response) {
                        $message.removeClass('alert-danger')
                            .addClass('alert alert-success')
                            .html(
                                '<i class="bi bi-check-circle me-2"></i>Reminder sent successfully!'
                            )
                            .show();

                        setTimeout(function() {
                            $('#reminderModal').modal('hide');
                        }, 2000);
                    },
                    error: function(xhr) {
                        var errorMsg = 'Failed to send reminder.';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            errorMsg = xhr.responseJSON.message;
                        }

                        $message.removeClass('alert-success')
                            .addClass('alert alert-danger')
                            .html('<i class="bi bi-exclamation-triangle me-2"></i>' + errorMsg)
                            .show();
                    },
                    complete: function() {
                        $btn.prop('disabled', false);
                        $btn.find('span').text('Send Reminder');
                        $btn.find('i').removeClass('bi-hourglass-split').addClass('bi-bell');
                    }
                });
            });

            $('#reminderModal').on('hidden.bs.modal', function() {
                $('#reminderEmailForm')[0].reset();
                $('#reminderMessage').hide().removeClass('alert-success alert-danger');
            });

            var resizeTimeout;
            $(window).on('resize', function() {
                clearTimeout(resizeTimeout);
                resizeTimeout = setTimeout(function() {
                    var nowMobile = window.innerWidth < 768;
                    if (isMobile !== nowMobile) {
                        isMobile = nowMobile;
                        location.reload();
                    } else if (table) {
                        table.settings()[0].oScroll.sY = getScrollHeight();
                        table.columns.adjust().draw();
                    }
                }, 250);
            });

            if (!isMobile) {
                initializeDesktopTable();
            }

            initializeHiddenTable();
            updateAppointmentStats();
            initializeTooltips();

            setTimeout(function() {
                if (isMobile) {
                    applyMobileFilters();
                } else {
                    applyDesktopFilters();
                }
            }, 500);
        });
    </script>

@endsection