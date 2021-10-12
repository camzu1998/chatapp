<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use App\Models\Files;
use Illuminate\Support\Facades\Storage;

class FilesController extends Controller
{
    protected $table = 'files';

    protected $form = [
        'file'
    ];


    public function save(Request $request){

        $file = $request->file('file');
        $filename = $file->getClientOriginalName();

        $path = $file->store('public/files');
        
        $files = new \App\Models\Files;

        return $files->save($filename, $path, $file->extension());
    }
}
