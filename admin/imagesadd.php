<?php
if (session_id() == "") session_start(); // Initialize Session data
ob_start(); // Turn on output buffering
?>
<?php include_once "ewcfg12.php" ?>
<?php include_once ((EW_USE_ADODB) ? "adodb5/adodb.inc.php" : "ewmysql12.php") ?>
<?php include_once "phpfn12.php" ?>
<?php include_once "imagesinfo.php" ?>
<?php include_once "userfn12.php" ?>
<?php

//
// Page class
//

$images_add = NULL; // Initialize page object first

class cimages_add extends cimages {

	// Page ID
	var $PageID = 'add';

	// Project ID
	var $ProjectID = "{D635DDF5-EC98-4B6F-806D-28D8D9C856B8}";

	// Table name
	var $TableName = 'images';

	// Page object name
	var $PageObjName = 'images_add';

	// Page name
	function PageName() {
		return ew_CurrentPage();
	}

	// Page URL
	function PageUrl() {
		$PageUrl = ew_CurrentPage() . "?";
		if ($this->UseTokenInUrl) $PageUrl .= "t=" . $this->TableVar . "&"; // Add page token
		return $PageUrl;
	}

	// Message
	function getMessage() {
		return @$_SESSION[EW_SESSION_MESSAGE];
	}

	function setMessage($v) {
		ew_AddMessage($_SESSION[EW_SESSION_MESSAGE], $v);
	}

	function getFailureMessage() {
		return @$_SESSION[EW_SESSION_FAILURE_MESSAGE];
	}

	function setFailureMessage($v) {
		ew_AddMessage($_SESSION[EW_SESSION_FAILURE_MESSAGE], $v);
	}

	function getSuccessMessage() {
		return @$_SESSION[EW_SESSION_SUCCESS_MESSAGE];
	}

	function setSuccessMessage($v) {
		ew_AddMessage($_SESSION[EW_SESSION_SUCCESS_MESSAGE], $v);
	}

	function getWarningMessage() {
		return @$_SESSION[EW_SESSION_WARNING_MESSAGE];
	}

	function setWarningMessage($v) {
		ew_AddMessage($_SESSION[EW_SESSION_WARNING_MESSAGE], $v);
	}

	// Methods to clear message
	function ClearMessage() {
		$_SESSION[EW_SESSION_MESSAGE] = "";
	}

	function ClearFailureMessage() {
		$_SESSION[EW_SESSION_FAILURE_MESSAGE] = "";
	}

	function ClearSuccessMessage() {
		$_SESSION[EW_SESSION_SUCCESS_MESSAGE] = "";
	}

	function ClearWarningMessage() {
		$_SESSION[EW_SESSION_WARNING_MESSAGE] = "";
	}

	function ClearMessages() {
		$_SESSION[EW_SESSION_MESSAGE] = "";
		$_SESSION[EW_SESSION_FAILURE_MESSAGE] = "";
		$_SESSION[EW_SESSION_SUCCESS_MESSAGE] = "";
		$_SESSION[EW_SESSION_WARNING_MESSAGE] = "";
	}

	// Show message
	function ShowMessage() {
		$hidden = FALSE;
		$html = "";

		// Message
		$sMessage = $this->getMessage();
		$this->Message_Showing($sMessage, "");
		if ($sMessage <> "") { // Message in Session, display
			if (!$hidden)
				$sMessage = "<button type=\"button\" class=\"close\" data-dismiss=\"alert\">&times;</button>" . $sMessage;
			$html .= "<div class=\"alert alert-info ewInfo\">" . $sMessage . "</div>";
			$_SESSION[EW_SESSION_MESSAGE] = ""; // Clear message in Session
		}

		// Warning message
		$sWarningMessage = $this->getWarningMessage();
		$this->Message_Showing($sWarningMessage, "warning");
		if ($sWarningMessage <> "") { // Message in Session, display
			if (!$hidden)
				$sWarningMessage = "<button type=\"button\" class=\"close\" data-dismiss=\"alert\">&times;</button>" . $sWarningMessage;
			$html .= "<div class=\"alert alert-warning ewWarning\">" . $sWarningMessage . "</div>";
			$_SESSION[EW_SESSION_WARNING_MESSAGE] = ""; // Clear message in Session
		}

		// Success message
		$sSuccessMessage = $this->getSuccessMessage();
		$this->Message_Showing($sSuccessMessage, "success");
		if ($sSuccessMessage <> "") { // Message in Session, display
			if (!$hidden)
				$sSuccessMessage = "<button type=\"button\" class=\"close\" data-dismiss=\"alert\">&times;</button>" . $sSuccessMessage;
			$html .= "<div class=\"alert alert-success ewSuccess\">" . $sSuccessMessage . "</div>";
			$_SESSION[EW_SESSION_SUCCESS_MESSAGE] = ""; // Clear message in Session
		}

		// Failure message
		$sErrorMessage = $this->getFailureMessage();
		$this->Message_Showing($sErrorMessage, "failure");
		if ($sErrorMessage <> "") { // Message in Session, display
			if (!$hidden)
				$sErrorMessage = "<button type=\"button\" class=\"close\" data-dismiss=\"alert\">&times;</button>" . $sErrorMessage;
			$html .= "<div class=\"alert alert-danger ewError\">" . $sErrorMessage . "</div>";
			$_SESSION[EW_SESSION_FAILURE_MESSAGE] = ""; // Clear message in Session
		}
		echo "<div class=\"ewMessageDialog\"" . (($hidden) ? " style=\"display: none;\"" : "") . ">" . $html . "</div>";
	}
	var $PageHeader;
	var $PageFooter;

