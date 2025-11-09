<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Follow extends Model
{
    use HasFactory;
    
    // Izinkan kolom ini diisi
    protected $fillable = ['follower_id', 'following_id'];
    
    // Kita tidak pakai 'id' di tabel ini, jadi matikan
    public $incrementing = false;
    
    // Kita pakai primary key gabungan
    protected $primaryKey = ['follower_id', 'following_id'];
}