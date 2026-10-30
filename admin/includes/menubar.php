<?php
$menuPage = basename($_SERVER['PHP_SELF']);
$menuEmployee = in_array($menuPage, array('employee.php', 'employee_detail.php'));
$menuStudy = in_array($menuPage, array('institution.php', 'career.php', 'modality.php'));
$menuConfiguration = $menuStudy || in_array($menuPage, array('schedule.php', 'position.php', 'area.php'));
?>
<aside class="main-sidebar postal-sidebar">
  <section class="sidebar">
    <a href="home.php" class="postal-brand"><img class="postal-brand-image" src="../images/logo%20GS%20fondo%20transparente.png" alt="Gestión de Pasantes" width="598" height="160"></a>
    <ul class="sidebar-menu" data-widget="tree" data-animation-speed="120" aria-label="MenÃº principal">
      <li class="<?php echo $menuPage === 'home.php' ? 'active' : ''; ?>"><a href="home.php"><i class="fa fa-home" aria-hidden="true"></i> <span>Panel de Control</span></a></li>
      <li class="<?php echo $menuEmployee ? 'active' : ''; ?>">
        <a href="employee.php"><i class="fa fa-users" aria-hidden="true"></i> <span>Lista de pasantes</span></a>
      </li>
      <li class="<?php echo $menuPage === 'attendance.php' ? 'active' : ''; ?>"><a href="attendance.php"><i class="fa fa-calendar-check-o" aria-hidden="true"></i> <span>Asistencia</span></a></li>
      <li class="treeview <?php echo $menuConfiguration ? 'active menu-open' : ''; ?>">
        <a href="#"><i class="fa fa-cog" aria-hidden="true"></i> <span>ConfiguraciÃ³n</span><span class="pull-right-container"><i class="fa fa-angle-left pull-right"></i></span></a>
        <ul class="treeview-menu">
          <li class="<?php echo $menuPage === 'schedule.php' ? 'active' : ''; ?>"><a href="schedule.php"><i class="fa fa-clock-o" aria-hidden="true"></i> Horarios</a></li>
          <li class="<?php echo $menuPage === 'position.php' ? 'active' : ''; ?>"><a href="position.php"><i class="fa fa-briefcase" aria-hidden="true"></i> Ãreas de trabajo</a></li>
          <li class="<?php echo $menuPage === 'area.php' ? 'active' : ''; ?>"><a href="area.php"><i class="fa fa-sitemap" aria-hidden="true"></i> Unidades organizacionales</a></li>
          <li class="treeview <?php echo $menuStudy ? 'active menu-open' : ''; ?>">
            <a href="#"><i class="fa fa-graduation-cap" aria-hidden="true"></i> <span>Casas Superiores de Estudio</span><span class="pull-right-container"><i class="fa fa-angle-left pull-right"></i></span></a>
            <ul class="treeview-menu">
              <li class="<?php echo $menuPage === 'institution.php' ? 'active' : ''; ?>"><a href="institution.php"><i class="fa fa-university" aria-hidden="true"></i> Universidades o institutos</a></li>
              <li class="<?php echo $menuPage === 'career.php' ? 'active' : ''; ?>"><a href="career.php"><i class="fa fa-book" aria-hidden="true"></i> Carreras</a></li>
              <li class="<?php echo $menuPage === 'modality.php' ? 'active' : ''; ?>"><a href="modality.php"><i class="fa fa-certificate" aria-hidden="true"></i> Modalidad de titulaciÃ³n</a></li>
            </ul>
          </li>
        </ul>
      </li>
    </ul>
  </section>
</aside>


