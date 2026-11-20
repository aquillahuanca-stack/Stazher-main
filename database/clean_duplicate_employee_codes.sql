-- Consolida fichas repetidas por employees.employee_id sin perder asistencia.
-- Se conserva la ficha con el ID interno menor y se respaldan las fichas retiradas.

USE stazher;

CREATE TABLE IF NOT EXISTS employees_duplicate_cleanup_backup LIKE employees;

CREATE TEMPORARY TABLE duplicate_employee_map AS
SELECT duplicate_row.id AS duplicate_id, canonical_row.id AS canonical_id
FROM employees AS duplicate_row
INNER JOIN (
    SELECT employee_id, MIN(id) AS canonical_id
    FROM employees
    GROUP BY employee_id
    HAVING COUNT(*) > 1
) AS duplicated_code ON duplicated_code.employee_id = duplicate_row.employee_id
INNER JOIN employees AS canonical_row ON canonical_row.id = duplicated_code.canonical_id
WHERE duplicate_row.id <> canonical_row.id;

START TRANSACTION;

INSERT IGNORE INTO employees_duplicate_cleanup_backup
SELECT employee.*
FROM employees AS employee
INNER JOIN duplicate_employee_map AS map ON map.duplicate_id = employee.id;

UPDATE absences AS record
INNER JOIN duplicate_employee_map AS map ON map.duplicate_id = record.employee_id
SET record.employee_id = map.canonical_id;

UPDATE attendance AS record
INNER JOIN duplicate_employee_map AS map ON map.duplicate_id = record.employee_id
SET record.employee_id = map.canonical_id;

UPDATE cashadvance AS record
INNER JOIN duplicate_employee_map AS map ON map.duplicate_id = record.employee_id
SET record.employee_id = map.canonical_id;

UPDATE licence AS record
INNER JOIN duplicate_employee_map AS map ON map.duplicate_id = record.employee_id
SET record.employee_id = map.canonical_id;

UPDATE overtime AS record
INNER JOIN duplicate_employee_map AS map ON map.duplicate_id = record.employee_id
SET record.employee_id = map.canonical_id;

UPDATE stazher_notifications AS record
INNER JOIN duplicate_employee_map AS map ON map.duplicate_id = record.employee_id
SET record.employee_id = map.canonical_id;

DELETE employee
FROM employees AS employee
INNER JOIN duplicate_employee_map AS map ON map.duplicate_id = employee.id;

COMMIT;

ALTER TABLE employees
    ADD CONSTRAINT uq_employees_employee_id UNIQUE (employee_id);
