<!-- jQuery 3 -->
<script>
  // Usar la altura visible real también al cambiar el zoom o tamaño de ventana.
  (function () {
    function updateAdminViewport() {
      document.documentElement.style.setProperty('--admin-viewport-height', window.innerHeight + 'px');
    }
    updateAdminViewport();
    window.addEventListener('resize', updateAdminViewport);
    window.addEventListener('pageshow', updateAdminViewport);
    if (window.visualViewport) {
      window.visualViewport.addEventListener('resize', updateAdminViewport);
    }
  }());
</script>
<script src="../bower_components/jquery/dist/jquery.min.js"></script>
<!-- jQuery UI 1.11.4 -->
<script src="../bower_components/jquery-ui/jquery-ui.min.js"></script>
<!-- DataTables -->
<script src="../bower_components/datatables.net/js/jquery.dataTables.min.js"></script>
<script src="../bower_components/datatables.net-bs/js/dataTables.bootstrap.min.js"></script>
<!-- Resolve conflict in jQuery UI tooltip with Bootstrap tooltip -->
<script>
  $.widget.bridge('uibutton', $.ui.button);
</script>
<!-- Bootstrap 3.3.7 -->
<script src="../bower_components/bootstrap/dist/js/bootstrap.min.js"></script>
<!-- Morris.js charts -->
<script src="../bower_components/raphael/raphael.min.js"></script>
<script src="../bower_components/morris.js/morris.min.js"></script>
<!-- ChartJS -->
<script src="../bower_components/chart.js/Chart.js"></script>
<!-- Sparkline -->
<script src="../bower_components/jquery-sparkline/dist/jquery.sparkline.min.js"></script>
<!-- jvectormap -->
<script src="../plugins/jvectormap/jquery-jvectormap-1.2.2.min.js"></script>
<script src="../plugins/jvectormap/jquery-jvectormap-world-mill-en.js"></script>
<!-- jQuery Knob Chart -->
<script src="../bower_components/jquery-knob/dist/jquery.knob.min.js"></script>
<!-- daterangepicker -->
<script src="../bower_components/moment/min/moment.min.js"></script>
<script src="../bower_components/bootstrap-daterangepicker/daterangepicker.js"></script>
<!-- datepicker -->
<script src="../bower_components/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js"></script>
<!-- bootstrap time picker -->
<script src="../plugins/timepicker/bootstrap-timepicker.min.js"></script>
<!-- Bootstrap WYSIHTML5 -->
<script src="../plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.all.min.js"></script>
<!-- Slimscroll -->
<script src="../bower_components/jquery-slimscroll/jquery.slimscroll.min.js"></script>
<!-- FastClick -->
<script src="../bower_components/fastclick/lib/fastclick.js"></script>
<!-- AdminLTE App -->
<script src="../dist/js/adminlte.min.js"></script>
<!-- AdminLTE dashboard demo (This is only for demo purposes) -->
<script src="../dist/js/pages/dashboard.js"></script>
<!-- AdminLTE for demo purposes -->
<script src="../dist/js/demo.js"></script>
<script>
  $(function () {
    var sharedDataTableLanguage = {
      search: 'Buscar:',
      lengthMenu: 'Mostrar _MENU_ registros',
      info: 'Mostrando _START_ a _END_ de _TOTAL_ registros',
      infoEmpty: 'No hay registros para mostrar',
      infoFiltered: '(filtrado de _MAX_ registros)',
      zeroRecords: 'No se encontraron resultados.',
      emptyTable: 'No hay registros disponibles.',
      paginate: { first: 'Primera', previous: 'Anterior', next: 'Siguiente', last: 'Última' },
      aria: { paginate: { first: 'Primera página', previous: 'Página anterior', next: 'Página siguiente', last: 'Última página' } }
    };
    if ($('#example1').length && !$('#example1').hasClass('server-side-table')) $('#example1').DataTable({
      responsive: true,
      pageLength: $('#example1').hasClass('employee-list-table') ? 15 : 10,
      lengthMenu: [[10, 15, 25, 50], [10, 15, 25, 50]],
      pagingType: 'full_numbers',
      lengthChange: !$('#example1').is('.areas-list-table, .catalog-list-table'),
      searching: !$('#example1').is('.areas-list-table, .catalog-list-table'),
      autoWidth: !$('#example1').hasClass('attendance-list-table'),
      paging: true,
      order: $('#example1').hasClass('attendance-list-table') ? [[1, 'desc'], [0, 'asc']] : ($('#example1').hasClass('employee-list-table') ? [[1, 'asc']] : [[0, 'asc']]),
      dom: $('#example1').is('.employee-list-table, .attendance-list-table') ? 'rt<"row"<"col-sm-5"i><"col-sm-7"p>>' : 'lfrtip',
      language: $('#example1').hasClass('employee-list-table') ? {
        search: 'Buscar en la lista:',
        searchPlaceholder: 'Nombre o carnet…',
        lengthMenu: 'Mostrar _MENU_ pasantes',
        info: '_START_–_END_ de _TOTAL_ pasantes',
        infoEmpty: '0 pasantes',
        infoFiltered: '(de _MAX_ registrados)',
        zeroRecords: 'No se encontraron pasantes con esa búsqueda.',
        emptyTable: 'Todavía no hay pasantes registrados.',
        paginate: { first: 'Primera', previous: 'Anterior', next: 'Siguiente', last: 'Última' },
        aria: { sortAscending: ': ordenar de forma ascendente', sortDescending: ': ordenar de forma descendente' }
      } : sharedDataTableLanguage,
      columnDefs: $('#example1').hasClass('employee-list-table') ? [{ targets: -1, orderable: false, searchable: false }] : []
    })
    $('#example2').DataTable({
      'paging'      : true,
      'pagingType'  : 'full_numbers',
      'pageLength'  : 10,
      'lengthChange': false,
      'searching'   : false,
      'ordering'    : true,
      'info'        : true,
      'autoWidth'   : false,
      'language'    : sharedDataTableLanguage
    })
  })
