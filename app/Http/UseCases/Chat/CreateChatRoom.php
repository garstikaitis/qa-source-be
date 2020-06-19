<?php

namespace App\Http\UseCases\Chat;

use App\Models\Task;
use App\Models\User;
use App\Models\Company;
use Twilio\Rest\Client;
use App\Helpers\FormHelpers;
use Illuminate\Support\Facades\Auth;
use Twilio\Exceptions\RestException;
use Illuminate\Support\Facades\Validator;

class CreateChatRoom {

	private $tester;
	private $companyClient;
	private $channelName;
	private $task;

	public function __construct(array $request) {
		$this->request = $request;
		$this->twilio = new Client(env('TWILIO_ACCOUNT_SID'), env('TWILIO_AUTH_TOKEN'));
	}

	public function handle() {
		
		$this->validate();

		$this->setUsers();

		$this->setChannelName();

		$this->handleFetchChannel();

		$this->addTesterToChannel();

		$this->addCompanyClientToChannel();

		return response(['success' => true, 'message' => 'Successfuly created chat room', 'data' => null], 201);

	}

	private function setUsers() {

		$this->tester = Auth::user();

		$this->companyClient = User::findOrFail($this->request['clientId']);

		$this->task = Task::findOrFail($this->request['taskId']);
	}

	private function setChannelName() {

		$this->channelName = "{$this->tester->id}-{$this->companyClient->id}-{$this->task->id}";

	}

	private function handleFetchChannel() {
		try {
			$this->twilio->chat->v2->services(env('TWILIO_CHAT_SID'))
					->channels($this->channelName)
					->fetch();
		} catch (RestException $e) {
			$this->twilio->chat->v2->services(env('TWILIO_CHAT_SID'))
					->channels
					->create([
							'uniqueName' => $this->channelName,
							'type' => 'private',
					]);
		}
	}

	private function addTesterToChannel() {
		try {
			$this->twilio->chat->v2->services(env('TWILIO_CHAT_SID'))
					->channels($this->channelName)
					->members($this->tester->email)
					->fetch();

		} catch (RestException $e) {
				$this->twilio->chat->v2->services(env('TWILIO_CHAT_SID'))
						->channels($this->channelName)
						->members
						->create($this->tester->email);
		}
	}

	private function addCompanyClientToChannel() {
		try {
			$this->twilio->chat->v2->services(env('TWILIO_CHAT_SID'))
					->channels($this->channelName)
					->members($this->companyClient->email)
					->fetch();

		} catch (RestException $e) {
				$this->twilio->chat->v2->services(env('TWILIO_CHAT_SID'))
						->channels($this->channelName)
						->members
						->create($this->companyClient->email);
		}
	}

	private function validate() {
		$validator = Validator::make($this->request, [
			'clientId' => 'required|integer|exists:users,id',
			'taskId' => 'required|integer|exists:tasks,id',
		], FormHelpers::validationMessages());

		if ($validator->fails()) {
			abort(403, $validator->errors()->first());
		}
	}
}