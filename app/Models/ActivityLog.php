<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class ActivityLog extends Model
{
    use HasFactory;
    protected $guarded = [];

    // Relasi ke User
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // --- FUNGSI AJAIB (Helper) ---
    // Cara pakai: ActivityLog::record('CREATE', 'Menambah data stack');
    public static function record($action, $description)
    {
        if (Auth::check()) {
            self::create([
                'user_id' => Auth::id(),
                'action'  => $action,
                'description' => $description
            ]);
        }
    }
}
