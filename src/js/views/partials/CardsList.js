class CardsList {
    constructor({ el }) {
        this.$el = el;
        this.filters    =   this.$el.querySelectorAll('[data-filter]');
        this.items    =   this.$el.querySelectorAll('[js-item]');

        this.init();
    }
        
    init() {
        this.filters.forEach(filter => {
            filter.addEventListener('click', this.onFilterClick.bind(this));
        });
    }

    onFilterClick(e) {
        const filterValue = e.currentTarget.dataset.filter;

        if (filterValue == "all") {
            this.items.forEach(item => {
                item.dataset.visible = 'true' ;
            });
        } else {
            this.items.forEach(item => {
                item.dataset.type == filterValue ? item.dataset.visible = 'true' : item.dataset.visible = 'false' ;
            });
        }

        this.filters.forEach(filter => {
            filter.dataset.selected = 'false';
        });

        e.currentTarget.dataset.selected = 'true';

        this.items.forEach(item => {
            if (item.dataset.visible == 'false') {
                item.style.display = 'none';
            } else {
                item.style.display = 'block';
            }
        });
    }
}

export default CardsList;