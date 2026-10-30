<style>
  /* Campos numéricos: ocultar los controles de incremento y decremento. */
  #identity_card, #edit_identity_card, #contact, #edit_contact, #required_hours, #edit_required_hours {
    -moz-appearance: textfield;
    appearance: textfield;
  }
  #identity_card::-webkit-inner-spin-button,
  #identity_card::-webkit-outer-spin-button,
  #edit_identity_card::-webkit-inner-spin-button,
  #edit_identity_card::-webkit-outer-spin-button,
  #contact::-webkit-inner-spin-button,
  #contact::-webkit-outer-spin-button,
  #edit_contact::-webkit-inner-spin-button,
  #edit_contact::-webkit-outer-spin-button,
  #required_hours::-webkit-inner-spin-button,
  #required_hours::-webkit-outer-spin-button,
  #edit_required_hours::-webkit-inner-spin-button,
  #edit_required_hours::-webkit-outer-spin-button {
    -webkit-appearance: none;
    margin: 0;
  }
</style>
<!-- Add -->
<div class="modal fade employee-edit-modal" id="addnew" tabindex="-1" role="dialog" aria-labelledby="employee-add-title">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span></button>
        <div class="employee-edit-heading"><span class="employee-edit-avatar"><i class="fa fa-user-plus" aria-hidden="true"></i></span><div><h4 class="modal-title" id="employee-add-title">Nuevo pasante</h4><p>Complete la información del nuevo pasante</p></div></div>
      </div>
      <form class="form-horizontal" method="POST" action="employee_add.php" enctype="multipart/form-data"><?php echo csrfField(); ?><div class="modal-body employee-edit-fields">
          <div class="form-group">
            <label for="firstname" class="col-sm-3 control-label"><i class="fa fa-id-card-o" aria-hidden="true"></i> Nombre</label>

            <div class="col-sm-9">
              <input type="text" class="form-control" id="firstname" name="firstname" required>
            </div>
          </div>
          <div class="form-group">
            <label for="lastname" class="col-sm-3 control-label"><i class="fa fa-user-o" aria-hidden="true"></i> Apellido</label>

            <div class="col-sm-9">
              <input type="text" class="form-control" id="lastname" name="lastname" required>
            </div>
          </div>
          <div class="form-group">
            <label for="identity_card" class="col-sm-3 control-label"><i class="fa fa-id-badge" aria-hidden="true"></i> Carnet de Identidad</label>

            <div class="col-sm-9">
              <input type="number" class="form-control" id="identity_card" name="identity_card" required>
            </div>
          </div>
          <div class="form-group">
            <label for="edit_email" class="col-sm-3 control-label"><i class="fa fa-envelope" aria-hidden="true"></i> Correo electrónico</label>

            <div class="col-sm-9">
              <input type="email" class="form-control" id="edit_email" name="email" required>
            </div>
          </div>
          <div class="form-group">
            <label for="address" class="col-sm-3 control-label"><i class="fa fa-location-arrow" aria-hidden="true"></i> Dirección</label>

            <div class="col-sm-9">
              <textarea class="form-control" name="address" id="address" required></textarea>
            </div>
          </div>
          <div class="form-group">
            <label for="datepicker_add" class="col-sm-3 control-label"><i class="fa fa-calendar" aria-hidden="true"></i> Fecha de Nacimiento</label>

            <div class="col-sm-9">
              <div class="date">
                <input type="text" class="form-control" id="datepicker_add" name="birthdate" required>
              </div>
            </div>
          </div>
          <div class="form-group">
            <label for="contact" class="col-sm-3 control-label"><i class="fa fa-phone" aria-hidden="true"></i> Número de teléfono</label>

            <div class="col-sm-9">
              <input type="number" class="form-control" id="contact" name="contact" required>
            </div>
          </div>
          <div class="form-group">
            <label for="gender" class="col-sm-3 control-label"><i class="fa fa-venus-mars" aria-hidden="true"></i> Género</label>

            <div class="col-sm-9">
              <select class="form-control" name="gender" id="gender" required>
                <option value="Male">Masculino</option>
                <option value="Female">Femenino</option>
              </select>
            </div>
          </div>
          <div class="form-group">
            <label for="position" class="col-sm-3 control-label"><i class="fa fa-briefcase" aria-hidden="true"></i> Cargo</label>

            <div class="col-sm-9">
              <select class="form-control" name="position" id="position" required>
                <option value="" selected>- Seleccionar -</option>
                <?php
                $sql = "SELECT * FROM position";
                $query = $conn->query($sql);
                while ($prow = $query->fetch_assoc()) {
                  echo "
                              <option value='" . $prow['id'] . "'>" . $prow['description'] . "</option>
                            ";
                }
                ?>
              </select>
            </div>
          </div>

          <div class="form-group">
            <label for="area" class="col-sm-3 control-label"><i class="fa fa-building" aria-hidden="true"></i> Área</label>

            <div class="col-sm-9">
              <select class="form-control" name="area" id="area" required>
                <option value="" selected>- Seleccionar -</option>
                <?php
                $sql = "SELECT * FROM area";
                $query = $conn->query($sql);
                while ($prow = $query->fetch_assoc()) {
                  echo "
                              <option value='" . $prow['id'] . "'>" . $prow['name_area'] . "</option>
                            ";
                }
                ?>
              </select>
            </div>
          </div>

          <div class="form-group">
            <label for="career" class="col-sm-3 control-label"><i class="fa fa-graduation-cap" aria-hidden="true"></i> Carrera</label>

            <div class="col-sm-9">
              <select class="form-control" name="career" id="career" required>
                <option value="" selected>- Seleccionar -</option>
                <?php
                $sql = "SELECT * FROM career";
                $query = $conn->query($sql);
                while ($prow = $query->fetch_assoc()) {
                  echo "
                              <option value='" . $prow['id'] . "'>" . $prow['name_career'] . "</option>
                            ";
                }
                ?>
              </select>
            </div>
          </div>

          <div class="form-group">
            <label for="institution" class="col-sm-3 control-label"><i class="fa fa-university" aria-hidden="true"></i> Universidad o institución</label>

            <div class="col-sm-9">
              <select class="form-control" name="institution" id="institution" required>
                <option value="" selected>- Seleccionar -</option>
                <?php
                $sql = "SELECT * FROM institution";
                $query = $conn->query($sql);
                while ($prow = $query->fetch_assoc()) {
                  echo "
                              <option value='" . $prow['id'] . "'>" . $prow['name_institution'] . "</option>
                            ";
                }
                ?>
              </select>
            </div>
          </div>

          <div class="form-group">
            <label for="modality" class="col-sm-3 control-label"><i class="fa fa-graduation-cap" aria-hidden="true"></i> Tipo de Modalidad</label>

            <div class="col-sm-9">
              <select class="form-control" name="modality" id="modality" required>
                <option value="" selected>- Seleccionar -</option>
                <?php
                $sql = "SELECT * FROM modality";
                $query = $conn->query($sql);
                while ($prow = $query->fetch_assoc()) {
                  echo "
                              <option value='" . $prow['id'] . "'>" . $prow['type_modality'] . "</option>
                            ";
                }
                ?>
              </select>
            </div>
          </div>

          <div class="form-group">
            <label for="schedule" class="col-sm-3 control-label"><i class="fa fa-clock-o" aria-hidden="true"></i> Horario</label>

            <div class="col-sm-9">
              <select class="form-control" id="schedule" name="schedule" required>
                <?php
                require_once __DIR__ . '/schedule_labels.php';
                $sql = "SELECT * FROM schedules ORDER BY time_in, time_out";
                $query = $conn->query($sql);
                while ($srow = $query->fetch_assoc()) {
                  $scheduleLabel = htmlspecialchars(getScheduleOptionLabel($srow), ENT_QUOTES, 'UTF-8');
                  $selected = getScheduleTurnLabel($srow) === 'Tiempo Completo' ? 'selected' : '';
                  echo "<option value='" . (int) $srow['id'] . "' " . $selected . ">" . $scheduleLabel . "</option>";
                }
                ?>
              </select>
            </div>
          </div>
          <div class="form-group">
            <label for="required_hours" class="col-sm-3 control-label"><i class="fa fa-shield" aria-hidden="true"></i> Horas requeridas</label>

            <div class="col-sm-9">
              <input type="number" class="form-control" id="required_hours" name="required_hours" min="0" step="0.25" value="600" placeholder="600">
            </div>
          </div>
          <div class="form-group">
            <label for="photo" class="col-sm-3 control-label"><i class="fa fa-camera" aria-hidden="true"></i> Foto</label>

            <div class="col-sm-9">
              <input type="file" name="photo" id="photo">
            </div>
          </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default btn-flat pull-left" data-dismiss="modal"><i class="fa fa-close"></i> Cerrar</button>
        <button type="submit" class="btn btn-primary btn-flat" name="add"><i class="fa fa-save"></i> Guardar</button>
      </div>
      </form>
    </div>
  </div>
