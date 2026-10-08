import { isLessThan } from '../../../utils/mq';

class Header {
  constructor({ el }) {
    this.$el = el;

    this.$burgerButton    = this.$el.querySelector('[js-burger-button]');
    this.$menu            = this.$el.querySelector('[js-menu]');
    this.$menuLinks       = this.$el.querySelectorAll('[js-menu-link]');
    this.$focusLinks       = this.$el.querySelectorAll('[js-focus-link]');

    this.$body            = document.querySelector('body');
    this.$header          = document.querySelector('[js-header]');

    this.isHidden         = 'header-is-hidden';
    this.isVisible        = 'header-is-sticky';

    this._showHeader      = this.showHeader.bind(this);
    this._hideHeader      = this.hideHeader.bind(this);

    let lastScroll = 0;


    window.addEventListener('scroll', () => {
      const currentScroll = window.scrollY;

      if (currentScroll <= (isLessThan('l') ? 70 : 85)) {
        const isTop = true;
        this._showHeader(isTop);
        return;
      }

      if (currentScroll > lastScroll && !this.$header.classList.contains(this.isHidden)) {
        // DOWN
        this._hideHeader();
      } else if (currentScroll < lastScroll && this.$header.classList.contains(this.isHidden)) {
        // UP
        this._showHeader();
      }

      lastScroll = currentScroll;
    });

    this.bindEvents();
  }

  bindEvents() {
    this._toggleMenu = this.toggleMenu.bind(this);
    this.$burgerButton.addEventListener('click', this._toggleMenu);

    if(isLessThan('l')) {
      this.$menuLinks.forEach(link => {
        link.addEventListener('click', this._toggleMenu);
      });
    }

    this.$focusLinks.forEach(link => {
      link.addEventListener('focus', this._showHeader)
    });

  }

  showHeader(isTop) {
    this.$header.classList.remove(this.isHidden);
    isTop ? this.$header.classList.remove(this.isVisible) : this.$header.classList.add(this.isVisible);
  }

  hideHeader() {
    this.$header.classList.remove(this.isVisible);
    this.$header.classList.add(this.isHidden);
  }

  toggleMenu() {
    if (this.$body.classList.contains('menu-is-open')) {
      this.$body.classList.remove('menu-is-open');
      this.$burgerButton.classList.remove('is-open');
      this.$header.classList.remove('menu-is-open');
    } else {
      this.$body.classList.add('menu-is-open');
      this.$burgerButton.classList.add('is-open');
      this.$header.classList.add('menu-is-open');
    }
  }
}

export default Header;
