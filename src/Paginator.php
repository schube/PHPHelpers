<?php

namespace Schubec\PHPHelpers;

/**
 * Diese Klasse erlaubt das Blättern in Ergebnislisten
 *
 * @copyright Copyright (c) 2007-2019, schubec
 * @version 1.0
 * @author Bernhard Schulz <bernhard.schulz@schubec.com>
 */
class Paginator {
	private $currentOffset = 0;
	private $recordsFound = 0;
	private $groupSize = 10;
	
	/**
	 * Gibt die Anzahl der gefundenen Datensätze zurück
	 *
	 * @return int Anzahl der gefundenen Datensätze
	 */
	public function getFoundCount() {
		return $this->recordsFound;
	}
	
	/**
	 * Gibt das aktuelle Offset zurück, also wie viele Datensätze ab dem Start ausgelassen wurden
	 *
	 * @return int Aktuelles Offset
	 */
	public function getCurrentOffset() {
		return $this->currentOffset;
	}
	
	/**
	 * Gibt einen Link zurück, der die Sortierparameter neu setzt aber andere Parameter nicht ändert
	 *
	 * @param
	 *        	$sortfield
	 * @param
	 *        	$sortorder
	 * @return string Link
	 */
	function getSortLink($sortfield, $sortorder = "ascend") {
		$dummy = $_SERVER ['QUERY_STRING'];
		// Sortfield rausnehmen
		$dummy = preg_replace ( "/sortfield=\w*?/", "", $dummy );
		// Sortorder rausnehmen
		$dummy = preg_replace ( "/sortorder=\w*?/", "", $dummy );
		$link = $_SERVER ['SCRIPT_NAME'] . "?" . $dummy . "&sortfield=" . $sortfield . "&sortorder=" . $sortorder;
		return $link;
	}
	
	/**
	 * Konstruktur
	 *
	 * @param $currentOffset Aktuelles
	 *        	Offset
	 * @param $recordsFound Anzahl
	 *        	an gefundenen Datensätze insgesamt
	 * @param $groupSize Anzahl
	 *        	maximale Datensätze pro Seite
	 */
	function __construct($currentOffset, $recordsFound, $groupSize) {
		$this->currentOffset = $currentOffset;
		$this->recordsFound = $recordsFound;
		$this->groupSize = $groupSize;
	}
	
	/**
	 * Gibt einen HTML Link zur ersten Seite zurück
	 *
	 * @return string Link
	 */
	function getLinkFirst() {
		$link_first = $_SERVER ['SCRIPT_NAME'] . "?" . preg_replace ( "/offset=\d*&?/", "", $_SERVER ['QUERY_STRING'] );
		if ($this->currentOffset == 0)
			return NULL;
			else
				return $link_first;
	}
	
	/**
	 * Gibt einen HTML Link zur letzten Seite zurück
	 *
	 * @return string Link
	 */
	function getLinkLast() {
		$offset = $this->recordsFound - $this->groupSize;
		$link_last = $_SERVER ['SCRIPT_NAME'] . "?offset=" . $offset . "&" . preg_replace ( "/offset=\d*&?/", "", $_SERVER ['QUERY_STRING'] );
		if ($this->currentOffset >= $offset)
			return NULL;
			else
				return $link_last;
	}
	
	/**
	 * Gibt einen HTML Link zur nächsten Seite zurück
	 *
	 * @return string Link
	 */
	function getLinkNext() {
		$offset = $this->currentOffset + $this->groupSize;
		$link_last = $_SERVER ['SCRIPT_NAME'] . "?offset=" . $offset . "&" . preg_replace ( "/offset=\d*&?/", "", $_SERVER ['QUERY_STRING'] );
		
		if ($this->recordsFound <= $offset)
			return NULL;
			else
				return $link_last;
	}
	
	/**
	 * Gibt einen HTML Link zur vorherigen Seite zurück
	 *
	 * @return string Link
	 */
	function getLinkPrevious() {
		$offset = $this->currentOffset - $this->groupSize;
		if ($offset < 0) {
			$offset = 0;
		}
		$link_last = $_SERVER ['SCRIPT_NAME'] . "?offset=" . $offset . "&" . preg_replace ( "/offset=\d*&?/", "", $_SERVER ['QUERY_STRING'] );
		
		if ($this->currentOffset == 0)
			return NULL;
			else
				return $link_last;
	}
	
	/**
	 * Gibt ein Array mit Links zum Blättern im Suchergebnis zurück
	 *
	 * @param $max_number_of_links Maximale
	 *        	Anzahl an Links
	 * @return array Links zu den einzelnen Seiten
	 */
	function getListNavigation($max_number_of_links = 10) {
		$current_page = round ( ($this->currentOffset + $this->groupSize) / $this->groupSize ) - 1;
		
		if (($current_page - ($max_number_of_links / 2)) >= 0)
			$start_page = $current_page - floor ( $max_number_of_links / 2 );
			else
				$start_page = 0;
				
				if ($start_page + $max_number_of_links <= $this->recordsFound / $this->groupSize) {
					$end_page = $start_page + $max_number_of_links;
				} else {
					$end_page = ceil ( $this->recordsFound / $this->groupSize );
					while ( $start_page > 1 and ($end_page - $start_page < $max_number_of_links) ) {
						$start_page --;
					}
				}
				$result = array ();
				for($i = $start_page; $i < $end_page; $i ++) {
					$offset = $i * $this->groupSize;
					$link = $_SERVER ['SCRIPT_NAME'] . "?offset=" . $offset . "&" . preg_replace ( "/offset=\d*&?/", "", $_SERVER ['QUERY_STRING'] );
					
					if ($current_page != $i)
						$result [] = array (
								'link' => $link,
								'page' => $i + 1,
								'current_page' => false
						);
						else
							$result [] = array (
									'link' => $link,
									'page' => $i + 1,
									'current_page' => true
							);
				}
				return $result;
	}
	
	/**
	 * Gibt einen HTML Link zur ersten Seite zurück
	 *
	 * @param
	 *        	$sperator
	 * @return string Anzeige der aufgerufenen Datensätze
	 */
	function getRecordRange($seperator = " - ") {
		if ($this->recordsFound == 0) {
			return "0" . $seperator . "0";
		}
		if ($this->groupSize == 0) {
			$returntext = "";
		} else {
			
			if ($this->recordsFound / $this->groupSize < 0) {
				// less than 1 page, show numbers
				$beginrec = 1;
				$endrec = $this->recordsFound;
			} else {
				// more than 1 page, see if last or not
				if ($this->currentOffset + $this->groupSize > $this->recordsFound) {
					// last page
					$beginrec = $this->currentOffset + 1;
					$endrec = $this->recordsFound;
				} else {
					// count them
					$beginrec = $this->currentOffset + 1;
					$endrec = $this->currentOffset + $this->groupSize;
				}
			}
			$returntext = $beginrec . $seperator . $endrec;
			return $returntext;
		}
	}
}