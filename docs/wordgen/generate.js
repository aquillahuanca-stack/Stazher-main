const fs = require('fs');
const { Document, Packer, Paragraph, TextRun, HeadingLevel, AlignmentType } = require('docx');

function buildManual() {
  return new Document({
    creator: 'GitHub Copilot',
    title: 'Manual de uso Stazher',
    description: 'Manual de uso del sistema Stazher',
    sections: [{
      properties: {},
      children: [
        new Paragraph({ text: 'Manual de uso del sistema Stazher', heading: HeadingLevel.TITLE }),
        new Paragraph({ text: '1. Introducción', heading: HeadingLevel.HEADING_1 }),
        new Paragraph('Stazher es un sistema web desarrollado en PHP y MySQL para gestionar la asistencia, horarios, pasantes y reportes del personal en prácticas.'),
        new Paragraph({ text: '2. Objetivo', heading: HeadingLevel.HEADING_1 }),
        new Paragraph('• Registrar asistencia diaria.'),
        new Paragraph('• Controlar horarios y permanencia.'),
        new Paragraph('• Mantener información de pasantes.'),
        new Paragraph('• Generar reportes.'),
        new Paragraph({ text: '3. Requisitos', heading: HeadingLevel.HEADING_1 }),
        new Paragraph('• XAMPP o servidor similar.'),
        new Paragraph('• PHP y MySQL.'),
        new Paragraph('• Navegador actualizado.'),
        new Paragraph('• Base de datos importada desde database/stazher.sql.'),
        new Paragraph({ text: '4. Instalación', heading: HeadingLevel.HEADING_1 }),
        new Paragraph('1. Copiar el proyecto a htdocs.'),
        new Paragraph('2. Crear la base de datos stazher.'),
        new Paragraph('3. Importar database/stazher.sql.'),
        new Paragraph('4. Ajustar conexión en conn.php.'),
        new Paragraph('5. Abrir http://localhost/Stazher-main/index.php.'),
        new Paragraph({ text: '5. Acceso administrativo', heading: HeadingLevel.HEADING_1 }),
        new Paragraph('Usuario: admin'),
        new Paragraph('Contraseña: 4gBc_P4s4nT3s'),
        new Paragraph({ text: '6. Módulo de asistencia', heading: HeadingLevel.HEADING_1 }),
        new Paragraph('• Ingresar código del pasante.'),
        new Paragraph('• Elegir Entrada o Salida.'),
        new Paragraph('• El sistema valida registros duplicados.'),
        new Paragraph({ text: '7. Administración', heading: HeadingLevel.HEADING_1 }),
        new Paragraph('• Lista de pasantes.'),
        new Paragraph('• Horarios.'),
        new Paragraph('• Áreas.'),
        new Paragraph('• Instituciones.'),
        new Paragraph('• Carreras.'),
        new Paragraph('• Modalidad.'),
        new Paragraph({ text: '8. Buenas prácticas', heading: HeadingLevel.HEADING_1 }),
        new Paragraph('• Respaldar la base de datos.'),
        new Paragraph('• Revisar asistencia diaria.'),
        new Paragraph('• No duplicar registros.'),
        new Paragraph('• Usar códigos únicos.'),
        new Paragraph({ text: '9. Conclusión', heading: HeadingLevel.HEADING_1 }),
        new Paragraph('Stazher es una herramienta útil para controlar asistencia y tiempos de pasantes.')
      ]
    }]
  });
}

function buildInforme() {
  return new Document({
    creator: 'GitHub Copilot',
    title: 'Informe del proyecto Stazher',
    description: 'Informe del proyecto Stazher',
    sections: [{
      properties: {},
      children: [
        new Paragraph({ text: 'Informe del proyecto Stazher', heading: HeadingLevel.TITLE }),
        new Paragraph({ text: '1. Datos generales', heading: HeadingLevel.HEADING_1 }),
        new Paragraph('• Nombre: Stazher'),
        new Paragraph('• Tipo: Sistema web de control de asistencia y gestión de pasantes'),
        new Paragraph('• Tecnologías: PHP, MySQL, JavaScript, Bootstrap, TCPDF'),
        new Paragraph({ text: '2. Descripción del problema', heading: HeadingLevel.HEADING_1 }),
        new Paragraph('La gestión manual de pasantes provoca errores y retrasos en la toma de decisiones.'),
        new Paragraph({ text: '3. Objetivos', heading: HeadingLevel.HEADING_1 }),
        new Paragraph('• Registrar entrada y salida.'),
        new Paragraph('• Gestionar información del pasante.'),
        new Paragraph('• Organizar horarios y áreas.'),
        new Paragraph('• Generar reportes.'),
        new Paragraph({ text: '4. Alcance funcional', heading: HeadingLevel.HEADING_1 }),
        new Paragraph('• Administración de usuarios'),
        new Paragraph('• Gestión de pasantes'),
        new Paragraph('• Control de asistencia'),
        new Paragraph('• Configuración de horarios'),
        new Paragraph('• Reportes'),
        new Paragraph({ text: '5. Arquitectura técnica', heading: HeadingLevel.HEADING_1 }),
        new Paragraph('• Frontend: HTML, CSS, JavaScript'),
        new Paragraph('• Backend: PHP'),
        new Paragraph('• Base de datos: MySQL'),
        new Paragraph('• Reportes: TCPDF'),
        new Paragraph({ text: '6. Fortalezas', heading: HeadingLevel.HEADING_1 }),
        new Paragraph('• Interfaz clara'),
        new Paragraph('• Registro centralizado'),
        new Paragraph('• Reducción de trabajo manual'),
        new Paragraph('• Trazabilidad de datos'),
        new Paragraph({ text: '7. Limitaciones', heading: HeadingLevel.HEADING_1 }),
        new Paragraph('• Seguridad necesita mejor configuración'),
        new Paragraph('• Requiere capacitación del personal'),
        new Paragraph('• Necesita respaldos periódicos'),
        new Paragraph({ text: '8. Recomendaciones', heading: HeadingLevel.HEADING_1 }),
        new Paragraph('• Cambiar credenciales por defecto'),
        new Paragraph('• Realizar copias de seguridad'),
        new Paragraph('• Mejorar seguridad y auditoría'),
        new Paragraph({ text: '9. Conclusión', heading: HeadingLevel.HEADING_1 }),
        new Paragraph('Stazher es una solución funcional y útil para la administración de asistencia y gestión de pasantes.')
      ]
    }]
  });
}

(async () => {
  const manual = buildManual();
  const informe = buildInforme();
  const manualBuffer = await Packer.toBuffer(manual);
  const informeBuffer = await Packer.toBuffer(informe);
  fs.writeFileSync('C:/xampp/htdocs/Stazher-main/docs/manual_de_uso_stazher.docx', manualBuffer);
  fs.writeFileSync('C:/xampp/htdocs/Stazher-main/docs/informe_proyecto_stazher.docx', informeBuffer);
  console.log('Word files generated:');
  console.log('C:/xampp/htdocs/Stazher-main/docs/manual_de_uso_stazher.docx');
  console.log('C:/xampp/htdocs/Stazher-main/docs/informe_proyecto_stazher.docx');
})();
