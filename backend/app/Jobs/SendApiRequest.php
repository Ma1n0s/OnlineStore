<?php

namespace App\Jobs;

use App\Events\OrderApiProcessed;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SendApiRequest implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */

     public $timeout = 30; // Таймаут в секундах

    public function __construct(
        protected string $url,
        protected array $data,
        protected array $headers = [],
        protected int $orderId,
    )
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle()
    {
        $response = Http::withHeaders($this->headers)
            ->post($this->url, $this->data);
            
        if ($response->failed()) {
            Log::error('API request failed', [
                'url' => $this->url,
                'status' => $response->status(),
                'response' => $response->body()
            ]);
            throw new \Exception("API request failed with status: " . $response->status());
        }

        $responseData = $response->json();

        event(new OrderApiProcessed($this->orderId, $responseData));
        
        return $responseData;
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
