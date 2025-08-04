import Echo from 'laravel-echo';
import Pusher from 'pusher-js';
import axios from 'axios'

axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
axios.defaults.withCredentials = true;
axios.defaults.withXSRFToken = true;
axios.defaults.baseURL = "http://192.168.88.42:8000";

window.Pusher = Pusher;
window.Echo = new Echo({
  broadcaster: "pusher",
  key: "611326fe66f2a0b70a5b",
  cluster: "eu",
  forceTLS: true,
  withCredentials: true,
  authorizer: (channel, options) => {
    return {
      authorize: (socketId, callback) => {
        axios.get("http://192.168.88.42:8000/sanctum/csrf-cookie", { withCredentials: true })
          .then(() => {
            axios.post("http://192.168.88.42:8000/api/broadcasting/auth", {
              socket_id: socketId,
              channel_name: channel.name,
            }, { withCredentials: true })
            .then(response => {
              callback(false, response.data);
            })
            .catch(error => {
              callback(true, error);
            });
          })
          .catch(error => {
            callback(true, error);
          });
      }
    };
  },
});

export default Echo