<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
class SendApiRequest implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */

     public $tries = 7; // Максимальное количество попыток
    
     public $backoff = [300, 3600, 43200, 86400, 86400*3, 86400*7, 86400*30]; // Задержки между попытками (в секундах)

    public function __construct(
        protected string $url,
        protected array $data,
        protected array $headers = []
    )
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $response = Http::withHeaders($this->headers)
            ->timeout(30)
            ->post($this->url, $this->data);
            
        if ($response->failed()) {
            throw new \Exception("API request failed with status: " . $response->status());
        }
        
        return $response->json();
    }

    public function failed(\Throwable $exception)
    {
        // Логирование окончательно неудачных запросов
        \Log::error("API request failed after all retries", [
            'url' => $this->url,
            'error' => $exception->getMessage()
        ]);
    }
}
