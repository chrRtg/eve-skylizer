<?php

namespace VposMoon\View\Helper;

use Zend\View\Helper\AbstractHelper;

class VposMoonHelper extends AbstractHelper
{

    private $eveDataManager;
    private $moonManager;
    private $config;

    /**
     * Constructor.
     *
     * @param array $items Menu items.
     */
    public function __construct($eveDataManager, $moonManager, $config)
    {
        $this->eveDataManager = $eveDataManager;
        $this->moonManager = $moonManager;
        $this->config = $config;
    }

    /**
     *
     * @param  type $vpos
     * @return string
     */
    public function determineVposType($vpos_elem)
    {
        $type = ['cat' => 'unknown', 'type' => 'unscanned', 'name' => 'unscanned'];

        switch ($vpos_elem['at_groupId']) {
            case '365':
                $type = ['cat' => 'structure', 'type' => 'pos', 'name' => 'Pos'];
                break;
            case '1406':
                $type = ['cat' => 'structure', 'type' => 'refinery', 'name' => 'Refinery'];
                break;
            case '1404':
                $type = ['cat' => 'structure', 'type' => 'engineering', 'name' => 'Engineering'];
                break;
            case '1657':
                $type = ['cat' => 'structure', 'type' => 'citadel', 'name' => 'Citadel'];
                break;
            case '1408':
                $type = ['cat' => 'structure', 'type' => 'flex', 'name' => 'Flex'];
                break;
            case '2016':
                $type = ['cat' => 'structure', 'type' => 'flex', 'name' => 'Flex'];
                break;
            case '2017':
                $type = ['cat' => 'structure', 'type' => 'flex', 'name' => 'Flex'];
                break;
            case '988':
                $type = ['cat' => 'celestial', 'type' => 'wh', 'name' => 'Wormhole'];
                break;
            case '885':
                if ($vpos_elem['acd_type'] == 'anomaly') {
                    $type = ['cat' => 'anomaly', 'type' => 'anomaly', 'name' => 'Anomaly'];
                } elseif ($vpos_elem['acd_type'] == 'faction') {
                    $type = ['cat' => 'anomaly', 'type' => 'faction', 'name' => 'Faction :' . $vpos_elem['acd_class']];
                } elseif ($vpos_elem['acd_type'] == 'unrated') {
                    $type = ['cat' => 'anomaly', 'type' => 'unrated', 'name' => 'unrated'];
                } elseif ($vpos_elem['acd_type'] == 'gas') {
                    $type = ['cat' => 'anomaly', 'type' => 'gas', 'name' => 'Gas'];
                } elseif ($vpos_elem['acd_type'] == 'ore') {
                    $type = ['cat' => 'anomaly', 'type' => 'ore', 'name' => 'Ore'];
                }
                break;
            case '502':
                if ($vpos_elem['acd_type'] == 'data') {
                    $type = ['cat' => 'signature', 'type' => 'data', 'name' => 'Data'];
                } elseif ($vpos_elem['acd_type'] == 'ghost') {
                    $type = ['cat' => 'signature', 'type' => 'ghost', 'name' => 'Ghost'];
                } elseif ($vpos_elem['acd_type'] == 'relic') {
                    $type = ['cat' => 'signature', 'type' => 'relic', 'name' => 'Relic'];
                } elseif ($vpos_elem['acd_type'] == 'plex') {
                    $type = ['cat' => 'signature', 'type' => 'plex', 'name' => 'Plex :' . $vpos_elem['acd_class']];
                }
                break;
            default:
                break;
        }
        return $type;
    }

    /**
     * Takes 'goo' string from MoonController Index action and renders the output
     *
     * @param  string $input
     * @return string rendered HTML
     */
    public function renderMoonComposition($input)
    {

        $moons = explode('#', $input);

        $res = '';

        if (!empty($moons)) {
            foreach ($moons as $moon) {
                if (!empty($moon)) {
                    $goo = explode('|', $moon);
                    if (!empty($goo)) {
                        $res .= '<span class="gooval">' . round(((float) $goo[0] * 100), 0) . '%</span>&nbsp;';
                        $res .= '<span class="gooname">' . $goo[1] . '</span>&nbsp;';
                        $res .= '<span class="gooprice">' . number_format(floatval($goo[2]), 0) . '</span><br />';
                    }
                }
            }
        }

        return ($res);
    }

    /**
     * Takes 'goo' string from MoonController Index action and calculates the normalilzed base price
     *
     * @param  string $input
     * @return string rendered HTML
     */
    public function calculateMoonComposition($input)
    {

        $moons = explode('#', $input);

        $val = 0;

        if (!empty($moons)) {
            foreach ($moons as $moon) {
                if (!empty($moon)) {
                    $goo = explode('|', $moon);
                    $val += floatval($goo[0]) * floatval($goo[2]);
                }
            }
        }

        $res = '<span class="goopricesum">' . number_format(round($val), 0) . '</span>';

        return ($res);
    }

