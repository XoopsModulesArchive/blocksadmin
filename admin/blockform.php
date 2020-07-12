<?php
// $Id: blockform.php,v 1.1 2005/08/18 08:29:00 jkp Exp $
//  ------------------------------------------------------------------------ //
//                XOOPS - PHP Content Management System                      //
//                    Copyright (c) 2000 XOOPS.org                           //
//                       <http://www.xoops.org/>                             //
//  ------------------------------------------------------------------------ //
//  This program is free software; you can redistribute it and/or modify     //
//  it under the terms of the GNU General Public License as published by     //
//  the Free Software Foundation; either version 2 of the License, or        //
//  (at your option) any later version.                                      //
//                                                                           //
//  You may not change or alter any portion of this comment or credits       //
//  of supporting developers from this source code or any supporting         //
//  source code which is considered copyrighted (c) material of the          //
//  original comment or credit authors.                                      //
//                                                                           //
//  This program is distributed in the hope that it will be useful,          //
//  but WITHOUT ANY WARRANTY; without even the implied warranty of           //
//  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the            //
//  GNU General Public License for more details.                             //
//                                                                           //
//  You should have received a copy of the GNU General Public License        //
//  along with this program; if not, write to the Free Software              //
//  Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307 USA //
//  ------------------------------------------------------------------------ //
function getInstanceForm(&$instance, $title, $op) {
    include_once XOOPS_ROOT_PATH."/class/xoopsformloader.php";
    $form = new XoopsThemeForm($title, 'blockform', 'index.php', "post", true);
    $form->addElement(new XoopsFormLabel(_AM_NAME, $instance->block->getVar('name')));

    $side_select = new XoopsFormSelect(_AM_BLKTYPE, "bside", $instance->getVar('side'));
    $side_select->addOptionArray(array(0 => _AM_SBLEFT, 1 => _AM_SBRIGHT, 3 => _AM_CBLEFT, 4 => _AM_CBRIGHT, 5 => _AM_CBCENTER));
    $form->addElement($side_select);
    
    $form->addElement(new XoopsFormText(_AM_WEIGHT, "bweight", 2, 5, $instance->getVar('weight')));
    $form->addElement(new XoopsFormRadioYN(_AM_VISIBLE, 'bvisible', $instance->getVar('visible')));
    
    $mod_select = new XoopsFormSelect(_AM_VISIBLEIN, "bmodule", $instance->getVisibleIn(), 10, true);
    $module_handler =& xoops_gethandler('module');
    //Get modules and pages for visible in
    $mod_select->addOption(_AM_SYSTEMLEVEL, "", true);
    $mod_select->addOption("0-2", " - "._AM_ADMINBLOCK);
    $mod_select->addOption("0-1", " - "._AM_TOPPAGE);
    $mod_select->addOption("0-0", " - "._AM_ALLPAGES);

    $criteria = new CriteriaCompo(new Criteria('hasmain', 1));
    $criteria->add(new Criteria('isactive', 1));
    $module_main =& $module_handler->getObjects($criteria, true, true);
    if (count($module_main) > 0) {
        foreach (array_keys($module_main) as $mid) {
            $mod_select->addOption($module_main[$mid]->getVar('name'), "", true);
            $mod_select->addOption($mid."-0", " - "._AM_ALLMODULEPAGES);
            $pages = $module_main[$mid]->getInfo("pages");
            if ($pages == false) {
                $pages = $module_main[$mid]->getInfo("sub");
            }
            if (is_array($pages) && $pages != array()) {
                foreach ($pages as $id => $pageinfo) {
                    $mod_select->addOption($mid."-".$id, " - ".$pageinfo['name']);
                }
            }
        }
    }
    $form->addElement($mod_select);
    
    $title = !$instance->isNew() ? $instance->getVar('title') : $instance->block->getVar('name');
    $form->addElement(new XoopsFormText(_AM_TITLE, 'btitle', 50, 255, $title), false);
    if ($instance->block->getVar('template') != '') {
        $tplfile_handler =& xoops_gethandler('tplfile');
        $btemplate =& $tplfile_handler->find($GLOBALS['xoopsConfig']['template_set'], 'block', $instance->block->getVar('bid'));
        if (count($btemplate) > 0) {
            $form->addElement(new XoopsFormLabel(_AM_CONTENT, '<a href="'.XOOPS_URL.'/modules/system/admin.php?fct=tplsets&op=edittpl&id='.$btemplate[0]->getVar('tpl_id').'">'._AM_EDITTPL.'</a>'));
        } else {
            $btemplate2 =& $tplfile_handler->find('default', 'block', $instance->block->getVar('bid'));
            if (count($btemplate2) > 0) {
                $form->addElement(new XoopsFormLabel(_AM_CONTENT, '<a href="'.XOOPS_URL.'/modules/system/admin.php?fct=tplsets&op=edittpl&id='.$btemplate2[0]->getVar('tpl_id').'" target="_blank">'._AM_EDITTPL.'</a>'));
            }
        }
    }
    if ($instance->isNew()) {
        $editelements = $instance->block->getOptions();
    }
    else {
        $editelements = $instance->getOptions();
    }
    if ($editelements != false) {
        $form->addElement(new XoopsFormLabel(_AM_OPTIONS, $editelements));
    }
    
    $cache_select = new XoopsFormSelect(_AM_BCACHETIME, 'bcachetime', $instance->getVar('bcachetime'));
    $cache_select->addOptionArray(array('0' => _NOCACHE, 
                                        '30' => sprintf(_SECONDS, 30), 
                                        '60' => _MINUTE, 
                                        '300' => sprintf(_MINUTES, 5), 
                                        '1800' => sprintf(_MINUTES, 30), 
                                        '3600' => _HOUR, 
                                        '18000' => sprintf(_HOURS, 5), 
                                        '86400' => _DAY, 
                                        '259200' => sprintf(_DAYS, 3), 
                                        '604800' => _WEEK, 
                                        '2592000' => _MONTH));
    $form->addElement($cache_select);
    
    $form->addElement(new XoopsFormSelectGroup(_AM_VISIBLETOGROUPS, 'groups', true, $instance->getVisibleGroups(), 5, true));
    
    if (isBlocksAdmin()) {
        global $xoopsModule;
        $groupperm_handler =& xoops_gethandler('groupperm');
        if ($instance->isNew()) {
            $groups_with_edit = array();
        }
        else {
            $groups_with_edit =& $groupperm_handler->getGroupIds("block_edit", $instance->getVar('instanceid'), $xoopsModule->getVar('mid'));
        }
        $form->addElement(new XoopsFormSelectGroup(_AM_EDITABLEBYGROUPS, 'edit_groups', false, $groups_with_edit, 5, true));
    }
    
    if ($instance->getVar('instanceid') > 0) {
        $form->addElement(new XoopsFormHidden('instanceid', $instance->getVar('instanceid')));
    }
    $form->addElement(new XoopsFormHidden('bid', $instance->block->getVar('bid')));
    $form->addElement(new XoopsFormHidden('op', $op));
    $button_tray = new XoopsFormElementTray('', '&nbsp;');

    $button_tray->addElement(new XoopsFormButton('', 'submitblock', _SUBMIT, "submit"));
    $form->addElement($button_tray);
    return $form;
}
?>