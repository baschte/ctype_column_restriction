<?php
namespace Baschte\CtypeColumnRestriction\Hooks;

use TYPO3\CMS\Backend\Utility\BackendUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Backend\Wizard\NewContentElementWizardHookInterface;

/**
 * Class/Function which manipulates the rendering of items within the new content element wizard
 *
 * @package Hochschule Bochum
 */
class WizardItems implements NewContentElementWizardHookInterface
{
	/**
	 * @var string
	 */
	protected $pageID;

	/**
	 * @var string
	 */
	protected $colPos;

	/**
	 * @var array|boolean
	 */
	protected $pageInfo;

	/**
	 * @var string
	 */
	protected $backendLayout;

	/**
	 * @var array BackendLayout Configuration
	 */
	protected $backendLayoutConfigArray;

	/**
	 * @var array Page TSconfig
	 */
	protected $pageTSConfig;


	/**
	 * Modifies WizardItems array
	 *
	 * @param array $wizardItems Array of Wizard Items
	 * @param \TYPO3\CMS\Backend\Controller\ContentElement\NewContentElementController $parentObject Parent object New Content element wizard
	 */
	public function manipulateWizardItems(&$wizardItems, &$parentObject) {
		// global definitions
		$this->pageID = $parentObject->id;
		$this->colPos = $parentObject->colPos;

		// check if page has BE Layout definition
		$this->backendLayout = $this->getBackendLayoutFromPageUid($this->pageID);

		// if no backend_layout is found => return and allow all
		if(!isset($this->backendLayout)) {
			return;
		}

		// get pageTSConfig
		$this->pageTSConfig = BackendUtility::getPagesTSconfig($this->pageID);

		// check if settings of backend_layout are found, if not => return
		$this->backendLayoutConfigArray = $this->pageTSConfig['mod.']['web_layout.']['BackendLayouts.'][$this->backendLayout . '.']['config.']['backend_layout.']['rows.'];

		if(!is_array($this->backendLayoutConfigArray)) {
			return;
		}


		// remove disallowed CTypes from $wizardItems array
		$this->removeDisallowedWizardItems($this->getAllowedCTypesForColPos($this->colPos), $wizardItems);

		// remove empty headers from array
		$this->removeEmptyHeadersFromWizard($wizardItems);
	}


	/**
	 * remove disallowed content elements from wizard items
	 *
	 * @param array $allowed
	 * @param array $wizardItems
	 */
	public function removeDisallowedWizardItems(array $allowed, array &$wizardItems)
	{
		if (!isset($allowed['*'])) {
			foreach ($wizardItems as $key => $wizardItem) {
				if (!$wizardItems[$key]['header']) {
					if (!empty($allowed) && !isset($allowed[$wizardItems[$key]['tt_content_defValues']['CType']])) {
						unset($wizardItems[$key]);
					}
				}
			}
		}
	}


	/**
	 * remove unnecessary headers from wizard items
	 *
	 * @param array $wizardItems
	 */
	public function removeEmptyHeadersFromWizard(array &$wizardItems)
	{
		$headersWithElements = [];
		foreach ($wizardItems as $key => $wizardItem) {
			$isElement = strpos($key, '_', 1);
			if ($isElement) {
				$headersWithElements[substr($key, 0, $isElement)] = true;
			}
		}
		foreach ($wizardItems as $key => $wizardItem) {
			if ($wizardItems[$key]['header']) {
				if (!isset($headersWithElements[$key])) {
					unset($wizardItems[$key]);
				}
			}
		}
	}


	/**
	 * get backend_layout from given pageUID
	 *
	 * @param string $pageUid
	 * @return string
	 */
	private function getBackendLayoutFromPageUid($pageUid)
	{
		$objectManager = GeneralUtility::makeInstance('TYPO3\\CMS\\Extbase\\Object\\ObjectManager');
		$pageRepository = $objectManager->get('TYPO3\\CMS\\Frontend\\Page\\PageRepository');
		$page = $pageRepository->getPage($pageUid);

		// check if backendlayout is set
		if($page['backend_layout'] !== '')
		{
			return $this->getBackendLayoutKeyFromString($page['backend_layout']);
		}
		// else check one level up
		else {
			// get rootline of current page
			$pageRootLine = $pageRepository->getRootLine($this->pageID);

			// remove current page out of rootline
			unset($pageRootLine[count($pageRootLine) - 1]);

			// iterate if backendlayout if found
			foreach ($pageRootLine as $page) {
				if ($page['backend_layout_next_level'] !== '' && $page['backend_layout_next_level'] !== '0' && $page['backend_layout_next_level'] !== '-1') {
					return $this->getBackendLayoutKeyFromString($page['backend_layout_next_level']);
				}
			}

			return null;
		}

	}

	/**
	 * get allowed CTypes from given colPos
	 *
	 * @param $colPos string ColPos
	 * @return array
	 */
	private function getAllowedCTypesForColPos($colPos) {
		// loop through backend_layout definitions and found col with given colpos
		foreach ($this->backendLayoutConfigArray as $backendLayoutRow) {
			if (is_array($backendLayoutRow['columns.']) && !empty($backendLayoutRow['columns.'])) {
				foreach ($backendLayoutRow['columns.'] as $backendLayoutColumns) {

					// if colPos is set to backend_layout configuration AND given $colPos === $backendLayoutColumns['colPos'] => return array
					if(isset($backendLayoutColumns['colPos']) && $backendLayoutColumns['colPos'] !== '' && $colPos === intval($backendLayoutColumns['colPos'])) {

						// check if allowed is defined in specified column if not allow all CTypes => *
						if(isset($backendLayoutColumns['allowed']) && $backendLayoutColumns['allowed'] !== '') {
							return array_flip(GeneralUtility::trimExplode(',', $backendLayoutColumns['allowed']));
						} else {
							return array_flip(['*']);
						}

					}
				}
			}
		}
	}


	/**
	 * helper function to get blank backend_layout key
	 *
	 * @return string
	 */
	private function getBackendLayoutKeyFromString($backendLayout) {
		return str_replace('pagets__', '', $backendLayout);
	}
}
