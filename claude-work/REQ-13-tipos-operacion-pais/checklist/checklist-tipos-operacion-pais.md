# Checklist — REQ-13: Tipos de operación y tipos de cuenta configurables por país

- [ ] TASK-001: 3 migraciones (side en PM, create account_types, sender_op_type + sender_phone + recipient_name en transactions)
- [ ] TASK-002: Actualizar PaymentMethod model + crear AccountType model
- [ ] TASK-003: PaymentMethodSeeder (6 países + side + fields_required) + AccountTypeSeeder
- [ ] TASK-004: Endpoints API (getPaymentMethods con side, getAccountTypes) + CountryController AccountType CRUD + rutas
- [ ] TASK-005: countries/show.blade.php — campo side en PM form + sección AccountType CRUD
- [ ] TASK-006: transactions/create.blade.php — selector sender op type + campos dinámicos por fields_required
- [ ] TASK-007: Ejecutar seeders y verificar