	// Show Page Header
	function ShowPageHeader() {
		$sHeader = $this->PageHeader;
		$this->Page_DataRendering($sHeader);
		if ($sHeader <> "") { // Header exists, display
			echo "<p>" . $sHeader . "</p>";
		}
	}

	// Show Page Footer
	function ShowPageFooter() {
		$sFooter = $this->PageFooter;
		$this->Page_DataRendered($sFooter);
		if ($sFooter <> "") { // Footer exists, display
			echo "<p>" . $sFooter . "</p>";
		}
	}

	// Validate page request
	function IsPageRequest() {
		global $objForm;
		if ($this->UseTokenInUrl) {
			if ($objForm)
				return ($this->TableVar == $objForm->GetValue("t"));
			if (@$_GET["t"] <> "")
				return ($this->TableVar == $_GET["t"]);
		} else {
			return TRUE;
		}
	}
	var $Token = "";
	var $TokenTimeout = 0;
	var $CheckToken = EW_CHECK_TOKEN;
	var $CheckTokenFn = "ew_CheckToken";
	var $CreateTokenFn = "ew_CreateToken";

	// Valid Post
	function ValidPost() {
		if (!$this->CheckToken || !ew_IsHttpPost())
			return TRUE;
		if (!isset($_POST[EW_TOKEN_NAME]))
			return FALSE;
		$fn = $this->CheckTokenFn;
		if (is_callable($fn))
			return $fn($_POST[EW_TOKEN_NAME], $this->TokenTimeout);
		return FALSE;
	}

	// Create Token
	function CreateToken() {
		global $gsToken;
		if ($this->CheckToken) {
			$fn = $this->CreateTokenFn;
			if ($this->Token == "" && is_callable($fn)) // Create token
				$this->Token = $fn();
			$gsToken = $this->Token; // Save to global variable
		}
	}

	//
	// Page class constructor
	//
	function __construct() {
		global $conn, $Language;
		$GLOBALS["Page"] = &$this;
		$this->TokenTimeout = ew_SessionTimeoutTime();

		// Language object
		if (!isset($Language)) $Language = new cLanguage();

		// Parent constuctor
		parent::__construct();

		// Table object (images)
		if (!isset($GLOBALS["images"]) || get_class($GLOBALS["images"]) == "cimages") {
			$GLOBALS["images"] = &$this;
			$GLOBALS["Table"] = &$GLOBALS["images"];
		}

		// Page ID
		if (!defined("EW_PAGE_ID"))
			define("EW_PAGE_ID", 'add', TRUE);

		// Table name (for backward compatibility)
		if (!defined("EW_TABLE_NAME"))
			define("EW_TABLE_NAME", 'images', TRUE);

		// Start timer
		if (!isset($GLOBALS["gTimer"])) $GLOBALS["gTimer"] = new cTimer();

		// Open connection
		if (!isset($conn)) $conn = ew_Connect($this->DBID);
	}

	// 
	//  Page_Init
	//
	function Page_Init() {
		global $gsExport, $gsCustomExport, $gsExportFile, $UserProfile, $Language, $Security, $objForm;

		// Security
		$Security = new cAdvancedSecurity();
		if (!$Security->IsLoggedIn()) $Security->AutoLogin();
		if (!$Security->IsLoggedIn()) $this->Page_Terminate(ew_GetUrl("login.php"));

		// Create form object
		$objForm = new cFormObj();
		$this->CurrentAction = (@$_GET["a"] <> "") ? $_GET["a"] : @$_POST["a_list"]; // Set up current action

		// Global Page Loading event (in userfn*.php)
		Page_Loading();

		// Page Load event
		$this->Page_Load();

		// Check token
		if (!$this->ValidPost()) {
			echo $Language->Phrase("InvalidPostRequest");
			$this->Page_Terminate();
			exit();
		}

		// Process auto fill
		if (@$_POST["ajax"] == "autofill") {
			$results = $this->GetAutoFill(@$_POST["name"], @$_POST["q"]);
			if ($results) {

				// Clean output buffer
				if (!EW_DEBUG_ENABLED && ob_get_length())
					ob_end_clean();
				echo $results;
				$this->Page_Terminate();
				exit();
			}
		}

		// Create Token
		$this->CreateToken();
	}

