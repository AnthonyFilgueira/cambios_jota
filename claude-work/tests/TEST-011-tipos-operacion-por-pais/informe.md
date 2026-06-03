# TEST-011 — REQ-13: Tipos de Operación y Tipos de Cuenta por País
> Fecha: 2026-05-28 | Tipo: Manual (funcional) + PHP Suite OK

---

## Resultado global

| Componente | Estado |
|------------|--------|
| PHP Suite (38 tests) | PASS ✅ |
| Seeders | PASS ✅ — 14 métodos, 12 tipos de cuenta |
| BD correcta | PASS ✅ — todos los `side` y `fields_required` correctos |
| Endpoints API | PASS ✅ (verificados con tinker) |
| Vista create.blade.php | PASS ✅ — campos condicionales implementados |
| TransactionController store() | PASS ✅ — validación actualizada, rama muerta eliminada |
| TransactionController update() | PASS ✅ — validación actualizada a nuevos campos |

---

## Verificaciones de BD realizadas

### PaymentMethods (14 registros)

| País | Método | side | fields_required |
|------|--------|------|----------------|
| Perú | Transferencia Bancaria | sender | bank, account_number, account_type |
| Perú | Agente | sender | bank |
| Perú | Yape | sender | phone |
| Perú | Plin | sender | phone |
| Venezuela | Transferencia Bancaria | recipient | bank, account_number, account_type |
| Venezuela | Pago Móvil | recipient | phone, bank |
| Chile | Transferencia Bancaria | sender | bank, account_number, account_type |
| Colombia | Transferencia Bancaria | sender | bank, account_number, account_type |
| Colombia | Nequi | sender | phone |
| Colombia | Daviplata | sender | phone |
| Argentina | Transferencia Bancaria | sender | bank, account_number, account_type |
| Argentina | CVU / Alias | sender | account_number |
| Brasil | Transferencia Bancaria | sender | bank, account_number, account_type |
| Brasil | PIX | sender | phone |

### AccountTypes (12 registros)

Ahorro + Corriente para los 6 países activos (PE, VE, CL, CO, AR, BR).

---

## Correcciones encontradas durante los tests

### Fix: nequi/daviplata y cvu_alias tenían side=recipient

**Causa:** `MultiCorridorSeeder` y `MoreCorridorsSeeder` los crearon sin campo `side` → quedaron con default `recipient`.
**Solución:** Agregados a `PaymentMethodSeeder` con `side=sender` y `fields_required` correctos. `updateOrCreate` los corrigió.
**Commit:** `33e05f1`

### Fix: store() tenía rama muerta `$operationType === 'pago_movil'`

**Causa:** La validación fue actualizada pero se dejó el bloque condicional que nullificaba campos.
**Solución:** Eliminado el bloque (todos los campos son nullable por diseño).
**Commit:** `ca5815c`

### Fix: update() tenía validación `in:transferencia,pago_movil` (enum viejo)

**Causa:** El método `update()` no fue actualizado junto con `store()` en iteraciones anteriores.
**Solución:** Reemplazado con la misma validación flexible de `store()`.
**Commit:** `ca5815c`

---

## Checklist funcional manual (basado en plan REQ-13)

- [x] API `GET /transactions/payment-methods?country_id=X&side=sender` retorna métodos del remitente
- [x] API `GET /transactions/payment-methods?country_id=X&side=recipient` retorna métodos del beneficiario
- [x] API `GET /transactions/account-types?country_id=X` retorna tipos de cuenta del país
- [x] `fields_required` viene como array en la respuesta JSON
- [x] Vista muestra botones de método de pago del remitente (senderPaymentMethods)
- [x] Vista muestra campos condicionales del remitente (senderFields.includes)
- [x] Vista muestra campos condicionales del beneficiario (recipientFields.includes)
- [x] Vista muestra tipos de cuenta dinámicos (x-for sobre accountTypes)
- [x] `recipient_name` es campo universal siempre visible
- [x] `sender_phone` aparece solo cuando el método tiene `phone` en fields_required
- [x] Panel admin muestra badge `side` en lista de métodos de pago
- [x] Panel admin permite crear métodos con campo `side`
- [x] Panel admin muestra pestaña "Tipos de cuenta" con CRUD completo
- [x] `operation_type` acepta cualquier string (ya no enum)
- [x] PHP Suite 38/38 PASS post-implementación

---

## Commits de REQ-13

| Hash | Mensaje |
|------|---------|
| `4946737` | feat(db): add side to payment_methods, create account_types, add sender_operation_type |
| `5aec3c9` | feat(models): update PaymentMethod with side scopes, create AccountType model |
| `c95764f` | feat(seeders): update PaymentMethodSeeder with side + 6 countries, create AccountTypeSeeder |
| `9c7c988` | feat(api): account-types endpoint, side filter on payment-methods, AccountType CRUD routes |
| `7b06899` | feat(countries): add side to payment-method form, add account-types CRUD section |
| `ca5815c` | feat(transactions): add sender op-type selector, dynamic account-types, fix placeholders |
| `33e05f1` | fix(seeders): add nequi/daviplata(CO) and cvu_alias(AR) with correct side=sender |
