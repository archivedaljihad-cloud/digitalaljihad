<?php
namespace App\Exports;
use App\Models\JadwalSholat;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
class JadwalSholatExport implements
    FromCollection,
    WithHeadings,
    WithMapping,
    ShouldAutoSize,
    WithStyles
{
    /**
     * Mengambil data jadwal sholat.
     */
    public function collection()
    {
        return JadwalSholat::urutkan()->get();
    }
    /**
     * Heading kolom Excel.
     */
    public function headings(): array
    {
        return [
            'No',
            'Nama Sholat',
            'Waktu',
            'Dibuat',
            'Diupdate',
        ];
    }
    /**
     * Mapping data setiap baris.
     */
    public function map($jadwal): array
    {  
        static $no = 1;
        return [
            $no++,
            $jadwal->nama_sholat,
            $jadwal->formattedWaktu,
            optional($jadwal->created_at)->format('d-m-Y H:i'),
            optional($jadwal->updated_at)->format('d-m-Y H:i'),
        ];
    }
    /**
     * Styling worksheet Excel.
     */
    public function styles(Worksheet $sheet)
    {
        return [
            // Header
            1 => [
                'font' => [
                    'bold' => true,
                ],
            ],
        ];
    }   
}