	//
	// Page_Terminate
	//
	function Page_Terminate($url = "") {
		global $gsExportFile, $gTmpImages;

		// Page Unload event
		$this->Page_Unload();

		// Global Page Unloaded event (in userfn*.php)
		Page_Unloaded();

		// Export
		global $EW_EXPORT, $images;
		if ($this->CustomExport <> "" && $this->CustomExport == $this->Export && array_key_exists($this->CustomExport, $EW_EXPORT)) {
				$sContent = ob_get_contents();
			if ($gsExportFile == "") $gsExportFile = $this->TableVar;
			$class = $EW_EXPORT[$this->CustomExport];
			if (class_exists($class)) {
				$doc = new $class($images);
				$doc->Text = $sContent;
				if ($this->Export == "email")
					echo $this->ExportEmail($doc->Text);
				else
					$doc->Export();
				ew_DeleteTmpImages(); // Delete temp images
				exit();
			}
		}
		$this->Page_Redirecting($url);

		 // Close connection
		ew_CloseConn();

		// Go to URL if specified
		if ($url <> "") {
			if (!EW_DEBUG_ENABLED && ob_get_length())
				ob_end_clean();
			header("Location: " . $url);
		}
		exit();
	}
	var $FormClassName = "form-horizontal ewForm ewAddForm";
	var $DbMasterFilter = "";
	var $DbDetailFilter = "";
	var $StartRec;
	var $Priv = 0;
	var $OldRecordset;
	var $CopyRecord;

	// 
	// Page main
	//
	function Page_Main() {
		global $objForm, $Language, $gsFormError;

		// Process form if post back
		if (@$_POST["a_add"] <> "") {
			$this->CurrentAction = $_POST["a_add"]; // Get form action
			$this->CopyRecord = $this->LoadOldRecord(); // Load old recordset
			$this->LoadFormValues(); // Load form values
		} else { // Not post back

			// Load key values from QueryString
			$this->CopyRecord = TRUE;
			if (@$_GET["image_id"] != "") {
				$this->image_id->setQueryStringValue($_GET["image_id"]);
				$this->setKey("image_id", $this->image_id->CurrentValue); // Set up key
			} else {
				$this->setKey("image_id", ""); // Clear key
				$this->CopyRecord = FALSE;
			}
			if ($this->CopyRecord) {
				$this->CurrentAction = "C"; // Copy record
			} else {
				$this->CurrentAction = "I"; // Display blank record
				$this->LoadDefaultValues(); // Load default values
			}
		}

		// Set up Breadcrumb
		$this->SetupBreadcrumb();

		// Validate form if post back
		if (@$_POST["a_add"] <> "") {
			if (!$this->ValidateForm()) {
				$this->CurrentAction = "I"; // Form error, reset action
				$this->EventCancelled = TRUE; // Event cancelled
				$this->RestoreFormValues(); // Restore form values
				$this->setFailureMessage($gsFormError);
			}
		}

		// Perform action based on action code
		switch ($this->CurrentAction) {
			case "I": // Blank record, no action required
				break;
			case "C": // Copy an existing record
				if (!$this->LoadRow()) { // Load record based on key
					if ($this->getFailureMessage() == "") $this->setFailureMessage($Language->Phrase("NoRecord")); // No record found
					$this->Page_Terminate("imageslist.php"); // No matching record, return to list
				}
				break;
			case "A": // Add new record
				$this->SendEmail = TRUE; // Send email on add success
				if ($this->AddRow($this->OldRecordset)) { // Add successful
					if ($this->getSuccessMessage() == "")
						$this->setSuccessMessage($Language->Phrase("AddSuccess")); // Set up success message
					$sReturnUrl = $this->getReturnUrl();
					if (ew_GetPageName($sReturnUrl) == "imagesview.php")
						$sReturnUrl = $this->GetViewUrl(); // View paging, return to view page with keyurl directly
					$this->Page_Terminate($sReturnUrl); // Clean up and return
				} else {
					$this->EventCancelled = TRUE; // Event cancelled
					$this->RestoreFormValues(); // Add failed, restore form values
				}
		}

		// Render row based on row type
		$this->RowType = EW_ROWTYPE_ADD; // Render add type

		// Render row
		$this->ResetAttrs();
		$this->RenderRow();
	}

	// Get upload files
	function GetUploadFiles() {
		global $objForm, $Language;

		// Get upload data
		$this->image_name->Upload->Index = $objForm->Index;
		$this->image_name->Upload->UploadFile();
		$this->image_name->CurrentValue = $this->image_name->Upload->FileName;
	}

	// Load default values
	function LoadDefaultValues() {
		$this->p_id->CurrentValue = NULL;
		$this->p_id->OldValue = $this->p_id->CurrentValue;
		$this->image_name->Upload->DbValue = NULL;
		$this->image_name->OldValue = $this->image_name->Upload->DbValue;
		$this->image_name->CurrentValue = NULL; // Clear file related field
		$this->image_detail->CurrentValue = NULL;
		$this->image_detail->OldValue = $this->image_detail->CurrentValue;
	}

