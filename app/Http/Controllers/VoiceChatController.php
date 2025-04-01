<?php

namespace App\Http\Controllers;

use App\Events\VoiceChatEvent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class VoiceChatController extends Controller
{
    public function join(Request $request)
    {
        event(new VoiceChatEvent(Auth::id(), 'joined'));
        return response()->json(['status' => 'success']);
    }
}}
