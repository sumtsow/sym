import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
    static targets = ['select', 'rows', 'options'];

    connect() {
      if (this.selectTargets.length && this.rowsTargets.length) this.changeParam();
      let params = new URLSearchParams(document.location.search);
      if (this.selectTargets.length && this.optionsTargets.length && params.has('type')) {
        let options = this.selectTarget.options;
        for (let i in options) {
          if (options[i].value) {
            if (options[i].value === params.get('type')) {
              options[i].setAttribute('selected', 'selected');
            } else {
              options[i].removeAttribute('selected');
            }
          }
        }
        this.changeType();
      }
    }

    changeParam() {
      let select = this.rowsTarget,
          needed = this.selectTarget.value ? parseInt(this.selectTarget.value, 10) + 1 : null;
      for (let i in select.options) {
        let option = select.options[i];
        if (option.classList) {
          option.classList.toggle('d-none', (needed && needed != option.dataset.parent));
        }
      }
    }

    changeType() {
      let options = this.optionsTarget.options,
          showAll = this.selectTarget.value === '0';
      for (let i in options) {
        let option = options[i];
        if (option.value) {
          let inList = option.dataset.type === this.selectTarget.value;
          option.classList.toggle('d-none', !(showAll || inList));
        }
      }
    }

};