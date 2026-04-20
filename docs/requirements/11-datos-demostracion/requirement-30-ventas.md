# REQ 11: Datos de Demostración - 30 Ventas

**Versión:** 2.0 (Corregida)  
**Fecha:** 2026-04-20 21:00  
**Estado:** ⏸️ ESPERANDO APROBACIÓN

---

## Resumen

Generar **30 ventas** con datos realistas que cubran todos los casos del sistema.

---

## 1. Usuarios (5)

```
👤 ADMIN
   Email: cambios_jota@innodite.com
   Password: password
   Rol: Administrador
   
👤 CLIENTE 1 - Juan Pérez (Perú)
   Email: juan.perez@gmail.com
   Password: password
   DNI: 45678912
   Teléfono: +51 987654321
   Perfil: Cliente frecuente
   
👤 CLIENTE 2 - María González (Perú)
   Email: maria.gonzalez@gmail.com
   Password: password
   DNI: 41234567
   Teléfono: +51 965432178
   Perfil: Cliente muy activo
   
👤 CLIENTE 3 - Carlos Rodríguez (Perú)
   Email: carlos.rodriguez@gmail.com
   Password: password
   DNI: 48765432
   Teléfono: +51 912345678
   Perfil: Cliente nuevo
   
👤 CLIENTE 4 - Ana Torres (Perú)
   Email: ana.torres@gmail.com
   Password: password
   DNI: 47654321
   Teléfono: +51 923456789
   Perfil: Cliente ocasional
```

---

## 2. Vendedores (4)

```
🏅 VEND001 - Pedro Martínez
   Email: pedro.martinez@cambioj.com
   Comisión: 5% personal + 15% dueño
   Estado: Activo
   Perfil: Top performer
   
🏅 VEND002 - Ana López
   Email: ana.lopez@cambioj.com
   Comisión: 7% personal + 13% dueño
   Estado: Activo
   Perfil: Vendedor consistente
   
🏅 VEND003 - Luis Torres
   Email: luis.torres@cambioj.com
   Comisión: 10% personal + 10% dueño
   Estado: Activo
   Perfil: Vendedor nuevo
   
🏅 VEND004 - Rosa Fernández
   Email: rosa.fernandez@cambioj.com
   Comisión: 5% personal + 15% dueño
   Estado: Inactivo
   Perfil: Suspendido temporalmente
```

---

## 3. Destinatarios en Venezuela (10)

**Estos son los clientes que RECIBEN el dinero en Venezuela**

```
🇻🇪 DESTINATARIO 1 - José Ramírez
   Cédula: V-18.234.567
   Banco: Banco de Venezuela
   Cuenta: 0102-1234-5678-9012
   Teléfono: +58 412-1234567
   
🇻🇪 DESTINATARIO 2 - Carmen Silva
   Cédula: V-20.456.789
   Banco: Banesco
   Cuenta: 0134-2345-6789-0123
   Teléfono: +58 414-2345678
   
🇻🇪 DESTINATARIO 3 - Miguel Pérez
   Cédula: V-22.567.890
   Banco: Mercantil
   Cuenta: 0105-3456-7890-1234
   Teléfono: +58 424-3456789
   
🇻🇪 DESTINATARIO 4 - Laura Medina
   Cédula: V-19.678.901
   Banco: Provincial
   Cuenta: 0108-4567-8901-2345
   Teléfono: +58 416-4567890
   
🇻🇪 DESTINATARIO 5 - Roberto Castro
   Cédula: V-21.789.012
   Banco: Venezuela
   Cuenta: 0102-5678-9012-3456
   Teléfono: +58 426-5678901
   
🇻🇪 DESTINATARIO 6 - Elena Morales
   Cédula: V-23.890.123
   Banco: Banesco
   Cuenta: 0134-6789-0123-4567
   Teléfono: +58 412-6789012
   
🇻🇪 DESTINATARIO 7 - Fernando Díaz
   Cédula: V-17.901.234
   Banco: Mercantil
   Cuenta: 0105-7890-1234-5678
   Teléfono: +58 414-7890123
   
🇻🇪 DESTINATARIO 8 - Sofía Vargas
   Cédula: V-24.012.345
   Banco: Provincial
   Cuenta: 0108-8901-2345-6789
   Teléfono: +58 424-8901234
   
🇻🇪 DESTINATARIO 9 - Andrés Ramos
   Cédula: V-16.123.456
   Banco: Venezuela
   Cuenta: 0102-9012-3456-7890
   Teléfono: +58 416-9012345
   
🇻🇪 DESTINATARIO 10 - Gabriela Ortiz
   Cédula: V-25.234.567
   Banco: Banesco
   Cuenta: 0134-0123-4567-8901
   Teléfono: +58 426-0123456
```

---

## 4. Tasas de Cambio

**Activas:**
- PEN → VES: 173.71
- ARS → VES: 2.50
- CLP → VES: 0.55

**Históricas:**
- PEN → VES: 170.00 (hace 7 días) - desactivada
- PEN → VES: 175.00 (hace 3 días) - desactivada
- ARS → VES: 2.30 (hace 5 días) - desactivada

