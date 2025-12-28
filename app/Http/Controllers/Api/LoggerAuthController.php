<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\LoggerActivation;

class LoggerAuthController extends Controller
{
    public function activate(Request $request)
    {
        // 1. Validasi Input dari Python
        $request->validate([
            'logger_id' => 'required',
            'activation_code' => 'required'
        ]);

        // 2. Cari di Database
        $logger = LoggerActivation::where('logger_id', $request->logger_id)
                    ->where('activation_code', $request->activation_code)
                    ->first();

        // 3. Cek Apakah Ketemu & Aktif?
        if (!$logger) {
            return response()->json([
                'status' => 'error',
                'message' => 'Logger ID atau Kode Aktivasi Salah!'
            ], 401);
        }

        if ($logger->status !== 'Active') {
            return response()->json([
                'status' => 'error',
                'message' => 'Logger ini statusnya Inactive (Nonaktif).'
            ], 403);
        }

        // 4. Jika Sukses, Kirim Token Rahasia
        return response()->json([
            'status' => 'success',
            'message' => 'Aktivasi Berhasil!',
            'data' => [
                'logger_name' => $logger->logger_name,
                'token' => $logger->token // <--- INI KUNCINYA
            ]
        ]);
    }
}