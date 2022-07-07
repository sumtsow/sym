import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
    static targets = ['select', 'items'];

    change() {
      let items = this.itemsTarget.children;
      for(let index in items) {
        if (items[index].dataset) {
          items[index].classList.toggle('d-none', (this.selectTarget.value !== '0' && (this.selectTarget.value !== items[index].dataset.type)));
        }
      }
    }
};