<?php

namespace App\Http\View\Composers;

use Illuminate\View\View;
use App\Models\ExchangeRate;

class TransactionFormComposer
{
    /**
     * Bind data to the view.
     */
    public function compose(View $view): void
    {
        if (!$view->offsetExists('pairs') || !$view->offsetExists('rates')) {
            $rates = ExchangeRate::where('is_active', true)->first();

            $pairs = ExchangeRate::with(['currencyPair.fromCurrency', 'currencyPair.toCurrency'])
                ->whereNotNull('currency_pair_id')
                ->where('is_active', true)
                ->get()
                ->map(function($rate) {
                    return [
                        'id' => $rate->id,
                        'from_code' => $rate->currencyPair->fromCurrency->code ?? 'N/A',
                        'from_name' => $rate->currencyPair->fromCurrency->name ?? 'N/A',
                        'from_symbol' => $rate->currencyPair->fromCurrency->symbol ?? '$',
                        'ves_rate' => $rate->ves_rate ?? 0,
                        'usd_rate' => $rate->usd_rate ?? 0,
                        'eur_rate' => $rate->eur_rate ?? 0,
                    ];
                });

            $view->with(compact('rates', 'pairs'));
        }
    }
}
