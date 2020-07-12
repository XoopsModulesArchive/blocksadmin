<?php
$adminmenu[0]['title'] = _BADMIN_MI_INDEX;
$adminmenu[0]['link'] = "admin/index.php";

global $xoopsUser;
$groupperm_handler = xoops_gethandler('groupperm');
if ($groupperm_handler->checkRight("system_admin", XOOPS_SYSTEM_BLOCK, $xoopsUser->getGroups(), 1)) {
    $adminmenu[1]['title'] = _BADMIN_MI_PERMISSIONS;
    $adminmenu[1]['link'] = "admin/permissions.php";
}
?>
