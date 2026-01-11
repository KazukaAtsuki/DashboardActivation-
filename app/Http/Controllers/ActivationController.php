<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Http;
use Yajra\DataTables\Facades\DataTables;

class ActivationController extends Controller
{
    protected $pythonApi;
    protected $apiKey;

    public function __construct()
    {
        // Mengambil nilai dari .env
        $this->pythonApi = env('PYTHON_API_URL', 'http://127.0.0.1:8000/api');
        $this->apiKey    = env('PYTHON_API_KEY', 'TRUSUR_SECRET_KEY_2024');
    }

    /**
     * 1. INDEX (Menampilkan Tabel)
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = [];
            try {
                // Memberitahu VS Code secara paksa bahwa ini adalah objek Response
                /** @var \Illuminate\Http\Client\Response $response */
                $response = Http::withHeaders([
                    'X-API-KEY' => $this->apiKey,
                    'Accept'    => 'application/json'
                ])->timeout(5)->get($this->pythonApi . "/loggers");

                if ($response->status() === 200) {
                    $data = $response->json();
                }
            } catch (\Exception $e) {
                $data = [];
            }

            return DataTables::of($data)
                ->addIndexColumn()
                ->editColumn('token', function($row) {
                    $token = $row['token'] ?? 'None';
                    if ($token === 'Expired') {
                        return '<span class="text-danger fw-bold"><i class="ti ti-clock-off"></i> EXPIRED</span>';
                    }
                    if ($token === 'None' || $token === '') {
                        return '<span class="text-muted">----------</span>';
                    }
                    // Tampilkan token pekat jika ada isinya
                    return '<code class="token-secret">' . $token . '</code>';
                })
                ->editColumn('activation_code', function($row) {
                    $code = $row['activation_code'] ?? 'None';
                    if ($code === 'Expired' || $code === 'None' || $code === '') {
                        return '<span class="text-muted">----</span>';
                    }
                    return '<span class="badge" style="background: rgba(0, 242, 255, 0.1); border: 1px dashed #00f2ff; color: #00f2ff; padding: 5px 10px;">' . $code . '</span>';
                })
                ->editColumn('status', function($row) {
                    $status = $row['status'] ?? 'Pending';
                    if ($status === 'Active') {
                        return '<span style="color:#10b981; font-weight: bold;"><i class="ti ti-circle-filled"></i> Active</span>';
                    } elseif ($status === 'Pending') {
                        return '<span style="color:#fbbf24; font-weight: bold;"><i class="ti ti-circle-filled"></i> Pending</span>';
                    } elseif ($status === 'Requesting') {
                        return '<span style="color:#d8b4fe; font-weight: bold;"><i class="ti ti-alert-octagon pulse-neon"></i> Requesting</span>';
                    }
                    return '<span style="color:#f43f5e; font-weight: bold;"><i class="ti ti-circle-filled"></i> Expired</span>';
                })
                ->addColumn('action', function($row) {
                    $id = $row['logger_id'] ?? '';
                    return '<button type="button" class="btn btn-outline-info btn-sm me-1 btn-generate" data-id="' . $id . '" title="Generate New Token"><i class="ti ti-refresh"></i></button>
                            <button type="button" class="btn btn-outline-danger btn-sm btn-delete" data-id="' . $id . '"><i class="ti ti-trash"></i></button>';
                })
                ->rawColumns(['token', 'activation_code', 'status', 'action'])
                ->make(true);
        }
        return view('activations.index');
    }


    /**
     * Tampilkan form tambah logger baru
     */
    public function create()
    {
        return view('activations.create');
    }

    /**
     * Simpan logger baru ke Python API
     */
    public function store(Request $request)
    {
        $request->validate([
            'logger_id'   => 'required',
            'logger_name' => 'required',
            'user_email'  => 'required|email',
        ]);

        try {
            // Kirim data ke Python API (Endpoint: POST /api/loggers)
            $response = Http::withHeaders([
                'X-API-KEY' => $this->apiKey
            ])->post($this->pythonApi . "/loggers", [
                'logger_id'   => $request->logger_id,
                'logger_name' => $request->logger_name,
                'user_email'  => $request->user_email,
            ]);
            
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Koneksi ke API Putus: ' . $e->getMessage()])->withInput();
        }
    }

    /**
     * 2. GENERATE TOKEN
     */
    public function generate($id): JsonResponse
    {
        try {
            /** @var \Illuminate\Http\Client\Response $response */
            $response = Http::withHeaders([
                'X-API-KEY' => $this->apiKey
            ])->post($this->pythonApi . "/generate/" . $id);

            if ($response->status() >= 200 && $response->status() < 300) {
                return response()->json(['status' => 'success', 'message' => 'Token Generated']);
            }
            return response()->json(['status' => 'error', 'message' => 'API Error: ' . $response->status()], 500);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => 'Connection Failed'], 500);
        }
    }

    /**
     * 3. DELETE LOGGER
     */
    public function destroy($id): JsonResponse
{
    try {
        /** @var \Illuminate\Http\Client\Response $response */
        $response = Http::withHeaders([
            'X-API-KEY' => $this->apiKey
        ])->delete($this->pythonApi . "/loggers/" . $id);

        if ($response->status() === 200) {
            return response()->json([
                'status' => 'success',
                'message' => 'Logger deleted successfully'
            ]);
        }

        return response()->json([
            'status' => 'error',
            'message' => 'Failed to delete from API'
        ], 500);

    } catch (\Exception $e) {
        return response()->json([
            'status' => 'error',
            'message' => 'Connection error: ' . $e->getMessage()
        ], 500);
    }
}
}