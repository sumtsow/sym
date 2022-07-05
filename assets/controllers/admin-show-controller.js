import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
    static targets = ['select', 'rows'];

    connect() {
      if (this.selectTargets.length && this.rowsTargets.length) this.changeParam();
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
};