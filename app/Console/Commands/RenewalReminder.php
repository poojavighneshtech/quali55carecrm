<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Http\Controllers\RenewalPickup\RenewalPickupController;
use App\Http\Controllers\TestController\TestController;

class RenewalReminder extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'renewal:reminder';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Renewal due date reminder through message and get response pickup or continue of product';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $RenewalPickup = new RenewalPickupController();
        $RenewalPickup->RenewalAutoReminder();
        $RenewalPickup->renewalPickupTestWhatsApp();
        // $RenewalPickup->RenewalAutoReminderOverdue();
        $RenewalPickup->RenewalReminderOverdue();
        $testController = new TestController();
        $testController->overdueIndividual();
        $testController->overdueCorpoprate();

    }
}
