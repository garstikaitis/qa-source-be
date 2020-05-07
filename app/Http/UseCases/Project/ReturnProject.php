<?php

namespace App\Http\UseCases\Project;

use App\Models\File;
use App\Models\Project;
use Illuminate\Support\Str;
use App\Helpers\FormHelpers;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class ReturnProject {

	private $project;
	private $file;

	public function __construct(array $request) {
		$this->request = $request;
	}

	public function handle() {
		
		$this->validate();

		$this->setDefaultValues();

		$this->validateUser();

		$this->uploadFile();

		$this->persistFileEntry();

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
			'userId' => 'required|integer|exists:users,id',
			'file' => 'required|file',
		], FormHelpers::validationMessages());

		if ($validator->fails()) {
			abort(403, $validator->errors()->first());
		}
	}

	private function setDefaultValues() {
		$this->project = Project::findOrFail($this->request['projectId']);
	}

	private function uploadFile() {
		Storage::putFileAs('submissions/' . $this->project->id, $this->request['file'], $this->request['file']->getClientOriginalName());
	}

	private function persistFileEntry() {
		$file = $this->request['file'];
		$this->file = File::create([
			'original_filename' => $file->getClientOriginalName(),
			'filename' => $file->hashName(),
			'mime' => $file->getMimeType(),
		]);
	}

	private function returnProject() {
		$this->project->update(['status' => Project::FINISHED, 'submission_file_id' => $this->file->id]);
	}
}