</div>

<!-- Edit -->
<div class="modal fade employee-edit-modal" id="edit" tabindex="-1" role="dialog" aria-labelledby="employee-edit-title">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span></button>
        <div class="employee-edit-heading"><span class="employee-edit-avatar"><i class="fa fa-user-plus" aria-hidden="true"></i></span><div><h4 class="modal-title" id="employee-edit-title">Editar pasante</h4><p>Complete la información del pasante <span class="employee_id"></span></p></div></div>
      </div>
      <form class="form-horizontal" method="POST" action="employee_edit.php"><?php echo csrfField(); ?><div class="modal-body employee-edit-fields">
          <input type="hidden" class="empid" name="id">
          <div class="form-group">
            <label for="edit_firstname" class="col-sm-3 control-label"><i class="fa fa-id-card-o" aria-hidden="true"></i> Nombre</label>
            <div class="col-sm-9">
              <input type="text" class="form-control" id="edit_firstname" name="firstname">
            </div>
          </div>
          <div class="form-group">
            <label for="edit_lastname" class="col-sm-3 control-label"><i class="fa fa-user-o" aria-hidden="true"></i> Apellido</label>

            <div class="col-sm-9">
              <input type="text" class="form-control" id="edit_lastname" name="lastname">
            </div>
          </div>

          <div class="form-group">
            <label for="edit_identity_card" class="col-sm-3 control-label"><i class="fa fa-id-badge" aria-hidden="true"></i> Carnet de Identidad</label>

            <div class="col-sm-9">
              <input type="number" class="form-control" id="edit_identity_card" name="identity_card">
            </div>
          </div>

          <div class="form-group">
            <label for="email_val" class="col-sm-3 control-label"><i class="fa fa-envelope" aria-hidden="true"></i> Correo electrónico</label>

            <div class="col-sm-9">
              <input type="email" class="form-control" id="email_val" name="email">
            </div>
          </div>

          <div class="form-group">
            <label for="edit_address" class="col-sm-3 control-label"><i class="fa fa-location-arrow" aria-hidden="true"></i> Dirección</label>

            <div class="col-sm-9">
              <textarea class="form-control" name="address" id="edit_address"></textarea>
            </div>
          </div>
          <div class="form-group">
            <label for="datepicker_edit" class="col-sm-3 control-label"><i class="fa fa-calendar" aria-hidden="true"></i> Fecha de Nacimiento</label>

            <div class="col-sm-9">
              <div class="date">
                <input type="text" class="form-control" id="datepicker_edit" name="birthdate">
              </div>
            </div>
          </div>
          <div class="form-group">
            <label for="edit_contact" class="col-sm-3 control-label"><i class="fa fa-phone" aria-hidden="true"></i> Número de teléfono</label>

            <div class="col-sm-9">
              <input type="number" class="form-control" id="edit_contact" name="contact">
            </div>
          </div>
          <div class="form-group">
            <label for="edit_gender" class="col-sm-3 control-label"><i class="fa fa-venus-mars" aria-hidden="true"></i> Género</label>

            <div class="col-sm-9">
              <select class="form-control" name="gender" id="edit_gender" required>
                <option value="Male">Masculino</option>
                <option value="Female">Femenino</option>
              </select>
            </div>
          </div>
          <div class="form-group">
            <label for="edit_position" class="col-sm-3 control-label"><i class="fa fa-briefcase" aria-hidden="true"></i> Cargo</label>

            <div class="col-sm-9">
              <select class="form-control" name="position" id="edit_position">
                <option selected id="position_val"></option>
                <?php
                $sql = "SELECT * FROM position";
                $query = $conn->query($sql);
                while ($prow = $query->fetch_assoc()) {
                  echo "
                              <option value='" . $prow['id'] . "'>" . $prow['description'] . "</option>
                            ";
                }
                ?>
              </select>
            </div>
          </div>

          <div class="form-group">
            <label for="edit_area" class="col-sm-3 control-label"><i class="fa fa-building" aria-hidden="true"></i> Área</label>

            <div class="col-sm-9">
              <select class="form-control" name="area" id="edit_area">
                <option selected id="area_val"></option>
                <?php
                $sql = "SELECT * FROM area";
                $query = $conn->query($sql);
                while ($prow = $query->fetch_assoc()) {
                  echo "
                              <option value='" . $prow['id'] . "'>" . $prow['name_area'] . "</option>
                            ";
                }
                ?>
              </select>
            </div>
          </div>

          <div class="form-group">
            <label for="edit_career" class="col-sm-3 control-label"><i class="fa fa-graduation-cap" aria-hidden="true"></i> Carrera </label>

            <div class="col-sm-9">
              <select class="form-control" name="career" id="edit_career" required>
              <option selected id="career_val"></option>
                <?php
                $sql = "SELECT * FROM career";
                $query = $conn->query($sql);
                while ($prow = $query->fetch_assoc()) {
                  echo "
                              <option value='" . $prow['id'] . "'>" . $prow['name_career'] . "</option>
                            ";
                }
                ?>
              </select>
            </div>
          </div>

          <div class="form-group">
            <label for="edit_institution" class="col-sm-3 control-label"><i class="fa fa-university" aria-hidden="true"></i> Universidad o institución</label>

            <div class="col-sm-9">
              <select class="form-control" name="institution" id="edit_institution" required>
              <option selected id="institution_val"></option>
                <?php
                $sql = "SELECT * FROM institution";
                $query = $conn->query($sql);
                while ($prow = $query->fetch_assoc()) {
                  echo "
                              <option value='" . $prow['id'] . "'>" . $prow['name_institution'] . "</option>
                            ";
                }
                ?>
              </select>
            </div>
          </div>

          <div class="form-group">
            <label for="edit_modality" class="col-sm-3 control-label"><i class="fa fa-graduation-cap" aria-hidden="true"></i> Tipo de Modalidad</label>

            <div class="col-sm-9">
              <select class="form-control" name="modality" id="edit_modality" required>
              <option selected id="modality_val"></option>
                <?php
                $sql = "SELECT * FROM modality";
                $query = $conn->query($sql);
                while ($prow = $query->fetch_assoc()) {
                  echo "
                              <option value='" . $prow['id'] . "'>" . $prow['type_modality'] . "</option>
                            ";
                }
                ?>
              </select>
            </div>
          </div>

          <div class="form-group">
            <label for="edit_schedule" class="col-sm-3 control-label"><i class="fa fa-clock-o" aria-hidden="true"></i> Horario</label>

            <div class="col-sm-9">
              <select class="form-control" id="edit_schedule" name="schedule">
                <?php
                require_once __DIR__ . '/schedule_labels.php';
                $sql = "SELECT * FROM schedules ORDER BY time_in, time_out";
                $query = $conn->query($sql);
                while ($srow = $query->fetch_assoc()) {
                  $scheduleLabel = htmlspecialchars(getScheduleOptionLabel($srow), ENT_QUOTES, 'UTF-8');
                  echo "<option value='" . (int) $srow['id'] . "'>" . $scheduleLabel . "</option>";
                }
                ?>
              </select>
            </div>
          </div>
          <div class="form-group">
            <label for="edit_required_hours" class="col-sm-3 control-label"><i class="fa fa-shield" aria-hidden="true"></i> Horas requeridas</label>

            <div class="col-sm-9">
              <input type="number" class="form-control" id="edit_required_hours" name="required_hours" min="0" step="0.25" placeholder="600">
            </div>
          </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default btn-flat pull-left" data-dismiss="modal"><i class="fa fa-close"></i> Cerrar</button>
        <button type="submit" class="btn btn-primary btn-flat" name="edit"><i class="fa fa-save"></i> Actualizar</button>
      </div>
      </form>
    </div>
  </div>
