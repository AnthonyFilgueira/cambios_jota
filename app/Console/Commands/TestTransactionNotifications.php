<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class TestTransactionNotifications extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:transaction-notifications {email}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Prueba las notificaciones de transacciones enviando emails de demostración';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $email = $this->argument('email');

        $this->info("Iniciando pruebas de notificaciones de transacciones...");
        $this->info("Se enviarán notificaciones a: {$email}");

        // Buscar o crear un usuario de prueba
        $user = \App\Models\User::where('email', $email)->first();

        if (!$user) {
            $this->error("Usuario con email {$email} no encontrado.");
            $this->info("Creando usuario de prueba...");

            $user = \App\Models\User::create([
                'name' => 'Usuario de Prueba',
                'email' => $email,
                'password' => bcrypt('password'),
            ]);

            $this->info("Usuario creado exitosamente.");
        }

        // Crear una transacción de prueba
        $transaction = \App\Models\Transaction::create([
            'user_id' => $user->id,
            'amount_pen' => 1000.00,
            'amount_ves' => 3650.00,
            'exchange_rate_id' => \App\Models\ExchangeRate::first()->id,
            'status' => 'pending',
            'notes' => 'Transacción de prueba para testing de notificaciones',
            'recipient_bank' => 'Banco de Venezuela',
            'recipient_account_number' => '0102-0000-0000000000',
            'recipient_dni' => 'V-12345678',
            'recipient_account_type' => 'ahorro',
            'sender_bank' => 'BCP',
            'sender_account_number' => '123456789',
            'usd_bcv_rate' => 36.50,
            'eur_bcv_rate' => 40.00,
        ]);

        $this->info("Transacción de prueba creada: #{$transaction->id}");
        $this->newLine();

        // Test 1: Notificación de observación
        $this->info("1. Probando notificación de OBSERVACIÓN...");
        $transaction->observation = "El comprobante de pago no es legible. Por favor, sube una imagen más clara.";
        $transaction->status = 'observed';
        $transaction->save();

        $user->notify(new \App\Notifications\TransactionObserved($transaction));
        $this->info("✓ Notificación de observación enviada");
        $this->newLine();

        // Test 2: Notificación de procesamiento
        sleep(2);
        $this->info("2. Probando notificación de PROCESAMIENTO...");
        $transaction->status = 'processing';
        $transaction->save();

        $user->notify(new \App\Notifications\TransactionProcessed($transaction));
        $this->info("✓ Notificación de procesamiento enviada");
        $this->newLine();

        // Test 3: Notificación de completado
        sleep(2);
        $this->info("3. Probando notificación de COMPLETADO...");
        $transaction->status = 'completed';
        $transaction->save();

        $user->notify(new \App\Notifications\TransactionCompleted($transaction));
        $this->info("✓ Notificación de completado enviada");
        $this->newLine();

        $this->info("==================================================");
        $this->info("Todas las notificaciones han sido enviadas.");
        $this->info("Por favor, revisa la bandeja de entrada de: {$email}");
        $this->info("Si estás usando MailHog, accede a: http://localhost:8025");
        $this->info("==================================================");

        return 0;
    }
}
