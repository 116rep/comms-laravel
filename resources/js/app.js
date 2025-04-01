import io from "socket.io-client";

const socket = io("http://localhost:3000"); // Connect to Node.js server
const joinButton = document.getElementById("joinVoiceChat");
const activeUsersDiv = document.getElementById("activeUsers");
let localStream;
const peerConnections = {};
const userId = "{{ Auth::id() }}"; // Laravel Blade for user ID
const userName = "{{ Auth::user()->name }}"; // Authenticated user's name

// Join Voice Chat
joinButton.addEventListener("click", async () => {
    try {
        localStream = await navigator.mediaDevices.getUserMedia({
            audio: true,
        });
        addUserCircle(socket.id, userName);

        // Notify Laravel of join
        await fetch("/voice-chat/join", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": document.querySelector(
                    'meta[name="csrf-token"]',
                ).content,
            },
        });

        socket.emit("join-voice-chat", { id: userId, name: userName });
        joinButton.disabled = true;
    } catch (err) {
        console.error("Error accessing microphone:", err);
        alert("Couldn’t access your microphone.");
    }
});

// Handle current users
socket.on("current-users", (users) => {
    users.forEach((user) => {
        if (user.id !== userId) {
            addUserCircle(user.socketId, user.name);
            initiatePeerConnection(user.socketId);
        }
    });
});

// Handle new user joining
socket.on("user-joined", (data) => {
    if (data.id !== userId) {
        addUserCircle(data.socketId, data.name);
        initiatePeerConnection(data.socketId);
    }
});

// Handle user leaving
socket.on("user-left", (data) => {
    removeUserCircle(data.socketId);
    if (peerConnections[data.socketId]) {
        peerConnections[data.socketId].close();
        delete peerConnections[data.socketId];
    }
});

// Add user circle to UI
function addUserCircle(socketId, name) {
    if (!document.getElementById(`user-${socketId}`)) {
        const circle = document.createElement("div");
        circle.id = `user-${socketId}`;
        circle.className = "user-circle";
        circle.textContent = name.charAt(0).toUpperCase();
        activeUsersDiv.appendChild(circle);
    }
}

// Remove user circle from UI
function removeUserCircle(socketId) {
    const circle = document.getElementById(`user-${socketId}`);
    if (circle) circle.remove();
}

// Initiate WebRTC Peer Connection
async function initiatePeerConnection(targetSocketId) {
    const pc = new RTCPeerConnection({
        iceServers: [{ urls: "stun:stun.l.google.com:19302" }],
    });
    peerConnections[targetSocketId] = pc;

    // Add local audio stream
    localStream.getTracks().forEach((track) => pc.addTrack(track, localStream));

    // Handle incoming audio
    pc.ontrack = (event) => {
        const audio = new Audio();
        audio.srcObject = event.streams[0];
        audio.autoplay = true;
    };

    // Exchange ICE candidates
    pc.onicecandidate = (event) => {
        if (event.candidate) {
            socket.emit("ice-candidate", {
                to: targetSocketId,
                candidate: event.candidate,
            });
        }
    };

    // Create and send offer
    const offer = await pc.createOffer();
    await pc.setLocalDescription(offer);
    socket.emit("offer", { to: targetSocketId, offer });

    // Handle incoming offer
    socket.on("offer", async (data) => {
        if (!pc.remoteDescription) {
            await pc.setRemoteDescription(
                new RTCSessionDescription(data.offer),
            );
            const answer = await pc.createAnswer();
            await pc.setLocalDescription(answer);
            socket.emit("answer", { to: data.from, answer });
        }
    });

    // Handle incoming answer
    socket.on("answer", (data) => {
        if (data.from === targetSocketId) {
            pc.setRemoteDescription(new RTCSessionDescription(data.answer));
        }
    });

    // Handle ICE candidates
    socket.on("ice-candidate", (data) => {
        if (data.to === socket.id) {
            pc.addIceCandidate(new RTCIceCandidate(data.candidate));
        }
    });
}

// Clean up on page unload
window.addEventListener("beforeunload", () => {
    if (localStream) {
        localStream.getTracks().forEach((track) => track.stop());
    }
});
