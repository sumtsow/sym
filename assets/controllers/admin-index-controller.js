import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
    static targets = ['select', 'rows', 'del', 'types'];

    connect() {
      window.deleteRow = this.delete;
      window.isEn = document.querySelector('html').getAttribute('lang') == 'en';
      if (this.selectTargets.length && this.rowsTargets.length) this.change();
    }

    change() {
      let path = this.rowsTarget.dataset.path,
          type = (path === 'device') ? document.location.search : ((path === 'param_option') ? '?type=' + this.typesTarget.value : '');
      fetch('/api/' + path + '/' + this.selectTarget.value + type)
        .then(response => response.json())
        .then(json => {
          this.rowsTarget.innerText = '';
          let btnEditRow = document.createElement('td'),
              btnEdit = document.createElement('a');
          btnEdit.setAttribute('class', 'btn btn-outline-primary');
          btnEdit.innerText = window.isEn ? 'Edit' : 'Редагувати';
          btnEditRow.append(btnEdit);
          let btnDelRow = document.createElement('td'),
              btnDel = document.createElement('a');
          btnDel.setAttribute('class', 'btn btn-outline-danger btn-del');
          btnDel.innerText = window.isEn ? 'Delete' : 'Видалити';
          btnDelRow.append(btnDel);
          for (let id in json.rows) {
            let row = document.createElement('tr');
            if (path === 'device') {
              let imgCell = document.createElement('td');
              img.setAttribute('src', '/img/img-' + id + '.jpg');
              img.setAttribute('style', 'width: 50px;');
              imgCell.append(img);
              row.append(imgCell);
            }
            for (let prop in json.rows[id]) {
              let col = document.createElement('td');
              col.innerText = json.rows[id][prop];
              row.append(col);
            }
            btnEdit.setAttribute('href', '/admin/' + path + '/edit/' + id + type);
            row.append(btnEditRow.cloneNode(true));
            btnDel.setAttribute('href', '/admin/' + path + '/' + id);
            btnDel.setAttribute('onclick', 'window.deleteRow(' + id + ');');
            btnDel.setAttribute('data-item-id-param', id);
            btnDel.setAttribute('data-admin-index-target', 'del');
            row.append(btnDelRow.cloneNode(true));
            this.rowsTarget.append(row);
          }
          if (path === 'param_option') {
            let url = document.location.origin + document.location.pathname;
            if (this.typesTarget.value != 0) url = url + '?type=' + this.typesTarget.value;
            window.history.pushState({path: url}, '', url);
            let isEmpty = this.typesTarget.value === '0';
            for (let i in this.selectTarget.options) {
              let option = this.selectTarget.options[i];
              let inList = !!json.params[option.value];
              if (option.value) option.classList.toggle('d-none', !(isEmpty || inList));
            }
          }
        });
    }

    delete(id) {
      let message = window.isEn ? 'Are you sure to delete item item with' : 'Ви дійсно хочете видалити елемент з';
      if (!window.confirm(message + ' id: ' + id + '?')) {
        event.preventDefault();
        event.stopPropagation();
      }
    }
};