    /**
     * Takes 'goo' string from MoonController Index action and calculates the normalilzed base price
     *
     * @param  string $input
     * @return string rendered HTML
     */
    public function renderMoonMateriallist($input)
    {
        $res = '';

        $data = $this->calculateMoonMateriallist($input);

        // sort ore by baseprice
        uasort($data, array($this, 'sortWorthForRenderMoonMateriallist'));

        // second step, create view
        if (!empty($data)) {
            foreach ($data as $k => $row) {
                if (!empty($row) && $k != 'val') {
                    $res .= '<span class="gooval">' . number_format(round(((float) $row['qty'] * 100), 0)) . '</span>&nbsp;';
                    $res .= '<span class="gooname">' . $row['name'] . '</span>&nbsp;';
                    $res .= '<span class="gooprice">' . number_format(floatval($row['worth']), 0) . '</span><br />';
                }
            }
        }

        return ($res);
    }

    /**
     * Utility function for @see renderMoonMateriallist()
     * Sort array by worth, used by uasort()
     *
     * @param  type $a
     * @param  type $b
     * @return type
     */
    private function sortWorthForRenderMoonMateriallist($a, $b)
    {
        return $a["worth"] <= $b["worth"];
    }

    /**
     *
     * @param  type $input
     * @return type
     */
    public function renderMoonMaterialValue($input)
    {
        $res = '';

        $data = $this->calculateMoonMateriallist($input);

        if (!empty($data)) {
            $res = $data['val'];
        }

        return ($res);
    }

    /**
     * Utility function for @see renderMoonMateriallist() and calculateMoonMateriallist()
     * Parse input string with material list for a single moon into a array.
     *
     * @param  string $input
     * @return array
     */
    private function calculateMoonMateriallist($input)
    {
        $data = [];
        $data['val'] = 0;

        $moons = explode('#', $input);

        // first step is to parse the string into a array and perform some calculation
        if (!empty($moons)) {
            foreach ($moons as $moon) {
                if (!empty($moon)) {
                    $goo = explode('|', $moon);
                    if (!empty($goo)) {
                        $data[$goo['3']]['name'] = $goo['5'];
                        $data[$goo['3']]['worth'] = $goo['6'];
                        if (empty($data[$goo['3']]['qty'])) {
                            $data[$goo['3']]['qty'] = (floatval($goo['1']) * floatval($goo['4']));
                        } else {
                            $data[$goo['3']]['qty'] += (floatval($goo['1']) * floatval($goo['4']));
                        }
                        $data['val'] += floatval($data[$goo['3']]['worth']) * (floatval($goo['1']) * floatval($goo['4']));
                    }
                }
            }
        }
        return ($data);
    }

    /**
     * Creates a list of links to the neighbours, systems as well constellations.
     * If the selected location in $system_data is a constellation, a list of links to the systems in the constellation is beeing created.
     *
     * @param  array  $system_data
     * @param  string $current_route default = '/vposmoon'
     * @return string
     */
    public function createNeighboursNavigation($system_data, $current_route = '/vposmoon')
    {
        $text = '';

        $neighbour_list = $this->createNeighboursNavigationList($system_data);

        if (!empty($neighbour_list['constellation_current'])) {
            $id = key($neighbour_list['constellation_current']);
            $text .= '<a href="/' . $current_route . '?system=' . $id . '" class="btn btn-constellation btn-xs sytemswitch" data-id="' . $id . '">' . current($neighbour_list['constellation_current']) . '</a>';
        }

        if (!empty($neighbour_list['system'])) {
            foreach ($neighbour_list['system'] as $id => $name) {
                $text .= '<a href="/' . $current_route . '?system=' . $id . '" class="btn btn-system btn-xs sytemswitch" data-id="' . $id . '">' . $name . '</a>';
            }
        }

        if (!empty($neighbour_list['constellation'])) {
            foreach ($neighbour_list['constellation'] as $id => $name) {
                $text .= '<a href="/' . $current_route . '?system=' . $id . '" class="btn btn-constellation btn-xs sytemswitch" data-id="' . $id . '">' . $name . '</a>';
            }
        }

        return ($text);
    }

