# INC-004 — Solución

## Causa raíz confirmada
TransactionStatusChanged era database-only. SellerTransactionController no usaba las
notificaciones con email. Ningún evento notificaba a admin/super-admin.

## Archivos modificados
- app/Notifications/TransactionStatusChanged.php — canal mail añadido
- app/Notifications/NewTransactionForOwner.php — nueva clase
- app/Http/Controllers/Controller.php — notifyOwners() heredable
- app/Http/Controllers/TransactionController.php — notifyOwners() en cada evento
- app/Http/Controllers/SellerTransactionController.php — email correcto + notifyOwners()

## Mapa final
| Acción             | Cliente   | Vendedor  | Admin/Super-admin |
|--------------------|-----------|-----------|-------------------|
| Nueva transacción  | —         | Email     | Email             |
| Admin observa      | Email     | —         | Email             |
| Admin procesa      | Email     | —         | Email             |
| Admin completa     | Email     | Email     | Email             |
| Vendedor aprueba   | Email     | —         | Email             |
| Vendedor observa   | Email     | —         | Email             |
| Vendedor deniega   | Email     | —         | Email             |

## Commits
- e46aa54 — TransactionStatusChanged con email
- 3fc8623 — NewTransactionForOwner
- 3398bc6 — TransactionController con notifyOwners
- dce9c91 — Controller base + SellerTransactionController
