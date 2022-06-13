<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use App\Models\Files;
use Illuminate\Support\Facades\Storage;

class FilesController extends Controller
{
    public function store(Request $request)
    {
        $file_data = array();

        $file_req = $request->file('file');
        $filename = $file_req->getClientOriginalName();
        //Store on disk
        $path = $file_req->store('files', 'public');
        //Fill array
        $files_data = pathinfo($path);
        $file = Files::create([
            'filename'   => $filename,
            'path'       => $path,
            'ext'        => $file_req->extension(),
            'created_at' => date('Y-m-d H:i:s')
        ]);
        $file_data['file_id'] = $file->id;

        return $file_data;
    }
}
