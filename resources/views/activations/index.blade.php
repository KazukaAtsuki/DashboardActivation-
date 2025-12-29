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

    .page-container {
        padding-top: 110px;
        padding-bottom: 50px;
    }

    .card-control-panel {
        background: var(--card-surface);
        backdrop-filter: blur(15px);
        border: 1px solid var(--border-glass);
        border-radius: 20px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.5);
        overflow: hidden;
    }

    .page-title {
        font-weight: 800;
        letter-spacing: -0.5px;
        color: var(--text-bright);
        text-transform: uppercase;
    }

    .status-indicator {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        font-size: 0.75rem;
        background: rgba(0, 242, 255, 0.1);
        padding: 4px 12px;
        border-radius: 50px;
        color: var(--primary-neon);
        border: 1px solid rgba(0, 242, 255, 0.3);
    }

    .pulse-dot {
        width: 8px;
        height: 8px;
        background-color: var(--primary-neon);
        border-radius: 50%;
        animation: pulse 1.5s infinite;
    }

    @keyframes pulse {
        0% { box-shadow: 0 0 0 0 rgba(0, 242, 255, 0.7); }
        70% { box-shadow: 0 0 0 10px rgba(0, 242, 255, 0); }
        100% { box-shadow: 0 0 0 0 rgba(0, 242, 255, 0); }
    }

    .table-modern {
        color: #cbd5e1;
        border-collapse: separate;
        border-spacing: 0 8px;
    }

    .table-modern thead th {
        background: rgba(0, 242, 255, 0.05);
        color: var(--primary-neon);
        font-weight: 700;
        text-transform: uppercase;
        font-size: 0.75rem;
        letter-spacing: 1px;
        border: none;
        padding: 15px;
    }

    .table-modern tbody tr {
        background: rgba(255, 255, 255, 0.02);
        transition: all 0.3s ease;
    }

    .table-modern tbody tr:hover {
        background: rgba(0, 242, 255, 0.08);
        transform: scale(1.002);
        box-shadow: inset 4px 0 0 var(--primary-neon);
    }

    .table-modern td {
        border: none;
        padding: 15px;
    }

    .token-secret {
        font-family: 'Courier New', monospace;
        background: rgba(0, 0, 0, 0.5);
        color: #00f2ff !important;
        border: 1px solid rgba(0, 242, 255, 0.4);
        padding: 5px 12px;
        border-radius: 6px;
        font-size: 0.85rem;
        display: inline-block;
        text-shadow: 0 0 8px rgba(0, 242, 255, 0.6);
        box-shadow: inset 0 0 5px rgba(0, 0, 0, 0.5);
    }

    .btn-neon {
        background: linear-gradient(45deg, var(--secondary-glow), var(--primary-neon));
        color: #000 !important;
        font-weight: 800;
        border: none;
        box-shadow: 0 0 15px rgba(0, 242, 255, 0.3);
    }

    .btn-neon:hover {
        transform: translateY(-2px);
        box-shadow: 0 0 25px rgba(0, 242, 255, 0.5);
        filter: brightness(1.1);
    }

    /* Animasi Loading untuk Tombol */
    .rotate { animation: spin 1s linear infinite; }
    @keyframes spin { from { transform: rotate(0deg); } to { transform: rotate(360deg); } }
</style>
@endpush

