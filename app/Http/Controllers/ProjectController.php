<?php

namespace App\Http\Controllers;

use App\Models\Project;
use Illuminate\Support\Facades\Auth;
use App\Http\UseCases\Project\GetProjects;
use App\Http\UseCases\Project\ReturnProject;
use App\Http\UseCases\Project\ApplyToProject;

class ProjectController extends Controller
{
    public function applyToProject() {
        try {
            return (new ApplyToProject(request()->all()))->handle();
        } catch (\Throwable $e) {
            return response(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function getProjects() {
		try {
            return (new GetProjects(request()->all()))->handle();
		} catch(\Throwable $e) {
			return response([ 'success' => false, 'message' => $e->getMessage() ], 500);
		}
	}

    
    public function returnProject() {
        try {
            return (new ReturnProject(request()->all()))->handle();
        } catch (\Throwable $e) {
            echo $e->getMessage();
            return response(['success' => false, 'message' => $e->getMessage()], 500);
        }
	}
}
