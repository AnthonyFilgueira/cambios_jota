# REQ 12: Arreglos de Vistas Pendientes

**Estado:** ⏸️ PENDIENTE  
**Horas estimadas:** 4h  
**Prioridad:** ALTA  
**Fecha creación:** 2026-04-20

---

## Objetivo

Completar las vistas que tienen funcionalidad implementada en backend pero NO se muestra en frontend, y mejorar la experiencia de usuario en módulos existentes.

---

## Problema Identificado

✅ **Ya corregido (en esta sesión):**
- Dashboard principal → Ahora redirige a owner-dashboard
- Navegación → Links agregados a 12 módulos ocultos

❌ **Pendiente de corrección:**
- Vista de vendedores muy básica (falta métricas, monedero, reportes)
- Vista de reportes no linkea a rankings ni exportación
- Rankings muestra info duplicada organizada diferente

---

## Tareas Pendientes

| ID | Tarea | Archivo | Tiempo | Prioridad |
|----|-------|---------|--------|-----------|
| 12.1 | Mejorar vista de vendedores con métricas | `sellers/index.blade.php` | 2h | ALTA |
| 12.2 | Mejorar vista de reportes con links | `reports/index.blade.php` | 1h | MEDIA |
| 12.3 | Optimizar rankings (info duplicada) | `owner-dashboard.blade.php` | 1h | BAJA |

**Total:** 4 horas

---

## Tarea 12.1: Mejorar Vista de Vendedores

### Problema:
Vista actual (`sellers/index.blade.php`) solo muestra:
- Código, nombre, comisiones (%)
- Botones editar/eliminar

### Métodos disponibles en Seller.php NO usados:
```php
$seller->walletBalance()           // Saldo monedero
$seller->totalSales()              // Total vendido
$seller->salesCount()              // Cantidad ventas
$seller->totalCommissionsEarned()  // Comisiones totales
$seller->averageTicket()           // Ticket promedio
```

### Solución:
Agregar cards con métricas por vendedor:

```blade
@foreach($sellers as $seller)
<div class="bg-white rounded-lg shadow p-6 mb-4">
    <!-- Header -->
    <div class="flex justify-between items-start mb-4">
        <div>
            <h3 class="text-xl font-bold">{{ $seller->name }}</h3>
            <span class="font-mono text-sm bg-purple-100 text-purple-700 px-2 py-1 rounded">
                {{ $seller->code }}
            </span>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('wallet.index') }}" class="btn-primary">Ver Monedero</a>
            <a href="{{ route('reports.performance', $seller) }}" class="btn-secondary">Ver Reportes</a>
            <a href="{{ route('sellers.edit', $seller) }}" class="btn-secondary">Editar</a>
        </div>
    </div>

    <!-- Métricas -->
    <div class="grid grid-cols-5 gap-4">
        <div class="bg-purple-50 p-3 rounded">
            <p class="text-xs text-purple-600 mb-1">Saldo Monedero</p>
            <p class="text-xl font-bold text-purple-700">
                S/. {{ number_format($seller->walletBalance(), 2) }}
            </p>
        </div>
        <div class="bg-green-50 p-3 rounded">
            <p class="text-xs text-green-600 mb-1">Total Vendido</p>
            <p class="text-xl font-bold text-green-700">
                S/. {{ number_format($seller->totalSales(), 2) }}
            </p>
        </div>
        <div class="bg-blue-50 p-3 rounded">
            <p class="text-xs text-blue-600 mb-1">Cantidad Ventas</p>
            <p class="text-xl font-bold text-blue-700">
                {{ $seller->salesCount() }}
            </p>
        </div>
        <div class="bg-orange-50 p-3 rounded">
            <p class="text-xs text-orange-600 mb-1">Comisiones</p>
            <p class="text-xl font-bold text-orange-700">
                S/. {{ number_format($seller->totalCommissionsEarned(), 2) }}
            </p>
        </div>
        <div class="bg-teal-50 p-3 rounded">
            <p class="text-xs text-teal-600 mb-1">Ticket Promedio</p>
            <p class="text-xl font-bold text-teal-700">
                S/. {{ number_format($seller->averageTicket(), 2) }}
            </p>
        </div>
    </div>

    <!-- Configuración Comisiones -->
    <div class="mt-3 text-sm text-gray-600">
        <span>Comisión Vendedor: {{ $seller->seller_commission }}%</span>
        <span class="mx-2">|</span>
        <span>Comisión Jefe: {{ $seller->boss_commission }}%</span>
    </div>
</div>
@endforeach
```

---

## Tarea 12.2: Mejorar Vista de Reportes

