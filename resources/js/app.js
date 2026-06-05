import './bootstrap';

import Alpine from 'alpinejs';

window.Alpine = Alpine;

// Notification panel component
document.addEventListener('alpine:init', () => {
    Alpine.data('notifPanel', () => ({
        open:          false,
        loading:       false,
        total:         0,
        notifications: {},

        init() {
            this.fetchNotifications();
            // Refresh every 60 seconds
            setInterval(() => this.fetchNotifications(), 60000);
        },

        async fetchNotifications() {
            try {
                const res  = await fetch('/notifications', {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json',
                    }
                });
                const data = await res.json();
                this.total         = data.total;
                this.notifications = data;
            } catch (e) {
                console.error('Notification fetch failed', e);
            }
        },

        async toggle() {
            this.open = !this.open;
            if (this.open) {
                this.loading = true;
                await this.fetchNotifications();
                this.loading = false;
            }
        }
    }));
});

Alpine.start();

