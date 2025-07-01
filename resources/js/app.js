import './bootstrap';
import axios from 'axios';

import Alpine from 'alpinejs';

window.Alpine = Alpine;

window.axios = axios;
//axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
//axios.defaults.headers.common['X-CSRF-TOKEN'] = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

Alpine.start();
