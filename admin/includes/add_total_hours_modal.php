<div class="modal fade" id="addTotalHoursModal" tabindex="-1" role="dialog" aria-labelledby="addTotalHoursModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="addTotalHoursModalLabel"><i class="fa fa-plus-circle text-success" aria-hidden="true"></i> Sumar horas al total</h4>
      </div>
      <form method="POST" action="add_total_hours.php">
        <?php echo csrfField(); ?>
        <div class="modal-body">
          <p>Pasante: <strong id="total-hours-employee-name"></strong></p>
          <p class="text-muted">Estas horas se agregarán directamente al total acumulado, sin crear una fecha ni un registro de asistencia.</p>
          <div class="row">
            <div class="col-xs-6 form-group">
              <label for="total-hours-value">Horas</label>
              <input type="number" class="form-control" id="total-hours-value" name="hours" min="0" max="10000" step="1" value="0" required>
            </div>
            <div class="col-xs-6 form-group">
              <label for="total-minutes-value">Minutos</label>
              <input type="number" class="form-control" id="total-minutes-value" name="minutes" min="0" max="59" step="1" value="0" required>
            </div>
          </div>
          <input type="hidden" id="total-hours-empid" name="empid">
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
          <button type="submit" class="btn btn-success"><i class="fa fa-plus" aria-hidden="true"></i> Sumar horas</button>
        </div>
      </form>
    </div>
  </div>
</div>
