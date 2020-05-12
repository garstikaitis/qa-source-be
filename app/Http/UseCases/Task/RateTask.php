<?php

namespace App\Http\UseCases\Task;

use App\Models\Project;
use App\Helpers\FormHelpers;
use App\Models\TaskRating;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class RateTask {

	private $project;
	private $rating;

	public function __construct(array $request) {
		$this->request = $request;
	}

	public function handle() {
		
		$this->validate();

		$this->validateNotAdmin();

		$this->setDefaultValues();

		$this->checkIfProjectIsFinished();

		$this->checkIfUsersAreInvolvedInTask();

		$this->createRating();

		return response(['success' => true, 'message' => 'Successfuly rated task', 'data' => $this->rating], 201);

	}

	private function validateNotAdmin() {
		if(Auth::user()->isAdmin()) abort(403, 'Admins can not rate tasks');
	}

	private function setDefaultValues() {
		$this->project = Project::where('taskId', $this->request['taskId'])->firstOrFail();
	}

	private function checkIfProjectIsFinished() {
		if($this->project->status !== Project::FINISHED) abort(403, 'Project must be finished before rating task');
	}

	private function checkIfUsersAreInvolvedInTask() {
		if($this->project->userId !== $this->request['created_by']) return;
		if($this->project->userId !== $this->request['given_to']) return;
		abort(403, 'User needs to be assosiated with this task');
	}

	private function createRating() {
		$this->rating = TaskRating::create($this->request);
	}

	private function validate() {
		$validator = Validator::make($this->request, [
			'rating' => 'required|integer',
			'comment' => 'nullable|string',
			'created_by' => 'required|integer|exists:users,id',
			'given_to' => 'required|integer|exists:users,id',
			'taskId' => 'required|integer|exists:tasks,id',
		], FormHelpers::validationMessages());

		if ($validator->fails()) {
			abort(403, $validator->errors()->first());
		}
	}
}