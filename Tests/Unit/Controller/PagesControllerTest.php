<?php
namespace EC\EcPageTranslator\Tests\Unit\Controller;
/***************************************************************
 *  Copyright notice
 *
 *  (c) 2016 Daniel Noweak <daniel.nowak@econsor.de>, econsor GmbH
 *  			
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 2 of the License, or
 *  (at your option) any later version.
 *
 *  The GNU General Public License can be found at
 *  http://www.gnu.org/copyleft/gpl.html.
 *
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/

/**
 * Test case for class EC\EcPageTranslator\Controller\PagesController.
 *
 * @author Daniel Noweak <daniel.nowak@econsor.de>
 */
class PagesControllerTest extends \TYPO3\CMS\Core\Tests\UnitTestCase
{

	/**
	 * @var \EC\EcPageTranslator\Controller\PagesController
	 */
	protected $subject = NULL;

	public function setUp()
	{
		$this->subject = $this->getMock('EC\\EcPageTranslator\\Controller\\PagesController', array('redirect', 'forward', 'addFlashMessage'), array(), '', FALSE);
	}

	public function tearDown()
	{
		unset($this->subject);
	}

	/**
	 * @test
	 */
	public function listActionFetchesAllPagessFromRepositoryAndAssignsThemToView()
	{

		$allPagess = $this->getMock('TYPO3\\CMS\\Extbase\\Persistence\\ObjectStorage', array(), array(), '', FALSE);

		$pagesRepository = $this->getMock('', array('findAll'), array(), '', FALSE);
		$pagesRepository->expects($this->once())->method('findAll')->will($this->returnValue($allPagess));
		$this->inject($this->subject, 'pagesRepository', $pagesRepository);

		$view = $this->getMock('TYPO3\\CMS\\Extbase\\Mvc\\View\\ViewInterface');
		$view->expects($this->once())->method('assign')->with('pagess', $allPagess);
		$this->inject($this->subject, 'view', $view);

		$this->subject->listAction();
	}

	/**
	 * @test
	 */
	public function showActionAssignsTheGivenPagesToView()
	{
		$pages = new \EC\EcPageTranslator\Domain\Model\Pages();

		$view = $this->getMock('TYPO3\\CMS\\Extbase\\Mvc\\View\\ViewInterface');
		$this->inject($this->subject, 'view', $view);
		$view->expects($this->once())->method('assign')->with('pages', $pages);

		$this->subject->showAction($pages);
	}
}
