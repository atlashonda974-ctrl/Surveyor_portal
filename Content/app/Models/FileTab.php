<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FileTab extends Model
{
    use HasFactory;

    
    protected $table = 'filestab';

    
    public $timestamps = false;

    
    protected $fillable = [
        'datetime_field',
        'doc_no',
        'remarks',
        'estimate_amount',
        'created_by',
        'file_path',
        'rep_tag'   ,
        'plr_final',
        'updated_by',
        'app_rem',
        'created_at', 
    'updated_at'   ,   
    ];
}
