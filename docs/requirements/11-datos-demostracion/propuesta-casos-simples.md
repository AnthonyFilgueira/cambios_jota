# REQ 11: Datos de Demostración - Propuesta Simplificada

**Creado:** 2026-04-20 20:45  
**Para aprobación antes de implementar**

---

## Propuesta: Casos Mínimos pero Completos

Esta es una propuesta **más simple** que la del requirement.md principal, enfocada en cubrir **todos los casos** sin generar demasiados datos.

---

## 📊 Resumen Ejecutivo

| Entidad | Cantidad Propuesta | Cantidad en requirement.md |
|---------|-------------------|----------------------------|
| Usuarios | 4 | 30+ |
| Vendedores | 4 | 5-8 |
| Tasas activas | 3 | 1 |
| Tasas históricas | 3 | 10-15 |
| Ventas totales | **15** | **100+** |
| Observaciones | 5 | Many |
| Liquidaciones | 2 | 20-30 |

---

## 🎯 Casos a Simular (SIMPLE)

### 1. Usuarios (4 total)

```
👤 Admin (admin@cambio.com / password)
👤 Cliente 1: Juan Pérez (juan@gmail.com / password) - Con historial
👤 Cliente 2: María González (maria@gmail.com / password) - Frecuente  
👤 Cliente 3: Carlos Rodríguez (carlos@gmail.com / password) - Nuevo (0 ventas)
```

### 2. Vendedores (4 total)

```
🏅 VEND001: Pedro Martínez
   - Comisión: 5% personal + 15% dueño
   - Perfil: Vendedor estrella
   - Estado: Activo

🏅 VEND002: Ana López
   - Comisión: 7% personal + 13% dueño
   - Perfil: Vendedor promedio
   - Estado: Activo

🏅 VEND003: Luis Torres
   - Comisión: 10% personal + 10% dueño
   - Perfil: Vendedor nuevo
   - Estado: Activo

🏅 VEND004: Rosa Fernández
   - Comisión: 5% personal + 15% dueño
   - Perfil: Vendedor suspendido
   - Estado: Inactivo
```

### 3. Tasas de Cambio

**Activas (3):**
- PEN → VES: 173.71 ✓
- ARS → VES: 2.50 ✓
- CLP → VES: 0.55 ✓

**Históricas (3):**
- PEN → VES: 170.00 (hace 5 días)
- PEN → VES: 175.00 (hace 2 días)
- ARS → VES: 2.30 (hace 3 días)

**Historial de cambios:** 5 registros en `exchange_rate_history`

### 4. Ventas (15 total)

#### Estado: PENDING (2)
1. Juan → VEND001 → 500 PEN → Hoy -2h
2. María → VEND002 → 1000 PEN → Hoy -1h

#### Estado: APPROVED (3)
3. Juan → VEND001 → 300 PEN → Hoy -3h
4. Directo → VEND003 → 200 ARS → Hoy -4h
5. María → VEND002 → 5000 CLP → Ayer

#### Estado: COMPLETED (5)
6. Juan → VEND001 → 1000 PEN → Hace 3 días ✓ Comprobante
7. María → VEND001 → 750 PEN → Hace 5 días ✓ Tasa 170.00 (snapshot)
8. Juan → VEND002 → 500 PEN → Hace 7 días ✓
9. Directo → VEND003 → 2000 PEN → Hace 2 días ✓
10. María → VEND001 → 300 PEN → Hace 10 días ✓

#### Estado: REJECTED (2)
11. Juan → VEND002 → 100 PEN → Hace 1 día
    - Motivo: "Datos de destinatario incorrectos"
    - Rechazado por: Vendedor

12. María → VEND001 → 10,000 PEN → Hace 2 días
    - Motivo: "Monto sospechoso, verificar identidad"
    - Rechazado por: Admin

#### Estado: OBSERVED (3)
13. Juan → VEND001 → 400 PEN → Hoy -5h
    - Observación: "Falta número de cuenta del destinatario"

14. María → VEND002 → 600 PEN → Hoy -6h
    - Observación: "Nombre del destinatario no coincide con DNI"

15. Directo → VEND003 → 800 PEN → Ayer
    - Observación: "Comprobante de identidad ilegible"

### 5. Observaciones / Sale Logs (5 registros)

1. Venta 13 → "Falta número de cuenta" (Admin, hoy)
2. Venta 14 → "Nombre no coincide con DNI" (Admin, hoy)
3. Venta 14 → "Cliente corrigió el nombre" (Ana, hoy)
4. Venta 15 → "Comprobante ilegible" (Admin, ayer)
5. Venta 12 → "Rechazada por monto sospechoso" (Admin, hace 2 días)

### 6. Cambios de Comisiones (3 cambios)

1. **Pedro (VEND001):** 7% → 5% (hace 5 días)
2. **Ana (VEND002):** 5% → 7% (hace 3 días)
3. **Luis (VEND003):** Inicio con 10% (hace 1 semana)

### 7. Liquidaciones (2)

**Liquidación 1:**
- Vendedor: Pedro
- Monto: S/ 150.00
- Ventas: 3
- Estado: Pagada (hace 1 semana)

**Liquidación 2:**
- Vendedor: Ana
- Monto: S/ 200.00
- Ventas: 4
- Estado: Pagada (hace 2 semanas)

**Monederos (saldos pendientes):**
- Pedro: S/ 75.00
- Ana: S/ 105.00
- Luis: S/ 200.00

---

## ✅ Ventajas de Esta Propuesta

1. **Menos datos = Más fácil debugging**
2. **Todos los casos cubiertos:**
   - ✓ Todos los estados de ventas
   - ✓ Observaciones y rechazos
   - ✓ Snapshots de tasas
   - ✓ Cambios de comisiones
   - ✓ Liquidaciones
   - ✓ Monedero virtual
   - ✓ Historial de tasas
   - ✓ Cliente nuevo vs frecuente

3. **Datos manejables** para presentación
4. **Rápido de generar** (1 seeder, <5 min ejecución)

---

## ❓ Decisión Requerida

**Opción A:** Usar esta propuesta simple (15 ventas)

**Opción B:** Usar propuesta detallada del requirement.md (100+ ventas)

**Opción C:** Mezclar (ej: casos simples pero más vendedores)

**Opción D:** Modificar alguna parte específica

---

**Por favor indica:** OPCIÓN ____ o especifica cambios.

---

**Pendiente aprobación de:** Anthony Filgueira
