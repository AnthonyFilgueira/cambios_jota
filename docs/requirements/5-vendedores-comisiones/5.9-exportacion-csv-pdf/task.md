# Tarea 5.9: Exportación CSV/PDF

**Horas estimadas:** 5h  
**Estado:** 🔄 EN PROGRESO

## Objetivo

Implementar exportación de reportes y rankings a formatos CSV y PDF para facilitar análisis offline, archivo y compartición de datos.

## Alcance

### 1. Exportación CSV

**Reportes exportables:**
- Rankings de vendedores
- Reporte individual de vendedor
- Dashboard del dueño (métricas globales)

**Formato CSV:**
- Headers descriptivos
- Datos separados por comas
- Codificación UTF-8
- Nombre de archivo con fecha

### 2. Exportación PDF

**Reportes exportables:**
- Reporte individual de vendedor
- Rankings de vendedores
- Dashboard del dueño

**Formato PDF:**
- Logo y branding Cambio J
- Headers y footers
- Tablas formateadas
- Colores de marca
- Fecha de generación

### 3. Botones de Exportación

**Ubicación:**
- Dashboard del dueño: botones CSV/PDF
- Rankings: botones CSV/PDF
- Reporte individual: botón PDF

**UX:**
- Íconos claros (📥 descarga)
- Indicador de generación
- Descarga automática

## Librerías a Utilizar

### Para PDF:
- **Laravel-dompdf** (recomendada)
  - `composer require barryvdh/laravel-dompdf`
  - Genera PDFs desde HTML/Blade
  - Fácil integración

### Para CSV:
- **Nativo PHP**
  - `fputcsv()`
  - `Response::streamDownload()`
  - No requiere librerías adicionales

## Archivos a crear/modificar

### Nuevos:
- `app/Exports/SellersRankingExport.php` (opcional: Laravel Excel)
- `app/Http/Controllers/ExportController.php`
- `resources/views/exports/pdf/seller-report.blade.php`
- `resources/views/exports/pdf/rankings.blade.php`
- `resources/views/exports/pdf/owner-dashboard.blade.php`

### Modificar:
- `routes/web.php` (agregar rutas de exportación)
- `resources/views/owner-dashboard.blade.php` (botones)
- `resources/views/reports/seller-rankings.blade.php` (botones)
- `resources/views/reports/seller-performance.blade.php` (botón)

## Criterios de aceptación

- ✅ CSV descargable con datos completos
- ✅ PDF con diseño profesional
- ✅ Nombre de archivo descriptivo (ej: rankings_2026-04-20.csv)
- ✅ Headers en CSV
- ✅ Logo y branding en PDF
- ✅ Datos filtrados por período en exportación
- ✅ Responsive (botones adaptan en móvil)