**Historial:** 8 cambios en `exchange_rate_history`

---

## 5. Ventas (30 TOTAL)

### 📊 Distribución por Estado

- **PENDING:** 4 ventas (13%)
- **APPROVED:** 6 ventas (20%)
- **COMPLETED:** 12 ventas (40%)
- **REJECTED:** 4 ventas (13%)
- **OBSERVED:** 4 ventas (13%)

### 🕐 Distribución Temporal

- **Hoy:** 8 ventas
- **Ayer:** 6 ventas
- **Hace 2-3 días:** 8 ventas
- **Hace 4-7 días:** 5 ventas
- **Hace 8-15 días:** 3 ventas

---

### PENDING (4 ventas)

**#1** Juan → Destinatario 1 (José) → VEND001 → 500 PEN → 86,855 VES
- Fecha: Hoy -2h
- Estado: pending

**#2** María → Destinatario 2 (Carmen) → VEND002 → 1,000 PEN → 173,710 VES
- Fecha: Hoy -1h
- Estado: pending

**#3** Carlos → Destinatario 3 (Miguel) → VEND001 → 300 PEN → 52,113 VES
- Fecha: Hoy -3h
- Estado: pending

**#4** Ana → Destinatario 4 (Laura) → VEND003 → 750 PEN → 130,282.50 VES
- Fecha: Hoy -4h
- Estado: pending

---

### APPROVED (6 ventas)

**#5** Juan → Destinatario 5 (Roberto) → VEND001 → 400 PEN → 69,484 VES
- Fecha: Ayer -10h
- Estado: approved

**#6** María → Destinatario 1 (José) → VEND002 → 2,000 PEN → 347,420 VES
- Fecha: Ayer -8h
- Estado: approved

**#7** Directo → Destinatario 6 (Elena) → VEND003 → 200 ARS → 500 VES
- Fecha: Ayer -6h
- Estado: approved

**#8** Juan → Destinatario 7 (Fernando) → VEND001 → 1,500 PEN → 260,565 VES
- Fecha: Hace 2 días
- Estado: approved

**#9** María → Destinatario 2 (Carmen) → VEND002 → 5,000 CLP → 2,750 VES
- Fecha: Hace 2 días
- Estado: approved

**#10** Ana → Destinatario 8 (Sofía) → VEND001 → 600 PEN → 104,226 VES
- Fecha: Hace 3 días
- Estado: approved

---

### COMPLETED (12 ventas)

**#11** Juan → Destinatario 1 (José) → VEND001 → 1,000 PEN → 173,710 VES
- Fecha: Hace 4 días
- Estado: completed
- Comprobante: ✅ Subido

**#12** María → Destinatario 2 (Carmen) → VEND001 → 750 PEN → 127,500 VES
- Fecha: Hace 7 días
- Tasa usada: 170.00 (snapshot antiguo)
- Estado: completed
- Comprobante: ✅

**#13** Juan → Destinatario 3 (Miguel) → VEND002 → 500 PEN → 86,855 VES
- Fecha: Hace 8 días
- Estado: completed
- Comprobante: ✅

**#14** Directo → Destinatario 4 (Laura) → VEND003 → 2,000 PEN → 347,420 VES
- Fecha: Hace 3 días
- Estado: completed
- Comprobante: ✅

**#15** María → Destinatario 5 (Roberto) → VEND001 → 300 PEN → 52,113 VES
- Fecha: Hace 10 días
- Estado: completed
- Comprobante: ✅

**#16** Juan → Destinatario 6 (Elena) → VEND002 → 1,200 PEN → 208,452 VES
- Fecha: Hace 5 días
- Estado: completed
- Comprobante: ✅

**#17** Ana → Destinatario 7 (Fernando) → VEND001 → 800 PEN → 138,968 VES
- Fecha: Hace 6 días
- Estado: completed
- Comprobante: ✅

**#18** Carlos → Destinatario 8 (Sofía) → VEND003 → 450 PEN → 78,169.50 VES
- Fecha: Hace 12 días
- Estado: completed
- Comprobante: ✅

**#19** María → Destinatario 9 (Andrés) → VEND002 → 3,000 PEN → 521,130 VES
- Fecha: Hace 4 días
- Estado: completed
- Comprobante: ✅

**#20** Juan → Destinatario 10 (Gabriela) → VEND001 → 950 PEN → 165,024.50 VES
- Fecha: Hace 9 días
- Estado: completed
- Comprobante: ✅

**#21** Directo → Destinatario 1 (José) → VEND003 → 1,800 PEN → 312,678 VES
- Fecha: Hace 11 días
- Estado: completed
- Comprobante: ✅

**#22** María → Destinatario 2 (Carmen) → VEND001 → 650 PEN → 112,911.50 VES
- Fecha: Hace 13 días
- Estado: completed
- Comprobante: ✅

---

### REJECTED (4 ventas)

