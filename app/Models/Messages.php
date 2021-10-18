<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use App\Models\Files;

class Messages extends Model
{
    use HasFactory;

    protected $table = 'messages';

    protected $form = [
        'nick',
        'content',
        'date'
    ];

    public function get($limiter = null){
        if(empty($limiter) && is_numeric($limiter)){
            return  DB::table($this->table)->select()->take($limiter)->latest()->get();
        }else{
            return  DB::table($this->table)->select()->latest()->get();   
        }
    }
    public function get_last(){
        return  DB::table($this->table)->select()->latest()->first(); 
    }

    public function save($Nick = '', $Content = '', $file_id = 0, $user_id = 0){
        if(empty($Nick) || empty($Content) || empty($user_id))
            return false;

        $Date = date('Y-m-d H:i:s');
        DB::table($this->table)->insert([
            'nick'       => $Nick,
            'content'    => $Content,
            'file_id'    => $file_id,
            'created_at' => $Date,
            'user_id'    => $user_id
        ]);

        return true;
    }
}
