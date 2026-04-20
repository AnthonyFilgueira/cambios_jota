# Tarea 3.7: Aplicar paleta Cambio J a vistas de sales

**Estado:** ✅ COMPLETO  
**Tiempo estimado:** 2h

---

## Objetivo

Actualizar las vistas existentes de sales (index, create, bulk-create) para aplicar la paleta de colores Cambio J de forma consistente con el resto del módulo.

---

## Vistas a actualizar

1. `resources/views/sales/index.blade.php`
2. `resources/views/sales/create.blade.php` (si existe)
3. `resources/views/sales/bulk-create.blade.php`

---

## Paleta Cambio J a aplicar

```css
/* Headers de tabla */
bg-gradient-to-r from-purple-700 to-purple-600  /* cj-morado-profundo */

/* Botones primarios */
bg-purple-600 hover:bg-purple-700               /* Acciones principales */

/* Botones secundarios */
bg-teal-500 hover:bg-teal-600                   /* Acciones positivas */

/* Botones peligro */
bg-pink-500 hover:bg-pink-600                   /* Eliminar, rechazar */

/* Badges de estado */
bg-purple-100 text-purple-800                   /* Estados generales */
bg-green-100 text-green-800                     /* Aprobado */
bg-yellow-100 text-yellow-800                   /* Pendiente */
bg-red-100 text-red-800                         /* Rechazado */
```

---

## Criterios de aceptación

- [ ] Vista index actualizada con paleta CJ
- [ ] Vista bulk-create actualizada con paleta CJ
- [ ] Headers de tabla con gradiente morado
- [ ] Botones con colores CJ
- [ ] Badges de estado con colores apropiados
- [ ] Consistencia con vistas pending-seller y pending-admin
- [ ] Sin breaking changes en funcionalidad
