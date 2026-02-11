@extends('layouts.master')

{{-- Tambahkan Meta CSRF agar AJAX Laravel berjalan lancar --}}
@section('title', 'Node Activation Console')

@push('styles')
<style>
    /* --- THEME DASHBOARD AKTIVASI (NEON TECH) --- */
    :root {
        --primary-neon: #00f2ff;
        --secondary-glow: #0066ff;
        --bg-deep: #020617;
        --card-surface: rgba(15, 23, 42, 0.7);
        --text-bright: #f8fafc;
        --border-glass: rgba(0, 242, 255, 0.2);
    }

    body {
        background-color: var(--bg-deep);
        color: var(--text-bright);
        background-image:
            radial-gradient(circle at 50% 0%, rgba(0, 102, 255, 0.1) 0%, transparent 50%),
            linear-gradient(rgba(255,255,255,0.01) 1px, transparent 1px),
            linear-gradient(90deg, rgba(255,255,255,0.01) 1px, transparent 1px);
        background-size: 100% 100%, 40px 40px, 40px 40px;
    }

    .page-container { padding-top: 110px; padding-bottom: 50px; }

    .card-control-panel {
        background: var(--card-surface);
        backdrop-filter: blur(15px);
        border: 1px solid var(--border-glass);
        border-radius: 20px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.5);
    }

    /* Pastikan kursor pointer muncul pada semua tombol */
    button, .btn, .btn-generate, .btn-delete {
        cursor: pointer !important;
        pointer-events: auto !important;
    }

    .status-indicator {
        display: inline-flex; align-items: center; gap: 8px; font-size: 0.75rem;
        background: rgba(255, 255, 255, 0.05); padding: 4px 12px; border-radius: 50px; border: 1px solid rgba(255, 255, 255, 0.1);
    }

    .pulse-dot { width: 8px; height: 8px; border-radius: 50%; }
    .dot-online { background-color: #22c55e; box-shadow: 0 0 10px #22c55e; animation: pulse-green 1.5s infinite; }
    .dot-offline { background-color: #ef4444; box-shadow: 0 0 10px #ef4444; }

    @keyframes pulse-green {
        0% { box-shadow: 0 0 0 0 rgba(34, 197, 94, 0.7); }
        70% { box-shadow: 0 0 0 10px rgba(34, 197, 94, 0); }
        100% { box-shadow: 0 0 0 0 rgba(34, 197, 94, 0); }
    }

    .table-modern { color: #cbd5e1; border-collapse: separate; border-spacing: 0 8px; }
    .table-modern thead th { background: rgba(0, 242, 255, 0.05); color: var(--primary-neon); font-weight: 700; text-transform: uppercase; font-size: 0.75rem; letter-spacing: 1px; border: none; padding: 15px; }
    .table-modern tbody tr { background: rgba(255, 255, 255, 0.02); transition: all 0.3s ease; }
    .table-modern td { border: none; padding: 15px; }

    .token-secret { font-family: 'Courier New', monospace; background: rgba(0, 0, 0, 0.5); color: #00f2ff !important; border: 1px solid rgba(0, 242, 255, 0.4); padding: 5px 12px; border-radius: 6px; font-size: 0.85rem; display: inline-block; text-shadow: 0 0 8px rgba(0, 242, 255, 0.6); }

    .btn-neon { background: linear-gradient(45deg, var(--secondary-glow), var(--primary-neon)); color: #000 !important; font-weight: 800; border: none; box-shadow: 0 0 15px rgba(0, 242, 255, 0.3); }

    .rotate { animation: spin 1s linear infinite; }
    @keyframes spin { from { transform: rotate(0deg); } to { transform: rotate(360deg); } }
</style>
@endpush

@section('content')
<div class="container-fluid page-container">
    <div class="d-flex justify-content-between align-items-end mb-4">
        <div>
            <div class="status-indicator mb-2">
                <div class="pulse-dot {{ $isApiRunning ? 'dot-online' : 'dot-offline' }}"></div>
                <span class="{{ $isApiRunning ? 'text-success' : 'text-danger' }} fw-bold ms-2">
                    SYSTEM API {{ $isApiRunning ? 'ONLINE' : 'OFFLINE' }}
                </span>
            </div>
            <h2 class="page-title mb-0" style="color: white">Node Activation Console</h2>
            <p class="text-muted small mb-0">Management interface for secure logger authentication.</p>
        </div>

        <div class="d-flex gap-2">
            @if(!$isApiRunning)
                <form action="{{ route('activations.start') }}" method="POST" id="form-start">
                    @csrf
                    <button type="submit" class="btn btn-success rounded-pill px-4 shadow-sm fw-bold">
                        <i class="ti ti-player-play-filled me-1"></i> START ENGINE
                    </button>
                </form>
            @else
                <form action="{{ route('activations.stop') }}" method="POST" id="form-stop">
                    @csrf
                    <button type="button" id="btn-stop-engine" class="btn btn-danger rounded-pill px-4 shadow-sm fw-bold">
                        <i class="ti ti-player-stop-filled me-1"></i> STOP ENGINE
                    </button>
                </form>
            @endif

            <div class="vr mx-2 bg-secondary opacity-25"></div>

            <a href="{{ route('activations.create') }}" class="btn btn-neon rounded-pill px-4">
                <i class="ti ti-bolt me-1"></i> INITIALIZE NEW LOGGER
            </a>
        </div>
    </div>

    <div class="card-control-panel">
        <div class="card-body p-4">
            <div class="table-responsive">
                <table class="table align-middle table-modern" id="activationTable" style="width:100%">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Logger ID</th>
                            <th>Logger Name</th>
                            <th>Token (Secret)</th>
                            <th>Activation Code</th>
                            <th>Status</th>
                            <th class="text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>

<script>
    // 1. FUNGSI GLOBAL KONFIRMASI STOP
    function handleStopEngine() {
        Swal.fire({
            title: 'TERMINATE SYSTEM?',
            text: "Mematikan API akan memutus seluruh jalur otorisasi unit DAS lapangan secara instan!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            cancelButtonColor: '#1e293b',
            confirmButtonText: 'YES, SHUTDOWN',
            background: '#020617',
            color: '#ffffff',
            customClass: { popup: 'rounded-4 border border-danger' }
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire({
                    title: 'Shutting Down...',
                    allowOutsideClick: false,
                    didOpen: () => { Swal.showLoading() },
                    background: '#020617', color: '#ffffff', showConfirmButton: false
                });
                document.getElementById('form-stop').submit();
            }
        });
    }

    $(document).ready(function() {
        $.ajaxSetup({ headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') } });

        // Bind tombol stop engine
        $('#btn-stop-engine').on('click', function(e) {
            e.preventDefault();
            handleStopEngine();
        });

        // Animasi loading saat Start
        $('#form-start').on('submit', function() {
            $(this).find('button').prop('disabled', true).html('<i class="ti ti-loader-2 rotate"></i> BOOTING...');
        });

        // 2. INISIALISASI DATATABLES
        var table = $('#activationTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('activations.index') }}",
            columns: [
                { data: 'DT_RowIndex', orderable: false, searchable: false },
                { data: 'logger_id', render: function(data){ return '<span class="fw-bold" style="color: var(--primary-neon)">'+data+'</span>'; } },
                { data: 'logger_name' },
                { data: 'token', render: function(data) {
                    if(data === 'Expired') return '<span class="text-danger fw-bold">EXPIRED</span>';
                    if(!data || data === 'None') return '<span class="text-muted">----------</span>';
                    return `<span class="token-secret">${data}</span>`;
                }},
                { data: 'activation_code', render: function(data) {
                    if(!data || data === 'None' || data === 'Expired') return '<span class="text-muted">----</span>';
                    return '<span class="badge" style="background: rgba(0, 242, 255, 0.1); border: 1px dashed var(--primary-neon); color: var(--primary-neon);">' + data + '</span>';
                }},
                { data: 'status', render: function(data) {
                    let color = '#f43f5e';
                    if(data === 'Active') color = '#10b981';
                    if(data === 'Pending') color = '#fbbf24';
                    if(data === 'Requesting') color = '#d8b4fe';
                    return `<span style="color:${color}; font-weight: bold;"><i class="ti ti-circle-filled me-1"></i>${data}</span>`;
                }},
                { data: 'logger_id', name: 'action', orderable: false, searchable: false, className: 'text-center', render: function(data) {
                    return `
                        <div class="btn-group">
                            <button type="button" class="btn btn-sm btn-outline-info btn-generate" data-id="${data}" title="Generate"><i class="ti ti-refresh"></i></button>
                            <button type="button" class="btn btn-sm btn-outline-danger btn-delete" data-id="${data}" title="Delete"><i class="ti ti-trash"></i></button>
                        </div>
                    `;
                }},
            ]
        });

        // 3. FUNGSI MENDENGARKAN NOTIFIKASI
        setInterval(function() {
            $.get("/activations/get-notif", function(data) {
                if (data && data.length > 0) {
                    data.forEach(notif => {
                        Swal.fire({ icon: 'warning', title: 'ALERT: USER LOGOUT', text: notif.message, toast: true, position: 'top-end', showConfirmButton: false, timer: 6000, background: '#0f172a', color: '#fff' });
                    });
                    table.ajax.reload(null, false);
                }
            });
        }, 5000);

        // -----------------------------------------------------------
        // MODIFIKASI: LOGIKA TOMBOL GENERATE DENGAN UI SWEETALERT2
        // -----------------------------------------------------------
        $(document).on('click', '.btn-generate', function() {
            let id = $(this).data('id');

            Swal.fire({
                title: 'RE-ISSUE TOKEN?',
                text: "Generate kunci baru untuk " + id + " akan membatalkan otorisasi sebelumnya.",
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#00f2ff',
                cancelButtonColor: '#1e293b',
                confirmButtonText: 'YES, GENERATE',
                background: '#020617',
                color: '#ffffff',
                customClass: { popup: 'rounded-4 border border-info', title: 'text-info fw-bold' }
            }).then((result) => {
                if (result.isConfirmed) {
                    // Animasi Loading
                    Swal.fire({
                        title: 'Generating Key...',
                        html: 'Syncing with Python Core Engine',
                        allowOutsideClick: false,
                        didOpen: () => { Swal.showLoading() },
                        background: '#020617', color: '#ffffff', showConfirmButton: false
                    });

                    // Proses AJAX
                    $.post(`/activations/generate/${id}`)
                        .done(function(response) {
                            Swal.fire({
                                icon: 'success',
                                title: 'KEY ISSUED',
                                text: 'Kode aktivasi baru berhasil dikirim.',
                                background: '#020617', color: '#ffffff', confirmButtonColor: '#00f2ff'
                            });
                            table.ajax.reload(null, false);
                        })
                        .fail(function() {
                            Swal.fire({
                                icon: 'error',
                                title: 'FAILED',
                                text: 'Gagal menghubungi API Engine.',
                                background: '#020617', color: '#ffffff'
                            });
                        });
                }
            });
        });

        // 5. LOGIKA TOMBOL DELETE
        $(document).on('click', '.btn-delete', function() {
            let id = $(this).data('id');
            if(confirm('Remove logger ' + id + '?')) {
                $.ajax({ url: `/activations/${id}`, type: 'DELETE', success: () => table.ajax.reload(null, false) });
            }
        });
    });
</script>
@endpush