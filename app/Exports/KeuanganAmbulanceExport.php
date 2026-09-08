<?php

namespace App\Exports;

use App\Models\KeuanganAmbulance;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;

class KeuanganAmbulanceExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithColumnWidths, WithTitle
{
	protected $dari;
	protected $sampai;

	public function __construct($dari, $sampai)
	{
		$this->dari = $dari;
		$this->sampai = $sampai;
	}

	public function collection()
	{
		return KeuanganAmbulance::whereBetween('tanggal', [$this->dari, $this->sampai])
			->orderBy('tanggal', 'desc')
			->orderBy('id', 'desc')
			->get();
	}

	public function headings(): array
	{
		return [
			'No',
			'Tanggal',
			'Deskripsi',
			'Kategori',
			'Pemasukan (Rp)',
			'Pengeluaran (Rp)',
			'Saldo (Rp)'
		];
	}

	public function map($keuangan): array
	{
		static $rowNumber = 0;
		$rowNumber++;
		
		return [
			$rowNumber,
			$keuangan->tanggal ? \Carbon\Carbon::parse($keuangan->tanggal)->format('Y-m-d') : '-',
			$keuangan->deskripsi,
			$keuangan->kategori ?? '-',
			$keuangan->pemasukan > 0 ? $keuangan->pemasukan : 0,
			$keuangan->pengeluaran > 0 ? $keuangan->pengeluaran : 0,
			$keuangan->saldo
		];
	}

	public function styles(Worksheet $sheet)
	{
		$sheet->getStyle('A1:G1')->applyFromArray([
			'font' => [
				'bold' => true,
				'color' => ['rgb' => 'FFFFFF'],
				'size' => 11
			],
			'fill' => [
				'fillType' => Fill::FILL_SOLID,
				'startColor' => ['rgb' => '0A4D68']
			],
			'alignment' => [
				'horizontal' => Alignment::HORIZONTAL_CENTER,
				'vertical' => Alignment::VERTICAL_CENTER
			]
		]);
		
		$sheet->getRowDimension(1)->setRowHeight(25);
		
		$highestRow = $sheet->getHighestRow();
		$sheet->getStyle('A1:G' . $highestRow)->applyFromArray([
			'borders' => [
				'allBorders' => [
					'borderStyle' => Border::BORDER_THIN,
					'color' => ['rgb' => 'CCCCCC']
				]
			],
			'alignment' => [
				'vertical' => Alignment::VERTICAL_CENTER
			]
		]);

		$sheet->getStyle('A2:A' . $highestRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
		$sheet->getStyle('B2:B' . $highestRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
		$sheet->getStyle('D2:D' . $highestRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
		$sheet->getStyle('E2:G' . $highestRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
		$sheet->getStyle('E2:G' . $highestRow)->getNumberFormat()->setFormatCode('#,##0');

		return [];
	}

	public function columnWidths(): array
	{
		return [
			'A' => 8,
			'B' => 15,
			'C' => 40,
			'D' => 20,
			'E' => 20,
			'F' => 20,
			'G' => 22,
		];
	}

	public function title(): string
	{
		return 'Kas Ambulance';
	}
}
