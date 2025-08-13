<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\UploadedFile;
use Yaza\LaravelGoogleDriveStorage\Gdrive;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class GoogleDrive implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $files;
    public $path;

    /**
     * Create a new job instance.
     */
    public function __construct($files, $path)
    {
        $this->files = $files;
        $this->path = $path;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        foreach ($this->files as $file) {
            $base64_string = preg_replace('#^data:.*;base64,#', '', $file['base64']);
            $decoded = base64_decode($base64_string);

            $tmpFilePath = tempnam(sys_get_temp_dir(), 'upload_');

            file_put_contents($tmpFilePath, $decoded);

            $uploadedFile = new UploadedFile(
                $tmpFilePath,
                $file['name'],
                $file['type'],
                null,
                true
            );
            Gdrive::put(($this->path . $file['name']), $uploadedFile);
        }
    }
}
