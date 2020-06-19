<?php

namespace App\Http\UseCases\Task;

use Carbon\Carbon;
use App\Models\Task;
use App\Models\File;
use App\Models\Company;
use App\Helpers\FormHelpers;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class CreateTask {

	private $task;
	private $company;
	private $price;
	private $file;

	public function __construct(array $request) {
		$this->request = $request;
	}

	public function handle() {
		
		$this->validate();

		$this->getPriceInCredits();

		$this->validateCredits();

		$this->deductCredits();

		$this->createTask();

		if(!is_null($this->request['file'])) {
			
			$this->uploadFile();
	
			$this->persistFileEntry();
	
			$this->task->update(['file_id' => $this->file->id]);

		}


		return response(['success' => true, 'message' => 'Successfuly created task', 'data' => $this->task], 201);

	}

	private function getPriceInCredits() {
		$deadline = (new Carbon($this->request['deadline']));
		$deadlineInHours = Carbon::now("Europe/Copenhagen")->diffInHours($deadline->endOfHour());
		$this->price = (int)ceil($deadlineInHours / 3);
	}

	private function uploadFile() {
		Storage::putFileAs('taskFiles/' . $this->task->id, $this->request['file'], $this->request['file']->getClientOriginalName());
	}

	private function persistFileEntry() {
		$file = $this->request['file'];
		$this->file = File::create([
			'original_filename' => $file->getClientOriginalName(),
			'filename' => $file->hashName(),
			'mime' => $file->getMimeType(),
		]);
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
			'file' => 'nullable|file',
			'clientId' => 'required|integer|exists:users,id'
		], FormHelpers::validationMessages());

		if ($validator->fails()) {
			abort(403, $validator->errors()->first());
		}
	}

	private function createTask() {
		$this->request['deadline'] = (new Carbon($this->request['deadline']))->endOfHour()->toDateTimeString();
		$this->request['client_id'] = $this->request['clientId'];
		$this->task = Task::create($this->request);
	}
}