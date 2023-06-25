<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CatchRecipt extends Model
{
    use HasFactory;
    protected $fillable = [
        'id',
        'from_account',
        'to_account',
        'client',
        'amount',
        'notes',
        'date',
        'docNumber',
        'payment_type'
    ];


}
