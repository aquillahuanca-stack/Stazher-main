# Manual de uso del sistema: Gestión de Pasantes

## 1. Introducción

Gestión de Pasantes es un sistema web desarrollado en PHP y MySQL para gestionar la asistencia, horarios, pasantes y reportes de personal en prácticas de la Agencia Boliviana de Correos. Su propósito principal es registrar la entrada y salida de cada pasante, controlar cumplimiento de horarios, mantener información laboral y generar reportes para administración y recursos humanos.

## 2. Objetivo del sistema

- Registrar de forma ordenada la asistencia diaria.
- Controlar horarios y tiempos de permanencia.
- Mantener la información de pasantes y su contexto institucional.
- Generar indicadores de cumplimiento y reportes de asistencia.
- Facilitar la administración de personal en prácticas.

## 3. Usuarios del sistema

### 3.1 Administrador
El administrador tiene acceso a la gestión completa del sistema:

- Inicio del panel de control.
- Registro y edición de pasantes.
- Configuración de horarios y áreas.
- Registro de asistencia manual.
- Consulta de reportes y exportación.

### 3.2 Usuario de asistencia
El usuario de servicio o pasante accede al sistema para marcar entrada y salida mediante código identificador.

## 4. Requisitos para usar el sistema

- Navegador web actualizado: Chrome, Edge, Firefox.
- Servidor local con PHP y MySQL (recomendado XAMPP).
- Base de datos importada desde `database/stazher.sql`.
- Acceso al proyecto desde la carpeta del servidor web.

## 5. Instalación y configuración

1. Instalar XAMPP o un servidor similar.
2. Clonar o copiar la carpeta del proyecto en `htdocs`.
3. Crear la base de datos `stazher` en MySQL.
4. Importar el archivo `database/stazher.sql`.
5. Configurar la conexión en `conn.php` si es necesario.
6. Abrir el navegador y acceder a:
   - `http://localhost/Stazher-main/index.php` para asistencia pública.
   - `http://localhost/Stazher-main/admin/index.php` para panel administrativo.

## 6. Inicio de sesión

### 6.1 Acceso administrativo
En la pantalla de login del módulo administrativo se debe ingresar:

- Usuario: `admin`
- Contraseña: `4gBc_P4s4nT3s`

> La contraseña registrada en la base de datos puede variar si se actualiza desde la administración.

## 7. Módulo de asistencia pública

La pantalla principal del sistema permite registrar asistencia por código del pasante.

### 7.1 Flujo de entrada
1. Ingresar el código del pasante.
2. Seleccionar la acción “Entrada”.
3. Confirmar la operación.
4. El sistema valida que no exista una marcación duplicada para el día.

### 7.2 Flujo de salida
1. Ingresar el código del pasante.
2. Seleccionar “Salida”.
3. El sistema valida que se haya registrado previamente la entrada.
4. Se calcula la hora de salida y las horas trabajadas.

### 7.3 Reglas del sistema
- No se permite ingresar dos veces la misma entrada del mismo día.
- No se permite marcar salida si no existe una entrada registrada.
- Si el pasante llega tarde o sale antes de lo programado, el sistema marca el estado correspondiente.

## 8. Panel administrativo

Desde el panel administrativo se accede al menú principal con las siguientes opciones:

- Panel de control
- Lista de pasantes
- Asistencia
- Configuración
  - Horarios
  - Áreas
  - Instituciones
  - Carreras
  - Modalidad

## 9. Gestión de pasantes

### 9.1 Agregar pasante
1. Ingresar a la opción “Lista de pasantes”.
2. Hacer clic en “Nuevo pasante”.
3. Completar los datos personales, institución, horario, cargo y área.
4. Guardar la información.

### 9.2 Editar información
1. Buscar el pasante por nombre, apellido o código.
2. Seleccionar la acción editar.
3. Ajustar los campos necesarios.
4. Guardar los cambios.

### 9.3 Eliminar pasante
- Se debe confirmar la acción antes de aplicar la eliminación.
- Se recomienda no eliminar registros históricos si se requiere auditoría.

## 10. Gestión de horarios y reglas de asistencia

### 10.1 Horarios
La configuración de horarios permite definir hora de entrada y salida. Esto sirve para:

- comparar asistencia real vs. programación,
- detectar tardanzas,
- calcular horas semanales o diarias.

### 10.2 Áreas y cargos
El sistema permite clasificar a los pasantes por:

- área de trabajo,
- puesto o cargo,
- institución de estudio,
- carrera,
- modalidad de titulación.

## 11. Registro de asistencia

En la sección “Asistencia” el administrador puede:

- consultar registros del día,
- revisar asistencias por pasante,
- corregir errores manuales,
- imprimir reportes o consolidados.

## 12. Reportes

El sistema incluye reportes para:

- asistencia diaria,
- horas acumuladas,
- registro individual por pasante,
- cumplimiento de horarios,
- gestión de permisos o ausencias.

## 13. Buenas prácticas

- Mantener la base de datos actualizada y respaldada.
- Revisar la asistencia diariamente.
- Validar códigos antes de registrar entradas o salidas.
- Usar nombres y códigos únicos para cada pasante.
- Evitar duplicidad de registros horarios.

## 14. Recomendaciones de seguridad

- Cambiar credenciales por defecto en producción.
- Usar HTTPS en entornos reales.
- Limitar el acceso administrativo al personal autorizado.
- Realizar copias de seguridad periódicas.
- Mantener el sistema actualizado y revisar logs.

## 15. Solución de problemas comunes

### Error: usuario o contraseña incorrectos
- Verificar usuario y contraseña.
- Revisar que la base de datos esté importada correctamente.

### Código del pasante no encontrado
- Confirmar que el pasante exista en la base de datos.
- Revisar si el código se encuentra activo.

### Marcación duplicada
- Consultar al administrador.
- Revisar si la entrada ya fue registrada en el mismo día.

## 16. Conclusión

Stazher es una herramienta útil para la gestión operativa de pasantes, especialmente en entornos donde se requiere un control preciso de asistencia y cumplimiento de horarios. Su implementación mejora la trazabilidad de las entradas y salidas, acelera procesos administrativos y facilita la toma de decisiones basada en reportes.
