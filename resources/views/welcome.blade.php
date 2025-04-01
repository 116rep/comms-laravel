<!DOCTYPE html>
<html>
<head>
    <title>My Rosé Pine Site</title>
    @vite('resources/css/app.css')
</head>
<body>
    <div class="container">
        <h1>Welcome to My Site</h1>
        <p>This is styled with Rosé Pine colors.</p>

        <!-- Message Form -->
        <form action="/messages" method="POST">
            @csrf
            <textarea name="message" placeholder="Type your message here..." rows="4" cols="50"></textarea>
            <br>
            <button type="submit">Send Message</button>
        </form>
        <h2>Messages</h2>
        @foreach (\App\Models\Message::all() as $msg)
            <p>{{ $msg->content }}</p>
        @endforeach
        <!-- Logout -->
        <form action="/logout" method="POST">
            @csrf
            <button type="submit">Logout</button>
        </form>
    </div>
</body>
</html>
