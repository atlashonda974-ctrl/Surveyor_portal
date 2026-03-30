@extends('master')
@section('content')

<div class="content-body">
    <div class="container-fluid px-3">
        <div class="row">
            <div class="col-12">
                <div class="card">

                    {{-- Header --}}
                    <div class="card-header border-0 pb-0">
                        <div class="d-flex justify-content-between align-items-center w-100">
                            <h4 class="card-title mb-0" style="font-family:'Reddit Sans';font-weight:600;">
                                <i class="bi bi-envelope-open me-2"></i>Email Logs
                            </h4>
                            <a href="{{ route('home') }}" class="btn btn-sm btn-outline-secondary">
                                <i class="bi bi-arrow-left me-1"></i>Back
                            </a>
                        </div>
                    </div>

                    <div class="card-body">

                        {{-- Filters --}}
                        <div class="row mb-3" id="filters" style="display:none;">
                            <div class="col-md-3">
                                <input type="date" id="filterDate" class="form-control form-control-sm">
                            </div>
                            <div class="col-md-4">
                                <input type="text" id="filterDocument" class="form-control form-control-sm"
                                       placeholder="Filter Document">
                            </div>
                            <div class="col-md-5">
                                <input type="text" id="filterEmail" class="form-control form-control-sm"
                                       placeholder="Filter Sender / Receiver">
                            </div>
                        </div>

                        {{-- Loader --}}
                        <div id="loadingSpinner" class="loading-spinner">
                            <div class="spinner"></div>
                            <p>Loading email logs...</p>
                        </div>

                        {{-- Table --}}
                        <div class="table-responsive" id="tableContainer" style="display:none;">
                            <table class="table table-hover table-striped align-middle w-100" id="emailLogsTable">
                                <thead class="bg-light">
                                    <tr>
                                        <th style="background-color: #0062cc; text-align:center;">ID</th>
                                        <th style="background-color: #0062cc ; text-align:center;">Document</th>
                                        <th style="background-color: #0062cc ; text-align:center;">Sender</th>
                                        <th style="background-color: #0062cc ; text-align:center;">Receiver</th>
                                        <th style="background-color: #0062cc ; text-align:center;">Subject</th>
                                        <th style="background-color: #0062cc ; text-align:center;">Route</th>
                                        <th style="background-color: #0062cc ; text-align:center;">Date</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>

                        {{-- Empty --}}
                        <div id="emptyState" class="empty-state" style="display:none;">
                            <i class="bi bi-inbox"></i>
                            <p>No email logs found</p>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Styles --}}
<style>
.loading-spinner{display:flex;flex-direction:column;align-items:center;gap:10px;height:250px}
.spinner{width:45px;height:45px;border:4px solid #e9ecef;border-top:4px solid #0062cc;border-radius:50%;animation:spin 1s linear infinite}
@keyframes spin{to{transform:rotate(360deg)}}
.route-badge{padding:4px 10px;border-radius:4px;font-size:11px;font-weight:700;background:#0062cc;color:#fff}
.empty-state{display:flex;flex-direction:column;align-items:center;height:250px;gap:10px;color:#6c757d}
.empty-state i{font-size:40px;color:#0062cc;opacity:.5}


.dataTables_length{display:none!important}
</style>

{{-- Scripts --}}
<script>
document.addEventListener('DOMContentLoaded', () => {

    const spinner = document.getElementById('loadingSpinner');
    const emptyState = document.getElementById('emptyState');
    const tableWrap = document.getElementById('tableContainer');
    const filters = document.getElementById('filters');

    let table;
    let columnIndex = {};

    fetch('{{ route("reminder.debugAllLogs") }}')
        .then(r => r.json())
        .then(res => {

            const logs = (res.all_logs || []).filter(l => l.route === 'client');

            spinner.style.display = 'none';

            if (!logs.length) {
                emptyState.style.display = 'flex';
                return;
            }

            tableWrap.style.display = 'block';
            filters.style.display = 'flex';

            table = $('#emailLogsTable').DataTable({
                data: logs,
                pageLength: 10,
                lengthChange: false,  
                autoWidth: false,
                columns: [
                    { data: null, render: (d,t,r,m)=>m.row+1 },
                    { data: 'uw_doc', defaultContent: 'N/A' },
                    { data: 'sender', defaultContent: 'N/A' },
                    { data: 'receiver', defaultContent: 'N/A' },
                    { data: 'sub', defaultContent: 'N/A' },
                    { data: 'route', render: d => `<span class="route-badge">${d}</span>` },
                    { data: 'curdatetime', render: d => formatDate(d) }
                ],
                initComplete: function () {
                    this.api().columns().every(function () {
                        columnIndex[this.header().innerText.toLowerCase()] = this.index();
                    });
                }
            });

         
            document.getElementById('filterDocument').addEventListener('keyup', e => {
                table.column(columnIndex.document).search(e.target.value).draw();
            });

            document.getElementById('filterEmail').addEventListener('keyup', e => {
                table
                    .column(columnIndex.sender).search(e.target.value)
                    .column(columnIndex.receiver).search(e.target.value)
                    .draw();
            });

          
            $.fn.dataTable.ext.search.push((settings, row) => {
                const selected = document.getElementById('filterDate').value;
                if (!selected) return true;

                const dateText = row[columnIndex.date];
                return dateText.includes(
                    new Date(selected).toLocaleDateString('en-US',{
                        year:'numeric',month:'short',day:'numeric'
                    })
                );
            });

            document.getElementById('filterDate').addEventListener('change', () => table.draw());
        })
        .catch(() => {
            spinner.style.display = 'none';
            emptyState.style.display = 'flex';
        });

    function formatDate(d){
        if(!d) return 'N/A';
        return new Date(d).toLocaleString('en-US',{
            year:'numeric',month:'short',day:'numeric',
            hour:'2-digit',minute:'2-digit'
        });
    }
});
</script>

@endsection
