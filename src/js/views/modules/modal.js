class Modal {
    constructor({ el }) {
        this.$el = el;
        this.$modal         =   this.$el;
        this.$openButton    =   this.$el.querySelector('[js-modal-btn-open]');
        this.$closeButton   =   this.$el.querySelector('[js-modal-btn-close]');
        this.$dialog        =   this.$el.querySelector('[js-modal-dialog]');
        this.$backdrop      =   this.$el.querySelector('[js-modal-backdrop]');

        this.$body          =     document.querySelector('body');
        this.$header        =     document.querySelector('[js-header]');

        this.init();
    }
        
    init() {
      this._openModal = this.openModal.bind(this);
      this.$openButton.addEventListener('click', this._openModal);

      this._closeModal = this.closeModal.bind(this);
      this.$closeButton.addEventListener('click', this._closeModal);
      this.$backdrop.addEventListener('click', this._closeModal);
    }

    openModal() {
      this.toggleClassAfterStyleChange();
      this.$header.classList.remove('header-is-sticky');
      this.$header.classList.add('header-is-hidden');
      this.$body.style.overflow = "hidden";
      this.focusTrap();
    }

    // Async function before we need to change the default modal style before triggering the animation
    toggleClassAfterStyleChange = async () => {
      await this.setModalDefaultStyle();
      this.$modal.classList.toggle('is-open'); 
    }

    setModalDefaultStyle() {
      return new Promise((resolve, reject) => {
        const btnRect = this.$openButton.getBoundingClientRect();
        const buttonCenterX = btnRect.left + btnRect.width / 2;
        const buttonCenterY = btnRect.top + btnRect.height / 2;
        this.$dialog.style.top = buttonCenterY + "px";
        this.$dialog.style.left = buttonCenterX + "px";
        this.ticker = buttonCenterX + " / " + buttonCenterY;
        resolve();
      })
    }

        
    closeModal() {
      this.$body.style.overflow = "auto";
      this.$modal.classList.toggle('is-open');

      // Remove style to avoid glitch with animation
      setTimeout(() => {
        this.$dialog.style.top = "";
        this.$dialog.style.left = "";
      }, 300);
    }

    focusTrap() {
        const focusableElements = 'button, [href], input, select, textarea, [tabindex]:not([tabindex="-1"])';
        const firstFocusableElement = this.$dialog.querySelectorAll(focusableElements)[0]; // get first element to be focused inside modal
        const focusableContent = this.$dialog.querySelectorAll(focusableElements);
        const lastFocusableElement = focusableContent[focusableContent.length - 1]; // get last element to be focused inside modal

        document.addEventListener('keydown', function(e) {
            let isTabPressed = e.key === 'Tab' || e.keyCode === 9;
          
            if (!isTabPressed) {
              return;
            }
          
            if (e.shiftKey) { // if shift key pressed for shift + tab combination
              if (document.activeElement === firstFocusableElement) {
                lastFocusableElement.focus(); // add focus for the last focusable element
                e.preventDefault();
              }
            } else { // if tab key is pressed
              if (document.activeElement === lastFocusableElement) { // if focused has reached to last focusable element then focus first focusable element after pressing tab
                firstFocusableElement.focus(); // add focus for the first focusable element
                e.preventDefault();
              }
            }
        });

        firstFocusableElement.focus();
    }
}

export default Modal;