	// Load form values
	function LoadFormValues() {

		// Load from form
		global $objForm;
		$this->GetUploadFiles(); // Get upload files
		if (!$this->p_id->FldIsDetailKey) {
			$this->p_id->setFormValue($objForm->GetValue("x_p_id"));
		}
		if (!$this->image_detail->FldIsDetailKey) {
			$this->image_detail->setFormValue($objForm->GetValue("x_image_detail"));
		}
	}

	// Restore form values
	function RestoreFormValues() {
		global $objForm;
		$this->LoadOldRecord();
		$this->p_id->CurrentValue = $this->p_id->FormValue;
		$this->image_detail->CurrentValue = $this->image_detail->FormValue;
	}

	// Load row based on key values
	function LoadRow() {
		global $Security, $Language;
		$sFilter = $this->KeyFilter();

		// Call Row Selecting event
		$this->Row_Selecting($sFilter);

		// Load SQL based on filter
		$this->CurrentFilter = $sFilter;
		$sSql = $this->SQL();
		$conn = &$this->Connection();
		$res = FALSE;
		$rs = ew_LoadRecordset($sSql, $conn);
		if ($rs && !$rs->EOF) {
			$res = TRUE;
			$this->LoadRowValues($rs); // Load row values
			$rs->Close();
		}
		return $res;
	}

	// Load row values from recordset
	function LoadRowValues(&$rs) {
		if (!$rs || $rs->EOF) return;

		// Call Row Selected event
		$row = &$rs->fields;
		$this->Row_Selected($row);
		$this->image_id->setDbValue($rs->fields('image_id'));
		$this->p_id->setDbValue($rs->fields('p_id'));
		$this->image_name->Upload->DbValue = $rs->fields('image_name');
		$this->image_name->CurrentValue = $this->image_name->Upload->DbValue;
		$this->image_detail->setDbValue($rs->fields('image_detail'));
	}

	// Load DbValue from recordset
	function LoadDbValues(&$rs) {
		if (!$rs || !is_array($rs) && $rs->EOF) return;
		$row = is_array($rs) ? $rs : $rs->fields;
		$this->image_id->DbValue = $row['image_id'];
		$this->p_id->DbValue = $row['p_id'];
		$this->image_name->Upload->DbValue = $row['image_name'];
		$this->image_detail->DbValue = $row['image_detail'];
	}

	// Load old record
	function LoadOldRecord() {

		// Load key values from Session
		$bValidKey = TRUE;
		if (strval($this->getKey("image_id")) <> "")
			$this->image_id->CurrentValue = $this->getKey("image_id"); // image_id
		else
			$bValidKey = FALSE;

		// Load old recordset
		if ($bValidKey) {
			$this->CurrentFilter = $this->KeyFilter();
			$sSql = $this->SQL();
			$conn = &$this->Connection();
			$this->OldRecordset = ew_LoadRecordset($sSql, $conn);
			$this->LoadRowValues($this->OldRecordset); // Load row values
		} else {
			$this->OldRecordset = NULL;
		}
		return $bValidKey;
	}

