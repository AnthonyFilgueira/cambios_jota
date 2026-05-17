# Proyecto: Cambio J
## Propuesta Técnica y Funcional — v6.0

**Autor:** Anthony Filgueira
**Fecha:** Mayo 2026
**Estado:** Documento vivo — en revisión activa

> 📸 **Nota sobre las imágenes:** Cada funcionalidad incluye una referencia `[ver pantalla]` que apunta al screenshot del wireframe correspondiente. Las imágenes se organizan en la carpeta `img/` junto a este documento.

---

## Arquitectura de Roles

| Rol | Acceso | Función |
|-----|--------|---------|
| **Visitante** | Simulador público (sin login) | Cotiza libremente, sin registro |
| **Cliente** | App completa (con registro) | Envía dinero usando código de vendedor |
| **Vendedor** | Panel de vendedor | Revisa, aprueba u observa solicitudes de sus clientes |
| **Dueño / Admin** | Panel de administración total | Ejecuta transferencias, gestiona cuentas, tasas, vendedores y comisiones |

---

## Flujo Principal de Operación

```
[Simulador público]
  → El visitante cotiza libremente sin necesidad de cuenta
  → Si quiere enviar dinero: click en "Enviar ahora"
        ↓
[Registro / Login del cliente]
  → Para registrarse, el cliente DEBE ingresar un código de vendedor válido
  → El código define el vendedor al que quedará vinculado desde el inicio
        ↓
[El cliente ve las cuentas bancarias habilitadas]
  → Desbloqueadas por el código de vendedor ingresado en el registro
  → Las cuentas son administradas por el dueño
        ↓
[El cliente completa los datos de la operación]
  → Elige el tipo de operación de destino (Transferencia Bancaria o Pago Móvil)
        ↓
[El cliente realiza el depósito y sube el comprobante]
        ↓
[La solicitud llega al VENDEDOR]
  → Aprobar → escala al dueño (bloqueada para el vendedor)
  → Observar → devuelve con motivo obligatorio
  → Denegar  → rechaza con motivo obligatorio
        ↓ (si aprueba)
[La solicitud aprobada llega al DUEÑO]
  → Aprobar → ejecuta la transferencia
  → Observar → devuelve al vendedor con motivo (notificación inmediata)
  → Denegar  → rechaza con motivo (notificación inmediata al vendedor)
        ↓ (si aprueba)
[El dueño ejecuta la transferencia y sube el comprobante]
        ↓
[Cierre del ciclo]
  → Cliente y vendedor reciben notificación + comprobante descargable
  → El sistema calcula automáticamente ganancia neta y comisión del vendedor
```

---

## Requerimiento 1: Simulador de Divisas (Público — Sin Login)

**Objetivo:** Herramienta de cotización libre y accesible para cualquier visitante. No requiere registro. Es la puerta de entrada al negocio.

![Cotizador público](img/01_cotizador.png)
*Pantalla: Cotizador público — `01_cotizador.html`*

![Resultado de cotización](img/02_resultado-cotizacion.png)
*Pantalla: Resultado de cotización — `02_resultado-cotizacion.html`*

### Bloque A: Estructura y Estilo (UI/UX)
- [ ] **Tarea A.1: Maquetado del Contenedor Principal:** Crear el layout base mobile-first con el branding "Cambio J". [ver pantalla: `01_cotizador.html`]
- [ ] **Tarea A.2: Componentes de Selección de Moneda:** Implementar los selectores para moneda de origen y moneda de destino, filtrados según los corredores habilitados por el dueño. [ver pantalla: `01_cotizador.html`]
- [ ] **Tarea A.3: Input de Monto y Visualización de Resultado:** Crear el campo de entrada numérica y el área donde se refleja el cálculo en tiempo real. [ver pantalla: `02_resultado-cotizacion.html`]

### Bloque B: Lógica de Negocio (Core)
- [ ] **Tarea B.1: Consumo de Tasas desde el Panel Admin:** El simulador debe leer las tasas y márgenes configurados por el dueño — nunca valores hardcodeados.
- [ ] **Tarea B.2: Función de Cálculo de Conversión:** Lógica que procesa el monto según la tasa y el margen de ganancia definido por el admin.
- [ ] **Tarea B.3: Formateo de Moneda y Localización:** Máscaras de moneda (símbolos, decimales, separadores de miles) según el par seleccionado. [ver pantalla: `02_resultado-cotizacion.html`]

### Bloque C: Refinamiento y CTA
- [ ] **Tarea C.1: Función de Intercambio (Swap):** Botón para invertir moneda de origen y destino. [ver pantalla: `01_cotizador.html`]
- [ ] **Tarea C.2: Botón "Enviar ahora":** CTA visible que lleva al cliente al flujo de registro/login y luego al flujo de envío — manteniendo la cotización activa en sesión. [ver pantalla: `02_resultado-cotizacion.html`]

---

## Requerimiento 2: Registro de Cliente y Gestión de Historial

**Objetivo:** Permitir al cliente crear su cuenta, gestionar sus envíos y consultar su historial. El código de vendedor es obligatorio en el registro — sin él no se puede crear una cuenta.

![Registro de cliente](img/01_registro.png)
*Pantalla: Registro — `01_registro.html`*

