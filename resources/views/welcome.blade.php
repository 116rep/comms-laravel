<!DOCTYPE html>
<html>
<head>
    <title>My Rosé Pine Site</title>
    @vite('resources/css/app.css')
</head>
<body>
    <div class="container">
        <h1>Welcome to Comms</h1>
        <p>I hope you enjoy it</p>

        <br>

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
