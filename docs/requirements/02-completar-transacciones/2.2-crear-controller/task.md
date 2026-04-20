# Tarea 2.2: Crear TransactionController

## Objetivo
Crear el controlador para gestionar el listado de transacciones del usuario autenticado.

## Método index()

```php
public function index()
{
    $transactions = Transaction::with(['seller', 'exchangeRate'])
        ->where('user_id', auth()->id())
        ->orderBy('created_at', 'desc')
        ->get();

    $totalSpent = $transactions->sum('amount_pen');

    return view('transactions.index', compact('transactions', 'totalSpent'));
}
```

## Características
- Eager loading de relaciones (seller, exchangeRate)
- Filtrar solo transacciones del usuario autenticado
- Ordenar por fecha descendente
- Calcular total gastado

## Duración
3 minutos

## Prioridad
High
