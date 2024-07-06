<?php

namespace App\Jobs;

use App\Models\IfoodIntegration;
use App\Services\IfoodEventService;
use App\Services\IfoodService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ProcessIfoodEventsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $ifoodConfigs = IfoodIntegration::where('active', true)->get();

        // Lógica para chamar o serviço que faz a requisição ao endpoint do iFood
        foreach($ifoodConfigs as $ifoodIntegration) {
            ProcessIfoodOrder::dispatch($ifoodIntegration);
        }
    }
}