    /**
     * Calculates neighbour systems and constellations.
     * If $system_data is about a constellation, a list of systems in the constellation is created as well.
     *
     * @param  array $system_data
     * @return array
     */
    private function createNeighboursNavigationList($system_data)
    {

        $neighbour_list = array();

        $res = $this->eveDataManager->getNeighboursByID($system_data['itemid']);

        if (empty($system_data['constellationid'])) {
            // mode: constellation
            foreach ($res as $system) {
                if ($system['mdc_id'] != $system_data['constellationid'] && $system['mdc_id'] != $system_data['itemid']) {
                    $neighbour_list['constellation'][$system['mdc_id']] = $system['mdc_name'] . ' <span class="system">(' . $system['mds_name'] . ')</span>';
                } else {
                    $neighbour_list['system'][$system['mds_id']] = $system['mds_name'];
                }
            }
        } else {
            // mode: system
            $neighbour_list['constellation_current'][$system_data['constellationid']] = $system_data['constellation'];
            foreach ($res as $system) {
                $neighbour_list['system'][$system['mds_id']] = $system['mds_name'];

                if ($system['mdc_id'] != $system_data['constellationid'] && $system['mdc_id'] != $system_data['itemid']) {
                    $neighbour_list['constellation'][$system['mdc_id']] = $system['mdc_name'] . ' <span class="system">(' . $system['mds_name'] . ')</span>';
                }
            }
        }

        return ($neighbour_list);
    }

    /**
     * Create Ore Option List to be used in HTML select
     *
     * @param  type $solar
     * @param  type $selected_id
     * @return boolean
     */
    public function getOreListAsOptions($solar, $selected_id = 0)
    {
        if (empty($solar)) {
            return false;
        }

        $data = $this->moonManager->getOreList($solar);
        if (empty($data)) {
            return (false);
        }

        $res = '';

        foreach ($data as $option) {
            $res .= '<option ' . (intval($selected_id) == intval($option['id']) ? 'selected' : '') . ' value="' . $option['id'] . '">' . $option['name'] . ' (' . $option['cnt'] . ')</option>';
        }
        return ($res);
    }

    /**
     * Create Ore-Compositions Option List to be used in HTML select
     *
     * @param  type $solar
     * @param  type $selected_id
     * @return boolean
     */
    public function getCompositionListAsOptions($solar, $selected_id = 0)
    {
        if (empty($solar)) {
            return false;
        }

        $data = $this->moonManager->getCompositionList($solar);
        if (empty($data)) {
            return (false);
        }

        $res = '';

        foreach ($data as $option) {
            $res .= '<option ' . (intval($selected_id) == intval($option['id']) ? 'selected' : '') . ' value="' . $option['id'] . '">' . $option['name'] . ' (' . $option['cnt'] . ')</option>';
        }
        return ($res);
    }

    /**
     * Undocumented function
     *
     * @param [type] $group_ids
     * @param integer $selected_id
     * @return void
     */
    public function getEveTypesListAsOptions($group_ids, $selected_id = 0)
    {
        if (!is_array($group_ids)) {
            return false;
        }

        $data = $this->eveDataManager->getTypeByGroupIDs($group_ids);
        if (empty($data)) {
            return (false);
        }

        $res = '';

        foreach ($data as $option) {
            $res .= '<option ' . (intval($selected_id) == intval($option['typeid']) ? 'selected' : '') . ' value="' . $option['typeid'] . '">' . $option['typename'] . '</option>';
        }
        return ($res);
    }

    /**
     * Create a URL to a "show me all details about a system" webiste for a given system by his name.
     *
     * Differentiate between regular systems and wormholes, uses individual URLs for both.
     *
     * @param  string $system_name
     * @return type
     */
    public function createExternalSystemURL($system_name)
    {
        if (isset($this->config['settings']['ext_system_url'])) {
            $ext_system_url = $this->config['settings']['ext_system_url'];
        } else {
            $ext_system_url = 'https://eve-prism.com/?view=system&name=%s';
        }

        if (isset($this->config['settings']['ext_wh_url'])) {
            $ext_wh_url = $this->config['settings']['ext_wh_url'];
        } else {
            $ext_wh_url = 'http://anoik.is/systems/%s';
        }

        $wh_regex = '/J[0-9]{6}/';
        if (preg_match($wh_regex, $system_name)) {
            return (sprintf($ext_wh_url, $system_name));
        } else {
            return (sprintf($ext_system_url, $system_name));
        }
    }

    /**
     * Create a system class string from L and H ClassID
     *
     * @param int $classidH SystemClassID
     * @param int $classidL regionClassID
     * @return string Class as string
     */
    public function formatClassid($classidH, $classidL)
    {
        if (!$classidL || $classidL === 0) {
            return '';
        }

        $val = 0;
        if ($classidH && $classidH !== 0) {
            $val = (int) $classidH;
        } else {
            $val = (int) $classidL;
        }

        switch ($val) {
            case 7:
                return '(high)';
            case 8:
                return '(low)';
            case 9:
                return '(0.0)';
            case 10:
                return '(WH)';
            default:
                return '(C' . $val . ')';
        }
    }

