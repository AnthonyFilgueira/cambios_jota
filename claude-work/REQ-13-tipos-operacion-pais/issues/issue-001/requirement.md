# TASK-001 — 3 migraciones

## 1a — add_side_to_payment_methods_table
Agregar columna `side` enum('sender','recipient','both') con default 'recipient' a la tabla payment_methods.

## 1b — create_account_types_table
Crear tabla account_types con: id, country_id (FK), name, code, active, timestamps. Unique en (country_id, code).

## 1c — add_sender_operation_type_to_transactions_table
- Agregar sender_operation_type (string nullable)
- Agregar sender_phone varchar(30) nullable
- Agregar recipient_name varchar(150) nullable
- Cambiar recipient_account_type de enum a string nullable (para soportar cualquier código)

## Commit objetivo
feat(db): add side to payment_methods, create account_types, add sender_operation_type
