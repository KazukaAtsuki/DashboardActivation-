<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Http;

class ActivationController extends Controller
{
    // Alamat API Python Anda
    protected $pythonApi = "http://localhost:8000/api";

    // 1. INDEX (Mengambil data dari Python API)
    public function index(Request $request)
    {
        if ($request->ajax()) {
            // Memanggil API Python (Di sini Http digunakan, warnanya akan menyala)
            $response = Http::get("{$this->pythonApi}/loggers");
            $data = $response->json();

            return DataTables::of($data)
                ->addIndexColumn()
                ->editColumn('token', function($row){
                    if($row['token'] == 'Expired') {
                        return '<span class="text-danger fw-bold"><i class="ti ti-clock-off"></i> EXPIRED</span>';
                    }
                    if($row['token'] == 'None') return '<span class="text-muted">----------</span>';
                    return '<code class="token-secret">' . $row['token'] . '</code>';
                })
                ->editColumn('activation_code', function($row){
                    if($row['activation_code'] == 'Expired' || $row['activation_code'] == 'None') return '<span class="text-muted">----</span>';
                    return '<span class="badge" style="background: rgba(0, 242, 255, 0.1); border: 1px dashed #00f2ff; color: #00f2ff;">'.$row['activation_code'].'</span>';
                })
                ->editColumn('status', function($row){
                    if($row['status'] == 'Active') {
                        return '<span style="color:#10b981; font-weight: bold;"><i class="ti ti-circle-filled"></i> Active</span>';
                    } elseif($row['status'] == 'Pending') {
                        return '<span style="color:#fbbf24; font-weight: bold;"><i class="ti ti-circle-filled"></i> Pending</span>';
                    }
                    return '<span style="color:#f43f5e; font-weight: bold;"><i class="ti ti-circle-filled"></i> Expired / Cancel</span>';
                })
                ->addColumn('action', function($row){
                    $id = $row['logger_id'];
                    // Tombol Generate (Menghubungi Python)
                    $btn = '<button type="button" class="btn btn-outline-info btn-sm me-1 btn-generate" data-id="'.$id.'" title="Generate New Code"><i class="ti ti-refresh"></i></button>';

                    // Tombol Delete (Menghubungi Python)
                    $btn .= '<button type="button" class="btn btn-outline-danger btn-sm btn-delete" data-id="'.$id.'"><i class="ti ti-trash"></i></button>';

                    return $btn;
                })
                ->rawColumns(['token', 'activation_code', 'status', 'action'])
                ->make(true);
        }

        return view('activations.index');
    }

    // 2. GENERATE BARU (Menghubungi API Python)
    public function generate($id)
{
    try {
        // Gunakan pemanggilan langsung tanpa timeout terlebih dahulu untuk tes
        $response = Http::post($this->pythonApi . "/generate/" . $id);

        // Jika error "successful()" masih muncul, gunakan status()
        if ($response->status() >= 200 && $response->status() < 300) {
            return response()->json([
                'status' => 'success',
                'message' => 'Token Refreshed'
            ]);
        }

        return response()->json([
            'status' => 'error',
            'message' => 'Python API returned status: ' . $response->status()
        ], 500);

    } catch (\Exception $e) {
        return response()->json([
            'status' => 'error',
            'message' => 'Connection Failed: ' . $e->getMessage()
        ], 500);
    }
}

    // 3. DESTROY (Menghubungi API Python)
    public function destroy($id)
    {
        // Menggunakan Http DELETE ke Python (Warna Http akan menyala)
        $response = Http::delete("{$this->pythonApi}/loggers/{$id}");

        if ($response->successful()) {
            return response()->json([
                'status' => 'success',
                'message' => 'Logger deleted from Python System'
            ]);
        }

        return response()->json(['status' => 'error'], 500);
    }

    // --- Create, Store, Edit, Update sementara tidak diubah jika Anda masih ingin simpan di DB Lokal ---
    // Tapi untuk dashboard utama, kita sudah full menggunakan API Python.

    public function create()
    {
        return view('activations.create');
    }

    public function edit($id)
    {
        // Karena data sekarang di Python, idealnya Anda ambil data detail dari Python juga
        return view('activations.edit');
    }
}