<?php

namespace App\Http\Controllers;

use App\Models\User;
use Twilio\Rest\Client;
use Twilio\Jwt\AccessToken;
use Illuminate\Http\Request;
use Twilio\Jwt\Grants\ChatGrant;

class ChatController extends Controller
{
    public function chat(Request $request)
{
        $authUser = $request->user();
        $otherUser = User::find(1);
        $users = User::where('id', '<>', $authUser->id)->get();

        $twilio = new Client(env('TWILIO_ACCOUNT_SID'), env('TWILIO_AUTH_TOKEN'));

        // Fetch channel or create a new one if it doesn't exist
        try {
                $channel = $twilio->chat->v2->services(env('TWILIO_CHAT_SID'))
                        ->channels('test')
                        ->fetch();
        } catch (\Twilio\Exceptions\RestException $e) {
                $channel = $twilio->chat->v2->services(env('TWILIO_CHAT_SID'))
                        ->channels
                        ->create([
                                'uniqueName' => 'test',
                                'type' => 'private',
                        ]);
        }

        // Add first user to the channel
        try {
                $twilio->chat->v2->services(env('TWILIO_CHAT_SID'))
                        ->channels('test')
                        ->members($authUser->email)
                        ->fetch();

        } catch (\Twilio\Exceptions\RestException $e) {
                $member = $twilio->chat->v2->services(env('TWILIO_CHAT_SID'))
                        ->channels('test')
                        ->members
                        ->create($authUser->email);
        }

        // Add second user to the channel
        try {
                $twilio->chat->v2->services(env('TWILIO_CHAT_SID'))
                        ->channels('test')
                        ->members($otherUser->email)
                        ->fetch();

        } catch (\Twilio\Exceptions\RestException $e) {
                $twilio->chat->v2->services(env('TWILIO_CHAT_SID'))
                        ->channels('test')
                        ->members
                        ->create($otherUser->email);
        }
        return response()->json(['success', true]);
    }
    public function generateChatToken(Request $request) {
        //     dd(env('TWILIO_AUTH_TOKEN'), env('TWILIO_API_KEY'), env('TWILIO_API_SECRET'), env('TWILIO_CHAT_SID'), $request->email);
        $token = new AccessToken(
                env('TWILIO_ACCOUNT_SID'),
                env('TWILIO_API_KEY'),
                env('TWILIO_API_SECRET'),
                3600,
                $request->email
        );


        $chatGrant = new ChatGrant();
        $chatGrant->setServiceSid(env('TWILIO_CHAT_SID'));
        $token->addGrant($chatGrant);

        return response()->json([
            'token' => $token->toJWT()
        ]);
    }
}
