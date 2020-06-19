<?php

namespace App\Http\Controllers;

use App\Models\Company;
use Illuminate\Support\Facades\Auth;
use App\Http\UseCases\Company\CreateCompany;
use Illuminate\Auth\AuthenticationException;
use App\Http\UseCases\Company\AddUserToCompany;

class CompanyController extends Controller
{
    public function createCompany() {
		try {
			return (new CreateCompany(request()->all()))->handle();
        } catch (\Throwable $e) {
            return response(['success' => false, 'message' => $e->getMessage()], 500);
        }
	}
	
	public function getCompanies() {
		try {
			if(!Auth::user()->is_admin) abort(403, 'Access denied');
			return response(['success' => true, 'data' => Company::all()], 200);
		} catch(\Throwable $e) {
			return response([ 'success' => false, 'message' => $e->getMessage() ], 500);
		}
	}

	public function updateCompany() {
		try {
			if(!Auth::user()->is_admin) abort(403, 'Access denied');
			$company = Company::findOrFail(request()->get('companyId'));
			$company->name = request()->get('name') ? request()->get('name') : $company->name;
			$company->slug = request()->get('slug') ? request()->get('slug') : $company->slug;
			$company->credits_remaining = request()->get('credits_remaining') ? request()->get('credits_remaining') : $company->credits_remaining; 
			$company->save();
			return response(['success' => true], 200);
		} catch(\Throwable $e) {
			return response([ 'success' => false, 'message' => $e->getMessage() ], 500);
		}
	}

	public function addUserToCompany() {
		try {
			return (new AddUserToCompany(request()->all()))->handle();
		} catch(\Throwable $e) {
			return response([ 'success' => false, 'message' => $e->getMessage() ], 500);
		}
	}
}
