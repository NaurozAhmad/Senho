<!-- Begin Main Menu -->
<?php $RootMenu = new cMenu(EW_MENUBAR_ID) ?>
<?php

// Generate all menu items
$RootMenu->IsRoot = TRUE;
$RootMenu->AddMenuItem(1, "mi_projects", $Language->MenuPhrase("1", "MenuText"), "projectslist.php", -1, "", TRUE, FALSE);
$RootMenu->Render();
?>
<!-- End Main Menu -->