	// Render row values based on field settings
	function RenderRow() {
		global $Security, $Language, $gsLanguage;

		// Initialize URLs
		// Call Row_Rendering event

		$this->Row_Rendering();

		// Common render codes for all row types
		// image_id
		// p_id
		// image_name
		// image_detail

		if ($this->RowType == EW_ROWTYPE_VIEW) { // View row

		// image_id
		$this->image_id->ViewValue = $this->image_id->CurrentValue;
		$this->image_id->ViewCustomAttributes = "";

		// p_id
		if (strval($this->p_id->CurrentValue) <> "") {
			$sFilterWrk = "`id`" . ew_SearchString("=", $this->p_id->CurrentValue, EW_DATATYPE_NUMBER, "");
		$sSqlWrk = "SELECT `id`, `title` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `projects`";
		$sWhereWrk = "";
		ew_AddFilter($sWhereWrk, $sFilterWrk);
		$this->Lookup_Selecting($this->p_id, $sWhereWrk); // Call Lookup selecting
		if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			$rswrk = Conn()->Execute($sSqlWrk);
			if ($rswrk && !$rswrk->EOF) { // Lookup values found
				$arwrk = array();
				$arwrk[1] = $rswrk->fields('DispFld');
				$this->p_id->ViewValue = $this->p_id->DisplayValue($arwrk);
				$rswrk->Close();
			} else {
				$this->p_id->ViewValue = $this->p_id->CurrentValue;
			}
		} else {
			$this->p_id->ViewValue = NULL;
		}
		$this->p_id->ViewCustomAttributes = "";

		// image_name
		$this->image_name->UploadPath = "/projectimages";
		if (!ew_Empty($this->image_name->Upload->DbValue)) {
			$this->image_name->ViewValue = $this->image_name->Upload->DbValue;
		} else {
			$this->image_name->ViewValue = "";
		}
		$this->image_name->ViewCustomAttributes = "";

		// image_detail
		$this->image_detail->ViewValue = $this->image_detail->CurrentValue;
		$this->image_detail->ViewCustomAttributes = "";

			// p_id
			$this->p_id->LinkCustomAttributes = "";
			$this->p_id->HrefValue = "";
			$this->p_id->TooltipValue = "";

			// image_name
			$this->image_name->LinkCustomAttributes = "";
			$this->image_name->HrefValue = "";
			$this->image_name->HrefValue2 = $this->image_name->UploadPath . $this->image_name->Upload->DbValue;
			$this->image_name->TooltipValue = "";

			// image_detail
			$this->image_detail->LinkCustomAttributes = "";
			$this->image_detail->HrefValue = "";
			$this->image_detail->TooltipValue = "";
		} elseif ($this->RowType == EW_ROWTYPE_ADD) { // Add row

			// p_id
			$this->p_id->EditAttrs["class"] = "form-control";
			$this->p_id->EditCustomAttributes = "";
			if (trim(strval($this->p_id->CurrentValue)) == "") {
				$sFilterWrk = "0=1";
			} else {
				$sFilterWrk = "`id`" . ew_SearchString("=", $this->p_id->CurrentValue, EW_DATATYPE_NUMBER, "");
			}
			$sSqlWrk = "SELECT `id`, `title` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld`, '' AS `SelectFilterFld`, '' AS `SelectFilterFld2`, '' AS `SelectFilterFld3`, '' AS `SelectFilterFld4` FROM `projects`";
			$sWhereWrk = "";
			ew_AddFilter($sWhereWrk, $sFilterWrk);
			$this->Lookup_Selecting($this->p_id, $sWhereWrk); // Call Lookup selecting
			if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			$rswrk = Conn()->Execute($sSqlWrk);
			$arwrk = ($rswrk) ? $rswrk->GetRows() : array();
			if ($rswrk) $rswrk->Close();
			array_unshift($arwrk, array("", $Language->Phrase("PleaseSelect"), "", "", "", "", "", "", ""));
			$this->p_id->EditValue = $arwrk;

			// image_name
			$this->image_name->EditAttrs["class"] = "form-control";
			$this->image_name->EditCustomAttributes = "";
			$this->image_name->UploadPath = "/projectimages";
			if (!ew_Empty($this->image_name->Upload->DbValue)) {
				$this->image_name->EditValue = $this->image_name->Upload->DbValue;
			} else {
				$this->image_name->EditValue = "";
			}
			if (!ew_Empty($this->image_name->CurrentValue))
				$this->image_name->Upload->FileName = $this->image_name->CurrentValue;
			if (($this->CurrentAction == "I" || $this->CurrentAction == "C") && !$this->EventCancelled) ew_RenderUploadField($this->image_name);

			// image_detail
			$this->image_detail->EditAttrs["class"] = "form-control";
			$this->image_detail->EditCustomAttributes = "";
			$this->image_detail->EditValue = ew_HtmlEncode($this->image_detail->CurrentValue);
			$this->image_detail->PlaceHolder = ew_RemoveHtml($this->image_detail->FldCaption());

			// Edit refer script
			// p_id

			$this->p_id->HrefValue = "";

			// image_name
			$this->image_name->HrefValue = "";
			$this->image_name->HrefValue2 = $this->image_name->UploadPath . $this->image_name->Upload->DbValue;

			// image_detail
			$this->image_detail->HrefValue = "";
		}
		if ($this->RowType == EW_ROWTYPE_ADD ||
			$this->RowType == EW_ROWTYPE_EDIT ||
			$this->RowType == EW_ROWTYPE_SEARCH) { // Add / Edit / Search row
			$this->SetupFieldTitles();
		}

		// Call Row Rendered event
		if ($this->RowType <> EW_ROWTYPE_AGGREGATEINIT)
			$this->Row_Rendered();
	}

	// Validate form
	function ValidateForm() {
		global $Language, $gsFormError;

		// Initialize form error message
		$gsFormError = "";

		// Check if validation required
		if (!EW_SERVER_VALIDATE)
			return ($gsFormError == "");
		if (!$this->p_id->FldIsDetailKey && !is_null($this->p_id->FormValue) && $this->p_id->FormValue == "") {
			ew_AddMessage($gsFormError, str_replace("%s", $this->p_id->FldCaption(), $this->p_id->ReqErrMsg));
		}
		if ($this->image_name->Upload->FileName == "" && !$this->image_name->Upload->KeepFile) {
			ew_AddMessage($gsFormError, str_replace("%s", $this->image_name->FldCaption(), $this->image_name->ReqErrMsg));
		}
		if (!$this->image_detail->FldIsDetailKey && !is_null($this->image_detail->FormValue) && $this->image_detail->FormValue == "") {
			ew_AddMessage($gsFormError, str_replace("%s", $this->image_detail->FldCaption(), $this->image_detail->ReqErrMsg));
		}

		// Return validate result
		$ValidateForm = ($gsFormError == "");

		// Call Form_CustomValidate event
		$sFormCustomError = "";
		$ValidateForm = $ValidateForm && $this->Form_CustomValidate($sFormCustomError);
		if ($sFormCustomError <> "") {
			ew_AddMessage($gsFormError, $sFormCustomError);
		}
		return $ValidateForm;
	}

