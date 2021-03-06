<?php namespace App\Services;

use \App\Model\ProcessLog\ProcessLog;
use \App\Model\ProcessLog\ProcessLogMessage;

use Illuminate\Support\Facades\Config;
use \App\Services\Utility;
use \App\Services\AwsService;
use App\Model\ProcessLog\Process;

class ProcessLogger  {

    public $runId;
    public $method;
    public $oanda_account_id;
    public $exchange_id;

    public $logId;
    public $apiId;

    public $loggingOn;

    public function __construct($processCode) {
        $this->utility = new Utility();
        $this->newStrategyLog($processCode);
    }

    public function newStrategyLog($processCode) {
        $awsService = new AwsService();
        $ipAddress = $awsService->getCurrentInstanceIp();
        $serverId = Config::get('server_id');

        $process = Process::where('code', '=', $processCode)->first();

        $newProcessLog = new ProcessLog();

        $newProcessLog->start_date_time = $this->utility->mysqlDateTime();

        $newProcessLog->process_id = $process->id;
        $newProcessLog->server_address = $ipAddress;
        $newProcessLog->server_id = $serverId;
        $newProcessLog->linux_pid = getmypid();

        $processQueueId = Config::get('process_queue_id');

        if (!is_null($processQueueId)) {
            $newProcessLog->process_queue_id = $processQueueId;
        }

        $newProcessLog->save();

        $this->logId = $newProcessLog->id;
        Config::set('process_log_id', $newProcessLog->id);
        Config::set('process_id', $process->id);
    }

    public function logMessage($message, $type = 4) {
            $logMessage = new ProcessLogMessage();
            $logMessage->process_log_id = $this->logId;
            $logMessage->message = $message;
            $logMessage->message_type_id = $type;
            $logMessage->save();
    }

    public function logJsonApiResponse($response, $characters = 250) {
        $this->logMessage('API Response: '.substr(json_encode($response), $characters));
    }

    public function logJson($label, $jsonObject) {
        $this->logMessage($label.': '.json_encode($jsonObject));
    }

    public function processEnd() {
        $logStrategy = ProcessLog::find($this->logId);
        $logStrategy->end_date_time = $this->utility->mysqlDateTime();
        $logStrategy->save();
    }

    public function setRelevantId($id) {
        Config::set('process_log_relevant_id', $id);
    }

    public function forceEndProcess($logId) {
        $logStrategy = ProcessLog::find($logId);
        $logStrategy->end_date_time = $this->utility->mysqlDateTime();
        $logStrategy->forced_end = 1;
        $logStrategy->save();
    }

    public function __destruct()
    {
        $this->processEnd();
    }
}