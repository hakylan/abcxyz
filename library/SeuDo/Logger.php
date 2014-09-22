<?php
namespace SeuDo;
use SeuDo;
use Background\Task\BackgroundBase;
use Flywheel\Config\ConfigHandler;
use Flywheel\Exception;
use Flywheel\Log\Handler\RotatingFileHandler;
use Flywheel\Log\Logger as BaseLogger;

use Seudo\ErrorHandler;
use Monolog\Processor\WebProcessor;

use Monolog\Handler\Mongo;

use Monolog\Handler\MongoDBHandler;


class Logger extends BaseLogger {
    protected static $_instances = array();

    protected static $_mongo;

    const BUSINESS = 'business';
    const SYSTEM = 'system';


    /**
     * factory Logger by channel
     *
     * @param $channel
     * @return \Monolog\Logger
     */
    public static function factory($channel) {

        if (!isset(self::$_instances[$channel])) {

            $logger = new self($channel);

            $loggerConfig = ConfigHandler::get('logger');
            $path   = $loggerConfig['path'];

            $debug  = $loggerConfig['debug']
                ? $loggerConfig['debug']:Logger::INFO;

            $filePath = $path.strtolower($channel);

            $logger->pushHandler(new RotatingFileHandler($filePath, 60, $debug));

            if ($channel == 'accountant') {
                if (isset($loggerConfig['mongo'])) {
                    //mongo log
                    $logger->pushHandler(new MongoDBHandler(
                        self::getMongoDBConnection(),
                        $loggerConfig['mongo']['database'],
                        'accountant',
                        self::INFO
                    ));
                }
            }

            $logger->pushProcessor(array('\Seudo\Logger','errorHandle'));


            self::$_instances[$channel] = $logger;
        }
        return self::$_instances[$channel];
    }

    public static function getEmailToAlert() {
        $loggerConfig = ConfigHandler::get('logger');
        $mail = $loggerConfig['mail'];

        $master = array($mail['master']);
        $follows = array();
        if(isset($mail['follow']) && !empty ($mail['follow']) ) {
            $follows = $mail['follow'];
        }

        $receivers = array_merge_recursive($master, $follows);
        return $receivers;
    }

    public static function errorHandle($record) {

        $traces = array_reverse(debug_backtrace());
        $trace = $traces[0];

        if(is_callable(array('\SeuDo\ErrorHandler','errorHandling'))) {
            if($record['level'] > Logger::NOTICE) {
                $message = Logger::$levels[$record['level']].' -- '.$record['message'].'['.json_encode($record['context']).']';
                $log = call_user_func_array(array('\SeuDo\ErrorHandler','errorHandling'),array( $record['level'], $message, $trace['file'], $trace['line']));
                self::sendMailError($log);
            }

        }
        return $record;
    }

    public static function sendMailError ($log) {

        $receivers = self::getEmailToAlert();

        if($receivers && !empty ($receivers)){
            foreach ($receivers as $receive) {
                $data = array(
                    'email'=>$receive,
                    'subject'=>'A log has been created at '.date('Y/d/m H:i:s',time()),
                    'body'=>$log
                );
                Queue::emailError()->push(json_encode($data));
            }
        }
    }
    /**
     * @return \Mongo|\MongoClient
     * @throws \RuntimeException
     */
    public static function getMongoDBConnection() {
        if (null == self::$_mongo) {
            $config = ConfigHandler::get('logger');
            if (!isset($config['mongo'])) {
                throw new \RuntimeException('Logger: config mongo not found');
            }

            if (class_exists('MongoClient')) {
                $mongo = new \MongoClient($config['mongo']['dns']);
            } elseif (class_exists('Mongo')) {
                $mongo = new \Mongo($config['mongo']['dns']);
            } else {
                throw new \RuntimeException('Mongo extension does not existed!');
            }

            self::$_mongo = $mongo;
        }

        return self::$_mongo;
    }
}