<?php

namespace app\core\base;

use \tecsvit\ObjectHelper as OH;
use \tecsvit\ConsoleLogger as Log;

/**
 * Class Base
 * CLI parsing/routing
 * usage: `./node [command]`
 * commands are defined as the keys in the $arrayCommands array
 *
 * @property array      $config
 * @property boolean    $verbose
 * @property array      $errors
 * @property array      $resultMessage
 * @property array      $arrayCommands
 * @property string     $command
 */
class Base
{
    public $config;

    protected $verbose          = false;
    protected $errors           = [];
    protected $resultMessage    = [];
    protected $command;

    private $arrayCommands      = [
        'add'       => 'add',
        'remove'    => 'remove',
        'up'        => 'up',
        'down'      => 'down',
        'rename'    => 'rename'
    ];

    /**
     * Base constructor.
     * @param array $config
     * @param bool  $verbose
     */
    public function __construct($config = [], $verbose = false)
    {
        $this->config    = $config;
        $this->verbose   = $verbose;
        $this->initDb(OH::getAttribute($this->config, 'db'));
    }

    /**
     * @param $dbConfig
     * @return void
     */
    public function initDb($dbConfig)
    {
        Db::initDb($dbConfig);
    }

    /**
     * @param array $argv
     * @return void
     */
    public function run($argv = [])
    {
        $file       = OH::getAttribute($argv, 0);
        $command    = OH::getAttribute($argv, 1);
        $arguments  = array_splice($argv, 2, 2);

        if (count($argv) < 2 || !array_key_exists($command, $this->arrayCommands)) {
            ksort($this->arrayCommands);
            Log::log('Usage: ');
            Log::log('    ' . $file . ' [COMMAND]', false, Log::WARNING);
            Log::log('Commands: ' . PHP_EOL . ' - ' . implode(array_keys($this->arrayCommands), PHP_EOL . ' - '));
            die(1);
        } elseif (method_exists($this, ($this->arrayCommands[$command] . 'Adapter'))) {
            $this->command = $file . ' '  . $command;
            if ($this->{$this->arrayCommands[$command] . 'Adapter'}($arguments) === true) {
                foreach ($this->getResultMessage() as $resultMessage) {
                    Log::log($resultMessage);
                }
            } else {
                foreach ($this->getErrors() as $error) {
                    Log::log($error, false, Log::ERROR);
                }
            }
        } else {
            Log::log(
                'Error! `' . $this->arrayCommands[$command] . '` command does not implemented!',
                false,
                Log::ERROR
            );
            die(2);
        }
    }

    /**
     * @param array $attributes
     * @return void
     */
    public function setAttributes(array $attributes)
    {
        foreach ($attributes as $attribute => $value) {
            if (property_exists($this, $attribute) && strpos($attribute, '_') !== 0) {
                $this->$attribute = $value;
            }
        }
    }

    /**
     * @return array
     */
    public function getErrors()
    {
        return $this->errors;
    }

    /**
     * @return array
     */
    public function getResultMessage()
    {
        return $this->resultMessage;
    }

    /**
     * @param string $error
     * @return void
     */
    protected function addError($error)
    {
        $this->errors[] = $error;
    }

    /**
     * @param string $message
     * @return void
     */
    protected function addResultMessage($message)
    {
        $this->resultMessage[] = $message;
    }
}
