<?php
include 'includes/session.php';
require_once __DIR__ . '/includes/report_theme.php';

function pdfText($value)
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

$sql = "SELECT employees.employee_id, employees.firstname, employees.lastname, employees.identity_card,
        area.name_area, career.name_career,
        COALESCE(SUM(" . attendanceSecondsSql('attendance') . "), 0)
          + MAX(employees.accumulated_extra_seconds) AS total_seconds
        FROM employees
        LEFT JOIN area ON area.id = employees.area_id
        LEFT JOIN career ON career.id = employees.career_id
        LEFT JOIN attendance ON attendance.employee_id = employees.id AND attendance.time_out IS NOT NULL
        GROUP BY employees.id, employees.employee_id, employees.firstname, employees.lastname,
        employees.identity_card, area.name_area, career.name_career, employees.add_hr
        ORDER BY employees.lastname, employees.firstname";
$query = $conn->query($sql);

$pdf = new StazherReportPdf('L', PDF_UNIT, 'LETTER', true, 'UTF-8', false);
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetTitle('Lista de Pasantes');
$pdf->SetSubject('Listado general de pasantes registrados');
$pdf->SetMargins(12, 58, 12);
$pdf->SetAutoPageBreak(true, 44);
$pdf->setPrintHeader(true);
$pdf->setPrintFooter(true);
$pdf->SetFooterMargin(8);
$pdf->SetFont('helvetica', '', 8);
$pdf->AddPage();
$pdf->addLetterheadBackground();
require __DIR__ . '/includes/employee_list_header.php';

$tableLeft = 12;
$tableWidth = $pdf->getPageWidth() - ($tableLeft * 2);
$pageBottom = $pdf->getPageHeight() - 44;
$tableY = $pdf->GetY();
$columnSpecs = array(
    array(0.04, 'N.', 'C'),
    array(0.14, 'Identificador', 'C'),
    array(0.27, 'Pasante', 'L'),
    array(0.12, 'Carnet', 'C'),
    array(0.30, 'Carrera y area', 'L'),
    array(0.13, 'Horas acumuladas', 'C')
);
$columnWidths = array_map(function ($spec) use ($tableWidth) { return $spec[0] * $tableWidth; }, $columnSpecs);
$drawTableHeader = function () use ($pdf, $columnSpecs, $columnWidths, $tableLeft, &$tableY) {
    $pdf->SetFont('helvetica', 'B', 7);
    $pdf->SetTextColor(255, 255, 255);
    $pdf->SetFillColor(23, 63, 104);
    $pdf->SetDrawColor(211, 221, 231);
    $pdf->SetLineWidth(0.25);
    $x = $tableLeft;
    foreach ($columnSpecs as $i => $spec) {
        $pdf->Rect($x, $tableY, $columnWidths[$i], 8, 'DF');
        $pdf->MultiCell($columnWidths[$i], 8, $spec[1], 0, 'C', false, 0, $x, $tableY, true, 0, false, false, 8, 'M', true);
        $x += $columnWidths[$i];
    }
    $tableY += 8;
};
$drawTableHeader();

$number = 1;
while ($row = $query->fetch_assoc()) {
    $totalSeconds = (int) $row['total_seconds'];
    $hours = floor($totalSeconds / 3600);
    $minutes = floor(($totalSeconds % 3600) / 60);
    $careerArea = trim(($row['name_career'] ?: 'Carrera no registrada') . ' / ' . ($row['name_area'] ?: 'Area no registrada'));
    $values = array(
        (string) $number,
        (string) $row['employee_id'],
        trim($row['lastname'] . ' ' . $row['firstname']),
        (string) $row['identity_card'],
        $careerArea,
        $hours . ' h. ' . str_pad($minutes, 2, '0', STR_PAD_LEFT) . ' min.'
    );
    $pdf->SetFont('helvetica', '', 7);
    $rowHeight = 7;
    foreach ($values as $i => $value) {
        $rowHeight = max($rowHeight, $pdf->getStringHeight($columnWidths[$i] - 1, $value, true, false) + 0.5);
    }
    if ($tableY + $rowHeight > $pageBottom) {
        $pdf->AddPage();
        $pdf->SetY(58);
        $tableY = $pdf->GetY();
        $drawTableHeader();
    }
    $rowColor = ($number % 2 === 0) ? array(243, 247, 251) : array(255, 255, 255);
    $x = $tableLeft;
    foreach ($values as $i => $value) {
        $pdf->SetFillColor($rowColor[0], $rowColor[1], $rowColor[2]);
        $pdf->SetDrawColor(211, 221, 231);
        $pdf->SetLineWidth(0.25);
        $pdf->SetTextColor($i === 0 ? 23 : 25, $i === 0 ? 63 : 33, $i === 0 ? 104 : 46);
        $pdf->SetFont('helvetica', $i === 0 ? 'B' : '', 7);
        $pdf->Rect($x, $tableY, $columnWidths[$i], $rowHeight, 'DF');
        $pdf->MultiCell($columnWidths[$i], $rowHeight, $value, 0, $columnSpecs[$i][2], false, 0, $x, $tableY, true, 0, false, false, $rowHeight, 'M', true);
        $x += $columnWidths[$i];
    }
    $tableY += $rowHeight;
    $number++;
}

if ($number === 1) {
    $emptyHeight = 9;
    if ($tableY + $emptyHeight > $pageBottom) {
        $pdf->AddPage();
        $pdf->SetY(58);
        $tableY = $pdf->GetY();
        $drawTableHeader();
    }
    $pdf->SetFillColor(248, 249, 253);
    $pdf->SetDrawColor(211, 221, 231);
    $pdf->SetTextColor(99, 117, 137);
    $pdf->SetFont('helvetica', '', 8);
    $pdf->MultiCell($tableWidth, $emptyHeight, 'No hay pasantes registrados.', 1, 'C', true, 0, $tableLeft, $tableY, true, 0, false, true, $emptyHeight, 'M', true);
    $tableY += $emptyHeight;
}

$totalHeight = 9;
if ($tableY + $totalHeight > $pageBottom) {
    $pdf->AddPage();
    $pdf->SetY(58);
    $tableY = $pdf->GetY();
}
$pdf->SetFillColor(237, 243, 248);
$pdf->SetDrawColor(211, 221, 231);
$pdf->SetTextColor(23, 63, 104);
$pdf->SetFont('helvetica', 'B', 8);
$pdf->MultiCell($tableWidth, $totalHeight, 'Total de pasantes registrados: ' . ($number - 1), 1, 'R', true, 0, $tableLeft, $tableY, true, 0, false, true, $totalHeight, 'M', true);
$pdf->Output('Lista_Pasantes.pdf', 'I');
?>
