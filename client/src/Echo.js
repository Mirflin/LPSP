import Echo from 'laravel-echo';
import Pusher from 'pusher-js';
import axios from 'axios'
import { useAuthStore } from './storage/auth';

axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
axios.defaults.withCredentials = true;
axios.defaults.withXSRFToken = true;
axios.defaults.baseURL = "http://192.168.88.39:8000";


window.Pusher = Pusher;
export function createEcho() {
  const auth = useAuthStore();

  return new Echo({
    broadcaster: "pusher",
    key: "fd648fb545372a9866d2",
    cluster: "eu",
    forceTLS: true,
    withCredentials: true,
    authorizer: (channel, options) => {
      return {
        authorize: async (socketId, callback) => {
          await axios.get('http://192.168.88.39:8000/sanctum/csrf-cookie');
          await auth.attempt();

          if (auth.authenticated) {
            axios.post("http://192.168.88.39:8000/api/broadcasting/auth", {
              socket_id: socketId,
              channel_name: channel.name,
            }).then(response => {
              callback(false, response.data);
            }).catch(error => {
              callback(true, error);
            });
          } else {
            console.log('no');
          }
        }
      };
    },
  });
}