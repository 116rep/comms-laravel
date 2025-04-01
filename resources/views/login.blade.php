<!DOCTYPE html>
<html>
<head>
    <title>Login | Rosé Pine</title>
    @vite('resources/css/app.css')
</head>
<body>
    <div class="container">
        <h1>Login</h1>
        @if (session('error'))
            <p class="error">{{ session('error') }}</p>
        @endif
        <form action="/login" method="POST">
            @csrf
            <div>
                <label for="email">Email:</label>
                <input type="email" name="email" id="email" required>
            </div>
            <div>
                <label for="password">Password:</label>
                <input type="password" name="password" id="password" required>
            </div>
            <button type="submit">Log In</button>
        </form>
        <p><a href="/">Back to Home</a></p>
    </div>
</body>
</html>
