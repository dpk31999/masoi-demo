const socket = new WebSocket('ws://localhost:8000');

socket.onopen(() => {
    socket.send('Hello!');
});

socket.onmessage(data => {
    console.log(data);
});