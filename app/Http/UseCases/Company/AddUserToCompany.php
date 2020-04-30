<?php

namespace App\Http\UseCases\Company;

use App\Models\User;
use App\Models\Company;
use App\Helpers\FormHelpers;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class AddUserToCompany {

	private $company;
	private $user;

	public function __construct(array $request) {
		$this->request = $request;
	}

	public function handle() {
		
		$this->validate();

		$this->checkIfLoggedInUserIsAdmin();

		$this->checkIfUserCanBeMovedToCompany();

		$this->setCompany();

		$this->addUserToCompany();

		return response(['success' => true, 'message' => 'Successfuly added user to company', 'data' => $this->company], 201);

	}

	private function checkIfLoggedInUserIsAdmin() {
		if(!Auth::user()->is_admin) abort(403, 'Access denied');
		return;
	}

	private function checkIfUserCanBeMovedToCompany() {
		$this->user = User::findOrFail($this->request['userId']);
		if($this->user->is_admin) abort(403, 'Admins can not belong to company');
		return;
	}

	private function validate() {
		$validator = Validator::make($this->request, [
			'companyId' => 'required|integer|exists:companies,id',
			'userId' => 'required|integer|exists:users,id'
		], FormHelpers::validationMessages());

		if ($validator->fails()) {
			abort(403, $validator->errors()->first());
		}
	}

	private function setCompany() {
		$this->company = Company::findOrFail($this->request['companyId']);
	}

	private function addUserToCompany() {
		$this->company->users()->sync([$this->user->id], false);
	}
}