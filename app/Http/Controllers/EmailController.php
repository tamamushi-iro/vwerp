<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Mail\ClientEventEmail;
use App\Event;
use Validator;

class EmailController extends Controller {

    public function __construct() {
        $this->middleware('auth:api,admins');
    }

    public function sendClientEventEmail(Request $request, Event $event) {

        if($event->mail_sent) {
            return response()->json([
                'code' => 400,
                'status' => false,
                'message' => 'An email has already been sent to the client.'
            ], 400);
        }

        $validator = Validator::make($request->all(), [
            'client_email' => 'required|email',
            'title' => 'required',
            'message' => 'required'
        ]);
        
        if($validator->fails()) {
            return response()->json([
                'code' => 400,
                'status' => false,
                'message' => $validator->errors()
            ], 400);
        } else {
            $to_email = $request['client_email'];
            $mailData = [
                'subject' => 'Video Waves Client Event Information',
                'title' => $request['title'],
                'message' => $request['message']
            ];

            Mail::to($to_email)->send(new ClientEventEmail($mailData));

            $event->update(['mail_sent' => true]);

            return response()->json([
                'code' => 200,
                'status' => true,
                'message' => 'Email sent successfully'
            ]);

        }

    }

}
