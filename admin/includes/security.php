<?php
function csrfField(): string
{
    if (empty($_SESSION['csrf_token'])) { $_SESSION['csrf_token'] = bin2hex(random_bytes(32)); }
    return '<input type="hidden" name="csrf_token" value="' . htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES, 'UTF-8') . '">';
}
function requireCsrf(): void
{
    $token = $_POST['csrf_token'] ?? null;
    if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !is_string($token) || empty($_SESSION['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $token)) {
        http_response_code(403);
        exit('Solicitud no válida. Recargue la página e intente nuevamente.');
    }
}
function savePhoto(array $file): string
{
    $error = $file['error'] ?? UPLOAD_ERR_NO_FILE;
    if ($error === UPLOAD_ERR_NO_FILE) { return ''; }
    if ($error !== UPLOAD_ERR_OK || ($file['size'] ?? 0) > 5 * 1024 * 1024 || !is_uploaded_file($file['tmp_name'] ?? '')) {
        throw new InvalidArgumentException('La foto debe pesar como máximo 5 MB y cargarse correctamente.');
    }
    $info = @getimagesize($file['tmp_name']);
    $extensions = array('image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp');
    $mime = (new finfo(FILEINFO_MIME_TYPE))->file($file['tmp_name']);
    if (!$info || !isset($extensions[$mime]) || $info['mime'] !== $mime || $info[0] > 8000 || $info[1] > 8000) {
        throw new InvalidArgumentException('Seleccione una foto JPG, PNG o WebP válida.');
    }
    $name = bin2hex(random_bytes(16)) . '.' . $extensions[$mime];
    if (!move_uploaded_file($file['tmp_name'], __DIR__ . '/../../images/' . $name)) { throw new RuntimeException('No se pudo guardar la foto.'); }
    return $name;
}
