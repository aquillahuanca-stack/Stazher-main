<?php
include 'includes/session.php';
include '../timezone.php';
$counts = array('pending' => 0, 'completed' => 0, 'active' => 0);
$today = date('Y-m-d');
$attendanceToday = $conn->prepare("SELECT COUNT(DISTINCT employee_id) AS total FROM attendance WHERE date = ? AND time_in IS NOT NULL AND time_in <> '00:00:00'");
$attendanceToday->bind_param('s', $today);
$attendanceToday->execute();
$activeToday = (int) ($attendanceToday->get_result()->fetch_assoc()['total'] ?? 0);
$attendanceToday->close();
$rows = $conn->query("SELECT COALESCE(a.seconds, 0) + e.accumulated_extra_seconds AS seconds, COALESCE(TIME_TO_SEC(e.add_hr), 0) AS required_seconds FROM employees e LEFT JOIN (SELECT employee_id, SUM(" . attendanceSecondsSql('') . ") AS seconds FROM attendance GROUP BY employee_id) a ON a.employee_id = e.id");
while ($row = $rows->fetch_assoc()) {
    if ($row['required_seconds'] > 0 && $row['seconds'] >= $row['required_seconds']) { $counts['completed']++; }
    elseif ($row['seconds'] > 0) { $counts['active']++; }
    else { $counts['pending']++; }
}
$total = array_sum($counts);
?>
<?php include 'includes/header.php'; ?>
<body class="hold-transition skin-blue sidebar-mini dashboard-reference postal-dashboard">
<div class="wrapper">
<?php include 'includes/navbar.php'; include 'includes/menubar.php'; ?>
<div class="content-wrapper">
<section class="content-header"><h1>Panel de Control</h1><ol class="breadcrumb"><li><i class="fa fa-home"></i> Inicio</li><li class="active">Panel de Control</li></ol></section>
<section class="content">
<?php foreach (array('success' => 'success', 'error' => 'danger') as $key => $color): if (isset($_SESSION[$key])): ?>
<div class="alert alert-<?php echo $color; ?>"><?php echo htmlspecialchars($_SESSION[$key], ENT_QUOTES, 'UTF-8'); unset($_SESSION[$key]); ?></div>
<?php endif; endforeach; ?>
<div class="postal-dashboard-grid">
  <section class="dashboard-welcome" aria-label="Bienvenida">
    <div class="welcome-copy"><span>PLATAFORMA DE GESTI&Oacute;N DE PASANT&Iacute;AS</span><h2>Recursos Humanos</h2><p>Gestión de Pasantes</p></div>
    <div class="welcome-visual" aria-hidden="true">
      <svg viewBox="0 0 390 190" focusable="false">
        <path class="visual-network" d="M8 48h24V24h34M5 151h33v18h38m246-130h31v28h26m-61 96h38v-24h28"/>
        <circle class="visual-node" cx="66" cy="24" r="3"/><circle class="visual-node" cx="76" cy="169" r="3"/><circle class="visual-node" cx="377" cy="50" r="3"/><circle class="visual-node" cx="380" cy="139" r="3"/>
        <rect class="visual-card" x="34" y="24" width="228" height="143" rx="12"/>
        <rect class="visual-avatar-bg" x="49" y="42" width="70" height="84" rx="8"/><circle class="visual-avatar" cx="84" cy="69" r="15"/><path class="visual-avatar" d="M59 111c2-17 12-25 25-25s23 8 25 25v5H59z"/>
        <path class="visual-line strong" d="M134 51h75"/><path class="visual-line" d="M134 69h91M134 86h63M134 105h76"/><rect class="visual-action" x="134" y="124" width="91" height="8" rx="4"/>
        <rect class="visual-calendar" x="286" y="61" width="82" height="85" rx="10"/><path class="visual-calendar-top" d="M286 80h82"/><path class="visual-calendar-ring" d="M306 54v15m42-15v15"/><g class="visual-calendar-days"><circle cx="306" cy="96" r="3"/><circle cx="327" cy="96" r="3"/><circle cx="348" cy="96" r="3"/><circle cx="306" cy="116" r="3"/><circle cx="327" cy="116" r="3"/><circle cx="348" cy="116" r="3"/></g>
      </svg>
    </div>
  </section>
  <div class="dashboard-stats">
    <a href="employee.php?active=1" class="dashboard-stat stat-in-progress"><span class="stat-icon"><i class="fa fa-users" aria-hidden="true"></i></span><div class="stat-copy"><h2>Pasantes en curso</h2><strong><?php echo $activeToday; ?></strong><p>Marcaron su entrada hoy</p></div><span class="stat-illustration" aria-hidden="true"><svg viewBox="0 0 100 92"><path class="cap" d="M9 28 49 8l42 17-41 21L9 28Z"/><path class="cap-band" d="m23 36 27 13 28-14v16c-15 13-40 13-55 0V36Z"/><path class="cap-tassel" d="m87 28 3 26"/><circle class="mini-person" cx="28" cy="72" r="8"/><circle class="mini-person" cx="50" cy="68" r="9"/><circle class="mini-person" cx="73" cy="72" r="8"/><path class="mini-body" d="M14 89c1-10 6-14 14-14s13 4 14 14m-5 0c1-12 6-17 13-17s13 5 14 17m-5 0c1-10 6-14 14-14s13 4 14 14"/></svg></span></a>
    <a href="employee.php" class="dashboard-stat stat-total"><span class="stat-icon violet"><i class="fa fa-users" aria-hidden="true"></i></span><div class="stat-copy"><h2>Total de pasantes</h2><strong><?php echo $total; ?></strong><p>Registrados en el sistema</p></div><span class="stat-illustration" aria-hidden="true"><svg viewBox="0 0 100 92"><rect class="doc" x="22" y="8" width="56" height="72" rx="6"/><path class="doc-lines" d="M34 25h31M34 37h31M34 49h25M34 61h21"/><circle class="check-disc" cx="72" cy="70" r="17"/><path class="check-mark" d="m64 70 6 6 11-13"/></svg></span></a>
  </div>
</div>
</section></div>
<?php include 'includes/footer.php'; ?>
</div>
<?php include 'includes/scripts.php'; ?>
</body></html>



