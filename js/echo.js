import Echo from 'laravel-echo'

raven.context(function() {
  if (app.broadcasting.driver == 'pusher') {
    window.Pusher = require('pusher-js');
    window.Echo = new Echo({
      broadcaster: 'pusher',
      key: app.broadcasting.key,
      cluster: app.broadcasting.cluster,
      encrypted: true,
    });
  } else if (app.broadcasting.driver == 'redis') {
    window.io = require('socket.io-client');
    window.Echo = new Echo({
      broadcaster: 'socket.io',
      host: location.host,
    });
  }
});
