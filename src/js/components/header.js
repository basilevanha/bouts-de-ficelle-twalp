import { isLessThan } from '../utils/mq';

/**
 * Site header (partials/header.twig)
 *
 * - Hides on scroll down, comes back (with a shadow) on scroll up.
 * - Burger menu on mobile/tablet; menu links close it.
 * - Listens to `header-hide` (dispatched by the modal).
 */
export default () => ({
  menuOpen: false,
  hidden: false,
  sticky: false,
  lastScroll: 0,

  onScroll() {
    const currentScroll = window.scrollY;

    // Near the top: always visible, without shadow.
    if (currentScroll <= (isLessThan('l') ? 70 : 85)) {
      this.show(true);
      return;
    }

    if (currentScroll > this.lastScroll && !this.hidden) {
      this.hide();
    } else if (currentScroll < this.lastScroll && this.hidden) {
      this.show();
    }

    this.lastScroll = currentScroll;
  },

  show(isTop = false) {
    this.hidden = false;
    this.sticky = !isTop;
  },

  hide() {
    this.sticky = false;
    this.hidden = true;
  },

  toggleMenu() {
    this.menuOpen = !this.menuOpen;
    document.body.classList.toggle('menu-is-open', this.menuOpen);
  },

  // Menu links close the burger menu (mobile/tablet only).
  onMenuLinkClick() {
    if (isLessThan('l')) {
      this.toggleMenu();
    }
  },
});
