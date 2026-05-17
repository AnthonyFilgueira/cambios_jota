<?php

namespace App\Observers;

use App\Models\AuditLog;
use Illuminate\Database\Eloquent\Model;

class AuditObserver
{
    // Campos que nunca se registran en el log
    private const HIDDEN = ['password', 'remember_token', 'two_factor_secret', 'two_factor_recovery_codes'];

    public function created(Model $model): void
    {
        $this->log('created', $model, null, $this->clean($model->getAttributes()));
    }

    public function updated(Model $model): void
    {
        $dirty = $model->getDirty();
        if (empty($dirty)) return;

        $old = array_intersect_key($this->clean($model->getOriginal()), $dirty);
        $new = $this->clean($dirty);

        $this->log('updated', $model, $old, $new);
    }

    public function deleted(Model $model): void
    {
        // Para soft deletes solo registramos si es delete físico
        if (method_exists($model, 'isForceDeleting') && !$model->isForceDeleting()) {
            $this->log('deleted', $model, ['deleted_at' => null], ['deleted_at' => now()->toDateTimeString()]);
            return;
        }
        $this->log('deleted', $model, $this->clean($model->getAttributes()), null);
    }

    public function restored(Model $model): void
    {
        $this->log('restored', $model, ['deleted_at' => $model->deleted_at], ['deleted_at' => null]);
    }

    private function log(string $action, Model $model, ?array $old, ?array $new): void
    {
        $user = auth()->user();

        AuditLog::create([
            'user_id'    => $user?->id,
            'user_role'  => $user?->getRoleNames()->first(),
            'action'     => $action,
            'model_type' => get_class($model),
            'model_id'   => $model->getKey(),
            'old_values' => $old,
            'new_values' => $new,
            'ip_address' => request()->ip(),
            'created_at' => now(),
        ]);
    }

    private function clean(array $attrs): array
    {
        return array_diff_key($attrs, array_flip(self::HIDDEN));
    }
}
