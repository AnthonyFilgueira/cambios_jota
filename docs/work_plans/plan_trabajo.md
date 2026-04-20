**COTIZACIÓN COMERCIAL  |  PROYECTO: CAMBIO J**     Lima, 14 de Abril de 2025

**COTIZACIÓN COMERCIAL**

**Proyecto: Cambio J — Plataforma de Gestión de Divisas**

Lima, Perú  •  14 de Abril de 2025  •  Versión 1.0

**1. Descripción del Proyecto**

Cambio J es una plataforma web de gestión de divisas orientada al mercado peruano y latinoamericano. El sistema digitaliza y automatiza el proceso completo de cambio de moneda, desde la simulación de tasas hasta la liquidación final con comprobante, integrando tres roles clave: cliente final, vendedor intermediario y dueño/administrador.

La plataforma contempla 8 módulos funcionales que cubren el ciclo operativo completo del negocio de divisas, incluyendo notificaciones en tiempo real, motor de comisiones, gestión de incentivos y trazabilidad completa de cada operación.

**2. Alcance del Desarrollo**

El proyecto comprende el desarrollo íntegro de los siguientes 8 requerimientos funcionales:

- REQ 1 — Simulador de Divisas Mobile-First (con lógica de conversión y formateo)

- REQ 2 — Registro de Usuario, Autenticación e Historial de Transacciones

- REQ 3 — Flujo de Venta Indirecta con Aprobación Multi-Nivel (cliente → vendedor → dueño)

- REQ 4 — Gestión de Observaciones, Feedback Loop y Trazabilidad de Operaciones

- REQ 5 — Administración de Vendedores, Motor de Comisiones y Dashboard BI

- REQ 6 — Configuración de Matriz de Divisas, Países y Corredores de Envío

- REQ 7 — Consola Centralizada de Tasas de Cambio y Margen de Utilidad

- REQ 8 — Módulo de Incentivos, Bonos Variables y Gestión de Comprobantes

Incluye además: setup de infraestructura, pruebas QA, documentación técnica básica y 7 días de soporte post-entrega.

**3. Desglose Detallado por Tarea**

