<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JustifyRequest extends Model
{
    use HasFactory;
    protected $fillable = ['user_id','type','reason','medical_report_file'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}