@extends('master')
@section('content')

    <div class="content-body">
        <div class="container-fluid px-2">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header border-0 pb-0">
                            <div class="d-flex flex-column flex-sm-row justify-content-between align-items-start align-items-sm-center w-100">
                                <h4  style="font-family: 'Reddit Sans'; font-weight: 600; letter-spacing: 1px;" class="card-title mb-1 fs-5 fs-md-4">Survey Fee Report</h4>
                                <!-- Back Button -->
                                <a href="{{ url('/') }}" class="btn btn-sm btn-outline-primary back-btn">
                                    <i class="bi bi-arrow-left-circle me-1"></i> Back to Home
                                </a>
                            </div>
                        </div>

                        <div class="card-body p-2 p-md-3">
                            <!-- Mobile-optimized form -->
                            <form method="GET" action="{{ route('survey.report') }}" class="mb-4">
                                <div class="row g-2 g-md-3 align-items-end">
                                    <div class="col-12 col-sm-6 col-md-4">
    <div class="d-flex align-items-center gap-2">
        <label for="datefrom" class="form-label mb-0">From Date</label>
        <div class="input-group input-group-sm flex-grow-1">
            <input type="text" class="form-control datepicker" name="datefrom"
                   value="{{ $datefrom }}" autocomplete="off" placeholder="Select date">
            <button class="btn btn-outline-secondary px-2" type="button" id="datefrom-btn">
                <i class="bi bi-calendar4-week"></i>
            </button>
        </div>
    </div>
</div>

                                    <div class="col-12 col-sm-6 col-md-4">
    <div class="d-flex align-items-center gap-2">
        <label for="dateto" class="form-label mb-0">To Date</label>
        <div class="input-group input-group-sm flex-grow-1">
            <input type="text" class="form-control datepicker" name="dateto"
                   value="{{ $dateto }}" autocomplete="off" placeholder="Select date">
            <button class="btn btn-outline-secondary px-2" type="button" id="dateto-btn">
                <i class="bi bi-calendar4-week"></i>
            </button>
        </div>
    </div>
