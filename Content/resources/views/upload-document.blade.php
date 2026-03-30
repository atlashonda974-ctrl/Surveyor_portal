@extends('master')

@section('content')
    <div class="upload-wrapper">
        <!-- Header Section -->
        <div class="page-header">
            <div class="container">
                <div class="header-content">
                    <div class="header-layout">
                        <div class="header-left">
                            <h1 style="font-family: 'Reddit Sans'; font-weight: 600; letter-spacing: 1px;" class="page-title">Document Upload Portal</h1>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="container main-content">
            <!-- Toast Container -->
            <div class="toast-container position-fixed top-0 end-0 p-3" id="toastContainer" style="z-index: 1055;"></div>

            <!-- Upload Container -->
            <div class="upload-container">
                <!-- Dynamic Status Alert based on admin actions -->
                @php
                    // Determine what message to show based on current status
                    $statusMessage = '';
                    $statusType = 'info'; // default: info, success, warning, danger
                    $statusIcon = 'bi-info-circle-fill';
                    $hideUploadForm = false;
                    $showCompletionState = false;
                    
                    // Check if Final Report is already approved
                    if ($isFrApproved) {
                        $statusMessage = 'Final Report (F/R) has been approved! Process completed. No further uploads required.';
                        $statusType = 'success';
                        $statusIcon = 'bi-check-circle-fill';
                        $hideUploadForm = true;
                        $showCompletionState = true;
                    }
                    // Check if PLR is approved
                    else if ($isPlrApproved) {
                        $statusMessage = 'Preliminary Report (P/R) has been Approved! You can now upload the Final Report (F/R).';
                        $statusType = 'success';
                        $statusIcon = 'bi-check-circle-fill';
                        $hideUploadForm = false;
                    } 
                    // Check if admin has sent a reminder (plr_final = 'R')
                    else if (isset($plr_final_status) && $plr_final_status == 'R') {
                        if (isset($actionIsForFR) && $actionIsForFR) {
                            $statusMessage = 'Admin has sent a reminder for Final Report. Please check your Email and upload Final Report (F/R).';
                        } else {
                            $statusMessage = 'Admin has sent a reminder for Preliminary Report. Please check your Email and upload Preliminary Report (P/R).';
                        }
                        $statusType = 'warning';
                        $statusIcon = 'bi-bell-fill';
                        $hideUploadForm = false;
                    }
                    // Check if admin requested revision (plr_final = 'V')
                    else if (isset($plr_final_status) && $plr_final_status == 'V') {
                        if (isset($actionIsForFR) && $actionIsForFR) {
                            $statusMessage = 'Final Report requires revision. Please review admin comments in your email and resubmit Final Report (F/R).';
                        } else {
                            $statusMessage = 'Preliminary Report requires revision. Please review admin comments in your email and resubmit Preliminary Report (P/R).';
                        }
                        $statusType = 'danger';
                        $statusIcon = 'bi-exclamation-triangle-fill';
                        $hideUploadForm = false;
                    }
                    // Check if report is in review (plr_final = 'I')
                    else if (isset($plr_final_status) && $plr_final_status == 'I') {
                        $statusMessage = 'Report is currently under review. Please wait for admin approval or further instructions.';
                        $statusType = 'info';
                        $statusIcon = 'bi-hourglass-split';
                        $hideUploadForm = false;
                    }
                    // Check if report is not approved (plr_final = 'N')
                    else if (isset($plr_final_status) && $plr_final_status == 'N') {
                        $statusMessage = 'Report was not approved. Please check for admin comments and resubmit.';
                        $statusType = 'secondary';
                        $statusIcon = 'bi-x-circle-fill';
                        $hideUploadForm = false;
                    }
                    // Default message for new upload
                    else {
                        $statusMessage = 'Please! upload the Preliminary Report (P/R) for Admin approval.';
                        $statusType = 'info';
                        $statusIcon = 'bi-info-circle-fill';
                        $hideUploadForm = false;
                    }
                @endphp

                <div class="alert alert-{{ $statusType }} status-alert">
                    <i class="bi {{ $statusIcon }} me-2"></i>
                    {{ $statusMessage }}
                </div>

                <!-- Completion State - Show when Final Report is approved -->
                @if($showCompletionState)
                <div class="completion-state text-center py-5" style="background: #f8f9fa; border-radius: 10px; margin: 2rem 0;">
                    <i class="bi bi-check-circle-fill text-success" style="font-size: 4rem;"></i>
                    <h3 class="mt-4">Process Completed</h3>
                    <p class="text-muted mb-4">Final Report has been approved. No further action is required.</p>
                    <div class="completion-details mb-4">
                        <div class="row justify-content-center">
                            <div class="col-md-6">
                                <div class="card">
                                    <div class="card-body">
                                        <h6 class="card-title">Document Information</h6>
                                        <p><strong>Claim Ref:</strong> {{ $document_no }}</p>
                                        <p><strong>Insured:</strong> {{ $insured_name }}</p>
                                        <p><strong>Policy:</strong> {{ $policy_name }}</p>
                                        <p><strong>Loss Cause:</strong> {{ $loss_cause }}</p>
                                        <p><strong>Status:</strong> <span class="badge bg-success">Final Report Approved</span></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <a href="{{ route('home') }}" class="btn btn-warning">
                        <i class="bi bi-arrow-left me-2"></i>Return to Dashboard
                    </a>
                </div>
                @endif

                @if(!$hideUploadForm)
                <!-- Guidelines Section with Claim Info -->
                <div class="guidelines-section">
                    <div class="guidelines-icon">
                        <i class="bi bi-info-circle"></i>
                    </div>
                    <div class="guidelines-text">
                        <p class="guidelines-title">Upload Instructions</p>
                        <p class="guidelines-desc">Please upload all relevant documents for your claim. Supported formats: <strong>PDF, JPG, JPEG, PNG</strong> (Max 5MB per file, 18MB total)</p>
                        
                        <!-- Claim Info Grid -->
                        <div class="claim-info-grid">
                            <div class="claim-info-item">
                                <span class="claim-info-label">
                                    <i class="bi bi-tag"></i>
                                    Claim Ref:
                                </span>
                                <span class="claim-info-value claim-ref">{{ $document_no }}</span>
                            </div>
                            <div class="claim-info-item">
                                <span class="claim-info-label">
                                    <i class="bi bi-person"></i>
                                    Insured:
                                </span>
                                <span class="claim-info-value">{{ $insured_name }}</span>
                            </div>
                            
                            <div class="claim-info-item">
                                <span class="claim-info-label">
                                    <i class="bi bi-file-text"></i>
                                    Policy:
                                </span>
                                <span class="claim-info-value">{{ $policy_name }}</span>
                            </div>
                            
                            <div class="claim-info-item">
                                <span class="claim-info-label">
                                    <i class="bi bi-exclamation-triangle"></i>
                                    Loss Cause:
                                </span>
                                <span class="claim-info-value">{{ $loss_cause }}</span>
                            </div>
                            
                            <div class="claim-info-item">
                                <span class="claim-info-label">
                                    <i class="bi bi-file-earmark-text"></i>
                                    Report Type:
                                </span>
                                <span class="claim-info-value {{ $isPlrApproved ? 'final-report' : 'preliminary-report' }}">
                                    @if($isFrApproved)
                                        Final Report (F/R) - Approved
                                    @elseif($isPlrApproved)
                                        Final Report (F/R)
                                    @else
                                        Preliminary Report (P/R)
                                    @endif
                                </span>
                            </div>
                            
                            <!-- Current Status Badge -->
                            @if(isset($plr_final_status))
                            <div class="claim-info-item">
                                <span class="claim-info-label">
                                    <i class="bi bi-flag"></i>
                                    Status:
                                </span>
                                <span class="claim-info-value status-badge status-{{ $plr_final_status }}">
                                    @if($isFrApproved) Final Report Approved
                                    @elseif($plr_final_status == 'Y') Approved
                                    @elseif($plr_final_status == 'R') Reminder Sent
                                    @elseif($plr_final_status == 'V') Revision Required
                                    @elseif($plr_final_status == 'I') In Review
                                    @elseif($plr_final_status == 'N') Not Approved
                                    @else Pending @endif
                                </span>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Upload Form -->
                <form action="{{ route('upload.document', $document_no) }}" method="POST" enctype="multipart/form-data" id="uploadForm">
                    @csrf
                    
                    <!-- Hidden input for report type -->
                    <input type="hidden" name="report_type" value="{{ $isPlrApproved ? 'F/R' : 'P/R' }}">
                    
                    <div class="upload-rows-container" id="uploadRowsContainer">
                        <!-- Upload Row -->
                        <div class="upload-row">
                            <!-- Drag and Drop Area -->
                            <div class="upload-section">
                                <div class="file-upload-zone">
                                    <input type="file" name="document[]" class="form-control file-input" accept=".pdf,.jpg,.jpeg,.png" required>
                                    <input type="hidden" name="upload_date[]" value="{{ $today }}">
                                    
                                    <div class="upload-area">
                                        <div class="upload-content">
                                            <i class="bi bi-cloud-arrow-up upload-icon"></i>
                                            <p class="upload-primary">Drag & Drop or Click to Browse</p>
                                            <p class="upload-secondary">PDF, JPG, PNG up to 5MB</p>
                                        </div>
                                    </div>
                                    
                                    <div class="file-info" style="display: none;">
                                        <div class="file-preview">
                                            <img class="img-preview" style="display: none;">
                                            <div class="pdf-preview" style="display: none;">
                                                <i class="bi bi-file-earmark-pdf-fill"></i>
                                                <span class="pdf-name"></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Preview and Remarks Section -->
                            <div class="preview-remarks-section">
                                <div class="preview-section">
                                    <p class="section-label">Preview</p>
                                    <div class="file-preview-container">
                                        <img src="" alt="Preview" class="document-preview" style="display: none;">
                                        <div class="pdf-indicator" style="display: none;">
                                            <i class="bi bi-file-earmark-pdf-fill"></i>
                                            <span class="pdf-filename"></span>
                                            <small class="pdf-size text-muted"></small>
                                        </div>
                                        <div class="no-preview">
                                            <i class="bi bi-image"></i>
                                            <span>No file selected</span>
                                        </div>
                                    </div>
                                </div>

                                <div class="remarks-section">
                                    <!-- Estimate Amount Field -->
                                    <div class="estimate-amount-wrapper">
                                        <p class="section-label">Estimate Amount <small class="text-muted">(Optional)</small></p>
                                        <div class="amount-input-wrapper">
                                            <div class="input-group">
                                                <span class="input-group-text">Rs</span>
                                                <input type="number" name="estimate_amount[]" class="form-control estimate-amount" placeholder="Enter amount" min="0" step="0.01" value="0.0">
                                            </div>
                                            <small class="amount-hint">Enter estimated cost for this document</small>
                                        </div>
                                    </div>

                                    <!-- Notes/Remarks Field -->
                                    <p class="section-label">Notes / Remarks <small class="text-muted">(Optional)</small></p>
                                    <div class="remarks-wrapper">
                                        <textarea name="remarks[]" class="form-control remarks-input" rows="3" placeholder="Add notes or remarks (optional)"></textarea>
                                        <div class="remarks-overlay">
                                            <i class="bi bi-pencil-square"></i>
                                        </div>
                                    </div>
                                </div>
                                
                            </div>

                            <!-- Remove Button -->
                            <div class="action-section">
                                <button type="button" class="btn-remove remove-row" title="Remove">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Form Actions -->
                    <div class="form-actions">
                        <button type="button" class="btn-add" id="addRow">
                            <i class="bi bi-plus-circle me-2"></i>
                            Add Another Document
                        </button>
                        
                        <div class="action-buttons">
                            <a href="{{ route('home') }}" class="btn-cancel">
                                <i class="bi bi-x-circle me-2"></i>
                                Cancel
                            </a>
                            @php
                                // Determine button text and class based on report type
                                $uploadButtonText = 'Upload Preliminary Report';
                                $buttonClass = 'btn-upload btn-upload-preliminary'; // Green for P/R
                                
                                if ($isPlrApproved) {
                                    $uploadButtonText = 'Upload Final Report';
                                    $buttonClass = 'btn-upload btn-upload-final'; // Blue for F/R
                                } 
                                elseif (isset($plr_final_status) && ($plr_final_status == 'R' || $plr_final_status == 'V')) {
                                    if (isset($actionIsForFR) && $actionIsForFR) {
                                        $uploadButtonText = 'Submit Final Report';
                                        $buttonClass = 'btn-upload btn-upload-final'; // Blue for F/R
                                    } else {
                                        $uploadButtonText = 'Submit Preliminary Report';
                                        $buttonClass = 'btn-upload btn-upload-preliminary'; // Green for P/R
                                    }
                                }
                            @endphp
                            
                            <button type="submit" class="{{ $buttonClass }}" id="uploadAllBtn" disabled>
                                <i class="bi bi-upload me-2"></i>
                                <span id="uploadBtnText">{{ $uploadButtonText }}</span>
                                <span class="upload-counter">(0)</span>
                            </button>
                        </div>
                    </div>
                </form>
                @endif
            </div>
        </div>

        <!-- Loading Overlay -->
        <div id="loadingOverlay" style="display:none;">
            <div class="loader-content">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
                <p>Please wait. Your request is being processed. Documents are being uploaded and the email is being sent....</p>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const uploadForm = document.getElementById('uploadForm');
            const loadingOverlay = document.getElementById('loadingOverlay');
            const addRowBtn = document.getElementById('addRow');
            const rowsContainer = document.getElementById('uploadRowsContainer');
            const uploadAllBtn = document.getElementById('uploadAllBtn');
            const uploadCounter = document.querySelector('.upload-counter');
            const uploadBtnText = document.getElementById('uploadBtnText');
            
            let selectedFilesCount = 0;

            if (uploadForm) {
                uploadForm.addEventListener('submit', function(e) {
                    // Validate file selection
                    const fileInputs = document.querySelectorAll('input[type="file"]');
                    const hasFiles = Array.from(fileInputs).some(input => input.files.length > 0);
                    
                    if (!hasFiles) {
                        e.preventDefault();
                        showToast('Please select at least one file to upload', 'warning');
                        return;
                    }
                    
                    loadingOverlay.style.display = 'flex';
                });
            }

            function updateUploadButtonState() {
                if (!uploadAllBtn) return;
                
                const fileInputs = document.querySelectorAll('input[type="file"]');
                selectedFilesCount = Array.from(fileInputs).filter(input => input.files.length > 0).length;

                uploadAllBtn.disabled = selectedFilesCount === 0;
                if (uploadCounter) {
                    uploadCounter.textContent = `(${selectedFilesCount})`;
                }

                if (selectedFilesCount > 0) {
                    uploadAllBtn.classList.add('has-files');
                } else {
                    uploadAllBtn.classList.remove('has-files');
                }
            }

            // Function to update button class based on context
            function updateUploadButtonClass() {
                if (!uploadAllBtn) return;
                
                const isFrApproved = @json($isFrApproved ?? false);
                const isPlrApproved = @json($isPlrApproved ?? false);
                const plrFinalStatus = @json($plr_final_status ?? null);
                const actionIsForFR = @json($actionIsForFR ?? false);
                
                let newButtonClass = 'btn-upload btn-upload-preliminary';
                let newButtonText = 'Upload Preliminary Report';
                
                if (isPlrApproved) {
                    newButtonClass = 'btn-upload btn-upload-final';
                    newButtonText = 'Upload Final Report';
                } 
                else if (plrFinalStatus === 'R' || plrFinalStatus === 'V') {
                    if (actionIsForFR) {
                        newButtonClass = 'btn-upload btn-upload-final';
                        newButtonText = 'Submit Final Report';
                    } else {
                        newButtonClass = 'btn-upload btn-upload-preliminary';
                        newButtonText = 'Submit Preliminary Report';
                    }
                }
                
                // Update button class
                uploadAllBtn.className = newButtonClass;
                
                // Update button text
                if (uploadBtnText) {
                    uploadBtnText.textContent = newButtonText;
                }
            }

            function handleFileChange(input) {
                const row = input.closest('.upload-row');
                const uploadArea = row.querySelector('.upload-area');
                const fileInfo = row.querySelector('.file-info');
                const imgPreview = row.querySelector('.img-preview');
                const pdfPreview = row.querySelector('.pdf-preview');
                const pdfName = row.querySelector('.pdf-name');
                const documentPreview = row.querySelector('.document-preview');
                const pdfIndicator = row.querySelector('.pdf-indicator');
                const pdfFilename = row.querySelector('.pdf-filename');
                const noPreview = row.querySelector('.no-preview');
                const pdfSize = row.querySelector('.pdf-size');
                const imageSizeIndicator = row.querySelector('.image-size-indicator');

                if (imgPreview) imgPreview.style.display = 'none';
                if (pdfPreview) pdfPreview.style.display = 'none';
                if (documentPreview) documentPreview.style.display = 'none';
                if (pdfIndicator) pdfIndicator.style.display = 'none';
                if (noPreview) noPreview.style.display = 'flex';
                if (fileInfo) fileInfo.style.display = 'none';
                if (uploadArea) uploadArea.style.display = 'flex';
                if (pdfSize) pdfSize.textContent = '';
                if (imageSizeIndicator) imageSizeIndicator.style.display = 'none';

                if (input.files.length > 0) {
                    const file = input.files[0];
                    const allowedTypes = ['image/jpeg', 'image/png', 'image/jpg', 'application/pdf'];

                    if (!allowedTypes.includes(file.type)) {
                        showToast('Please select a valid file format (PDF, JPG, PNG)', 'warning');
                        input.value = '';
                        return;
                    }

                    const MAX_FILE_MB = 5;
                    const MAX_TOTAL_MB = 18;
                    const MAX_FILE_BYTES = MAX_FILE_MB * 1024 * 1024;
                    const MAX_TOTAL_BYTES = MAX_TOTAL_MB * 1024 * 1024;

                    let totalSize = 0;
                    const fileInputs = document.querySelectorAll('input[type="file"]');

                    for (let inp of fileInputs) {
                        if (inp.files.length === 0) continue;
                        for (let f of inp.files) {
                            if (f.size > MAX_FILE_BYTES) {
                                const fileSizeMB = (f.size / (1024 * 1024)).toFixed(2);
                                showToast(`The file "${f.name}" is ${fileSizeMB} MB. Each file must be under ${MAX_FILE_MB} MB.`, 'error');
                                input.value = '';
                                return;
                            }
                            totalSize += f.size;
                        }
                    }

                    if (totalSize > MAX_TOTAL_BYTES) {
                        const totalSizeMB = (totalSize / (1024 * 1024)).toFixed(2);
                        showToast(`Total upload is ${totalSizeMB} MB. Maximum allowed is ${MAX_TOTAL_MB} MB for all files combined.`, 'error');
                        fileInputs.forEach(inp => inp.value = '');
                        return;
                    }

                    if (uploadArea) uploadArea.style.display = 'none';
                    if (fileInfo) fileInfo.style.display = 'block';
                    if (noPreview) noPreview.style.display = 'none';

                    // Calculate human readable size
                    let size = file.size;
                    let displaySize = size < 1024 * 1024 ?
                        (size / 1024).toFixed(2) + " KB" :
                        (size / (1024 * 1024)).toFixed(2) + " MB";

                    if (file.type.startsWith('image/')) {
                        const reader = new FileReader();
                        reader.onload = function(e) {
                            if (imgPreview) {
                                imgPreview.src = e.target.result;
                                imgPreview.style.display = 'block';
                            }
                            if (documentPreview) {
                                documentPreview.src = e.target.result;
                                documentPreview.style.display = 'block';
                            }
                            
                            // Display file size for images
                            if (imageSizeIndicator) {
                                imageSizeIndicator.textContent = displaySize;
                                imageSizeIndicator.style.display = 'block';
                            }
                        }
                        reader.readAsDataURL(file);
                    } else if (file.type === 'application/pdf') {
                        if (pdfPreview) {
                            pdfPreview.style.display = 'flex';
                        }
                        if (pdfName) {
                            pdfName.textContent = file.name;
                        }
                        if (pdfIndicator) {
                            pdfIndicator.style.display = 'flex';
                        }
                        if (pdfFilename) {
                            pdfFilename.textContent = file.name;
                        }
                        
                        // Display file size for PDFs in preview
                        if (pdfSize) {
                            pdfSize.textContent = displaySize;
                            pdfSize.style.display = 'block';
                        }
                    }

                    if (row) {
                        row.classList.add('file-selected');
                    }
                } else {
                    if (row) {
                        row.classList.remove('file-selected');
                    }
                }

                updateUploadButtonState();
            }

            if (addRowBtn) {
                addRowBtn.addEventListener('click', function() {
                    const newRow = document.querySelector('.upload-row').cloneNode(true);
                    const fileInput = newRow.querySelector('input[type="file"]');
                    const textarea = newRow.querySelector('textarea');
                    const uploadArea = newRow.querySelector('.upload-area');
                    const fileInfo = newRow.querySelector('.file-info');
                    const noPreview = newRow.querySelector('.no-preview');

                    if (fileInput) fileInput.value = '';
                    if (textarea) textarea.value = '';

                    const estimateAmountInput = newRow.querySelector('.estimate-amount');
                    if (estimateAmountInput) {
                        estimateAmountInput.value = '';
                    }
                    
                    if (uploadArea) uploadArea.style.display = 'flex';
                    if (fileInfo) fileInfo.style.display = 'none';
                    if (noPreview) noPreview.style.display = 'flex';
                    newRow.classList.remove('file-selected');

                    const imgPreview = newRow.querySelector('.img-preview');
                    const pdfPreview = newRow.querySelector('.pdf-preview');
                    const documentPreview = newRow.querySelector('.document-preview');
                    const pdfIndicator = newRow.querySelector('.pdf-indicator');
                    const imageSizeIndicator = newRow.querySelector('.image-size-indicator');

                    if (imgPreview) {
                        imgPreview.style.display = 'none';
                        imgPreview.src = '';
                    }
                    if (pdfPreview) pdfPreview.style.display = 'none';
                    if (documentPreview) {
                        documentPreview.style.display = 'none';
                    }
                    if (pdfIndicator) pdfIndicator.style.display = 'none';
                    if (imageSizeIndicator) imageSizeIndicator.style.display = 'none';

                    if (rowsContainer) {
                        rowsContainer.appendChild(newRow);
                    }
                    updateUploadButtonState();

                    newRow.scrollIntoView({ behavior: 'smooth', block: 'center' });
                });
            }

            if (rowsContainer) {
                rowsContainer.addEventListener('click', function(e) {
                    if (e.target.closest('.remove-row')) {
                        const rows = rowsContainer.querySelectorAll('.upload-row');
                        if (rows.length > 1) {
                            e.target.closest('.upload-row').remove();
                            updateUploadButtonState();
                        } else {
                            showToast('At least one document row is required', 'warning');
                        }
                    }

                    const uploadArea = e.target.closest('.upload-area');
                    if (uploadArea) {
                        const fileInput = uploadArea.closest('.file-upload-zone').querySelector('.file-input');
                        if (fileInput) {
                            fileInput.click();
                        }
                    }
                });

                rowsContainer.addEventListener('change', function(e) {
                    if (e.target.classList.contains('file-input')) {
                        handleFileChange(e.target);
                    }
                });

                rowsContainer.addEventListener('dragover', function(e) {
                    const uploadArea = e.target.closest('.upload-area');
                    if (uploadArea) {
                        e.preventDefault();
                        uploadArea.classList.add('drag-over');
                    }
                });

                rowsContainer.addEventListener('dragleave', function(e) {
                    const uploadArea = e.target.closest('.upload-area');
                    if (uploadArea && !uploadArea.contains(e.relatedTarget)) {
                        uploadArea.classList.remove('drag-over');
                    }
                });

                rowsContainer.addEventListener('drop', function(e) {
                    const uploadArea = e.target.closest('.upload-area');
                    if (uploadArea) {
                        e.preventDefault();
                        const fileInput = uploadArea.closest('.file-upload-zone').querySelector('.file-input');
                        if (fileInput) {
                            fileInput.files = e.dataTransfer.files;
                            handleFileChange(fileInput);
                            uploadArea.classList.remove('drag-over');
                        }
                    }
                });
            }

            function showToast(message, type = 'success') {
                const toastContainer = document.getElementById('toastContainer');
                if (!toastContainer) return;
                
                const toastId = 'toast' + Date.now();
                const bgClass = type === 'success' ? 'bg-success' : type === 'warning' ? 'bg-warning' : 'bg-danger';

                const toastHtml = `
                    <div id="${toastId}" class="toast align-items-center ${bgClass} text-white border-0 mb-2" role="alert">
                        <div class="d-flex">
                            <div class="toast-body">
                                <i class="bi bi-${type === 'success' ? 'check-circle' : type === 'warning' ? 'exclamation-triangle' : 'x-circle'} me-2"></i>
                                ${message}
                            </div>
                            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
                        </div>
                    </div>
                `;
                toastContainer.insertAdjacentHTML('beforeend', toastHtml);
                const toastEl = document.getElementById(toastId);
                const bsToast = new bootstrap.Toast(toastEl, { delay: 5000 });
                bsToast.show();
            }

            // Initialize button
            updateUploadButtonClass();
            updateUploadButtonState();

            @if (session('success'))
                showToast("{{ session('success') }}", 'success');
            @endif
            @if (session('error'))
                showToast("{{ session('error') }}", 'danger');
            @endif
            
            // Auto-refresh status check every 30 seconds
            setInterval(checkDocumentStatus, 30000);
        });
        
        // Function to check document status via AJAX
        function checkDocumentStatus() {
            const docNo = '{{ $document_no }}';
            
            fetch(`/api/check-document-status?doc_no=${docNo}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        updateStatusDisplay(data.plr_final, data.isPlrApproved, data.isFrApproved, data.actionIsForFR);
                    }
                })
                .catch(error => {
                    console.error('Error checking document status:', error);
                });
        }
        
        // Function to update status display
        function updateStatusDisplay(plrFinal, isPlrApproved, isFrApproved, actionIsForFR) {
            const statusAlert = document.querySelector('.status-alert');
            const statusBadge = document.querySelector('.status-badge');
            const uploadForm = document.getElementById('uploadForm');
            const completionState = document.querySelector('.completion-state');
            const guidelinesSection = document.querySelector('.guidelines-section');
            const reportTypeDisplay = document.querySelector('.claim-info-value.final-report, .claim-info-value.preliminary-report');
            const uploadAllBtn = document.getElementById('uploadAllBtn');
            const uploadBtnText = document.getElementById('uploadBtnText');
            
            let message = '';
            let type = 'info';
            let icon = 'bi-info-circle-fill';
            
            if (isFrApproved) {
                message = 'Final Report (F/R) has been approved! Process completed. No further uploads required.';
                type = 'success';
                icon = 'bi-check-circle-fill';
                
                // Hide upload form and show completion state
                if (uploadForm) uploadForm.style.display = 'none';
                if (guidelinesSection) guidelinesSection.style.display = 'none';
                if (uploadAllBtn) uploadAllBtn.style.display = 'none';
                
                // Show completion state if not already shown
                if (!completionState) {
                    showCompletionState();
                }
                
                // Update status badge
                if (statusBadge) {
                    statusBadge.textContent = 'Final Report Approved';
                    statusBadge.className = 'claim-info-value status-badge status-Y';
                }
                
                // Update report type display
                if (reportTypeDisplay) {
                    reportTypeDisplay.textContent = 'Final Report (F/R) - Approved';
                    reportTypeDisplay.className = 'claim-info-value final-report';
                }
                
            } else if (isPlrApproved) {
                message = 'Preliminary Report (P/R) has been Approved! You can now upload the Final Report (F/R).';
                type = 'success';
                icon = 'bi-check-circle-fill';
                
                // Show upload form for F/R
                if (uploadForm) uploadForm.style.display = 'block';
                if (guidelinesSection) guidelinesSection.style.display = 'flex';
                if (uploadAllBtn) uploadAllBtn.style.display = 'flex';
                
                // Update button text and class
                if (uploadAllBtn && uploadBtnText) {
                    uploadAllBtn.className = 'btn-upload btn-upload-final';
                    uploadBtnText.textContent = 'Upload Final Report';
                }
                
                // Update report type display
                if (reportTypeDisplay) {
                    reportTypeDisplay.textContent = 'Final Report (F/R)';
                    reportTypeDisplay.className = 'claim-info-value final-report';
                }
                
            } else if (plrFinal === 'R') {
                message = 'Admin has sent a reminder. Please check your email and proceed accordingly.';
                type = 'warning';
                icon = 'bi-bell-fill';
                
                // Show upload form
                if (uploadForm) uploadForm.style.display = 'block';
                if (guidelinesSection) guidelinesSection.style.display = 'flex';
                if (uploadAllBtn) uploadAllBtn.style.display = 'flex';
                
                // Update button based on context
                if (uploadAllBtn && uploadBtnText) {
                    if (actionIsForFR) {
                        uploadAllBtn.className = 'btn-upload btn-upload-final';
                        uploadBtnText.textContent = 'Submit Final Report';
                    } else {
                        uploadAllBtn.className = 'btn-upload btn-upload-preliminary';
                        uploadBtnText.textContent = 'Submit Preliminary Report';
                    }
                }
                
            } else if (plrFinal === 'V') {
                message = 'This report requires revision. Please review admin comments in your email and resubmit.';
                type = 'danger';
                icon = 'bi-exclamation-triangle-fill';
                
                // Show upload form
                if (uploadForm) uploadForm.style.display = 'block';
                if (guidelinesSection) guidelinesSection.style.display = 'flex';
                if (uploadAllBtn) uploadAllBtn.style.display = 'flex';
                
                // Update button based on context
                if (uploadAllBtn && uploadBtnText) {
                    if (actionIsForFR) {
                        uploadAllBtn.className = 'btn-upload btn-upload-final';
                        uploadBtnText.textContent = 'Submit Final Report';
                    } else {
                        uploadAllBtn.className = 'btn-upload btn-upload-preliminary';
                        uploadBtnText.textContent = 'Submit Preliminary Report';
                    }
                }
                
            } else if (plrFinal === 'I') {
                message = 'Report is currently under review. Please wait for admin approval or further instructions.';
                type = 'info';
                icon = 'bi-hourglass-split';
                
            } else if (plrFinal === 'N') {
                message = 'Report was not approved. Please check for admin comments and resubmit.';
                type = 'secondary';
                icon = 'bi-x-circle-fill';
                
            } else {
                message = 'Please upload the Preliminary Report (P/R) for Admin approval.';
                type = 'info';
                icon = 'bi-info-circle-fill';
                
                // Update button text and class
                if (uploadAllBtn && uploadBtnText) {
                    uploadAllBtn.className = 'btn-upload btn-upload-preliminary';
                    uploadBtnText.textContent = 'Upload Preliminary Report';
                }
                
                // Update report type display
                if (reportTypeDisplay) {
                    reportTypeDisplay.textContent = 'Preliminary Report (P/R)';
                    reportTypeDisplay.className = 'claim-info-value preliminary-report';
                }
            }
            
            // Update status alert
            if (statusAlert) {
                statusAlert.innerHTML = `<i class="bi ${icon} me-2"></i>${message}`;
                statusAlert.className = `alert alert-${type} status-alert`;
            }
            
            // Update status badge if exists
            if (statusBadge && !isFrApproved) {
                let statusText = 'Pending';
                if (plrFinal === 'Y') statusText = 'Approved';
                else if (plrFinal === 'R') statusText = 'Reminder Sent';
                else if (plrFinal === 'V') statusText = 'Revision Required';
                else if (plrFinal === 'I') statusText = 'In Review';
                else if (plrFinal === 'N') statusText = 'Not Approved';
                
                statusBadge.textContent = statusText;
                statusBadge.className = `claim-info-value status-badge status-${plrFinal || 'pending'}`;
            }
        }
        
        function showCompletionState() {
            const uploadContainer = document.querySelector('.upload-container');
            if (!uploadContainer) return;
            
            // Check if completion state already exists
            if (document.querySelector('.completion-state')) return;
            
            const completionHTML = `
                <div class="completion-state text-center py-5" style="background: #f8f9fa; border-radius: 10px; margin: 2rem 0;">
                    <i class="bi bi-check-circle-fill text-success" style="font-size: 4rem;"></i>
                    <h3 class="mt-4">Process Completed</h3>
                    <p class="text-muted mb-4">Final Report has been approved. No further action is required.</p>
                    <div class="completion-details mb-4">
                        <div class="row justify-content-center">
                            <div class="col-md-6">
                                <div class="card">
                                    <div class="card-body">
                                        <h6 class="card-title">Document Information</h6>
                                        <p><strong>Claim Ref:</strong> {{ $document_no }}</p>
                                        <p><strong>Insured:</strong> {{ $insured_name }}</p>
                                        <p><strong>Policy:</strong> {{ $policy_name }}</p>
                                        <p><strong>Loss Cause:</strong> {{ $loss_cause }}</p>
                                        <p><strong>Status:</strong> <span class="badge bg-success">Final Report Approved</span></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <a href="{{ route('home') }}" class="btn btn-primary">
                        <i class="bi bi-arrow-left me-2"></i>Return to Dashboard
                    </a>
                </div>
            `;
            
            // Insert after the status alert
            const statusAlert = document.querySelector('.status-alert');
            if (statusAlert) {
                statusAlert.insertAdjacentHTML('afterend', completionHTML);
            } else {
                uploadContainer.insertAdjacentHTML('afterbegin', completionHTML);
            }
            
            // Hide any existing upload form
            const uploadForm = document.getElementById('uploadForm');
            const guidelinesSection = document.querySelector('.guidelines-section');
            const uploadAllBtn = document.getElementById('uploadAllBtn');
            if (uploadForm) uploadForm.style.display = 'none';
            if (guidelinesSection) guidelinesSection.style.display = 'none';
            if (uploadAllBtn) uploadAllBtn.style.display = 'none';
        }
    </script>

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        .upload-wrapper {
            min-height: 100vh;
            background: #f5f7fa;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            max-height: 100vh;
            overflow: hidden;
        }

         .page-header {
            background: linear-gradient(135deg, #1e3a8a 0%, #3b82f6 100%);
            color: white;
            padding: 1.5rem 0;
            width: 90%;
            margin: 2vw auto 0 auto;
            color: white;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
            border-radius: 10px;
            position: relative;
        }

        .header-content {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 1rem;
        }

        .page-title {
            font-size: 1.75rem;
            font-weight: 600;
            margin: 0;
            letter-spacing: -0.5px;
        }

        .main-content {
            padding: 1.5rem 0;
            max-width: 1200px;
            margin: 0 auto;
            height: calc(100vh - 200px);
            overflow-y: auto;
        }

        /* Remove scrollbar for Chrome, Safari and Opera */
        .main-content::-webkit-scrollbar {
            display: none;
        }

        /* Hide scrollbar for IE, Edge and Firefox */
        .main-content {
            -ms-overflow-style: none;  /* IE and Edge */
            scrollbar-width: none;  /* Firefox */
        }

        .upload-container {
            background: white;
            border-radius: 12px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
            padding: 1.5rem;
        }

        .status-alert {
            margin-bottom: 1.5rem;
            border: none;
            border-radius: 8px;
            padding: 0.75rem 1rem;
            font-size: 0.9rem;
            font-weight: 500;
            animation: fadeIn 0.5s ease;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* Status badge styles */
        .status-badge {
            font-weight: 600 !important;
            padding: 0.25rem 0.75rem !important;
            border-radius: 20px !important;
            font-size: 0.75rem !important;
        }

        .status-Y {
            background: rgba(25, 135, 84, 0.15) !important;
            color: #198754 !important;
            border: 1px solid #198754;
        }

        .status-R {
            background: rgba(255, 193, 7, 0.15) !important;
            color: #856404 !important;
            border: 1px solid #ffc107;
        }

        .status-V {
            background: rgba(220, 53, 69, 0.15) !important;
            color: #dc3545 !important;
            border: 1px solid #dc3545;
        }

        .status-I {
            background: rgba(13, 110, 253, 0.15) !important;
            color: #0d6efd !important;
            border: 1px solid #0d6efd;
        }

        .status-N {
            background: rgba(108, 117, 125, 0.15) !important;
            color: #6c757d !important;
            border: 1px solid #6c757d;
        }

        .status-pending {
            background: rgba(255, 193, 7, 0.1) !important;
            color: #856404 !important;
            border: 1px dashed #ffc107;
        }

        /* Enhanced Guidelines Section with Claim Info */
        .guidelines-section {
            display: flex;
            align-items: flex-start;
            gap: 1rem;
            background: #eff6ff;
            border-left: 4px solid #3b82f6;
            padding: 1rem;
            border-radius: 8px;
            margin-bottom: 1.5rem;
        }

        .guidelines-icon {
            flex-shrink: 0;
            width: 35px;
            height: 35px;
            background: #3b82f6;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 1.1rem;
        }

        .guidelines-text {
            flex: 1;
        }

        .guidelines-title {
            font-weight: 600;
            color: #1e3a8a;
            margin-bottom: 0.5rem;
            font-size: 0.95rem;
        }

        .guidelines-desc {
            color: #475569;
            margin: 0 0 0.75rem 0;
            font-size: 0.85rem;
            line-height: 1.4;
        }

        /* Claim Info Grid */
        .claim-info-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 0.75rem;
            margin-top: 0.75rem;
            padding-top: 0.75rem;
            border-top: 1px solid rgba(59, 130, 246, 0.2);
        }

        .claim-info-item {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-size: 0.8rem;
        }

        .claim-info-label {
            display: flex;
            align-items: center;
            gap: 0.35rem;
            color: #1e3a8a;
            font-weight: 600;
            flex-shrink: 0;
            white-space: nowrap;
        }

        .claim-info-label i {
            font-size: 0.8rem;
            opacity: 0.8;
        }

        .claim-info-value {
            color: #334155;
            font-weight: 500;
            background: rgba(59, 130, 246, 0.08);
            padding: 0.2rem 0.5rem;
            border-radius: 4px;
            flex: 1;
            font-size: 0.8rem;
            line-height: 1.3;
            overflow-wrap: break-word;
            word-break: break-word;
        }

        .claim-info-value.claim-ref {
            font-weight: 700;
            color: #1e3a8a;
            background: rgba(30, 58, 138, 0.1);
            font-family: 'Courier New', monospace;
        }

        .claim-info-value.preliminary-report {
            color: #0d6efd;
            background: rgba(13, 110, 253, 0.1);
            font-weight: 600;
        }

        .claim-info-value.final-report {
            color: #198754;
            background: rgba(25, 135, 84, 0.1);
            font-weight: 600;
        }

        .upload-rows-container {
            display: flex;
            flex-direction: column;
            gap: 1rem;
            margin-bottom: 1.5rem;
        }

        .upload-row {
            display: grid;
            grid-template-columns: 280px 1fr 60px;
            gap: 1.5rem;
            padding: 1.5rem;
            border: 2px solid #e5e7eb;
            border-radius: 10px;
            transition: all 0.3s ease;
            background: #fafbfc;
            align-items: start;
        }

        .upload-row:hover {
            border-color: #3b82f6;
            box-shadow: 0 4px 12px rgba(59, 130, 246, 0.1);
        }

        .upload-row.file-selected {
            background: #f0fdf4;
            border-color: #22c55e;
        }

        .upload-section {
            width: 280px;
        }

        .file-upload-zone {
            position: relative;
            height: 180px;
        }

        .file-input {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            opacity: 0;
            cursor: pointer;
            z-index: 2;
        }

        .upload-area {
            border: 2px dashed #cbd5e1;
            border-radius: 8px;
            padding: 1rem;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s ease;
            background: white;
            height: 180px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .file-upload-zone:hover .upload-area {
            border-color: #007bff;
            background: #f8fafc;
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(0, 123, 255, 0.15);
        }

        .upload-area.drag-over {
            border-color: #22c55e;
            background: #f0fdf4;
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(34, 197, 94, 0.15);
        }

        .upload-content {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 0.5rem;
        }

        .upload-icon {
            font-size: 1.75rem;
            color: #64748b;
            transition: color 0.3s ease;
        }

        .file-upload-zone:hover .upload-icon {
            color: #3b82f6;
        }

        .upload-primary {
            font-weight: 600;
            color: #334155;
            font-size: 0.85rem;
            margin: 0;
        }

        .upload-secondary {
            font-size: 0.75rem;
            color: #64748b;
            margin: 0;
        }

        .file-info {
            background: #f0fdf4;
            border: 2px solid #22c55e;
            border-radius: 8px;
            padding: 0.75rem;
            text-align: center;
            height: 180px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .file-preview {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 1rem;
        }

        .img-preview {
            max-width: 70px;
            max-height: 50px;
            border-radius: 8px;
            border: 1px solid #dee2e6;
            transition: transform 0.3s ease;
        }

        .img-preview:hover {
            transform: scale(1.1);
        }

        .pdf-preview {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            font-weight: 500;
            color: #dc3545;
        }

        .pdf-preview i {
            font-size: 1.75rem;
        }

        .preview-remarks-section {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1.5rem;
        }

        .section-label {
            font-weight: 600;
            color: #334155;
            margin-bottom: 0.75rem;
            font-size: 0.85rem;
        }

        /* Enhanced Preview Section Styling */
        .preview-section {
            display: flex;
            flex-direction: column;
        }

        .file-preview-container {
            height: 120px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            border-radius: 12px;
            border: 2px solid #e9ecef;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .file-preview-container:hover {
            border-color: #007bff;
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(0, 123, 255, 0.15);
        }

        .document-preview {
            max-width: 120px;
            max-height: 140px;
            border-radius: 6px;
            object-fit: cover;
            transition: transform 0.3s ease;
        }

        .document-preview:hover {
            transform: scale(1.05);
        }

        .pdf-indicator {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 0.5rem;
            color: #dc3545;
            text-align: center;
            padding: 0.75rem;
        }

        .pdf-indicator i {
            font-size: 2.5rem;
            transition: transform 0.3s ease;
        }

        .pdf-indicator:hover i {
            transform: scale(1.1);
        }

        .pdf-filename {
            font-size: 0.8rem;
            font-weight: 500;
            max-width: 180px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .pdf-size {
            display: block;
            font-size: 0.7rem;
            margin-top: 0.25rem;
            color: #64748b;
            font-weight: 500;
        }

        .image-size-indicator {
            position: absolute;
            bottom: 6px;
            left: 50%;
            transform: translateX(-50%);
            background: rgba(0, 0, 0, 0.75);
            color: white;
            padding: 0.2rem 0.6rem;
            border-radius: 12px;
            font-size: 0.65rem;
            font-weight: 600;
            backdrop-filter: blur(4px);
        }

        .no-preview {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 0.5rem;
            color: #94a3b8;
        }

        .no-preview i {
            font-size: 2rem;
        }

        .no-preview span {
            font-size: 0.8rem;
        }

        .remarks-section {
            display: flex;
            flex-direction: column;
        }

        .remarks-wrapper {
            position: relative;
            transition: all 0.3s ease;
            height: 100%;
        }

        .remarks-wrapper:hover {
            transform: translateY(-2px);
        }

        .remarks-input {
            width: 100%;
            height: 170px;
            border: 2px solid #e5e7eb;
            border-radius: 8px;
            padding: 0.75rem;
            font-size: 0.85rem;
            resize: vertical;
            transition: all 0.3s ease;
            background: #fafbfc;
        }

        .remarks-input:hover {
            border-color: #007bff;
            background-color: white;
            box-shadow: 0 4px 15px rgba(0, 123, 255, 0.1);
        }

        .remarks-input:focus {
            outline: none;
            border-color: #007bff;
            background-color: white;
            box-shadow: 0 0 0 3px rgba(0, 123, 255, 0.25);
            transform: scale(1.02);
        }

        .remarks-overlay {
            position: absolute;
            top: 8px;
            right: 8px;
            background: rgba(0, 123, 255, 0.1);
            color: #007bff;
            padding: 0.25rem 0.5rem;
            border-radius: 6px;
            font-size: 0.75rem;
            pointer-events: none;
            opacity: 0;
            transition: all 0.3s ease;
        }

        .remarks-wrapper:hover .remarks-overlay {
            opacity: 1;
        }

        .remarks-input:focus + .remarks-overlay {
            opacity: 0;
        }

        .action-section {
            display: flex;
            align-items: center;
            justify-content: center;
            height: 180px;
        }

        .btn-remove {
            background: white;
            border: 2px solid #ef4444;
            color: #ef4444;
            padding: 0.75rem;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            width: 45px;
            height: 45px;
        }

        .btn-remove:hover {
            background: #ef4444;
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(239, 68, 68, 0.3);
        }

        .btn-remove i {
            font-size: 1.25rem;
        }

        .form-actions {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding-top: 1rem;
            border-top: 2px solid #e5e7eb;
            flex-wrap: wrap;
            gap: 1rem;
        }

        .action-buttons {
            display: flex;
            gap: 1rem;
        }

        .btn-add {
            background: linear-gradient(135deg, #22c55e 0%, #16a34a 100%);
            color: white;
            border: none;
            padding: 0.75rem 1.5rem;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
        }

        .btn-add:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(34, 197, 94, 0.3);
        }

        .btn-cancel {
            background: white;
            color: #64748b;
            border: 2px solid #e5e7eb;
            padding: 0.75rem 1.5rem;
            border-radius: 8px;
            font-weight: 600;
            text-decoration: none;
            display: flex;
            align-items: center;
            transition: all 0.3s ease;
        }

        .btn-cancel:hover {
            border-color: #cbd5e1;
            background: #f8fafc;
        }

        /* Upload Button Styles - Different colors for P/R and F/R */
        .btn-upload {
            color: white !important;
            border: none !important;
            padding: 0.75rem 1.5rem !important;
            border-radius: 8px !important;
            font-weight: 600 !important;
            cursor: pointer !important;
            transition: all 0.3s ease !important;
            display: flex !important;
            align-items: center !important;
        }

        .btn-upload:disabled {
            background: #94a3b8 !important;
            cursor: not-allowed !important;
            opacity: 0.6 !important;
        }

        .btn-upload-preliminary {
            background: linear-gradient(135deg, #22c55e 0%, #16a34a 100%) !important;
        }

        .btn-upload-preliminary:not(:disabled):hover {
            transform: translateY(-2px) !important;
            box-shadow: 0 4px 12px rgba(34, 197, 94, 0.3) !important;
        }

        .btn-upload-preliminary.has-files {
            animation: pulse-green 2s infinite !important;
        }

        .btn-upload-final {
            background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%) !important;
        }

        .btn-upload-final:not(:disabled):hover {
            transform: translateY(-2px) !important;
            box-shadow: 0 4px 12px rgba(59, 130, 246, 0.3) !important;
        }

        .btn-upload-final.has-files {
            animation: pulse-blue 2s infinite !important;
        }

        .upload-counter {
            background: rgba(255, 255, 255, 0.2);
            padding: 0.25rem 0.5rem;
            border-radius: 12px;
            margin-left: 0.5rem;
            font-weight: 700;
        }

        /* Different pulse animations for different colors */
        @keyframes pulse-green {
            0%, 100% {
                box-shadow: 0 0 0 0 rgba(34, 197, 94, 0.4);
            }
            50% {
                box-shadow: 0 0 0 8px rgba(34, 197, 94, 0);
            }
        }

        @keyframes pulse-blue {
            0%, 100% {
                box-shadow: 0 0 0 0 rgba(59, 130, 246, 0.4);
            }
            50% {
                box-shadow: 0 0 0 8px rgba(59, 130, 246, 0);
            }
        }

        @keyframes pulse {
            0%, 100% {
                box-shadow: 0 0 0 0 rgba(59, 130, 246, 0.4);
            }
            50% {
                box-shadow: 0 0 0 8px rgba(59, 130, 246, 0);
            }
        }

        #loadingOverlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.6);
            z-index: 2000;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .loader-content {
            background: white;
            padding: 2rem 2.5rem;
            border-radius: 12px;
            text-align: center;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.3);
        }

        .loader-content p {
            margin-top: 1rem;
            font-weight: 500;
            color: #334155;
        }

        .toast {
            min-width: 320px;
            border-radius: 10px;
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.15);
            font-weight: 500;
        }

        /* Completion State Styles */
        .completion-state {
            animation: fadeInUp 0.5s ease;
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Responsive adjustments */
        @media (max-width: 1024px) {
            .upload-row {
                grid-template-columns: 1fr;
                gap: 1rem;
            }

            .upload-section {
                width: 100%;
            }

            .preview-remarks-section {
                grid-template-columns: 1fr;
                gap: 1rem;
            }

            .action-section {
                justify-content: flex-start;
            }

            .btn-remove {
                width: 100%;
                height: auto;
                padding: 0.75rem 1.5rem;
            }

            .btn-remove::after {
                content: ' Remove Document';
                margin-left: 0.5rem;
            }
        }

        @media (max-width: 768px) {
            .page-header {
                padding: 1rem 0;
                margin: 1rem auto;
            }

            .page-title {
                font-size: 1.5rem;
            }

            .main-content {
                padding: 1rem 0;
                height: calc(100vh - 150px);
            }

            .upload-container {
                padding: 1rem;
            }

            .guidelines-section {
                flex-direction: column;
                padding: 0.75rem;
                gap: 0.75rem;
            }

            .guidelines-icon {
                width: 30px;
                height: 30px;
                font-size: 1rem;
            }

            .claim-info-grid {
                grid-template-columns: 1fr;
                gap: 0.5rem;
            }

            .upload-row {
                padding: 1rem;
            }

            .upload-area, .file-info {
                height: 150px;
            }

            .remarks-input {
                height: 150px;
            }

            .form-actions {
                flex-direction: column;
                gap: 0.75rem;
            }

            .action-buttons {
                flex-direction: column;
                width: 100%;
                gap: 0.75rem;
            }

            .btn-add, .btn-cancel, .btn-upload {
                width: 100%;
                justify-content: center;
                padding: 0.65rem 1rem;
            }
        }

        @media (max-width: 480px) {
            .page-title {
                font-size: 1.25rem;
            }

            .main-content {
                padding: 0.5rem 0;
                height: calc(100vh - 120px);
            }

            .upload-container {
                padding: 0.75rem;
            }

            .status-alert {
                padding: 0.5rem 0.75rem;
                font-size: 0.8rem;
                margin-bottom: 1rem;
            }

            .guidelines-title {
                font-size: 0.9rem;
            }

            .guidelines-desc {
                font-size: 0.8rem;
            }

            .claim-info-item {
                font-size: 0.75rem;
            }

            .upload-row {
                padding: 0.75rem;
            }

            .upload-area, .file-info {
                height: 120px;
                padding: 0.5rem;
            }

            .upload-icon {
                font-size: 1.5rem;
            }

            .upload-primary {
                font-size: 0.8rem;
            }

            .upload-secondary {
                font-size: 0.7rem;
            }

            .remarks-input {
                height: 120px;
                font-size: 0.8rem;
                padding: 0.5rem;
            }
        }

        @media print {
            .upload-wrapper {
                background: white;
            }

            .page-header {
                background: #1e3a8a !important;
            }

            .form-actions,
            .action-section {
                display: none;
            }

            .upload-row {
                break-inside: avoid;
            }
        }
    </style>

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
@endsection