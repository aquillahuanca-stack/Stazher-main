<?php

require '../vendor/autoload.php';
require_once __DIR__ . '/includes/letterhead_docx.php';

use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\IOFactory;
use PhpOffice\PhpWord\SimpleType\Jc;
use PhpOffice\PhpWord\SimpleType\TblWidth;
use PhpOffice\PhpWord\Style\Paragraph;

if (isset($_GET['empid'])) {

  require __DIR__ . '/includes/certificate_data.php';

  // Verificar si se obtuvieron datos del empleado antes de acceder a sus propiedades.
  if (!empty($employee) && isset($employee['firstname'], $employee['lastname'], $employee['identity_card'], $employee['type_modality'], $employee['description'], $employee['created_on'])) {
    
    $totalHours = (int) floor($employee['totalSeconds'] / 3600);
    $hoursInWords = mb_convert_case(certificateNumberWords($totalHours), MB_CASE_TITLE, 'UTF-8');

    // Crear una instancia de PHPWord
    $phpWord = new PhpWord();

    // Configuración de la página en formato carta
    $section = $phpWord->addSection(array(
      'pageSizeW' => \PhpOffice\PhpWord\Shared\Converter::inchToTwip(8.5),
      'pageSizeH' => \PhpOffice\PhpWord\Shared\Converter::inchToTwip(11),
      'marginTop' => \PhpOffice\PhpWord\Shared\Converter::cmToTwip(3.5),
      'marginLeft' => \PhpOffice\PhpWord\Shared\Converter::cmToTwip(3),
      'marginRight' => \PhpOffice\PhpWord\Shared\Converter::cmToTwip(3),
      'marginBottom' => \PhpOffice\PhpWord\Shared\Converter::cmToTwip(3.2),
      'headerDistance' => 0,
      'footerDistance' => \PhpOffice\PhpWord\Shared\Converter::inchToTwip(0.2)
    ));

    // Obtener la fecha actual en español
    $meses = array(
      'enero', 'febrero', 'marzo', 'abril', 'mayo', 'junio',
      'julio', 'agosto', 'septiembre', 'octubre', 'noviembre', 'diciembre'
    );
    $fecha_actual = date('d') . ' de ' . $meses[date('n') - 1] . ' del ' . date('Y');
    $fecha_creacion = date('d', strtotime($employee['created_on'])) . ' de ' . $meses[date('n', strtotime($employee['created_on'])) - 1] . ' del ' . date('Y', strtotime($employee['created_on']));

    $fullName = trim($employee['firstname'] . ' ' . $employee['lastname']);
    $surnameParts = preg_split('/\s+/u', trim($employee['lastname']));
    $paternalSurname = $surnameParts[0] ?? trim($employee['lastname']);
    $honorific = isset($employee['gender']) && strtolower($employee['gender']) === 'female' ? 'la Srta.' : 'el Sr.';
    $reference = 'AGBC/DAF/RRHH-CP-N°' . str_pad((string) $employee['empid'], 3, '0', STR_PAD_LEFT) . '/' . date('Y');
    $titleStyle = array('bold' => true, 'size' => 15, 'underline' => 'single', 'name' => 'Times New Roman');
    $subtitleStyle = array('bold' => true, 'size' => 10, 'underline' => 'single', 'name' => 'Times New Roman');
    $title = $section->addTextRun(array('alignment' => Jc::CENTER));
    $title->addText('CERTIFICADO DE PRÁCTICA PROFESIONAL', $titleStyle);
    $section->addTextBreak(1, array('size' => 4), array('spaceAfter' => 0));

    $subtitle = $section->addTextRun(array('alignment' => Jc::CENTER));
    $subtitle->addText($reference, $subtitleStyle);
    $section->addTextBreak(1, array('size' => 4), array('spaceAfter' => 0));

    // Configuración de alineación justificada
    $paragraphStyle = array('alignment' => Jc::BOTH, 'spaceAfter' => 150, 'lineSpacing' => 260);
    $bodyFont = array('name' => 'Times New Roman', 'size' => 11);

    $area = $employee['certificate_area'];
    $section->addText('El suscrito Lic. Abel Gerson Rojas Peñaloza Director Administrativo Financiero de la Agencia Boliviana de Correos “Correos de Bolivia”, en uso de las atribuciones conferidas por Ley.', $bodyFont, $paragraphStyle);
    $section->addTextBreak(1, array('size' => 4), array('spaceAfter' => 0));
    $section->addText('CERTIFICA A QUIEN CORRESPONDA:', array('name' => 'Times New Roman', 'size' => 12, 'bold' => true), $paragraphStyle);
    $section->addTextBreak(1, array('size' => 4), array('spaceAfter' => 0));
    $section->addText('Que ' . $honorific . ' ' . $fullName . ' con Cédula de Identidad N°' . $employee['identity_card'] . ', realizó su ' . $employee['type_modality'] . ' en la ' . $area . ', de la Agencia Boliviana de Correos “Correos de Bolivia”, desde el ' . $fecha_creacion . ' hasta el ' . $fecha_actual . ', cumpliendo ' . $totalHours . ' (' . $hoursInWords . ') horas de carga horaria, bajo la supervisión del responsable del área correspondiente.', $bodyFont, $paragraphStyle);
    $section->addTextBreak(1, array('size' => 4), array('spaceAfter' => 0));
    $section->addText('Durante su permanencia en la Agencia, ' . $honorific . ' ' . $paternalSurname . ' ha demostrado excelente aptitud, responsabilidad, puntualidad, y colaboración en el desempeño de las funciones asignadas, desarrollando un alto grado de compromiso con la Agencia Boliviana de Correos “Correos de Bolivia”.', $bodyFont, $paragraphStyle);
    $section->addTextBreak(1, array('size' => 4), array('spaceAfter' => 0));
    $section->addText('Exhortamos al estudiante a continuar con ese mismo espíritu, para bien personal y su respectiva proyección profesional, augurándole permanente éxito.', $bodyFont, $paragraphStyle);
    $section->addTextBreak(1, array('size' => 4), array('spaceAfter' => 0));
    $section->addText('Es cuanto tengo a bien certificar en honor a la verdad, para los fines consiguientes.', $bodyFont, $paragraphStyle);
    $section->addTextBreak(1, array('size' => 4), array('spaceAfter' => 0));
    // Texto alineado a la derecha
    $section->addText('La Paz, ' . $fecha_actual, $bodyFont, ['alignment' => Jc::RIGHT]);

    // Guardar el documento en un archivo temporal
    $tempFile = tempnam(sys_get_temp_dir(), 'PHPWord');
    $writer = IOFactory::createWriter($phpWord, 'Word2007');
    $writer->save($tempFile);
    applyInstitutionalLetterhead($tempFile);

    // Forzar la descarga del archivo DOCX
    header('Content-Description: File Transfer');
    header('Content-Type: application/vnd.openxmlformats-officedocument.wordprocessingml.document');
    header('Content-Disposition: attachment; filename="certificado_practica.docx"');
    header('Content-Transfer-Encoding: binary');
    header('Expires: 0');
    header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
    header('Pragma: public');
    header('Content-Length: ' . filesize($tempFile));
    ob_clean();
    flush();
    readfile($tempFile);
    unlink($tempFile);
    exit();
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
