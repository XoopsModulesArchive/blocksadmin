<?php
// $Id: index.php,v 1.2 2005/08/18 12:19:39 jkp Exp $
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
// Author: Kazumi Ono (AKA onokazu)                                          //
// URL: http://www.myweb.ne.jp/, http://www.xoops.org/, http://jp.xoops.org/ //
// Project: The XOOPS Project                                                //
// ------------------------------------------------------------------------- //
include '../../../include/cp_header.php';
xoops_cp_header();
include_once "blocksadmin.php";
$op = "list";

$xTheme->loadModuleAdminMenu(0);

if ( isset($_REQUEST['op']) && in_array($_REQUEST['op'], array("edit", "delete", "delete_ok", "new", "save", "order") )) {
    $op = $_REQUEST['op'];
    $bid = isset($_REQUEST['bid']) ? intval($_REQUEST['bid']) : 0;
}
$_REQUEST["selmod"] = empty($_REQUEST["selmod"]) ? 0 : intval($_REQUEST["selmod"]);

switch ($op) {
    case "list" :
    list_blocks();
    break;

    case "order" :
    if (!$GLOBALS['xoopsSecurity']->check()) {
        redirect_header("index.php?selmod=".$_REQUEST["selmod"], 3, implode('<br />', $GLOBALS['xoopsSecurity']->getErrors()));
        exit();
    }
    if (order_block($_POST['id'], $_POST['weight'], $_POST['side'], $_POST['module'])) {
        redirect_header("index.php?selmod=".$_REQUEST["selmod"],2,_AM_DBUPDATED);
    }
    redirect_header("index.php?selmod=".$_REQUEST["selmod"], 2, _AM_ERRORDURINGSAVE);
    break;

    case "save" :
    if (!$GLOBALS['xoopsSecurity']->check()) {
        redirect_header("index.php?selmod=".$_REQUEST["selmod"], 3, implode('<br />', $GLOBALS['xoopsSecurity']->getErrors()));
    }
    if (!isset($_REQUEST['instanceid'])) {
        $_REQUEST['instanceid'] = 0;
    }
    $options = isset($_REQUEST['options']) ? $_REQUEST['options'] : array();
    $bmodule = isset($_REQUEST['bmodule']) ? $_REQUEST['bmodule'] : array();
    $groups = isset($_REQUEST['groups']) ? $_REQUEST['groups'] : array();
    $edit_groups = isset($_REQUEST['edit_groups']) ? $_REQUEST['edit_groups'] : array();
    if (save_block($_REQUEST['instanceid'], $_REQUEST['bside'], $_REQUEST['bweight'], $_REQUEST['btitle'], $_REQUEST['bid'], $_REQUEST['bcachetime'], $bmodule, $groups, $options, $edit_groups)) {
        redirect_header("index.php?selmod=".$_REQUEST["selmod"], 2, _AM_DBUPDATED);
    }
    redirect_header("index.php?selmod=".$_REQUEST["selmod"], 2, _AM_ERRORDURINGSAVE);
    break;

    case "delete_ok" :
    if (!$GLOBALS['xoopsSecurity']->check()) {
        redirect_header("index.php?selmod=".$_REQUEST["selmod"], 3, implode('<br />', $GLOBALS['xoopsSecurity']->getErrors()));
    }
    delete_block_ok($_REQUEST['bid']);
    break;

    case "delete" :

    delete_block($_REQUEST['id']);
    break;

    case "edit" :
    edit_block($_REQUEST['id']);
    break;

    case 'new' :
    instantiate_block($_REQUEST['bid']);
    break;
}
xoops_cp_footer();
?>