<?php

namespace app\Console\Commands;

use App\Models\ConcreteSession;
use App\Models\Consumer;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Spatie\PdfToText\Pdf;

class ImportConcreteSessionsFromPdfFiles extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'chronobeton:import_concrete_sessions';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $allFiles = Storage::disk('sftp')->files('PDF Production data/Production data RFID tag');

//        if(count($files) == 0){
//            Log::channel("pdfs_import")->warning('Import pdf files cron did not find any file to import');
//            return false;
//        }

        $files = array_slice($allFiles, 0, 2);

        foreach($files as $sftpFilePath) {
            $filename = basename($sftpFilePath);
            if (ConcreteSession::where('file_name', $filename)->exists()) {
                Log::channel('pdfs_import')->info('Import file skipped (already imported)', [
                    "filename" => $filename
                ]);
                continue;
            }

            $deliveredAt = Carbon::createFromTimestamp(
                Storage::disk('sftp')->lastModified($sftpFilePath)
            );

            Log::channel('pdfs_import')->info('Import file starting', [
                "filename" => $filename
            ]);

            $parts = explode('-', pathinfo($filename, PATHINFO_FILENAME));

            $consumer = Consumer::where('rfid_code', $parts[3])->first();
            if(!$consumer) {
                Log::channel('pdfs_import')->warning('Import file failed (consumer not found)', [
                    "filename" => $filename,
                    "rfid_code" => $parts[3],
                ]);
                continue;
            }

            Storage::disk('imports')->put(
                sprintf('/%s/%s', $consumer->id, $filename),
                Storage::disk('sftp')->get($sftpFilePath)
            );

            $pdfText = Pdf::getText(
                Storage::disk('imports')->path(
                    sprintf('/%s/%s', $consumer->id, $filename)
                )
            );

            $pdfStrings = collect(explode("\n", $pdfText));

            ConcreteSession::create([
                'consumer_id' => $consumer->id,
                'file_name' => $filename,
                'file_path' => sprintf('/%s/%s', $consumer->id, $filename),
                'delivered_at' => $deliveredAt,
                'imported_at' => Carbon::now(),
                'quantity' => $this->extractQuantity($pdfStrings),
                'concrete_type' => $this->extractConcreteType($pdfStrings),
            ]);

            //we keep file in archive folder instead of deleting it
//            Storage::disk('sftp')->move($sftpFilePath, 'sage-ekip/Archives/' . $filename . '.imported.' . Carbon::now()->format('Ymd'));

            Log::channel('pdfs_import')->info('Import file finished with success (distant file has been archived)', [
                "filename" => $filename,
            ]);
        }

        $this->info("Finished.");

        return 0;
    }

    private function extractQuantity(Collection $pdfStrings): ?string
    {
        $quantityString = $pdfStrings->filter(fn($string) => Str::contains($string, 'Quantité totale: '))->first();
        if (!$quantityString) {
            return null;
        }

        return (int) Str::remove(' m³', Str::after($quantityString, 'Quantité totale: '));
    }

    private function extractConcreteType(Collection $pdfStrings): ?string
    {
        $concreteTypeString = $pdfStrings->filter(fn($string) => Str::contains($string, 'Variété: '))->first();
        if (!$concreteTypeString) {
            return null;
        }

        return Str::after($concreteTypeString, 'Variété: ');
    }
}
