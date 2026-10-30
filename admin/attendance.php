<?php include 'includes/session.php'; ?>
<?php include 'includes/header.php'; ?>
<?php $attendanceDateNotice = $_SESSION['date_notice'] ?? ''; unset($_SESSION['date_notice']); ?>

<body class="hold-transition skin-blue sidebar-mini">
  <div class="wrapper">

    <?php include 'includes/navbar.php'; ?>
    <?php include 'includes/menubar.php'; ?>

    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
      <!-- Main content -->
      <section class="content">
        <?php
        if (isset($_SESSION['error'])) {
          echo "
            <div class='alert alert-danger alert-dismissible'>
              <button type='button' class='close' data-dismiss='alert' aria-hidden='true'>&times;</button>
              <h4><i class='icon fa fa-warning'></i> Error!</h4>
              " . $_SESSION['error'] . "
            </div>
          ";
          unset($_SESSION['error']);
        }
        if (isset($_SESSION['success'])) {
          echo "
            <div class='alert alert-success alert-dismissible'>
              <button type='button' class='close' data-dismiss='alert' aria-hidden='true'>&times;</button>
              <h4><i class='icon fa fa-check'></i>¡Proceso Exitoso!</h4>
              " . $_SESSION['success'] . "
            </div>
          ";
          unset($_SESSION['success']);
        }
        ?>
        <style>
          .attendance-page-shell {
            background: #f0f3f7;
            padding: 0 0 20px;
          }
          .attendance-topbar {
            display: flex;
            align-items: center;
            gap: 16px;
            background: #f4f7fb;
            border: 1px solid #dfe7f0;
            border-radius: 12px 12px 0 0;
            padding: 18px 20px;
            margin-bottom: 0;
          }
          .attendance-icon {
            width: 42px;
            height: 42px;
            background: #1a3d7a;
            border-radius: 10px;
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
            box-shadow: 0 8px 18px rgba(26,61,122,.18);
          }
          .attendance-header-copy h2 {
            margin: 0;
            font-size: 24px;
            line-height: 1.2;
            color: #1f2d3d;
            font-weight: 800;
          }
          .attendance-header-copy p {
            margin: 4px 0 0;
            color: #5d6b7a;
            font-size: 14px;
          }
          .attendance-filter-panel {
            background: #ffffff;
            border: 1px solid #dfe7f0;
            border-top: none;
            border-radius: 0 0 12px 12px;
            padding: 18px 16px 12px;
            box-shadow: 0 2px 8px rgba(20, 44, 70, .04);
            margin-bottom: 18px;
          }
          .attendance-filter-row {
            display: flex;
            flex-wrap: wrap;
            align-items: end;
            gap: 14px;
          }
          .attendance-field { min-width: 150px; flex: 1 1 160px; }
          .attendance-field label {
            display: block;
            margin-bottom: 6px;
            color: #2b3d52;
            font-size: 13px;
            font-weight: 700;
          }
          .attendance-field input,
          .attendance-field select {
            width: 100%;
            height: 38px;
            border: 1px solid #cdd8e4;
            border-radius: 8px;
            background: #fff;
            color: #2c3d52;
            padding: 0 12px;
            font-size: 14px;
          }
          .attendance-btn {
            height: 38px;
            border: none;
            border-radius: 8px;
            padding: 0 18px;
            font-weight: 700;
            font-size: 14px;
            cursor: pointer;
          }
          .attendance-btn.primary {
            background: #0d6efd;
            color: #fff;
          }
          .attendance-report-button {
            transition: none;
          }
          .attendance-report-button:hover {
            background: #0b5ed7;
          }
          .attendance-report-button:focus-visible {
            outline: 3px solid rgba(255, 193, 7, .85);
            outline-offset: 3px;
          }
          .attendance-date-notice {
            display: none;
            position: fixed;
            top: 88px;
            right: 24px;
            z-index: 2000;
            width: min(380px, calc(100vw - 32px));
            padding: 15px 44px 15px 16px;
            border: 1px solid #cfe8df;
            border-left: 5px solid #25a36f;
            border-radius: 10px;
            background: #fff;
            color: #17375e;
            font-weight: 600;
            box-shadow: 0 12px 32px rgba(17, 45, 78, .2);
          }
          .attendance-date-notice.is-visible {
            display: flex;
            align-items: center;
            gap: 10px;
            animation: attendance-notice-in .22s ease-out;
          }
          .attendance-date-notice .notice-icon {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            flex: 0 0 32px;
            height: 32px;
            border-radius: 50%;
            background: #e6f6ee;
            color: #168052;
            font-size: 16px;
          }
          .attendance-date-notice .notice-title {
            display: block;
            margin-bottom: 2px;
            color: #123d72;
            font-size: 14px;
          }
          .attendance-date-notice .notice-text {
            display: block;
            color: #586b82;
            font-size: 13px;
            font-weight: 500;
          }
          .attendance-date-notice .notice-close {
            position: absolute;
            top: 8px;
            right: 10px;
            border: 0;
            background: transparent;
            color: #718096;
            font-size: 18px;
            cursor: pointer;
          }
          @keyframes attendance-notice-in {
            from { opacity: 0; transform: translateY(-5px); }
            to { opacity: 1; transform: translateY(0); }
          }
          .attendance-btn.secondary {
            background: #eaf1f7;
            color: #224168;
          }
          .attendance-btn.danger {
            background: #d9534f;
            color: #fff;
          }
          .attendance-table-wrap {
            background: #fff;
            border: 1px solid #dfe7f0;
            border-radius: 16px;
            overflow-x: auto;
            box-shadow: 0 8px 22px rgba(15, 41, 68, 0.08);
            width: 100%;
            max-width: 1200px;
            margin: 0 auto;
          }
          .attendance-table-tools {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 12px 16px;
            border-bottom: 1px solid #edf1f5;
            background: #fbfcff;
          }
          .attendance-show-text {
            color: #3a4d63;
            font-size: 14px;
          }
          .attendance-show-text select {
            margin: 0 8px;
            height: 32px;
            border: 1px solid #cfd9e4;
            border-radius: 6px;
            padding: 0 8px;
            background: #fff;
          }
          .attendance-search {
            position: relative;
          }
          .attendance-search input {
            height: 34px;
            width: 240px;
            border: 1px solid #d3dbe5;
            border-radius: 8px;
            padding: 0 38px 0 14px;
            font-size: 14px;
          }
          .attendance-search .fa {
            position: absolute;
            right: 12px;
            top: 9px;
            color: #7d8ea3;
          }
          #example1 {
            width: 100% !important;
            margin-bottom: 0;
            border-collapse: collapse;
            table-layout: fixed;
          }
          #example1 thead th {
            background: linear-gradient(180deg, #0b2e5a 0%, #0d3c73 100%);
            color: #ffffff;
            font-size: 13px;
            font-weight: 700;
            text-transform: none;
            letter-spacing: 0.02em;
            padding: 14px 10px;
            border-bottom: none;
            text-align: center;
            vertical-align: middle;
            height: 52px;
            position: relative;
          }
          #example1 thead th:after {
            content: "";
            position: absolute;
            right: 0;
            top: 14%;
            bottom: 14%;
            width: 1px;
            background: rgba(255, 255, 255, 0.18);
          }
          #example1 thead th:last-child:after {
            display: none;
          }
          #example1 thead th:first-child { width: 24%; min-width: 180px; }
          #example1 tbody td {
            vertical-align: middle;
            text-align: center;
            padding: 12px 10px;
            color: #2a3e5d;
            border-top: 1px solid #edf1f5;
            background: #fff;
            font-size: 12px;
            line-height: 1.3;
            word-break: break-word;
            height: 46px;
          }
          #example1 tbody tr:nth-child(even) td { background: #fafcff; }
          #example1 tbody tr td:first-child,
          #example1 thead th:first-child {
            width: 24%;
            min-width: 180px;
            max-width: none;
          }
          #example1 tbody td .label {
            display: inline-block;
            padding: 5px 10px;
            border-radius: 999px;
            font-size: 11px;
            font-weight: 700;
          }
          #example1 tbody td .label-success {
            background: linear-gradient(135deg, #19a15f 0%, #0e7d4d 100%);
            color: #fff;
            box-shadow: 0 6px 12px rgba(25, 161, 95, 0.18);
          }
          #example1 tbody td .label-warning {
            background: linear-gradient(135deg, #f0ad4e 0%, #d98c14 100%);
            color: #fff;
            box-shadow: 0 6px 12px rgba(240, 173, 78, 0.18);
          }
          #example1 tbody td .label-danger {
            background: linear-gradient(135deg, #d9534f 0%, #b83f3a 100%);
            color: #fff;
            box-shadow: 0 6px 12px rgba(217, 83, 79, 0.18);
          }
          .attendance-action-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 28px;
            height: 28px;
            border-radius: 8px;
            margin: 0 2px;
            color: #fff;
            border: none;
            font-size: 11px;
            padding: 0;
            line-height: 1;
            box-shadow: 0 5px 12px rgba(13, 69, 131, 0.12);
          }
          .attendance-action-btn.edit { background: linear-gradient(135deg, #0d6efd 0%, #0a4da0 100%); }
          .attendance-action-btn.delete { background: linear-gradient(135deg, #f0ad4e 0%, #c67a17 100%); }
          .attendance-action-btn.view { background: linear-gradient(135deg, #28a745 0%, #1e7e34 100%); }
          .attendance-actions-cell {
            white-space: nowrap;
            width: 1%;
            text-align: center;
          }
          .dataTables_wrapper .dataTables_paginate .paginate_button.current {
            background: #0d6efd !important;
            border-color: #0d6efd !important;
            color: #fff !important;
          }
          .dataTables_wrapper .dataTables_length,
          .dataTables_wrapper .dataTables_filter {
            display: none;
          }
          @media (max-width: 767px) {
            .attendance-filter-row { display: grid; grid-template-columns: 1fr; }
            .attendance-search input { width: 100%; }
            .attendance-table-tools { display: block; }
            .attendance-search { margin-top: 10px; }
            .attendance-table-wrap { width: 100%; }
            #example1 thead th:first-child,
            #example1 tbody tr td:first-child { min-width: 150px; }
          }
        </style>

        <div class="attendance-page-shell">
          <div class="attendance-topbar" style="display:flex; align-items:center; justify-content:space-between;">
            <div style="display:flex; align-items:center; gap:16px;">
              <div class="attendance-icon"><i class="fa fa-calendar-check-o"></i></div>
              <div class="attendance-header-copy">
                <h2>Asistencia</h2>
                <p>Historial de todos los pasantes con registros presentes u omisiones. Filtra por fecha, nombre o ID.</p>
              </div>
            </div>
            <div style="font-size:12px; color:#4f627a; font-weight:700; white-space:nowrap; margin-left:auto;">
              <i class="fa fa-home" style="margin-right:6px;"></i> Inicio <span style="color:#8aa0b7;">›</span> Asistencia
            </div>
          </div>

          <div class="attendance-filter-panel">
            <div class="attendance-filter-row">
              <div class="attendance-field">
                <label for="attendanceDateFrom">Desde</label>
                <input type="date" id="attendanceDateFrom" name="from" form="attendanceReportForm" class="form-control" required>
              </div>
              <div class="attendance-field">
                <label for="attendanceDateTo">Hasta</label>
                <input type="date" id="attendanceDateTo" name="to" form="attendanceReportForm" class="form-control">
              </div>
              <form id="attendanceReportForm" action="attendance_print.php" method="get" target="_blank">
                <button type="submit" id="generateAttendanceReport" class="attendance-btn primary attendance-report-button"><i class="fa fa-file-pdf-o" aria-hidden="true"></i> Generar reporte PDF</button>
              </form>
              <div id="attendanceDateNotice" class="attendance-date-notice<?php echo $attendanceDateNotice ? ' is-visible' : ''; ?>" role="status" aria-live="polite">
                <i class="fa fa-calendar-check-o notice-icon" aria-hidden="true"></i>
                <span><strong class="notice-title">Aviso</strong><span class="notice-text"><?php echo htmlspecialchars($attendanceDateNotice ?: 'Seleccione fechas desde y hasta válidas para generar el reporte.', ENT_QUOTES, 'UTF-8'); ?></span></span>
                <button type="button" class="notice-close" aria-label="Cerrar aviso">&times;</button>
              </div>
              <div class="attendance-field">
                <label for="employeeInput">Pasante</label>
                <input type="text" id="employeeInput" class="form-control" placeholder="Buscar por nombre o ID...">
              </div>
              <button type="button" id="searchAttendanceBtn" class="attendance-btn primary">Buscar</button>
              <button type="button" id="clearAttendanceFilters" class="attendance-btn secondary">Limpiar</button>
            </div>
          </div>

          <div class="attendance-table-wrap">
            <table id="example1" class="table table-bordered attendance-list-table server-side-table">
              <thead>
                <tr>
                  <th>Nombre</th>
                  <th>Fecha</th>
                  <th>Hora Entrada</th>
                  <th>Hora Salida</th>
                  <th>Total Horas</th>
                  <th>Estado</th>
                  <th>Acción</th>
                </tr>
              </thead>
              <tbody></tbody>
            </table>
          </div>
        </div>
      </section>
    </div>

    <?php include 'includes/footer.php'; ?>
    <?php include 'includes/attendance_modal.php'; ?>
  </div>
  <?php include 'includes/scripts.php'; ?>
  <script>
    (function() {
      var reportDateFrom = document.getElementById('attendanceDateFrom');
      var reportDateTo = document.getElementById('attendanceDateTo');
      var reportForm = document.getElementById('attendanceReportForm');
      var dateNotice = document.getElementById('attendanceDateNotice');
      if (reportDateFrom && reportDateTo) {
        reportDateFrom.addEventListener('change', function() { reportDateTo.min = reportDateFrom.value; });
        reportDateTo.addEventListener('change', function() { reportDateFrom.max = reportDateTo.value; });
      }
      if (reportForm && reportDateFrom && reportDateTo && dateNotice) {
        var noticeTimer;
        function showDateNotice(message) {
          dateNotice.querySelector('.notice-text').textContent = message;
          dateNotice.classList.add('is-visible');
          window.clearTimeout(noticeTimer);
          noticeTimer = window.setTimeout(function() { dateNotice.classList.remove('is-visible'); }, 5000);
        }
        dateNotice.querySelector('.notice-close').addEventListener('click', function() {
          dateNotice.classList.remove('is-visible');
          window.clearTimeout(noticeTimer);
        });
        reportForm.addEventListener('submit', function(event) {
          if (!reportDateFrom.value || (reportDateTo.value && reportDateFrom.value > reportDateTo.value)) {
            event.preventDefault();
            showDateNotice(reportDateTo.value
              ? 'La fecha desde debe ser anterior o igual a la fecha hasta.'
              : 'Seleccione al menos la fecha desde para generar el reporte.');
            (!reportDateFrom.value ? reportDateFrom : reportDateTo).focus();
          }
        });
      }
    })();

    $(function() {
      var attendanceTable = $('#example1').DataTable({
        processing: true,
        serverSide: true,
        pageLength: 25,
        lengthMenu: [[10, 25, 50, 100], [10, 25, 50, 100]],
        pagingType: 'full_numbers',
        searching: false,
        autoWidth: false,
        order: [[1, 'desc'], [2, 'desc']],
        dom: 'rt<"row"<"col-sm-5"i><"col-sm-7"p>>',
        ajax: {
          url: 'attendance_data.php',
          type: 'GET',
          data: function (request) {
            request.date_from = $('#attendanceDateFrom').val();
            request.date_to = $('#attendanceDateTo').val();
            request.employee = $('#employeeInput').val();
          }
        },
        columns: [
          { data: 'employee' }, { data: 'date' }, { data: 'time_in' },
          { data: 'time_out' }, { data: 'hours', orderable: false },
          { data: 'status', orderable: false }, { data: 'actions', orderable: false }
        ],
        language: {
          info: 'Mostrando _START_ a _END_ de _TOTAL_ registros',
          infoEmpty: 'No hay registros para mostrar',
          zeroRecords: 'No se encontraron registros.',
          processing: 'Cargando...',
          paginate: { first: 'Primera', previous: 'Anterior', next: 'Siguiente', last: 'Última' }
        }
      });
      function applyAttendanceFilters() {
        attendanceTable.ajax.reload(null, true);
      }
      $('#searchAttendanceBtn').on('click', function() {
        applyAttendanceFilters();
      });

      var searchTimer;
      $('#employeeInput').on('input', function () {
        window.clearTimeout(searchTimer);
        searchTimer = window.setTimeout(applyAttendanceFilters, 300);
      });
      $('#attendanceDateFrom, #attendanceDateTo').on('change', applyAttendanceFilters);
      $('#attendanceDateFrom').on('change', function() { $('#attendanceDateTo').attr('min', this.value); });
      $('#attendanceDateTo').on('change', function() { $('#attendanceDateFrom').attr('max', this.value); });
      $('#clearAttendanceFilters').on('click', function() {
        $('#attendanceDateFrom, #attendanceDateTo, #employeeInput').val('');
        $('#attendanceDateFrom, #attendanceDateTo').removeAttr('min max');
        applyAttendanceFilters();
      });

      // Utiliza la delegación de eventos en lugar de adjuntar directamente a .edit y .delete
      $(document).on('click', '.edit', function(e) {
        e.preventDefault();
        $('#edit').modal('show');
        var id = $(this).data('id');
        getRow(id);
      });

      $(document).on('click', '.delete', function(e) {
        e.preventDefault();
        $('#delete').modal('show');
        var id = $(this).data('id');
        getRow(id);
      });
    });

    function getRow(id) {
      $.ajax({
        type: 'POST',
        url: 'attendance_row.php',
        data: {
          id: id
        },
        dataType: 'json',
        success: function(response) {
          $('#datepicker_edit').val(response.date);
          $('#attendance_date').text(response.date);
          $('#edit_time_in').val(response.time_in);
          $('#edit_time_out').val(response.time_out);
          $('#attid').val(response.attid);
          $('#employee_name').text(response.firstname + ' ' + response.lastname);
          $('#del_attid').val(response.attid);
          $('#del_employee_name').text(response.firstname + ' ' + response.lastname);
        }
      });
    }

    // Script para manejar la impresión del reporte único
    var printUniqueButton = document.getElementById('printUnique');
    if (printUniqueButton) printUniqueButton.addEventListener('click', function() {
        var employee_id = document.getElementById('employeeInput').value.trim();
        var url = 'asistence_print_unique.php?employee_id=' + encodeURIComponent(employee_id);
        window.open(url, '_blank', 'noopener');
    });

    $(document).ready(function() {
  // Verifica si la tabla ya está inicializada
  if (!$.fn.DataTable.isDataTable('#example1')) {
    $('#example1').DataTable({
      "paging": true,
      "lengthChange": false,
      "searching": true,
      "ordering": true,
      "info": true,
      "autoWidth": false
    });
  }
});

  </script>
</body>

</html>
