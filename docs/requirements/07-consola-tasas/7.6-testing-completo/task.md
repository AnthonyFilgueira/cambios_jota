# Tarea 7.6: Testing Completo en Navegador

**Duración estimada:** 3h  
**Estado:** En progreso  

## Objetivo

Probar todas las funcionalidades implementadas en REQ 7 (Consola de Tasas) en el navegador para verificar que todo funciona correctamente antes de avanzar al historial de cambios.

## Checklist de pruebas

- [ ] **Simulador dinámico (welcome.blade.php)**
  - [ ] Cargar página principal
  - [ ] Verificar selector de países (Perú, Argentina, Chile)
  - [ ] Probar cálculos con cada par de divisas
  - [ ] Verificar labels dinámicos y símbolos de moneda
  - [ ] Comprobar tasas correctas desde BD

- [ ] **Vista index (exchange_rates/index.blade.php)**
  - [ ] Listar todas las tasas activas
  - [ ] Probar filtro por divisa (PEN, ARS, CLP)
  - [ ] Probar filtro por estado (activa/inactiva)
  - [ ] Verificar acciones (editar, desactivar)

- [ ] **Formulario create**
  - [ ] Crear nueva tasa PEN→VES
  - [ ] Validar campos obligatorios
  - [ ] Verificar ejemplos en placeholders
  - [ ] Confirmar creación exitosa

- [ ] **Formulario edit**
  - [ ] Editar tasa existente
  - [ ] Cambiar ves_rate
  - [ ] Actualizar referencias BCV
  - [ ] Verificar actualización

- [ ] **Integración con ventas**
  - [ ] Crear venta con tasa activa
  - [ ] Verificar snapshot de tasa en venta
  - [ ] Comprobar cálculos correctos

## Resultado esperado

Todas las funcionalidades deben operar sin errores, mostrando datos correctos y con UX fluida.
