# Tarea 3.2: Métodos approve() y reject() en modelo Sale

**Estado:** ✅ COMPLETO  
**Tiempo estimado:** 2h

---

## Objetivo

Implementar métodos `approve()` y `reject()` en el modelo `Sale` para gestionar las transiciones de estado del workflow de aprobación.

---

## Lógica de transiciones

### Método approve()
- **Si estado actual = `pending_seller`:** → cambia a `pending_admin`
- **Si estado actual = `pending_admin`:** → cambia a `approved`
- **Otros estados:** lanza excepción (no se puede aprobar)

### Método reject()
- **Si estado actual = `pending_seller` o `pending_admin`:** → cambia a `rejected`
- **Otros estados:** lanza excepción (no se puede rechazar)

---

## Archivos a modificar

- `app/Models/Sale.php` (agregar métodos)

---

## Criterios de aceptación

- [ ] Método `approve()` implementado
- [ ] Método `reject()` implementado
- [ ] Validación de estados antes de transiciones
- [ ] Excepciones claras para estados inválidos
- [ ] Código documentado
