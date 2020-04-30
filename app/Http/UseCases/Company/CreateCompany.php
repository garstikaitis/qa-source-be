<?php

namespace App\Http\UseCases\Company;

use App\Models\Company;
use App\Helpers\FormHelpers;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class CreateCompany {

	private $company;

	public function __construct(array $request) {
		$this->request = $request;
	}

	public function handle() {
		
		$this->validate();

		$this->checkIfUserIsAdmin();

		$this->createCompany();

		return response(['success' => true, 'message' => 'Successfuly created company', 'data' => $this->company], 201);

	}

	private function checkIfUserIsAdmin() {
		if(!Auth::user()->is_admin) abort(403, 'Access denied');
		return;
	}

	private function validate() {
		$validator = Validator::make($this->request, [
			'name' => 'required|string',
			'slug' => 'required|string',
			'credits_remaining' => 'required|integer'
		], FormHelpers::validationMessages());

		if ($validator->fails()) {
			abort(403, $validator->errors()->first());
		}
	}

	private function createCompany() {
		$this->company = Company::create($this->request);
	}
}