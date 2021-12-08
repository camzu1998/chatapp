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


    public function store(Request $request){        
        $files = new Files();
        $file_data = array();

        $file = $request->file('file');
        $filename = $file->getClientOriginalName();
        //Store on disk
        $path = $file->store('files', 'public');
        //Fill array
        $files_data = pathinfo($path);
        $file_data['file_id'] = $files->create($filename, $path, $file->extension());

        return $file_data;
    }
}
