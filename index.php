<?php session_start(); ?>
<?php include 'header.php'; ?>

<body class="hold-transition login-page">
  <title>Gestión de Pasantes</title>
  <link rel="stylesheet" href="attendance-screen.css?v=<?php echo filemtime(__DIR__ . '/attendance-screen.css'); ?>">
  <main class="attendance-stage">
    <aside class="postal-identity" aria-label="Correos de Bolivia">
      <div class="postal-identity-shade"></div>
      <svg class="postal-ribbons" viewBox="0 0 400 220" preserveAspectRatio="none" aria-hidden="true" focusable="false">
        <path d="M0 0 C120 55 185 152 400 194 L400 220 C170 190 70 85 0 55Z" fill="#db242c"/>
        <path d="M0 35 C120 90 215 177 400 198 L400 220 C170 202 70 124 0 91Z" fill="#ffce32"/>
        <path d="M0 76 C115 127 235 193 400 204 L400 220 C180 214 65 160 0 135Z" fill="#07854b"/>
      </svg>
    </aside>
    <svg class="postal-ribbons postal-ribbons-right" viewBox="0 0 400 220" preserveAspectRatio="none" aria-hidden="true" focusable="false">
      <path d="M0 0 C120 55 185 152 400 194 L400 220 C170 190 70 85 0 55Z" fill="#db242c"/>
      <path d="M0 35 C120 90 215 177 400 198 L400 220 C170 202 70 124 0 91Z" fill="#ffce32"/>
      <path d="M0 76 C115 127 235 193 400 204 L400 220 C180 214 65 160 0 135Z" fill="#07854b"/>
    </svg>
  <div class="login-box">
    
    <div class="login-box-body">
            <div class="attendance-brand"><img src="images/Logo.jpeg" alt="Correos de Bolivia" width="1099" height="359"></div>
      <h1 class="login-box-msg">GESTIÓN DE PASANTES</h1>
      <div class="attendance-clock"><p id="date"></p><p id="time"></p></div>
      <form id="attendance-direct">
        <input type="hidden" id="attendance-status" name="status" value="">
        <div class="form-group has-feedback attendance-code"><label for="employee">Código del pasante</label><input type="text" class="form-control" id="employee" name="employee" placeholder="Ingrese su código" autocomplete="off" required><span class="fa fa-id-card-o form-control-feedback"></span></div>
        <div class="row attendance-actions"><div class="col-xs-6 action-column"><button type="submit" class="btn btn-success btn-block btn-flat attendance-action" data-status="in"><i class="fa fa-sign-in"></i>REGISTRAR ENTRADA</button></div><div class="col-xs-6 action-column"><button type="submit" class="btn btn-danger btn-block btn-flat attendance-action" data-status="out"><i class="fa fa-sign-out"></i>REGISTRAR SALIDA</button></div></div>
      </form>
    </div>
    <div class="alert alert-success alert-dismissible mt20 text-center" style="display:none;"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button><span class="result"><i class="icon fa fa-check"></i> <span class="message"></span></span></div>
    <div class="alert alert-danger alert-dismissible mt20 text-center" style="display:none;"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button><span class="result"><i class="icon fa fa-warning"></i> <span class="message"></span></span></div>
  </div>
  </main>
  <?php include 'scripts.php' ?>
  <script type="text/javascript">
    $(function() {
      $('.attendance-code label').text('C\u00f3digo del pasante');
      $('#employee').attr('placeholder', 'Ingrese su c\u00f3digo');

      var dateFormat = new Intl.DateTimeFormat('es-BO', {
        timeZone: 'America/La_Paz', weekday: 'long', day: 'numeric', month: 'long', year: 'numeric'
      });
      var timeFormat = new Intl.DateTimeFormat('es-BO', {
        timeZone: 'America/La_Paz', hour: '2-digit', minute: '2-digit', second: '2-digit', hourCycle: 'h23'
      });
      function updateClock() {
        var now = new Date();
        $('#date').text(dateFormat.format(now));
        $('#time').text(timeFormat.format(now));
      }
      updateClock();
      setInterval(updateClock, 1000);

      $('.attendance-action').click(function() {
        $('#attendance-status').val($(this).data('status'));
      });

      $('#attendance-direct').submit(function(e) {
        e.preventDefault();
        if (!$('#attendance-status').val()) {
          return;
        }
        var attendance = $(this).serialize();
        var shouldReload = false;
        $('.attendance-action').prop('disabled', true);
        $.ajax({
          type: 'POST',
          url: 'attendance.php',
          data: attendance,
          dataType: 'json',
          complete: function() {
            if (!shouldReload) {
              $('.attendance-action').prop('disabled', false);
            }
          },
          error: function() {
            $('.alert').hide();
            $('.alert-danger').show().find('.message').text('No se pudo confirmar la marcación. Revise la conexión e intente nuevamente.');
          },
          success: function(response) {
            if (response.error) {
              $('.alert').hide();
              $('.alert-danger').show();
              $('.message').text(response.message);
            } else {
              $('.alert').hide();
              $('.alert-success').show();
              $('.message').text(response.message);
              $('#employee').val('');
              $('#attendance-status').val('');
              // Conserva el mensaje de confirmación y después deja la pantalla lista para la siguiente marcación.
              shouldReload = true;
              $('.attendance-action').prop('disabled', true);
              window.setTimeout(function() {
                window.location.reload();
              }, 1500);
            }
          }
        });
      });

    });
  </script>
<footer class="main-footer">
  <div class="pull-right hidden-xs">
    <b>Hecho por: 
      <a href="https://www.linkedin.com/in/marco-antonio-espinoza-rojas-617539256/">Marco Espinoza</a>, 
      <a href="#">Alexandra Quintanilla</a> y 
      <a href="#">Leonardo Doria Medina</a> By 
      <a href="https://www.correos.gob.bo">Agencia Boliviana de Correos</a>
    </b>
  </div>
  <strong>&copy; <?php echo date('Y'); ?> Gestión de Pasantes</strong>
</footer>
</body>
</html>
