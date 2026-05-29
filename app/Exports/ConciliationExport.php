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
                'exchangeRate.currencyPair.toCurrency',
            ])
            ->where('status', 'completed')
            ->whereBetween('created_at', [$this->start . ' 00:00:00', $this->end . ' 23:59:59'])
            ->orderByDesc('created_at')
            ->get();
    }

    public function headings(): array
    {
        return [
            'ID',
            'Fecha y Hora',
            'Corredor',
            'Tipo Operación',
            'Remitente',
            'Doc. Remitente',
            'Banco/Método Remitente',
            'Nº Cuenta/Teléfono Remitente',
            'Nº Operación',
            'Monto Enviado',
            'Moneda Origen',
            'Beneficiario',
            'Doc. Beneficiario',
            'Banco/Método Beneficiario',
            'Nº Cuenta/Teléfono Beneficiario',
            'Monto Recibido',
            'Moneda Destino',
        ];
    }

    public function map($tx): array
    {
        $fromCode = $tx->exchangeRate?->currencyPair?->fromCurrency?->code ?? '—';
        $toCode   = $tx->exchangeRate?->currencyPair?->toCurrency?->code ?? '—';

        $docRemitente = $tx->sender_document_number
            ? (($tx->sender_document_type ? $tx->sender_document_type . ': ' : '') . $tx->sender_document_number)
            : ($tx->sender_dni ?? '—');

        $docBeneficiario = $tx->recipient_document_number
            ? (($tx->recipient_document_type ? $tx->recipient_document_type . ': ' : '') . $tx->recipient_document_number)
            : ($tx->recipient_dni ?? '—');

        $opType = $tx->sender_operation_type ?? $tx->operation_type ?? '—';

        return [
            '#' . str_pad((string) $tx->id, 5, '0', STR_PAD_LEFT),
            $tx->created_at->format('d/m/Y H:i'),
            $fromCode . ' → ' . $toCode,
            $opType !== '—' ? str_replace('_', ' ', $opType) : '—',
            $tx->user?->name ?? '—',
            $docRemitente,
            $tx->sender_bank ?? '—',
            $tx->sender_account_number ?? $tx->sender_phone ?? '—',
            $tx->operation_number ?? '—',
            number_format((float) $tx->amount_pen, 2),
            $fromCode,
            $tx->recipient_name ?? '—',
            $docBeneficiario,
            $tx->recipient_bank ?? '—',
            $tx->recipient_account_number ?? $tx->recipient_phone ?? '—',
            number_format((float) $tx->amount_ves, 2),
            $toCode,
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
            'C' => 14,
            'D' => 22,
            'E' => 25,
            'F' => 20,
            'G' => 28,
            'H' => 25,
            'I' => 18,
            'J' => 14,
            'K' => 10,
            'L' => 25,
            'M' => 20,
            'N' => 28,
            'O' => 25,
            'P' => 14,
            'Q' => 10,
        ];
    }
}
