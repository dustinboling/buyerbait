<?php

namespace App\Http\Controllers;

use App\Extension;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ExtensionController extends Controller
{
    /**
     * Get a validator for an incoming extension request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'user_id' => ['required', 'numeric', 'max:255'],
            'number' => ['required', 'numeric', 'min:100', 'max:999', 'unique:extensions'],
            'name' => ['required', 'string', 'min:3', 'max:255'],
            'message' => ['required', 'string', 'min:3', 'max:255'],
            'transfer_prompt' => ['required', 'string', 'min:5'],
            'voicemail_prompt' => ['required', 'string', 'min:5'],
        ]);
    }

    /**
     * Display a listing of the extensions.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return Extension::all();
    }

    /**
     * Show the form for creating a new extension.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return Extension::create([
            'user_id' => $data['agent_id'],
            'number' => $data['number'],
            'message' => $data['message'],
            'transfer_prompt' => $data['transfer_prompt'],
            'voicemail_prompt' => $data['voicemail_prompt'],
        ]);
    }

    /**
     * Store a newly created extension in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified extension.
     *
     * @param  \App\Extension  $extension
     * @return \Illuminate\Http\Response
     */
    public function show(Extension $extension)
    {
        //
    }

    /**
     * Show the form for editing the specified extension.
     *
     * @param  \App\Extension  $extension
     * @return \Illuminate\Http\Response
     */
    public function edit(Extension $extension)
    {
        //
    }

    /**
     * Update the specified extension in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Extension  $extension
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Extension $extension)
    {
        //
    }

    /**
     * Remove the specified extension from storage.
     *
     * @param  \App\Extension  $extension
     * @return \Illuminate\Http\Response
     */
    public function destroy(Extension $extension)
    {
        //
    }
}
