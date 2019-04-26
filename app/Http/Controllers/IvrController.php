<?php

namespace App\Http\Controllers;

use Twilio\Twiml;
use Twilio\TwiML\VoiceResponse;
use Illuminate\Http\Request;

class IvrController extends Controller
{
    public function index()
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

    public function showWelcome()
    {
        $response = new Twiml();
        $gather = $response->gather(
            [
                'numDigits' => 3,
                'action' => route('menu-response', [], false)
            ]
        );
        for ($i=3; $i < 3; $i++)
        {
            $gather->say('Thank you for calling Willamette Valley for sale by owners dot com',['voice' => 'Polly.Amy', 'language' => 'en-GB']);
            $gather->say('Please enter the 3 digit code of the property you would like to tour.',['voice' => 'Polly.Brian', 'language' => 'en-GB']);
            $gather->pause();
        }

        return $response;
    }

    public function showMenuResponse(Request $request)
    {
        $selectedOption = $request->input('Digits');

        switch ($selectedOption) {
            case 100:
                return $this->_getReturnInstructions();
            case 200:
                return $this->_getPlanetsMenu();
        }

        $response = new Twiml();
        $response->say(
            'Returning to the main menu',
            ['voice' => 'Alice', 'language' => 'en-GB']
        );
        $response->redirect(route('welcome', [], false));

        return $response;
    }
}
