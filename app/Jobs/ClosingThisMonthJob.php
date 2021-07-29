<?php

namespace App\Jobs;

use DateTime;
use App\Models\Branch;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\Log;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Contracts\Queue\ShouldBeUnique;


class ClosingThisMonthJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    private $closingService;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->closingService = app()->make('ClosingService');
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $nowDateTime = new DateTime("first day of last month");
        $yearMonth = $nowDateTime -> format('Y-m');
        $branches = Branch::all()->pluck('id')->toArray();
        $this->closingService->closeMonth($yearMonth, $branches);
    }
}
