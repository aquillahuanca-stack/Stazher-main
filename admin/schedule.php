<?php include 'includes/session.php'; ?>
<?php include 'includes/header.php'; ?>
<body class="hold-transition skin-blue sidebar-mini schedule-page">
<style>
  /* Horarios: el footer cierra el contenedor, sin filas ni espacio posteriores. */
  html:has(body.schedule-page) { height: auto; min-height: 100%; }
  body.schedule-page { height: auto; min-height: 100vh; min-height: 100dvh; margin: 0; padding: 0; }
  body.schedule-page > .wrapper {
    display: flex;
    flex-direction: column;
    min-height: 100vh;
    min-height: 100dvh;
    height: auto;
    margin: 0;
    padding: 0 0 var(--schedule-footer-height, 44px);
    overflow: visible;
  }
  body.schedule-page > .wrapper > .main-header { flex: 0 0 auto; }
  body.schedule-page > .wrapper > .content-wrapper { flex: 1 0 auto; min-height: 0 !important; }
  body.schedule-page > .wrapper > .main-footer { position: fixed; bottom: 0; left: 0; right: 0; z-index: 820; margin-top: 0; margin-bottom: 0; padding-top: 6px; padding-bottom: 6px; line-height: 1.3; row-gap: 3px; }
  body.schedule-page > .wrapper > .postal-sidebar { position: fixed; bottom: 0; height: auto !important; overflow-y: auto; }
</style>
<div class="wrapper">

  <?php include 'includes/navbar.php'; ?>
  <?php include 'includes/menubar.php'; ?>

  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
        Horarios
      </h1>
      <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Inicio</a></li>
        <li>Pasante</li>
        <li class="active">Horarios</li>
      </ol>
    </section>
    <!-- Main content -->
    <section class="content">
      <?php
        if(isset($_SESSION['error'])){
          echo "
            <div class='alert alert-danger alert-dismissible'>
              <button type='button' class='close' data-dismiss='alert' aria-hidden='true'>&times;</button>
              <h4><i class='icon fa fa-warning'></i> Error!</h4>
              ".$_SESSION['error']."
            </div>
          ";
          unset($_SESSION['error']);
        }
        if(isset($_SESSION['success'])){
          echo "
            <div class='alert alert-success alert-dismissible'>
              <button type='button' class='close' data-dismiss='alert' aria-hidden='true'>&times;</button>
              <h4><i class='icon fa fa-check'></i>¡Proceso Exitoso!</h4>
              ".$_SESSION['success']."
            </div>
          ";
          unset($_SESSION['success']);
        }
      ?>
      <div class="row">
        <div class="col-xs-12">
          <div class="box">
            <div class="box-header with-border">
              <a href="#addnew" data-toggle="modal" class="btn btn-primary btn-sm btn-flat"><i class="fa fa-plus"></i> Nuevo</a>
            </div>
            <div class="box-body">
              <table id="example1" class="table table-bordered schedule-list-table">
                <thead>
                  <th>Tiempo Entrada</th>
                  <th>Tiempo Salida</th>
                  <th>Acción</th>
                </thead>
                <tbody>
                  <?php
                    $sql = "SELECT * FROM schedules";
                    $query = $conn->query($sql);
                    while($row = $query->fetch_assoc()){
                      echo "
                        <tr>
                          <td>".date('h:i A', strtotime($row['time_in']))."</td>
                          <td>".date('h:i A', strtotime($row['time_out']))."</td>
                          <td>
                            <button class='btn btn-success btn-sm edit btn-flat' data-id='".$row['id']."'><i class='fa fa-edit'></i> Editar</button>
                            <button class='btn btn-danger btn-sm delete btn-flat' data-id='".$row['id']."'><i class='fa fa-trash'></i> Eliminar</button>
                          </td>
                        </tr>
                      ";
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
</div>
<?php include 'includes/schedule_modal.php'; ?>
<?php include 'includes/scripts.php'; ?>
<script>
$(function(){
  var scheduleFooter = document.querySelector('.schedule-page > .wrapper > .main-footer');
  function reserveFooterSpace() {
    document.body.style.setProperty('--schedule-footer-height', scheduleFooter.offsetHeight + 'px');
  }
  reserveFooterSpace();
  if (window.ResizeObserver) {
    new ResizeObserver(reserveFooterSpace).observe(scheduleFooter);
  } else {
    window.addEventListener('resize', reserveFooterSpace);
  }
  $('.edit').click(function(e){
    e.preventDefault();
    $('#edit').modal('show');
    var id = $(this).data('id');
    getRow(id);
  });

  $('.delete').click(function(e){
    e.preventDefault();
    $('#delete').modal('show');
    var id = $(this).data('id');
    getRow(id);
  });
});

function getRow(id){
  $.ajax({
    type: 'POST',
    url: 'schedule_row.php',
    data: {id:id},
    dataType: 'json',
    success: function(response){
      $('#timeid').val(response.id);
      $('#edit_time_in').val(response.time_in);
      $('#edit_time_out').val(response.time_out);
      $('#del_timeid').val(response.id);
      $('#del_schedule').html(response.time_in+' - '+response.time_out);
    }
  });
}
</script>
</body>
</html>
