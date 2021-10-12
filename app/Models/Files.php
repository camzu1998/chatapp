<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;



class Files extends Model
{
    use HasFactory;

    protected $table = 'files';

    public function save($name = '', $path = '', $ext = ''){
        if(empty($name) || empty($path) || empty($ext))
            return false;

        $Date = date('Y-m-d H:i:s');
        
        DB::table($this->table)->insert([
            'filename'   => $name,
            'path'       => $path,
            'ext'        => $ext,
            'created_at' => $Date
        ]);

        return true;
    }
}
