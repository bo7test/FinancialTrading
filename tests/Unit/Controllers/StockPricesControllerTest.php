<?php

namespace Tests\Unit\Controllers;

use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use App\Services\CurrencyIndicators;
use App\Model\HistoricalRates;
use App\Model\TmpTestRates;
use App\Http\Controllers\Equity\StockPricesController;
use Illuminate\Support\Facades\Config;
use App\Model\Servers;
use App\Broker\OandaV20;

class StockPricesControllerTest extends TestCase
{

    public $transactionController;
    public $oanda;

    public function testUpdateMinStockDate() {
        $serversController = new StockPricesController();

        $serversController->updateMostRecentPrices(4542);
    }
}
