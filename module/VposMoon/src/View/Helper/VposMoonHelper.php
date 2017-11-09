<?php

namespace VposMoon\View\Helper;

use Zend\View\Helper\AbstractHelper;

class VposMoonHelper extends AbstractHelper {

	private $eveDataManager;
	private $moonManager;

	/**
	 * Constructor.
	 * @param array $items Menu items.
	 */
	public function __construct($eveDataManager, $moonManager)
	{
		$this->eveDataManager = $eveDataManager;
		$this->moonManager = $moonManager;
	}

	/**
	 * Takes 'goo' string from MoonController Index action and renders the output
	 * 
	 * @param string $input
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
	 * @param string $input
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
	 * @param string $input
	 * @return string rendered HTML
	 */
	public function renderMoonMateriallist($input)
	{
		$res = '';
		$val = 0;

		$data = $this->calculateMoonMateriallist($input);


		// second step, create view
		if (!empty($data)) {
			foreach ($data as $k => $row) {
				if (!empty($row) && $k != 'val') {
					$res .= '<span class="gooval">' . round(((float) $row['qty'] * 100), 0) . '</span>&nbsp;';
					$res .= '<span class="gooname">' . $row['name'] . '</span>&nbsp;';
					$res .= '<span class="gooprice">' . number_format(floatval($row['worth']), 0) . '</span><br />';
				}
			}
		}

		return ($res);
	}

	public function renderMoonMaterialValue($input)
	{
		$res = '';

		$data = $this->calculateMoonMateriallist($input);

		if (!empty($data)) {
			$res = $data['val'];
		}

		return($res);
	}

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
	 * @param array $system_data
	 * @return string
	 */
	public function createNeighboursNavigation($system_data)
	{
		$text = '';

		$neighbour_list = $this->createNeighboursNavigationList($system_data);

		if (!empty($neighbour_list['constellation_current'])) {
			$id = key($neighbour_list['constellation_current']);
			$text .= '<a href="/vposmoon?system=' . $id . '" class="btn btn-constellation btn-xs sytemswitch" data-id="'.$id.'">' . current($neighbour_list['constellation_current']) . '</a>';
		}

		if (!empty($neighbour_list['system'])) {
			foreach ($neighbour_list['system'] as $id => $name) {
				$text .= '<a href="/vposmoon?system=' . $id . '" class="btn btn-system btn-xs sytemswitch" data-id="'.$id.'">' . $name . '</a>';
			}
		}

		if (!empty($neighbour_list['constellation'])) {
			foreach ($neighbour_list['constellation'] as $id => $name) {
				$text .= '<a href="/vposmoon?system=' . $id . '" class="btn btn-constellation btn-xs sytemswitch" data-id="'.$id.'">' . $name . '</a>';
			}
		}

		return($text);
	}

	/**
	 * Calculates neighbour systems and constellations.
	 * If $system_data is about a constellation, a list of systems in the constellation is created as well. 
	 * 
	 * @param array $system_data
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
					$neighbour_list['constellation'][$system['mdc_id']] = $system['mdc_name'];
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
					$neighbour_list['constellation'][$system['mdc_id']] = $system['mdc_name'];
				}
			}
		}

		return($neighbour_list);
	}

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
			$res .= '<option '.(intval($selected_id)==intval($option['id']) ? 'selected' : '').' value="'.$option['id'].'">'.$option['name'].' ('.$option['cnt'].')</option>';
		}
		return($res);
	}


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
			$res .= '<option '.(intval($selected_id) == intval($option['id']) ? 'selected' : '').' value="'.$option['id'].'">'.$option['name'].' ('.$option['cnt'].')</option>';
		}
		return($res);
	}

	/**
	 * Convert moon names to shortform "PxMy"
	 * The shortform is better suited for filtering.
	 * 
	 * Example:
	 * "Zaid VIII - Moon 2" becomes "P8M2"
	 * 
	 * @param string $moonname
	 * @return string
	 */
	public function convertMoonToShortform($moonname)
	{

		$regex = '/^[\w-]+\s([IXV]+)\s-\sMoon\s(\d+)$/';
		preg_match($regex, $moonname, $matches);

		if (!empty($matches) && !empty($matches['1']) && !empty($matches['2'])) {
			return('P' . $this->romansToNumber($matches['1']) . 'M' . $matches['2']);
		}

		return('');
	}

	/**
	 * Convert roman number to decimal
	 * 
	 * @param string $roman
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
		return($result);
	}

}
