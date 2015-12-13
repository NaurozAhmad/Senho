<?php

// Create page object
if (!isset($images_grid)) $images_grid = new cimages_grid();

// Page init
$images_grid->Page_Init();

// Page main
$images_grid->Page_Main();

// Global Page Rendering event (in userfn*.php)
Page_Rendering();

// Page Rendering event
$images_grid->Page_Render();
?>
<?php if ($images->Export == "") { ?>
<script type="text/javascript">

// Form object
var fimagesgrid = new ew_Form("fimagesgrid", "grid");
fimagesgrid.FormKeyCountName = '<?php echo $images_grid->FormKeyCountName ?>';

// Validate form
fimagesgrid.Validate = function() {
	if (!this.ValidateRequired)
		return true; // Ignore validation
	var $ = jQuery, fobj = this.GetForm(), $fobj = $(fobj);
	if ($fobj.find("#a_confirm").val() == "F")
		return true;
	var elm, felm, uelm, addcnt = 0;
	var $k = $fobj.find("#" + this.FormKeyCountName); // Get key_count
	var rowcnt = ($k[0]) ? parseInt($k.val(), 10) : 1;
	var startcnt = (rowcnt == 0) ? 0 : 1; // Check rowcnt == 0 => Inline-Add
	var gridinsert = $fobj.find("#a_list").val() == "gridinsert";
	for (var i = startcnt; i <= rowcnt; i++) {
		var infix = ($k[0]) ? String(i) : "";
		$fobj.data("rowindex", infix);
		var checkrow = (gridinsert) ? !this.EmptyRow(infix) : true;
		if (checkrow) {
			addcnt++;
			felm = this.GetElements("x" + infix + "_image_name");
			elm = this.GetElements("fn_x" + infix + "_image_name");
			if (felm && elm && !ew_HasValue(elm))
				return this.OnError(felm, "<?php echo ew_JsEncode2(str_replace("%s", $images->image_name->FldCaption(), $images->image_name->ReqErrMsg)) ?>");
			elm = this.GetElements("x" + infix + "_image_detail");
			if (elm && !ew_IsHidden(elm) && !ew_HasValue(elm))
				return this.OnError(elm, "<?php echo ew_JsEncode2(str_replace("%s", $images->image_detail->FldCaption(), $images->image_detail->ReqErrMsg)) ?>");

			// Fire Form_CustomValidate event
			if (!this.Form_CustomValidate(fobj))
				return false;
		} // End Grid Add checking
	}
	return true;
}

// Check empty row
fimagesgrid.EmptyRow = function(infix) {
	var fobj = this.Form;
	if (ew_ValueChanged(fobj, infix, "image_name", false)) return false;
	if (ew_ValueChanged(fobj, infix, "image_detail", false)) return false;
	return true;
}

// Form_CustomValidate event
fimagesgrid.Form_CustomValidate = 
 function(fobj) { // DO NOT CHANGE THIS LINE!

 	// Your custom validation code here, return false if invalid. 
 	return true;
 }

// Use JavaScript validation or not
<?php if (EW_CLIENT_VALIDATE) { ?>
fimagesgrid.ValidateRequired = true;
<?php } else { ?>
fimagesgrid.ValidateRequired = false; 
<?php } ?>

// Dynamic selection lists
// Form object for search

</script>
<?php } ?>
<?php
if ($images->CurrentAction == "gridadd") {
	if ($images->CurrentMode == "copy") {
		$bSelectLimit = $images_grid->UseSelectLimit;
		if ($bSelectLimit) {
			$images_grid->TotalRecs = $images->SelectRecordCount();
			$images_grid->Recordset = $images_grid->LoadRecordset($images_grid->StartRec-1, $images_grid->DisplayRecs);
		} else {
			if ($images_grid->Recordset = $images_grid->LoadRecordset())
				$images_grid->TotalRecs = $images_grid->Recordset->RecordCount();
		}
		$images_grid->StartRec = 1;
		$images_grid->DisplayRecs = $images_grid->TotalRecs;
	} else {
		$images->CurrentFilter = "0=1";
		$images_grid->StartRec = 1;
		$images_grid->DisplayRecs = $images->GridAddRowCount;
	}
	$images_grid->TotalRecs = $images_grid->DisplayRecs;
	$images_grid->StopRec = $images_grid->DisplayRecs;
} else {
	$bSelectLimit = $images_grid->UseSelectLimit;
	if ($bSelectLimit) {
		if ($images_grid->TotalRecs <= 0)
			$images_grid->TotalRecs = $images->SelectRecordCount();
	} else {
		if (!$images_grid->Recordset && ($images_grid->Recordset = $images_grid->LoadRecordset()))
			$images_grid->TotalRecs = $images_grid->Recordset->RecordCount();
	}
	$images_grid->StartRec = 1;
	$images_grid->DisplayRecs = $images_grid->TotalRecs; // Display all records
	if ($bSelectLimit)
		$images_grid->Recordset = $images_grid->LoadRecordset($images_grid->StartRec-1, $images_grid->DisplayRecs);

	// Set no record found message
	if ($images->CurrentAction == "" && $images_grid->TotalRecs == 0) {
		if ($images_grid->SearchWhere == "0=101")
			$images_grid->setWarningMessage($Language->Phrase("EnterSearchCriteria"));
		else
			$images_grid->setWarningMessage($Language->Phrase("NoRecord"));
	}
}
$images_grid->RenderOtherOptions();
?>
<?php $images_grid->ShowPageHeader(); ?>
<?php
$images_grid->ShowMessage();
?>
<?php if ($images_grid->TotalRecs > 0 || $images->CurrentAction <> "") { ?>
<div class="panel panel-default ewGrid">
<div id="fimagesgrid" class="ewForm form-inline">
<div id="gmp_images" class="<?php if (ew_IsResponsiveLayout()) { echo "table-responsive "; } ?>ewGridMiddlePanel">
<table id="tbl_imagesgrid" class="table ewTable">
<?php echo $images->TableCustomInnerHtml ?>
<thead><!-- Table header -->
	<tr class="ewTableHeader">
<?php

// Header row
$images_grid->RowType = EW_ROWTYPE_HEADER;

// Render list options
$images_grid->RenderListOptions();

// Render list options (header, left)
$images_grid->ListOptions->Render("header", "left");
?>
<?php if ($images->image_name->Visible) { // image_name ?>
	<?php if ($images->SortUrl($images->image_name) == "") { ?>
		<th data-name="image_name"><div id="elh_images_image_name" class="images_image_name"><div class="ewTableHeaderCaption"><?php echo $images->image_name->FldCaption() ?></div></div></th>
	<?php } else { ?>
		<th data-name="image_name"><div><div id="elh_images_image_name" class="images_image_name">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $images->image_name->FldCaption() ?></span><span class="ewTableHeaderSort"><?php if ($images->image_name->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($images->image_name->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
        </div></div></th>
	<?php } ?>
<?php } ?>		
<?php if ($images->image_detail->Visible) { // image_detail ?>
	<?php if ($images->SortUrl($images->image_detail) == "") { ?>
		<th data-name="image_detail"><div id="elh_images_image_detail" class="images_image_detail"><div class="ewTableHeaderCaption"><?php echo $images->image_detail->FldCaption() ?></div></div></th>
	<?php } else { ?>
		<th data-name="image_detail"><div><div id="elh_images_image_detail" class="images_image_detail">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $images->image_detail->FldCaption() ?></span><span class="ewTableHeaderSort"><?php if ($images->image_detail->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($images->image_detail->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
        </div></div></th>
	<?php } ?>
<?php } ?>		
<?php

// Render list options (header, right)
$images_grid->ListOptions->Render("header", "right");
?>
	</tr>
</thead>
<tbody>
<?php
$images_grid->StartRec = 1;
$images_grid->StopRec = $images_grid->TotalRecs; // Show all records

// Restore number of post back records
if ($objForm) {
	$objForm->Index = -1;
	if ($objForm->HasValue($images_grid->FormKeyCountName) && ($images->CurrentAction == "gridadd" || $images->CurrentAction == "gridedit" || $images->CurrentAction == "F")) {
		$images_grid->KeyCount = $objForm->GetValue($images_grid->FormKeyCountName);
		$images_grid->StopRec = $images_grid->StartRec + $images_grid->KeyCount - 1;
	}
}
$images_grid->RecCnt = $images_grid->StartRec - 1;
if ($images_grid->Recordset && !$images_grid->Recordset->EOF) {
	$images_grid->Recordset->MoveFirst();
	$bSelectLimit = $images_grid->UseSelectLimit;
	if (!$bSelectLimit && $images_grid->StartRec > 1)
		$images_grid->Recordset->Move($images_grid->StartRec - 1);
} elseif (!$images->AllowAddDeleteRow && $images_grid->StopRec == 0) {
	$images_grid->StopRec = $images->GridAddRowCount;
}

// Initialize aggregate
$images->RowType = EW_ROWTYPE_AGGREGATEINIT;
$images->ResetAttrs();
$images_grid->RenderRow();
if ($images->CurrentAction == "gridadd")
	$images_grid->RowIndex = 0;
if ($images->CurrentAction == "gridedit")
	$images_grid->RowIndex = 0;
while ($images_grid->RecCnt < $images_grid->StopRec) {
	$images_grid->RecCnt++;
	if (intval($images_grid->RecCnt) >= intval($images_grid->StartRec)) {
		$images_grid->RowCnt++;
		if ($images->CurrentAction == "gridadd" || $images->CurrentAction == "gridedit" || $images->CurrentAction == "F") {
			$images_grid->RowIndex++;
			$objForm->Index = $images_grid->RowIndex;
			if ($objForm->HasValue($images_grid->FormActionName))
				$images_grid->RowAction = strval($objForm->GetValue($images_grid->FormActionName));
			elseif ($images->CurrentAction == "gridadd")
				$images_grid->RowAction = "insert";
			else
				$images_grid->RowAction = "";
		}

		// Set up key count
		$images_grid->KeyCount = $images_grid->RowIndex;

		// Init row class and style
		$images->ResetAttrs();
		$images->CssClass = "";
		if ($images->CurrentAction == "gridadd") {
			if ($images->CurrentMode == "copy") {
				$images_grid->LoadRowValues($images_grid->Recordset); // Load row values
				$images_grid->SetRecordKey($images_grid->RowOldKey, $images_grid->Recordset); // Set old record key
			} else {
				$images_grid->LoadDefaultValues(); // Load default values
				$images_grid->RowOldKey = ""; // Clear old key value
			}
		} else {
			$images_grid->LoadRowValues($images_grid->Recordset); // Load row values
		}
		$images->RowType = EW_ROWTYPE_VIEW; // Render view
		if ($images->CurrentAction == "gridadd") // Grid add
			$images->RowType = EW_ROWTYPE_ADD; // Render add
		if ($images->CurrentAction == "gridadd" && $images->EventCancelled && !$objForm->HasValue("k_blankrow")) // Insert failed
			$images_grid->RestoreCurrentRowFormValues($images_grid->RowIndex); // Restore form values
		if ($images->CurrentAction == "gridedit") { // Grid edit
			if ($images->EventCancelled) {
				$images_grid->RestoreCurrentRowFormValues($images_grid->RowIndex); // Restore form values
			}
			if ($images_grid->RowAction == "insert")
				$images->RowType = EW_ROWTYPE_ADD; // Render add
			else
				$images->RowType = EW_ROWTYPE_EDIT; // Render edit
		}
		if ($images->CurrentAction == "gridedit" && ($images->RowType == EW_ROWTYPE_EDIT || $images->RowType == EW_ROWTYPE_ADD) && $images->EventCancelled) // Update failed
			$images_grid->RestoreCurrentRowFormValues($images_grid->RowIndex); // Restore form values
		if ($images->RowType == EW_ROWTYPE_EDIT) // Edit row
			$images_grid->EditRowCnt++;
		if ($images->CurrentAction == "F") // Confirm row
			$images_grid->RestoreCurrentRowFormValues($images_grid->RowIndex); // Restore form values

		// Set up row id / data-rowindex
		$images->RowAttrs = array_merge($images->RowAttrs, array('data-rowindex'=>$images_grid->RowCnt, 'id'=>'r' . $images_grid->RowCnt . '_images', 'data-rowtype'=>$images->RowType));

		// Render row
		$images_grid->RenderRow();

		// Render list options
		$images_grid->RenderListOptions();

		// Skip delete row / empty row for confirm page
		if ($images_grid->RowAction <> "delete" && $images_grid->RowAction <> "insertdelete" && !($images_grid->RowAction == "insert" && $images->CurrentAction == "F" && $images_grid->EmptyRow())) {
?>
	<tr<?php echo $images->RowAttributes() ?>>
<?php

// Render list options (body, left)
$images_grid->ListOptions->Render("body", "left", $images_grid->RowCnt);
?>
	<?php if ($images->image_name->Visible) { // image_name ?>
		<td data-name="image_name"<?php echo $images->image_name->CellAttributes() ?>>
<?php if ($images_grid->RowAction == "insert") { // Add record ?>
<span id="el$rowindex$_images_image_name" class="form-group images_image_name">
<div id="fd_x<?php echo $images_grid->RowIndex ?>_image_name">
<span title="<?php echo $images->image_name->FldTitle() ? $images->image_name->FldTitle() : $Language->Phrase("ChooseFile") ?>" class="btn btn-default btn-sm fileinput-button ewTooltip<?php if ($images->image_name->ReadOnly || $images->image_name->Disabled) echo " hide"; ?>">
	<span><?php echo $Language->Phrase("ChooseFileBtn") ?></span>
	<input type="file" title=" " data-table="images" data-field="x_image_name" name="x<?php echo $images_grid->RowIndex ?>_image_name" id="x<?php echo $images_grid->RowIndex ?>_image_name"<?php echo $images->image_name->EditAttributes() ?>>
</span>
<input type="hidden" name="fn_x<?php echo $images_grid->RowIndex ?>_image_name" id= "fn_x<?php echo $images_grid->RowIndex ?>_image_name" value="<?php echo $images->image_name->Upload->FileName ?>">
<input type="hidden" name="fa_x<?php echo $images_grid->RowIndex ?>_image_name" id= "fa_x<?php echo $images_grid->RowIndex ?>_image_name" value="0">
<input type="hidden" name="fs_x<?php echo $images_grid->RowIndex ?>_image_name" id= "fs_x<?php echo $images_grid->RowIndex ?>_image_name" value="200">
<input type="hidden" name="fx_x<?php echo $images_grid->RowIndex ?>_image_name" id= "fx_x<?php echo $images_grid->RowIndex ?>_image_name" value="<?php echo $images->image_name->UploadAllowedFileExt ?>">
<input type="hidden" name="fm_x<?php echo $images_grid->RowIndex ?>_image_name" id= "fm_x<?php echo $images_grid->RowIndex ?>_image_name" value="<?php echo $images->image_name->UploadMaxFileSize ?>">
</div>
<table id="ft_x<?php echo $images_grid->RowIndex ?>_image_name" class="table table-condensed pull-left ewUploadTable"><tbody class="files"></tbody></table>
</span>
<input type="hidden" data-table="images" data-field="x_image_name" name="o<?php echo $images_grid->RowIndex ?>_image_name" id="o<?php echo $images_grid->RowIndex ?>_image_name" value="<?php echo ew_HtmlEncode($images->image_name->OldValue) ?>">
<?php } elseif ($images->RowType == EW_ROWTYPE_VIEW) { // View record ?>
<span id="el<?php echo $images_grid->RowCnt ?>_images_image_name" class="images_image_name">
<span<?php echo $images->image_name->ViewAttributes() ?>>
<?php echo ew_GetFileViewTag($images->image_name, $images->image_name->ListViewValue()) ?>
</span>
</span>
<?php } else  { // Edit record ?>
<span id="el<?php echo $images_grid->RowCnt ?>_images_image_name" class="form-group images_image_name">
<div id="fd_x<?php echo $images_grid->RowIndex ?>_image_name">
<span title="<?php echo $images->image_name->FldTitle() ? $images->image_name->FldTitle() : $Language->Phrase("ChooseFile") ?>" class="btn btn-default btn-sm fileinput-button ewTooltip<?php if ($images->image_name->ReadOnly || $images->image_name->Disabled) echo " hide"; ?>">
	<span><?php echo $Language->Phrase("ChooseFileBtn") ?></span>
	<input type="file" title=" " data-table="images" data-field="x_image_name" name="x<?php echo $images_grid->RowIndex ?>_image_name" id="x<?php echo $images_grid->RowIndex ?>_image_name"<?php echo $images->image_name->EditAttributes() ?>>
</span>
<input type="hidden" name="fn_x<?php echo $images_grid->RowIndex ?>_image_name" id= "fn_x<?php echo $images_grid->RowIndex ?>_image_name" value="<?php echo $images->image_name->Upload->FileName ?>">
<?php if (@$_POST["fa_x<?php echo $images_grid->RowIndex ?>_image_name"] == "0") { ?>
<input type="hidden" name="fa_x<?php echo $images_grid->RowIndex ?>_image_name" id= "fa_x<?php echo $images_grid->RowIndex ?>_image_name" value="0">
<?php } else { ?>
<input type="hidden" name="fa_x<?php echo $images_grid->RowIndex ?>_image_name" id= "fa_x<?php echo $images_grid->RowIndex ?>_image_name" value="1">
<?php } ?>
<input type="hidden" name="fs_x<?php echo $images_grid->RowIndex ?>_image_name" id= "fs_x<?php echo $images_grid->RowIndex ?>_image_name" value="200">
<input type="hidden" name="fx_x<?php echo $images_grid->RowIndex ?>_image_name" id= "fx_x<?php echo $images_grid->RowIndex ?>_image_name" value="<?php echo $images->image_name->UploadAllowedFileExt ?>">
<input type="hidden" name="fm_x<?php echo $images_grid->RowIndex ?>_image_name" id= "fm_x<?php echo $images_grid->RowIndex ?>_image_name" value="<?php echo $images->image_name->UploadMaxFileSize ?>">
</div>
<table id="ft_x<?php echo $images_grid->RowIndex ?>_image_name" class="table table-condensed pull-left ewUploadTable"><tbody class="files"></tbody></table>
</span>
<?php } ?>
<a id="<?php echo $images_grid->PageObjName . "_row_" . $images_grid->RowCnt ?>"></a></td>
	<?php } ?>
<?php if ($images->RowType == EW_ROWTYPE_ADD) { // Add record ?>
<input type="hidden" data-table="images" data-field="x_image_id" name="x<?php echo $images_grid->RowIndex ?>_image_id" id="x<?php echo $images_grid->RowIndex ?>_image_id" value="<?php echo ew_HtmlEncode($images->image_id->CurrentValue) ?>">
<input type="hidden" data-table="images" data-field="x_image_id" name="o<?php echo $images_grid->RowIndex ?>_image_id" id="o<?php echo $images_grid->RowIndex ?>_image_id" value="<?php echo ew_HtmlEncode($images->image_id->OldValue) ?>">
<?php } ?>
<?php if ($images->RowType == EW_ROWTYPE_EDIT || $images->CurrentMode == "edit") { ?>
<input type="hidden" data-table="images" data-field="x_image_id" name="x<?php echo $images_grid->RowIndex ?>_image_id" id="x<?php echo $images_grid->RowIndex ?>_image_id" value="<?php echo ew_HtmlEncode($images->image_id->CurrentValue) ?>">
<?php } ?>
	<?php if ($images->image_detail->Visible) { // image_detail ?>
		<td data-name="image_detail"<?php echo $images->image_detail->CellAttributes() ?>>
<?php if ($images->RowType == EW_ROWTYPE_ADD) { // Add record ?>
<span id="el<?php echo $images_grid->RowCnt ?>_images_image_detail" class="form-group images_image_detail">
<?php ew_AppendClass($images->image_detail->EditAttrs["class"], "editor"); ?>
<textarea data-table="images" data-field="x_image_detail" name="x<?php echo $images_grid->RowIndex ?>_image_detail" id="x<?php echo $images_grid->RowIndex ?>_image_detail" cols="35" rows="4" placeholder="<?php echo ew_HtmlEncode($images->image_detail->getPlaceHolder()) ?>"<?php echo $images->image_detail->EditAttributes() ?>><?php echo $images->image_detail->EditValue ?></textarea>
<script type="text/javascript">
ew_CreateEditor("fimagesgrid", "x<?php echo $images_grid->RowIndex ?>_image_detail", 35, 4, <?php echo ($images->image_detail->ReadOnly || FALSE) ? "true" : "false" ?>);
</script>
</span>
<input type="hidden" data-table="images" data-field="x_image_detail" name="o<?php echo $images_grid->RowIndex ?>_image_detail" id="o<?php echo $images_grid->RowIndex ?>_image_detail" value="<?php echo ew_HtmlEncode($images->image_detail->OldValue) ?>">
<?php } ?>
<?php if ($images->RowType == EW_ROWTYPE_EDIT) { // Edit record ?>
<span id="el<?php echo $images_grid->RowCnt ?>_images_image_detail" class="form-group images_image_detail">
<?php ew_AppendClass($images->image_detail->EditAttrs["class"], "editor"); ?>
<textarea data-table="images" data-field="x_image_detail" name="x<?php echo $images_grid->RowIndex ?>_image_detail" id="x<?php echo $images_grid->RowIndex ?>_image_detail" cols="35" rows="4" placeholder="<?php echo ew_HtmlEncode($images->image_detail->getPlaceHolder()) ?>"<?php echo $images->image_detail->EditAttributes() ?>><?php echo $images->image_detail->EditValue ?></textarea>
<script type="text/javascript">
ew_CreateEditor("fimagesgrid", "x<?php echo $images_grid->RowIndex ?>_image_detail", 35, 4, <?php echo ($images->image_detail->ReadOnly || FALSE) ? "true" : "false" ?>);
</script>
</span>
<?php } ?>
<?php if ($images->RowType == EW_ROWTYPE_VIEW) { // View record ?>
<span id="el<?php echo $images_grid->RowCnt ?>_images_image_detail" class="images_image_detail">
<span<?php echo $images->image_detail->ViewAttributes() ?>>
<?php echo $images->image_detail->ListViewValue() ?></span>
</span>
<input type="hidden" data-table="images" data-field="x_image_detail" name="x<?php echo $images_grid->RowIndex ?>_image_detail" id="x<?php echo $images_grid->RowIndex ?>_image_detail" value="<?php echo ew_HtmlEncode($images->image_detail->FormValue) ?>">
<input type="hidden" data-table="images" data-field="x_image_detail" name="o<?php echo $images_grid->RowIndex ?>_image_detail" id="o<?php echo $images_grid->RowIndex ?>_image_detail" value="<?php echo ew_HtmlEncode($images->image_detail->OldValue) ?>">
<?php } ?>
</td>
	<?php } ?>
<?php

// Render list options (body, right)
$images_grid->ListOptions->Render("body", "right", $images_grid->RowCnt);
?>
	</tr>
<?php if ($images->RowType == EW_ROWTYPE_ADD || $images->RowType == EW_ROWTYPE_EDIT) { ?>
<script type="text/javascript">
fimagesgrid.UpdateOpts(<?php echo $images_grid->RowIndex ?>);
</script>
<?php } ?>
<?php
	}
	} // End delete row checking
	if ($images->CurrentAction <> "gridadd" || $images->CurrentMode == "copy")
		if (!$images_grid->Recordset->EOF) $images_grid->Recordset->MoveNext();
}
?>
<?php
	if ($images->CurrentMode == "add" || $images->CurrentMode == "copy" || $images->CurrentMode == "edit") {
		$images_grid->RowIndex = '$rowindex$';
		$images_grid->LoadDefaultValues();

		// Set row properties
		$images->ResetAttrs();
		$images->RowAttrs = array_merge($images->RowAttrs, array('data-rowindex'=>$images_grid->RowIndex, 'id'=>'r0_images', 'data-rowtype'=>EW_ROWTYPE_ADD));
		ew_AppendClass($images->RowAttrs["class"], "ewTemplate");
		$images->RowType = EW_ROWTYPE_ADD;

		// Render row
		$images_grid->RenderRow();

		// Render list options
		$images_grid->RenderListOptions();
		$images_grid->StartRowCnt = 0;
?>
	<tr<?php echo $images->RowAttributes() ?>>
<?php

// Render list options (body, left)
$images_grid->ListOptions->Render("body", "left", $images_grid->RowIndex);
?>
	<?php if ($images->image_name->Visible) { // image_name ?>
		<td data-name="image_name">
<span id="el$rowindex$_images_image_name" class="form-group images_image_name">
<div id="fd_x<?php echo $images_grid->RowIndex ?>_image_name">
<span title="<?php echo $images->image_name->FldTitle() ? $images->image_name->FldTitle() : $Language->Phrase("ChooseFile") ?>" class="btn btn-default btn-sm fileinput-button ewTooltip<?php if ($images->image_name->ReadOnly || $images->image_name->Disabled) echo " hide"; ?>">
	<span><?php echo $Language->Phrase("ChooseFileBtn") ?></span>
	<input type="file" title=" " data-table="images" data-field="x_image_name" name="x<?php echo $images_grid->RowIndex ?>_image_name" id="x<?php echo $images_grid->RowIndex ?>_image_name"<?php echo $images->image_name->EditAttributes() ?>>
</span>
<input type="hidden" name="fn_x<?php echo $images_grid->RowIndex ?>_image_name" id= "fn_x<?php echo $images_grid->RowIndex ?>_image_name" value="<?php echo $images->image_name->Upload->FileName ?>">
<input type="hidden" name="fa_x<?php echo $images_grid->RowIndex ?>_image_name" id= "fa_x<?php echo $images_grid->RowIndex ?>_image_name" value="0">
<input type="hidden" name="fs_x<?php echo $images_grid->RowIndex ?>_image_name" id= "fs_x<?php echo $images_grid->RowIndex ?>_image_name" value="200">
<input type="hidden" name="fx_x<?php echo $images_grid->RowIndex ?>_image_name" id= "fx_x<?php echo $images_grid->RowIndex ?>_image_name" value="<?php echo $images->image_name->UploadAllowedFileExt ?>">
<input type="hidden" name="fm_x<?php echo $images_grid->RowIndex ?>_image_name" id= "fm_x<?php echo $images_grid->RowIndex ?>_image_name" value="<?php echo $images->image_name->UploadMaxFileSize ?>">
</div>
<table id="ft_x<?php echo $images_grid->RowIndex ?>_image_name" class="table table-condensed pull-left ewUploadTable"><tbody class="files"></tbody></table>
</span>
<input type="hidden" data-table="images" data-field="x_image_name" name="o<?php echo $images_grid->RowIndex ?>_image_name" id="o<?php echo $images_grid->RowIndex ?>_image_name" value="<?php echo ew_HtmlEncode($images->image_name->OldValue) ?>">
</td>
	<?php } ?>
	<?php if ($images->image_detail->Visible) { // image_detail ?>
		<td data-name="image_detail">
<?php if ($images->CurrentAction <> "F") { ?>
<span id="el$rowindex$_images_image_detail" class="form-group images_image_detail">
<?php ew_AppendClass($images->image_detail->EditAttrs["class"], "editor"); ?>
<textarea data-table="images" data-field="x_image_detail" name="x<?php echo $images_grid->RowIndex ?>_image_detail" id="x<?php echo $images_grid->RowIndex ?>_image_detail" cols="35" rows="4" placeholder="<?php echo ew_HtmlEncode($images->image_detail->getPlaceHolder()) ?>"<?php echo $images->image_detail->EditAttributes() ?>><?php echo $images->image_detail->EditValue ?></textarea>
<script type="text/javascript">
ew_CreateEditor("fimagesgrid", "x<?php echo $images_grid->RowIndex ?>_image_detail", 35, 4, <?php echo ($images->image_detail->ReadOnly || FALSE) ? "true" : "false" ?>);
</script>
</span>
<?php } else { ?>
<span id="el$rowindex$_images_image_detail" class="form-group images_image_detail">
<span<?php echo $images->image_detail->ViewAttributes() ?>>
<p class="form-control-static"><?php echo $images->image_detail->ViewValue ?></p></span>
</span>
<input type="hidden" data-table="images" data-field="x_image_detail" name="x<?php echo $images_grid->RowIndex ?>_image_detail" id="x<?php echo $images_grid->RowIndex ?>_image_detail" value="<?php echo ew_HtmlEncode($images->image_detail->FormValue) ?>">
<?php } ?>
<input type="hidden" data-table="images" data-field="x_image_detail" name="o<?php echo $images_grid->RowIndex ?>_image_detail" id="o<?php echo $images_grid->RowIndex ?>_image_detail" value="<?php echo ew_HtmlEncode($images->image_detail->OldValue) ?>">
</td>
	<?php } ?>
<?php

// Render list options (body, right)
$images_grid->ListOptions->Render("body", "right", $images_grid->RowCnt);
?>
<script type="text/javascript">
fimagesgrid.UpdateOpts(<?php echo $images_grid->RowIndex ?>);
</script>
	</tr>
<?php
}
?>
</tbody>
</table>
<?php if ($images->CurrentMode == "add" || $images->CurrentMode == "copy") { ?>
<input type="hidden" name="a_list" id="a_list" value="gridinsert">
<input type="hidden" name="<?php echo $images_grid->FormKeyCountName ?>" id="<?php echo $images_grid->FormKeyCountName ?>" value="<?php echo $images_grid->KeyCount ?>">
<?php echo $images_grid->MultiSelectKey ?>
<?php } ?>
<?php if ($images->CurrentMode == "edit") { ?>
<input type="hidden" name="a_list" id="a_list" value="gridupdate">
<input type="hidden" name="<?php echo $images_grid->FormKeyCountName ?>" id="<?php echo $images_grid->FormKeyCountName ?>" value="<?php echo $images_grid->KeyCount ?>">
<?php echo $images_grid->MultiSelectKey ?>
<?php } ?>
<?php if ($images->CurrentMode == "") { ?>
<input type="hidden" name="a_list" id="a_list" value="">
<?php } ?>
<input type="hidden" name="detailpage" value="fimagesgrid">
</div>
<?php

// Close recordset
if ($images_grid->Recordset)
	$images_grid->Recordset->Close();
?>
<?php if ($images_grid->ShowOtherOptions) { ?>
<div class="panel-footer ewGridLowerPanel">
<?php
	foreach ($images_grid->OtherOptions as &$option)
		$option->Render("body", "bottom");
?>
</div>
<div class="clearfix"></div>
<?php } ?>
</div>
</div>
<?php } ?>
<?php if ($images_grid->TotalRecs == 0 && $images->CurrentAction == "") { // Show other options ?>
<div class="ewListOtherOptions">
<?php
	foreach ($images_grid->OtherOptions as &$option) {
		$option->ButtonClass = "";
		$option->Render("body", "");
	}
?>
</div>
<div class="clearfix"></div>
<?php } ?>
<?php if ($images->Export == "") { ?>
<script type="text/javascript">
fimagesgrid.Init();
</script>
<?php } ?>
<?php
$images_grid->ShowPageFooter();
if (EW_DEBUG_ENABLED)
	echo ew_DebugMsg();
?>
<?php
$images_grid->Page_Terminate();
?>
