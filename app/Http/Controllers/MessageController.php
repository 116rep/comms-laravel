<?php
namespace App\Http\Controllers;

use App\Models\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MessageController extends Controller
{
    public function store(Request $request)
    {
        if (!Auth::check()) {
            return redirect("/login")->with(
                "error",
                "You must be logged in to post a message."
            );
        }

        $request->validate([
            "message" => "required|string|max:255",
        ]);

        Message::create([
            "content" => $request->message,
            "user_id" => Auth::id(),
        ]);

        return redirect("/")->with("success", "Message sent!");
    }
}
