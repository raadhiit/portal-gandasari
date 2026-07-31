<?php

namespace App\Jobs;

use App\Models\Coa;
use App\Models\ItemCategory;
use App\Models\ItemRequestExport;
use App\Notifications\ItemRequestExportReadyNotification;
use DOMDocument;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;
use Modules\ItemRequest\Models\ItemRequest;
use ZipArchive;

class ExportItemRequest implements ShouldQueue
{
    use Queueable;

    public function __construct(public ItemRequestExport $itemRequestExport) {}

    public function handle(): void
    {
        $export = $this->itemRequestExport->fresh(['itemRequest.items', 'requester']);

        if ($export === null) {
            return;
        }

        $export->update([
            'status' => 'processing',
            'started_at' => now(),
            'error_message' => null,
        ]);

        $fileName = 'item-import-'.$export->itemRequest->request_no.'-'.$export->id.'.xlsx';
        $filePath = 'item-request-exports/'.$fileName;
        $disk = 'local';

        try {
            $templatePath = resource_path('templates/item-import-id.xlsx');
            $exportPath = Storage::disk($disk)->path($filePath);

            Storage::disk($disk)->makeDirectory(dirname($filePath));
            copy($templatePath, $exportPath);

            $zip = new ZipArchive;
            $zip->open($exportPath);
            $sheetXml = $zip->getFromName('xl/worksheets/sheet1.xml');
            $zip->deleteName('xl/worksheets/sheet1.xml');
            $zip->addFromString('xl/worksheets/sheet1.xml', $this->buildSheetXml((string) $sheetXml, $export->itemRequest));
            $zip->close();

            $export->update([
                'status' => 'ready',
                'disk' => $disk,
                'file_name' => $fileName,
                'file_path' => $filePath,
                'finished_at' => now(),
            ]);

            Notification::send($export->requester, new ItemRequestExportReadyNotification($export->fresh(['itemRequest'])));
        } catch (\Throwable $throwable) {
            $export->update([
                'status' => 'failed',
                'error_message' => $throwable->getMessage(),
                'finished_at' => now(),
            ]);

            throw $throwable;
        }
    }

    private function buildSheetXml(string $sheetXml, ItemRequest $itemRequest): string
    {
        $document = new DOMDocument('1.0', 'UTF-8');
        $document->preserveWhiteSpace = false;
        $document->loadXML($sheetXml);

        $namespace = 'http://schemas.openxmlformats.org/spreadsheetml/2006/main';
        $sheetData = $document->getElementsByTagNameNS($namespace, 'sheetData')->item(0);

        while ($sheetData !== null && $sheetData->childNodes->length > 1) {
            $sheetData->removeChild($sheetData->lastChild);
        }

        $rowNumber = 2;

        foreach ($itemRequest->items as $item) {
            $row = $document->createElementNS($namespace, 'row');
            $row->setAttribute('r', (string) $rowNumber);
            $row->setAttribute('spans', '1:87');
            $row->setAttribute('ht', '20');
            $row->setAttribute('x14ac:dyDescent', '0.35');

            $values = [
                'A' => $this->lookupCategoryCode($item->category_id),
                'B' => $item->item_code,
                'C' => $item->item_name,
                'D' => $item->item_type,
                'E' => $this->lookupCategoryCode($item->category_id),
                'G' => $this->lookupUnitCode($item->unit_id),
                'BL' => $this->lookupCoaCode($item->inventory_coa_id),
                'BM' => $this->lookupCoaCode($item->sales_coa_id),
                'BT' => $itemRequest->notes,
            ];

            foreach ($this->columnMap() as $column) {
                $cell = $document->createElementNS($namespace, 'c');
                $cell->setAttribute('r', $column.$rowNumber);
                $cell->setAttribute('t', 'inlineStr');
                $cell->setAttribute('s', $column === 'B' ? '13' : '17');

                $inline = $document->createElementNS($namespace, 'is');
                $text = $document->createElementNS($namespace, 't');
                $text->appendChild($document->createTextNode((string) ($values[$column] ?? '')));
                $inline->appendChild($text);
                $cell->appendChild($inline);
                $row->appendChild($cell);
            }

            $sheetData?->appendChild($row);
            $rowNumber++;
        }

        $dimension = $document->getElementsByTagNameNS($namespace, 'dimension')->item(0);
        $dimension?->setAttribute('ref', 'A1:CI'.max($rowNumber - 1, 2));

        return $document->saveXML() ?: $sheetXml;
    }

    /** @return array<int, string> */
    private function columnMap(): array
    {
        return ['A', 'B', 'C', 'D', 'E', 'G', 'BL', 'BM', 'BT'];
    }

    private function lookupCategoryCode(?int $categoryId): string
    {
        if ($categoryId === null) {
            return '';
        }

        return (string) ItemCategory::query()->whereKey($categoryId)->value('code');
    }

    private function lookupUnitCode(?int $unitId): string
    {
        if ($unitId === null) {
            return '';
        }

        return (string) DB::table('units')->where('id', $unitId)->value('code');
    }

    private function lookupCoaCode(?int $coaId): string
    {
        if ($coaId === null) {
            return '';
        }

        return (string) Coa::query()->whereKey($coaId)->value('code');
    }
}
