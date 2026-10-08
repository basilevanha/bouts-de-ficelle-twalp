// Styles
// Tailwind (utilities only during the migration, see main.css) + legacy SCSS.
import '../css/main.css';
import '../scss/app.scss';

// Legacy components (header, footer, modal, cards filter, lazy images).
// To be progressively replaced by Alpine.js components.
import Manager from './views/manager';

// Alpine.js — reactive HTML attributes for UI interactions
import Alpine from 'alpinejs';
window.Alpine = Alpine;
Alpine.start();

new Manager();
