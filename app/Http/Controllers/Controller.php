<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\User;
use App\Notifications\NewTransactionForOwner;

abstract class Controller
{
    protected function notifyOwners(Transaction $transaction, string $event, ?string $detail = null): void
    {
        User::role(['admin', 'super-admin'])->get()->each(
            fn (User $owner) => $owner->notify(new NewTransactionForOwner($transaction, $event, $detail))
        );
    }
}