| **#** | **Descripción de la Tarea** | **Módulo** | **Horas Est.** | **Costo (S/)** | **Fecha Límite** |
| --- | --- | --- | --- | --- | --- |
|  | **REQ 1: Simulador de Divisas (Mobile-First)** |  |  |  |  |
| 1.1 | Maquetado contenedor principal mobile-first + branding | UI/UX | 6h | S/ 150 | 15 Abr |
| 1.2 | Componentes selectores de moneda (origen/destino) | UI/UX | 5h | S/ 125 | 15 Abr |
| 1.3 | Input de monto y visualización del resultado | UI/UX | 4h | S/ 100 | 15 Abr |
| 1.4 | Estructura de tasas y datos de conversión (Mock) | Core Logic | 4h | S/ 100 | 16 Abr |
| 1.5 | Función de cálculo de conversión | Core Logic | 5h | S/ 125 | 16 Abr |
| 1.6 | Formateo de moneda, símbolos y separadores | Core Logic | 3h | S/ 75 | 16 Abr |
| 1.7 | Función de intercambio (Swap) origen/destino | Refinamiento | 3h | S/ 75 | 16 Abr |
|  | **REQ 2: Registro de Usuario y Gestión de Historial** |  |  |  |  |
| 2.1 | Vistas de Registro / Login de usuario | Auth | 6h | S/ 150 | 17 Abr |
| 2.2 | Lógica de persistencia: nombre, correo, teléfono | Auth | 5h | S/ 125 | 17 Abr |
| 2.3 | Vista "Mi Perfil" y ofertas personalizadas | UI/UX | 5h | S/ 125 | 17 Abr |
| 2.4 | Vista de historial de transacciones del usuario | Historial | 6h | S/ 150 | 18 Abr |
| 2.5 | Indicador visual de estado (Pendiente/En proceso/Completado) | Historial | 4h | S/ 100 | 18 Abr |
| 2.6 | Widget de consumo acumulado (control de gastos) | Historial | 4h | S/ 100 | 18 Abr |
| 2.7 | Sistema de alertas de llegada de envío al cliente | Notif. | 5h | S/ 125 | 21 Abr |
| 2.8 | Panel Admin: listado de clientes frecuentes | Admin | 5h | S/ 125 | 21 Abr |
| 2.9 | Lógica de notificación automática por cambio de estado | Notif. | 6h | S/ 150 | 21 Abr |
|  | **REQ 3: Flujo de Venta Indirecta y Aprobación Multi-Nivel** |  |  |  |  |
| 3.1 | Campo de código de vendedor en formulario de envío | Ventas | 4h | S/ 100 | 22 Abr |
| 3.2 | Notificación al vendedor por nueva transacción vinculada | Notif. | 5h | S/ 125 | 22 Abr |
| 3.3 | Interfaz de registro manual de venta para vendedor | Ventas | 5h | S/ 125 | 22 Abr |
| 3.4 | Tablero "Ventas Pendientes de Validar" (vendedor) | Ventas | 6h | S/ 150 | 22 Abr |
| 3.5 | Formulario de edición y ajuste de datos del envío | Ventas | 6h | S/ 150 | 23 Abr |
| 3.6 | Flujo "Aprobar y Escalar" al panel del dueño | Workflow | 7h | S/ 175 | 23 Abr |
| 3.7 | Cola de procesamiento por timestamp (Admin) | Admin | 6h | S/ 150 | 23 Abr |
| 3.8 | Alertas en tiempo real para el dueño (venta validada) | Notif. | 6h | S/ 150 | 24 Abr |
| 3.9 | Consolidado de ventas por vendedor (día/mes) | Reportes | 5h | S/ 125 | 24 Abr |
|  | **REQ 4: Gestión de Observaciones y Soporte Operativo** |  |  |  |  |
| 4.1 | Botón "Solicitar Información Adicional" con observación | Admin | 5h | S/ 125 | 24 Abr |
| 4.2 | Módulo de carga de comprobante final (Admin) | Admin | 5h | S/ 125 | 25 Abr |
| 4.3 | Cambio automático de estado a "Observado" | Workflow | 5h | S/ 125 | 25 Abr |
| 4.4 | Centro de notificaciones de error para vendedor | Notif. | 5h | S/ 125 | 25 Abr |
| 4.5 | Interfaz de re-edición rápida con un clic | Ventas | 5h | S/ 125 | 25 Abr |
| 4.6 | Canal de notificación al cliente (dato requerido) | Notif. | 4h | S/ 100 | 26 Abr |
| 4.7 | Visor y descarga del comprobante de transferencia | UI/UX | 4h | S/ 100 | 26 Abr |
| 4.8 | Log de trazabilidad: quién observó, qué, quién corrigió | Core Logic | 6h | S/ 150 | 26 Abr |
|  | **REQ 5: Gestión de Vendedores y Comisiones** |  |  |  |  |
| 5.1 | Formulario de alta de vendedores con datos personales | Admin | 5h | S/ 125 | 26 Abr |
| 5.2 | Generador de código único alfanumérico por vendedor | Core Logic | 4h | S/ 100 | 26 Abr |
| 5.3 | Configuración de reglas de comisión (fijo o porcentaje) | Admin | 5h | S/ 125 | 26 Abr |
| 5.4 | Motor de cálculo de ganancias al finalizar transacción | Core Logic | 7h | S/ 175 | 26 Abr |
| 5.5 | Monedero virtual del vendedor (saldo en tiempo real) | UI/UX | 5h | S/ 125 | 27 Abr |
| 5.6 | Registro de liquidaciones (marcar comisiones como pagadas) | Admin | 4h | S/ 100 | 27 Abr |
| 5.7 | Dashboard diario del dueño (total enviado, ganancia, volumen) | BI | 7h | S/ 175 | 27 Abr |
| 5.8 | Reporte de rendimiento y ranking de vendedores | BI | 5h | S/ 125 | 27 Abr |
| 5.9 | Exportación de cierre diario en CSV/PDF | Reportes | 5h | S/ 125 | 27 Abr |
|  | **REQ 6: Configuración de Matriz de Divisas y Corredores** |  |  |  |  |
| 6.1 | Catálogo maestro de monedas (Nombre, Símbolo, ISO, País) | Admin | 6h | S/ 150 | 28 Abr |
| 6.2 | CRUD de divisas (agregar, editar, activar/desactivar) | Admin | 5h | S/ 125 | 28 Abr |
| 6.3 | Definición de pares de conversión (corredores habilitados) | Admin | 6h | S/ 150 | 28 Abr |
| 6.4 | Interfaz matriz de corredores (vista de habilitar/deshabilitar) | Admin | 5h | S/ 125 | 28 Abr |
| 6.5 | API/Servicio de rutas dinámicas para el simulador | Core Logic | 6h | S/ 150 | 28 Abr |
| 6.6 | Validación de disponibilidad en tiempo real (ruta desactivada) | Core Logic | 5h | S/ 125 | 28 Abr |
|  | **REQ 7: Consola de Gestión de Tasas y Margen de Utilidad** |  |  |  |  |
| 7.1 | Vista unificada de pares activos con tasa actual | Admin | 5h | S/ 125 | 29 Abr |
| 7.2 | Input de tasa base por par de divisas | Admin | 4h | S/ 100 | 29 Abr |
| 7.3 | Selector de margen de ganancia (por par o global) | Admin | 5h | S/ 125 | 29 Abr |
| 7.4 | Lógica de cálculo inverso (tasa base + margen = tasa cliente) | Core Logic | 7h | S/ 175 | 29 Abr |
| 7.5 | Visualizador "Ganancia por Operación" en tiempo real | UI/UX | 5h | S/ 125 | 29 Abr |
| 7.6 | Actualización instantánea de tasas en el simulador público | Core Logic | 6h | S/ 150 | 29 Abr |
| 7.7 | Log de historial de cambios de tasas y márgenes | Core Logic | 5h | S/ 125 | 29 Abr |
|  | **REQ 8: Módulo de Incentivos y Beneficios Variables** |  |  |  |  |
| 8.1 | Interfaz de carga de comprobante (Admin) con selector de archivo | Admin | 4h | S/ 100 | 30 Abr |
| 8.2 | Gatillo automático: estado "Completado" + notificación final | Workflow | 6h | S/ 150 | 30 Abr |
| 8.3 | Panel de bonos para clientes fieles (% adicional en recibo) | Admin | 6h | S/ 150 | 30 Abr |
| 8.4 | Configuración de "Vendedor Estrella" (comisión extra por meta) | Admin | 5h | S/ 125 | 30 Abr |
| 8.5 | Motor de reglas flexibles (bonos temporales o permanentes) | Core Logic | 8h | S/ 200 | 30 Abr |
| 8.6 | Display "Bono Aplicado" en el simulador del cliente | UI/UX | 4h | S/ 100 | 30 Abr |
| 8.7 | Desglose de comisión extra en monedero del vendedor | UI/UX | 4h | S/ 100 | 30 Abr |
|  | **SUBTOTAL DESARROLLO** |  | **365h** | **S/ 9,125** |  |
|  | Setup Infraestructura (VPS, dominio, SSL, CI/CD básico) | DevOps | 10h | S/ 350 | 16 Abr |
|  | Pruebas QA, correcciones y entrega final documentada | QA | 15h | S/ 450 | 28-30 Abr |
|  | **TOTAL DEL PROYECTO** |  | **390h** | **S/ 9,925** | **30 Abr 2025** |

