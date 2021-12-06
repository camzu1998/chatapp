<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;



class Files extends Model
{
    use HasFactory;

    protected $table = 'files';

    public function get($id = null){
        if(empty($id))
            return false;
        
        return DB::table($this->table)->select()->where('id', '=', $id)->get();
    }

    public function create(string $name,string $path,string $ext){
        if(empty($name) || empty($path) || empty($ext))
            return false;

        $date = date('Y-m-d H:i:s');
        
        $file_id = DB::table($this->table)->insertGetId([
            'filename'   => $name,
            'path'       => $path,
            'ext'        => $ext,
            'created_at' => $date
        ]);

        return $file_id;
    }
}
