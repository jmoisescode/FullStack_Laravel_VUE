import Echo from "laravel-echo";
import Pusher from "pusher-js";

window.Pusher = Pusher;

export const echo = new Echo({
  broadcaster: "pusher",
  key: import.meta.env.VITE_PUSHER_APP_KEY,
  cluster: import.meta.env.VITE_PUSHER_APP_CLUSTER,
  forceTLS: true,
  wsHost: "ws-ap1.pusher.com" ,
  wsPort: 443,
  wssPort: 443,
  enabledTransports: ["ws", "wss"],  
});