</script>
<script>
$(function(){
  /** add active class and stay opened when selected */
  var url = window.location;

  // for sidebar menu entirely but not cover treeview
  $('ul.sidebar-menu a').filter(function() {
     return this.href == url;
  }).parent().addClass('active');

  // for treeview
  $('ul.treeview-menu a').filter(function() {
     return this.href == url;
  }).parentsUntil(".sidebar-menu > .treeview-menu").addClass('active');
  
});
</script>
<script>
$(function(){
	//Date picker
  $('#datepicker_add').datepicker({
    autoclose: true,
    format: 'yyyy-mm-dd'
  })
  $('#datepicker_edit').datepicker({
    autoclose: true,
    format: 'yyyy-mm-dd'
  })

  //Timepicker
  $('.timepicker').timepicker({
    showInputs: false,
    showMeridian: false,
    minuteStep: 1
  })

  //Date range picker
  $('#reservation').daterangepicker()
  //Date range picker with time picker
  $('#reservationtime').daterangepicker({ timePicker: true, timePickerIncrement: 30, format: 'MM/DD/YYYY h:mm A' })
  //Date range as a button
  $('#daterange-btn').daterangepicker(
    {
      ranges   : {
        'Today'       : [moment(), moment()],
        'Yesterday'   : [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
        'Last 7 Days' : [moment().subtract(6, 'days'), moment()],
        'Last 30 Days': [moment().subtract(29, 'days'), moment()],
        'This Month'  : [moment().startOf('month'), moment().endOf('month')],
        'Last Month'  : [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
      },
      startDate: moment().subtract(29, 'days'),
      endDate  : moment()
    },
    function (start, end) {
      $('#daterange-btn span').html(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'))
    }
  )
  
});
</script>
