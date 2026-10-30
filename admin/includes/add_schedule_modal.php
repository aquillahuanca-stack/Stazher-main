<div class="modal fade hours-adjustment-modal" id="addScheduleModal" tabindex="-1" role="dialog" aria-labelledby="addScheduleModalLabel" aria-describedby="hours-adjustment-description" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <span class="hours-adjustment-icon" aria-hidden="true"><i class="fa fa-clock-o"></i></span>
        <div class="hours-adjustment-heading">
          <span class="hours-adjustment-eyebrow">CONTROL DE ASISTENCIA</span>
          <h2 class="modal-title" id="addScheduleModalLabel">Agregar horario</h2>
        </div>
        <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar ventana"><span aria-hidden="true">&times;</span></button>
      </div>
      <form id="addScheduleForm" method="POST" action="add_schedule.php"><?php echo csrfField(); ?>
        <div class="modal-body">
          <p id="hours-adjustment-description">Registre la asistencia. La duración se calcula automáticamente.</p>
          <div class="form-group">
            <label for="scheduleDate">Fecha de asistencia <span class="hours-required">Obligatorio</span></label>
            <input type="date" class="form-control schedule-entry-control" id="scheduleDate" name="date" min="1000-01-01" max="9999-12-31" required>
          </div>
          <div class="schedule-time-grid">
            <div class="form-group">
              <label for="timeIn"><span><i class="fa fa-sign-in" aria-hidden="true"></i> Hora de entrada</span></label>
              <input type="time" class="form-control schedule-entry-control" id="timeIn" name="time_in" step="60" aria-describedby="hours-format-help" required>
            </div>
            <div class="form-group">
              <label for="timeOut"><span><i class="fa fa-sign-out" aria-hidden="true"></i> Hora de salida</span></label>
              <input type="time" class="form-control schedule-entry-control" id="timeOut" name="time_out" step="60" aria-describedby="hours-format-help schedule-entry-error" required>
            </div>
          </div>
          <p class="help-block" id="hours-format-help">Campos obligatorios. Entrada y salida del mismo día.</p>
          <p id="schedule-entry-error" class="schedule-entry-error" role="status" aria-live="polite"></p>
          <div class="hours-adjustment-note">
            <i class="fa fa-clock-o" aria-hidden="true"></i>
            <div><span class="schedule-duration-label">DURACIÓN DEL REGISTRO</span><p id="hours-adjustment-preview" aria-live="polite">Complete las horas de entrada y salida.</p></div>
          </div>
          <input type="hidden" id="empid" name="empid">
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
          <button type="submit" class="btn btn-primary"><i class="fa fa-check" aria-hidden="true"></i> Guardar horario</button>
        </div>
      </form>
    </div>
  </div>
</div>
<script>
  (function () {
    var input = document.getElementById('timeIn');
    var output = document.getElementById('timeOut');
    var error = document.getElementById('schedule-entry-error');
    var preview = document.getElementById('hours-adjustment-preview');
    function validateHours() {
      output.setCustomValidity('');
      output.setAttribute('aria-invalid', 'false');
      error.textContent = '';
      preview.textContent = 'Complete las horas de entrada y salida.';
      if (!input.value || !output.value) return;
      function minutes(value) {
        var parts = value.split(':');
        return Number(parts[0]) * 60 + Number(parts[1]);
      }
      var duration = minutes(output.value) - minutes(input.value);
      if (duration <= 0) {
        var message = 'La salida debe ser posterior a la entrada del mismo día.';
        output.setCustomValidity(message);
        output.setAttribute('aria-invalid', 'true');
        error.textContent = message;
        preview.textContent = 'Revise el horario ingresado.';
        return;
      }
      preview.textContent = Math.floor(duration / 60) + ' h ' + String(duration % 60).padStart(2, '0') + ' min · Se sumarán al acumulado.';
    }
    input.addEventListener('input', validateHours);
    output.addEventListener('input', validateHours);
    validateHours();
  })();
</script>
