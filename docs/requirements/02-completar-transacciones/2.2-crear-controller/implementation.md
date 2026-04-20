# Implementación: TransactionController

## Fecha
2026-04-20

## Archivo creado
`app/Http/Controllers/TransactionController.php`

## Método implementado: index()

### Funcionalidad
- Lista todas las transacciones del usuario autenticado
- Carga eager loading de relaciones (seller, exchangeRate)
- Ordena por fecha descendente (más recientes primero)
- Calcula el total gastado sumando `amount_pen`

### Código
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

### Variables pasadas a la vista
- `$transactions` - Colección de transacciones del usuario
- `$totalSpent` - Suma total de soles enviados

## Próximo paso
Tarea 2.3: Crear vista transactions/index.blade.php