    /**
     * Convert moon names to shortform "PxMy"
     * The shortform is better suited for filtering.
     *
     * Example:
     * "Zaid VIII - Moon 2" becomes "P8M2"
     *
     * @param  string $moonname
     * @return string
     */
    public function convertMoonToShortform($moonname)
    {

        $regex = '/^[\w-]+\s([IXV]+)\s-\sMoon\s(\d+)$/';
        preg_match($regex, $moonname, $matches);

        if (!empty($matches) && !empty($matches['1']) && !empty($matches['2'])) {
            return ('P' . $this->romansToNumber($matches['1']) . 'M' . $matches['2']);
        }

        return ('');
    }

    /**
     * Format date to "nnd nnh nnm" format and set classes depending on the distance to today.
     *
     * The method accept date strings and DateTime objects for both date parameters
     *
     * @param string Label
     * @param string-DateTime $date_to
     * @param integer $warndays [5]
     * @param string css class if warning  (default "badge badge-warning")
     * @param string css class if all fine (default "badge")
     * @return string human readable date difference
     */
    public function dateUntil($title, $date_to, $warndays = 5, $warnclass = 'badge  badge-warning',  $class='badge')
    {
        $differenceFormat = '%ad %hh %im';

        $now = new \DateTime('NOW');

        if (is_string($date_to)) {
            $tmp_t = new \DateTime($date_to);
            $date_to = $tmp_t;
        }

        $interval = date_diff($now, $date_to);

        $diff_days = (int) $interval->format('%a');
        if ($diff_days <= $warndays) {
            $class=$warnclass;
        }

        if ($title) {
            $title .= ': ';
        }

        if($date_to < $now) {
            $class=$warnclass;
            $differenceFormat = '- %ad %hh %im';
        }

        return $interval->format('<span class="' . $class . '" title="due: '.date_format($date_to, 'Y/m/d H:i').'">' . $title . $differenceFormat.'</span>');
    }

    /**
     * Create human readable reinforce timer.
     *
     * @param integer day 	ReinforceWeekday: Monday is 0 and Sunday is 6 
     * @param integer hour  ReinforceHour: The structure will become vulnerable at a random time that is +/- 2 hours centered on the value of this property
     * @param string css class if warning  (default "badge badge-warning")
     * @param string css class if all fine (default "badge")
     * @return void
     */
    public function formatReinforceDate($reinforce_day, $reinforce_hour = 0, $warnclass = 'badge  badge-warning',  $class = 'badge badge-normal')
    {
        $today = (int) date('w'); // 0 for Sunday, 6 for Saturday
        $today = ($today-1 >= 1 ? $today-1 : 6); // format to eve style

        if ($reinforce_day == $today) {
            $class= $warnclass;
        }

        return('<span class="' . $class . '" title="timer">' . sprintf("%s %02d:00", $this->getDayByInt($reinforce_day), $reinforce_hour) . ' +/-2h</span>');
    }

    /**
     * Get abbrevated weekday by eve-weekday integer
     *
     * Monday is 0 and Sunday is 6
     *
     * @param int $day
     * @return string   abbrevated weekday
     */
    private function getDayByInt($day)
    {
        $dtxt = '';
        switch((int) $day) {
            case 0:
                $dtxt = 'Mon';
                break;
            case 1:
                $dtxt = 'Tue';
                break;
            case 2:
                $dtxt = 'Wed';
                break;
            case 3:
                $dtxt = 'Thu';
                break;
            case 4:
                $dtxt = 'Fri';
                break;
            case 5:
                $dtxt = 'Sat';
                break;
            case 6:
            case -1:
                $dtxt = 'Sun';
                break;
            default:
                $dtxt = '??';
                break;
        }
        return ($dtxt);
    }

    /**
     * Convert roman number to decimal
     *
     * @param  string $roman
     * @return string
     */
    private function romansToNumber($roman)
    {

        // from http://stackoverflow.com/questions/6265596/how-to-convert-a-roman-numeral-to-integer-in-php

        $romans = array(
            'M' => 1000,
            'CM' => 900,
            'D' => 500,
            'CD' => 400,
            'C' => 100,
            'XC' => 90,
            'L' => 50,
            'XL' => 40,
            'X' => 10,
            'IX' => 9,
            'V' => 5,
            'IV' => 4,
            'I' => 1,
        );

        $result = 0;

        foreach ($romans as $key => $value) {
            while (strpos($roman, $key) === 0) {
                $result += $value;
                $roman = substr($roman, strlen($key));
            }
        }
        return ($result);
    }
}