![Login](img/02_login.png)
*Pantalla: Login — `02_login.html`*

![Historial de envíos](img/01_historial-envios.png)
*Pantalla: Historial de envíos del cliente — `01_historial-envios.html`*

![Detalle de transacción cliente](img/02_detalle-transaccion.png)
*Pantalla: Detalle de transacción — `02_detalle-transaccion.html`*

![Visor de comprobante](img/03_visor-comprobante.png)
*Pantalla: Visor de comprobante descargable — `03_visor-comprobante.html`*

![Mi perfil](img/04_mi-perfil.png)
*Pantalla: Mi perfil del cliente — `04_mi-perfil.html`*

### Bloque A: Autenticación y Perfil
- [ ] **Tarea A.1: Maquetado de Interfaz de Registro/Login:** Vistas para crear cuenta o iniciar sesión (nombre, correo, teléfono, código de vendedor). El código de vendedor es un campo obligatorio en el registro. [ver pantalla: `01_registro.html`, `02_login.html`]
- [ ] **Tarea A.2: Validación de Código de Vendedor en Registro:** El sistema verifica que el código ingresado exista y esté activo. Si el código es inválido, el registro no procede y se muestra un mensaje de error claro. [ver pantalla: `01_registro.html`]
- [ ] **Tarea A.3: Lógica de Persistencia de Usuario:** Guardar datos del perfil del cliente, incluido el vendedor al que quedó vinculado.
- [ ] **Tarea A.4: Vista de "Mi Perfil":** Área donde el cliente ve sus datos personales, su vendedor asignado y el historial de envíos realizados. [ver pantalla: `04_mi-perfil.html`]

### Bloque B: Seguimiento de Transacciones
- [ ] **Tarea B.1: Vista de Historial de Envíos:** Listado de todas las transacciones del cliente con fecha, monto, estado y vendedor asociado. [ver pantalla: `01_historial-envios.html`]
- [ ] **Tarea B.2: Indicador de Estado de Transacción:** Componente visual con estados: Pendiente → En revisión (vendedor) → Aprobado → En proceso (dueño) → Completado / Observado / Denegado. [ver pantalla: `02_detalle-transaccion.html`]
- [ ] **Tarea B.3: Detalle de Consumo Acumulado:** Widget con el total enviado por el cliente (control de gastos personal). [ver pantalla: `04_mi-perfil.html`]

### Bloque C: Notificaciones al Cliente
- [ ] **Tarea C.1: Notificación de Cambio de Estado:** El cliente recibe aviso cada vez que su transacción cambia de estado.
- [ ] **Tarea C.2: Visor de Comprobante de Envío:** Vista donde el cliente puede ver y descargar el comprobante de transferencia subido por el dueño. [ver pantalla: `03_visor-comprobante.html`]
- [ ] **Tarea C.3: Notificación de Requerimiento de Datos:** Aviso al cliente cuando el vendedor o el dueño soliciten corrección del comprobante.

---

## Requerimiento 3: Flujo de Envío con Datos del Destinatario

**Objetivo:** Gestionar el proceso completo de una solicitud de envío. El formulario se divide en 4 secciones secuenciales.

![Confirmar vendedor asignado](img/01_ingresar-codigo-vendedor.png)
*Pantalla: Confirmar vendedor asignado al iniciar envío — `01_ingresar-codigo-vendedor.html`*

![Formulario de envío completo](img/02_formulario-envio.png)
*Pantalla: Formulario de envío (4 secciones: cotización, datos receptor, datos transferencia, notas) — `02_formulario-envio.html`*

![Subir comprobante cliente](img/03_subir-comprobante.png)
*Pantalla: Carga del comprobante de depósito — `03_subir-comprobante.html`*

![Confirmación de envío](img/04_confirmacion-envio.png)
*Pantalla: Confirmación final del envío — `04_confirmacion-envio.html`*

### Bloque A: Sección 1 — Cotización y Validación del Vendedor
- [ ] **Tarea A.1: Selector de Tasa de Cambio:** El cliente selecciona el par activo (ej. PEN → VES). El selector muestra la tasa vigente. [ver pantalla: `01_ingresar-codigo-vendedor.html`]
- [ ] **Tarea A.2: Código de Vendedor con Validación en Tiempo Real:** Campo que verifica el código contra la API mientras el cliente escribe (debounce 500ms). Tres estados: buscando / encontrado / no encontrado. [ver pantalla: `01_ingresar-codigo-vendedor.html`]
- [ ] **Tarea A.3: Visualización de Cuentas del Vendedor:** Una vez validado el código, se despliegan las cuentas bancarias asignadas a ese vendedor por el admin. [ver pantalla: `02_formulario-envio.html`]
- [ ] **Tarea A.4: Cotizador Múltiple (PEN / USD / EUR):** El cliente puede ingresar el monto en cualquiera de las tres monedas — el sistema recalcula las otras dos en tiempo real. [ver pantalla: `02_formulario-envio.html`]
- [ ] **Tarea A.5: Resumen Visual del Envío:** Cards que muestran "Tú envías S/. X" y "Tu familiar recibe Bs. Y" más la tasa aplicada. [ver pantalla: `02_formulario-envio.html`]