@section('content')
<div class="container-fluid page-container">
    <div class="d-flex justify-content-between align-items-end mb-4">
        <div>
            <div class="status-indicator mb-2">
                <div class="pulse-dot"></div>
                SYSTEM CORE ONLINE
            </div>
            <h2 class="page-title mb-0">Node Activation Console</h2>
            <p class="text-muted small mb-0">Management interface for secure logger authentication.</p>
        </div>
        <a href="{{ route('activations.create') }}" class="btn btn-neon rounded-pill px-4">
            <i class="ti ti-bolt me-1"></i> INITIALIZE NEW LOGGER
        </a>
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
    $(document).ready(function() {
        // 1. SETUP AJAX CSRF
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        // 2. INISIALISASI DATATABLES
        var table = $('#activationTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('activations.index') }}",
            columns: [
                { data: 'DT_RowIndex', orderable: false, searchable: false },
                {
                    data: 'logger_id',
                    name: 'logger_id',
                    render: function(data){
                        return '<span class="fw-bold" style="color: var(--primary-neon)">'+data+'</span>';
                    }
                },
                { data: 'logger_name', name: 'logger_name' },
                {
                    data: 'token',
                    name: 'token',
                    render: function(data) {
                        // Jika Python mengirim status Expired
                        if(data === 'Expired') {
                            return '<span class="text-danger fw-bold"><i class="ti ti-clock-off me-1"></i>EXPIRED</span>';
                        }
                        if(data === 'None' || !data) return '<span class="text-muted">----------</span>';
                        return `<span class="token-secret">${data}</span>`;
                    }
                },
                {
                    data: 'activation_code',
                    name: 'activation_code',
                    render: function(data) {
                        if(data === 'Expired' || data === 'None' || !data) return '<span class="text-muted">----</span>';
                        return '<span class="badge" style="background: rgba(0, 242, 255, 0.1); border: 1px dashed var(--primary-neon); color: var(--primary-neon);">' + data + '</span>';
                    }
                },
                {
                    data: 'status',
                    name: 'status',
                    render: function(data) {
                        let color = '#f43f5e'; // Default Red (Cancel/Expired)
                        let label = data;

                        if(data === 'Active') color = '#10b981'; // Green
                        if(data === 'Pending') color = '#fbbf24'; // Yellow
                        if(data === 'Cancel') label = 'Expired / Cancel';

                        return `<span style="color:${color}; font-weight: bold;"><i class="ti ti-circle-filled me-1"></i>${label}</span>`;
                    }
                },
                {
                    data: 'logger_id',
                    name: 'action',
                    orderable: false,
                    searchable: false,
                    className: 'text-center',
                    render: function(data) {
                        return `
                            <div class="btn-group">
                                <button type="button" class="btn btn-sm btn-outline-info btn-generate" data-id="${data}" title="Generate New Token">
                                    <i class="ti ti-refresh"></i>
                                </button>
                                <button type="button" class="btn btn-sm btn-outline-danger btn-delete" data-id="${data}" title="Delete Logger">
                                    <i class="ti ti-trash"></i>
                                </button>
                            </div>
                        `;
                    }
                },
            ],
            language: {
                search: "_INPUT_",
                searchPlaceholder: "Scan Logger ID..."
            }
        });

        // 4. LOGIKA TOMBOL GENERATE (Ganti bagian ini di index.blade.php Anda)
$(document).on('click', '.btn-generate', function() {
    let id = $(this).data('id');
    let btn = $(this);
    let icon = btn.find('i');

    if(confirm('Generate new token for logger ' + id + '? (Valid for 60 minutes)')) {
        icon.addClass('rotate');
        btn.prop('disabled', true);

        // Kirim request ke Controller Laravel
        $.post(`/activations/generate/${id}`)
            .done(function(response) {
                // Berhasil! Reload tabel
                table.ajax.reload(null, false);
                console.log("Success:", response.message);
            })
            .fail(function(xhr) {
                // Ambil pesan error dari Laravel atau Python
                let errorMsg = "Unknown Error";
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMsg = xhr.responseJSON.message;
                }

                // Tampilkan error yang LEBIH DETAIL
                alert('FAILED: ' + errorMsg);
                console.error("Detail Error:", xhr.responseText);
            })
            .always(function() {
                icon.removeClass('rotate');
                btn.prop('disabled', false);
            });
    }
});

        // 5. LOGIKA TOMBOL DELETE
        $(document).on('click', '.btn-delete', function() {
            let id = $(this).data('id');
            if(confirm('Are you sure you want to delete logger ' + id + '?')) {
                $.ajax({
                    url: `/activations/${id}`,
                    type: 'DELETE',
                    success: function(result) {
                        table.ajax.reload();
                    }
                });
            }
        });
    });
</script>
@endpush