<?php namespace App\ForexBackTest\BackTestToBeProcessed\ForexStrategy\NewIndicatorTesting;

/**********************
NewIndicatorTesting Backtest Variable Definitions
Created at: 12/09/18by Brian O'Neill
***********************/

use \DB;
use App\Model\Exchange;
use \App\ForexBackTest\TakeProfitStopLossTest;
use \App\Services\StrategyLogger;

use \App\ForexStrategy\NewIndicatorTesting\HmaTurnAdxBelow;
use \App\ForexStrategy\NewIndicatorTesting\TestHmaSlopeMin;
use \App\ForexStrategy\NewIndicatorTesting\HmaPriceTargetTest;
use \App\ForexStrategy\NewIndicatorTesting\BollingerGetOuterCrossTest;
use \App\ForexStrategy\NewIndicatorTesting\TestCenterCross;
use \App\ForexStrategy\NewIndicatorTesting\TestEmaCrossPoint;
use \App\ForexStrategy\NewIndicatorTesting\TestRsiTarget;
//END STRATEGY DECLARATIONS

class NewIndicatorTestingBackTestToBeProcessed extends \App\ForexBackTest\BackTestToBeProcessed\Base
{

    public function callProcess() {
        /******************************
        * SET BACK TEST
        ******************************/

        //Set
        $backTest = new TakeProfitStopLossTest('Automated Backtest');

        $backTest->strategyRunName = 'Ema Momentum 15 Minutes';
        $backTest->strategyId = 6;

        //All Back Tests
        $backTest->currencyId = $this->backTestToBeProcessed->exchange_id;
        $fullExchange = Exchange::find($this->backTestToBeProcessed->exchange_id);
        $backTest->exchange = $fullExchange;
        $backTest->frequencyId = $this->backTestToBeProcessed->frequency_id;

        //Most Back Tests
        $backTest->stopLoss = $this->backTestToBeProcessed->stop_loss_pips;
        $backTest->takeProfit = $this->backTestToBeProcessed->take_profit_pips;

        $backTest->recordBackTestStart($this->backTestToBeProcessed->id);
        //Starting Unix Time to Run Strategy
        $backTest->rateUnixStart = $this->server->rate_unix_time_start;

        /******************************
        * SET STRATEGY
        ******************************/
        if (1==2) {

        }
        elseif ($this->server->strategy_iteration == 'HMA_TURN_ADX_BELOW') {
            $backTest->rateLevel = 'both';
        
            $strategy = new HmaTurnAdxBelow(1,1,true);
        
            //$strategy->orderType = 'MARKET_IF_TOUCHED';
            $strategy->hmaLength = intval($this->backTestToBeProcessed->variable_1);
            $strategy->adxLength = intval($this->backTestToBeProcessed->variable_2);
            $strategy->adxUndersoldThreshold = intval($this->backTestToBeProcessed->variable_3);
        
            $strategy->takeProfitPipAmount = 0;
            $multiplyValue = max([intval($this->backTestToBeProcessed->variable_1), intval($this->backTestToBeProcessed->variable_2), 
        intval($this->backTestToBeProcessed->variable_3), intval($this->backTestToBeProcessed->variable_4), intval($this->backTestToBeProcessed->variable_5)]);
        
            //Values for Getting Rates
            $backTest->rateCount = intval($multiplyValue)*10;
            $backTest->rateIndicatorMin = intval($multiplyValue)*3;
            $backTest->currentRatesProcessed = $backTest->rateCount;
        }
        elseif ($this->server->strategy_iteration == 'HMA_TURN_ADX_BELOW') {
            $backTest->rateLevel = 'both';
        
            $strategy = new HmaTurnAdxBelow(1,1,true);
        
            //$strategy->orderType = 'MARKET_IF_TOUCHED';
            $strategy->hmaLength = intval($this->backTestToBeProcessed->variable_1);
            $strategy->adxLength = intval($this->backTestToBeProcessed->variable_2);
            $strategy->adxThreshold = intval($this->backTestToBeProcessed->variable_3);
        
            $strategy->takeProfitPipAmount = 0;
            $multiplyValue = max([intval($this->backTestToBeProcessed->variable_1), intval($this->backTestToBeProcessed->variable_2), 
        intval($this->backTestToBeProcessed->variable_3), intval($this->backTestToBeProcessed->variable_4), intval($this->backTestToBeProcessed->variable_5)]);
        
            //Values for Getting Rates
            $backTest->rateCount = intval($multiplyValue)*10;
            $backTest->rateIndicatorMin = intval($multiplyValue)*3;
            $backTest->currentRatesProcessed = $backTest->rateCount;
        }
        elseif ($this->server->strategy_iteration == 'HMA_SLOPE_MIN_TEST') {
            $backTest->rateLevel = 'both';
        
            $strategy = new TestHmaSlopeMin(1,1,true);
        
            //$strategy->orderType = 'MARKET_IF_TOUCHED';
            $strategy->hmaLength = intval($this->backTestToBeProcessed->variable_1);
            $strategy->hmaSlopeCutoff = intval($this->backTestToBeProcessed->variable_2);
            $strategy->adxLength = intval($this->backTestToBeProcessed->variable_3);
            $strategy->adxUndersoldThreshold = intval($this->backTestToBeProcessed->variable_4);
        
            $strategy->takeProfitPipAmount = 0;
            $multiplyValue = max([intval($this->backTestToBeProcessed->variable_1), intval($this->backTestToBeProcessed->variable_2), 
        intval($this->backTestToBeProcessed->variable_3), intval($this->backTestToBeProcessed->variable_4), intval($this->backTestToBeProcessed->variable_5)]);
        
            //Values for Getting Rates
            $backTest->rateCount = intval($multiplyValue)*10;
            $backTest->rateIndicatorMin = intval($multiplyValue)*3;
            $backTest->currentRatesProcessed = $backTest->rateCount;
        }
        elseif ($this->server->strategy_iteration == 'HMA_PRICE_TARGET_TEST') {
            $backTest->rateLevel = 'both';
        
            $strategy = new HmaPriceTargetTest(1,1,true);
        
            //$strategy->orderType = 'MARKET_IF_TOUCHED';
            $strategy->hmaLength = intval($this->backTestToBeProcessed->variable_1);
            $strategy->adxLength = intval($this->backTestToBeProcessed->variable_2);
            $strategy->adxUndersoldThreshold = intval($this->backTestToBeProcessed->variable_3);
        
            $strategy->takeProfitPipAmount = 0;
            $multiplyValue = max([intval($this->backTestToBeProcessed->variable_1), intval($this->backTestToBeProcessed->variable_2), 
        intval($this->backTestToBeProcessed->variable_3), intval($this->backTestToBeProcessed->variable_4), intval($this->backTestToBeProcessed->variable_5)]);
        
            //Values for Getting Rates
            $backTest->rateCount = intval($multiplyValue)*10;
            $backTest->rateIndicatorMin = intval($multiplyValue)*3;
            $backTest->currentRatesProcessed = $backTest->rateCount;
        }
        elseif ($this->server->strategy_iteration == 'BOLLINGER_OUTER_CROSS') {
            $backTest->rateLevel = 'both';
        
            $strategy = new BollingerGetOuterCrossTest(1,1,true);
        
            //$strategy->orderType = 'MARKET_IF_TOUCHED';
            $strategy->bollingerLength = intval($this->backTestToBeProcessed->variable_1);
            $strategy->bollingerSdMultiplier = intval($this->backTestToBeProcessed->variable_2);
            $strategy->adxLength = intval($this->backTestToBeProcessed->variable_3);
            $strategy->adxUndersoldThreshold = intval($this->backTestToBeProcessed->variable_4);
        
            $strategy->takeProfitPipAmount = 0;
            $multiplyValue = max([intval($this->backTestToBeProcessed->variable_1), intval($this->backTestToBeProcessed->variable_2), 
        intval($this->backTestToBeProcessed->variable_3), intval($this->backTestToBeProcessed->variable_4), intval($this->backTestToBeProcessed->variable_5)]);
        
            //Values for Getting Rates
            $backTest->rateCount = intval($multiplyValue)*10;
            $backTest->rateIndicatorMin = intval($multiplyValue)*3;
            $backTest->currentRatesProcessed = $backTest->rateCount;
        }
        elseif ($this->server->strategy_iteration == 'TEST_CENTER_CROSS') {
            $backTest->rateLevel = 'both';
        
            $strategy = new TestCenterCross(1,1,true);
        
            //$strategy->orderType = 'MARKET_IF_TOUCHED';
            $strategy->bollingerLength = intval($this->backTestToBeProcessed->variable_1);
            $strategy->bollingerSdMultiplier = intval($this->backTestToBeProcessed->variable_2);
            $strategy->bollingerLength = intval($this->backTestToBeProcessed->variable_3);
            $strategy->bollingerSdMultiplier = intval($this->backTestToBeProcessed->variable_4);
        
            $strategy->takeProfitPipAmount = 0;
            $multiplyValue = max([intval($this->backTestToBeProcessed->variable_1), intval($this->backTestToBeProcessed->variable_2), 
        intval($this->backTestToBeProcessed->variable_3), intval($this->backTestToBeProcessed->variable_4), intval($this->backTestToBeProcessed->variable_5)]);
        
            //Values for Getting Rates
            $backTest->rateCount = intval($multiplyValue)*10;
            $backTest->rateIndicatorMin = intval($multiplyValue)*3;
            $backTest->currentRatesProcessed = $backTest->rateCount;
        }
        elseif ($this->server->strategy_iteration == 'TEST_EMA_CROSS_POINT') {
            $backTest->rateLevel = 'both';
        
            $strategy = new TestEmaCrossPoint(1,1,true);
        
            //$strategy->orderType = 'MARKET_IF_TOUCHED';
            $strategy->fastEma = intval($this->backTestToBeProcessed->variable_1);
            $strategy->slowEma = intval($this->backTestToBeProcessed->variable_2);
            $strategy->adxLength = intval($this->backTestToBeProcessed->variable_3);
            $strategy->adxUndersoldThreshold = intval($this->backTestToBeProcessed->variable_4);
        
            $strategy->takeProfitPipAmount = 0;
            $multiplyValue = max([intval($this->backTestToBeProcessed->variable_1), intval($this->backTestToBeProcessed->variable_2), 
        intval($this->backTestToBeProcessed->variable_3), intval($this->backTestToBeProcessed->variable_4), intval($this->backTestToBeProcessed->variable_5)]);
        
            //Values for Getting Rates
            $backTest->rateCount = intval($multiplyValue)*10;
            $backTest->rateIndicatorMin = intval($multiplyValue)*3;
            $backTest->currentRatesProcessed = $backTest->rateCount;
        }
        elseif ($this->server->strategy_iteration == 'TEST_RSI_TARGET') {
            $backTest->rateLevel = 'both';
        
            $strategy = new TestRsiTarget(1,1,true);
        
            //$strategy->orderType = 'MARKET_IF_TOUCHED';
            $strategy->rsiLength = intval($this->backTestToBeProcessed->variable_1);
            $strategy->rsiLevel = intval($this->backTestToBeProcessed->variable_2);
            $strategy->adxLength = intval($this->backTestToBeProcessed->variable_3);
            $strategy->adxUndersoldThreshold = intval($this->backTestToBeProcessed->variable_4);
        
            $strategy->takeProfitPipAmount = 0;
            $multiplyValue = max([intval($this->backTestToBeProcessed->variable_1), intval($this->backTestToBeProcessed->variable_2), 
        intval($this->backTestToBeProcessed->variable_3), intval($this->backTestToBeProcessed->variable_4), intval($this->backTestToBeProcessed->variable_5)]);
        
            //Values for Getting Rates
            $backTest->rateCount = intval($multiplyValue)*10;
            $backTest->rateIndicatorMin = intval($multiplyValue)*3;
            $backTest->currentRatesProcessed = $backTest->rateCount;
        }
        //END OF SYSTEM STRATEGY IFS

        $strategy->strategyLogger = new StrategyLogger();

        $strategy->strategyId = 6;
        $strategy->strategyDesc = 'Automated Test';
        $strategy->positionMultiplier = 5;

        //Most Back Tests
        $strategy->stopLossPipAmount = $this->backTestToBeProcessed->stop_loss_pips;

        //ALL
        $strategy->exchange = $fullExchange;
        $strategy->backTestHelpers->exchange = $fullExchange;
        $strategy->maxPositions = 5;
        $backTest->strategy = $strategy;
        $backTest->run();
    }
}