### Bloque B: Sección 2 — Datos del Receptor en Venezuela
- [ ] **Tarea B.1: Selector de Tipo de Operación:** Transferencia Bancaria o Pago Móvil — la elección condiciona qué campos aparecen. [ver pantalla: `02_formulario-envio.html`]
- [ ] **Tarea B.2: Campos Comunes (Ambas Modalidades):** Cédula, banco receptor, teléfono del titular. [ver pantalla: `02_formulario-envio.html`]
- [ ] **Tarea B.3: Campos Exclusivos de Transferencia Bancaria:** Número de cuenta y tipo (Ahorro / Corriente) — solo visibles cuando el tipo es "Transferencia". [ver pantalla: `02_formulario-envio.html`]

### Bloque C: Sección 3 — Datos de la Transferencia desde Perú
- [ ] **Tarea C.1: Documento de Identidad del Titular que Transfiere.** [ver pantalla: `02_formulario-envio.html`]
- [ ] **Tarea C.2: Banco Origen (Seleccionable — Bancos de Perú).** [ver pantalla: `02_formulario-envio.html`]
- [ ] **Tarea C.3: Número de Cuenta Origen.** [ver pantalla: `02_formulario-envio.html`]
- [ ] **Tarea C.4: Carga de Comprobante Bancario:** Imagen o PDF del comprobante (JPG, PNG, PDF — máx. 2MB). La solicitud queda en "Pendiente de revisión por vendedor". [ver pantalla: `03_subir-comprobante.html`]

### Bloque D: Sección 4 — Notas Adicionales
- [ ] **Tarea D.1: Campo de Notas Libres (Opcional).** [ver pantalla: `02_formulario-envio.html`]

### Bloque E: Panel del Vendedor — Revisión de Solicitudes

![Bandeja de solicitudes vendedor](img/04_bandeja-solicitudes.png)
*Pantalla: Bandeja de solicitudes del vendedor — `04_bandeja-solicitudes.html`*

![Detalle de solicitud vendedor](img/02_detalle-solicitud.png)
*Pantalla: Detalle de solicitud con comprobante — `02_detalle-solicitud.html`*

![Aprobar solicitud](img/03_aprobar-solicitud.png)
*Pantalla: Acción de aprobación de solicitud — `03_aprobar-solicitud.html`*

![Observar solicitud](img/04_observar-solicitud.png)
*Pantalla: Acción de observación (motivo obligatorio) — `04_observar-solicitud.html`*

- [ ] **Tarea E.1: Bandeja de Solicitudes Pendientes de Revisión:** Lista de solicitudes de sus clientes ordenadas por timestamp. [ver pantalla: `04_bandeja-solicitudes.html`]
- [ ] **Tarea E.2: Detalle de Solicitud y Comprobante:** Vendedor ve monto, tasa, comprobante del cliente, datos de la transferencia y datos del receptor. [ver pantalla: `02_detalle-solicitud.html`]
- [ ] **Tarea E.3: Acciones del Vendedor:** Aprobar / Observar / Denegar — las dos últimas con motivo obligatorio. [ver pantalla: `03_aprobar-solicitud.html`, `04_observar-solicitud.html`]
- [ ] **Tarea E.4: Sección de Transacciones Ejecutadas:** Lista de transacciones ya aprobadas por el vendedor — bloqueadas, sin posibilidad de edición. [ver pantalla: `05_transacciones.html`]
- [ ] **Tarea E.5: Descarga de Comprobante de Transacción:** Desde ejecutadas, el vendedor puede descargar el comprobante subido por el dueño. Botón visible solo cuando el estado es "Completado". [ver pantalla: `05_transacciones.html`]

### Bloque F: Revisión y Ejecución por el Dueño

![Bandeja admin — transacciones](img/07_transacciones.png)
*Pantalla: Cola de solicitudes aprobadas por vendedores (panel admin) — `07_transacciones.html`*

![Detalle transacción admin](img/08_detalle-transaccion.png)
*Pantalla: Detalle de transacción con acciones del dueño — `08_detalle-transaccion.html`*

![Subir comprobante final dueño](img/09_subir-comprobante-final.png)
*Pantalla: Carga del comprobante de transferencia ejecutada — `09_subir-comprobante-final.html`*

- [ ] **Tarea F.1: Cola de Solicitudes Aprobadas por Vendedores.** [ver pantalla: `07_transacciones.html`]
- [ ] **Tarea F.2: Acciones del Dueño:** Aprobar / Observar / Denegar — Observar y Denegar con motivo obligatorio. [ver pantalla: `08_detalle-transaccion.html`]
- [ ] **Tarea F.3: Notificación de Alta Prioridad:** Alerta en tiempo real al dueño cuando un vendedor aprueba una solicitud.
- [ ] **Tarea F.4: Carga de Comprobante de Transferencia (Dueño):** Una vez aprobada, el dueño sube el comprobante de la transferencia ejecutada — cierra el ciclo y dispara notificaciones al cliente y al vendedor. [ver pantalla: `09_subir-comprobante-final.html`]

---

## Requerimiento 4: Gestión de Observaciones y Soporte Operativo (Feedback Loop)

