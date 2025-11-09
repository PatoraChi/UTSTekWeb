<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Like extends Model
{
    use HasFactory;

    // Izinkan kolom ini diisi massal
    protected $fillable = ['user_id', 'post_id'];
}