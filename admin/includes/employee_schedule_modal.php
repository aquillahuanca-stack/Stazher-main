<!-- Edit -->
<div class="modal fade" id="edit">
    <div class="modal-dialog">
        <div class="modal-content">
          	<div class="modal-header">
            	<button type="button" class="close" data-dismiss="modal" aria-label="Close">
              		<span aria-hidden="true">&times;</span></button>
            	<h4 class="modal-title"><b><span class="employee_name"></span></b></h4>
          	</div>
          	<div class="modal-body">
            	<form class="form-horizontal" method="POST" action="schedule_employee_edit.php">
            		<input type="hidden" id="empid" name="id">
                <div class="form-group">
                    <label for="edit_schedule" class="col-sm-3 control-label">Horario</label>

                    <div class="col-sm-9">
                      <select class="form-control" id="edit_schedule" name="schedule">
                        <option selected id="schedule_val"></option>
                        <?php
                          require_once __DIR__ . '/schedule_labels.php';
                          $sql = "SELECT * FROM schedules ORDER BY time_in, time_out";
                          $query = $conn->query($sql);
                          while($srow = $query->fetch_assoc()){
                            $scheduleLabel = htmlspecialchars(getScheduleOptionLabel($srow), ENT_QUOTES, 'UTF-8');
                            echo "<option value='" . (int) $srow['id'] . "'>" . $scheduleLabel . "</option>";
                          }
                        ?>
                      </select>
                    </div>
                </div>
          	</div>
          	<div class="modal-footer">
            	<button type="button" class="btn btn-default btn-flat pull-left" data-dismiss="modal"><i class="fa fa-close"></i> Cerrar</button>
            	<button type="submit" class="btn btn-success btn-flat" name="edit"><i class="fa fa-check-square-o"></i> Actualizar</button>
            	</form>
          	</div>
        </div>
    </div>
</div>