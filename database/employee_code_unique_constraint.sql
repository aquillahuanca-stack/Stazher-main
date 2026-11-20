-- Protección contra códigos de pasante repetidos.
-- Ejecute este archivo solo después de comprobar que no existan duplicados.
-- El sistema debe conservar un único código por cada pasante.

ALTER TABLE employees
    ADD CONSTRAINT uq_employees_employee_id UNIQUE (employee_id);
