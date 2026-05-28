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

class ConciliationExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithColumnWidths
{
    public function __construct(
        private string $start,
        private string $end
    ) {}

    public function collection()
    {
        return Transaction::with([
                'seller',
                'user',
                'exchangeRate.currencyPair.fromCurrency',
            ])
            ->whereIn('status', ['processing', 'completed'])
            ->where('operation_type', 'transferencia')
            ->whereBetween('created_at', [$this->start . ' 00:00:00', $this->end . ' 23:59:59'])
            ->orderByDesc('created_at')
            ->get();
    }

    public function headings(): array
    {
        return [
            'ID',
            'Fecha y Hora',
            'Titular (Cliente)',
            'DNI Titular',
            'Banco Origen (Perú)',
            'Nº Cuenta Origen',
            'Nº Operación',
            'Moneda',
            'Monto Enviado',
            'Banco Receptor (VE)',
            'Estado',
        ];
    }

    public function map($tx): array
    {
        $fromCode = $tx->exchangeRate?->currencyPair?->fromCurrency?->code ?? 'PEN';

        $statusLabels = [
            'processing' => 'En proceso',
            'completed'  => 'Completada',
        ];

        return [
            '#' . str_pad((string) $tx->id, 5, '0', STR_PAD_LEFT),
            $tx->created_at->format('d/m/Y H:i'),
            $tx->user?->name ?? '—',
            $tx->sender_dni ?? '—',
            $tx->sender_bank ?? '—',
            $tx->sender_account_number ?? '—',
            $tx->operation_number ?? '',
            $fromCode,
            number_format($tx->amount_pen, 2),
            $tx->recipient_bank ?? '—',
            $statusLabels[$tx->status] ?? $tx->status,
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => [
                'font'      => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill'      => ['fillType' => 'solid', 'startColor' => ['rgb' => '047857']],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            ],
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 10,
            'B' => 18,
            'C' => 25,
            'D' => 14,
            'E' => 28,
            'F' => 22,
            'G' => 18,
            'H' => 8,
            'I' => 14,
            'J' => 28,
            'K' => 14,
        ];
    }
}
