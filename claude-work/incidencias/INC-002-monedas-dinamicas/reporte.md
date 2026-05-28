# INC-002 — Monedas dinámicas

## Descripción del problema
El sistema soporta múltiples pares de divisas (PEN→VES, USD→VES, COP→VES, EUR→VES, etc.)
pero en todas las vistas del frontend siempre aparece "Soles" y el símbolo "S/." sin importar
qué par de divisa esté seleccionado.

## Pasos para reproducir
1. Ir a `/transactions/create`
2. Seleccionar el par de divisa USD → VES
3. Observar que el formulario sigue mostrando "En Soles (PEN)" y "S/."
4. El simulador público en `/` también muestra "Soles peruanos" aunque cambies el par

## Comportamiento esperado
- Al seleccionar USD→VES: mostrar "En Dólares (USD)" y símbolo "$"
- Al seleccionar COP→VES: mostrar "En Pesos Colombianos (COP)" y símbolo "COP"
- Los dashboards del cliente y dueño deben mostrar la moneda correcta de cada transacción

## Comportamiento actual
- Siempre muestra "Soles (PEN)" y "S/." independientemente del par seleccionado

## Entorno
- Local (desarrollo)
- Reportado: 2026-05-28
