import { isLessThan } from '../../../utils/mq';

class Footer {
  constructor({ el }) {
    this.$el = el;

    this.$blocks                =this.$el.querySelectorAll('[js-bloc]')
    this.$blockBtns             = this.$el.querySelectorAll('[js-bloc-button]');

    this.bindEvents();
  }

  bindEvents() {
    this._toggleBlock = this.toggleBlock.bind(this);

    if(isLessThan('l')) {
      this.$blocks.forEach(block => {
        this.setBlockHeight(block)
      });
      
      this.$blockBtns.forEach(blockBtn => {
        blockBtn.addEventListener('click', this._toggleBlock);
      });
    }
  }

  setBlockHeight(block) {
    const blockHeight = block.clientHeight;
    block.style.height = blockHeight + 'px';
    block.classList.add('is-closed');
  }

  toggleBlock(blockBtn) {
    const block = blockBtn.currentTarget.parentNode;

    if (block.classList.contains('is-closed')) {
      block.classList.remove('is-closed');
    } else {
      block.classList.add('is-closed');
    }
  }
}

export default Footer;
