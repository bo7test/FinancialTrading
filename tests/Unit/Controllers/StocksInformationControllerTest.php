<?php

namespace Tests\Unit\Controllers;

use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use App\Services\CurrencyIndicators;
use App\Model\HistoricalRates;
use App\Model\TmpTestRates;
use App\Http\Controllers\Equity\StocksInformationController;
use App\Http\Controllers\ServersController;
use App\Model\Servers;
use Illuminate\Support\Facades\Config;
use App\Broker\OandaV20;

class StocksInformationControllerTest extends TestCase
{
    public $transactionController;
    public $oanda;

//    public function testGetSymbolData() {
//        $stocksHistoricalDataTest = new StocksHistoricalDataController();
//        $stocksHistoricalDataTest->getStockData();
//    }

    public function testKeepRunning() {
        $serverController = new StocksInformationController();

        $serverController->runNextTask();
    }
}