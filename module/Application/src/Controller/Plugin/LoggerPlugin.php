<?php
namespace Application\Controller\Plugin;

use Laminas\Mvc\Controller\Plugin\AbstractPlugin;

/**
 * Logger Plugin
 *
 * Logger to be available in any Controller
 * Typical usage inside a controller:
 *    $this->logger()->log(\Laminas\Log\Logger::ERR, "{function} Testing logger", ["function" => __FUNCTION__]);
 *
 *    $this->logger()->emerg('EMERG');
 *    $this->logger()->alert('ALERT');
 *    $this->logger()->crit('CRIT');
 *    $this->logger()->err('ERR');
 *    $this->logger()->warn('WARN');
 *    $this->logger()->notice('NOTICE');
 *    $this->logger()->info('INFO');
 *    $this->logger()->debug('DEBUG');
 */

class LoggerPlugin extends AbstractPlugin
{
    private $container;

    private $logger;

    private $writer;

    private $filter;

    private $config;

    public function __construct($container)
    {
        $this->container = $container;

        $this->getConfig();

        $this->logger = new \Laminas\Log\Logger();
        $this->logger->addProcessor(new \Laminas\Log\Processor\PsrPlaceholder());
        $this->setWriter();
        $this->setFilter();
    }

    /**
     * Add a message as a log entry
     *
     * Usage, available in any controller:
     *    $this->logger()->log(\Laminas\Log\Logger::ERR, "{function} Testing logger", ["function" => __FUNCTION__]);
     *
     * @param  int               $priority
     * @param  mixed             $message
     * @param  array|Traversable $extra
     * @return Logger
     * @throws Exception\InvalidArgumentException if message can't be cast to string
     * @throws Exception\InvalidArgumentException if extra can't be iterated over
     * @throws Exception\RuntimeException if no log writer specified
     */
    public function log($priority, $message, $extra = [])
    {
        return ($this->logger->log($priority, $message, $extra));
    }

    /**
     * @param  string            $message
     * @param  array|Traversable $extra
     * @return Logger
     */
    public function emerg($message, $extra = [])
    {
        return ($this->logger->log(\Laminas\Log\Logger::EMERG, $message, $extra));
    }

    /**
     * @param  string            $message
     * @param  array|Traversable $extra
     * @return Logger
     */
    public function alert($message, $extra = [])
    {
        return ($this->logger->log(\Laminas\Log\Logger::ALERT, $message, $extra));
    }

    /**
     * @param  string            $message
     * @param  array|Traversable $extra
     * @return Logger
     */
    public function crit($message, $extra = [])
    {
        return ($this->logger->log(\Laminas\Log\Logger::CRIT, $message, $extra));
    }

    /**
     * @param  string            $message
     * @param  array|Traversable $extra
     * @return Logger
     */
    public function err($message, $extra = [])
    {
        return ($this->logger->log(\Laminas\Log\Logger::ERR, $message, $extra));
    }

    /**
     * @param  string            $message
     * @param  array|Traversable $extra
     * @return Logger
     */
    public function warn($message, $extra = [])
    {
        return ($this->logger->log(\Laminas\Log\Logger::WARN, $message, $extra));
    }

    /**
     * @param  string            $message
     * @param  array|Traversable $extra
     * @return Logger
     */
    public function notice($message, $extra = [])
    {
        return ($this->logger->log(\Laminas\Log\Logger::NOTICE, $message, $extra));
    }

    /**
     * @param  string            $message
     * @param  array|Traversable $extra
     * @return Logger
     */
    public function info($message, $extra = [])
    {
        return ($this->logger->log(\Laminas\Log\Logger::INFO, $message, $extra));
    }

    /**
     * @param  string            $message
     * @param  array|Traversable $extra
     * @return Logger
     */
    public function debug($message, $extra = [])
    {
        return ($this->logger->log(\Laminas\Log\Logger::DEBUG, $message, $extra));
    }

    /**
     * Set log writer from config ['log']['logfile']
     * add date (ymd) to logfile if config ['log']['logdate'] is true
     *
     * @return void
     */
    private function setWriter()
    {
        if (isset($this->config['logfile'])) {
            $logfile = $this->config['logfile'];
        } else {
            $logfile = './data/log/logfile';
        }

        if (isset($this->config['logdate']) && $this->config['logdate'] === true) {
            $logfile .= '_'-date('ymd');
        }

        $this->writer = new \Laminas\Log\Writer\Stream($logfile . '.log');
        $this->logger->addWriter($this->writer);
    }

    /**
     * Set log filter to show only messages of a certain loglevel
     * takes loglevel as integer from from config ['log']['loglevel']
     * defaults to 3 (ERR)
     *
     * Loglevels: EMERG:0, ALERT:1, CRIT:2, ERR:3, WARN:4, NOTICE:5, INFO:6, DEBUG:7
     *
     * @return void
     */
    private function setFilter()
    {
        if (isset($this->config['loglevel'])) {
            $loglevel = $this->config['loglevel'];
        } else {
            $loglevel = \Laminas\Log\Logger::ERR;
        }

        $this->filter = new \Laminas\Log\Filter\Priority($loglevel);
        $this->writer->addFilter($this->filter);
    }

    /**
     * @return void
     */
    private function getConfig()
    {
        $configfile = $this->container->get('config');

        if (isset($configfile['log'])) {
            $this->config = $configfile['log'];
        } else {
            $this->config = false;
        }
    }
}
