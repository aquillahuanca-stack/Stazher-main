<?php include 'includes/session.php'; ?>
<?php include 'includes/header.php'; ?>
<?php
  $detailEmployeeId = isset($_GET['id']) && ctype_digit($_GET['id']) ? (int) $_GET['id'] : 0;
  $detailEmployeeCode = '';
  if ($detailEmployeeId) {
    $detailStatement = $conn->prepare('SELECT employee_id FROM employees WHERE id = ?');
    $detailStatement->bind_param('i', $detailEmployeeId);
    $detailStatement->execute();
    $detailResult = $detailStatement->get_result()->fetch_assoc();
    $detailEmployeeCode = $detailResult ? $detailResult['employee_id'] : '';
    $detailStatement->close();
  }
?>

<style>
  .detail-person-icon { width: 100px; height: 100px; margin: 0 auto; border-radius: 50%; background: #e9eef3; color: #3c8dbc; font-size: 52px; line-height: 100px; }
  .detail-edit-button { border: 1px solid #194d78 !important; border-radius: 5px !important; background: linear-gradient(135deg, #2b6c9e, #17476f) !important; color: #fff !important; font-weight: 600; box-shadow: 0 2px 5px rgba(23, 71, 111, .2); transition: background .18s ease, box-shadow .18s ease, transform .18s ease; }
  .detail-edit-button:hover, .detail-edit-button:focus { background: linear-gradient(135deg, #225b87, #123b60) !important; color: #fff !important; box-shadow: 0 4px 9px rgba(18, 59, 96, .28); transform: translateY(-1px); }
  .detail-list dt { margin-bottom: 9px; }
  .detail-list dd { margin-bottom: 9px; word-break: break-word; }
  .detail-summary { margin: 0 -5px 20px; }
  .detail-summary .summary-column { padding: 5px; }
  .detail-summary-card { display: flex; align-items: center; justify-content: center; gap: 13px; min-height: 126px; padding: 15px 16px; border: 1px solid #d5e0eb; border-radius: 5px; background: #fff; box-shadow: 0 3px 10px rgba(20, 44, 70, .08); }
  .detail-summary-card .summary-icon { flex: 0 0 46px; width: 46px; height: 46px; margin: 0; border-radius: 5px; color: #fff; text-align: center; font-size: 24px; line-height: 46px; }
  .detail-summary-card .summary-content { min-width: 0; }
  .detail-summary-card .summary-label { margin: 0 0 3px; color: #52677d; font-size: 13px; font-weight: 700; text-align: left; text-transform: uppercase; letter-spacing: .5px; }
  .detail-summary-card .summary-value { margin: 0; color: #123c72; font-size: 34px; font-weight: 800; line-height: 1.1; text-align: left; white-space: nowrap; }
  .summary-hours .summary-icon { background: #0c8f4a; }
  .summary-required .summary-icon { background: #f2a900; }
  .summary-remaining .summary-icon { background: #5b6fe7; }
  .attendance-section { margin: 24px -15px -15px; overflow: hidden; border: 1px solid #cfe0f1; border-radius: 7px; background: #f5faff; box-shadow: 0 3px 12px rgba(27, 89, 148, .08); }
  .attendance-heading { display: flex; align-items: center; justify-content: space-between; gap: 15px; min-height: 54px; padding: 10px 16px; border-bottom: 1px solid #d5e5f5; background: linear-gradient(110deg, #f8fbff 0%, #eaf4ff 68%, #d9ebff 100%); }
  .attendance-title-group { display: flex; align-items: center; gap: 10px; min-width: 0; }
  .attendance-title-icon { display: flex; flex: 0 0 42px; align-items: center; justify-content: center; width: 42px; height: 42px; border-radius: 50%; background: #1769bd; color: #fff; font-size: 19px; box-shadow: 0 2px 6px rgba(23, 105, 189, .25); }
  .attendance-heading h3 { margin: 0; color: #12365e; font-size: 21px; font-weight: 800; letter-spacing: .45px; line-height: 1.3; text-transform: uppercase; }
  .attendance-actions { display: flex; flex: 0 0 auto; align-items: flex-end; gap: 8px; }
  .workload-report-form { display: flex; align-items: flex-end; gap: 7px; margin: 0; }
  .workload-report-field { display: flex; flex-direction: column; gap: 3px; color: #365779; font-size: 10px; font-weight: 700; }
  .workload-report-field input { width: 132px; height: 35px; padding: 5px 7px; border: 1px solid #cbd8e4; border-radius: 4px; font-size: 12px; }
  .workload-report-error { display: none; padding: 6px 12px; color: #a94442; background: #fdf2f2; font-size: 12px; }
  .attendance-heading .btn { flex: 0 0 auto; padding: 10px 15px; border: 1px solid #105ca8; border-radius: 5px; background: #1769bd; color: #fff; font-size: 13px; font-weight: 700; box-shadow: 0 2px 5px rgba(23, 105, 189, .22); }
  .attendance-heading .btn:hover, .attendance-heading .btn:focus { border-color: #0c4e91; background: #12589f; color: #fff; box-shadow: 0 3px 7px rgba(18, 88, 159, .3); }
  .attendance-actions a.btn { border-color: #08727c; background: #078b96; box-shadow: 0 2px 5px rgba(7, 139, 150, .22); }
  .attendance-actions a.btn:hover, .attendance-actions a.btn:focus { border-color: #055d65; background: #066f78; color: #fff; box-shadow: 0 3px 7px rgba(6, 111, 120, .3); }
  .attendance-heading .workload-report-button { transition: transform .2s ease, box-shadow .2s ease, background-color .2s ease; }
  .attendance-heading .workload-report-button i { display: inline-block; transition: transform .45s ease; }
  .attendance-heading .workload-report-button:hover,
  .attendance-heading .workload-report-button:focus-visible { transform: translateY(-2px); box-shadow: 0 5px 11px rgba(6, 111, 120, .3); }
  .attendance-heading .workload-report-button:hover i,
  .attendance-heading .workload-report-button:focus-visible i { transform: rotate(360deg); }
  @media (prefers-reduced-motion: reduce) { .attendance-heading .workload-report-button, .attendance-heading .workload-report-button i { transition: none; } }
  .attendance-section .table-responsive { padding: 8px; background: #f5faff; }
  .attendance-table { margin-bottom: 0; border: 1px solid #dce8f4; border-radius: 4px; background: #fff; }
  .attendance-table thead th { padding: 9px 12px; border-bottom: 1px solid #dce8f4; background: #edf4fc; color: #365779; font-size: 10px; font-weight: 700; white-space: nowrap; }
  .attendance-table thead th i { margin-right: 5px; color: #397ab8; }
  .attendance-table tbody td { padding: 8px 12px; border-color: #e9f0f7; color: #365779; font-size: 11px; vertical-align: middle; }
  .attendance-table tbody tr:hover { background: #f5faff; }
  .attendance-table tbody td:first-child { white-space: nowrap; }
  .attendance-table tbody td:first-child:before { display: inline-block; width: 7px; height: 7px; margin-right: 9px; border-radius: 50%; background: #2479cf; content: ''; vertical-align: 1px; }
  @media (max-width: 900px) { .attendance-heading { flex-wrap: wrap; } .attendance-actions { width: 100%; justify-content: flex-start; flex-wrap: wrap; } }
  @media (max-width: 480px) { .attendance-title-icon { display: none; } .attendance-heading h3 { font-size: 18px; } .attendance-heading .btn { padding: 8px 10px; font-size: 12px; } .workload-report-form { flex-wrap: wrap; } .workload-report-field input { width: 125px; } }
</style>

<body class="hold-transition skin-blue sidebar-mini">
  <div class="wrapper">
    <?php include 'includes/navbar.php'; ?>
    <?php include 'includes/menubar.php'; ?>

    <div class="content-wrapper">
      <section class="content-header">
        <h1>Información del pasante</h1>
        <ol class="breadcrumb">
          <li><a href="home.php"><i class="fa fa-dashboard"></i> Inicio</a></li>
          <li><a href="employee.php">Pasantes</a></li>
          <li class="active">Información</li>
        </ol>
      </section>

      <section class="content">
        <?php foreach (array('success' => 'success', 'error' => 'danger') as $messageKey => $alertType): ?>
          <?php if (isset($_SESSION[$messageKey])): ?>
            <div class="alert alert-<?php echo $alertType; ?> alert-dismissible">
              <button type="button" class="close" data-dismiss="alert" aria-label="Cerrar">&times;</button>
              <?php echo htmlspecialchars($_SESSION[$messageKey], ENT_QUOTES, 'UTF-8'); unset($_SESSION[$messageKey]); ?>
            </div>
          <?php endif; ?>
        <?php endforeach; ?>
        <div class="clearfix margin-bottom">
          <a href="employee.php" class="btn btn-default btn-flat pull-left"><i class="fa fa-arrow-left"></i> Atrás a la lista</a>
          <a href="employee.php?edit=<?php echo isset($_GET['id']) && ctype_digit($_GET['id']) ? (int) $_GET['id'] : 0; ?>" class="btn btn-primary btn-flat pull-right detail-edit-button" style="margin-left: 8px;">
            <i class="fa fa-pencil"></i> Editar
          </a>
          <a href="certificate_edit.php?empid=<?php echo urlencode($detailEmployeeCode); ?>" class="btn btn-info btn-flat pull-right" style="margin-left: 8px;">
            <i class="fa fa-file-word-o"></i> Certificado-Word
          </a>
        </div>
        <div class="box box-primary">
          <div class="box-body" id="detail-loading"><p class="text-muted"><i class="fa fa-spinner fa-spin"></i> Cargando información...</p></div>
          <div class="box-body" id="detail-content" style="display: none;">
            <div class="row">
              <div class="col-sm-3 text-center">
                <div class="detail-person-icon" aria-label="Ícono de pasante"><i class="fa fa-user"></i></div>
                <h3 id="detail-name"></h3><p class="text-muted" id="detail-id"></p>
              </div>
              <div class="col-sm-4"><dl class="dl-horizontal detail-list">
                <dt>Carnet:</dt><dd id="detail-ci"></dd><dt>Correo:</dt><dd id="detail-email"></dd>
                <dt>Teléfono:</dt><dd id="detail-phone"></dd><dt>Dirección:</dt><dd id="detail-address"></dd>
                <dt>Fecha de nacimiento:</dt><dd id="detail-birthdate"></dd><dt>Género:</dt><dd id="detail-gender"></dd>
              </dl></div>
              <div class="col-sm-5"><dl class="dl-horizontal detail-list">
                <dt>Área:</dt><dd id="detail-area"></dd><dt>Cargo:</dt><dd id="detail-position"></dd>
                <dt>Carrera:</dt><dd id="detail-career"></dd><dt>Institución:</dt><dd id="detail-institution"></dd>
                <dt>Modalidad:</dt><dd id="detail-modality"></dd><dt>Horario:</dt><dd id="detail-schedule"></dd><dt>Registrado:</dt><dd id="detail-created"></dd>
              </dl></div>
            </div>
            <div class="row detail-summary">
              <div class="col-sm-4 summary-column">
                <div class="detail-summary-card summary-hours">
                  <span class="summary-icon"><i class="fa fa-clock-o"></i></span>
                  <div class="summary-content">
                    <p class="summary-label">Horas acumuladas</p>
                    <p class="summary-value"><span id="detail-total-hours">0</span> h</p>
                  </div>
                </div>
              </div>
              <div class="col-sm-4 summary-column">
                <div class="detail-summary-card summary-required">
                  <span class="summary-icon"><i class="fa fa-check-circle"></i></span>
                  <div class="summary-content">
                    <p class="summary-label">Horas requeridas</p>
                    <p class="summary-value"><span id="detail-required-hours">0</span> h</p>
                  </div>
                </div>
              </div>
              <div class="col-sm-4 summary-column">
                <div class="detail-summary-card summary-remaining">
                  <span class="summary-icon"><i class="fa fa-hourglass-half"></i></span>
                  <div class="summary-content">
                    <p class="summary-label">Horas restantes</p>
                    <p class="summary-value"><span id="detail-remaining-hours">0</span> h</p>
                  </div>
                </div>
              </div>
            </div>
            <div class="attendance-section">
            <div class="attendance-heading">
              <div class="attendance-title-group">
                <span class="attendance-title-icon"><i class="fa fa-calendar"></i></span>
                <h3>Detalle de registros</h3>
              </div>
              <div class="attendance-actions">
                <form action="asistence_print_unique.php" method="get" target="_blank" id="workloadReportForm" class="workload-report-form">
                  <input type="hidden" name="employee_id" value="<?php echo $detailEmployeeId; ?>">
                  <label class="workload-report-field" for="workloadFrom">Desde<input type="date" id="workloadFrom" name="from" required></label>
                  <label class="workload-report-field" for="workloadTo">Hasta<input type="date" id="workloadTo" name="to" required></label>
                  <button type="submit" class="btn btn-flat workload-report-button"><i class="fa fa-file-pdf-o"></i> Carga horaria PDF</button>
                </form>
                <?php if ($detailEmployeeCode !== ''): ?>
                  <button type="button" id="detail-add-schedule" class="btn btn-flat" data-toggle="modal" data-target="#addScheduleModal">
                    <i class="fa fa-clock-o"></i> Agregar horario
                  </button>
                <?php endif; ?>
              </div>
            </div>
            <div id="workloadReportError" class="workload-report-error" role="alert">La fecha desde debe ser anterior o igual a la fecha hasta.</div>
            <div class="table-responsive"><table class="table attendance-table">
              <thead><tr><th scope="col"><i class="fa fa-calendar"></i>Fecha</th><th scope="col"><i class="fa fa-sign-in"></i>Hora de entrada</th><th scope="col"><i class="fa fa-sign-out"></i>Hora de salida</th><th scope="col"><i class="fa fa-clock-o"></i>Hora asistencia</th></tr></thead>
              <tbody id="detail-attendance"></tbody>
            </table></div>
            </div>
          </div>
        </div>
      </section>
    </div>
    <?php include 'includes/footer.php'; ?>
    <?php include 'includes/add_schedule_modal.php'; ?>
  </div>
  <?php include 'includes/scripts.php'; ?>
  <script>
    $(function () {
      $('#workloadFrom').on('change', function () { $('#workloadTo').attr('min', this.value); });
      $('#workloadTo').on('change', function () { $('#workloadFrom').attr('max', this.value); });
      $('#workloadReportForm').on('submit', function (event) {
        var from = $('#workloadFrom').val();
        var to = $('#workloadTo').val();
        if (!from || !to || from > to) {
          event.preventDefault();
          $('#workloadReportError').toggle(!!(from && to));
          (!from ? $('#workloadFrom') : $('#workloadTo')).trigger('focus');
        }
      });
      var employeeId = <?php echo $detailEmployeeId; ?>;
      $('#addScheduleForm #empid').val(<?php echo json_encode($detailEmployeeCode, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT); ?>);
      $('<input>', { type: 'hidden', name: 'return_employee_id', value: employeeId }).appendTo('#addScheduleForm');
      if (!employeeId) {
        $('#detail-loading').html('<p class="text-danger">El pasante solicitado no es válido.</p>');
        return;
      }

      $.ajax({
        type: 'POST', url: 'employee_details.php', data: { id: employeeId }, dataType: 'json',
        success: function (response) {
          if (!response.employee) {
            $('#detail-loading').html('<p class="text-danger">No se encontró el pasante solicitado.</p>');
            return;
          }
          var employee = response.employee;
          var summary = response.summary || {};
          $('#detail-total-hours').text(Number(summary.hours || 0).toFixed(2));
          $('#detail-required-hours').text(Number(summary.required_hours || 0).toFixed(2));
          $('#detail-remaining-hours').text(Number(summary.remaining_hours || 0).toFixed(2));
          if (summary.first_attendance_date) {
            $('#workloadFrom').val(summary.first_attendance_date).attr('min', summary.first_attendance_date);
            $('#workloadTo').attr('min', summary.first_attendance_date);
          }
          if (summary.last_attendance_date) {
            $('#workloadTo').val(summary.last_attendance_date);
            $('#workloadFrom').attr('max', summary.last_attendance_date);
          }
          $('#detail-name').text(employee.firstname + ' ' + employee.lastname);
          $('#detail-id').text('ID: ' + employee.employee_id);
          $('#detail-ci').text(employee.identity_card || 'No registrado');
          $('#detail-email').text(employee.email || 'No registrado');
          $('#detail-phone').text(employee.contact_info || 'No registrado');
          $('#detail-address').text(employee.address || 'No registrado');
          $('#detail-birthdate').text(employee.birthdate || 'No registrado');
          $('#detail-gender').text(employee.gender || 'No registrado');
          $('#detail-area').text(employee.name_area || 'No registrado');
          $('#detail-position').text(employee.description || 'No registrado');
          $('#detail-career').text(employee.name_career || 'No registrado');
          $('#detail-institution').text(employee.name_institution || 'No registrado');
          $('#detail-modality').text(employee.type_modality || 'No registrado');
          $('#detail-schedule').text(employee.schedule || 'Horario no definido');
          $('#detail-created').text(employee.created_on || 'No registrado');

          var rows = '';
          if (response.attendance.length) {
            $.each(response.attendance, function (_, attendance) {
              var hasExit = attendance.exit_status === 'recorded' || attendance.exit_status === 'scheduled';
              var attendanceHours = '<span class="text-muted" title="La duración se calcula al registrar la salida">—</span>';
              var exitDisplay;
              if (hasExit) {
                exitDisplay = $('<span>').text(attendance.time_out).prop('outerHTML');
                if (attendance.exit_status === 'scheduled') {
                  exitDisplay += '<br><span class="attendance-state attendance-state-scheduled">Salida según horario</span>';
                }
              } else if (attendance.exit_status === 'in_progress') {
                exitDisplay = '<span class="attendance-state attendance-state-progress">Horario en curso</span>';
              } else {
                exitDisplay = '<span class="attendance-state attendance-state-missing">Salida no registrada</span>';
              }
              if (hasExit && attendance.num_hr !== null && attendance.num_hr !== '') {
                var decimalHours = Number(attendance.num_hr);
                var wholeHours = Math.floor(decimalHours);
                var minutes = Math.floor((decimalHours - wholeHours) * 60 + 0.000001);
                if (minutes === 60) {
                  wholeHours += 1;
                  minutes = 0;
                }
                attendanceHours = wholeHours + ' h. ' + String(minutes).padStart(2, '0') + ' min.';
              }
              rows += '<tr><td>' + attendance.date + '</td><td>' + attendance.time_in + '</td><td>' + exitDisplay + '</td><td>' + attendanceHours + '</td></tr>';
            });
          } else {
            rows = '<tr><td colspan="4" class="text-center text-muted">No hay registros de marcación.</td></tr>';
          }
          $('#detail-attendance').html(rows);
          $('#detail-loading').hide();
          $('#detail-content').show();
        },
        error: function () { $('#detail-loading').html('<p class="text-danger">No se pudo cargar la información del pasante.</p>'); }
      });
    });
  </script>
</body>
</html>
