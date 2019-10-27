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
    // fixed position elements we may use as a reference point
    const EVE_GROUP_SUN = 6;
    const EVE_GROUP_PLANET = 7;
    const EVE_GROUP_MOON = 8;
    const EVE_GROUP_ASTEROIDBELT = 9;
    const EVE_GROUP_STARGATE = 10;
    const EVE_GROUP_STATION = 15;
    // structures we may store individual
    const EVE_GROUP_CONTROLTOWER = 365;
    const EVE_GROUP_CITADEL = 1657;
    const EVE_GROUP_ENGINEERING_COMPLEX = 1404;
    const EVE_GROUP_REFINERY = 1406;
    const EVE_GROUP_COSMICANOMALY = 885;
    const EVE_GROUP_COSMICSIGNATURE = 502;
    const EVE_GROUP_FORCEFIELD = 411;
    const EVE_GROUP_WORMHOLE = 988;
    const EVE_TYPE_UWORMHOLE = 26272;
    // flex structures
    const EVE_GROUP_UPWELL_JUMP_GATE = 1408;
    const EVE_GROUP_UPWELL_CYNO_JAMMER = 2016;
    const EVE_GROUP_UPWELL_CYNO_BEACON = 2017;

    // pattern to analyze and break various inputs
    private const COSMIC_SCAN_REGEXP = '/^([A-Z]{3}-[0-9]{3})\t(.*)\t(.*)\t(.*)\t([0-9\,\.]+.?\%)\t(.*)/';
    private const COSMIC_DSCAN_REGEXP = '/^(\S*)\t([\S ]*)\t([\S ]*)\t(-|[0-9\.\,]+ [AEUkm]+)/';
    
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
                } elseif ($this->isDscan($line)) {
                    $res_counter['dscan']++;
                } elseif ($this->isScan($line)) {
                    $res_counter['scan']++;
                } elseif (trim($line)) { // avoid emtpy lines
                    $this->logger->notice('#Scan: no match for line:  __' . $line . '__');
                }
            }
        }

        $scan_res = null;
        // if results were idendified & collected, now the final step, preperatate & persist
        if ($res_counter['goo']) {
            $scan_res = $this->moonManager->processScan();
        }

        if ($res_counter['dscan'] || $res_counter['scan']) {
            $scan_res = $this->cosmicManager->processScan();
        }

        return (array(
            'message' => $this->prepareResultMessage($res_counter, $scan_res),
            'counter' => $res_counter,
            'scanres' => (isset($scan_res) && isset($scan_res['scan_anom']) && $scan_res['scan_anom'] ?  $scan_res['scan_anom'] : [])
            )
        );
    }

    /**
     * Create a array of message with information about what has been processed
     *
     * @param array of counters
     * @param array result array from cosmicManager->processScan()
     * @return void
     */
    private function prepareResultMessage($res_counter, $scan_res) {
        $msg = null;

        //$this->logger->debug('### scan_res: '.print_r($scan_res, true));

        if (!$res_counter['goo'] && !$res_counter['dscan'] && !$res_counter['scan']) {
            $msg[] = array('info' => 'Emtpy input field, nothing to do');
            return $msg;
        }

        if ($res_counter['goo']) {
            $msg[] = array('info' => $res_counter['goo'] . ' rows scanned');
            if (isset($scan_res['moons']) && isset($scan_res['goo'])) {
                $msg[] = array('info' => $scan_res['moons'] . ' moons in your scan');
                $msg[] = array('info' => $scan_res['goo'] . ' different goo found');
            }
        }
        if ($res_counter['dscan']) {
            $msg[] = array('info' => $res_counter['dscan'] . ' results in dscan');
        }
        if ($res_counter['scan']) {
            $msg[] = array('info' => $res_counter['scan'] . ' results in scan');
            if (isset($scan_res['del_anom']) && $scan_res['del_anom'] > 1) {
                $msg[] = array('info' => $scan_res['del_anom'] . ' old entries removed from storage');
            }
            if (isset($scan_res['scan_anom']) && count($scan_res['scan_anom']) > 0) {
                $counts = $this->countArrayValues($scan_res['scan_anom']);
                if (isset($counts['n']) && $counts['n']) {
                    $msg[] = array('info' => $counts['n'] . ' new entries added');
                }
                if (isset($counts['u']) && $counts['u']) {
                    $msg[] = array('info' => $counts['u'] . ' entries updated');
                }
            }
        }

        return ($msg);
    }

    /**
     * Helper function to count values in a result array
     *
     * @param array $arr
     * @return array result array
     */
    private function countArrayValues($arr)
    {
        $res = array('n' => 0, 'u'=> 0);

        if (isset($arr) && is_array($arr) &&  count($arr)) {
            foreach ($arr as $val) {
                $res[$val] ++;
            }
        }
        return ($res);
    }

    /*******************************************************************************
     * Helpers
     *******************************************************************************/


    /**
     * Takes a EVE distance in m, km or au/ae and calculates the distance in kilomenters
     *
     * @param string Eve distance
     * @return int  distance INT
     */ 
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
            return null;
        }

        return (((int) preg_replace('/[^0-9]+/', '', $dist)) * $multiplier);
    }

        /**
     * Check if scan is a SCAN
     * If match the scan will be added as strucutre data into $this->data_collector
     *
     * @param  string $line
     * @return bool true on match, otherwise false
     */
    public function isScan($line)
    {
        if (preg_match(self::COSMIC_SCAN_REGEXP, $line, $match)) {
            $structure_data = \VposMoon\Service\StructureManager::getStructureArray();

            $structure_data['scantype'] = 'SCAN';
            $structure_data['signature'] = $match[1];
            $structure_data['eve_categoryname'] = $match[2];
            $structure_data['eve_groupname'] = $match[3];
            $structure_data['eve_typename'] = $match[4];
            $structure_data['quality'] = $match[5];
            $structure_data['distance'] = \VposMoon\Service\ScanManager::getEveDistanceKM($match[6]);

            $this->cosmicManager->addToDataToCollector($structure_data);
            return true;
        }

        return false;
    }

    /**
     * Check if scan is a DSCAN
     * If match the scan will be added as strucutre data into $this->data_collector
     *
     * @param  string $line
     * @return bool true on match, otherwise false
     */
    public function isDscan($line)
    {
        if (preg_match(self::COSMIC_DSCAN_REGEXP, $line, $match)) {
            $structure_data = \VposMoon\Service\StructureManager::getStructureArray();

            $structure_data['scantype'] = 'DSCAN';
            $structure_data['eve_typeid'] = $match[1];
            $structure_data['eve_itemname'] = $match[2];
            $structure_data['eve_typename'] = $match[3];
            $structure_data['distance'] = \VposMoon\Service\ScanManager::getEveDistanceKM($match[4]);

            $this->cosmicManager->addToDataToCollector($structure_data);
            return true;
        }

        return false;
    }

    /**
     * Check if scan is an anomaly
     *
     * @param  type $eve_groupID
     * @param  type $eve_typeID
     * @return type
     */
    public static function isAnomaly($eve_groupID, $eve_typeID = 0)
    {
        if ($eve_groupID == self::EVE_GROUP_COSMICANOMALY
            || $eve_groupID == self::EVE_GROUP_COSMICSIGNATURE
            || $eve_groupID == self::EVE_GROUP_WORMHOLE
            || $eve_typeID == self::EVE_TYPE_UWORMHOLE
        ) {
            return (true);
        }
        return (false);
    }

    /**
     * Check if scan is a structure
     *
     * @param [type] $eve_groupID
     * @param integer $eve_typeID
     * @return boolean
     */
    public static function isStructure($eve_groupID, $eve_typeID = 0)
    {
        if ($eve_groupID == self::EVE_GROUP_CONTROLTOWER
            || $eve_groupID == self::EVE_GROUP_CITADEL
            || $eve_groupID == self::EVE_GROUP_ENGINEERING_COMPLEX
            || $eve_groupID == self::EVE_GROUP_REFINERY
            || $eve_groupID == self::EVE_GROUP_UPWELL_JUMP_GATE
            || $eve_groupID == self::EVE_GROUP_UPWELL_CYNO_JAMMER
            || $eve_groupID == self::EVE_GROUP_UPWELL_CYNO_BEACON
        ) {
            return (true);
        }
        return (false);
    }

    /**
     * Check if scan is a refinery
     *
     * @param [type] $eve_groupID
     * @param integer $eve_typeID
     * @return boolean
     */
    public static function isRefinery($eve_groupID, $eve_typeID = 0)
    {
        if ($eve_groupID == self::EVE_GROUP_REFINERY) {
            return (true);
        }
        return (false);
    }

    /**
     * Check if scan is a celestial
     *
     * @param [type] $eve_groupID
     * @param integer $eve_typeID
     * @return boolean
     */
    public static function isCelestial($eve_groupID, $eve_typeID = 0)
    {
        if ($eve_groupID == self::EVE_GROUP_SUN
            || $eve_groupID == self::EVE_GROUP_PLANET
            || $eve_groupID == self::EVE_GROUP_MOON
            || $eve_groupID == self::EVE_GROUP_ASTEROIDBELT
            || $eve_groupID == self::EVE_GROUP_STARGATE
            || $eve_groupID == self::EVE_GROUP_STATION
        ) {
            return (true);
        }
        return (false);
    }
}
