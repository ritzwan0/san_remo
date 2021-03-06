<?php
/**
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 *
 * This package designed for Magento COMMUNITY edition
 * PSS Digital does not guarantee correct work of this extension
 * on any other Magento edition except Magento COMMUNITY edition.
 * PSS Digital does not provide extension support in case of * incorrect edition usage.
 *
 * @author PSS Digital Team
 * @category PSS
 * @package PSS_WordPress
 * @copyright Copyright (c) 2018 PSS (https://www.pss-ti.com)
 * @license http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 */
namespace PSS\WordPress\Model\Integration;

/* Constructor Args */
use PSS\WordPress\Model\Theme;

/* Misc */
use PSS\WordPress\Model\Integration\IntegrationException;

class ThemeTest
{
	/*
	 *
	 *
	 */
	protected $theme;

	/*
	 *
	 *
	 */
	public function __construct(Theme $theme)
	{
		$this->theme = $theme;
	}
	
	/*
	 *
	 *
	 */
	public function runTest()
	{
		if (!$this->theme->isThemeIntegrated()) {
			return $this;
		}
		
		$this->theme->validate();
		
		return $this;
	}
}