<?php
$leftMargin = 12;
$contentWidth = $pdf->getPageWidth() - ($leftMargin * 2);
$pdf->SetTextColor(23, 63, 104);
$pdf->SetFont('helvetica', 'B', 14);
$pdf->SetXY($leftMargin, 38);
$pdf->Cell($contentWidth, 7, 'LISTA GENERAL DE PASANTES', 0, 0, 'C');
$pdf->SetTextColor(99, 117, 137);
$pdf->SetFont('helvetica', '', 8);
$pdf->SetXY($leftMargin, 46);
$pdf->Cell($contentWidth, 5, 'Reporte generado el ' . date('d/m/Y'), 0, 0, 'R');
$pdf->SetDrawColor(212, 220, 231);
$pdf->SetLineWidth(0.3);
$pdf->Line($leftMargin, 53, $leftMargin + $contentWidth, 53);
$pdf->SetTextColor(0, 0, 0);
$pdf->SetFont('helvetica', '', 8);
$pdf->SetY(58);
?>