<?php

namespace App\Http\Controllers;

use App\Models\ItemRequestExport;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ItemRequestExportDownloadController extends Controller
{
    use AuthorizesRequests;

    public function __invoke(ItemRequestExport $itemRequestExport): BinaryFileResponse
    {
        $this->authorize('export', $itemRequestExport->itemRequest);

        abort_unless($itemRequestExport->status === 'ready', 404);
        abort_unless($itemRequestExport->disk !== null, 404);

        return response()->download(
            Storage::disk($itemRequestExport->disk)->path($itemRequestExport->file_path),
            $itemRequestExport->file_name,
        );
    }
}
