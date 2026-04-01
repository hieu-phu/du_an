import Echo from 'laravel-echo';
import Pusher from 'pusher-js';

// Ngăn chặn khởi tạo Echo nhiều lần
if (window.Echo) {
    console.log('Echo already initialized, skipping...');
} else {
    // Kiểm tra có config broadcasting không
    const broadcastDriver = import.meta.env.VITE_BROADCAST_DRIVER || 'reverb';

    if (broadcastDriver === 'pusher' && import.meta.env.VITE_PUSHER_APP_KEY) {
        window.Pusher = Pusher;
        try {
            window.Echo = new Echo({
                broadcaster: 'pusher',
                key: import.meta.env.VITE_PUSHER_APP_KEY,
                cluster: import.meta.env.VITE_PUSHER_APP_CLUSTER,
                forceTLS: true,
                encrypted: true,
                authEndpoint: '/broadcasting/auth',
                auth: {
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content'),
                        'Accept': 'application/json',
                    }
                }
            });
            console.log('Echo initialized with Pusher');
        } catch (error) {
            console.error('Failed to initialize Echo with Pusher:', error);
        }
    } else if (broadcastDriver === 'reverb' && import.meta.env.VITE_REVERB_APP_KEY) {
        window.Pusher = Pusher;

        try {
            const reverbScheme = import.meta.env.VITE_REVERB_SCHEME || 'http';
            const useTLS = reverbScheme === 'https';


            window.Echo = new Echo({
                broadcaster: 'reverb',
                key: import.meta.env.VITE_REVERB_APP_KEY,
                wsHost: import.meta.env.VITE_REVERB_HOST || '127.0.0.1',
                wsPort: import.meta.env.VITE_REVERB_PORT || 8080,
                wssPort: import.meta.env.VITE_REVERB_PORT || 8080,
                forceTLS: useTLS,
                enabledTransports: ['ws', 'wss'],
                authEndpoint: '/broadcasting/auth',
                auth: {
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content'),
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                },
                authorizer: (channel, options) => {
                    return {
                        authorize: (socketId, callback) => {
                            fetch('/broadcasting/auth', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content'),
                                    'Accept': 'application/json',
                                    'X-Requested-With': 'XMLHttpRequest'
                                },
                                credentials: 'same-origin',
                                body: JSON.stringify({
                                    socket_id: socketId,
                                    channel_name: channel.name
                                })
                            })
                                .then(response => {
                                    if (!response.ok) {
                                        throw new Error(`HTTP error! status: ${response.status}`);
                                    }
                                    return response.json();
                                })
                                .then(data => {
                                    callback(null, data);
                                })
                                .catch(error => {
                                    console.error('Authorization failed:', error);
                                    callback(error, null);
                                });
                        }
                    };
                }
            });
        } catch (error) {
            console.error('Failed to initialize Echo with Reverb:', error);
        }
    } else {
        console.warn('Broadcasting disabled - notifications will use polling');
    }
}