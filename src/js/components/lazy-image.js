/**
 * Responsive image with blurred placeholder (atoms/image.twig)
 *
 * Removes `js-loading` (which shows the placeholder) once the image is loaded.
 */
export default () => ({
  init() {
    const img = this.$refs.img;
    const loaded = () => this.$el.classList.remove('js-loading');

    if (img.complete) {
      loaded();
    } else {
      img.addEventListener('load', loaded, { once: true });
    }
  },
});
