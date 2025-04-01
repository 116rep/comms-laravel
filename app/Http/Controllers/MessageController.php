<?php
namespace App\Http\Controllers;

use App\Models\Message;
use Illuminate\Http\Request;

class MessageController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            "message" => "required|string|max:255",
        ]);

        Message::create([
            "content" => $request->message,
        ]);

        return redirect("/")->with("success", "Message sent!");
    }
}
