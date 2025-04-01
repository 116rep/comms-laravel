import io from "socket.io-client";

// Sidebar Toggle
const toggleButton = document.getElementById("toggleSidebar");
const sidebar = document.getElementById("sidebar");
toggleButton.addEventListener("click", () => {
    sidebar.classList.toggle("active");
});

// Message Form Submission with Enter
const messageForm = document.getElementById("messageForm");
const messageTextarea = document.getElementById("messageTextarea");
if (messageForm && messageTextarea) {
    messageTextarea.addEventListener("keydown", (event) => {
        if (event.key === "Enter" && !event.shiftKey) {
            event.preventDefault(); // Prevent newline
            messageForm.submit(); // Submit the form
        }
    });
}

// Microphone Selection
const micSelect = document.getElementById("micSelect");
let localStream;

async function populateMicDropdown() {
    const status = document.getElementById("micStatus");
    try {
        status.textContent = "Requesting microphone access...";
        const stream = await navigator.mediaDevices.getUserMedia({
            audio: true,
        });
        localStream = stream;

        status.textContent = "Loading devices...";
        const devices = await navigator.mediaDevices.enumerateDevices();
        const audioDevices = devices.filter(
            (device) => device.kind === "audioinput",
        );

        micSelect.innerHTML = '<option value="">Select a microphone</option>';
        if (audioDevices.length === 0) {
            micSelect.innerHTML =
                '<option value="">No microphones found</option>';
            status.textContent = "No microphones detected.";
        } else {
            audioDevices.forEach((device) => {
                const option = document.createElement("option");
                option.value = device.deviceId;
                option.text =
                    device.label ||
                    `Microphone ${micSelect.options.length + 1}`;
                micSelect.appendChild(option);
            });
            status.textContent = "Microphones loaded successfully.";
        }

        stream.getTracks().forEach((track) => track.stop());
    } catch (err) {
        console.error("Error accessing microphones:", err.name, err.message);
        micSelect.innerHTML =
            '<option value="">Error: ' + err.message + "</option>";
        status.textContent = "Error: " + err.message;
    }
}

const loadMicsButton = document.getElementById("loadMics");
if (loadMicsButton) {
    loadMicsButton.addEventListener("click", populateMicDropdown);
}

// Voice Chat Logic
const socket = io("http://localhost:3000");
const voiceButton = document.getElementById("joinVoiceChat");
const activeUsersDiv = document.getElementById("activeUsers");
const peerConnections = {};
const userId = window.authUser.id;
const userName = window.authUser.name;
let isInVoiceChat = false;

voiceButton.addEventListener("click", async () => {
    if (!isInVoiceChat) {
        // Join Voice Chat
        try {
            const selectedMic = micSelect ? micSelect.value : null;
            localStream = await navigator.mediaDevices.getUserMedia({
                audio: selectedMic
                    ? { deviceId: { exact: selectedMic } }
                    : true,
            });
            addUserCircle(socket.id, userName);

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
            voiceButton.textContent = "Leave Voice Chat";
            isInVoiceChat = true;
        } catch (err) {
            console.error("Error joining voice chat:", err);
            alert("Couldn’t access your microphone.");
        }
    } else {
        // Leave Voice Chat
        socket.emit("leave-voice-chat", { id: userId, name: userName });
        if (localStream) {
            localStream.getTracks().forEach((track) => track.stop());
            localStream = null;
        }
        Object.values(peerConnections).forEach((pc) => pc.close());
        Object.keys(peerConnections).forEach(
            (id) => delete peerConnections[id],
        );
        activeUsersDiv.innerHTML = ""; // Clear user circles
        voiceButton.textContent = "Join Voice Chat";
        isInVoiceChat = false;
    }
});

socket.on("current-users", (users) => {
    users.forEach((user) => {
        if (user.id !== userId) {
            addUserCircle(user.socketId, user.name);
            initiatePeerConnection(user.socketId);
        }
    });
});

socket.on("user-joined", (data) => {
    if (data.id !== userId) {
        addUserCircle(data.socketId, data.name);
        initiatePeerConnection(data.socketId);
    }
});

socket.on("user-left", (data) => {
    removeUserCircle(data.socketId);
    if (peerConnections[data.socketId]) {
        peerConnections[data.socketId].close();
        delete peerConnections[data.socketId];
    }
});

function addUserCircle(socketId, name) {
    if (!document.getElementById(`user-${socketId}`)) {
        const circle = document.createElement("div");
        circle.id = `user-${socketId}`;
        circle.className = "user-circle";
        circle.textContent = name.charAt(0).toUpperCase();
        activeUsersDiv.appendChild(circle);
    }
}

function removeUserCircle(socketId) {
    const circle = document.getElementById(`user-${socketId}`);
    if (circle) circle.remove();
}

async function initiatePeerConnection(targetSocketId) {
    const pc = new RTCPeerConnection({
        iceServers: [{ urls: "stun:stun.l.google.com:19302" }],
    });
    peerConnections[targetSocketId] = pc;

    localStream.getTracks().forEach((track) => pc.addTrack(track, localStream));

    pc.ontrack = (event) => {
        const audio = new Audio();
        audio.srcObject = event.streams[0];
        audio.autoplay = true;
    };

    pc.onicecandidate = (event) => {
        if (event.candidate) {
            socket.emit("ice-candidate", {
                to: targetSocketId,
                candidate: event.candidate,
            });
        }
    };

    const offer = await pc.createOffer();
    await pc.setLocalDescription(offer);
    socket.emit("offer", { to: targetSocketId, offer });

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

    socket.on("answer", (data) => {
        if (data.from === targetSocketId) {
            pc.setRemoteDescription(new RTCSessionDescription(data.answer));
        }
    });

    socket.on("ice-candidate", (data) => {
        if (data.to === socket.id) {
            pc.addIceCandidate(new RTCIceCandidate(data.candidate));
        }
    });
}

window.addEventListener("beforeunload", () => {
    if (isInVoiceChat) {
        socket.emit("leave-voice-chat", { id: userId, name: userName });
        if (localStream)
            localStream.getTracks().forEach((track) => track.stop());
    }
});