**#23** Juan → Destinatario 3 (Miguel) → VEND002 → 100 PEN → 17,371 VES
- Fecha: Ayer
- Estado: rejected
- Motivo: "Datos de destinatario incorrectos"
- Rechazado por: Vendedor (Ana López)

**#24** María → Destinatario 4 (Laura) → VEND001 → 10,000 PEN → 1,737,100 VES
- Fecha: Hace 2 días
- Estado: rejected
- Motivo: "Monto sospechoso, verificar identidad del cliente"
- Rechazado por: Admin

**#25** Carlos → Destinatario 5 (Roberto) → VEND003 → 250 PEN → 43,427.50 VES
- Fecha: Hace 4 días
- Estado: rejected
- Motivo: "Comprobante de pago ilegible"
- Rechazado por: Admin

**#26** Directo → Destinatario 6 (Elena) → VEND002 → 150 PEN → 26,056.50 VES
- Fecha: Hace 5 días
- Estado: rejected
- Motivo: "Cliente solicitó cancelación"
- Rechazado por: Vendedor (Ana López)

---

### OBSERVED (4 ventas)

**#27** Juan → Destinatario 7 (Fernando) → VEND001 → 400 PEN → 69,484 VES
- Fecha: Hoy -5h
- Estado: observed
- Observación: "Falta número de cuenta del destinatario"

**#28** María → Destinatario 8 (Sofía) → VEND002 → 600 PEN → 104,226 VES
- Fecha: Hoy -6h
- Estado: observed
- Observación: "Nombre del destinatario no coincide con cédula"

**#29** Directo → Destinatario 9 (Andrés) → VEND003 → 800 PEN → 138,968 VES
- Fecha: Ayer
- Estado: observed
- Observación: "Comprobante de identidad ilegible, reenviar"

**#30** Ana → Destinatario 10 (Gabriela) → VEND001 → 550 PEN → 95,540.50 VES
- Fecha: Hace 2 días
- Estado: observed
- Observación: "Verificar monto con el cliente (discrepancia reportada)"

---

## 6. Observaciones / Sale Logs (8 registros)

1. Venta #27 → "Falta número de cuenta" (Admin, hoy)
2. Venta #28 → "Nombre no coincide" (Admin, hoy)
3. Venta #28 → "Cliente envió corrección" (Ana López, hoy -5h)
4. Venta #29 → "Comprobante ilegible" (Admin, ayer)
5. Venta #30 → "Verificar monto" (Admin, hace 2 días)
6. Venta #24 → "Rechazada por monto sospechoso" (Admin, hace 2 días)
7. Venta #25 → "Comprobante ilegible" (Admin, hace 4 días)
8. Venta #23 → "Datos incorrectos" (Ana López, ayer)

---

## 7. Cambios de Comisiones (4 cambios)

1. **Pedro (VEND001):** 7% → 5% (hace 10 días)
2. **Ana (VEND002):** 5% → 7% (hace 6 días)
3. **Luis (VEND003):** Inicio 10% (hace 2 semanas)
4. **Pedro (VEND001):** Comisión dueño 12% → 15% (hace 8 días)

---

## 8. Liquidaciones (3)

**Liquidación 1:**
- Vendedor: Pedro Martínez
- Monto: S/ 250.00
- Ventas incluidas: 5
- Estado: Pagada
- Fecha: Hace 1 semana
- Método: Transferencia

**Liquidación 2:**
- Vendedor: Ana López
- Monto: S/ 180.00
- Ventas incluidas: 3
- Estado: Pagada
- Fecha: Hace 2 semanas
- Método: Yape

**Liquidación 3:**
- Vendedor: Luis Torres
- Monto: S/ 120.00
- Ventas incluidas: 2
- Estado: Pagada
- Fecha: Hace 10 días
- Método: Efectivo

**Monederos (saldos pendientes):**
- Pedro: S/ 150.00
- Ana: S/ 210.00
- Luis: S/ 340.00
- Rosa: S/ 0.00 (inactiva)

---

## 9. Métricas Esperadas

**Ventas totales:** 30  
**Monto total enviado:** ~S/ 32,000  
**Comisiones vendedores:** ~S/ 1,600  
**Comisiones dueño:** ~S/ 4,800  
**Liquidado:** S/ 550  
**Pendiente en monederos:** S/ 700

---

## ✅ Casos Cubiertos

- ✅ Todos los estados de ventas (pending, approved, completed, rejected, observed)
- ✅ Ventas con diferentes monedas (PEN, ARS, CLP)
- ✅ Clientes que envían (Perú)
- ✅ Destinatarios que reciben (Venezuela)
- ✅ Snapshots de tasas antiguas
- ✅ Snapshots de comisiones
- ✅ Observaciones con trazabilidad
- ✅ Rechazos por vendedor y admin
- ✅ Ventas directas (sin cliente registrado)
- ✅ Cambios de comisiones
- ✅ Liquidaciones pagadas
- ✅ Monedero virtual con saldos
- ✅ Vendedor inactivo
- ✅ Cliente nuevo sin ventas

---

**Estado:** ⏸️ ESPERANDO APROBACIÓN  
**Responder:** "APROBADO" o solicitar cambios