**Objetivo:** Garantizar que cualquier acción que no sea una aprobación quede registrada con motivo obligatorio y que las notificaciones lleguen al rol correspondiente de forma inmediata.

![Observar solicitud dueño](img/04_observar-solicitud.png)
*Pantalla: Observación con motivo obligatorio (dueño → vendedor) — `04_observar-solicitud.html`*

### Bloque A: Observaciones del Dueño al Vendedor
- [ ] **Tarea A.1: Función "Observar / Denegar" (Dueño):** Motivo obligatorio — no se puede confirmar la acción sin completarlo. [ver pantalla: `04_observar-solicitud.html`]
- [ ] **Tarea A.2: Notificación Inmediata al Vendedor:** Al observar o denegar, el vendedor recibe una alerta en tiempo real con el motivo exacto.
- [ ] **Tarea A.3: Sistema de Cambio de Estado:** La transacción regresa a la bandeja del vendedor (si fue Observada) o se cierra como Denegada, con log del motivo.

### Bloque B: Observaciones del Vendedor al Cliente
- [ ] **Tarea B.1: Centro de Notificaciones de Errores (Vendedor):** Alerta indicando qué transacción fue observada por el dueño y el motivo exacto.
- [ ] **Tarea B.2: Función "Observar / Denegar" (Vendedor):** El vendedor puede devolver una solicitud al cliente con motivo obligatorio, o denegarla. [ver pantalla: `04_observar-solicitud.html`]
- [ ] **Tarea B.3: Interfaz de Re-edición Rápida:** El vendedor corrige el dato observado y reenvía a aprobación con un solo clic.

### Bloque C: Comunicación con el Cliente Final
- [ ] **Tarea C.1: Canal de Notificación de Requerimiento:** Aviso al cliente informando que se requiere una corrección para procesar su envío.
- [ ] **Tarea C.2: Historial de Trazabilidad (Log Interno):** Registro completo de quién tomó cada acción, con motivo, fecha y corrección. El motivo siempre queda visible en el historial de la transacción. [ver pantalla: `08_detalle-transaccion.html`]

---

## Requerimiento 5: Módulo de Bancos por País (Admin)

**Objetivo:** Dar al dueño control sobre qué bancos están disponibles para cada país de operación. Esta lista alimenta los selectores del formulario de envío.

> Esta funcionalidad se gestiona dentro del Requerimiento 11 (gestor jerárquico País → Bancos → Cuentas). Ver pantallas `08_paises.html` y `09_pais-detalle.html` a continuación.

---

## Requerimiento 6: Gestión de Vendedores y Comisiones

**Objetivo:** Dar al dueño control total sobre su fuerza de ventas: alta de vendedores, asignación de cuentas bancarias, reglas de comisión y visibilidad de rendimiento.

![Lista de vendedores](img/02_vendedores.png)
*Pantalla: Lista de vendedores activos e inactivos — `02_vendedores.html`*

![Crear / editar vendedor](img/03_form-vendedor.png)
*Pantalla: Formulario de alta y edición de vendedor — `03_form-vendedor.html`*

![Asignación de comisión](img/04_comision-vendedor.png)
*Pantalla: Configuración de regla de comisión por vendedor — `04_comision-vendedor.html`*

![Catálogo global de cuentas](img/03_cuentas-bancarias.png)
*Pantalla: Catálogo global de cuentas bancarias del negocio — `03_cuentas-bancarias.html`*

![Asignación de cuentas a vendedores](img/04_asignacion-cuentas.png)
*Pantalla: Asignación de cuentas del catálogo a vendedores — `04_asignacion-cuentas.html`*

### Bloque A: Administración de Vendedores (Dueño)
- [ ] **Tarea A.1: Módulo de Registro de Vendedores:** Formulario de alta con datos personales, contacto y documento de identidad. [ver pantalla: `03_form-vendedor.html`]
- [ ] **Tarea A.2: Generador de Código Único:** Código alfanumérico único asignado automáticamente (ej. VND-004) — visible y copiable, no editable. [ver pantalla: `03_form-vendedor.html`]
- [ ] **Tarea A.3: Configuración de Reglas de Comisión por Vendedor:** Porcentaje del monto enviado o monto fijo por transacción completada. Borrado lógico — al cambiar la regla, la anterior se archiva con fecha de cierre. [ver pantalla: `04_comision-vendedor.html`]

### Bloque B: Catálogo Global de Cuentas Bancarias y Asignación a Vendedores
- [ ] **Tarea B.1: Catálogo Centralizado de Cuentas (Dueño):** El dueño registra y administra un catálogo global de cuentas bancarias del negocio. [ver pantalla: `03_cuentas-bancarias.html`]
- [ ] **Tarea B.2: Asignación de Cuentas a Vendedores (Reutilizable):** La misma cuenta puede estar asignada a múltiples vendedores simultáneamente. [ver pantalla: `04_asignacion-cuentas.html`]
- [ ] **Tarea B.3: Gestión Independiente de Cuenta y Asignación:** Modificar o desactivar una cuenta en el catálogo no afecta el perfil del vendedor.
- [ ] **Tarea B.4: Historial de Asignaciones:** Registro de cada cambio de asignación por vendedor — fecha, cuenta añadida o removida.

### Bloque C: Lógica de Comisiones y Finanzas

