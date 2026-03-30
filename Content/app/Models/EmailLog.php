<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmailLog extends Model
{
    use HasFactory;

    protected $table = 'email_logs';

    protected $fillable = [
        'uw_doc',
        'curdatetime',
        'sender',
        'receiver',
        'sub',
        'body',
        'rep_name',
        'created_by',
        'route',
        'email_cc',
    ];

    
}