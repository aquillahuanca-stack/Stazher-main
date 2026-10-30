<?php
require_once __DIR__ . '/notifications.php';
$adminNotifications = array();
$unreadNotifications = 0;
try {
  $unreadResult = $conn->query('SELECT COUNT(*) AS total FROM stazher_notifications WHERE read_at IS NULL');
  $unreadNotifications = (int) $unreadResult->fetch_assoc()['total'];
  $notificationResult = $conn->query('SELECT id, type, title, message, employee_id, created_at FROM stazher_notifications ORDER BY created_at DESC, id DESC LIMIT 8');
  $adminNotifications = $notificationResult->fetch_all(MYSQLI_ASSOC);
} catch (Throwable $notificationError) {
  $adminNotifications = array();
  $unreadNotifications = 0;
}
?>
<header class="main-header">
    <!-- Logo -->
    <a href="home.php" class="logo">
      <!-- mini logo for sidebar mini 50x50 pixels -->
      <span class="logo-mini"><b>G</b>P</span>
      <!-- logo for regular state and mobile devices -->
      <span class="logo-lg"><img src="../images/logo%20GS%20fondo%20transparente.png" alt="Gestión de Pasantes"></span>
    </a>
    <!-- Header Navbar: style can be found in header.less -->
    <nav class="navbar navbar-static-top">
      <ul class="nav navbar-nav postal-top-links">
        <li class="<?php echo basename($_SERVER['PHP_SELF']) === 'home.php' ? 'active' : ''; ?>"><a href="home.php"><i class="fa fa-home"></i><span>Inicio</span></a></li>
        <li class="<?php echo in_array(basename($_SERVER['PHP_SELF']), array('employee.php', 'employee_detail.php', 'hours.php')) ? 'active' : ''; ?>"><a href="employee.php"><i class="fa fa-users"></i><span>Pasantes</span></a></li>
        <li class="<?php echo basename($_SERVER['PHP_SELF']) === 'attendance.php' ? 'active' : ''; ?>"><a href="attendance.php"><i class="fa fa-calendar-check-o"></i><span>Asistencia</span></a></li>
        <li class="dropdown reports-dropdown"><a href="#" class="dropdown-toggle" data-toggle="dropdown"><i class="fa fa-bar-chart"></i><span>Reportes</span><i class="fa fa-angle-down nav-caret"></i></a><ul class="dropdown-menu"><li><a href="employee_print.php" target="_blank" rel="noopener">Lista de pasantes</a></li><li><a href="attendance_print.php" target="_blank" rel="noopener">Reporte de asistencia</a></li></ul></li>
        <li class="dropdown"><a href="#" class="dropdown-toggle" data-toggle="dropdown"><i class="fa fa-cog"></i><span>Configuración</span><i class="fa fa-angle-down nav-caret"></i></a><ul class="dropdown-menu"><li><a href="schedule.php">Horarios</a></li><li><a href="position.php">Áreas de trabajo</a></li><li><a href="area.php">Unidades organizacionales</a></li><li><a href="institution.php">Instituciones</a></li><li><a href="career.php">Carreras</a></li><li><a href="modality.php">Modalidades</a></li></ul></li>
      </ul>
      <div class="navbar-custom-menu">
        <ul class="nav navbar-nav">
          <li class="notifications-menu">
            <a href="#" class="notification-trigger" title="Notificaciones" aria-label="Notificaciones" data-toggle="dropdown" data-csrf="<?php echo htmlspecialchars($_SESSION['csrf_token'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
              <i class="fa fa-bell notification-bell-icon" aria-hidden="true"></i>
              <?php if ($unreadNotifications > 0): ?><span class="notification-count"><?php echo $unreadNotifications > 99 ? '99+' : $unreadNotifications; ?></span><?php endif; ?>
            </a>
            <ul class="dropdown-menu notification-dropdown">
              <li class="notification-heading"><span class="notification-heading-copy"><span>Notificaciones</span><small><?php echo $unreadNotifications; ?> nuevas</small></span><button type="button" class="notification-clear" title="Limpiar todas las notificaciones"><i class="fa fa-trash-o" aria-hidden="true"></i><span>Limpiar</span></button></li>
              <li class="notification-list" data-csrf="<?php echo htmlspecialchars($_SESSION['csrf_token'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                <ul>
                  <?php if (!$adminNotifications): ?>
                    <li class="notification-empty"><i class="fa fa-check-circle" aria-hidden="true"></i> No hay notificaciones por el momento.</li>
                  <?php else: foreach ($adminNotifications as $notification): ?>
                    <li class="notification-item <?php echo empty($notification['employee_id']) ? '' : 'has-employee'; ?>">
                      <?php if (!empty($notification['employee_id'])): ?><a class="notification-item-link" href="employee_detail.php?id=<?php echo (int) $notification['employee_id']; ?>"><?php endif; ?>
                      <span class="notification-item-icon <?php echo $notification['type'] === 'completion' ? 'completion' : 'change'; ?>"><i class="fa <?php echo $notification['type'] === 'completion' ? 'fa-check' : 'fa-refresh'; ?>" aria-hidden="true"></i></span>
                      <span class="notification-item-copy"><strong><?php echo htmlspecialchars($notification['title'], ENT_QUOTES, 'UTF-8'); ?></strong><span><?php echo htmlspecialchars($notification['message'], ENT_QUOTES, 'UTF-8'); ?></span><small><?php echo date('d/m/Y H:i', strtotime($notification['created_at'])); ?></small></span>
                      <?php if (!empty($notification['employee_id'])): ?></a><?php endif; ?>
                      <span class="notification-item-actions">
                        <button type="button" class="notification-item-more" aria-label="Opciones de notificación" aria-haspopup="true" aria-expanded="false"><i class="fa fa-ellipsis-v" aria-hidden="true"></i></button>
                        <span class="notification-item-actions-menu"><button type="button" class="notification-item-delete" data-notification-id="<?php echo (int) $notification['id']; ?>"><i class="fa fa-trash-o" aria-hidden="true"></i> Eliminar notificación</button></span>
                      </span>
                    </li>
                  <?php endforeach; endif; ?>
                </ul>
              </li>
            </ul>
          </li>
          <!-- User Account: style can be found in dropdown.less -->
          <li class="dropdown user user-menu">
            <a href="#" class="dropdown-toggle" data-toggle="dropdown">
              <?php if (!empty($user['photo']) && file_exists('../images/'.$user['photo'])): ?>
                <img src="<?php echo '../images/'.$user['photo']; ?>" class="user-image" alt="User Image">
              <?php else: ?>
                <span class="user-image user-avatar fa fa-user" aria-hidden="true"></span>
              <?php endif; ?>
            </a>
            <ul class="dropdown-menu user-profile-menu">
              <li class="user-header">
                <div class="user-profile-hero">
                  <div class="user-profile-avatar-wrap">
                    <?php if (!empty($user['photo']) && file_exists('../images/'.$user['photo'])): ?>
                      <img src="<?php echo '../images/'.$user['photo']; ?>" class="user-profile-avatar" alt="User Image">
                    <?php else: ?>
                      <span class="user-profile-avatar user-avatar fa fa-user" aria-hidden="true"></span>
                    <?php endif; ?>
                  </div>
                  <div class="user-profile-name"><?php echo $user['firstname'].' '.$user['lastname']; ?></div>
                  <div class="user-profile-membership">Miembro desde <?php echo date('M. Y', strtotime($user['created_on'])); ?></div>
                </div>
              </li>
              <li class="user-footer">
                <div class="user-action-buttons">
                  <a href="#profile" data-toggle="modal" class="btn btn-default btn-flat" id="admin_profile"><i class="fa fa-user"></i> Actualizar perfil</a>
                  <a href="logout.php" class="btn btn-default btn-flat"><i class="fa fa-sign-out"></i> Cerrar sesi&oacute;n</a>
                </div>
              </li>
            </ul>
          </li>
        </ul>
      </div>
    </nav>
  </header>
  <style>
    .navbar-custom-menu .notifications-menu > a.notification-trigger {
      position: relative;
      display: flex;
      align-items: center;
      justify-content: center;
      flex: 0 0 38px;
      width: 38px !important;
      min-width: 38px;
      height: 38px !important;
      min-height: 38px;
      margin: 6px 5px;
      padding: 0;
      box-sizing: border-box;
      border: 1px solid rgba(221,177,44,.8);
      border-radius: 50%;
      background: #17477f;
      color: #fff !important;
      box-shadow: 0 1px 3px rgba(0,0,0,.12);
      transition: background .18s ease, color .18s ease, box-shadow .18s ease;
    }
    .navbar-custom-menu .notification-bell-icon {
      display: block;
      width: auto;
      height: auto;
      color: #fff !important;
      font-size: 19px !important;
      line-height: 1;
    }
    .notification-dropdown {
      width: 360px;
      max-width: calc(100vw - 24px);
      padding: 0;
      border: 1px solid #dce5ef;
      border-radius: 10px;
      overflow: hidden;
      box-shadow: 0 12px 30px rgba(10,35,65,.2);
    }
    .notification-heading {
      display: flex;
      align-items: center;
      justify-content: space-between;
      padding: 13px 16px;
      border-bottom: 1px solid #e8eef5;
      color: #153e70;
      font-size: 14px;
      font-weight: 700;
    }
    .notification-heading-copy { display:flex; flex-direction:column; gap:2px; }
    .notification-heading small { color: #68809a; font-size: 11px; font-weight: 600; }
    .notification-clear { display:inline-flex; align-items:center; gap:5px; padding:6px 8px; border:1px solid #d9e3ee; border-radius:6px; background:#fff; color:#61758b; font-size:11px; font-weight:600; }
    .notification-clear:hover, .notification-clear:focus { border-color:#c65c5c; background:#fff5f5; color:#b64242; outline:none; }
    .notification-clear:disabled { cursor:wait; opacity:.6; }
    .notification-list { max-height: 360px; overflow-y: auto; }
    .notification-list > ul { margin: 0; padding: 0; list-style: none; }
    .notification-item {
      display: flex;
      align-items: flex-start;
      gap: 11px;
      padding: 13px 15px;
      border-bottom: 1px solid #edf1f5;
      white-space: normal;
    }
    .notification-item.has-employee { padding:0; }
    .notification-item-link { display:flex; align-items:flex-start; gap:11px; width:100%; padding:13px 15px; color:inherit; text-decoration:none !important; }
    .notification-item.has-employee .notification-item-link { width:auto; flex:1; min-width:0; }
    .notification-item-link:hover, .notification-item-link:focus { background:#f3f8fc; color:inherit; outline:none; }
    .notification-item-link:focus-visible { box-shadow:inset 0 0 0 2px #2871b9; }
    .notification-item-actions { position:relative; flex:0 0 auto; padding:9px 9px 0 0; }
    .notification-item-more { display:flex; align-items:center; justify-content:center; width:30px; height:30px; border:0; border-radius:50%; background:transparent; color:#718197; }
    .notification-item-more:hover, .notification-item-more:focus, .notification-item-actions.open .notification-item-more { background:#edf3f8; color:#173f70; outline:none; }
    .notification-item-actions-menu { display:none; position:absolute; z-index:3; top:37px; right:8px; min-width:178px; padding:4px; border:1px solid #dce5ee; border-radius:7px; background:#fff; box-shadow:0 8px 22px rgba(10,35,65,.18); }
    .notification-item-actions.open .notification-item-actions-menu { display:block; }
    .notification-item-delete { display:flex; align-items:center; gap:8px; width:100%; padding:8px 10px; border:0; border-radius:5px; background:#fff; color:#a63e3e; text-align:left; font-size:12px; }
    .notification-item-delete:hover, .notification-item-delete:focus { background:#fff1f1; outline:none; }
    .notification-item:last-child { border-bottom: 0; }
    .notification-item-icon {
      display: flex;
      align-items: center;
      justify-content: center;
      flex: 0 0 32px;
      width: 32px;
      height: 32px;
      border-radius: 50%;
      color: #fff;
      background: #2871b9;
    }
    .notification-item-icon.completion { background: #22845f; }
    .notification-item-copy { display: block; min-width: 0; color: #4f6278; font-size: 12px; line-height: 1.45; }
    .notification-item-copy strong, .notification-item-copy span, .notification-item-copy small { display: block; white-space: normal; }
    .notification-item-copy strong { margin-bottom: 2px; color: #193c66; font-size: 13px; }
    .notification-item-copy small { margin-top: 4px; color: #8795a5; font-size: 10px; }
    .notification-empty { padding: 22px 16px; color: #74869a; text-align: center; font-size: 12px; white-space: normal; }
    .notification-empty i { margin-right: 5px; color: #22845f; }
    .notification-count {
      position: absolute;
      top: -2px;
      right: -1px;
      min-width: 17px;
      height: 17px;
      padding: 0 4px;
      border: 2px solid #123d72;
      border-radius: 10px;
      background: #d8a91f;
      color: #fff;
      font-size: 9px;
      font-weight: 700;
      line-height: 13px;
      text-align: center;
    }
    @media (max-width: 767px) {
      .notification-dropdown { right: -55px; }
    }
    .navbar-custom-menu > .navbar-nav {
      display: flex;
      align-items: center;
    }
    .navbar-custom-menu > .navbar-nav > li.notifications-menu,
    .navbar-custom-menu > .navbar-nav > li.user-menu {
      display: flex;
      align-items: center;
      height: 50px;
    }
    .navbar-custom-menu .user-menu > a.dropdown-toggle {
      display: flex;
      align-items: center;
      justify-content: center;
      height: 50px;
      padding: 0 5px !important;
    }
    .navbar-custom-menu .user-menu > a > .user-avatar {
      display: inline-flex !important;
      align-items: center;
      justify-content: center;
      width: 38px !important;
      height: 38px !important;
      margin: 0 !important;
      border: 1px solid rgba(221,177,44,.8) !important;
      border-radius: 50%;
      background: #17477f !important;
      color: #fff !important;
      font-size: 18px !important;
      line-height: 1 !important;
      vertical-align: middle;
      box-shadow: 0 1px 3px rgba(0,0,0,.12);
    }
    .navbar-custom-menu .notifications-menu > a.notification-trigger:hover,
    .navbar-custom-menu .notifications-menu > a.notification-trigger:focus {
      border-color: #c89c19;
      background: #20558d;
      color: #fff !important;
      outline: none;
    }
    .navbar-custom-menu .notifications-menu > a.notification-trigger:focus-visible {
      outline: 2px solid #f4c542;
      outline-offset: 2px;
    }
    .user-profile-menu {
      width: 310px !important;
      border: none;
      border-radius: 0;
      padding: 0;
      overflow: hidden;
      box-shadow: 0 8px 18px rgba(0,0,0,.18);
    }
    .user-profile-menu .user-header {
      background: linear-gradient(180deg, #0f3b78 0%, #1b5a9a 100%);
      padding: 0;
      margin: 0;
      border: none;
    }
    .user-profile-hero {
      padding: 18px 20px 16px;
      text-align: center;
      color: #fff;
    }
    .user-profile-avatar-wrap {
      display: flex;
      align-items: center;
      justify-content: center;
      margin: 0 auto 10px;
      width: 80px;
      height: 80px;
      border-radius: 50%;
      background: rgba(255,255,255,.18);
      border: 3px solid rgba(255,255,255,.4);
      box-shadow: inset 0 0 0 1px rgba(255,255,255,.2);
    }
    .user-profile-avatar {
      width: 58px;
      height: 58px;
      border-radius: 50%;
      object-fit: cover;
      display: block;
      margin: 0 auto;
      background: #fff;
      color: #0f3b78;
      font-size: 28px;
      line-height: 58px;
      text-align: center;
    }
    .user-profile-name {
      font-size: 20px;
      font-weight: 700;
      margin: 0;
      line-height: 1.3;
      color: #fff;
    }
    .user-profile-membership {
      margin-top: 6px;
      font-size: 13px;
      color: rgba(255,255,255,.9);
      line-height: 1.3;
    }
    .user-profile-menu .user-footer {
      background: #eef3f8;
      padding: 10px 12px 12px;
      border-top: 1px solid rgba(15,59,120,.08);
    }
    .user-action-buttons {
      display: flex;
      gap: 8px;
      align-items: center;
      justify-content: space-between;
    }
    .user-action-buttons .btn {
      flex: 1 1 0;
      min-height: 36px;
      border-radius: 6px;
      font-weight: 600;
      font-size: 13px;
      display: inline-flex;
      align-items: center;
      justify-content: center;
      gap: 6px;
      background: #fff;
      border: 1px solid #d8e2ee;
      color: #1d3557;
      box-shadow: none;
    }
    .user-action-buttons .btn:hover {
      background: #f7faff;
    }
    .user-action-buttons .btn i {
      font-size: 12px;
    }
    @media (max-width: 767px) {
      .user-profile-menu {
        width: 260px !important;
      }
      .user-action-buttons {
        flex-direction: column;
      }
    }
  </style>
  <script>
    document.addEventListener('DOMContentLoaded', function () {
      var trigger = document.querySelector('.notification-trigger');
      if (!trigger) return;
      var clearButton = document.querySelector('.notification-clear');
      var notificationList = document.querySelector('.notification-list');
      if (notificationList) {
        notificationList.addEventListener('click', function (event) {
          var moreButton = event.target.closest('.notification-item-more');
          if (moreButton) {
            event.preventDefault();
            event.stopPropagation();
            var actions = moreButton.closest('.notification-item-actions');
            var wasOpen = actions.classList.contains('open');
            document.querySelectorAll('.notification-item-actions.open').forEach(function (item) {
              item.classList.remove('open');
              var triggerButton = item.querySelector('.notification-item-more');
              if (triggerButton) triggerButton.setAttribute('aria-expanded', 'false');
            });
            if (!wasOpen) {
              actions.classList.add('open');
              moreButton.setAttribute('aria-expanded', 'true');
            }
            return;
          }
          var deleteButton = event.target.closest('.notification-item-delete');
          if (!deleteButton) return;
          event.preventDefault();
          event.stopPropagation();
          if (deleteButton.disabled) return;
          deleteButton.disabled = true;
          var body = new URLSearchParams();
          body.set('csrf_token', notificationList.getAttribute('data-csrf') || '');
          body.set('notification_id', deleteButton.getAttribute('data-notification-id') || '');
          fetch('notifications_delete.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8' },
            body: body.toString(),
            credentials: 'same-origin'
          }).then(function (response) {
            if (!response.ok) throw new Error('No se pudo eliminar la notificación.');
            return response.json();
          }).then(function (result) {
            if (!result.ok) throw new Error('No se pudo eliminar la notificación.');
            var item = deleteButton.closest('.notification-item');
            if (item) item.remove();
            var list = notificationList.querySelector('ul');
            if (list && !list.querySelector('.notification-item')) {
              list.innerHTML = '<li class="notification-empty"><i class="fa fa-check-circle" aria-hidden="true"></i> No hay notificaciones por el momento.</li>';
            }
            var badge = trigger.querySelector('.notification-count');
            if (result.unread > 0) {
              if (!badge) {
                badge = document.createElement('span');
                badge.className = 'notification-count';
                trigger.appendChild(badge);
              }
              badge.textContent = result.unread > 99 ? '99+' : String(result.unread);
            } else if (badge) badge.remove();
            var newCount = document.querySelector('.notification-heading small');
            if (newCount) newCount.textContent = result.unread + ' nuevas';
          }).catch(function (error) {
            window.alert(error.message);
            deleteButton.disabled = false;
          });
        });
      }
      if (clearButton) {
        clearButton.addEventListener('click', function (event) {
          event.preventDefault();
          event.stopPropagation();
          if (clearButton.disabled) return;
          clearButton.disabled = true;
          var body = new URLSearchParams();
          body.set('csrf_token', trigger.getAttribute('data-csrf') || '');
          fetch('notifications_clear.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8' },
            body: body.toString(),
            credentials: 'same-origin'
          }).then(function (response) {
            if (!response.ok) throw new Error('No se pudieron limpiar las notificaciones.');
            return response.json();
          }).then(function (result) {
            if (!result.ok) throw new Error('No se pudieron limpiar las notificaciones.');
            var list = document.querySelector('.notification-list > ul');
            if (list) list.innerHTML = '<li class="notification-empty"><i class="fa fa-check-circle" aria-hidden="true"></i> No hay notificaciones por el momento.</li>';
            var badge = trigger.querySelector('.notification-count');
            if (badge) badge.remove();
            var newCount = document.querySelector('.notification-heading small');
            if (newCount) newCount.textContent = '0 nuevas';
          }).catch(function (error) {
            window.alert(error.message);
          }).finally(function () {
            clearButton.disabled = false;
          });
        });
      }
      trigger.addEventListener('click', function () {
        var token = trigger.getAttribute('data-csrf');
        if (!token) return;
        var body = new URLSearchParams();
        body.set('csrf_token', token);
        fetch('notifications_read.php', {
          method: 'POST',
          headers: { 'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8' },
          body: body.toString(),
          credentials: 'same-origin'
        }).then(function (response) {
          if (!response.ok) return;
          var badge = trigger.querySelector('.notification-count');
          if (badge) badge.remove();
          var newCount = document.querySelector('.notification-heading small');
          if (newCount) newCount.textContent = '0 nuevas';
        }).catch(function () {});
      });
    });
  </script>
  <?php include 'includes/profile_modal.php'; ?>


