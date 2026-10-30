<?php
require_once __DIR__ . '/report_theme.php';

$pdf = new StazherReportPdf('P', 'mm', 'LETTER', true, 'UTF-8', false);
$pdf->SetCreator('STAZHER');
$pdf->SetTitle('Reporte de Carga Horaria');
$pdf->setPrintHeader(true);
$pdf->setPrintFooter(false);
$pdf->SetMargins(8, 8, 8);
$pdf->SetAutoPageBreak(true, 25);
$pdf->AddPage();
$pdf->addLetterheadBackground();
$pdf->SetTextColor(0, 32, 104);
$pdf->SetDrawColor(0, 32, 104);
$pdf->SetLineWidth(0.3);
$contentTopOffset = 8;

$pdf->SetFont('helvetica', 'B', 12);
$pdf->SetXY(8, 30 + $contentTopOffset);
$pdf->Cell(90, 6, 'RECURSOS HUMANOS');
$pdf->SetFont('helvetica', 'B', 6);
$pdf->SetXY(8, 36 + $contentTopOffset);
$pdf->Cell(90, 3, 'SISTEMA DE ASISTENCIA');
$pdf->SetFont('helvetica', 'B', 14);
$pdf->SetXY(99, 29 + $contentTopOffset);
$pdf->Cell(103, 7, 'Reporte de Carga Horaria', 0, 0, 'R');
$pdf->SetFont('helvetica', '', 7);
$pdf->SetXY(99, 36 + $contentTopOffset);
$pdf->Cell(103, 3, 'Emisión: ' . $emissionDate . ' | Periodo: ' . $workloadReportPeriod, 0, 0, 'R');
$pdf->SetDrawColor(0, 32, 104);
$pdf->Line(8, 41 + $contentTopOffset, 202, 41 + $contentTopOffset);

$columns = array(array(18, 70, 'PASANTE', $fullName), array(89, 35, 'CARNET', $documentCode), array(124, 69, 'CARRERA', $careerName));
$pdf->SetFont('helvetica', 'B', 8);
$infoHeight = 12;
foreach ($columns as $column) {
    $infoHeight = max($infoHeight, 6 + $pdf->getStringHeight($column[1], $column[3]));
}
$pdf->SetFillColor(236, 238, 246);
$pdf->SetDrawColor(223, 230, 241);
$pdf->RoundedRect(8, 44 + $contentTopOffset, 194, $infoHeight, 1.5, '1111', 'DF');
foreach ($columns as $column) {
    $pdf->SetTextColor(90, 98, 117);
    $pdf->SetFont('helvetica', 'B', 5);
    $pdf->SetXY($column[0], 46 + $contentTopOffset);
    $pdf->Cell($column[1], 3, $column[2]);
    $pdf->SetTextColor(0, 32, 104);
    $pdf->SetFont('helvetica', 'B', 8);
    $pdf->SetXY($column[0], 49 + $contentTopOffset);
    $pdf->MultiCell($column[1], 4, $column[3], 0, 'L', false);
}
$summaryY = 44 + $contentTopOffset + $infoHeight + 3;
$pdf->SetFillColor(0, 32, 104);
$pdf->RoundedRect(8, $summaryY, 194, 18, 1.5, '1111', 'F');
$pdf->SetTextColor(255, 255, 255);
$pdf->SetFont('helvetica', 'B', 7);
$pdf->SetXY(8, $summaryY + 3);
$pdf->Cell(194, 4, 'HORAS ACUMULADAS', 0, 0, 'C');
$totalMinutes = max(0, (int) floor($totalSeconds / 60));
$totalLabel = intdiv($totalMinutes, 60) . ' h ' . str_pad((string) ($totalMinutes % 60), 2, '0', STR_PAD_LEFT) . ' min';
$pdf->SetFont('helvetica', 'B', 16);
$pdf->SetXY(8, $summaryY + 8);
$pdf->Cell(194, 7, $totalLabel, 0, 0, 'C');

$escape = function ($value) { return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8'); };
$html = '<table border="0" cellpadding="5" cellspacing="0" width="100%"><tbody><tr>';
foreach (array(array('FECHA', 16), array('D&#205;A', 14), array('HORA DE ENTRADA', 24), array('HORA DE SALIDA', 21), array('HORAS TRABAJADAS', 25)) as $heading) {
    $html .= '<td width="' . $heading[1] . '%" bgcolor="#002068" align="center"><font color="#ffffff"><b>' . $heading[0] . '</b></font></td>';
}
$html .= '</tr>';
foreach ($attendanceRows as $index => $record) {
    $seconds = (int) ($record['segundos_trabajados'] ?? 0);
    $rowColor = $index % 2 ? '#f8f9fd' : '#ffffff';
    $html .= '<tr nobr="true">';
    foreach (array($record['fecha'], $record['dia'], $record['entrada'], $record['salida'], format_hours_display($seconds)) as $value) {
        $html .= '<td bgcolor="' . $rowColor . '" style="border-bottom:0.3pt solid #e1e5ee;">' . $escape($value) . '</td>';
    }
    $html .= '</tr>';
}
if (!$attendanceRows) {
    $html .= '<tr><td colspan="5" bgcolor="#f8f9fd" align="center">No hay registros de marcaci&#243;n para este pasante.</td></tr>';
}
$html .= '</tbody></table>';
$pdf->SetTextColor(26, 27, 34);
$pdf->SetFont('helvetica', '', 8);
$pdf->SetXY(8, $summaryY + 17);
$pdf->writeHTML($html, true, false, true, false, '');
$pdf->Output('Carga_Horaria_' . $employeeId . '_' . $fromValue . '_al_' . $toValue . '.pdf', 'I');