![Mi código de vendedor](img/05_mi-codigo.png)
*Pantalla: Código único del vendedor y métricas — `05_mi-codigo.html`*

![Ganancias del vendedor](img/06_ganancias-vendedor.png)
*Pantalla: Monedero y ganancias del vendedor — `06_ganancias-vendedor.html`*

- [ ] **Tarea C.1: Motor de Cálculo de Ganancias:** Al cerrar una transacción el sistema desglosa: [Monto enviado] → [Ganancia neta Cambio J] + [Comisión vendedor]. [ver pantalla: `17_comisiones.html`]
- [ ] **Tarea C.2: Monedero Virtual del Vendedor:** Vista donde el vendedor ve su saldo acumulado de comisiones en tiempo real, desglosado por transacción. [ver pantalla: `06_ganancias-vendedor.html`]
- [ ] **Tarea C.3: Registro de Liquidaciones:** El dueño marca comisiones como "Pagadas" al vendedor, manteniendo el historial contable limpio. [ver pantalla: `17_comisiones.html`]

### Bloque D: Business Intelligence (Dueño)

![Dashboard admin](img/01_dashboard.png)
*Pantalla: Tablero de operación diaria del dueño — `01_dashboard.html`*

![Ganancias generales del negocio](img/16_ganancias.png)
*Pantalla: Reporte de ganancias del negocio con KPIs y gráficos — `16_ganancias.html`*

![Reporte de comisiones](img/17_comisiones.png)
*Pantalla: Reporte de comisiones por vendedor y período — `17_comisiones.html`*

- [ ] **Tarea D.1: Tablero de Operación Diaria:** Métricas clave en tiempo real: total enviado hoy, ganancia neta estimada, volumen por vendedor. [ver pantalla: `01_dashboard.html`]
- [ ] **Tarea D.2: Reporte de Rendimiento de Vendedores:** Ranking de vendedores por volumen generado (día / mes). [ver pantalla: `16_ganancias.html`]
- [ ] **Tarea D.3: Exportación de Datos Operativos:** Descarga de resumen diario en CSV/PDF para control contable del dueño. [ver pantalla: `16_ganancias.html`, `17_comisiones.html`]

---

## Requerimiento 7: Configuración de Matriz de Divisas y Corredores de Envío

**Objetivo:** Dar al dueño control total sobre qué monedas opera y qué rutas de envío están disponibles. Alimenta directamente al simulador público.

![Gestión de monedas y corredores](img/11_monedas.png)
*Pantalla: Catálogo de monedas y matriz de corredores de envío — `11_monedas.html`*

### Bloque A: Gestión de Monedas y Países
- [ ] **Tarea A.1: Catálogo Maestro de Monedas:** Interfaz para registrar monedas (Nombre, Símbolo, Código ISO) asociadas a un país. [ver pantalla: `11_monedas.html`]
- [ ] **Tarea A.2: CRUD de Divisas:** Agregar, editar, activar o desactivar monedas según disponibilidad del negocio. [ver pantalla: `11_monedas.html`]

### Bloque B: Configuración de Rutas de Envío (Corredores)
- [ ] **Tarea B.1: Definición de Pares de Conversión:** El dueño selecciona una moneda origen y le asigna las monedas destino permitidas. [ver pantalla: `11_monedas.html`]
- [ ] **Tarea B.2: Interfaz de Gestión de Corredores:** Matriz visual donde el dueño ve de un vistazo qué rutas están habilitadas. [ver pantalla: `11_monedas.html`]

### Bloque C: Integración con el Simulador
- [ ] **Tarea C.1: Filtro Dinámico de Rutas:** El simulador filtra automáticamente según los corredores activos. [ver pantalla: `01_cotizador.html`]
- [ ] **Tarea C.2: Validación de Disponibilidad en Tiempo Real:** Si el dueño desactiva una ruta mientras un cliente cotiza, el simulador muestra un aviso inmediato.

---

## Requerimiento 8: Consola de Gestión de Tasas y Margen de Utilidad

**Objetivo:** Centralizar el control de tasas de cambio y márgenes de ganancia.

![Gestión de tasas](img/04_tasas.png)
*Pantalla: Consola de tasas de cambio y márgenes — `04_tasas.html`*

![Tasas vista vendedor](img/05_tasas.png)
*Pantalla: Vista de tasas del día (referencia del vendedor) — `05_tasas.html`*

### Bloque A: Panel de Control de Tasas
- [ ] **Tarea A.1: Vista Unificada de Pares Activos:** Tabla con todos los pares configurados y su tasa actual en una sola pantalla. [ver pantalla: `04_tasas.html`]
- [ ] **Tarea A.2: Input de Tasa Base por Par:** Campo para que el dueño defina el valor de mercado o valor base de cada moneda. [ver pantalla: `04_tasas.html`]
- [ ] **Tarea A.3: Selector de Margen de Ganancia:** Control para definir el porcentaje de ganancia por par o de forma global (ej. 3%, 5%, 10%). [ver pantalla: `04_tasas.html`]

