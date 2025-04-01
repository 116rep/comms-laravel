<!DOCTYPE html>
<html>
<head>
    <title>My Rosé Pine Site</title>
    @vite('resources/css/app.css')
    @vite('resources/js/app.js')
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<body>
    <div class="wrapper">
            <!-- Sidebar -->
            <div id="sidebar" class="sidebar">
                <h2>Menu</h2>
                <a href="/settings" class="sidebar-item">Settings</a>
                <!-- Logout -->
                <form action="/logout" method="POST">
                    @csrf
                    <button type="submit">Logout</button>
                </form>
            </div>

            <!-- Main Content -->
            <div class="main-content">
                <button id="toggleSidebar" class="toggle-button">☰</button>
                <div class="container">
                    <h1>Welcome to Comms</h1>
                    <p>Be nice and have fun</p>

                    <!-- Voice Chat Section -->
                    <div class="voice-chat">
                        <div id="activeUsers" class="active-users"></div>
                        <br>
                        <button id="joinVoiceChat" class="voice-button">Join Voice Chat</button>
                        <h2>Voice Chat Activity</h2>
                        @foreach (\App\Models\VoiceChatEvent::with('user')->latest()->take(5)->get() as $event)
                            <p>{{ $event->user->name }} {{ $event->action }} voice chat at {{ $event->created_at->diffForHumans() }}</p>
                        @endforeach
                    </div>
                    <!-- Display Messages -->
                    <div class="messages">
                        <h2>Messages</h2>
                        @foreach (\App\Models\Message::with('user')->oldest()->get() as $message)
                            <p><strong>{{ $message->user ? $message->user->name : 'Anonymous' }}</strong>: {{ $message->content }}</p>
                        @endforeach
                    </div>
                    <br>
                    <!-- Message Form -->
                    <form id="messageForm" action="/messages" method="POST">
                        @csrf
                        <textarea id="messageTextarea" name="message" placeholder="Type your message here..." rows="4" cols="50"></textarea>
                        <br>
                        <button type="submit">Send Message</button>
                    </form>



                </div>
            </div>
        </div>
</body>
</html>
