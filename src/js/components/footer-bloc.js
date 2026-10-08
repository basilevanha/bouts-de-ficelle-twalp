import { isLessThan } from '../utils/mq';

/**
 * Footer accordion bloc (partials/footer.twig)
 *
 * Mobile/tablet only: the bloc's open height is fixed in px so the
 * CSS height transition works, then it starts closed (`is-closed`).
 */
export default () => ({
  enabled: false,
  closed: false,

  init() {
    if (!isLessThan('l')) {
      return;
    }

    this.enabled = true;
    this.$el.style.height = `${this.$el.clientHeight}px`;
    this.closed = true;
  },

  toggle() {
    if (this.enabled) {
      this.closed = !this.closed;
    }
  },
});