### Bloque B: Simulador Interno de Rentabilidad
- [ ] **Tarea B.1: Lógica de Cálculo Inverso:** El sistema calcula cuánto recibe el cliente final después de aplicar el margen. El dueño ve el resultado antes de publicar la tasa. [ver pantalla: `04_tasas.html`]
- [ ] **Tarea B.2: Visualizador de "Ganancia por Operación":** En tiempo real: cuánto dinero neto queda para el negocio por cada unidad de moneda enviada. [ver pantalla: `04_tasas.html`]

### Bloque C: Sincronización con el Simulador Público
- [ ] **Tarea C.1: Actualización Instantánea de Tasas:** Cualquier cambio en la consola admin se refleja de inmediato en el simulador que ve el cliente.
- [ ] **Tarea C.2: Historial de Cambios de Tasa:** Log interno para que el dueño vea cómo han fluctuado sus tasas y márgenes. [ver pantalla: `10_auditoria.html`]

---

## Requerimiento 9: Módulo de Incentivos y Beneficios Variables

**Objetivo:** Herramienta de retención y motivación. El dueño puede crear bonos para clientes frecuentes y premios para vendedores de alto rendimiento.

![Gestión de incentivos](img/12_incentivos.png)
*Pantalla: Gestión de incentivos y bonos — `12_incentivos.html`*

![Beneficios por vendedor/cliente](img/05_beneficios.png)
*Pantalla: Asignación de beneficios a vendedores y clientes — `05_beneficios.html`*

### Bloque A: Gestión de Comprobantes y Cierre de Ciclo
- [ ] **Tarea A.1: Interfaz de Carga de Comprobante (Dueño):** Selector de archivos para subir la imagen de la transferencia realizada al destinatario final. [ver pantalla: `09_subir-comprobante-final.html`]
- [ ] **Tarea A.2: Gatillo de Finalización:** Al subir el comprobante, el estado cambia automáticamente a "Completado" y se disparan las notificaciones finales al cliente y al vendedor.

### Bloque B: Configuración de Reglas de Fidelización
- [ ] **Tarea B.1: Panel de Bonos para Clientes:** Interfaz para asignar porcentajes adicionales a clientes específicos o por volumen acumulado. [ver pantalla: `05_beneficios.html`]
- [ ] **Tarea B.2: Configuración de "Vendedor Estrella":** Regla de negocio que suma un porcentaje extra a la comisión base al alcanzar metas definidas por el dueño. [ver pantalla: `05_beneficios.html`]
- [ ] **Tarea B.3: Motor de Reglas Flexibles:** Beneficios temporales (ej. "Bono solo por fin de semana") o permanentes, sin cambios en el código. [ver pantalla: `12_incentivos.html`]

### Bloque C: Visualización de Beneficios
- [ ] **Tarea C.1: Display de "Bono Aplicado" en Simulador:** El cliente ve cuánto extra recibe gracias a su fidelidad (ej. "Recibes 900 + 45 de bono"). [ver pantalla: `02_resultado-cotizacion.html`]
- [ ] **Tarea C.2: Desglose de Comisión Extra (Vendedor):** El monedero del vendedor muestra [Comisión Base] + [Bono Estrella]. [ver pantalla: `06_ganancias-vendedor.html`]

---

## Requerimiento 10: Sistema de Auditoría y Trazabilidad Total

**Objetivo:** Garantizar que cada acción quede registrada de forma permanente e inmutable. Ningún registro se elimina físicamente.

![Log de auditoría](img/10_auditoria.png)
*Pantalla: Panel de auditoría con log completo de eventos — `10_auditoria.html`*

### Principio General — Borrado Lógico (Sin Eliminación Física)
Aplica a todas las entidades del sistema sin excepción. Ningún registro se elimina de la base de datos. En su lugar se marca con estado `eliminado / inactivo` y fecha de baja.

### Bloque A: Motor de Auditoría Central
- [ ] **Tarea A.1: Tabla de Log de Auditoría:** Captura automáticamente cada operación de escritura — fecha/hora, usuario, rol, tipo de operación, entidad afectada, ID, datos anteriores y nuevos. [ver pantalla: `10_auditoria.html`]
- [ ] **Tarea A.2: Captura Automática sin Intervención Manual:** El registro se dispara a nivel de sistema — ningún desarrollador ni usuario puede omitirlo.
- [ ] **Tarea A.3: Inmutabilidad del Log:** Los registros de auditoría no pueden editarse ni eliminarse bajo ninguna circunstancia, ni siquiera por el administrador.

### Bloque B: Panel de Auditoría para el Administrador
- [ ] **Tarea B.1: Vista General del Log:** Tabla paginada con todos los eventos, ordenados por fecha descendente. [ver pantalla: `10_auditoria.html`]
- [ ] **Tarea B.2: Detalle de Evento:** Snapshot completo con datos anteriores vs. datos nuevos en formato comparativo. [ver pantalla: `10_auditoria.html`]
- [ ] **Tarea B.3: Filtros de Búsqueda:** Por rango de fechas, usuario/rol, tipo de operación, entidad e ID de registro específico. [ver pantalla: `10_auditoria.html`]
- [ ] **Tarea B.4: Exportación del Log:** Descarga del log filtrado en CSV o PDF para revisión externa o auditoría contable. [ver pantalla: `10_auditoria.html`]