**4. Cronograma de Ejecución (14 días hábiles)**

**Inicio: Lunes 14 de Abril de 2025   |   Entrega Final: Miércoles 30 de Abril de 2025**

| **Semana** | **Actividades** | **Requerimientos** | **Fechas** | **Entregable** |
| --- | --- | --- | --- | --- |
| **Semana 1 (Días 1-3)** | Setup de infraestructura, arquitectura modular, módulo simulador completo + autenticación y perfil | **REQ 1, REQ 2 (Bloque A)** | 14 Abr - 17 Abr | **Simulador funcional + Login/Registro** |
| **Semana 1 (Días 4-5)** | Historial de transacciones, notificaciones básicas y panel admin inicial | **REQ 2 (Bloques B y C)** | 17 Abr - 19 Abr | **Panel usuario completo** |
| **Semana 2 (Días 6-7)** | Flujo venta indirecta, aprobación multi-nivel y feedback loop de observaciones | **REQ 3, REQ 4** | 21 Abr - 24 Abr | **Flujo vendedor → dueño** |
| **Semana 2 (Días 8-9)** | Gestión de vendedores, comisiones, matriz de divisas y consola de tasas | **REQ 5, REQ 6, REQ 7** | 24 Abr - 28 Abr | **Motor financiero completo** |
| **Semana 2 (Día 10)** | Módulo de incentivos, pruebas QA, correcciones y entrega final documentada | **REQ 8 + QA** | 28 Abr - 30 Abr | **Sistema completo entregado** |

