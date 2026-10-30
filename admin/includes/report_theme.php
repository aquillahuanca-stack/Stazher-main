<?php
require_once __DIR__ . '/../../tcpdf/tcpdf.php';

class StazherReportPdf extends TCPDF
{
    public function __construct($orientation = 'P', $unit = 'mm', $format = 'LETTER', $unicode = true, $encoding = 'UTF-8', $diskcache = false, $pdfa = false)
    {
        // Forzar carta en todas las salidas que usan esta clase, aunque el llamador indique otro formato.
        parent::__construct($orientation, $unit, 'LETTER', $unicode, $encoding, $diskcache, $pdfa);
    }

    public function Header()
    {
        if ($this->getPage() === 1) {
            return;
        }
        $this->addLetterheadBackground();
    }

    public function addLetterheadBackground(): void
    {
        $portrait = $this->getPageWidth() < $this->getPageHeight();
        $asset = __DIR__ . '/../../images/' . ($portrait ? 'hoja menbretado_page-0001.jpg' : 'hoja-membretado-horizontal.jpg');
        // El fondo ocupa físicamente la hoja carta completa en ambas orientaciones.
        if (!is_file($asset)) {
            return;
        }
        $width = $this->getPageWidth();
        $height = $this->getPageHeight();
        $x = $this->GetX();
        $y = $this->GetY();
        $autoPageBreak = $this->getAutoPageBreak();
        $breakMargin = $this->getBreakMargin();
        $this->SetAutoPageBreak(false, 0);
        $this->Image($asset, 0, 0, $width, $height, 'JPEG');
        $this->SetAutoPageBreak($autoPageBreak, $breakMargin);
        $this->SetXY($x, $y);
    }

    public function reportTitle(string $title, float $fontSize = 16): void
    {
        $margins = $this->getMargins();
        $width = $this->getPageWidth() - $margins['left'] - $margins['right'];
        $this->SetTextColor(10, 63, 120);
        $this->SetFont('helvetica', 'B', $fontSize);
        $this->SetXY($margins['left'], $margins['top']);
        $this->Cell($width, 8, $title, 0, 0, 'C');
        $this->SetFont('helvetica', '', 8);
        $this->SetTextColor(0, 0, 0);
        $this->SetY($margins['top'] + 12);
    }

    public function Footer()
    {
        $this->SetTextColor(99, 117, 137);
        $this->SetFont('helvetica', '', 7);
        $this->SetXY(12, $this->getPageHeight() - 13);
        $this->Cell($this->getPageWidth() - 24, 3, $this->getAliasNumPage() . ' / ' . $this->getAliasNbPages(), 0, 0, 'C');
    }
}
