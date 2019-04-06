<?php

namespace VposMoon\Service;

/**
 * The MoonController is about Moons, Survey Scans and Moon Goo
 */
class ScanManager
{

    /*
     * EVE constants
     */
    const EVE_CATEGORY_STRUCTURE = 23;
    const EVE_CATEGORY_SHIP = 6;

    const EVE_GROUP_MOON = 8;
    const EVE_GROUP_CONTROLTOWER = 365;
    const EVE_GROUP_COSMICANOMALY = 885;
    const EVE_GROUP_COSMICSIGNATURE = 502;
    const EVE_GROUP_FORCEFIELD = 411;
    const EVE_GROUP_WORMHOLE = 988;

    const EVE_TYPE_UWORMHOLE = 26272;

    /**
     * Moon manager.
     *
     * @var VposMoon\Service\MoonManager
     */
    private $moonManager;

    /**
     *
     * @var VposMoon\Service\CosmicManager
     */
    private $cosmicManager;

    /**
     *
     * @var \Application\Controller\Plugin\LoggerPlugin
     */
    private $logger;

    /**
     * Constructor. Its purpose is to inject dependencies into the controller.
     */
    public function __construct($moonManager, $cosmicManager, $logger)
    {
        $this->moonManager = $moonManager;
        $this->cosmicManager = $cosmicManager;
        $this->logger = $logger;
    }

    /**
     * Analyse scan results
     * The scan result get idendified and routed to the appropriate manager
     * After the line by line analysis is finished the managers store the (prepared) values.
     *
     * @param string $data_scan
     * @param int   $eveuser_id
     *
     * @return array 'message', 'counter'
     */
    public function processScan($data_scan, $eveuser_id = 0)
    {
        $res_counter = array('goo' => 0, 'dscan' => 0, 'scan' => 0);

        // split input into lines. Interpret result line By line
        $lines = preg_split("/[\f\r\n]+/", $data_scan);
        if (!empty($lines)) {
            foreach ($lines as $line) {
                // if is moon Scan
                if ($this->moonManager->isMoonScan($line, $eveuser_id)) {
                    $res_counter['goo']++;
                } elseif ($this->cosmicManager->isDscan($line)) {
                    $res_counter['dscan']++;
                } elseif ($this->cosmicManager->isScan($line)) {
                    $res_counter['scan']++;
                } elseif (trim($line)) { // avoid emtpy lines
                    $this->logger->notice('#Scan: no match for line:  __' . $line . '__');
                }
            }
        } else {
            $message[] = array('info' => 'Emtpy input field, nothing to do');
        }
        $message[] = array('info' => 'thanks, scanned a lot');

        // $this->logger->debug($res_counter);

        // if results were idendified & collected, now the final step, preperatate & persist
        if ($res_counter['goo']) {
            $this->moonManager->processScan();
        }
        if ($res_counter['dscan'] || $res_counter['scan']) {
            $structure_plusminus = $this->cosmicManager->processScan();
            // $this->logger->debug('### Structure plusminus: '.print_r($structure_plusminus, true));
        }

        return (array('message' => $message, 'counter' => $res_counter, 'newscan' => ($structure_plusminus['new'] ?  $structure_plusminus['new'] : [])));
    }

    /*******************************************************************************
     * Helpers
     *******************************************************************************/

    public static function getEveDistanceKM($dist)
    {

        $multiplier = 1;

        if (stristr($dist, 'km')) {
            $multiplier = 1;
        } elseif (stristr($dist, 'm')) {
            $multiplier = 0.001;
        } elseif (stristr($dist, 'au') || stristr($dist, 'ae')) {
            $multiplier = 149597870.7;
        } else {
            // no match
            return false;
        }

        return (((int) preg_replace('/[^0-9]+/', '', $dist)) * $multiplier);
    }

    public function isAnomaly($data)
    {

        if ($data->eve_groupID == self::EVE_GROUP_COSMICANOMALY ||
            $data->eve_groupID == self::EVE_GROUP_COSMICSIGNATURE ||
            $data->eve_groupID == self::EVE_GROUP_WORMHOLE ||
            $data->eve_typeID == self::EVE_TYPE_UWORMHOLE) {
            return (true);
        }
        return (false);
    }
}
