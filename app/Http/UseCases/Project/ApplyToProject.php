<?php

namespace App\Http\UseCases\Project;

use App\Models\Task;
use App\Models\User;
use App\Models\Project;
use App\Helpers\FormHelpers;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class ApplyToProject {

	private $project;
	private $task;
	private $user;

	public function __construct(array $request) {
		$this->request = $request;
	}

	public function handle() {
		
		$this->validate();

		$this->validateUser();

		$this->setDefaultValues();

		$this->createProject();

		return response(['success' => true, 'message' => 'Successfuly created task', 'data' => $this->project], 201);

	}

	protected function validateUser() {
		$this->user = User::findOrFail($this->request['userId']);
		if($this->user->isAdmin()) abort(403, 'Admins cannot apply to projects');
		if($this->user->companies()->count()) abort(403, 'Clients cannot apply to projects');
	}

	private function validate() {
		$validator = Validator::make($this->request, [
			'taskId' => 'required|integer|exists:tasks,id',
			'userId' => 'required|integer|exists:users,id'
		], FormHelpers::validationMessages());

		if ($validator->fails()) {
			abort(403, $validator->errors()->first());
		}
	}

	private function setDefaultValues() {
		$this->task = Task::findOrFail($this->request['taskId']);
		$this->user = User::findOrFail($this->request['userId']);
	}

	private function createProject() {
		$this->request['status'] = Project::STARTED;
		$this->project = Project::create($this->request);
	}
}