### Bloque C: Auditoría por Módulo
- [ ] **Transacciones:** Cada cambio de estado con usuario, fecha y motivo cuando aplica.
- [ ] **Vendedores:** Alta, modificación de datos, cambio de comisión, desactivación.
- [ ] **Cuentas Bancarias:** Creación, edición, desactivación y cada cambio de asignación.
- [ ] **Países:** Creación, edición de rol, activación y desactivación.
- [ ] **Bancos por País:** Creación, edición y desactivación.
- [ ] **Tasas de Cambio:** Cada modificación con valor anterior, valor nuevo, fecha y usuario.
- [ ] **Usuarios / Clientes:** Creación, modificación de perfil y desactivación.
- [ ] **Bonos e Incentivos:** Creación, modificación de reglas y desactivación.
- [ ] **Comprobantes:** Cada carga con usuario, fecha y referencia al archivo.

---

## Requerimiento 11: Gestor de Países, Bancos y Cuentas por País

**Objetivo:** Módulo jerárquico para administrar con qué países opera el negocio, qué bancos existen en cada uno, y qué cuentas propias tiene registradas para operar allí.

```
País (ej. Perú)
  └── Bancos del país (ej. BCP, Interbank, BBVA)
        [Alimentan el selector "Banco origen" / "Banco receptor" en el formulario del cliente]
  └── Cuentas del negocio en ese país (ej. cuenta BCP de Cambio J)
        [Alimentan el catálogo global de cuentas asignables a vendedores — Req 6]
```

![Gestor de países](img/08_paises.png)
*Pantalla: Lista de países operativos con contadores de bancos y cuentas — `08_paises.html`*

![Detalle de país](img/09_pais-detalle.png)
*Pantalla: Detalle de un país — sección de bancos y sección de cuentas del negocio — `09_pais-detalle.html`*

### Bloque A: Gestión de Países
- [ ] **Tarea A.1: Catálogo de Países Operativos:** El dueño registra los países con nombre, código ISO, bandera/emoji y moneda principal. [ver pantalla: `08_paises.html`]
- [ ] **Tarea A.2: Rol del país en el flujo:** Origen / Destino / Ambos — determina en qué selectores del formulario aparecen sus bancos. [ver pantalla: `08_paises.html`]
- [ ] **Tarea A.3: Activar / Desactivar País:** Un país desactivado desaparece de todos los selectores sin borrar su historial. [ver pantalla: `08_paises.html`]
- [ ] **Tarea A.4: Acceso rápido desde el país a sus bancos y cuentas.** [ver pantalla: `08_paises.html` → `09_pais-detalle.html`]

### Bloque B: Gestión de Bancos por País
- [ ] **Tarea B.1: Catálogo de Bancos por País:** Nombre, código BIC/SWIFT (opcional), estado (activo/inactivo). [ver pantalla: `09_pais-detalle.html`]
- [ ] **Tarea B.2: Rol del banco en los selectores del cliente:** Bancos de países origen → "Banco desde donde transferiste". Bancos de países destino → "Banco receptor". [ver pantalla: `09_pais-detalle.html`]
- [ ] **Tarea B.3: Activar / Desactivar Banco:** Un banco inactivo desaparece de los selectores sin afectar el historial. [ver pantalla: `09_pais-detalle.html`]
- [ ] **Tarea B.4: CRUD completo.** [ver pantalla: `09_pais-detalle.html`]

### Bloque C: Gestión de Cuentas del Negocio por País
- [ ] **Tarea C.1: Cuentas Operativas del Negocio por País:** Banco, número de cuenta, titular, tipo, DNI/RUC, alias interno, estado. [ver pantalla: `09_pais-detalle.html`]
- [ ] **Tarea C.2: Integración con el Catálogo Global (Req 6):** Las cuentas registradas aquí son exactamente las que aparecen en el catálogo global. No hay duplicación — es la misma entidad.
- [ ] **Tarea C.3: Vista de cuentas desde el país:** Todas las cuentas activas e inactivas de un país, con su estado de asignación. [ver pantalla: `09_pais-detalle.html`]
- [ ] **Tarea C.4: Activar / Desactivar Cuenta:** Borrado lógico — el historial contable permanece. [ver pantalla: `09_pais-detalle.html`]

### Bloque D: Vista Consolidada del Gestor
- [ ] **Tarea D.1: Panel principal de países:** Lista con contadores (N bancos activos, N cuentas activas, corredor habilitado sí/no). [ver pantalla: `08_paises.html`]
- [ ] **Tarea D.2: Vista de detalle por país:** Dos secciones: bancos (toggle + editar) y cuentas operativas (estado + N vendedores asignados). [ver pantalla: `09_pais-detalle.html`]
- [ ] **Tarea D.3: Navegación jerárquica clara:** País → detalle del país → bancos y cuentas. Sin saltos entre módulos. [ver pantalla: `08_paises.html` → `09_pais-detalle.html`]

---

## Configuración del Sistema

![Configuración del sistema](img/11_configuracion.png)
*Pantalla: Panel de configuración general del sistema — `11_configuracion.html`*

---

## Resumen de Valor por Rol

