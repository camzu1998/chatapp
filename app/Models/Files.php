<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;



class Files extends Model
{
    use HasFactory;

    protected $table = 'files';

    protected $attributes = [
        'filename' => false,
        'path' => false,
        'ext' => false,
        'created_at'  => '1998-07-14 14:00:00',
        'updated_at'  => '1998-07-14 14:00:00'
    ];

    public $timestamps = true;
    public $incrementing = true;
    protected $primaryKey = 'id';

    /**
     * Scope a query to only include room messages
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeRoom($query, int $room_id)
    {
        return $query->where('room_id', $room_id)->latest();
    }


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
