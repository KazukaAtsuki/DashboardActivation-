<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LoggerActivation extends Model
{
    use HasFactory;
    protected $guarded = []; // Biar bisa create data langsung
}