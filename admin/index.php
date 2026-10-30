<?php
session_start();
if (isset($_SESSION['admin'])) {
    header('Location: home.php');
    exit;
}
$loginError = $_SESSION['error'] ?? '';
unset($_SESSION['error']);
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Acceso administrador | Gestión de Pasantes</title>
  <link rel="icon" href="../images/Logo.jpeg" type="image/jpeg">
  <link rel="stylesheet" href="css/admin-login.css?v=<?php echo filemtime(__DIR__ . '/css/admin-login.css'); ?>">
</head>
<body>
  <main class="login-layout">
    <section class="access-panel" aria-labelledby="login-title">
      <div class="access-content">
        <div class="logo-frame"><img src="../images/Logo.jpeg" alt="Correos de Bolivia" width="1099" height="359"></div>
        <span class="access-badge">ACCESO ADMINISTRATIVO</span>
        <h2 id="login-title">Iniciar sesión</h2>
        <?php if ($loginError !== ''): ?>
          <div class="login-error" role="alert"><?php echo htmlspecialchars((string) $loginError, ENT_QUOTES, 'UTF-8'); ?></div>
        <?php endif; ?>
        <form action="login.php" method="post">
          <div class="field">
            <label for="username">Usuario</label>
            <input id="username" type="text" name="username" placeholder="Ingresa tu usuario" autocomplete="username" autocapitalize="none" spellcheck="false" required>
          </div>
          <div class="field">
            <label for="password">Contraseña</label>
            <div class="password-wrap">
              <input id="password" type="password" name="password" placeholder="Ingresa tu contraseña" autocomplete="current-password" required>
              <button class="password-toggle" type="button" aria-controls="password" aria-pressed="false" aria-label="Mostrar contraseña">Mostrar</button>
            </div>
          </div>
          <button class="submit-button" type="submit" name="login">Ingresar al sistema <span aria-hidden="true">&#8594;</span></button>
        </form>
        <p class="access-note">Uso exclusivo del personal autorizado.<br>Si necesitas ayuda, contacta al responsable del sistema.</p>
      </div>
      <div class="panel-footer">Sistema de control de asistencia</div>
    </section>
  </main>
  <footer class="site-footer">
    <span>&copy; <?php echo date('Y'); ?> Agencia Boliviana de Correos</span>
    <details><summary>Créditos del sistema</summary><p>Desarrollado por <a href="https://www.linkedin.com/in/marco-antonio-espinoza-rojas-617539256/">Marco Espinoza</a>, Alexandra Quintanilla y Leonardo Doria Medina.</p></details>
  </footer>
  <script>
    const password = document.getElementById('password');
    const toggle = document.querySelector('.password-toggle');
    toggle.addEventListener('click', function () {
      const show = password.type === 'password';
      password.type = show ? 'text' : 'password';
      toggle.textContent = show ? 'Ocultar' : 'Mostrar';
      toggle.setAttribute('aria-pressed', String(show));
      toggle.setAttribute('aria-label', show ? 'Ocultar contraseña' : 'Mostrar contraseña');
    });
  </script>
</body>
</html>
