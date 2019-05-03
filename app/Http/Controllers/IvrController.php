<?php

// return response($response)->header('Content-Type', 'application/xml');

namespace App\Http\Controllers;

use App\Call;
use App\User;
use App\Caller;
use App\Extension;
use Twilio\Rest\Client;
use Illuminate\Http\Request;
use Twilio\TwiML\VoiceResponse;

class IvrController extends Controller
{
    /**
     * This is the entry point when a call comes in
     *
     * @param Illuminate\Http\Request $request
     * @return Twilio\TwiML\VoiceResponse
     */
    public function greeting(Request $request)
    {
        $response = new VoiceResponse();

        if (! $request->filled('From'))
        {
            $response->say('No caller information found.');
            return response($response)->header('Content-Type', 'application/xml');
        }

        // Get or create the caller
        $caller = $this->firstOrCreateCaller($request);

        // Create a new call and associate it with the caller
        $call = $caller->calls()->create(['sid' => $request->CallSid]);

        $gather = $response->gather(
            [
                'numDigits' => 3,
                'action' => route('extension-message', [], false)
            ]
        );

        $gather->say(
            'Hello! Thank you for calling Willamette Valley for sale by owners dot com',
            ['voice' => 'Polly.Matthew', 'language' => 'en-US']
        );
        $gather->pause();
        $gather->say(
            'Please enter the 3 digit extension for the property you would like to tour.',
            ['voice' => 'Polly.Salli', 'language' => 'en-GB']
        );
        $gather->pause();

        return response($response)->header('Content-Type', 'application/xml');
    }

    /**
     * Retrieves the extensions message and plays it to the caller. Then the coller is
     * asked if they would like more information.
     *
     * @param Illuminate\Http\Request $request
     * @return Twilio\TwiML\VoiceResponse
     */
    public function extensionMessage(Request $request)
    {
        $response = new VoiceResponse();

        // Lookup the extension using the number entered by the caller
        $extension = Extension::where('number', $request->Digits)->first();

        // Play the message and then the transfer prompt
        if ($extension !== null)
        {
            // Get the current call
            $call = Call::where('sid', $request->CallSid)->first();

            // Associate the call with the extension called
            if ($call !== null)
            {
                $call->extension()->associate($extension);
                $call->save();
            }

            // Get the agent (User) that owns the extension
            $agent = $extension->agents()->first();

            $caller = $call->caller;

            // If this is the first time the caller has called in
            // with an extension number, assign the caller to the
            // agent of the extension.

            if($caller->agent === null)
            {
                // dump($caller);
                $caller->agent()->associate($agent);
                //$caller->user_id = $agent->id;
                $caller->save();
            }
            // dump($caller->agent);

            // Call the agent and get the conference room
            // friendly name for use later.
            $conferenceRoom = $this->createConferenceWithAgent($call);

            // Now we are done recording the call data, play the extension message
            // to the caller and
            $response->say(
                'Thank you. Please wait while I gather information for extension number ' . $extension->number,
                ['voice' => 'Polly.Salli', 'language' => 'en-GB']
            );
            $response->pause();
            $response->say(
                'Here\'s the information you requested.',
                ['voice' => 'Polly.Salli', 'language' => 'en-GB']
            );
            $response->pause();

            // Play the message to the caller
            $response->say( $extension->message, ['voice' => 'Polly.Matthew', 'language' => 'en-US']);
            $response->pause();

            $gather = $response->gather([
                'input' => 'dtmf speech',
                'hints' => 'Yes, No',
                'numDigits' => 1,
                //pass along the conference room name that the agent is waiting in.
                'action' => route('connect-agent',['confRoom' => $conferenceRoom], true),
            ]);
            $gather->say( $extension->transfer_prompt, ['voice' => 'Polly.Salli', 'language' => 'en-GB']);

            $response->say(
                'I\'m sorry, I did not get a response. Please call again. Goodbye!',
                ['voice' => 'Polly.Matthew', 'language' => 'en-US']
            );
        }
        else // If extension doesn't exist
        {
            $response->say(
                'Sorry, we could not find an active property that matched the code you entered. '.
                'I\'m returning you to the main menu so you can try again.',
                ['voice' => 'Polly.Matthew', 'language' => 'en-US']
            );
            $response->redirect(route('greeting', [], false));
        }

        return response($response)->header('Content-Type', 'application/xml');
    }