	// Add record
	function AddRow($rsold = NULL) {
		global $Language, $Security;
		$conn = &$this->Connection();

		// Load db values from rsold
		if ($rsold) {
			$this->LoadDbValues($rsold);
			$this->image_name->OldUploadPath = "/projectimages";
			$this->image_name->UploadPath = $this->image_name->OldUploadPath;
		}
		$rsnew = array();

		// p_id
		$this->p_id->SetDbValueDef($rsnew, $this->p_id->CurrentValue, 0, FALSE);

		// image_name
		if (!$this->image_name->Upload->KeepFile) {
			$this->image_name->Upload->DbValue = ""; // No need to delete old file
			if ($this->image_name->Upload->FileName == "") {
				$rsnew['image_name'] = NULL;
			} else {
				$rsnew['image_name'] = $this->image_name->Upload->FileName;
			}
		}

		// image_detail
		$this->image_detail->SetDbValueDef($rsnew, $this->image_detail->CurrentValue, "", FALSE);
		if (!$this->image_name->Upload->KeepFile) {
			$this->image_name->UploadPath = "/projectimages";
			if (!ew_Empty($this->image_name->Upload->Value)) {
				$rsnew['image_name'] = ew_UploadFileNameEx(ew_UploadPathEx(TRUE, $this->image_name->UploadPath), $rsnew['image_name']); // Get new file name
			}
		}

		// Call Row Inserting event
		$rs = ($rsold == NULL) ? NULL : $rsold->fields;
		$bInsertRow = $this->Row_Inserting($rs, $rsnew);
		if ($bInsertRow) {
			$conn->raiseErrorFn = $GLOBALS["EW_ERROR_FN"];
			$AddRow = $this->Insert($rsnew);
			$conn->raiseErrorFn = '';
			if ($AddRow) {

				// Get insert id if necessary
				$this->image_id->setDbValue($conn->Insert_ID());
				$rsnew['image_id'] = $this->image_id->DbValue;
				if (!$this->image_name->Upload->KeepFile) {
					if (!ew_Empty($this->image_name->Upload->Value)) {
						$this->image_name->Upload->SaveToFile($this->image_name->UploadPath, $rsnew['image_name'], TRUE);
					}
				}
			}
		} else {
			if ($this->getSuccessMessage() <> "" || $this->getFailureMessage() <> "") {

				// Use the message, do nothing
			} elseif ($this->CancelMessage <> "") {
				$this->setFailureMessage($this->CancelMessage);
				$this->CancelMessage = "";
			} else {
				$this->setFailureMessage($Language->Phrase("InsertCancelled"));
			}
			$AddRow = FALSE;
		}
		if ($AddRow) {

			// Call Row Inserted event
			$rs = ($rsold == NULL) ? NULL : $rsold->fields;
			$this->Row_Inserted($rs, $rsnew);
		}

		// image_name
		ew_CleanUploadTempPath($this->image_name, $this->image_name->Upload->Index);
		return $AddRow;
	}

	// Set up Breadcrumb
	function SetupBreadcrumb() {
		global $Breadcrumb, $Language;
		$Breadcrumb = new cBreadcrumb();
		$url = substr(ew_CurrentUrl(), strrpos(ew_CurrentUrl(), "/")+1);
		$Breadcrumb->Add("list", $this->TableVar, "imageslist.php", "", $this->TableVar, TRUE);
		$PageId = ($this->CurrentAction == "C") ? "Copy" : "Add";
		$Breadcrumb->Add("add", $PageId, $url);
	}

	// Page Load event
	function Page_Load() {

		//echo "Page Load";
	}

	// Page Unload event
	function Page_Unload() {

		//echo "Page Unload";
	}

	// Page Redirecting event
	function Page_Redirecting(&$url) {

		// Example:
		//$url = "your URL";

	}

	// Message Showing event
	// $type = ''|'success'|'failure'|'warning'
	function Message_Showing(&$msg, $type) {
		if ($type == 'success') {

			//$msg = "your success message";
		} elseif ($type == 'failure') {

			//$msg = "your failure message";
		} elseif ($type == 'warning') {

			//$msg = "your warning message";
		} else {

			//$msg = "your message";
		}
	}

	// Page Render event
	function Page_Render() {

		//echo "Page Render";
	}

