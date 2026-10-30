# Informe del proyecto: Gestión de Pasantes

## 1. Datos generales

- Nombre del proyecto: Gestión de Pasantes
- Tipo: Sistema web de control de asistencia y gestión de pasantes
- Tecnologías: PHP, MySQL, JavaScript, Bootstrap, TCPDF
- Entorno recomendado: XAMPP / Apache + MySQL
- Objetivo principal: Automatizar la gestión de asistencia, horarios y reportes del personal en prácticas.

## 2. Descripción del problema

La administración de pasantes en instituciones públicas o privadas suele requerir un seguimiento constante de asistencias, horarios y cumplimiento de actividades. En muchos casos, este proceso se realiza de forma manual, lo que provoca errores, retrasos y dificultad para consolidar información.

El proyecto Gestión de Pasantes responde a esta necesidad mediante un sistema centralizado que registra marcaciones de entrada y salida, almacena información del pasante, organiza horarios y genera reportes útiles para tomar decisiones.

## 3. Objetivos

### Objetivo general
Desarrollar un sistema informático para controlar la asistencia de pasantes y apoyar la administración de recursos humanos de la institución.

### Objetivos específicos
- Registrar ingreso y salida del personal en prácticas.
- Gestionar la información personal y académica de cada pasante.
- Definir horarios y reglas de asistencia.
- Conservar datos históricos de asistencia.
- Generar reportes para control y auditoría.

## 4. Alcance funcional

El sistema contempla las siguientes funciones:

### 4.1 Administración de usuarios
- Inicio de sesión para administrador.
- Diferenciación de roles y permisos.
- Control de acceso al panel administrativo.

### 4.2 Gestión de pasantes
- Registro de pasantes.
- Edición de datos personales.
- Asignación de área, cargo y horario.
- Búsqueda y filtrado por campo.

### 4.3 Control de asistencia
- Registro de entrada.
- Registro de salida.
- Validación de registros duplicados.
- Control de días con tardanza o retiro anticipado.

### 4.4 Configuración del sistema
- Horarios.
- Áreas.
- Instituciones.
- Carreras.
- Modalidades.

### 4.5 Reportes
- Reportes diarios.
- Reportes de pasantes.
- Consolidado de horas.
- Visualización de cumplimiento de turnos.

## 5. Arquitectura técnica

El sistema sigue una estructura sencilla y funcional basada en PHP y base de datos relacional:

- Frontend: HTML, CSS, Bootstrap, JavaScript.
- Backend: PHP.
- Base de datos: MySQL.
- Reportes: TCPDF.
- Patrón de acceso: páginas de control con autenticación y validaciones del lado del servidor.

## 6. Estructura del proyecto

Los principales archivos y carpetas del proyecto incluyen:

- `index.php`: acceso público para asistencia.
- `admin/index.php`: acceso administrativo.
- `attendance.php`: proceso de registro de asistencia.
- `admin/home.php`: panel principal.
- `admin/employee.php`: gestión de pasantes.
- `admin/attendance.php`: control de asistencia administrativa.
- `database/stazher.sql`: esquema de la base de datos.

## 7. Análisis de funcionalidades principales

### 7.1 Registro de entrada y salida
En el flujo principal del sistema, cada pasante ingresa un código para registrar entrada o salida. El backend valida:

- si el código existe,
- si el registro del día ya existe,
- si el empleado no ha marcado salida sin antes entrar,
- si la hora registrada es válida según el horario.

### 7.2 Validación de asistencia
El sistema clasifica las asistencias con funciones de cálculo de horas y estados, lo que permite distinguir:

- asistencia puntual,
- tardanza,
- salida anticipada,
- ausencia o retraso no registrado.

### 7.3 Administración de datos
El administrador puede mantener información referente a:

- personales,
- académicos,
- laborales,
- institucionales,
- de horarios.

Esto permite que la gestión del pasante sea consistente y evitar información dispersa.

## 8. Verificación técnica realizada

Se ejecutó una validación básica de integridad sintáctica en los archivos principales del proyecto con PHP. El resultado fue positivo:

- `attendance.php`: sin errores de sintaxis.
- `admin/home.php`: sin errores de sintaxis.
- `admin/index.php`: sin errores de sintaxis.

Esto indica que la base del sistema está consistente a nivel de carga y estructura PHP principal.

## 9. Fortalezas del sistema

- Interfaz clara y enfocada en operaciones rápidas.
- Gestión centralizada de asistencias y pasantes.
- Reducción del trabajo manual.
- Registro histórico útil para evaluación y auditoría.
- Generación de reportes para administración.

## 10. Limitaciones y riesgos

- La seguridad requiere mejores políticas en producción.
- El sistema dependen de la base de datos y del servidor local.
- Requiere capacitación para que los usuarios manejen adecuadamente la interfaz.
- La documentación técnica puede mejorarse para facilitar mantenimiento futuro.

## 11. Recomendaciones

1. Cambiar las credenciales por defecto antes de desplegar en producción.
2. Configurar autenticación segura y HTTPS.
3. Realizar copias de seguridad periódicas.
4. Mejorar la trazabilidad de los cambios realizados por el administrador.
5. Agregar procesos de validación y auditoría para la edición de registros.
6. Incluir respaldo de usuarios y permisos por rol.

## 12. Conclusión

Gestión de Pasantes es una solución funcional y adecuada para controlar la asistencia y administración de pasantes en un entorno institucional. El sistema combina gestión operativa, seguimiento de desempeño y reportes básicos con una estructura accesible para usuarios no técnicos. Aun cuando puede mejorar en seguridad y escalabilidad, cumple de manera efectiva con su propósito original y representa una base sólida para aplicaciones de gestión de personal en prácticas.
