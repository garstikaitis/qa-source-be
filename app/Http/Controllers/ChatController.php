<?php

namespace App\Http\Controllers;

use Twilio\Jwt\AccessToken;
use Illuminate\Http\Request;
use Twilio\Jwt\Grants\ChatGrant;
use App\Http\UseCases\Chat\CreateChatRoom;

class ChatController extends Controller
{
    public function chat(Request $request) {
        try {
                return (new CreateChatRoom($request->all()))->handle();
        } catch(\Throwable $e) {
                return response(['success' => false, 'message' => 'Something went wrong', 'debug' => $e->getMessage()]);
        }
    }

    public function generateChatToken(Request $request) {
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
