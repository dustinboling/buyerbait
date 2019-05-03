<?php

namespace App\Http\Controllers;

use App\Extension;
use Illuminate\Http\Request;
use Twilio\TwiML\VoiceResponse;

class AgentIvrController extends Controller
{
    public function agentCalled(Request $request)
    {
        $response = new VoiceResponse();

        if($request->filled('confRoom') && $request->filled('extNumber'))
        {
            $extension = Extension::where('number', $request->extNumber)->first();
            $agent = $extension->agents()->first();

            $response->pause();
            $say = $response->say(
                'Hello, ' . $agent->name . '! You have a caller inquiring about ' . $extension->name .
                '. Please hold while they finish listening to the property description. Start speaking when you hear the beep.',
                ['voice' => 'Polly.Matthew', 'language' => 'en-US']
            );
            $dial = $response->dial('');
            $conferenceRoom = $request->confRoom ? $request->confRoom : 'test-room';
            $dial->conference($conferenceRoom);
        }
        else
        {
            $response->pause();
            $say = $response->say('I\'m sorry, their has been an error. Either the extension '.
                'number or conference room name is missing from the request.');
        }

        return response($response)->header('Content-Type', 'application/xml');
    }
}
