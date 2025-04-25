const pusher = new Pusher("app-key", {
  wsHost: "quazymodo",
  wsPort: 6001,  
  wssPort: 6001,
  forceTLS: true, 
  disableStats: true,
  enabledTransports: ["ws","wss"],
});

const channel = pusher.subscribe("public-chat");
channel.bind("chat-message", (data) => {
  const li = document.createElement("li");
  li.textContent = data.message;
  document.getElementById("chat").appendChild(li);
});

function send() {
  const msg = document.getElementById("msg").value;
  fetch("/chat/broadcast", {
    method: "POST",
    headers: { "Content-Type": "application/json" },
    body: JSON.stringify({ message: msg }),
  });
}

$("button#sendMessage").click(send);