### Problema:
Vista actual (`reports/index.blade.php`) es muy básica:
- Solo muestra tabla por vendedor
- NO linkea a rankings (que SÍ existe completo)
- NO tiene botones de exportación

### Solución:
```blade
<!-- Link prominente a Rankings -->
<div class="mb-6 bg-gradient-to-r from-purple-600 to-purple-700 text-white p-6 rounded-lg">
    <div class="flex justify-between items-center">
        <div>
            <h2 class="text-2xl font-bold mb-2">🏆 Rankings de Vendedores</h2>
            <p class="text-purple-100">Ver análisis completo con métricas y comparativas</p>
        </div>
        <a href="{{ route('reports.rankings') }}" 
           class="bg-white text-purple-600 px-6 py-3 rounded-lg font-semibold hover:bg-purple-50">
            Ver Rankings Completos →
        </a>
    </div>
</div>

<!-- Resumen General -->
<div class="grid grid-cols-4 gap-4 mb-6">
    <div class="bg-white p-4 rounded-lg shadow">
        <p class="text-xs text-gray-500 mb-1">Total Sistema</p>
        <p class="text-2xl font-bold">S/. {{ number_format($totalSales, 2) }}</p>
    </div>
    <!-- más cards de resumen -->
</div>

<!-- Botones Exportación -->
<div class="flex gap-2 mb-4">
    <a href="{{ route('export.rankings.csv') }}" class="btn-green">
        📥 Exportar CSV
    </a>
    <a href="{{ route('export.rankings.pdf') }}" class="btn-red">
        📄 Exportar PDF
    </a>
</div>
```

---

## Tarea 12.3: Optimizar Rankings (owner-dashboard)

### Problema:
Actualmente muestra 2 tablas con la misma información:
- **Top Vendedores por Monto:** ordenado por total vendido
- **Top Vendedores por Cantidad:** ordenado por cantidad de ventas

Ambas muestran: Vendedor | Monto | Cantidad (solo cambia el orden)

### Solución A (Simple):
Unificar en una sola tabla con ordenamiento dinámico por columna

### Solución B (Óptima):
Agregar gráfico visual + tabla única
```blade
<!-- Gráfico de Barras -->
<div class="mb-6">
    <canvas id="chartVendedores"></canvas>
</div>

<!-- Tabla Única con Ordenamiento -->
<table>
    <thead>
        <tr>
            <th>
                <a href="?sort=seller">Vendedor</a>
            </th>
            <th>
                <a href="?sort=monto">Total Vendido ↓</a>
            </th>
            <th>
                <a href="?sort=cantidad">Cantidad</a>
            </th>
        </tr>
    </thead>
    <!-- datos -->
</table>
```

---

## Criterios de Aceptación

### 12.1 Vendedores:
- [x] Links navegación agregados ✅
- [ ] Cards con 5 métricas (monedero, ventas, cantidad, comisiones, ticket)
- [ ] Botones: Ver Monedero, Ver Reportes, Editar
- [ ] Paleta de colores Cambio J aplicada
- [ ] Responsive design

### 12.2 Reportes:
- [x] Links navegación agregados ✅
- [ ] Link prominente a rankings
- [ ] Cards resumen general del sistema
- [ ] Botones exportación CSV/PDF
- [ ] Diseño mejorado con paleta Cambio J

### 12.3 Rankings:
- [ ] Decidir: tabla única vs 2 tablas
- [ ] Si tabla única: ordenamiento por columna
- [ ] (Opcional) Agregar gráfico visual

---

## Notas Técnicas

### Controladores ya implementados:
- `SellerController` → tiene todos los métodos necesarios
- `SellerReportController@rankings` → ranking completo existe
- `ExportController` → CSV/PDF ya funcionan
- `Seller Model` → todos los métodos de métricas disponibles

### Solo falta:
- Actualizar vistas Blade para mostrar datos disponibles
- Agregar links entre módulos

---

## Impacto Usuario

**Antes:**
- Usuario veía lista básica de vendedores sin métricas
- No sabía que existían rankings completos
- No podía exportar reportes fácilmente

**Después:**
- Usuario ve métricas clave de cada vendedor al instante
- Acceso directo a rankings y reportes desde navegación
- Exportación visible y accesible
- Mejor experiencia de navegación entre módulos

---

## Dependencias

- ✅ Backend completo (Seller model con métodos)
- ✅ Rutas creadas
- ✅ Controladores implementados
- ✅ Navegación mejorada (REQ 12.0 - completado hoy)

**Solo falta:** Actualizar vistas Blade

---

**Creado por:** Claude Sonnet 4.5  
**Fecha:** 2026-04-20  
**Razón:** Completar implementación de vistas pendientes