	// Page Data Rendering event
	function Page_DataRendering(&$header) {

		// Example:
		//$header = "your header";

	}

	// Page Data Rendered event
	function Page_DataRendered(&$footer) {

		// Example:
		//$footer = "your footer";

	}

	// Form Custom Validate event
	function Form_CustomValidate(&$CustomError) {

		// Return error message in CustomError
		return TRUE;
	}
}
?>
<?php ew_Header(FALSE) ?>
<?php

// Create page object
if (!isset($images_add)) $images_add = new cimages_add();

// Page init
$images_add->Page_Init();

// Page main
$images_add->Page_Main();

// Global Page Rendering event (in userfn*.php)
Page_Rendering();

// Page Rendering event
$images_add->Page_Render();
?>
<?php include_once "header.php" ?>
<script type="text/javascript">

// Form object
var CurrentPageID = EW_PAGE_ID = "add";
var CurrentForm = fimagesadd = new ew_Form("fimagesadd", "add");

// Validate form
fimagesadd.Validate = function() {
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
			elm = this.GetElements("x" + infix + "_p_id");
			if (elm && !ew_IsHidden(elm) && !ew_HasValue(elm))
				return this.OnError(elm, "<?php echo ew_JsEncode2(str_replace("%s", $images->p_id->FldCaption(), $images->p_id->ReqErrMsg)) ?>");
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
	}

	// Process detail forms
	var dfs = $fobj.find("input[name='detailpage']").get();
	for (var i = 0; i < dfs.length; i++) {
		var df = dfs[i], val = df.value;
		if (val && ewForms[val])
			if (!ewForms[val].Validate())
				return false;
	}
	return true;
}

// Form_CustomValidate event
fimagesadd.Form_CustomValidate = 
 function(fobj) { // DO NOT CHANGE THIS LINE!

 	// Your custom validation code here, return false if invalid. 
 	return true;
 }

// Use JavaScript validation or not
<?php if (EW_CLIENT_VALIDATE) { ?>
fimagesadd.ValidateRequired = true;
<?php } else { ?>
fimagesadd.ValidateRequired = false; 
<?php } ?>

// Dynamic selection lists
fimagesadd.Lists["x_p_id"] = {"LinkField":"x_id","Ajax":true,"AutoFill":false,"DisplayFields":["x_title","","",""],"ParentFields":[],"ChildFields":[],"FilterFields":[],"Options":[],"Template":""};

// Form object for search
</script>
<script type="text/javascript">