    public function connectAgent(Request $request)
    {
        $response = new VoiceResponse();

        // If the caller said yes, or dialed 1
        if($request->SpeechResult == 'Yes.' || $request->Digits == 1)
        {
            $response->say('Great! Hang on while we prepare this information.', ['voice' => 'Polly.Salli', 'language' => 'en-GB']);
            $dial = $response->dial('');
            $dial->conference($request->confRoom);
        }
        else {
            $response->say('Well, I guess we\'ll see you later then. Goodbye!');
        }

        // dump(response($response)->header('Content-Type', 'application/xml')->content());

        return response($response)->header('Content-Type', 'application/xml');
    }

    /**
     * Calls the agent and returns the conference room name
     *
     * @return string the conference room friendly name
     */
    private function createConferenceWithAgent(Call $call)
    {
        $agent = $call->caller->agent;
        $extNum = $call->extension->number;

        if( $agent->phone !== null && $extNum !== null )
        {
            // Make the friendly name for the confernce room
            $conferenceRoom = $extNum . '-' . $call->sid;

            $twilio = new Client(env('TWILIO_ACCOUNT_SID'), env('TWILIO_AUTH_TOKEN'));

            $call = $twilio->calls
                    ->create($agent->phone, // to
                        env('TWILIO_FROM'), [
                            "url" => route('agent-called',
                                ['confRoom' => $conferenceRoom, 'extNumber' => $extNum],
                                true)
                        ]
                    );
            // CallInstance
            // dump($call);
        }

        return $conferenceRoom;
    }

    public function whisper()
    {
        $response = new VoiceResponse();

        return $response;
    }

    public function sample()
    {
        $response = new VoiceResponse();
        $say = $response->say('Hi', ['voice' => 'Polly.Joanna']);
        $say->break_(['strength' => 'x-weak', 'time' => '100ms']);
        $say->emphasis('Words to emphasize', ['level' => 'moderate']);
        $say->p('Words to speak');
        $say->append('aaaaaa');
        $say->phoneme('Words to speak', ['alphabet' => 'x-sampa', 'ph' => 'pɪˈkɑːn']);
        $say->append('bbbbbbb');
        $say->prosody('Words to speak', ['pitch' => '-10%', 'rate' => '85%',
            'volume' => '-6dB']);
        $say->s('Words to speak');
        $say->say_as('Words to speak', ['interpret-as' => 'spell-out']);
        $say->sub('Words to be substituted', ['alias' => 'alias']);
        $say->w('Words to speak');

        return $response;
    }

    public function testGetExtensionMessage($number)
    {
        return $description = Extension::first()->where(['number',$number])->pluck('description');
    }

    /**
     *  This method creates a new caller Caller or returns an existing
     *  one if the phone number already exists.
     *
     * @param Illuminate\Http\Request $request
     *
     * @return App\Caller
     */
    protected function firstOrCreateCaller(Request $request)
    {
        // Get existing Caller record or create one
        $caller = Caller::firstOrCreate(
            ['phone' => $request->From],
            [
                'name' => $request->CallerName,
                'city' => $request->CallerCity,
                'state' => $request->CallerState,
                'country' => $request->CallerCountry,
            ]
        );

        return $caller;
    }
}

/*
Twilio Request Payload
[
  "Called" => "+15417359007"
  "ToState" => "OR"
  "CallerCountry" => "US"
  "Direction" => "inbound"
  "CallerState" => "OR"
  "ToZip" => null
  "CallSid" => "CAe3a27b3a42b218b1d81f8c73da7fde98"
  "To" => "+15417359007"
  "CallerZip" => "97439"
  "CallerName" => "DUSTIN BOLING"
  "ToCountry" => "US"
  "ApiVersion" => "2010-04-01"
  "CalledZip" => null
  "CalledCity" => null
  "CallStatus" => "ringing"
  "From" => "+15419991884"
  "AccountSid" => "ACf3b4bcdce6d392d9160538057ae34889"
  "CalledCountry" => "US"
  "CallerCity" => "FLORENCE"
  "Caller" => "+15419991884"
  "FromCountry" => "US"
  "ToCity" => null
  "FromCity" => "FLORENCE"
  "CalledState" => "OR"
  "FromZip" => "97439"
  "FromState" => "OR"
]
*/
