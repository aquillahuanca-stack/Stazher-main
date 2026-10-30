(function ($) {
  'use strict';
  $(function () {
    var input = $('#employee-search');
    if (!input.length) return;
    var table = $('#example1').DataTable();
    var message = $('#employee-search-message');
    function normalize(value) {
      return String(value || '').normalize('NFD').replace(/[\u0300-\u036f]/g, '').toLowerCase().trim();
    }
    var employees = [];
    table.rows().every(function () {
      var row = $(this.node());
      var employee = {
        index: this.index(),
        id: row.attr('data-record-id'),
        code: row.attr('data-employee-id'),
        name: row.find('.employee-name-link').text().trim(),
        card: row.attr('data-identity-card') || ''
      };
      employee.search = normalize([employee.name, employee.card, employee.code, employee.id].join(' '));
      employees[employee.index] = employee;
    });
    function isMatch(employee) {
      var tokens = normalize(input.val()).split(/\s+/);
      return tokens.every(function (token) { return employee.search.indexOf(token) !== -1; });
    }
    $.fn.dataTable.ext.search.push(function (settings, data, index) {
      return settings.nTable.id !== 'example1' || isMatch(employees[index]);
    });
    function updateUrl() {
      var url = new URL(window.location.href);
      if (input.val().trim()) url.searchParams.set('q', input.val().trim());
      else url.searchParams.delete('q');
      window.history.replaceState({}, '', url.toString());
    }
    function validate(required) {
      var value = input.val().trim();
      var error = ((required && !value) || value.length > 100 || /[\x00-\x1f\x7f]/.test(value))
        ? 'Escribe un nombre, apellido, carnet o ID de hasta 100 caracteres.' : '';
      message.text(error);
      input.attr('aria-invalid', error ? 'true' : 'false');
      return !error;
    }
    input.on('input', function () {
      if (!validate(false)) return;
      table.draw();
      updateUrl();
    });
    $('.employee-search-form').on('submit', function (event) {
      event.preventDefault();
      if (!validate(true)) { input[0].focus(); return; }
      input.val(input.val().trim());
      table.draw();
      updateUrl();
      message.text(table.rows({ filter: 'applied' }).count() ? '' : 'No se encontraron pasantes.');
    });
    $('#clear-employee-search').on('click', function () {
      input.val('').attr('aria-invalid', 'false');
      table.search('').columns().search('');
      table.page('first').draw();
      updateUrl();
      input.trigger('focus');
      message.text('');
    });
    if (input.val()) table.draw();
  });
})(jQuery);
