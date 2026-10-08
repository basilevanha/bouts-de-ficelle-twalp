// Styles (Tailwind)
import '../css/main.css';

// Alpine.js — reactive HTML attributes for UI interactions
import Alpine from 'alpinejs';
import focus from '@alpinejs/focus';

import header from './components/header';
import footerBloc from './components/footer-bloc';
import modal from './components/modal';
import lazyImage from './components/lazy-image';

Alpine.plugin(focus);

Alpine.data('header', header);
Alpine.data('footerBloc', footerBloc);
Alpine.data('modal', modal);
Alpine.data('lazyImage', lazyImage);

window.Alpine = Alpine;
Alpine.start();
