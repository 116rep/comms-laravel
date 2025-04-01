<!DOCTYPE html>
<html>
<head>
    <title>My Rosé Pine Site</title>
    @vite('resources/css/app.css')
    @vite('resources/js/app.js')
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<body>
    <div class="container">
        <h1>Welcome to Comms</h1>
        <p>I hope you enjoy it</p>

        <br>
        <div class="voice-chat">
            <button id="joinVoiceChat" class="voice-button">Join Voice Chat</button>
            <div id="activeUsers" class="active-users"></div>
            <h2>Voice Chat Activity</h2>
            @foreach (\App\Models\VoiceChatEvent::with('user')->latest()->take(5)->get() as $event)
                <p>{{ $event->user->name }} {{ $event->action }} voice chat at {{ $event->created_at->diffForHumans() }}</p>
            @endforeach
        </div>

        <div class="messages">
            <h2>Messages</h2>
            @foreach (\App\Models\Message::with('user')->latest()->get() as $message)
                <p><strong>{{  $message->user->name }}</strong>: {{ $message->content }}</p>
            @endforeach
        </div>
        <!-- Message Form -->
        <form action="/messages" method="POST">
            @csrf
            <textarea name="message" placeholder="Type your message here..." rows="4" cols="50"></textarea>
            <br>
            <button type="submit">Send Message</button>
        </form>
        <!-- Logout -->
        <br>
        <form action="/logout" method="POST">
            @csrf
            <button type="submit">Logout</button>
        </form>
    </div>
</body>
</html>
