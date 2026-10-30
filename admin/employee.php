<?php include 'includes/session.php'; include '../timezone.php'; ?>
<?php
$searchTerm = isset($_GET['q']) && is_string($_GET['q']) ? trim($_GET['q']) : '';
$activeTodayOnly = isset($_GET['active']) && $_GET['active'] === '1';
$searchError = '';
if (isset($_GET['q']) && (!is_string($_GET['q']) || $searchTerm === '' || mb_strlen($searchTerm) > 100 || preg_match('/[\x00-\x1F\x7F]/u', $searchTerm))) {
  $searchError = 'Escribe un nombre, apellido, carnet o ID de hasta 100 caracteres.';
  $searchTerm = '';
}
?>
<?php include 'includes/header.php'; ?>

<body class="hold-transition skin-blue sidebar-mini employee-list-page">
  <div class="wrapper">

    <?php include 'includes/navbar.php'; ?>
    <?php include 'includes/menubar.php'; ?>

    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
      <!-- Main content -->
      <section class="content">
        <style>
          .employee-list-page > .wrapper > .main-footer {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            min-height: 34px;
            padding: 7px 14px;
            font-size: 12px;
            line-height: 1.2;
          }
          .employee-list-page > .wrapper > .main-footer .pull-right {
            float: none !important;
            margin: 0;
            text-align: right;
            line-height: 1.2;
          }
          .employee-list-page > .wrapper > .main-footer strong {
            display: inline-block;
            margin: 0;
            line-height: 1.2;
          }
          .employee-page-shell {
            background: transparent;
            padding: 0 0 24px;
          }
          .employee-topbar {
            display: flex;
            align-items: center;
            gap: 16px;
            background: #fff;
            border: 1px solid #e1e8f0;
            border-radius: 14px;
            padding: 20px 22px;
            margin-bottom: 18px;
            box-shadow: 0 4px 14px rgba(20, 44, 70, .04);
          }
          .employee-icon {
            width: 46px;
            height: 46px;
            flex: 0 0 46px;
            background: #153e68;
            border-radius: 12px;
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 19px;
            box-shadow: 0 6px 14px rgba(21, 62, 104, .16);
          }
          .employee-header-copy h2 {
            margin: 0;
            font-size: 23px;
            line-height: 1.2;
            color: #18344f;
            font-weight: 700;
          }
          .employee-header-copy p {
            margin: 4px 0 0;
            color: #68798a;
            font-size: 14px;
          }
          .employee-breadcrumb {
            margin-left: auto;
            color: #607286;
            font-size: 13px;
            font-weight: 500;
            white-space: nowrap;
          }
          .employee-breadcrumb i {
            margin-right: 6px;
            color: #55718d;
          }
          .employee-card {
            background: #fff;
            border: 1px solid #e1e8f0;
            border-radius: 14px;
            box-shadow: 0 6px 20px rgba(15, 41, 68, 0.06);
            overflow: visible;
          }
          .employee-table-tools {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
            padding: 16px 18px;
            background: #fff;
            border-bottom: 1px solid #e9eef4;
          }
          .employee-toolbar-actions {
            display: flex;
            align-items: center;
            gap: 9px;
            flex: 0 0 auto;
          }
          .employee-search-form {
            margin: 0;
            display: flex;
            align-items: flex-end;
            gap: 8px;
            flex-wrap: wrap;
          }
          .employee-search-field {
            position: relative;
            display: flex;
            flex-direction: column;
            gap: 5px;
          }
          .employee-search-field label {
            margin: 0;
            color: #344b62;
            font-size: 12px;
            font-weight: 700;
          }
          .employee-search-input {
            width: 260px;
            height: 36px;
            border: 1px solid #cbd6e2;
            border-radius: 8px;
            color: #293e52;
            box-shadow: none;
          }
          .employee-search-input:focus {
            border-color: #3977a9;
            box-shadow: 0 0 0 3px rgba(57, 119, 169, .12);
          }
          .employee-search-message { color: #a94442; font-size: 12px; max-width: 260px; }
          .employee-table-tools .btn {
            min-height: 36px;
            border-radius: 8px;
            font-weight: 600;
            padding: 0 13px;
            transition: background-color .15s ease, box-shadow .15s ease;
          }
          .employee-table-tools .btn-primary {
            background: #15588b;
            border-color: #15588b;
          }
          .employee-table-tools .btn-primary:hover,
          .employee-table-tools .btn-primary:focus {
            background: #10466f;
            border-color: #10466f;
            box-shadow: 0 3px 8px rgba(16, 70, 111, .16);
          }
          .employee-table-tools .btn-success {
            background: #fff;
            border: 1px solid #b9c9d8;
            color: #294c6b;
          }
          .employee-table-tools .btn-success:hover,
          .employee-table-tools .btn-success:focus {
            background: #f1f6fa;
            border-color: #8ea9c0;
            color: #183e60;
          }
          .employee-table-tools .employee-toolbar-button {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            min-height: 40px;
            border-radius: 9px;
            padding: 0 16px;
            font-size: 13px;
            font-weight: 600;
            line-height: 1;
            text-decoration: none;
            box-shadow: 0 2px 5px rgba(20, 44, 70, .08);
            transition: background-color .16s ease, border-color .16s ease, box-shadow .16s ease, transform .16s ease;
          }
          .employee-table-tools .employee-toolbar-button i,
          .employee-table-tools .employee-toolbar-button .glyphicon {
            font-size: 13px;
          }
          .employee-table-tools .employee-toolbar-button-primary {
            color: #fff;
            background: #15588b;
            border: 1px solid #15588b;
          }
          .employee-table-tools .employee-toolbar-button-primary:hover,
          .employee-table-tools .employee-toolbar-button-primary:focus {
            color: #fff;
            background: #10466f;
            border-color: #10466f;
            box-shadow: 0 4px 10px rgba(16, 70, 111, .2);
            transform: translateY(-1px);
            text-decoration: none;
          }
          .employee-table-tools .employee-toolbar-button-secondary {
            color: #fff;
            background: #198754;
            border: 1px solid #198754;
          }
          .employee-table-tools .employee-toolbar-button-secondary:hover,
          .employee-table-tools .employee-toolbar-button-secondary:focus {
            color: #fff;
            background: #146c43;
            border-color: #146c43;
            box-shadow: 0 4px 10px rgba(20, 44, 70, .1);
            transform: translateY(-1px);
            text-decoration: none;
          }
          .employee-table-tools .employee-toolbar-button:focus-visible {
            outline: 3px solid rgba(57, 119, 169, .35);
            outline-offset: 2px;
          }
          .employee-table-tools .employee-toolbar-button:active {
            box-shadow: none;
            transform: translateY(0);
          }
          .employee-table-wrap {
            padding: 0;
            overflow-x: auto;
          }
          #example1 {
            width: 100%;
            margin-bottom: 0;
            border-collapse: separate;
            border-spacing: 0;
            table-layout: fixed;
          }
          #example1 thead th {
            background: #153e68;
            color: #fff;
            font-weight: 700;
            font-size: 12px;
            letter-spacing: .035em;
            text-transform: uppercase;
            padding: 14px 18px;
            border-bottom: none;
            text-align: left;
            vertical-align: middle;
            height: 48px;
          }
          #example1.employee-list-table thead th:after {
            display: none !important;
          }
          #example1 thead th + th {
            border-left: 1px solid rgba(255, 255, 255, .1);
          }
          #example1 thead th:first-child {
            width: 18%;
          }
          #example1 thead th:nth-child(2) {
            width: 35%;
          }
          #example1 thead th.employee-total-hours {
            width: 23%;
            text-align: center;
          }
          #example1 thead th.employee-actions {
            width: 24%;
            text-align: center;
          }
          #example1 tbody td {
            padding: 14px 18px;
            vertical-align: middle;
            font-size: 13px;
            color: #33485f;
            border-top: 1px solid #edf1f5;
            line-height: 1.4;
            overflow-wrap: anywhere;
          }
          #example1 tbody tr:nth-child(even) td {
            background: #fafbfd;
          }
          #example1 tbody tr:hover td {
            background: #f1f6fa;
          }
          #example1 .employee-name-link {
            color: #173f68;
            font-weight: 600;
            text-decoration: none;
          }
          #example1 .employee-name-link:hover,
          #example1 .employee-name-link:focus {
            color: #0d6ab9;
            text-decoration: underline;
          }
          #example1 tbody td.employee-actions {
            text-align: center;
          }
          #example1 tbody td.employee-total-hours {
            text-align: center;
            font-weight: 600;
            color: #183e60;
            white-space: nowrap;
          }
          .employee-actions {
            white-space: nowrap;
          }
          .employee-action {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            min-width: 68px;
            height: 30px;
            border: 1px solid #cbd8e4;
            border-radius: 7px;
            margin: 0 2px;
            color: #244d70;
            background: #fff;
            text-decoration: none;
            font-size: 12px;
            font-weight: 600;
            padding: 0 9px;
            transition: background-color .15s ease, border-color .15s ease, color .15s ease;
          }
          .employee-action:hover,
          .employee-action:focus {
            color: #153e68;
            background: #edf4f9;
            border-color: #9fb5c8;
            text-decoration: none;
          }
          .employee-action-add-hours {
            min-width: 30px;
            width: 30px;
            padding: 0;
            color: #fff;
            background: #198754;
            border-color: #198754;
          }
          .employee-action-add-hours:hover,
          .employee-action-add-hours:focus {
            color: #fff;
            background: #146c43;
            border-color: #146c43;
          }
          .employee-action-delete {
            background: #d9534f;
          }
          @media (max-width: 767px) {
            .employee-list-page > .wrapper > .main-footer {
              align-items: center;
              padding-right: 14px;
              padding-left: 14px;
            }
            .employee-topbar {
              flex-wrap: wrap;
              padding: 16px;
            }
            .employee-breadcrumb {
              margin-left: 0;
              width: 100%;
            }
            .employee-table-tools {
              align-items: stretch;
              flex-direction: column;
              padding: 14px;
            }
            .employee-toolbar-actions {
              justify-content: space-between;
            }
            .employee-search-form { align-items: stretch; }
            .employee-search-field { flex: 1 1 100%; }
            .employee-search-input { width: 100%; }
            #example1 {
              min-width: 620px;
            }
          }
        </style>

        <div class="employee-page-shell">
          <div class="employee-topbar">
            <div class="employee-icon"><i class="fa fa-users"></i></div>
            <div class="employee-header-copy">
              <h2><?php echo $activeTodayOnly ? 'Pasantes activos hoy' : 'Pasantes'; ?></h2>
              <p><?php echo $activeTodayOnly ? 'Pasantes que marcaron su entrada hoy.' : 'Listado de pasantes registrados en el sistema.'; ?></p>
            </div>
            <div class="employee-breadcrumb"><i class="fa fa-home"></i> Inicio <span style="color:#8aa0b7;">›</span> Pasantes</div>
          </div>
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
        <div class="row">
          <div class="col-xs-12">
            <div class="employee-card">
              <div class="employee-table-tools">
                <div class="employee-toolbar-actions">
                  <a href="#addnew" data-toggle="modal" class="btn btn-sm btn-flat employee-toolbar-button employee-toolbar-button-primary"><i class="fa fa-plus" aria-hidden="true"></i> Registrar pasante</a>
                  <a href="employee_print.php" target="_blank" rel="noopener" class="btn btn-sm btn-flat employee-toolbar-button employee-toolbar-button-secondary">
                    <span class="glyphicon glyphicon-print"></span> Imprimir
                  </a>
                </div>

                <form method="GET" action="employee.php" class="form-inline employee-search-form">
                  <?php if ($activeTodayOnly): ?><input type="hidden" name="active" value="1"><?php endif; ?>
                  <div class="employee-search-field">
                    <label for="employee-search">Buscar pasante</label>
                    <input type="text" id="employee-search" name="q" class="form-control employee-search-input" value="<?php echo htmlspecialchars($searchTerm, ENT_QUOTES, 'UTF-8'); ?>" placeholder="Nombre, apellido, carnet o ID" maxlength="100" autocomplete="off" aria-describedby="employee-search-message">
                    <span id="employee-search-message" class="employee-search-message" role="status" aria-live="polite"><?php echo htmlspecialchars($searchError, ENT_QUOTES, 'UTF-8'); ?></span>
                  </div>
                  <button type="submit" class="btn btn-primary btn-sm btn-flat"><i class="fa fa-search" aria-hidden="true"></i> Buscar</button>
                  <button type="button" id="clear-employee-search" class="btn btn-default btn-sm btn-flat">Limpiar</button>
                </form>
              </div>

              <div class="employee-table-wrap">
                <table id="example1" class="table employee-list-table">
                  <thead>
                    <tr>
                      <th>IDENTIFICADOR</th>
                      <th>Nombre</th>
                      <th class="employee-total-hours">Horas totales</th>
                      <th class="employee-actions">Acciones</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php
                    $sql = "SELECT *, employees.id AS empid,
                    COALESCE((SELECT SUM(" . attendanceSecondsSql('attendance_hours') . ")
                      FROM attendance attendance_hours
                      WHERE attendance_hours.employee_id = employees.id), 0)
                      + employees.accumulated_extra_seconds AS total_seconds
                    FROM employees 
                    LEFT JOIN position ON position.id = employees.position_id 
                    LEFT JOIN schedules ON schedules.id = employees.schedule_id
                    LEFT JOIN modality ON modality.id = employees.modality_id
                    LEFT JOIN institution ON institution.id = employees.institution_id
                    LEFT JOIN area ON area.id = employees.area_id
                    LEFT JOIN career ON career.id = employees.career_id";
                    if ($activeTodayOnly) {
                      $today = date('Y-m-d');
                      $sql .= " WHERE EXISTS (SELECT 1 FROM attendance active_attendance WHERE active_attendance.employee_id = employees.id AND active_attendance.date = ? AND active_attendance.time_in IS NOT NULL AND active_attendance.time_in <> '00:00:00')";
                      $sql .= " ORDER BY total_seconds DESC, employees.lastname ASC, employees.firstname ASC";
                      $statement = $conn->prepare($sql);
                      $statement->bind_param('s', $today);
                      $statement->execute();
                      $query = $statement->get_result();
                    } else {
                      $sql .= " ORDER BY total_seconds DESC, employees.lastname ASC, employees.firstname ASC";
                      $query = $conn->query($sql);
                    }

                    while ($row = $query->fetch_assoc()) {
                      $totalSeconds = max(0, (int) $row['total_seconds']);
                      $totalHours = intdiv($totalSeconds, 3600);
                      $totalMinutes = intdiv($totalSeconds % 3600, 60);
                      ?>
                      <tr data-employee-id="<?php echo htmlspecialchars($row['employee_id'], ENT_QUOTES, 'UTF-8'); ?>" data-record-id="<?php echo (int) $row['empid']; ?>" data-identity-card="<?php echo htmlspecialchars($row['identity_card'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                        <td><?php echo htmlspecialchars($row['employee_id'], ENT_QUOTES, 'UTF-8'); ?></td>
                        <td><a class="employee-name-link" href="employee_detail.php?id=<?php echo (int) $row['empid']; ?>"><?php echo htmlspecialchars($row['firstname'] . ' ' . $row['lastname'], ENT_QUOTES, 'UTF-8'); ?></a></td>
                        <td class="employee-total-hours"><?php echo $totalHours . ' h ' . str_pad((string) $totalMinutes, 2, '0', STR_PAD_LEFT) . ' min'; ?></td>
                        <td class="employee-actions">
                          <button type="button" class="employee-action employee-action-add-hours add-total-hours" data-empid="<?php echo htmlspecialchars($row['employee_id'], ENT_QUOTES, 'UTF-8'); ?>" data-name="<?php echo htmlspecialchars($row['firstname'] . ' ' . $row['lastname'], ENT_QUOTES, 'UTF-8'); ?>" title="Sumar horas al total" aria-label="Sumar horas al total de <?php echo htmlspecialchars($row['firstname'] . ' ' . $row['lastname'], ENT_QUOTES, 'UTF-8'); ?>"><i class="fa fa-plus-circle" aria-hidden="true"></i><span class="sr-only">Sumar horas</span></button>
                          <button type="button" class="employee-action edit" data-id="<?php echo $row['empid']; ?>" title="Editar pasante" aria-label="Editar pasante"><i class="fa fa-pencil" aria-hidden="true"></i><span>Editar</span></button>
                          <a class="employee-action" href="employee_detail.php?id=<?php echo $row['empid']; ?>" title="Ver pasante" aria-label="Ver pasante"><i class="fa fa-eye" aria-hidden="true"></i><span>Ver</span></a>
                        </td>
                      </tr>
                      <?php
                    }
                    ?>
                  </tbody>
                </table>
              </div>
            </div>

          </div>
        </div>
      </section>
    </div>

    <?php include 'includes/footer.php'; ?>
    <?php include 'includes/employee_modal.php'; ?>
    <?php include 'includes/add_total_hours_modal.php'; ?>
  </div>
  <?php include 'includes/scripts.php'; ?>
  <script src="../dist/js/employee-search.js"></script>
  <script>
    $(document).ready(function () {
      if (new URLSearchParams(window.location.search).get('new') === '1') {
        $('#addnew').modal('show');
      }
      // Delegar el evento de clic para el botón de editar
      $(document).on('click', '.edit', function (e) {
        e.preventDefault();
        $('#edit').modal('show');
        var id = $(this).data('id');
        getRow(id);
      });

      // Delegar el evento de clic para el botón de eliminar
      $(document).on('click', '.delete', function (e) {
        e.preventDefault();
        $('#delete').modal('show');
        var id = $(this).data('id');
        getRow(id);
      });

      // Delegar el evento de clic para la foto
      $(document).on('click', '.photo', function (e) {
        e.preventDefault();
        var id = $(this).data('id');
        getRow(id);
      });

      $(document).on('click', '.add-total-hours', function () {
        $('#total-hours-empid').val($(this).data('empid'));
        $('#total-hours-employee-name').text($(this).data('name'));
        $('#total-hours-value').val('0').trigger('focus');
        $('#total-minutes-value').val('0');
        $('#addTotalHoursModal').modal('show');
      });

      function getRow(id) {
        $.ajax({
          type: 'POST',
          url: 'employee_row.php',
          data: { id: id },
          dataType: 'json',
          success: function (response) {
            $('.empid').val(response.empid);
            $('.employee_id').text(response.employee_id);
            $('.del_employee_name').text(response.firstname + ' ' + response.lastname);
            $('#employee_name').text(response.firstname + ' ' + response.lastname);
            $('#edit_firstname').val(response.firstname);
            $('#edit_lastname').val(response.lastname);
            $('#edit_identity_card').val(response.identity_card);
            $('#edit_address').val(response.address);
            $('#edit_email').val(response.email);
            $('#edit_career').val(response.career);
            $('#edit_area').val(response.area);
            $('#datepicker_edit').val(response.birthdate);
            $('#edit_contact').val(response.contact_info);
            $('#email_val').val(response.email).text(response.email);
            $('#edit_gender').val(response.gender);
            $('#position_val').val(response.position_id).text(response.description);
            $('#career_val').val(response.career_id).text(response.name_career);
            $('#area_val').val(response.area_id).text(response.name_area);
            $('#modality_val').val(response.modality_id).text(response.type_modality);
            $('#institution_val').val(response.institution_id).text(response.name_institution);
            if (response.schedule_id) {
              $('#edit_schedule').val(response.schedule_id);
            } else {
              $('#edit_schedule').val(10);
            }
            var requiredHours = 0;
            if (response.add_hr) {
              var parts = String(response.add_hr).split(':');
              requiredHours = Number(parts[0] || 0) + ((Number(parts[1] || 0) / 60) || 0) + ((Number(parts[2] || 0) / 3600) || 0);
            }
            $('#edit_required_hours').val(requiredHours);
          }
        });
      }

      var editId = new URLSearchParams(window.location.search).get('edit');
      if (editId && /^\d+$/.test(editId)) {
        $('#edit').modal('show');
        getRow(editId);
      }
    });
  </script>
</body>

</html>
