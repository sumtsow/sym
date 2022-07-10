import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
    static targets = ['select', 'items', 'compare', 'btn'];

    connect() {
      this.translate = {
        uk: {
          add: 'Додати до порівняння',
          rm: 'Видалити з порівняння',
          type: 'Порівняти можна лише пристрої одного типу!'
        },
        en: {
          add: 'Add to compare',
          rm: 'Remove from compare',
          type: 'Only same type devices can be compared!'
        }
      };
      this.lang = document.querySelector('html').getAttribute('lang');
    }

    change() {
      let items = this.itemsTarget.children;
      for(let index in items) {
        if (items[index].dataset) {
          items[index].classList.toggle('d-none', (this.selectTarget.value !== '0' && (this.selectTarget.value !== items[index].dataset.type)));
        }
      }
    }

    compare() {
      let devices = {},
          form = this.btnTarget.parentNode,
          hasDifferentTypes = false,
          type;
      for (let i in this.compareTargets) {
        if (this.compareTargets[i]['checked']) {
          if (!type) type = this.itemsTarget.children[i].dataset.type;
          if (type !== this.itemsTarget.children[i].dataset.type) {
            console.log(type);
            hasDifferentTypes = true;
            type = this.itemsTarget.children[i].dataset.type;
          }
          devices[i] = this.compareTargets[i].dataset.id;
        }
      }
      form[0].value = JSON.stringify(devices);
      hasDifferentTypes ? alert(this.translate[this.lang]['type']) : form.submit();
    }

    switchCompare(e) {
      let el = e.currentTarget,
        btnDisable = document.querySelectorAll('input:checked').length < 2;
      el.title = el.checked ? this.translate[this.lang]['rm'] : this.translate[this.lang]['add'];
      this.btnTarget.classList.toggle('disabled', btnDisable);
      this.btnTarget.setAttribute('aria-disabled', btnDisable);
    }
};