// Write your client script here, no need to add script tags.
</script>
<div class="ewToolbar">
<?php $Breadcrumb->Render(); ?>
<?php echo $Language->SelectionForm(); ?>
<div class="clearfix"></div>
</div>
<?php $images_add->ShowPageHeader(); ?>
<?php
$images_add->ShowMessage();
?>
<form name="fimagesadd" id="fimagesadd" class="<?php echo $images_add->FormClassName ?>" action="<?php echo ew_CurrentPage() ?>" method="post">
<?php if ($images_add->CheckToken) { ?>
<input type="hidden" name="<?php echo EW_TOKEN_NAME ?>" value="<?php echo $images_add->Token ?>">
<?php } ?>
<input type="hidden" name="t" value="images">
<input type="hidden" name="a_add" id="a_add" value="A">
<div>
<?php if ($images->p_id->Visible) { // p_id ?>
	<div id="r_p_id" class="form-group">
		<label id="elh_images_p_id" for="x_p_id" class="col-sm-2 control-label ewLabel"><?php echo $images->p_id->FldCaption() ?><?php echo $Language->Phrase("FieldRequiredIndicator") ?></label>
		<div class="col-sm-10"><div<?php echo $images->p_id->CellAttributes() ?>>
<span id="el_images_p_id">
<select data-table="images" data-field="x_p_id" data-value-separator="<?php echo ew_HtmlEncode(is_array($images->p_id->DisplayValueSeparator) ? json_encode($images->p_id->DisplayValueSeparator) : $images->p_id->DisplayValueSeparator) ?>" id="x_p_id" name="x_p_id"<?php echo $images->p_id->EditAttributes() ?>>
<?php
if (is_array($images->p_id->EditValue)) {
	$arwrk = $images->p_id->EditValue;
	$rowswrk = count($arwrk);
	$emptywrk = TRUE;
	for ($rowcntwrk = 0; $rowcntwrk < $rowswrk; $rowcntwrk++) {
		$selwrk = ew_SameStr($images->p_id->CurrentValue, $arwrk[$rowcntwrk][0]) ? " selected" : "";
		if ($selwrk <> "") $emptywrk = FALSE;		
		if ($selwrk <> "" || $arwrk[$rowcntwrk][0] == "") {
?>
<option value="<?php echo ew_HtmlEncode($arwrk[$rowcntwrk][0]) ?>"<?php echo $selwrk ?>>
<?php echo $images->p_id->DisplayValue($arwrk[$rowcntwrk]) ?>
</option>
<?php
		}
	}
}
?>
</select>
<?php
$sSqlWrk = "SELECT `id`, `title` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `projects`";
$sWhereWrk = "";
$images->p_id->LookupFilters = array("s" => $sSqlWrk, "d" => "");
$images->p_id->LookupFilters += array("f0" => "`id` = {filter_value}", "t0" => "3", "fn0" => "");
$sSqlWrk = "";
$images->Lookup_Selecting($images->p_id, $sWhereWrk); // Call Lookup selecting
if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
if ($sSqlWrk <> "") $images->p_id->LookupFilters["s"] .= $sSqlWrk;
?>
<input type="hidden" name="s_x_p_id" id="s_x_p_id" value="<?php echo $images->p_id->LookupFilterQuery() ?>">
</span>
<?php echo $images->p_id->CustomMsg ?></div></div>
	</div>
<?php } ?>
<?php if ($images->image_name->Visible) { // image_name ?>
	<div id="r_image_name" class="form-group">
		<label id="elh_images_image_name" class="col-sm-2 control-label ewLabel"><?php echo $images->image_name->FldCaption() ?><?php echo $Language->Phrase("FieldRequiredIndicator") ?></label>
		<div class="col-sm-10"><div<?php echo $images->image_name->CellAttributes() ?>>
<span id="el_images_image_name">
<div id="fd_x_image_name">
<span title="<?php echo $images->image_name->FldTitle() ? $images->image_name->FldTitle() : $Language->Phrase("ChooseFile") ?>" class="btn btn-default btn-sm fileinput-button ewTooltip<?php if ($images->image_name->ReadOnly || $images->image_name->Disabled) echo " hide"; ?>">
	<span><?php echo $Language->Phrase("ChooseFileBtn") ?></span>
	<input type="file" title=" " data-table="images" data-field="x_image_name" name="x_image_name" id="x_image_name"<?php echo $images->image_name->EditAttributes() ?>>
</span>
<input type="hidden" name="fn_x_image_name" id= "fn_x_image_name" value="<?php echo $images->image_name->Upload->FileName ?>">
<input type="hidden" name="fa_x_image_name" id= "fa_x_image_name" value="0">
<input type="hidden" name="fs_x_image_name" id= "fs_x_image_name" value="200">
<input type="hidden" name="fx_x_image_name" id= "fx_x_image_name" value="<?php echo $images->image_name->UploadAllowedFileExt ?>">
<input type="hidden" name="fm_x_image_name" id= "fm_x_image_name" value="<?php echo $images->image_name->UploadMaxFileSize ?>">
</div>
<table id="ft_x_image_name" class="table table-condensed pull-left ewUploadTable"><tbody class="files"></tbody></table>
</span>
<?php echo $images->image_name->CustomMsg ?></div></div>
	</div>
<?php } ?>
<?php if ($images->image_detail->Visible) { // image_detail ?>
	<div id="r_image_detail" class="form-group">
		<label id="elh_images_image_detail" class="col-sm-2 control-label ewLabel"><?php echo $images->image_detail->FldCaption() ?><?php echo $Language->Phrase("FieldRequiredIndicator") ?></label>
		<div class="col-sm-10"><div<?php echo $images->image_detail->CellAttributes() ?>>
<span id="el_images_image_detail">
<?php ew_AppendClass($images->image_detail->EditAttrs["class"], "editor"); ?>
<textarea data-table="images" data-field="x_image_detail" name="x_image_detail" id="x_image_detail" cols="35" rows="4" placeholder="<?php echo ew_HtmlEncode($images->image_detail->getPlaceHolder()) ?>"<?php echo $images->image_detail->EditAttributes() ?>><?php echo $images->image_detail->EditValue ?></textarea>
<script type="text/javascript">
ew_CreateEditor("fimagesadd", "x_image_detail", 35, 4, <?php echo ($images->image_detail->ReadOnly || FALSE) ? "true" : "false" ?>);
</script>
</span>
<?php echo $images->image_detail->CustomMsg ?></div></div>
	</div>
<?php } ?>
</div>
<div class="form-group">
	<div class="col-sm-offset-2 col-sm-10">
<button class="btn btn-primary ewButton" name="btnAction" id="btnAction" type="submit"><?php echo $Language->Phrase("AddBtn") ?></button>
<button class="btn btn-default ewButton" name="btnCancel" id="btnCancel" type="button" data-href="<?php echo $images_add->getReturnUrl() ?>"><?php echo $Language->Phrase("CancelBtn") ?></button>
	</div>
</div>
</form>
<script type="text/javascript">
fimagesadd.Init();
</script>
<?php
$images_add->ShowPageFooter();
if (EW_DEBUG_ENABLED)
	echo ew_DebugMsg();
?>
<script type="text/javascript">

// Write your table-specific startup script here
// document.write("page loaded");

</script>
<?php include_once "footer.php" ?>
<?php
$images_add->Page_Terminate();
?>
