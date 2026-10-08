class Evenements {
    constructor({ el }) {
      this.$el = el;

      this.init();
    }

    init() {
        this.helloWorld();
    }

    helloWorld() {
        console.log('LE TEMPLATE EVENEMENTS EST ACTIF');
    }
}

export default Evenements;