* El cronograma asume disponibilidad del cliente para revisiones rápidas (máx. 4h de respuesta) en los hitos de validación de cada semana. Retrasos en feedback del cliente podrían afectar las fechas de entrega.

**5. Resumen Financiero por Módulo**

| **Concepto** | **Horas** | **Monto (S/)** |
| --- | --- | --- |
| REQ 1: Simulador de Divisas | 30h | S/ 750 |
| REQ 2: Registro y Gestión de Historial | 46h | S/ 1,150 |
| REQ 3: Flujo de Venta Indirecta | 50h | S/ 1,250 |
| REQ 4: Observaciones y Soporte Operativo | 39h | S/ 975 |
| REQ 5: Gestión de Vendedores y Comisiones | 47h | S/ 1,175 |
| REQ 6: Matriz de Divisas y Corredores | 33h | S/ 825 |
| REQ 7: Consola de Tasas y Márgenes | 37h | S/ 925 |
| REQ 8: Incentivos y Beneficios Variables | 37h | S/ 925 |
| Setup Infraestructura y DevOps | 10h | S/ 350 |
| QA, Pruebas y Entrega Final | 15h | S/ 450 |
| **TOTAL DEL PROYECTO** | **390h** | **S/ 9,925** |

* Tarifa promedio aplicada: S/ 25/hora (desarrollador Senior Full-Stack en mercado peruano, rango de mercado S/ 20-35/hora para perfil Laravel + Vue 3).

**6. Condiciones Comerciales**

| **Condición** | **Detalle** |
| --- | --- |
| **Precio Total** | S/ 9,925 soles (Nueve mil novecientos veinticinco soles) |
| **Equivalente USD (ref.)** | Aprox. USD 2,690 (T/C referencial S/ 3.69) |
| **Forma de Pago** | 50% al inicio del proyecto (S/ 4,962.50) + 50% a la entrega final (S/ 4,962.50) |
| **Plazo de Ejecución** | 14 días hábiles (14 de abril al 30 de abril de 2025) |
| **Revisiones incluidas** | 2 rondas de correcciones menores incluidas sin costo adicional |
| **Soporte Post-Entrega** | 7 días de soporte técnico gratuito ante bugs reportados |
| **Stack Tecnológico** | Laravel 12 + Inertia.js + Vue 3 + Tailwind CSS + Arquitectura Modular |
| **Entregables** | Código fuente documentado, variables de entorno de ejemplo, guía de despliegue |
| **Validez de la Oferta** | 15 días calendario desde la fecha de emisión |

**7. Supuestos y Exclusiones**

**Supuestos del proyecto:**

- El cliente proporcionará las credenciales de acceso al entorno de hosting antes del inicio.

- Las decisiones sobre flujos de negocio no documentados serán respondidas en un máximo de 4 horas hábiles.

- El diseño UI/UX seguirá el estilo definido en la propuesta (mobile-first con Tailwind CSS); cambios de branding significativos se cotizarán por separado.

- La integración con pasarelas de pago externas o APIs bancarias NO está incluida en este alcance.

- Las notificaciones se implementarán vía base de datos + broadcasting (Pusher/Reverb); SMS o emails transaccionales requieren configuración adicional del cliente.

**Exclusiones (fuera de alcance):**

- Integración con APIs de tasas de cambio en tiempo real (Fixer.io, CurrencyLayer, etc.)

- Aplicación móvil nativa (iOS/Android); el sistema es responsive web.

- Migración de datos desde sistemas anteriores.

- Capacitación al equipo del cliente (se puede cotizar por separado).

- Mantenimiento continuo posterior al período de soporte de 7 días.

**8. Stack Tecnológico**

- Backend: PHP 8.3 / Laravel 12 — Arquitectura modular con patrones Repository, Service y SOLID

