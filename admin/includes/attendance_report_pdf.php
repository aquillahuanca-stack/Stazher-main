<?php
require_once __DIR__ . '/report_theme.php';
$escape = static function ($value) { return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8'); };
$durationText = static function ($seconds) {
    $minutes = (int) floor($seconds / 60);
    return intdiv($minutes, 60) . ' h. ' . str_pad((string) ($minutes % 60), 2, '0', STR_PAD_LEFT) . ' min.';
};
$interns = array();
$totalSeconds = 0;
$reviewCount = 0;
$assignedCount = 0;
foreach ($attendanceReportRows as $row) {
    $interns[$row['intern_id']] = true;
    if ((int) $row['duration_seconds'] >= 0) { $totalSeconds += (int) $row['duration_seconds']; }
    else { $reviewCount++; }
    if ((int) $row['status2'] === 3) { $assignedCount++; }
}
$pdf = new StazherReportPdf('L', PDF_UNIT, 'LETTER', true, 'UTF-8', false);
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetTitle('Reporte de asistencia de pasantes');
$pdf->SetMargins(12, 42, 12);
$pdf->SetAutoPageBreak(true, 28);
$pdf->setPrintHeader(true);
$pdf->setPrintFooter(true);
$pdf->SetFooterMargin(8);
$pdf->SetFont('helvetica', '', 8);
$pdf->AddPage();
$pdf->addLetterheadBackground();
$pdf->SetTextColor(23, 63, 104);
$pdf->SetFont('helvetica', 'B', 14);
$pdf->SetXY(12, 34);
$pdf->Cell(273, 7, 'REPORTE DE ASISTENCIA', 0, 0, 'C');
$pdf->SetTextColor(0, 0, 0);
$pdf->SetFont('helvetica', '', 8);
$pdf->SetY(44);
$heading = '<table width="100%" cellpadding="3" style="background-color:#edf3f8;color:#173f68;font-size:8pt;"><tr>'
    . '<td width="39%" nowrap="nowrap"><b>Periodo:</b> ' . htmlspecialchars($attendanceReportPeriod, ENT_QUOTES, 'UTF-8') . '</td>'
    . '<td width="14%" nowrap="nowrap"><b>Pasantes:</b> ' . count($interns) . '</td>'
    . '<td width="24%" nowrap="nowrap"><b>Marcaciones:</b> ' . count($attendanceReportRows) . '</td>'
    . '<td width="23%" nowrap="nowrap"><b>Total de horas:</b> ' . $durationText($totalSeconds) . '</td></tr></table><br>';
if ($assignedCount) {
    $heading .= '<span style="color:#637589;">* Salida completada según el horario asignado (' . $assignedCount . ' registros).</span><br>';
}
if ($reviewCount) {
    $heading .= '<span style="color:#875a12;">' . $reviewCount . ' registros tienen salida anterior a la entrada: se incluyen para revisión y no suman al total.</span><br>';
}
$pdf->writeHTML($heading, true, false, true, false, '');
$tableLeft = 12;
$tableWidth = $pdf->getPageWidth() - ($tableLeft * 2);
$pageBottom = $pdf->getPageHeight() - 28;
$tableY = $pdf->GetY();
$columnSpecs = array(
    array(0.05, 'Nro.', 'C'), array(0.14, 'Identificador', 'C'), array(0.29, 'Pasante', 'L'),
    array(0.12, 'Fecha', 'C'), array(0.12, 'Entrada', 'C'), array(0.12, 'Salida', 'C'),
    array(0.16, 'Horas registradas', 'C')
);
$columnWidths = array_map(function ($spec) use ($tableWidth) { return $spec[0] * $tableWidth; }, $columnSpecs);
$drawTableHeader = function () use ($pdf, $columnSpecs, $columnWidths, $tableLeft, &$tableY) {
    $pdf->SetFont('helvetica', 'B', 7);
    $pdf->SetTextColor(255, 255, 255);
    $pdf->SetFillColor(23, 63, 104);
    $pdf->SetDrawColor(35, 50, 67);
    $pdf->SetLineWidth(0.25);
    $x = $tableLeft;
    foreach ($columnSpecs as $i => $spec) {
        $pdf->Rect($x, $tableY, $columnWidths[$i], 8, 'DF');
        $pdf->MultiCell($columnWidths[$i], 8, $spec[1], 0, 'C', false, 0, $x, $tableY, true, 0, false, true, 8, 'M', true);
        $x += $columnWidths[$i];
    }
    $tableY += 8;
};
$drawTableHeader();
$number = 0;
foreach ($attendanceReportRows as $row) {
    $number++;
    $date = DateTimeImmutable::createFromFormat('!Y-m-d', $row['date']);
    $hasExit = !empty($row['time_out']) && $row['time_out'] !== '00:00:00';
    $hours = $hasExit ? $durationText((int) $row['duration_seconds']) : 'Sin salida registrada';
    $values = array(
        (string) $number, (string) $row['employee_id'], trim($row['lastname'] . ' ' . $row['firstname']),
        $date ? $date->format('d/m/Y') : $row['date'], substr($row['time_in'], 0, 5),
        $hasExit ? substr($row['time_out'], 0, 5) . ((int) $row['status2'] === 3 ? ' *' : '') : 'Pendiente', $hours
    );
    $pdf->SetFont('helvetica', '', 7);
    $rowHeight = 7;
    foreach ($values as $i => $value) {
        $rowHeight = max($rowHeight, $pdf->getStringHeight($columnWidths[$i] - 2, $value) + 1);
    }
    if ($tableY + $rowHeight > $pageBottom) {
        $pdf->AddPage();
        $pdf->SetY(42);
        $tableY = $pdf->GetY();
        $drawTableHeader();
    }
    $rowColor = ($number % 2 === 0) ? array(243, 247, 251) : array(255, 255, 255);
    $x = $tableLeft;
    foreach ($values as $i => $value) {
        $pdf->SetFillColor($rowColor[0], $rowColor[1], $rowColor[2]);
        $pdf->SetDrawColor(35, 50, 67);
        $pdf->SetLineWidth(0.25);
        $pdf->SetTextColor(25, 33, 46);
        $pdf->SetFont('helvetica', '', 7);
        $pdf->Rect($x, $tableY, $columnWidths[$i], $rowHeight, 'DF');
        $pdf->MultiCell($columnWidths[$i], $rowHeight, $value, 0, $columnSpecs[$i][2], false, 0, $x, $tableY, true, 0, false, true, $rowHeight, 'M', true);
        $x += $columnWidths[$i];
    }
    $tableY += $rowHeight;
}
if (!$number) {
    $emptyText = 'No hay marcaciones registradas en el período seleccionado.';
    $emptyHeight = 9;
    $pdf->SetFillColor(248, 249, 253);
    $pdf->SetDrawColor(35, 50, 67);
    $pdf->SetLineWidth(0.25);
    $pdf->SetTextColor(99, 117, 137);
    $pdf->SetFont('helvetica', '', 8);
    $pdf->Rect($tableLeft, $tableY, $tableWidth, $emptyHeight, 'DF');
    $pdf->MultiCell($tableWidth, $emptyHeight, $emptyText, 0, 'C', false, 0, $tableLeft, $tableY, true, 0, false, true, $emptyHeight, 'M', true);
}
$pdf->Output($attendanceReportFilename, $attendanceReportDestination);
