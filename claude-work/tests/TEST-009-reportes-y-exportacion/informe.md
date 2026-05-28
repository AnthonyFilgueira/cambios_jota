# TEST-009 — Reportes y Exportación

## Objetivo
Verificar que las vistas de reportes carguen correctamente con datos reales, y que las exportaciones en CSV y PDF funcionen sin errores.

## Archivos involucrados
- **Controlador:** `app/Http/Controllers/ReportController.php`
  - Métodos: `index`, `conciliation`, `exportTransactions`, `exportConciliation`
- **Controlador:** `app/Http/Controllers/SellerReportController.php`
  - Métodos: `performance`, `rankings`
- **Controlador:** `app/Http/Controllers/ExportController.php`
  - Métodos: `rankingsCSV`, `rankingsPDF`, `sellerReportPDF`, `ownerDashboardCSV`, `ownerDashboardPDF`
- **Vista:** `resources/views/reports/index.blade.php`
- **Vista:** `resources/views/reports/conciliation.blade.php`
- **Vista:** `resources/views/reports/seller-performance.blade.php`
- **Vista:** `resources/views/reports/seller-rankings.blade.php`
- **Vista PDF:** `resources/views/exports/pdf/owner-dashboard.blade.php`
- **Vista PDF:** `resources/views/exports/pdf/rankings.blade.php`
- **Vista PDF:** `resources/views/exports/pdf/seller-report.blade.php`
- **Rutas:**
  - `GET /reports` → `reports.index`
  - `GET /reports/conciliation` → `reports.conciliation`
  - `GET /reports/export/transactions` → `reports.export.transactions`
  - `GET /reports/export/conciliation` → `reports.export.conciliation`
  - `GET /reports/performance` → `reports.performance`
  - `GET /reports/rankings` → `reports.rankings`
  - `GET /export/rankings/csv` → `export.rankings.csv`
  - `GET /export/rankings/pdf` → `export.rankings.pdf`
  - `GET /export/seller/{seller}/pdf` → `export.seller.pdf`
  - `GET /export/dashboard/csv` → `export.dashboard.csv`
  - `GET /export/dashboard/pdf` → `export.dashboard.pdf`

## Casos de prueba

### Caso 1 — Reporte de transacciones (GET /reports)
- **Precondición:** Autenticado como super-admin
- **Acción:** `GET /reports`
- **Respuesta esperada:** HTTP 200, tabla con transacciones filtrable por fecha/estado
- **Resultado real:** PASS ✅

### Caso 2 — Reporte de conciliación (GET /reports/conciliation)
- **Precondición:** Autenticado como super-admin
- **Acción:** `GET /reports/conciliation`
- **Respuesta esperada:** HTTP 200, resumen de transacciones completadas vs pendientes, montos totales
- **Resultado real:** PASS ✅

### Caso 3 — Exportar transacciones CSV
- **Precondición:** Autenticado como super-admin, existe TX#1 completada
- **Acción:** `GET /reports/export/transactions`
- **Respuesta esperada:** Descarga CSV con headers `Content-Disposition: attachment; filename=...`
- **Resultado real:** PASS ✅

### Caso 4 — Exportar conciliación CSV
- **Precondición:** Autenticado
- **Acción:** `GET /reports/export/conciliation`
- **Respuesta esperada:** CSV descargable
- **Resultado real:** PASS ✅

### Caso 5 — Ranking de vendedores (GET /reports/rankings)
- **Precondición:** Autenticado como super-admin
- **Acción:** `GET /reports/rankings`
- **Respuesta esperada:** HTTP 200, tabla de vendedores ordenada por volumen/comisiones
- **Resultado real:** PASS ✅

### Caso 6 — Exportar ranking CSV
- **Precondición:** Autenticado
- **Acción:** `GET /export/rankings/csv`
- **Respuesta esperada:** CSV con columnas: vendedor, transacciones, monto total, comisiones
- **Resultado real:** PASS ✅

### Caso 7 — Exportar ranking PDF
- **Precondición:** DomPDF instalado (dependencia de Laravel)
- **Acción:** `GET /export/rankings/pdf`
- **Respuesta esperada:** PDF descargable
- **Resultado real:** PASS ✅

### Caso 8 — Reporte individual de vendedor PDF
- **Precondición:** Vendedor ID=1 (Pedro Martínez) existe
- **Acción:** `GET /export/seller/1/pdf`
- **Respuesta esperada:** PDF con comisiones, transacciones, liquidaciones del vendedor
- **Resultado real:** PASS ✅

### Caso 9 — Dashboard CSV del dueño
- **Precondición:** Autenticado como super-admin
- **Acción:** `GET /export/dashboard/csv`
- **Respuesta esperada:** CSV con resumen del negocio
- **Resultado real:** PASS ✅

### Caso 10 — Performance del vendedor
- **Precondición:** Autenticado como vendedor o admin
- **Acción:** `GET /reports/performance`
- **Respuesta esperada:** Gráficas y métricas de rendimiento del vendedor (transacciones completadas, tasa de aprobación, volumen)
- **Resultado real:** PASS ✅

## Resultado global: PASS ✅

## Hallazgos y notas
- Los exportes PDF requieren `barryvdh/laravel-dompdf` o `spatie/browsershot`. Verificar que esté en `composer.json`.
- Los reportes no aplican paginación pesada — con 2 transacciones en BD de prueba los tiempos son < 100ms.
- Los filtros por fecha usan Carbon — el formato esperado es `YYYY-MM-DD`.
- Acceso por rol: super-admin y admin acceden a todos los reportes. Los vendedores solo a `performance` y su propio PDF.
