<?php
require_once __DIR__ . '/report_theme.php';
$escape = static fn($value) => htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
$pdf = new StazherReportPdf('L', 'mm', 'LETTER', true, 'UTF-8', false);
$pdf->SetCreator('STAZHER');
$pdf->SetTitle($recordsReportTitle);
$pdf->SetMargins(14, 43, 14);
$pdf->SetAutoPageBreak(true, 33);
$pdf->setPrintHeader(true);
$pdf->setPrintFooter(true);
$pdf->SetFooterMargin(8);
$pdf->AddPage();
$pdf->addLetterheadBackground();
$pdf->reportTitle($recordsReportTitle);
$pdf->SetY(55);
$pdf->SetFont('helvetica', '', 9);
$html = '<table width="100%" border="1" cellspacing="0" cellpadding="5"><thead><tr style="background-color:#0a3f78;color:#ffffff;font-weight:bold;">';
foreach (array(array('N°',5), array('Código de pasante',15), array('Nombre del pasante',25), array('Razón o motivo',40), array($recordsReportDateLabel,15)) as $index => $column) {
    $html .= '<th width="' . $column[1] . '%" style="border-bottom:1.2pt solid #ffce00;' . ($index === 0 ? 'background-color:#ffce00;color:#0a3f78;' : '') . '">' . $escape($column[0]) . '</th>';
}
$html .= '</tr></thead><tbody>';
foreach ($recordsReportRows as $index => $row) {
    $date = DateTimeImmutable::createFromFormat('!Y-m-d', $row['record_date']);
    $html .= '<tr nobr="true" style="background-color:' . ($index % 2 ? '#eaf3fa' : '#ffffff') . ';">';
    foreach (array(array($index + 1, 5), array($row['empid'],15), array(trim($row['lastname'] . ' ' . $row['firstname']),25), array($row['reason'],40), array($date ? $date->format('d/m/Y') : $row['record_date'],15)) as $columnIndex => $column) {
        $html .= '<td width="' . $column[1] . '%"' . ($columnIndex === 0 ? ' align="center" style="background-color:#fff8d9;color:#0a3f78;font-weight:bold;"' : '') . '>' . $escape($column[0]) . '</td>';
    }
    $html .= '</tr>';
}
if (!$recordsReportRows) { $html .= '<tr><td colspan="5" align="center">No hay registros disponibles.</td></tr>'; }
$html .= '</tbody></table>';
$pdf->writeHTML($html, true, false, true, false, '');
$pdf->Output($recordsReportFilename, $recordsReportDestination ?? 'I');