### Para el Cliente
- Cotiza sin fricción, sin registro [ver: `01_cotizador.html`, `02_resultado-cotizacion.html`]
- Registro simple con código de vendedor — vinculado desde el primer momento [ver: `01_registro.html`]
- Proceso de envío guiado: elige tipo de operación, registra datos del destinatario, sube comprobante [ver: `02_formulario-envio.html`, `03_subir-comprobante.html`]
- Historial completo con comprobantes descargables [ver: `01_historial-envios.html`, `03_visor-comprobante.html`]
- Notificaciones en cada etapa del proceso

### Para el Vendedor
- Bandeja de trabajo clara con todas sus solicitudes [ver: `04_bandeja-solicitudes.html`]
- Notificaciones inmediatas cuando el dueño observa o deniega, con motivo detallado
- Monedero en tiempo real con sus comisiones [ver: `06_ganancias-vendedor.html`]
- Código único que le da identidad profesional [ver: `05_mi-codigo.html`]
- Recibe comprobante de cierre al completarse cada envío [ver: `05_transacciones.html`]

### Para el Dueño
- Control total: tasas, países y bancos, cuentas, vendedores, comisiones e incentivos [ver: `01_dashboard.html`]
- Doble revisión del flujo: el vendedor filtra, el dueño aprueba o devuelve con motivo [ver: `07_transacciones.html`, `08_detalle-transaccion.html`]
- Tablero de operación diaria con métricas clave [ver: `01_dashboard.html`]
- Cero cuellos de botella: el sistema filtra y organiza, él solo ejecuta
- Trazabilidad completa de cada transacción [ver: `10_auditoria.html`]
- Ningún dato se pierde — todo el historial es permanente y consultable

---

## Índice de Pantallas por Requerimiento

| Pantalla | Archivo | Requerimiento |
|----------|---------|---------------|
| Cotizador público | `01_cotizador.html` | Req 1 |
| Resultado de cotización | `02_resultado-cotizacion.html` | Req 1 |
| Registro de cliente | `01_registro.html` | Req 2 |
| Login | `02_login.html` | Req 2 |
| Historial de envíos | `01_historial-envios.html` | Req 2 |
| Detalle de transacción (cliente) | `02_detalle-transaccion.html` | Req 2 |
| Visor de comprobante | `03_visor-comprobante.html` | Req 2 |
| Mi perfil | `04_mi-perfil.html` | Req 2 |
| Confirmar vendedor asignado | `01_ingresar-codigo-vendedor.html` | Req 3 |
| Formulario de envío completo | `02_formulario-envio.html` | Req 3 |
| Subir comprobante (cliente) | `03_subir-comprobante.html` | Req 3 |
| Confirmación de envío | `04_confirmacion-envio.html` | Req 3 |
| Bandeja de solicitudes (vendedor) | `04_bandeja-solicitudes.html` | Req 3 — Bloque E |
| Detalle de solicitud (vendedor) | `02_detalle-solicitud.html` | Req 3 — Bloque E |
| Aprobar solicitud | `03_aprobar-solicitud.html` | Req 3 — Bloque E |
| Observar solicitud | `04_observar-solicitud.html` | Req 3 — Bloques E y F |
| Transacciones ejecutadas (vendedor) | `05_transacciones.html` | Req 3 — Bloque E |
| Bandeja de transacciones (admin) | `07_transacciones.html` | Req 3 — Bloque F |
| Detalle de transacción (admin) | `08_detalle-transaccion.html` | Req 3 — Bloque F |
| Subir comprobante final (dueño) | `09_subir-comprobante-final.html` | Req 3 — Bloque F |
| Dashboard admin | `01_dashboard.html` | Req 6 — Bloque D |
| Lista de vendedores | `02_vendedores.html` | Req 6 |
| Crear / editar vendedor | `03_form-vendedor.html` | Req 6 |
| Asignación de comisión | `04_comision-vendedor.html` | Req 6 |
| Beneficios vendedor/cliente | `05_beneficios.html` | Req 6 / Req 9 |
| Catálogo global de cuentas | `03_cuentas-bancarias.html` | Req 6 |
| Asignación de cuentas | `04_asignacion-cuentas.html` | Req 6 |
| Mi código (vendedor) | `05_mi-codigo.html` | Req 6 |
| Ganancias del vendedor | `06_ganancias-vendedor.html` | Req 6 |
| Ganancias generales del negocio | `16_ganancias.html` | Req 6 — Bloque D |
| Reporte de comisiones | `17_comisiones.html` | Req 6 — Bloque D |
| Monedas y corredores | `11_monedas.html` | Req 7 |
| Tasas de cambio (admin) | `04_tasas.html` | Req 8 |
| Tasas del día (vendedor) | `05_tasas.html` | Req 8 |
| Gestión de incentivos y bonos | `12_incentivos.html` | Req 9 |
| Log de auditoría | `10_auditoria.html` | Req 10 |
| Gestor de países | `08_paises.html` | Req 11 |
| Detalle de país (bancos + cuentas) | `09_pais-detalle.html` | Req 11 |
| Configuración del sistema | `11_configuracion.html` | Sistema |

---

*Versión 6.0 — Mayo 2026 — Propuesta con referencias visuales a wireframes por funcionalidad · 40 pantallas documentadas · Paleta de marca Cambio J aplicada*
