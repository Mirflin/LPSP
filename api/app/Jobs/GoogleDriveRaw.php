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
use Illuminate\Support\Facades\Log;
use Illuminate\Queue\SerializesModels;

class GoogleDriveRaw implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $file;
    public $path;

    /**
     * Create a new job instance.
     */
    public function __construct($file, $path)
    {
        $this->file = $file;
        $this->path = $path;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $full_path = $this->path;
        // $file = storage_path('app/public/'.$this->file);

        //$fileContent = Storage::disk('public')->get($this->file);

        $fileContents = Storage::disk('public')->get($this->file);

        $tempPath = sys_get_temp_dir() . '/' . basename($this->file);
        file_put_contents($tempPath, $fileContents);

        

        Gdrive::put($full_path, $tempPath);
    }
}
