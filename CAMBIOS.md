# Correcciones del sistema de pasantes

Se corrigieron los flujos principales sin modificar los registros existentes ni el esquema de la base de datos.

## Cambios

- El alta de pasantes guarda correctamente el área y el horario.
- Consultas preparadas en acceso, registro/edición de pasantes, fotografías, marcaciones, eliminaciones, consultas de detalle y certificados.
- La sesión detiene la ejecución si no existe un administrador válido; el acceso renueva el identificador de sesión.
- Protección CSRF en los formularios modificados de pasantes, asistencia, carga manual y perfil.
- PHP y MySQL utilizan la hora de Bolivia.
- La entrada y salida se guardan con transacciones y bloqueo del pasante para evitar envíos simultáneos duplicados.
- Las horas se calculan con el tiempo real entre entrada y salida. No se resta automáticamente una hora de descanso. Esta regla sigue el criterio de los acumulados anteriores y está pendiente de confirmar con el responsable.
- Marcaciones incompletas, con salida cero o con salida anterior a la entrada no acreditan horas.
- Totales y filas de detalle utilizan la misma regla, incluyendo certificados PDF y Word. Las horas adicionales previas permanecen incluidas en el acumulado.
- Los certificados conservan horas y minutos completos y muestran el área asignada.
- La carga manual valida fechas, orden de horas y evita marcaciones futuras.
- Las fotografías admiten JPG, PNG y WebP, como máximo 5 MB, con nombres aleatorios. La carpeta images bloquea solicitudes a scripts en Apache.
- Se bloquea la descarga directa de la carpeta database en Apache.
- El perfil permite conservar la contraseña dejando el campo vacío y deja de mostrar el hash.
- Se impide eliminar un pasante que tiene asistencias, para conservar su historial.
- Las respuestas de la pantalla de marcación y de los detalles modificados se insertan como texto.
- La marcación muestra errores de conexión y deshabilita los botones mientras se procesa.

## Validación

Ejecutar desde la raíz:

```powershell
C:\xampp\php\php.exe tmp/verify_hours.php
C:\xampp\php\php.exe tmp/verify_hours.php unauthenticated
```

La primera prueba comprueba cálculo de horas, fechas, salida completada y estados. La segunda debe terminar sin mostrar el mensaje de ejecución sin sesión.

En esta revisión pasaron 16 comprobaciones de lógica. Apache y MySQL no estaban disponibles, por lo que falta validar la integración, los formularios y la generación de documentos con XAMPP activo. Las reglas .htaccess requieren Apache 2.4 y AllowOverride habilitado.

## Trabajo pendiente

- Confirmar la política de descansos y horas fuera del horario.
- Revisar asignaciones antiguas de área/horario: no se intercambiaron masivamente porque no se puede distinguir automáticamente cuáles son correctas.
- Revisar y proteger los módulos de catálogos, permisos y faltas que conservan código anterior.
- Incorporar roles, estados del pasante, auditoría de correcciones y aprobación de salidas completadas.
- Registrar fechas reales de pasantía, firmante configurable, correlativo y registro de certificados emitidos. Los certificados aún usan created_on y el texto institucional existente.
- Añadir restricciones únicas, claves foráneas e índices tras revisar los datos existentes.
- Reemplazar la carga requerida almacenada como TIME si se necesitan más de 838 horas y 59 minutos.
- Definir la autenticación o el dispositivo autorizado para la marcación por código.
