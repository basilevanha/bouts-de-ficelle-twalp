class Image {
    constructor({ el }) {
      this.$el = el;

      this.$imgTag = this.$el.querySelector('[js-img-lazy-tag]');

      this.init();
    }
    
    init() {
      // Check if image is already in cache
      if(this.$imgTag.complete){
        this.$el.classList.remove('js-loading')
      } else {
        this.$imgTag.onload = () => {
          this.$el.classList.remove('js-loading')
        }
      }
    }
}

export default Image;
