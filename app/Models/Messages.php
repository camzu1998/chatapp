<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

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

    public function save($Nick = '', $Content = ''){
        if(empty($Nick) || empty($Content))
            return false;

        $Date = date('Y-m-d H:i:s');
        DB::table($this->table)->insert([
            'nick'       => $Nick,
            'content'    => $Content,
            'created_at' => $Date
        ]);

        return true;
    }
}
