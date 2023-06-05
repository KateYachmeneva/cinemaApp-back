<?php

namespace App\Http\Controllers;



use Storage;

class FileController extends Controller {

    public function loadFile($fileName): ?string
    {
        return Storage::disk('public')->get($fileName);
    }
}