/**
 * Modal (modules/modal.twig)
 *
 * The dialog grows from the button that opened it: its top/left are set
 * to the button's center, then `is-open` animates it to the screen center.
 * Focus is trapped inside while open (x-trap, @alpinejs/focus).
 */
export default () => ({
  open: false,

  openModal() {
    const button = this.$refs.button.getBoundingClientRect();
    this.$refs.dialog.style.top = `${button.top + button.height / 2}px`;
    this.$refs.dialog.style.left = `${button.left + button.width / 2}px`;

    window.dispatchEvent(new CustomEvent('header-hide'));
    document.body.style.overflow = 'hidden';
    this.open = true;
  },

  closeModal() {
    if (!this.open) {
      return;
    }

    document.body.style.overflow = 'auto';
    this.open = false;

    // Reset the start position once the closing animation is done.
    setTimeout(() => {
      this.$refs.dialog.style.top = '';
      this.$refs.dialog.style.left = '';
    }, 300);
  },
});
