import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
    static targets = ['select', 'items', 'compare', 'btn'];

    connect() {
      this.translate = {
        uk: {
          add: 'Додати до порівняння',
          rm: 'Видалити з порівняння',
          type: 'Порівняти можна лише пристрої одного типу!',
          check: 'Обрати всі',
          uncheck: 'Очистити всі'
        },
        en: {
          add: 'Add to compare',
          rm: 'Remove from compare',
          type: 'Only same type devices can be compared!',
          check: 'Check all',
          uncheck: 'Uncheck all'
        }
      };
      this.lang = document.querySelector('html').getAttribute('lang');
    }

    change() {
      let items = this.itemsTarget.children;
      for(let index in items) {
        if (items[index].dataset) {
          let hide = this.selectTarget.value !== '0' && (this.selectTarget.value !== items[index].dataset.type);
          items[index].classList.toggle('d-none', hide);
          if (hide) items[index].querySelector('input[type="checkbox"]').checked = false;
        }
      }
    }

    checkAll(e) {
      let checkboxes = document.querySelectorAll('input[type="checkbox"]'),
          btn = e.currentTarget,
          state = btn.classList.contains('btn-outline-primary'),
          self = this;
        checkboxes.forEach(function(checkbox) {
          if (!checkbox.parentNode.parentNode.classList.contains('d-none')) checkbox.checked = state;
        });
        btn.innerText = state ? self.translate[self.lang]['uncheck'] : self.translate[self.lang]['check'];
        btn.classList.toggle('btn-primary', state);
        btn.classList.toggle('btn-outline-primary', !state);
        this.btnTarget.classList.toggle('disabled', !state);
        this.btnTarget.setAttribute('aria-disabled', !state);
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