</div>

<!-- Delete -->
<div class="modal fade" id="delete">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title"><b><span class="employee_id"></span></b></h4>
      </div>
      <div class="modal-body">
        <form class="form-horizontal" method="POST" action="employee_delete.php"><?php echo csrfField(); ?>
          <input type="hidden" class="empid" name="id">
          <div class="text-center">
            <p>ELIMINAR PASANTE</p>
            <h2 class="bold del_employee_name"></h2>
          </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default btn-flat pull-left" data-dismiss="modal"><i class="fa fa-close"></i> Cerrar</button>
        <button type="submit" class="btn btn-danger btn-flat" name="delete"><i class="fa fa-trash"></i> Eliminar</button>
        </form>
      </div>
    </div>
  </div>
</div>

<!-- Update Photo -->
<div class="modal fade" id="edit_photo">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title"><b><span class="del_employee_name"></span></b></h4>
      </div>
      <div class="modal-body">
        <form class="form-horizontal" method="POST" action="employee_edit_photo.php" enctype="multipart/form-data"><?php echo csrfField(); ?>
          <input type="hidden" class="empid" name="id">
          <div class="form-group">
            <label for="photo" class="col-sm-3 control-label">Foto</label>

            <div class="col-sm-9">
              <input type="file" id="photo" name="photo" required>
            </div>
          </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default btn-flat pull-left" data-dismiss="modal"><i class="fa fa-close"></i> Cerrar</button>
        <button type="submit" class="btn btn-success btn-flat" name="upload"><i class="fa fa-check-square-o"></i> Actualizar</button>
        </form>
      </div>
    </div>
  </div>
</div>