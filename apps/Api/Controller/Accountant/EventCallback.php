<?php
namespace Api\Controller\Accountant;


use SeuDo\Accountant\EventHandler;
use SeuDo\Logger;

class EventCallback extends Base {
    public function postHandling() {
        $transaction = $this->post('transaction');
        $history = $this->post('transaction_history');
        $event_name = $this->post('event_name');

        $logger = Logger::factory('accountant');

        $logger->info("receive accountant event: \n
            transaction: {$transaction},\n
            transaction_history: {$history},\n
            event_name: {$event_name}");

        if ($transaction) {
            $transaction = json_decode($transaction, true);
        }

        if ($history) {
            $history = json_decode($history, true);
        }

        if (!$transaction) {
            return $this->sendResponse(400, array(
                'message' => 'Incorrect transaction data'
            ));
        }

        try {
            if ('onCommitTransaction' == $event_name) {
                $userTransaction = EventHandler::transactionCommitCallback($transaction, $history);
            }

            return $this->sendResponse(200, array(
                'message' => 'I got it'
            ));
        } catch (\Exception $e) {
            $logger->error($e->getMessage() . "\nTrances:\n" . $e->getTraceAsString());
            return $this->sendResponse(500, array(
                'message' => 'Something went wrong!'
            ));
        }
    }
} 