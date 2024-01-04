<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Store extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'created_by',
        'users',
        'shopify_store_public_key',
        'shopify_store_private_key',
        'notices',
        'logo_file'
    ];

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function notices()
    {
        return $this->belongsTo(Notice::class);
    }
}
