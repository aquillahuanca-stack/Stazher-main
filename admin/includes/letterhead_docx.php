<?php
function applyInstitutionalLetterhead(string $generatedPath): void
{
    $templatePath = __DIR__ . '/../templates/hoja-membretado.docx';
    if (!is_file($templatePath)) {
        throw new RuntimeException('No se encontró la plantilla institucional.');
    }

    $generated = new ZipArchive();
    if ($generated->open($generatedPath) !== true) {
        throw new RuntimeException('No se pudo abrir el certificado generado.');
    }
    $documentXml = $generated->getFromName('word/document.xml');
    $generated->close();
    if ($documentXml === false) {
        throw new RuntimeException('El certificado generado no contiene un documento válido.');
    }

    $temporary = $generatedPath . '.template.docx';
    if (!copy($templatePath, $temporary)) {
        throw new RuntimeException('No se pudo cargar la plantilla institucional.');
    }
    $template = new ZipArchive();
    if ($template->open($temporary) !== true) {
        @unlink($temporary);
        throw new RuntimeException('No se pudo abrir la plantilla institucional.');
    }

    try {
        $reference = new DOMDocument();
        $content = new DOMDocument();
        if (!$reference->loadXML($template->getFromName('word/document.xml'), LIBXML_NONET)
            || !$content->loadXML($documentXml, LIBXML_NONET)) {
            throw new RuntimeException('No se pudo leer el contenido del certificado.');
        }
        $namespace = 'http://schemas.openxmlformats.org/wordprocessingml/2006/main';
        $referenceBody = $reference->getElementsByTagNameNS($namespace, 'body')->item(0);
        $contentBody = $content->getElementsByTagNameNS($namespace, 'body')->item(0);
        $section = $reference->getElementsByTagNameNS($namespace, 'sectPr')->item(0);
        $contentSection = $content->getElementsByTagNameNS($namespace, 'sectPr')->item(0);
        if (!$referenceBody || !$contentBody || !$section || !$contentSection) {
            throw new RuntimeException('La plantilla institucional no tiene una estructura válida.');
        }
        $section = $section->cloneNode(true);
        foreach (array('pgMar') as $layoutElement) {
            $sourceLayout = $contentSection->getElementsByTagNameNS($namespace, $layoutElement)->item(0);
            $templateLayout = $section->getElementsByTagNameNS($namespace, $layoutElement)->item(0);
            if ($sourceLayout && $templateLayout) {
                $section->replaceChild($reference->importNode($sourceLayout, true), $templateLayout);
            }
        }
        $pageSize = $section->getElementsByTagNameNS($namespace, 'pgSz')->item(0);
        if (!$pageSize) {
            throw new RuntimeException('La plantilla no tiene tamaño de página definido.');
        }
        // Carta vertical: 8.5 x 11 pulgadas, expresadas en twips.
        $pageSize->setAttributeNS($namespace, 'w:w', '12240');
        $pageSize->setAttributeNS($namespace, 'w:h', '15840');
        $pageSize->removeAttributeNS($namespace, 'orient');
        while ($referenceBody->firstChild) {
            $referenceBody->removeChild($referenceBody->firstChild);
        }
        foreach ($contentBody->childNodes as $child) {
            if ($child instanceof DOMElement && $child->localName !== 'sectPr') {
                $referenceBody->appendChild($reference->importNode($child, true));
            }
        }
        $referenceBody->appendChild($section);
        if (!$template->addFromString('word/document.xml', $reference->saveXML())) {
            throw new RuntimeException('No se pudo aplicar la plantilla al certificado.');
        }
    } finally {
        $template->close();
    }
    if (!copy($temporary, $generatedPath)) {
        @unlink($temporary);
        throw new RuntimeException('No se pudo guardar el certificado con membrete.');
    }
    unlink($temporary);
}
