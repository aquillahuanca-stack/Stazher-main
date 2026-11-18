-- Protección contra marcaciones duplicadas.
-- Ejecute este archivo UNA SOLA VEZ con la base de datos stazher seleccionada.
-- Primero conserva la fila más antigua de cada pasante y fecha, luego elimina las repetidas.

START TRANSACTION;

CREATE TABLE IF NOT EXISTS attendance_duplicates_backup LIKE attendance;

INSERT INTO attendance_duplicates_backup
SELECT a.*
FROM attendance a
INNER JOIN (
    SELECT employee_id, date
    FROM attendance
    GROUP BY employee_id, date
    HAVING COUNT(*) > 1
) duplicated ON duplicated.employee_id = a.employee_id AND duplicated.date = a.date
WHERE NOT EXISTS (
    SELECT 1
    FROM attendance_duplicates_backup backup
    WHERE backup.id = a.id
);

-- Si una fila duplicada contiene una salida válida, se conserva en la primera fila del día.
UPDATE attendance kept
INNER JOIN (
    SELECT employee_id, date, MIN(id) AS kept_id,
           MAX(NULLIF(time_out, '00:00:00')) AS valid_time_out,
           MAX(status2) AS valid_status2,
           MAX(num_hr) AS valid_num_hr
    FROM attendance
    GROUP BY employee_id, date
    HAVING COUNT(*) > 1
) merged ON merged.kept_id = kept.id
SET kept.time_out = COALESCE(merged.valid_time_out, kept.time_out),
    kept.status2 = GREATEST(kept.status2, merged.valid_status2),
    kept.num_hr = GREATEST(kept.num_hr, merged.valid_num_hr);

DELETE duplicate
FROM attendance duplicate
INNER JOIN (
    SELECT employee_id, date, MIN(id) AS kept_id
    FROM attendance
    GROUP BY employee_id, date
    HAVING COUNT(*) > 1
) merged ON merged.employee_id = duplicate.employee_id AND merged.date = duplicate.date
WHERE duplicate.id <> merged.kept_id;

ALTER TABLE attendance
    ADD CONSTRAINT uq_attendance_employee_date UNIQUE (employee_id, date);

COMMIT;
