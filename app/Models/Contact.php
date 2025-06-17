<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Contact extends Model
{
    //
    use HasFactory;

    protected $fillable = [
        'client_id',
        'name',
        'email',
        'gender',
        'nationality',
        'age',
        'email_data'
    ];

    protected $casts = [
        'email_data' => 'array',
    ];

    public function client()
    {
        return $this->belongsTo(Client::class);
    }
}
