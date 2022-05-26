<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Files extends Model
{
    use HasFactory;

    protected $table = 'files';

    protected $fillable = [
        'filename',
        'path',
        'ext'
    ];

    protected $attributes = [
        'filename' => false,
        'path' => false,
        'ext' => false
    ];

    public function message()
    {
        return $this->belongsTo(Messages::class);
    }
}
