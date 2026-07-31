<?php

namespace App\Exports;

use App\Models\Coa;
use App\Models\ItemCategory;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Modules\ItemRequest\Models\ItemRequest;

class ItemRequestApprovedExport implements FromArray, ShouldAutoSize, WithHeadings
{
    public string $fileName;

    public function __construct(private ItemRequest $itemRequest)
    {
        $this->fileName = 'item-import-'.$itemRequest->request_no.'.xlsx';
    }

    /** @return array<int, array<int, string>> */
    public function array(): array
    {
        $categories = ItemCategory::query()->pluck('code', 'id');
        $coas = Coa::query()->pluck('code', 'id');
        $units = DB::table('units')->pluck('code', 'id');

        return $this->itemRequest->items->map(function ($item) use ($categories, $coas, $units): array {
            $row = array_fill(0, count($this->headings()), '');

            $row[0] = (string) ($categories[$item->category_id] ?? '');
            $row[1] = (string) ($item->item_code ?? '');
            $row[2] = (string) $item->item_name;
            $row[3] = (string) $item->item_type;
            $row[4] = (string) ($categories[$item->category_id] ?? '');
            $row[6] = (string) ($units[$item->unit_id] ?? '');
            $row[64] = (string) ($coas[$item->inventory_coa_id] ?? '');
            $row[65] = (string) ($coas[$item->sales_coa_id] ?? '');
            $row[66] = (string) ($coas[$item->sales_coa_id] ?? '');
            $row[71] = (string) ($this->itemRequest->notes ?? '');

            return $row;
        })->all();
    }

    /** @return array<int, string> */
    public function headings(): array
    {
        return [
            'Kategori Barang',
            'Kode Barang',
            'Nama Barang',
            'Jenis Barang',
            'Tipe Persediaan',
            'UPC/Barcode',
            'Satuan',
            'Satuan #2',
            'Rasio Satuan #2',
            'Satuan #3',
            'Rasio Satuan #3',
            'Satuan #4',
            'Rasio Satuan #4',
            'Satuan #5',
            'Rasio Satuan #5',
            'Def. Hrg. Jual Satuan #1',
            'Def. Hrg. Jual Satuan #2',
            'Def. Hrg. Jual Satuan #3',
            'Def. Hrg. Jual Satuan #4',
            'Def. Hrg. Jual Satuan #5',
            'Satuan Beli',
            'Merupakan Satuan Kontrol',
            'Harga Beli',
            'Minimum Beli',
            'Batas Minimum Stok',
            'Default Diskon (%)',
            'Merek Barang',
            'Pemasok Utama',
            'Kode Barang Pajak',
            'PPN',
            'PPNBM',
            'PPh',
            'Cabang Saldo',
            'Gudang Saldo Awal',
            'Kuantitas Saldo Awal',
            'Satuan Saldo Awal',
            'Nilai Satuan',
            'Per Tanggal',
            'Karakter 1',
            'Karakter 2',
            'Karakter 3',
            'Karakter 4',
            'Karakter 5',
            'Karakter 6',
            'Karakter 7',
            'Karakter 8',
            'Karakter 9',
            'Karakter 10',
            'Angka 1',
            'Angka 2',
            'Angka 3',
            'Angka 4',
            'Angka 5',
            'Angka 6',
            'Angka 7',
            'Angka 8',
            'Angka 9',
            'Angka 10',
            'Tanggal 1',
            'Tanggal 2',
            'Persediaan',
            'Penjualan',
            'Retur Pembelian',
            'Beban Pokok Penjualan',
            'Retur Penjualan',
            'Diskon Penjualan',
            'Barang Terkirim',
            'Pembelian Belum Tertagih',
            'Dipakai di Cabang',
            'Catatan',
            'Menggunakan No. Seri',
            'Tipe Nomor Seri',
            'Pakai tanggal kadaluarsa',
            'Substitusi dengan',
            'Non Aktif',
            'Harga Grosir',
            'Panjang (cm)',
            'Lebar (cm)',
            'Tinggi (cm)',
            'Berat (gr)',
            'Label Varian 1',
            'Label Varian 2',
            'Data Varian 1',
            'Data Varian 2',
            'Tampilkan rincian saat dicetak',
            'Dapat mengubah Kuantitas Rincian Grup',
            'Minimum Jual',
        ];
    }
}
