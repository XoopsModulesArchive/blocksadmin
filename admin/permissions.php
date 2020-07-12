<?php
// $Id: permissions.php,v 1.1 2005/08/18 12:19:39 jkp Exp $
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
// Author: XOOPS Foundation                                                  //
// URL: http://www.xoops.org/                                                //
// Project: The XOOPS Project                                                //
// ------------------------------------------------------------------------- //
include '../../../include/cp_header.php';
xoops_cp_header();

require_once("blocksadmin.php");
if (!isBlocksAdmin()) {
    redirect_header('index.php', 2, _NOPERM);
}

$xTheme->loadModuleAdminMenu(1, _BADMIN_MI_PERMISSIONS);

include_once XOOPS_ROOT_PATH."/class/xoopsformloader.php";

$title_of_form = _BADMIN_AM_INSTANCES;
$perm_name = "block_edit";

$module_id = $xoopsModule->getVar('mid');
$perm_desc = "";

include_once XOOPS_ROOT_PATH . '/class/xoopsform/grouppermform.php';
$form = new XoopsGroupPermForm($title_of_form, $module_id, $perm_name, $perm_desc, 'admin/permissions.php', false);

$instance_handler =& xoops_gethandler('blockinstance');
$instances = $instance_handler->getObjects();

foreach (array_keys($instances) as $i) {
    $id = $instances[$i]->getVar('instanceid');
    if ($instances[$i]->getVar('title') != "") {
        $blocks[$id] = $instances[$i]->getVar('title');
    }
    else {
        $block_names_to_find[] = $instances[$i]->getVar('bid');
        $blockids[$instances[$i]->getVar('bid')] = $id;
    }
}
if (count($block_names_to_find) > 0) {
    $block_handler = xoops_gethandler('block');
    $foundblocks = $block_handler->getList(new Criteria('bid', "(".implode(',', $block_names_to_find).")", "IN"));
    if (count($foundblocks) > 0) {
        foreach ($foundblocks as $bid => $title) {
            $id = $blockids[$bid];
            $blocks[$id] = $title;
        }
    }
}
asort($blocks);

foreach ($blocks as $id => $title) {
    $form->addItem($id, $title." (".$id.")");
}
$form->display();
xoops_cp_footer();
?>