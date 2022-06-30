import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
    static targets = ['select', 'rows', 'del'];

    connect() {
      this.change();
    }

    change() {
      let path = this.rowsTarget.dataset.path;
      fetch('/api/' + path + '/' + this.selectTarget.value)
        .then(response => response.json())
        .then(json => {
          this.rowsTarget.innerText = '';
          let btnEditRow = document.createElement('td'),
              btnEdit = document.createElement('a');
          btnEdit.setAttribute('class', 'btn btn-outline-primary');
          btnEdit.innerText = 'Edit';
          btnEditRow.append(btnEdit);
          let btnDelRow = document.createElement('td'),
              btnDel = document.createElement('a');
          btnDel.setAttribute('class', 'btn btn-outline-danger btn-del');
          btnDel.innerText = 'Delete';
          btnDelRow.append(btnDel);
          for (let id in json.rows) {
            let row = document.createElement('tr');
            for (let prop in json.rows[id]) {
              let col = document.createElement('td');
              col.innerText = json.rows[id][prop];
              row.append(col);
            }
            btnEdit.setAttribute('href', '/admin/' + path + '/edit/' + id);
            row.append(btnEditRow.cloneNode(true));
            btnDel.setAttribute('href', '/admin/' + path + '/' + id);
            btnDel.setAttribute('data-action', 'click->admin-index#delete');
            btnDel.setAttribute('data-item-id-param', id);
            btnDel.setAttribute('data-admin-index-target', 'del');
            row.append(btnDelRow.cloneNode(true));
            this.rowsTarget.append(row);
          }
        });
    }

    delete(event) {
      console.log(event.params.id);
      if (!window.confirm('Are you sure to delete device item with id: ' + event.params.id + '?')) {
        event.preventDefault();
        event.stopPropagation();
      }
    }
}
