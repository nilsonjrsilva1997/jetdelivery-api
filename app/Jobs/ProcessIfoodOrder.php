<?php

namespace App\Jobs;

use App\Services\IfoodEventService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ProcessIfoodOrder implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $ifoodIntegration;

    /**
     * Create a new job instance.
     */
    public function __construct($ifoodIntegration)
    {
        $this->ifoodIntegration = $ifoodIntegration;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $ifoodEventService = new IfoodEventService();

        $ifoodEventService->fetchIfoodEvents($this->ifoodIntegration);
    }
}
