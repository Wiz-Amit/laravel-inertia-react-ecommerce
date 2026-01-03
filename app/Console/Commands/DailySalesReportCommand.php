<?php

namespace App\Console\Commands;

use App\Mail\DailySalesReportMail;
use App\Services\OrderService;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class DailySalesReportCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sales:daily-report {--date=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate and send daily sales report';

    /**
     * Execute the console command.
     */
    public function handle(OrderService $orderService): int
    {
        $date = $this->option('date')
            ? Carbon::parse($this->option('date'))
            : Carbon::yesterday();

        $this->info("Generating sales report for {$date->toDateString()}...");

        $salesData = $orderService->aggregateDailySales($date);

        $adminEmail = config('ecommerce.admin_email', 'admin@example.com');

        Mail::to($adminEmail)->send(new DailySalesReportMail($salesData));

        $this->info("Sales report sent to {$adminEmail}");

        return Command::SUCCESS;
    }
}
