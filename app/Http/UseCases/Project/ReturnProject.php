<?php

namespace App\Http\UseCases\Project;

use App\Model\Task;
use App\Models\User;
use App\Models\Project;
use App\Helpers\FormHelpers;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class ReturnProject {

	private $project;

	public function __construct(array $request) {
		$this->request = $request;
	}

	public function handle() {
		
		$this->validate();

		$this->setDefaultValues();

		$this->validateUser();

		$this->returnProject();

		return response(['success' => true, 'message' => 'Successfuly returned project', 'data' => $this->project], 200);

	}

	protected function validateUser() {
		if(Auth::user()->is_admin) return true;
		if(Auth::user()->companies()->count()) abort(403, 'Clients cannot return projects');
		if(Auth::id() != $this->project->userId) abort(403, 'Auth id and project id are diferent');
	}

	private function validate() {
		$validator = Validator::make($this->request, [
			'projectId' => 'required|integer|exists:projects,id',
			'userId' => 'required|integer|exists:users,id'
		], FormHelpers::validationMessages());

		if ($validator->fails()) {
			abort(403, $validator->errors()->first());
		}
	}

	private function setDefaultValues() {
		$this->project = Project::findOrFail($this->request['projectId']);
	}

	private function returnProject() {
		$this->project->update(['status' => Project::FINISHED]);
	}
}