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
        $this->pythonApi = env('PYTHON_API_URL', 'http://127.0.0.1:8000/api');
        $this->apiKey    = env('PYTHON_API_KEY', 'TRUSUR_SECRET_KEY_2024');
    }

    /**
     * 1. INDEX - Menampilkan Tabel & Cek Status Engine
     */
    public function index(Request $request)
    {
        // CEK STATUS PORT 8000 (Apakah ada yang LISTENING?)
        $checkPort = shell_exec('netstat -ano | findstr :8000 | findstr LISTENING');
        $isApiRunning = !empty($checkPort);

        if ($request->ajax()) {
            $data = [];
            // Hanya tembak API jika statusnya terlihat Running
            if ($isApiRunning) {
                try {
                    /** @var \Illuminate\Http\Client\Response $response */
                    $response = Http::withHeaders([
                        'X-API-KEY' => $this->apiKey,
                        'Accept'    => 'application/json'
                    ])->timeout(2)->get($this->pythonApi . "/loggers");

                    if ($response->status() === 200) {
                        $data = $response->json();
                    }
                } catch (\Exception $e) {
                    $data = [];
                }
            }

            return DataTables::of($data)
                ->addIndexColumn()
                ->editColumn('token', function($row) {
                    $token = $row['token'] ?? 'None';
                    if ($token === 'Expired') return '<span class="text-danger fw-bold"><i class="ti ti-clock-off"></i> EXPIRED</span>';
                    if ($token === 'None' || $token === '') return '<span class="text-muted">----------</span>';
                    return '<code class="token-secret">' . $token . '</code>';
                })
                ->editColumn('activation_code', function($row) {
                    $code = $row['activation_code'] ?? 'None';
                    if ($code === 'Expired' || $code === 'None') return '<span class="text-muted">----</span>';
                    return '<span class="badge" style="background: rgba(0, 242, 255, 0.1); border: 1px dashed #00f2ff; color: #00f2ff; padding: 5px 10px;">' . $code . '</span>';
                })
                ->editColumn('status', function($row) {
                    $status = $row['status'] ?? 'Pending';
                    if ($status === 'Active') return '<span style="color:#10b981; font-weight: bold;"><i class="ti ti-circle-filled"></i> Active</span>';
                    if ($status === 'Pending') return '<span style="color:#fbbf24; font-weight: bold;"><i class="ti ti-circle-filled"></i> Pending</span>';
                    if ($status === 'Requesting') return '<span style="color:#d8b4fe; font-weight: bold;"><i class="ti ti-alert-octagon pulse-neon"></i> Requesting</span>';
                    return '<span style="color:#f43f5e; font-weight: bold;"><i class="ti ti-circle-filled"></i> Expired</span>';
                })
                ->addColumn('action', function($row) {
                    $id = $row['logger_id'] ?? '';
                    return '<button type="button" class="btn btn-outline-info btn-sm me-1 btn-generate" data-id="' . $id . '" title="Generate"><i class="ti ti-refresh"></i></button>
                            <button type="button" class="btn btn-outline-danger btn-sm btn-delete" data-id="' . $id . '" title="Delete"><i class="ti ti-trash"></i></button>';
                })
                ->rawColumns(['token', 'activation_code', 'status', 'action'])
                ->make(true);
        }

        return view('activations.index', compact('isApiRunning'));
    }

    /**
     * NYALAKAN ENGINE (Tanpa --reload agar lebih stabil untuk dikontrol tombol)
     */
    public function startEngine()
    {
        try {
            $path = base_path();
            // Menggunakan start /B agar berjalan di background tanpa membuka jendela CMD baru
            $cmd = "cd /d $path && start /B python -m uvicorn backend_api:app --host 127.0.0.1 --port 8000 > NUL 2>&1";

            pclose(popen($cmd, "r"));

            sleep(3); // Beri waktu uvicorn untuk inisialisasi
            return back()->with('swal_success', 'Backend Engine Started Successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to start: ' . $e->getMessage());
        }
    }

    /**
     * MATIKAN ENGINE (PowerShell Force Kill)
     */
    public function stopEngine()
    {
        try {
            // Mencari PID yang dengerin port 8000 lalu dimatikan secara paksa
            $psCommand = 'Get-NetTCPConnection -LocalPort 8000 -ErrorAction SilentlyContinue | Select-Object -ExpandProperty OwningProcess | ForEach-Object { Stop-Process -Id $_ -Force -ErrorAction SilentlyContinue }';
            $cmd = "powershell -Command \"$psCommand\"";

            shell_exec($cmd);
            sleep(2);

            return back()->with('swal_success', 'Backend Engine Terminated.');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to stop: ' . $e->getMessage());
        }
    }

    /**
     * FUNGSI LAINNYA (GetNotif, Store, Generate, Destroy)
     */
    public function getAdminNotifications(): JsonResponse
    {
        try {
            $response = Http::withHeaders(['X-API-KEY' => $this->apiKey])->timeout(2)->get($this->pythonApi . "/notifications");
            return response()->json($response->json());
        } catch (\Exception $e) { return response()->json([]); }
    }

    public function create() { return view('activations.create'); }

    public function store(Request $request)
    {
        $request->validate(['logger_id' => 'required', 'logger_name' => 'required', 'user_email' => 'required|email']);
        try {
            Http::withHeaders(['X-API-KEY' => $this->apiKey])->post($this->pythonApi . "/loggers", [
                'logger_id' => $request->logger_id, 'logger_name' => $request->logger_name, 'user_email' => $request->user_email,
            ]);
            return redirect()->route('activations.index')->with('success', 'Logger Registered.');
        } catch (\Exception $e) { return back()->withErrors(['error' => 'API Connection Failed']); }
    }

    public function generate($id): JsonResponse
    {
        try {
            $response = Http::withHeaders(['X-API-KEY' => $this->apiKey])->post($this->pythonApi . "/generate/" . $id);
            return response()->json(['status' => 'success']);
        } catch (\Exception $e) { return response()->json(['status' => 'error'], 500); }
    }

    public function destroy($id): JsonResponse
    {
        try {
            Http::withHeaders(['X-API-KEY' => $this->apiKey])->delete($this->pythonApi . "/loggers/" . $id);
            return response()->json(['status' => 'success']);
        } catch (\Exception $e) { return response()->json(['status' => 'error'], 500); }
    }
}