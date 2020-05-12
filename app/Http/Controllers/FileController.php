<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class FileController extends Controller
{

	public function downloadFile(string $type, int $fileId) {
		try {			
			// if(!Auth::user()) abort(403, 'Access denied');
			$files = Storage::files($type . '/' . $fileId . '/');
			return Storage::download($files[0]);
		} catch(\Throwable $e) {
			return response([ 'success' => false, 'message' => $e->getMessage() ], 500);
		}
	}

}
