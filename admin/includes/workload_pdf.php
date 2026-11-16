<?php
require_once __DIR__ . '/report_theme.php';

$pdf = new StazherReportPdf('P', 'mm', 'LETTER', true, 'UTF-8', false);
$pdf->SetCreator('STAZHER');
$pdf->SetTitle('Reporte de Carga Horaria');
$pdf->setPrintHeader(true);
$pdf->setPrintFooter(false);
$pdf->SetMargins(30, 25, 30);
$pdf->SetAutoPageBreak(true, 25);
$pdf->AddPage();
$pdf->addLetterheadBackground();
$pdf->SetTextColor(0, 32, 104);
$pdf->SetDrawColor(0, 32, 104);
$pdf->SetLineWidth(0.3);
// La hoja membretada incluye el título de asistencia; se reemplaza solo para este reporte.
$pdf->SetFillColor(255, 255, 255);
$pdf->Rect(29, 37, 157, 19, 'F');
$pdf->SetDrawColor(0, 32, 104);
$pdf->Line(30, 38, 186, 38);
$pdf->SetFont('helvetica', 'B', 15);
$pdf->SetTextColor(0, 32, 104);
$pdf->SetXY(30, 43);
$pdf->Cell(156, 7, 'PLANILLA DE CARGA HORARIA', 0, 0, 'C');
$pdf->Line(30, 55, 186, 55);
$contentTopOffset = 14;

$columns = array(array(33, 55, 'PASANTE', $fullName), array(88, 38, 'CARNET', $documentCode), array(126, 57, 'CARRERA', $careerName));
$pdf->SetFont('helvetica', 'B', 8);
$infoHeight = 12;
foreach ($columns as $column) {
    $infoHeight = max($infoHeight, 6 + $pdf->getStringHeight($column[1], $column[3]));
}
$pdf->SetFillColor(236, 238, 246);
$pdf->SetDrawColor(223, 230, 241);
$pdf->RoundedRect(30, 44 + $contentTopOffset, 156, $infoHeight, 1.5, '1111', 'DF');
foreach ($columns as $column) {
    $pdf->SetTextColor(90, 98, 117);
    $pdf->SetFont('helvetica', 'B', 5);
    $pdf->SetXY($column[0], 46 + $contentTopOffset);
    $pdf->Cell($column[1], 3, $column[2], 0, 0, 'C');
    $pdf->SetTextColor(0, 32, 104);
    $pdf->SetFont('helvetica', 'B', 8);
    $pdf->SetXY($column[0], 49 + $contentTopOffset);
    $pdf->MultiCell($column[1], 4, $column[3], 0, 'C', false);
}
$summaryY = 44 + $contentTopOffset + $infoHeight + 3;
$pdf->SetFillColor(0, 32, 104);
$pdf->RoundedRect(30, $summaryY, 156, 18, 1.5, '1111', 'F');
$pdf->SetTextColor(255, 255, 255);
$pdf->SetFont('helvetica', 'B', 7);
$pdf->SetXY(30, $summaryY + 3);
$pdf->Cell(156, 4, 'HORAS ACUMULADAS', 0, 0, 'C');
$totalMinutes = max(0, (int) floor($totalSeconds / 60));
$totalLabel = intdiv($totalMinutes, 60) . ' h ' . str_pad((string) ($totalMinutes % 60), 2, '0', STR_PAD_LEFT) . ' min';
$pdf->SetFont('helvetica', 'B', 16);
$pdf->SetXY(30, $summaryY + 8);
$pdf->Cell(156, 7, $totalLabel, 0, 0, 'C');

$tableX = 30;
$tableY = $summaryY + 22;
$tableWidths = array(34, 41, 41, 40);
$tableHeaders = array('FECHA', 'ENTRADA', 'SALIDA', 'HORAS TRABAJADAS');
$pdf->SetFont('helvetica', 'B', 7);
$pdf->SetFillColor(0, 32, 104);
$pdf->SetTextColor(255, 255, 255);
$x = $tableX;
foreach ($tableHeaders as $index => $header) {
    $pdf->Rect($x, $tableY, $tableWidths[$index], 8, 'F');
    $pdf->MultiCell($tableWidths[$index], 8, $header, 0, 'C', false, 0, $x, $tableY, true, 0, false, true, 8, 'M');
    $x += $tableWidths[$index];
}
$tableY += 8;
$pdf->SetFont('helvetica', '', 8);
$pdf->SetTextColor(26, 27, 34);
foreach ($attendanceRows as $index => $record) {
    // Evita que una fila se corte o se imprima sobre el membrete de la página siguiente.
    if ($tableY + 7 > $pdf->getPageHeight() - 25) {
        $pdf->AddPage();
        $tableY = 42;
        $pdf->SetFont('helvetica', 'B', 7);
        $pdf->SetFillColor(0, 32, 104);
        $pdf->SetTextColor(255, 255, 255);
        $x = $tableX;
        foreach ($tableHeaders as $headerIndex => $header) {
            $pdf->Rect($x, $tableY, $tableWidths[$headerIndex], 8, 'F');
            $pdf->MultiCell($tableWidths[$headerIndex], 8, $header, 0, 'C', false, 0, $x, $tableY, true, 0, false, true, 8, 'M');
            $x += $tableWidths[$headerIndex];
        }
        $tableY += 8;
        $pdf->SetFont('helvetica', '', 8);
        $pdf->SetTextColor(26, 27, 34);
    }
    $seconds = (int) ($record['segundos_trabajados'] ?? 0);
    $fill = $index % 2 === 0;
    $pdf->SetFillColor($fill ? 248 : 255, $fill ? 249 : 255, $fill ? 253 : 255);
    $x = $tableX;
    foreach (array($record['fecha'], $record['entrada'], $record['salida'], format_hours_display($seconds)) as $column => $value) {
        $pdf->Rect($x, $tableY, $tableWidths[$column], 7, 'F');
        $pdf->MultiCell($tableWidths[$column], 7, $value, 0, 'C', false, 0, $x, $tableY, true, 0, false, true, 7, 'M');
        $x += $tableWidths[$column];
    }
    $tableY += 7;
}
if (!$attendanceRows) {
    $pdf->SetFillColor(248, 249, 253);
    $pdf->MultiCell(156, 8, 'No hay registros de marcación para este pasante.', 0, 'C', true, 0, $tableX, $tableY, true, 0, false, true, 8, 'M');
}
$pdf->Output('Carga_Horaria_' . $employeeId . '_' . $fromValue . '_al_' . $toValue . '.pdf', 'I');
