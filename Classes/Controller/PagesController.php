<?php
namespace EC\EcPageTranslator\Controller;


/***************************************************************
 *
 *  Copyright notice
 *
 *  (c) 2016 Daniel Noweak <daniel.nowak@econsor.de>, econsor GmbH
 *
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 3 of the License, or
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
 * PagesController
 */
class PagesController extends \TYPO3\CMS\Extbase\Mvc\Controller\ActionController {



    /**
     * pageRepository
     * @var TYPO3\CMS\Frontend\Page\PageRepository
     * @inject
     */
    protected $sysPageRepository;

    /**
     * languageRepository
     * @var TYPO3\CMS\Lang\Domain\Repository\LanguageRepository
     * @inject
     */
    protected $languageRepository;


    /**
     * action list
     *
     * @return void
     */
    public function listAction()
    {
        $pages = $this->sysPageRepository->getRootLine(5);

        $node = $this->objectManager->get('TYPO3\CMS\Backend\Tree\Pagetree\PagetreeNode');
        $dataProvider = $this->objectManager->get('TYPO3\CMS\Backend\Tree\Pagetree\DataProvider', 1 );
        $nodeCollection = $dataProvider->getNodes($node);
        $pageTree = $this->getPageTreeTest($nodeCollection);
        //\TYPO3\CMS\Extbase\Utility\DebuggerUtility::var_dump($pageTree);
        $this->view->assign('pagetree', $pageTree);
    }
    
     /**
     * action listLangs
     *
     * @return void
     */
    public function listLangsAction()
    {
        $res = $GLOBALS['TYPO3_DB']->exec_SELECTquery(
            '*',
            'sys_language',
             'uid != 0',
            'title ASC'
            
        );
        $langs = array() ;
        $lang = array();
        while ($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res)) {
            $lang = array('title' => $row['title'], 'id' => $row['uid'] );
            array_push( $langs, $lang);
        }
        $GLOBALS['TYPO3_DB']->sql_free_result($res);
        $this->view->assign('langs', $langs);
    }
    
    
    /**
     * action insertLangPages
     *
     * @return void
     */
    public function insertLangPagesAction()
    {
        // get chosen language
        $lang =$this->request->getArgument('langs');
        
        
        
        
        // get all pages where l18n not 2
        $res = $GLOBALS['TYPO3_DB']->exec_SELECTquery(
            '*',
            'pages',
            'l18n_cfg = 0',
            'uid DESC'
        );
        while ($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res)) {
        $pageId = $row['uid'];
        $pageTitle = $row['title'];
        $res2 = $GLOBALS['TYPO3_DB']->exec_SELECTquery(
                'uid',
                'pages_language_overlay',
                'pid = '. $pageId .' AND deleted=0 AND sys_language_uid='.$lang,
                'title ASC'
            );
            $hasOverlay = false;
            
            while ($row2 = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res2)) {
                $hasOverlay = true;
            }
            if(!$hasOverlay){
                $GLOBALS['TYPO3_DB']->exec_INSERTquery('pages_language_overlay', array('pid' => $pageId, 'sys_language_uid' => $lang, 'title' => $pageTitle));
            }
            $GLOBALS['TYPO3_DB']->sql_free_result($res2);
        
        }
        $GLOBALS['TYPO3_DB']->sql_free_result($res);
    }
    
    
    
    
    
    
    
    
    


    protected function getPageTreeTest( \TYPO3\CMS\Backend\Tree\TreeNodeCollection $nodeCollection ) {

        foreach ($nodeCollection as $childNode) {
            $pids_data = array(
                'id' => $childNode->getId(),
                'text' => $childNode->getText(),
                'viewable' => $childNode->canBeViewed(),
            );
            //$pids[] = $childNode->toArray();

            if ( $childNode->hasChildNodes() ) {
                $pids_data['subPages'] = $this->getPageTreeTest( $childNode->getChildNodes() );
            }

            $pids[] = $pids_data;
        }

        return $pids;
    }
    
    /**
     * action show
     *
     *
     * @return void
     */
    public function showAction()
    {
        $page =$this->request->getArgument('page');
        $res = $GLOBALS['TYPO3_DB']->exec_SELECTquery(
            'uid',
            'sys_language',
            'pid = 0',
            'title ASC'
        );
        while ($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res)) {
            $res3 = $GLOBALS['TYPO3_DB']->exec_SELECTquery(
                'title',
                'pages',
                'uid = '. $page

            );
            while ($row3 = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res3)) {
                $pageTitle = $row3['title'];
            }





            $res2 = $GLOBALS['TYPO3_DB']->exec_SELECTquery(
                'uid',
                'pages_language_overlay',
                'pid = '. $page .' AND deleted=0 AND sys_language_uid='.$row['uid'],
                'title ASC'
            );
            $hasOverlay = false;
            while ($row2 = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res2)) {
                $hasOverlay = true;
            }
            if(!$hasOverlay){
                $GLOBALS['TYPO3_DB']->exec_INSERTquery('pages_language_overlay', array('pid' => $page, 'sys_language_uid' => $row['uid'], 'title' => $pageTitle));
            }
            $GLOBALS['TYPO3_DB']->sql_free_result($res2);

        }
        $GLOBALS['TYPO3_DB']->sql_free_result($res);
        //$this->view->assign('allLanguages', $allLanguages);
    }

}