# INC-003 — Cuentas bancarias del vendedor no filtran por país

## Descripción del problema
Al crear una transacción, las cuentas bancarias del vendedor que se muestran no
corresponden al país de la divisa de origen seleccionada. El vendedor puede tener
cuentas en Perú, Venezuela, Colombia, USA — pero siempre se muestran todas sin filtrar.

## Pasos para reproducir
1. Ir a `/transactions/create`
2. Seleccionar un vendedor que tenga cuentas en múltiples países
3. Cambiar el par de divisa de PEN→VES a USD→VES
4. Observar que las cuentas bancarias NO cambian ni se filtran por el país de la divisa

## Comportamiento esperado
- Par PEN→VES → mostrar solo las cuentas del vendedor en Perú (cuentas en soles)
- Par USD→VES → mostrar solo las cuentas del vendedor en USA (cuentas en dólares)
- Par COP→VES → mostrar solo las cuentas del vendedor en Colombia
- Al cambiar el par: las cuentas deben actualizarse en tiempo real sin recargar la página

## Comportamiento actual
- Se muestran TODAS las cuentas activas del vendedor sin importar el país

## Entorno
- Local (desarrollo)
- Reportado: 2026-05-28
