<?php

namespace app\Console\Commands;

use App\Models\ConcreteSession;
use App\Models\Consumer;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

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
        $files = Storage::disk('sftp')->files('sage-ekip');

        if(count($files) == 0){
            Log::channel("pdfs_import")->warning('Import pdf files cron did not find any file to import');
            return false;
        }

        foreach($files as $sftpFilePath) {
            $filename = basename($sftpFilePath);

            Log::channel('pdfs_import')->info('Import file starting', [
                "filename" => $filename
            ]);

            Storage::disk('imports')->put(
                $filename,
                Storage::disk('sftp')->get($sftpFilePath)
            );

            $parts = explode('-', pathinfo($filename, PATHINFO_FILENAME));

            $consumer = Consumer::where('rfid_code', $parts[1])->first();
            if(!$consumer) {
                Log::channel('pdfs_import')->warning('Import file failed (consumer not found)', [
                    "filename" => $filename,
                    "rfid_code" => $parts[1],
                ]);
                continue;
            }

            ConcreteSession::create([
                'consumer_id' => $consumer->id,
                'file_name' => $filename,
                'file_path' => Storage::disk('imports')->path($filename),
            ]);

            //we keep file in archive folder instead of deleting it
            Storage::disk('sftp')->move($sftpFilePath, 'sage-ekip/Archives/' . $filename . '.imported.' . Carbon::now()->format('Ymd'));

            Log::channel('pdfs_import')->info('Import file finished with success (distant file has been archived)', [
                "filename" => $filename,
            ]);
        }

        $this->info("Finished.");

        return 0;
    }
}
