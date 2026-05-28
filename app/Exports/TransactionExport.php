<?php

declare(strict_types=1);

namespace App\Exports;

use App\Models\Transaction;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class TransactionExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithColumnWidths
{
    public function __construct(
        private string $start,
        private string $end,
        private string $status = 'all',
        private ?int   $sellerId = null
    ) {}

    public function collection()
    {
        $query = Transaction::with([
                'seller',
                'user',
                'exchangeRate.currencyPair.fromCurrency',
                'exchangeRate.currencyPair.toCurrency',
            ])
            ->whereBetween('created_at', [$this->start . ' 00:00:00', $this->end . ' 23:59:59']);

        if ($this->status !== 'all') {
            $query->where('status', $this->status);
        }

        if ($this->sellerId) {
            $query->where('seller_id', $this->sellerId);
        }

        return $query->orderByDesc('created_at')->get();
    }

    public function headings(): array
    {
        return [
            'ID',
            'Fecha',
            'Cliente',
            'Vendedor',
            'Moneda Origen',
            'Monto Enviado',
            'Moneda Destino',
            'Monto Recibido',
            'Tipo Operación',
            'Nº Operación',
            'Banco Origen',
            'Banco Receptor',
            'Estado',
        ];
    }

    public function map($tx): array
    {
        $fromCode = $tx->exchangeRate?->currencyPair?->fromCurrency?->code ?? 'PEN';
        $toCode   = $tx->exchangeRate?->currencyPair?->toCurrency?->code  ?? 'VES';

        $statusLabels = [
            'pending'    => 'Pendiente',
            'processing' => 'En proceso',
            'completed'  => 'Completada',
            'observed'   => 'Observada',
            'cancelled'  => 'Cancelada',
        ];

        return [
            '#' . str_pad((string) $tx->id, 5, '0', STR_PAD_LEFT),
            $tx->created_at->format('d/m/Y H:i'),
            $tx->user?->name ?? '—',
            $tx->seller?->name ?? '—',
            $fromCode,
            number_format($tx->amount_pen, 2),
            $toCode,
            number_format($tx->amount_ves, 2),
            $tx->operation_type === 'pago_movil' ? 'Pago Móvil' : 'Transferencia Bancaria',
            $tx->operation_number ?? '—',
            $tx->sender_bank ?? '—',
            $tx->recipient_bank ?? '—',
            $statusLabels[$tx->status] ?? $tx->status,
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => [
                'font'      => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill'      => ['fillType' => 'solid', 'startColor' => ['rgb' => '4C1D95']],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            ],
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 10,
            'B' => 18,
            'C' => 22,
            'D' => 22,
            'E' => 10,
            'F' => 14,
            'G' => 10,
            'H' => 14,
            'I' => 22,
            'J' => 18,
            'K' => 25,
            'L' => 25,
            'M' => 14,
        ];
    }
}
