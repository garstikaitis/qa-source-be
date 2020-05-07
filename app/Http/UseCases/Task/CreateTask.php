<?php

namespace App\Http\UseCases\Task;

use Carbon\Carbon;
use App\Model\Task;
use App\Models\Company;
use App\Helpers\FormHelpers;
use App\Models\Project;
use Illuminate\Support\Facades\Validator;

class CreateTask {

	private $task;
	private $company;
	private $price;
	private $project;

	public function __construct(array $request) {
		$this->request = $request;
	}

	public function handle() {
		
		$this->validate();

		$this->getPriceInCredits();

		$this->validateCredits();

		$this->deductCredits();

		$this->createTask();

		return response(['success' => true, 'message' => 'Successfuly created task', 'data' => $this->task], 201);

	}

	private function getPriceInCredits() {
		$deadline = (new Carbon($this->request['deadline']));
		$deadlineInHours = Carbon::now("Europe/Copenhagen")->diffInHours($deadline->endOfHour());
		$this->price = (int)ceil($deadlineInHours / 3);
	}

	private function validateCredits() {
		$this->company = Company::findOrFail($this->request['companyId']);
		if($this->company->credits_remaining < $this->price + 10) abort(403, 'Insufficient credits');
		return;
	}

	private function deductCredits() {
		$credits_remaining = $this->company->credits_remaining - $this->price;
		$this->company->credits_remaining = $credits_remaining;
		$this->company->save();
	}

	private function validate() {
		$validator = Validator::make($this->request, [
			'name' => 'required|string',
			'description' => 'required|string',
			'companyId' => 'required|integer|exists:companies,id',
			'deadline' => 'date_format:Y-m-d H:i:s',
			'type' => 'required|string',
		], FormHelpers::validationMessages());

		if ($validator->fails()) {
			abort(403, $validator->errors()->first());
		}
	}

	private function createTask() {
		$this->request['deadline'] = (new Carbon($this->request['deadline']))->endOfHour()->toDateTimeString();
		$this->task = Task::create($this->request);
	}
}