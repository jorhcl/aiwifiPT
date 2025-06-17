<?php


/**
 *
 * @author. Jorge Cortes Lopez
 *
 * Class   ProcessContactCsvFile
 *   Job to process Csv files
 *
 *
 */

namespace App\Jobs;

use App\Services\ContactService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Http\UploadedFile;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ProcessContactCsvFile implements ShouldQueue
{
    use Queueable, Dispatchable, InteractsWithQueue, SerializesModels;


    protected $path;
    protected $clientId;

    /**
     * Create a new job instance.
     */
    public function __construct(string $path, int $clientId)
    {
        $this->path = $path;
        $this->clientId = $clientId;
    }

    /**
     * Execute the job.
     */
    public function handle(ContactService $service): void
    {
        //
        $file = new UploadedFile(storage_path('app/' . $this->path), basename($this->path));
        $service->importContactsFromCsv($file, $this->clientId);
    }
}