</div>

                                    <div class="col-12 col-md-4">
                                        <button type="submit" class="btn btn-primary btn-sm w-100 w-md-auto">
                                            <i class="bi bi-send-check-fill me-1"></i>Submit
                                        </button>
                                    </div>
                                </div>
                            </form>

                            @if (empty($data))
                                <div class="text-center py-4">
                                    <i class="bi bi-inbox fs-1 text-muted"></i>
                                    <p class="mt-2 text-muted">No Reports Found!</p>
                                </div>
                            @elseif(isset($data['error']))
                                <div class="alert alert-danger">{{ $data['error'] }}</div>
                            @else
                                <!-- Desktop Table View -->
                                <div class="table-responsive d-none d-md-block">
                                    <table id="surveyReportTable" class="table table-bordered table-striped table-hover nowrap" style="width:100%">
                                        <thead style="background-color:#00008B; color:#fff;">
                                            <tr>
                                                <th>Surveyor Code</th>
                                                <th>Surveyor Name</th>
                                                <th>Doc Ref No</th>
                                                <th>Paid Amount</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($data as $report)
                                                <tr>
                                                    <td>{{ $report['PSR_SURV_CODE'] ?? 'N/A' }}</td>
                                                    <td>{{ $report['PSR_SURV_NAME'] ?? 'N/A' }}</td>
                                                    <td>{{ $report['GSH_DOC_REF_NO'] ?? 'N/A' }}</td>
                                                    <td>{{ number_format($report['GPD_PAYEE_AMOUNT'] ?? 0, 0, '.', ',') }}</td>
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
                                            <button type="button" class="btn-mobile-export btn-copy" data-export="copy">
                                                <i class="fas fa-copy"></i>
                                                <span>Copy</span>
                                            </button>
                                            <button type="button" class="btn-mobile-export btn-csv" data-export="csv">
                                                <i class="fas fa-file-csv"></i>
                                                <span>CSV</span>
                                            </button>
                                            <button type="button" class="btn-mobile-export btn-excel" data-export="excel">
                                                <i class="fas fa-file-excel"></i>
                                                <span>Excel</span>
                                            </button>
                                            <button type="button" class="btn-mobile-export btn-pdf" data-export="pdf">
                                                <i class="fas fa-file-pdf"></i>
                                                <span>PDF</span>
                                            </button>
                                            <button type="button" class="btn-mobile-export btn-print" data-export="print">
                                                <i class="fas fa-print"></i>
                                                <span>Print</span>
                                            </button>
                                        </div>
                                    </div>

                                    <!-- Mobile Search -->
                                    <div class="mobile-controls mb-3">
                                        <div class="row g-2">
                                            <div class="col-12">
                                                <label for="mobileSearch" class="form-label fw-bold mb-1">Search:</label>
                                                <input type="text" id="mobileSearch" class="form-control form-control-sm" placeholder="Search reports...">
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Mobile Cards -->
                                    <div id="mobileCardsContainer">
                                        @foreach ($data as $index => $report)
                                            <div class="mobile-report-card mb-3" data-index="{{ $index }}">
                                                <div class="card border-0 shadow-sm">
                                                    <div class="card-body p-3">
                                                        <div class="row g-2">
                                                            <div class="col-6">
                                                                <small class="text-muted d-block">Surveyor Code</small>
                                                                <small class="fw-medium">{{ $report['PSR_SURV_CODE'] ?? 'N/A' }}</small>
                                                            </div>
                                                            <div class="col-6">
                                                                <small class="text-muted d-block">Paid Amount</small>
                                                                <small class="fw-medium text-success">{{ number_format($report['GPD_PAYEE_AMOUNT'] ?? 0, 0, '.', ',') }}</small>
                                                            </div>
                                                            <div class="col-12">
                                                                <small class="text-muted d-block">Surveyor Name</small>
                                                                <small class="fw-medium">{{ $report['PSR_SURV_NAME'] ?? 'N/A' }}</small>
                                                            </div>
                                                            <div class="col-12">
                                                                <small class="text-muted d-block">Doc Ref No</small>
                                                                <small class="fw-medium">{{ $report['GSH_DOC_REF_NO'] ?? 'N/A' }}</small>
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
                    <th>Surveyor Code</th>
                    <th>Surveyor Name</th>
                    <th>Doc Ref No</th>
                    <th>Paid Amount</th>
                </tr>
            </thead>
            <tbody>
                @if(!empty($data))
                    @foreach ($data as $report)
                        <tr>
                            <td>{{ $report['PSR_SURV_CODE'] ?? 'N/A' }}</td>
                            <td>{{ $report['PSR_SURV_NAME'] ?? 'N/A' }}</td>
                            <td>{{ $report['GSH_DOC_REF_NO'] ?? 'N/A' }}</td>
                            <td>{{ number_format($report['GPD_PAYEE_AMOUNT'] ?? 0, 0, '.', ',') }}</td>
                        </tr>
                    @endforeach
                @endif
            </tbody>
        </table>
    </div>

    <style>
        /* ===== CRITICAL: MOBILE EXPORT BUTTONS ===== */
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

        /* Export button colors */
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

        /* ===== DESKTOP DATATABLE CONTROLS - CSS GRID LAYOUT ===== */
        .desktop-controls-grid {
            display: grid;
            grid-template-columns: 1fr auto 1fr;
            align-items: center;
            gap: 20px;
            width: 100%;
        }

        .dt-search-wrapper {
            /* First column - left side */
            display: flex;
            justify-content: flex-start;
            align-items: center;
        }

        .dt-status-filter {
            /* Second column - center */
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .status-filter-inner {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            white-space: nowrap;
        }

        .dt-buttons {
            /* Third column - right side */
            display: flex !important;
            visibility: visible !important;
            opacity: 1 !important;
            justify-content: flex-end;
            align-items: center;
            gap: 4px;
            flex-wrap: wrap;
        }

        /* ===== DESKTOP DATATABLE STYLING ===== */
        #surveyReportTable thead tr th,
        table.dataTable thead th {
             background-color:  #f8f9fa!important;
            color: #212529 !important;
            text-align: center;
            font-family: 'Calibri'; 

        }

        #surveyReportTable tbody tr td {
            text-align: center;
            vertical-align: middle;
            padding: 8px 4px;
            font-size: 12px;
        }

        #surveyReportTable tbody tr:nth-child(even) {
            background-color: #f8f9fa;
        }

        #surveyReportTable tbody tr:hover {
            background-color: #d1e7fd !important;
        }

        div.dataTables_scrollBody {
            max-height: 400px !important;
            overflow-y: scroll !important;
            border: 1px solid #ddd;
            border-radius: 4px;
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

        /* ===== MOBILE CARD STYLING ===== */
        .mobile-report-card {
            opacity: 1;
            transition: opacity 0.3s ease;
        }

        .mobile-report-card.hidden {
            display: none;
        }

        .mobile-report-card .card {
            border-radius: 10px;
            border-left: 4px solid #007bff;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .mobile-report-card .card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15) !important;
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

        .back-btn:hover {
            background-color: #0d6efd !important;
            color: #fff !important;
            border-color: #0d6efd !important;
        }

        /* ===== RESPONSIVENESS ===== */
        @media (max-width: 768px) {
            .container-fluid {
                padding-left: 8px;
                padding-right: 8px;
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

            div.dataTables_scrollBody {
                max-height: 350px !important;
            }
        }

        @media (max-width: 480px) {
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

            div.dataTables_scrollBody {
                max-height: 280px !important;
            }
        }

        @media (max-width: 360px) {
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

            div.dataTables_scrollBody {
                max-height: 250px !important;
            }
        }

        /* ===== ORIGINAL MOBILE-FIRST STYLES ===== */
        @media (max-width: 575.98px) {
            .content-body {
                padding: 10px 5px;
            }

            .card-body {
                padding: 15px 10px !important;
            }

            .btn {
                font-size: 14px;
                padding: 8px 12px;
            }

            .form-control {
                font-size: 14px;
            }

            .card-title {
                font-size: 1.1rem !important;
            }

            .input-group-sm .btn {
                padding: 6px 10px;
            }

            .table-responsive {
                border: 1px solid #dee2e6;
                border-radius: 0.375rem;
            }
        }

        /* Datepicker mobile optimizations */
        @media (max-width: 767.98px) {
            .datepicker {
                font-size: 16px !important;
            }

            .datepicker.dropdown-menu {
                font-size: 14px;
                width: 100%;
                max-width: 300px;
            }

            .datepicker table tr td,
            .datepicker table tr th {
                width: 30px;
                height: 30px;
                font-size: 12px;
            }

            .ui-datepicker {
                font-size: 14px !important;
                width: auto !important;
                max-width: 300px;
            }

            .ui-datepicker-calendar td {
                padding: 2px;
            }

            .ui-datepicker-calendar td a {
                padding: 6px;
                font-size: 12px;
            }
        }

        /* Native date input fallback */
        input[type="date"] {
            -webkit-appearance: none;
            -moz-appearance: none;
            appearance: none;
            background: white;
            border: 1px solid #ced4da;
            border-radius: 0.375rem;
            padding: 0.375rem 0.75rem;
            font-size: 1rem;
            line-height: 1.5;
        }

        input[type="date"]::-webkit-calendar-picker-indicator {
            opacity: 0;
            position: absolute;
            right: 0;
            width: 100%;
            height: 100%;
            cursor: pointer;
        }

        /* ===== FORCE VISIBILITY ===== */
        .mobile-export-buttons,
        .btn-mobile-export {
            display: flex !important;
            visibility: visible !important;
            opacity: 1 !important;
        }
        /* Apply these styles to ALL labels */

/* All labels styled the same */
label {
    font-size: 15px !important;
    font-weight: 700 !important;
    color: #000000 !important;
    font-family: Calibri, sans-serif !important;
    margin-right: 8px;      /* spacing between label and input */
    white-space: nowrap;    /* prevents "From Date" from breaking */
}
Use more specific selector for DataTables headers
#surveyReportTable.dataTable thead th:nth-child(2),
#surveyReportTable.dataTable tbody td:nth-child(2) {
    text-align: left !important;
}

#surveyReportTable.dataTable thead th:nth-child(3),
#surveyReportTable.dataTable tbody td:nth-child(3) {
    text-align: left !important;
}

#surveyReportTable.dataTable thead th:nth-child(4),
#surveyReportTable.dataTable tbody td:nth-child(4) {
    text-align: right !important;
}




        
    </style>

    <script>
        $(document).ready(function() {
            var table;
            var hiddenTable;
            var isMobile = window.innerWidth < 768;

            // Initialize hidden table for mobile exports
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
                            extend: 'copy',
                            exportOptions: {
                                columns: ':visible'
                            }
                        },
                        {
                            extend: 'csv',
                            exportOptions: {
                                columns: ':visible'
                            },
                            filename: 'Survey_Report_Export_' + new Date().toISOString().slice(0,10)
                        },
                        {
                            extend: 'excel',
                            exportOptions: {
                                columns: ':visible'
                            },
                            filename: 'Survey_Report_Export_' + new Date().toISOString().slice(0,10)
                        },
                        {
                            extend: 'pdf',
                            exportOptions: {
                                columns: ':visible'
                            },
                            filename: 'Survey_Report_Export_' + new Date().toISOString().slice(0,10),
                            orientation: 'landscape',
                            pageSize: 'A4'
                        },
                        {
                            extend: 'print',
                            exportOptions: {
                                columns: ':visible'
                            },
                            title: 'Survey Fee Report'
                        }
                    ]
                });
            }

            // Desktop DataTable initialization
            function initializeDesktopTable() {
                if (table) {
                    table.destroy();
                }

                table = $('#surveyReportTable').DataTable({
                    paging: false,
                    ordering: true,
                    responsive: false,
                    scrollX: true,
                    scrollY: getScrollHeight(),
                    scrollCollapse: false,
                    dom: '<"desktop-controls-grid mb-2"<"dt-search-wrapper"f><"dt-status-filter"<"status-filter-inner">><"dt-buttons"B>>rtip',
                    buttons: [
                        {
                            extend: 'copy',
                            text: '<i class="fas fa-copy me-1"></i><span>Copy</span>',
                            className: 'btn btn-sm btn-outline-secondary'
                        },
                        {
                            extend: 'csv',
                            text: '<i class="fas fa-file-csv me-1"></i><span>CSV</span>',
                            className: 'btn btn-sm btn-outline-success',
                            filename: 'Survey_Report_Export_' + new Date().toISOString().slice(0,10)
                        },
                        {
                            extend: 'excel',
                            text: '<i class="fas fa-file-excel me-1"></i><span>Excel</span>',
                            className: 'btn btn-sm btn-outline-success',
                            filename: 'Survey_Report_Export_' + new Date().toISOString().slice(0,10)
                        },
                        {
                            extend: 'pdf',
                            text: '<i class="fas fa-file-pdf me-1"></i><span>PDF</span>',
                            className: 'btn btn-sm btn-outline-danger',
                            filename: 'Survey_Report_Export_' + new Date().toISOString().slice(0,10),
                            orientation: 'landscape'
                        },
                        {
                            extend: 'print',
                            text: '<i class="fas fa-print me-1"></i><span>Print</span>',
                            className: 'btn btn-sm btn-outline-primary',
                            title: 'Survey Fee Report'
                        }
                    ],
                    columnDefs: [{
                        targets: "_all",
                        className: "text-nowrap text-center"
                    }],
                    language: {
                       search: "Search",
                       
                    },
                    initComplete: function() {
                        
                        $('.status-filter-inner').html('&nbsp;');

                        // Style search input
                        $('#surveyReportTable_filter label').css({
                            'font-size': '14px',
                            'font-weight': '600',
                            'color': '#333333',
                            'margin-bottom': '6px'

                            
                        });

                        setTimeout(function() {
                            if (table) {
                                table.columns.adjust().draw();
                            }
                        }, 100);
                    }
                });
            }

            // Mobile export button handlers
            $('.btn-mobile-export').on('click', function() {
                var exportType = $(this).data('export');
                
                // Show loading state
                $(this).addClass('loading').prop('disabled', true);
                var originalHtml = $(this).html();
                $(this).html('<i class="fas fa-spinner fa-spin"></i><span>Processing...</span>');
                
                // Filter hidden table data based on current mobile filters
                updateHiddenTableData();
                
                setTimeout(() => {
                    try {
                        switch(exportType) {
                            case 'copy':
                                hiddenTable.button('.buttons-copy').trigger();
                                showToast('Data copied to clipboard!', 'success');
                                break;
                            case 'csv':
                                hiddenTable.button('.buttons-csv').trigger();
                                showToast('CSV file downloaded!', 'success');
                                break;
                            case 'excel':
                                hiddenTable.button('.buttons-excel').trigger();
                                showToast('Excel file downloaded!', 'success');
                                break;
                            case 'pdf':
                                hiddenTable.button('.buttons-pdf').trigger();
                                showToast('PDF file downloaded!', 'success');
                                break;
                            case 'print':
                                hiddenTable.button('.buttons-print').trigger();
                                showToast('Print dialog opened!', 'info');
                                break;
                        }
                    } catch (error) {
                        console.error('Export error:', error);
                        showToast('Export failed. Please try again.', 'error');
                    }
                    
                    // Reset button state
                    $(this).removeClass('loading').prop('disabled', false).html(originalHtml);
                }, 500);
            });

            // Update hidden table data based on mobile filters
            function updateHiddenTableData() {
                var visibleData = [];
                var searchTerm = $('#mobileSearch').val().toLowerCase();
                
                $('.mobile-report-card:not(.hidden)').each(function() {
                    var $card = $(this);
                    var cardData = {
                        surveyor_code: $card.find('small:contains("Surveyor Code")').next('small').text().trim(),
                        surveyor_name: $card.find('small:contains("Surveyor Name")').next('small').text().trim(),
                        doc_ref_no: $card.find('small:contains("Doc Ref")').next('small').text().trim(),
                        paid_amount: $card.find('small:contains("Paid Amount")').next('small').text().trim()
                    };
                    
                    // Apply search filter
                    var searchMatch = (searchTerm === '' || 
                        cardData.surveyor_code.toLowerCase().includes(searchTerm) ||
                        cardData.surveyor_name.toLowerCase().includes(searchTerm) ||
                        cardData.doc_ref_no.toLowerCase().includes(searchTerm) ||
                        cardData.paid_amount.toLowerCase().includes(searchTerm));
                    
                    if (searchMatch) {
                        visibleData.push(cardData);
                    }
                });
                
                // Clear and repopulate hidden table
                hiddenTable.clear();
                visibleData.forEach(function(row) {
                    hiddenTable.row.add([
                        row.surveyor_code,
                        row.surveyor_name,
                        row.doc_ref_no,
                        row.paid_amount
                    ]);
                });
                hiddenTable.draw();
            }

            // Toast notification function
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

            // Add CSS animation for toast
            $('<style>').prop('type', 'text/css').html(`
                @keyframes slideInRight {
                    from {
                        transform: translateX(100%);
                        opacity: 0;
                    }
                    to {
                        transform: translateX(0);
                        opacity: 1;
                    }
                }
            `).appendTo('head');

            function getScrollHeight() {
                if (window.innerWidth <= 360) return "250px";
                if (window.innerWidth <= 480) return "280px";
                if (window.innerWidth <= 768) return "350px";
                return "400px";
            }

            // Mobile search functionality
            $('#mobileSearch').on('input', function() {
                var searchTerm = $(this).val().toLowerCase();
                $('.mobile-report-card').each(function() {
                    var $card = $(this);
                    var cardText = $card.text().toLowerCase();
                    if (searchTerm === '' || cardText.includes(searchTerm)) {
                        $card.removeClass('hidden');
                    } else {
                        $card.addClass('hidden');
                    }
                });
            });

            // Resize handler
            var resizeTimeout;
            $(window).on('resize', function() {
                clearTimeout(resizeTimeout);
                resizeTimeout = setTimeout(function() {
                    var nowMobile = window.innerWidth < 768;
                    if (isMobile !== nowMobile) {
                        isMobile = nowMobile;
                        location.reload(); // Reload for dramatic layout changes
                    } else if (table) {
                        table.settings()[0].oScroll.sY = getScrollHeight();
                        table.columns.adjust().draw();
                    }
                }, 250);
            });

            // Initialize everything
            if (!isMobile) {
                initializeDesktopTable();
            }
            
            initializeHiddenTable();

            // Datepicker initialization
            function initializeDatepicker() {
                if (typeof $.fn.datepicker !== 'undefined') {
                    if ($.fn.datepicker.Constructor) {
                        $('.datepicker').datepicker({
                            format: 'dd-M-yyyy',
                            autoclose: true,
                            todayHighlight: true,
                            orientation: window.innerWidth <= 768 ? 'bottom auto' : 'auto',
                            container: 'body'
                        });
                    } else {
                        // Fallback to jQuery UI Datepicker
                        $('.datepicker').datepicker({
                            dateFormat: 'dd-M-yy',
                            showButtonPanel: true,
                            changeMonth: true,
                            changeYear: true,
                            yearRange: '-10:+2'
                        });
                    }
                } else {
                    $('.datepicker').attr('type', 'date');
                }
            }

            initializeDatepicker();

            // Datepicker button handlers
            $('#datefrom-btn').on('click', function(e) {
                e.preventDefault();
                const $input = $('[name="datefrom"]');
                if ($input.hasClass('hasDatepicker') || $input.data('datepicker')) {
                    $input.datepicker('show');
                } else if ($input.attr('type') === 'date') {
                    $input.focus();
                    $input[0].showPicker && $input[0].showPicker();
                } else {
                    $input.focus();
                }
            });

            $('#dateto-btn').on('click', function(e) {
                e.preventDefault();
                const $input = $('[name="dateto"]');
                if ($input.hasClass('hasDatepicker') || $input.data('datepicker')) {
                    $input.datepicker('show');
                } else if ($input.attr('type') === 'date') {
                    $input.focus();
                    $input[0].showPicker && $input[0].showPicker();
                } else {
                    $input.focus();
                }
            });

            // Final adjustments
            setTimeout(function() {
                if (table) {
                    table.columns.adjust().draw();
                }
            }, 300);

            // Tab/modal events
            $('a[data-bs-toggle="tab"], button[data-bs-toggle="collapse"], .modal').on(
                'shown.bs.tab shown.bs.collapse shown.bs.modal',
                function() {
                    if (table) {
                        table.columns.adjust().draw();
                    }
                }
            );
        });
    </script>

@endsection