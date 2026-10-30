<?php

require_once __DIR__ . '/includes/report_theme.php';

class StazherCertificatePdf extends StazherReportPdf {}

if (isset($_GET['empid'])) {

  require __DIR__ . '/includes/certificate_data.php';

  // Verificar si se obtuvieron datos del empleado antes de acceder a sus propiedades.
  if (!empty($employee) && isset($employee['firstname'], $employee['lastname'], $employee['identity_card'], $employee['type_modality'], $employee['description'], $employee['created_on'])) {
    $hours = (int) floor($employee['totalSeconds'] / 3600);
    $hoursInWords = mb_convert_case(certificateNumberWords($hours), MB_CASE_TITLE, 'UTF-8');
    foreach (array('firstname', 'lastname', 'identity_card', 'type_modality', 'description') as $key) {
      $employee[$key] = htmlspecialchars((string) $employee[$key], ENT_QUOTES, 'UTF-8');
    }

    // Crear una instancia de TCPDF
    $pdf = new StazherCertificatePdf('P', 'mm', 'LETTER', true, 'UTF-8');

    // Establecer información del documento
    $pdf->SetCreator(PDF_CREATOR);
    $pdf->SetAuthor('Nombre del Autor');
    $pdf->SetTitle('Carta de Certificado');
    $pdf->SetSubject('Carta de Certificado');
    $pdf->SetKeywords('TCPDF, Carta, Certificado');

    // Agregar una nueva página
    $pdf->SetMargins(30, 45, 30);
    $pdf->SetHeaderMargin(8);
    $pdf->SetFooterMargin(20);
    $pdf->SetAutoPageBreak(true, 32);
    $pdf->setPrintHeader(false);
    $pdf->setPrintFooter(false);
    $pdf->AddPage();
    $pdf->addLetterheadBackground();
    $pdf->SetXY(30, 45);

    // Obtener la fecha actual en español
    $meses = array(
      'enero', 'febrero', 'marzo', 'abril', 'mayo', 'junio',
      'julio', 'agosto', 'septiembre', 'octubre', 'noviembre', 'diciembre'
    ); // Configurar la localización a español
    $fecha_actual = date('d') . ' de ' . $meses[date('n') - 1] . ' del ' . date('Y');
    $fecha_creacion = date('d', strtotime($employee['created_on'])) . ' de ' . $meses[date('n', strtotime($employee['created_on'])) - 1];
    $fullName = trim($employee['firstname'] . ' ' . $employee['lastname']);
    $surnameParts = preg_split('/\s+/u', trim($employee['lastname']));
    $paternalSurname = $surnameParts[0] ?? trim($employee['lastname']);
    $honorific = isset($employee['gender']) && strtolower($employee['gender']) === 'female' ? 'la Srta.' : 'el Sr.';
    $safe = static function ($value) { return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8'); };
    $reference = "AGBC/DAF/RRHH-CP-N\u{00b0}" . str_pad((string) $employee['empid'], 3, '0', STR_PAD_LEFT) . '/' . date('Y');

    // Formato institucional del certificado, con los datos del registro seleccionado.
    $pdf->SetTextColor(25, 25, 25);
    $pdf->SetFont('times', 'B', 16);
    $pdf->SetXY(22, 43);
    $pdf->Cell(172, 8, "CERTIFICADO DE PR\u{00c1}CTICA PROFESIONAL", 0, 1, 'C');
    $pdf->SetFont('times', 'BU', 10);
    $pdf->Cell(172, 6, $reference, 0, 1, 'C');
    $pdf->Ln(8);

    $area = $safe($employee['certificate_area']);
    $body = '<p style="text-align:justify;">El suscrito <b>Lic. Abel Gerson Rojas Peñaloza</b> Director Administrativo Financiero de la Agencia Boliviana de Correos “Correos de Bolivia”, en uso de las atribuciones conferidas por Ley.</p>'
      . '<p style="margin-top:12px;"><b>CERTIFICA A QUIEN CORRESPONDA:</b></p>'
      . '<p style="text-align:justify;margin-top:12px;">Que ' . $honorific . ' <b>' . $safe($fullName) . '</b> con Cédula de Identidad <b>N°' . $safe($employee['identity_card']) . '</b>, realizó su ' . $safe($employee['type_modality']) . ' en la <b>' . $area . '</b>, de la Agencia Boliviana de Correos “Correos de Bolivia”, desde el ' . $safe($fecha_creacion) . ' hasta el ' . $safe($fecha_actual) . ', cumpliendo <b>' . $hours . ' (' . $hoursInWords . ') horas</b> de carga horaria, bajo la supervisión del responsable del área correspondiente.</p>'
      . '<p style="text-align:justify;margin-top:8px;">Durante su permanencia en la Agencia, ' . $honorific . ' <b>' . $safe($paternalSurname) . '</b> ha demostrado excelente aptitud, responsabilidad, puntualidad, y colaboración en el desempeño de las funciones asignadas, desarrollando un alto grado de compromiso con la Agencia Boliviana de Correos “Correos de Bolivia”.</p>'
      . '<p style="text-align:justify;margin-top:8px;">Exhortamos al estudiante a continuar con ese mismo espíritu, para bien personal y su respectiva proyección profesional, augurándole permanente éxito.</p>'
      . '<p style="text-align:justify;margin-top:8px;">Es cuanto tengo a bien certificar en honor a la verdad, para los fines consiguientes.</p>';
    $pdf->SetFont('times', '', 11);
    $pdf->setCellHeightRatio(1.12);
    $pdf->writeHTMLCell(172, 0, 22, $pdf->GetY(), $body, 0, 1, false, true, 'J', true);
    $pdf->Ln(14);
    $pdf->SetFont('times', '', 11);
    $pdf->Cell(172, 6, 'La Paz, ' . $fecha_actual, 0, 1, 'R');

    // Generar el archivo PDF y enviarlo al navegador para su descarga
    $pdf->Output('carta_certificado.pdf', 'I');
  } else {
    // Si faltan datos o el array está vacío, redirigir a otra página o mostrar un mensaje de error
    header('Location: error_page.php');
    exit();
  }
} else {
  // Redirigir si no se proporciona el parámetro empid
  header('Location: index.php');
  exit();
}
?>