- Frontend: Vue 3 (Composition API) + Inertia.js + Tailwind CSS 3

- Base de Datos: MySQL 8 / PostgreSQL 15

- Tiempo Real: Laravel Reverb (WebSockets) o Pusher para notificaciones

- Infraestructura: VPS Linux (Ubuntu 22.04), Nginx, SSL Let's Encrypt

- Control de Versiones: Git con ramas por feature (GitHub / GitLab)

**9. Observaciones Finales**

La presente cotización ha sido elaborada en base al detalle de requerimientos funcionales contenido en la propuesta comercial del proyecto Cambio J. El precio refleja el estándar de mercado para desarrollo senior en Perú (Lima Metropolitana), con énfasis en calidad de código, arquitectura escalable y cumplimiento de las mejores prácticas de desarrollo.

Para la aceptación formal, se requiere la firma de un Acuerdo de Servicio (Service Agreement) que detalle los hitos de pago, criterios de aceptación y política de cambios de alcance (Change Requests).

*Documento generado el 14 de Abril de 2025  —  Válido hasta el 29 de Abril de 2025*

---

## ANEXO: ESTADO ACTUAL DEL PROYECTO
**Última actualización:** 20 de Abril de 2026  
**Analista:** Claude Sonnet 4.5

### Resumen de Avance

| REQ | Nombre | Horas Plan | Horas Real | % Completo | Estado |
|-----|--------|------------|------------|------------|--------|
| 1 | Simulador de Divisas | 30h | 30h | 100% | ✅ COMPLETO |
| 2 | Registro y Autenticación | 46h | 41h | 90% | 🔄 CASI COMPLETO |
| 3 | Venta Indirecta | 50h | 20h | 40% | 🔄 EN PROGRESO |
| 4 | Observaciones | 39h | 0h | 0% | ⏸️ PENDIENTE |
| 5 | Vendedores y Comisiones | 47h | 16h | 35% | 🔄 EN PROGRESO |
| 6 | Matriz de Divisas | 33h | 0h | 0% | ⏸️ PENDIENTE |
| 7 | Consola de Tasas | 37h | 18h | 50% | 🔄 EN PROGRESO |
| 8 | Incentivos y Bonos | 37h | 0h | 0% | ⏸️ PENDIENTE |
| **TOTAL** | **Desarrollo** | **319h** | **125h** | **39%** | **EN PROGRESO** |

### Componentes Implementados

**Modelos (4):**
- ✅ User
- ✅ ExchangeRate
- ✅ Sale
- ✅ Seller

**Controladores (6 + 9 auth):**
- ✅ ExchangeRateController (completo)
- ✅ Auth/* (Breeze - completo)
- ✅ ProfileController (completo)
- 🔄 SaleController (CRUD + bulk)
- 🔄 SellerController (CRUD básico)
- ⏸️ ReportController (mínimo)

**Vistas (38 archivos):**
- 4 vistas con paleta Cambio J
- 9 vistas con Alpine.js
- 38 total (auth, perfil, tasas, ventas, vendedores, reportes)

**Rutas (48 activas)**

### Próximos Pasos Recomendados

**Prioridad CRÍTICA:**
1. Completar REQ 2 - Crear módulo de transacciones (5h est.)
2. Aplicar paleta Cambio J a vistas de ventas y vendedores (4h est.)
3. Implementar workflow de aprobación REQ 3 (30h est.)

**Prioridad ALTA:**
4. Motor de comisiones REQ 5 (20h est.)
5. Sistema de observaciones REQ 4 (39h est.)

**Prioridad MEDIA:**
6. Matriz de divisas REQ 6 (33h est.)
7. Completar consola de tasas REQ 7 (19h est.)
8. Incentivos y bonos REQ 8 (37h est.)

### Horas Restantes Estimadas
- **Total pendiente:** 194 horas
- **Progreso actual:** 39% completado
- **Tiempo proyectado para completar:** ~13 días hábiles (15h/día)

### Documentación Generada
- `/docs/requirements/0-analisis-estado-actual/reporte-final.md` - Análisis completo
- `/docs/requirements/00-sistema-gestion-pm/requirement.md` - Sistema de gestión
- `/docs/checkpoints/` - Para gestión de contexto

**Referencia completa:** Ver `reporte-final.md` para análisis detallado por REQ.