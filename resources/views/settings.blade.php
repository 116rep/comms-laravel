<!DOCTYPE html>
<html>
<head>
    <title>Settings | Rosé Pine</title>
    @vite('resources/css/app.css')
    @vite('resources/js/app.js')
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<body>
    <div class="wrapper">
        <!-- Sidebar -->
        <div id="sidebar" class="sidebar">
            <h2>Menu</h2>
            <a href="/" class="sidebar-item">Home</a>
            <a href="/settings" class="sidebar-item">Settings</a>
        </div>

        <!-- Main Content -->
        <div class="main-content">
            <button id="toggleSidebar" class="toggle-button">☰</button>
            <div class="container">
                <h1>Settings</h1>
                <h2>Microphone</h2>
                <select id="micSelect" class="mic-dropdown">
                    <option value="">Select a microphone</option>
                    <!-- Options populated by JS -->
                </select>
                <br><br>
                <a href="/" class="back-link">Back to Home</a>
            </div>
        </div>
    </div>
</body>
</html>
