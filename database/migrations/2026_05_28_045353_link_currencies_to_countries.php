<?php

use Illuminate\Database\Migrations\Migration;
use App\Models\Currency;
use App\Models\Country;

return new class extends Migration
{
    public function up(): void
    {
        // Link each currency to its country by matching the currency code
        // in the Country.currency_name field (format: "PEN — Sol peruano")
        Currency::get()->each(function (Currency $currency) {
            $country = Country::where('currency_name', 'like', $currency->code . ' %')
                ->orWhere('currency_name', 'like', $currency->code . ' —%')
                ->first();

            if ($country) {
                $currency->update(['country_id' => $country->id]);
            }
        });
    }

    public function down(): void
    {
        Currency::query()->update(['country_id' => null]);
    }
};
