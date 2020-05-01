<?php

namespace App\Http\UseCases\Project;

use App\Models\Project;
use Illuminate\Support\Facades\Auth;

class GetProjects {

	private $data;

	public function __construct(array $request) {
		$this->request = $request;
	}

	public function handle() {
		
		if(Auth::user()->isAdmin()) { $this->handleIsAdmin(); }
		if(Auth::user()->isClient()) { $this->handleIsClient(); }
		if(Auth::user()->isTester()) { $this->handleIsTester(); }

		return response(['success' => true, 'message' => 'Successfuly returned project', 'data' => $this->data], 200);

	}

	private function handleIsAdmin() {
		$this->data = Project::with('task.company', 'tester')->get();
	}

	private function handleIsClient() {
		$this->data = Project::whereHas('task', function($q) {
			$q->where('companyId', Auth::user()->companies()->first()->id);
		})->with('tester', 'task.company')->get(); 
	}

	private function handleIsTester() {
		$this->data = Project::where('userId', Auth::id())->with('task.company', 'tester')->get();
	}
}