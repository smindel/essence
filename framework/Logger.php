<?php

class Logger extends Base
{
    const EMERGENCY = 'emergency';  // System is unusable.
    const ALERT     = 'alert';      // Action must be taken immediately.
    const CRITICAL  = 'critical';   // Critical conditions.
    const ERROR     = 'error';      // Runtime errors that do not require immediate action but should typically be logged.
    const WARNING   = 'warning';    // Exceptional occurrences that are not errors.
    const NOTICE    = 'notice';     // Normal but significant events.
    const INFO      = 'info';       // Interesting events.
    const DEBUG     = 'debug';      // Detailed debug information.

    public function emergency($message, array $context = array()) { return $this->log(self::EMERGENCY, $message, $context); }
    public function alert($message, array $context = array()) { return $this->log(self::ALERT, $message, $context); }
    public function critical($message, array $context = array()) { return $this->log(self::CRITICAL, $message, $context); }
    public function error($message, array $context = array()) { return $this->log(self::ERROR, $message, $context); }
    public function warning($message, array $context = array()) { return $this->log(self::WARNING, $message, $context); }
    public function notice($message, array $context = array()) { return $this->log(self::NOTICE, $message, $context); }
    public function info($message, array $context = array()) { return $this->log(self::INFO, $message, $context); }
    public function debug($message, array $context = array()) { return $this->log(self::DEBUG, $message, $context); }

    public function log($level, $message, array $context = array())
    {
        $line = implode(' : ', array(
            date('Y-m-d H:i:s'),
            $level,
            $this->interpolate($message, $context),
        )) . "\n";
        file_put_contents(BASE_PATH . DIRECTORY_SEPARATOR . 'messages.log', $line, FILE_APPEND);
    }

    protected function interpolate($message, array $context = array())
    {
        // build a replacement array with braces around the context keys
        $replace = array();
        foreach ($context as $key => $val) {
            $replace['{' . $key . '}'] = $val;
        }
        // interpolate replacement values into the message and return
        return strtr($message, $replace);
    }
}