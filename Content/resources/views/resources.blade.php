@extends('master')
@section('content')

<div class="content-body">
    <div class="container-fluid px-3">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header border-0 pb-0">
                        <div class="d-flex justify-content-between align-items-center w-100">
                            <h4 style="font-family: 'Reddit Sans'; font-weight: 600; letter-spacing: 1px;" class="card-title mb-0">
                                <i class="bi bi-folder-fill me-2"></i>Download Resources
                            </h4>
                            <a href="{{ route('home') }}" class="btn btn-sm btn-outline-secondary">
                                <i class="bi bi-arrow-left me-1"></i>Back to Dashboard
                            </a>
                        </div>
                    </div>

                    <div class="card-body">
                        <p class="text-muted mb-4">
                            <i class="bi bi-info-circle me-1"></i> 
                            Download important documents and resources for your reference.
                        </p>

                        <div class="row g-4">
                            <div class="col-md-4">
                                <div class="resource-card h-100 border rounded-3 p-4 text-center">
                                    <div class="resource-icon mb-3">
                                        <i class="bi bi-file-pdf-fill text-danger" style="font-size: 4rem;"></i>
                                    </div>
                                    <h5 class="fw-bold mb-2">Approval Note</h5>
                                    <p class="text-muted small mb-3">Approval Note for disposal of Salvage 2025</p>
                                    <a href="{{ route('resources.download.single', ['filename' => urlencode('Approval Note for disposal of Slavage 2025.pdf')]) }}" 
                                       class="btn btn-outline-danger w-100">
                                        <i class="bi bi-download me-1"></i> Download PDF
                                    </a>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="resource-card h-100 border rounded-3 p-4 text-center">
                                    <div class="resource-icon mb-3">
                                        <i class="bi bi-file-pdf-fill text-danger" style="font-size: 4rem;"></i>
                                    </div>
                                    <h5 class="fw-bold mb-2">Claim Form Motor</h5>
                                    <p class="text-muted small mb-3">CLAIM FORM MOTOR (CONVENTIONAL)</p>
                                    <a href="{{ route('resources.download.single', ['filename' => urlencode('CLAIM FORM MOTOR (CONVENTIONAL).pdf')]) }}" 
                                       class="btn btn-outline-danger w-100">
                                        <i class="bi bi-download me-1"></i> Download PDF
                                    </a>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="resource-card h-100 border rounded-3 p-4 text-center">
                                    <div class="resource-icon mb-3">
                                        <i class="bi bi-file-pdf-fill text-danger" style="font-size: 4rem;"></i>
                                    </div>
                                    <h5 class="fw-bold mb-2">Satisfaction Note</h5>
                                    <p class="text-muted small mb-3">SATISFACTION NOTE MOTOR</p>
                                    <a href="{{ route('resources.download.single', ['filename' => urlencode('SATISFACTION NOTE MOTOR.pdf')]) }}" 
                                       class="btn btn-outline-danger w-100">
                                        <i class="bi bi-download me-1"></i> Download PDF
                                    </a>
                                </div>
                            </div>
                        </div>

                        <div class="text-center mt-5">
                            <button type="button" id="downloadAllResources" class="btn btn-primary btn-lg rounded-pill px-5">
                                <i class="bi bi-file-zip me-2"></i> Download All Resources (ZIP)
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .content-body {
        padding: 20px 0;
    }

    .resource-card {
        transition: all 0.3s ease;
        background: #ffffff;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
    }

    .resource-card:hover {
        transform: translateY(-8px);
        box-shadow: 0 8px 24px rgba(0, 0, 0, 0.15) !important;
        border-color: #0062cc !important;
    }

    .resource-icon {
        animation: float 3s ease-in-out infinite;
    }

    @keyframes float {
        0%, 100% {
            transform: translateY(0px);
        }
        50% {
            transform: translateY(-10px);
        }
    }

    .btn-outline-danger:hover {
        background-color: #dc3545;
        color: white;
        border-color: #dc3545;
    }

    .btn-primary {
        background: linear-gradient(135deg, #0066cc 0%, #0052a3 100%);
        border: none;
    }

    .btn-primary:hover {
        background: linear-gradient(135deg, #0052a3 0%, #003d7a 100%);
    }

    @media (max-width: 768px) {
        .resource-card {
            margin-bottom: 20px;
        }

        .btn-lg {
            font-size: 14px;
            padding: 10px 20px;
        }
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const downloadAllBtn = document.getElementById('downloadAllResources');

        downloadAllBtn.addEventListener('click', function() {
            const btn = this;
            btn.disabled = true;
            btn.innerHTML = '<i class="bi bi-hourglass-split me-2"></i> Preparing Download...';

            fetch('{{ route("resources.download.zip") }}')
                .then(response => response.blob())
                .then(blob => {
                    const link = document.createElement('a');
                    link.href = window.URL.createObjectURL(blob);
                    link.download = 'Resources_' + new Date().toISOString().slice(0, 10) + '.zip';
                    link.click();
                    showToast('ZIP downloaded successfully!', 'success');
                })
                .catch(error => {
                    console.error('Error:', error);
                    showToast('Failed to download ZIP file.', 'error');
                })
                .finally(() => {
                    btn.disabled = false;
                    btn.innerHTML = '<i class="bi bi-file-zip me-2"></i> Download All Resources (ZIP)';
                });
        });

        function showToast(message, type) {
            const bgColor = type === 'success' ? '#28a745' : '#dc3545';
            const toast = document.createElement('div');
            toast.style.cssText = `
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
            `;
            toast.innerHTML = `<i class="bi bi-${type === 'success' ? 'check-circle' : 'exclamation-circle'} me-2"></i>${message}`;
            document.body.appendChild(toast);

            setTimeout(() => {
                toast.style.opacity = '0';
                toast.style.transition = 'opacity 0.3s';
                setTimeout(() => toast.remove(), 300);
            }, 3000);
        }
    });
</script>

@endsection