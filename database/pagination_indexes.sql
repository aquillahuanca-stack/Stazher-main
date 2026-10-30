-- Índices para la paginación y los filtros de asistencia.
-- Ejecute este archivo una sola vez en la base de datos stazher antes del despliegue.
CREATE INDEX idx_attendance_date_time_employee ON attendance (date, time_in, employee_id);
CREATE INDEX idx_attendance_employee_date ON attendance (employee_id, date);
