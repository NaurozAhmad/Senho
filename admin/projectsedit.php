<?php
if (session_id() == "") session_start(); // Initialize Session data
ob_start(); // Turn on output buffering
?>
<?php include_once "ewcfg12.php" ?>
<?php include_once ((EW_USE_ADODB) ? "adodb5/adodb.inc.php" : "ewmysql12.php") ?>
<?php include_once "phpfn12.php" ?>
<?php include_once "projectsinfo.php" ?>
<?php include_once "imagesgridcls.php" ?>
<?php include_once "userfn12.php" ?>
<?php

//
// Page class
//

$projects_edit = NULL; // Initialize page object first

class cprojects_edit extends cprojects {

	// Page ID
	var $PageID = 'edit';

	// Project ID
	var $ProjectID = "{D635DDF5-EC98-4B6F-806D-28D8D9C856B8}";

	// Table name
	var $TableName = 'projects';

	// Page object name
	var $PageObjName = 'projects_edit';

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

		// Table object (projects)
		if (!isset($GLOBALS["projects"]) || get_class($GLOBALS["projects"]) == "cprojects") {
			$GLOBALS["projects"] = &$this;
			$GLOBALS["Table"] = &$GLOBALS["projects"];
		}

		// Page ID
		if (!defined("EW_PAGE_ID"))
			define("EW_PAGE_ID", 'edit', TRUE);

		// Table name (for backward compatibility)
		if (!defined("EW_TABLE_NAME"))
			define("EW_TABLE_NAME", 'projects', TRUE);

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
		$this->id->Visible = !$this->IsAdd() && !$this->IsCopy() && !$this->IsGridAdd();

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

			// Process auto fill for detail table 'images'
			if (@$_POST["grid"] == "fimagesgrid") {
				if (!isset($GLOBALS["images_grid"])) $GLOBALS["images_grid"] = new cimages_grid;
				$GLOBALS["images_grid"]->Page_Init();
				$this->Page_Terminate();
				exit();
			}
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
		global $EW_EXPORT, $projects;
		if ($this->CustomExport <> "" && $this->CustomExport == $this->Export && array_key_exists($this->CustomExport, $EW_EXPORT)) {
				$sContent = ob_get_contents();
			if ($gsExportFile == "") $gsExportFile = $this->TableVar;
			$class = $EW_EXPORT[$this->CustomExport];
			if (class_exists($class)) {
				$doc = new $class($projects);
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
	var $FormClassName = "form-horizontal ewForm ewEditForm";
	var $DbMasterFilter;
	var $DbDetailFilter;

	// 
	// Page main
	//
	function Page_Main() {
		global $objForm, $Language, $gsFormError;

		// Load key from QueryString
		if (@$_GET["id"] <> "") {
			$this->id->setQueryStringValue($_GET["id"]);
		}

		// Set up Breadcrumb
		$this->SetupBreadcrumb();

		// Process form if post back
		if (@$_POST["a_edit"] <> "") {
			$this->CurrentAction = $_POST["a_edit"]; // Get action code
			$this->LoadFormValues(); // Get form values

			// Set up detail parameters
			$this->SetUpDetailParms();
		} else {
			$this->CurrentAction = "I"; // Default action is display
		}

		// Check if valid key
		if ($this->id->CurrentValue == "")
			$this->Page_Terminate("projectslist.php"); // Invalid key, return to list

		// Validate form if post back
		if (@$_POST["a_edit"] <> "") {
			if (!$this->ValidateForm()) {
				$this->CurrentAction = ""; // Form error, reset action
				$this->setFailureMessage($gsFormError);
				$this->EventCancelled = TRUE; // Event cancelled
				$this->RestoreFormValues();
			}
		}
		switch ($this->CurrentAction) {
			case "I": // Get a record to display
				if (!$this->LoadRow()) { // Load record based on key
					if ($this->getFailureMessage() == "") $this->setFailureMessage($Language->Phrase("NoRecord")); // No record found
					$this->Page_Terminate("projectslist.php"); // No matching record, return to list
				}

				// Set up detail parameters
				$this->SetUpDetailParms();
				break;
			Case "U": // Update
				$this->SendEmail = TRUE; // Send email on update success
				if ($this->EditRow()) { // Update record based on key
					if ($this->getSuccessMessage() == "")
						$this->setSuccessMessage($Language->Phrase("UpdateSuccess")); // Update success
					if ($this->getCurrentDetailTable() <> "") // Master/detail edit
						$sReturnUrl = $this->GetViewUrl(EW_TABLE_SHOW_DETAIL . "=" . $this->getCurrentDetailTable()); // Master/Detail view page
					else
						$sReturnUrl = $this->getReturnUrl();
					$this->Page_Terminate($sReturnUrl); // Return to caller
				} else {
					$this->EventCancelled = TRUE; // Event cancelled
					$this->RestoreFormValues(); // Restore form values if update failed

					// Set up detail parameters
					$this->SetUpDetailParms();
				}
		}

		// Render the record
		$this->RowType = EW_ROWTYPE_EDIT; // Render as Edit
		$this->ResetAttrs();
		$this->RenderRow();
	}

	// Set up starting record parameters
	function SetUpStartRec() {
		if ($this->DisplayRecs == 0)
			return;
		if ($this->IsPageRequest()) { // Validate request
			if (@$_GET[EW_TABLE_START_REC] <> "") { // Check for "start" parameter
				$this->StartRec = $_GET[EW_TABLE_START_REC];
				$this->setStartRecordNumber($this->StartRec);
			} elseif (@$_GET[EW_TABLE_PAGE_NO] <> "") {
				$PageNo = $_GET[EW_TABLE_PAGE_NO];
				if (is_numeric($PageNo)) {
					$this->StartRec = ($PageNo-1)*$this->DisplayRecs+1;
					if ($this->StartRec <= 0) {
						$this->StartRec = 1;
					} elseif ($this->StartRec >= intval(($this->TotalRecs-1)/$this->DisplayRecs)*$this->DisplayRecs+1) {
						$this->StartRec = intval(($this->TotalRecs-1)/$this->DisplayRecs)*$this->DisplayRecs+1;
					}
					$this->setStartRecordNumber($this->StartRec);
				}
			}
		}
		$this->StartRec = $this->getStartRecordNumber();

		// Check if correct start record counter
		if (!is_numeric($this->StartRec) || $this->StartRec == "") { // Avoid invalid start record counter
			$this->StartRec = 1; // Reset start record counter
			$this->setStartRecordNumber($this->StartRec);
		} elseif (intval($this->StartRec) > intval($this->TotalRecs)) { // Avoid starting record > total records
			$this->StartRec = intval(($this->TotalRecs-1)/$this->DisplayRecs)*$this->DisplayRecs+1; // Point to last page first record
			$this->setStartRecordNumber($this->StartRec);
		} elseif (($this->StartRec-1) % $this->DisplayRecs <> 0) {
			$this->StartRec = intval(($this->StartRec-1)/$this->DisplayRecs)*$this->DisplayRecs+1; // Point to page boundary
			$this->setStartRecordNumber($this->StartRec);
		}
	}

	// Get upload files
	function GetUploadFiles() {
		global $objForm, $Language;

		// Get upload data
		$this->images->Upload->Index = $objForm->Index;
		$this->images->Upload->UploadFile();
		$this->images->CurrentValue = $this->images->Upload->FileName;
	}

	// Load form values
	function LoadFormValues() {

		// Load from form
		global $objForm;
		$this->GetUploadFiles(); // Get upload files
		if (!$this->id->FldIsDetailKey)
			$this->id->setFormValue($objForm->GetValue("x_id"));
		if (!$this->title->FldIsDetailKey) {
			$this->title->setFormValue($objForm->GetValue("x_title"));
		}
		if (!$this->intro->FldIsDetailKey) {
			$this->intro->setFormValue($objForm->GetValue("x_intro"));
		}
		if (!$this->full_intro->FldIsDetailKey) {
			$this->full_intro->setFormValue($objForm->GetValue("x_full_intro"));
		}
		if (!$this->details->FldIsDetailKey) {
			$this->details->setFormValue($objForm->GetValue("x_details"));
		}
		if (!$this->livelink->FldIsDetailKey) {
			$this->livelink->setFormValue($objForm->GetValue("x_livelink"));
		}
	}

	// Restore form values
	function RestoreFormValues() {
		global $objForm;
		$this->LoadRow();
		$this->id->CurrentValue = $this->id->FormValue;
		$this->title->CurrentValue = $this->title->FormValue;
		$this->intro->CurrentValue = $this->intro->FormValue;
		$this->full_intro->CurrentValue = $this->full_intro->FormValue;
		$this->details->CurrentValue = $this->details->FormValue;
		$this->livelink->CurrentValue = $this->livelink->FormValue;
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
		$this->id->setDbValue($rs->fields('id'));
		$this->title->setDbValue($rs->fields('title'));
		$this->images->Upload->DbValue = $rs->fields('images');
		$this->images->CurrentValue = $this->images->Upload->DbValue;
		$this->intro->setDbValue($rs->fields('intro'));
		$this->full_intro->setDbValue($rs->fields('full_intro'));
		$this->details->setDbValue($rs->fields('details'));
		$this->livelink->setDbValue($rs->fields('livelink'));
	}

	// Load DbValue from recordset
	function LoadDbValues(&$rs) {
		if (!$rs || !is_array($rs) && $rs->EOF) return;
		$row = is_array($rs) ? $rs : $rs->fields;
		$this->id->DbValue = $row['id'];
		$this->title->DbValue = $row['title'];
		$this->images->Upload->DbValue = $row['images'];
		$this->intro->DbValue = $row['intro'];
		$this->full_intro->DbValue = $row['full_intro'];
		$this->details->DbValue = $row['details'];
		$this->livelink->DbValue = $row['livelink'];
	}

	// Render row values based on field settings
	function RenderRow() {
		global $Security, $Language, $gsLanguage;

		// Initialize URLs
		// Call Row_Rendering event

		$this->Row_Rendering();

		// Common render codes for all row types
		// id
		// title
		// images
		// intro
		// full_intro
		// details
		// livelink

		if ($this->RowType == EW_ROWTYPE_VIEW) { // View row

		// id
		$this->id->ViewValue = $this->id->CurrentValue;
		$this->id->ViewCustomAttributes = "";

		// title
		$this->title->ViewValue = $this->title->CurrentValue;
		$this->title->ViewCustomAttributes = "";

		// images
		$this->images->UploadPath = "/uploads";
		if (!ew_Empty($this->images->Upload->DbValue)) {
			$this->images->ViewValue = $this->images->Upload->DbValue;
		} else {
			$this->images->ViewValue = "";
		}
		$this->images->ViewCustomAttributes = "";

		// intro
		$this->intro->ViewValue = $this->intro->CurrentValue;
		$this->intro->ViewCustomAttributes = "";

		// full_intro
		$this->full_intro->ViewValue = $this->full_intro->CurrentValue;
		$this->full_intro->ViewCustomAttributes = "";

		// details
		$this->details->ViewValue = $this->details->CurrentValue;
		$this->details->ViewCustomAttributes = "";

		// livelink
		$this->livelink->ViewValue = $this->livelink->CurrentValue;
		$this->livelink->ViewCustomAttributes = "";

			// id
			$this->id->LinkCustomAttributes = "";
			$this->id->HrefValue = "";
			$this->id->TooltipValue = "";

			// title
			$this->title->LinkCustomAttributes = "";
			$this->title->HrefValue = "";
			$this->title->TooltipValue = "";

			// images
			$this->images->LinkCustomAttributes = "";
			$this->images->HrefValue = "";
			$this->images->HrefValue2 = $this->images->UploadPath . $this->images->Upload->DbValue;
			$this->images->TooltipValue = "";

			// intro
			$this->intro->LinkCustomAttributes = "";
			$this->intro->HrefValue = "";
			$this->intro->TooltipValue = "";

			// full_intro
			$this->full_intro->LinkCustomAttributes = "";
			$this->full_intro->HrefValue = "";
			$this->full_intro->TooltipValue = "";

			// details
			$this->details->LinkCustomAttributes = "";
			$this->details->HrefValue = "";
			$this->details->TooltipValue = "";

			// livelink
			$this->livelink->LinkCustomAttributes = "";
			$this->livelink->HrefValue = "";
			$this->livelink->TooltipValue = "";
		} elseif ($this->RowType == EW_ROWTYPE_EDIT) { // Edit row

			// id
			$this->id->EditAttrs["class"] = "form-control";
			$this->id->EditCustomAttributes = "";
			$this->id->EditValue = $this->id->CurrentValue;
			$this->id->ViewCustomAttributes = "";

			// title
			$this->title->EditAttrs["class"] = "form-control";
			$this->title->EditCustomAttributes = "";
			$this->title->EditValue = ew_HtmlEncode($this->title->CurrentValue);
			$this->title->PlaceHolder = ew_RemoveHtml($this->title->FldCaption());

			// images
			$this->images->EditAttrs["class"] = "form-control";
			$this->images->EditCustomAttributes = "";
			$this->images->UploadPath = "/uploads";
			if (!ew_Empty($this->images->Upload->DbValue)) {
				$this->images->EditValue = $this->images->Upload->DbValue;
			} else {
				$this->images->EditValue = "";
			}
			if (!ew_Empty($this->images->CurrentValue))
				$this->images->Upload->FileName = $this->images->CurrentValue;
			if ($this->CurrentAction == "I" && !$this->EventCancelled) ew_RenderUploadField($this->images);

			// intro
			$this->intro->EditAttrs["class"] = "form-control";
			$this->intro->EditCustomAttributes = "";
			$this->intro->EditValue = ew_HtmlEncode($this->intro->CurrentValue);
			$this->intro->PlaceHolder = ew_RemoveHtml($this->intro->FldCaption());

			// full_intro
			$this->full_intro->EditAttrs["class"] = "form-control";
			$this->full_intro->EditCustomAttributes = "";
			$this->full_intro->EditValue = ew_HtmlEncode($this->full_intro->CurrentValue);
			$this->full_intro->PlaceHolder = ew_RemoveHtml($this->full_intro->FldCaption());

			// details
			$this->details->EditAttrs["class"] = "form-control";
			$this->details->EditCustomAttributes = "";
			$this->details->EditValue = ew_HtmlEncode($this->details->CurrentValue);
			$this->details->PlaceHolder = ew_RemoveHtml($this->details->FldCaption());

			// livelink
			$this->livelink->EditAttrs["class"] = "form-control";
			$this->livelink->EditCustomAttributes = "";
			$this->livelink->EditValue = ew_HtmlEncode($this->livelink->CurrentValue);
			$this->livelink->PlaceHolder = ew_RemoveHtml($this->livelink->FldCaption());

			// Edit refer script
			// id

			$this->id->HrefValue = "";

			// title
			$this->title->HrefValue = "";

			// images
			$this->images->HrefValue = "";
			$this->images->HrefValue2 = $this->images->UploadPath . $this->images->Upload->DbValue;

			// intro
			$this->intro->HrefValue = "";

			// full_intro
			$this->full_intro->HrefValue = "";

			// details
			$this->details->HrefValue = "";

			// livelink
			$this->livelink->HrefValue = "";
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
		if (!$this->title->FldIsDetailKey && !is_null($this->title->FormValue) && $this->title->FormValue == "") {
			ew_AddMessage($gsFormError, str_replace("%s", $this->title->FldCaption(), $this->title->ReqErrMsg));
		}
		if ($this->images->Upload->FileName == "" && !$this->images->Upload->KeepFile) {
			ew_AddMessage($gsFormError, str_replace("%s", $this->images->FldCaption(), $this->images->ReqErrMsg));
		}
		if (!$this->intro->FldIsDetailKey && !is_null($this->intro->FormValue) && $this->intro->FormValue == "") {
			ew_AddMessage($gsFormError, str_replace("%s", $this->intro->FldCaption(), $this->intro->ReqErrMsg));
		}
		if (!$this->full_intro->FldIsDetailKey && !is_null($this->full_intro->FormValue) && $this->full_intro->FormValue == "") {
			ew_AddMessage($gsFormError, str_replace("%s", $this->full_intro->FldCaption(), $this->full_intro->ReqErrMsg));
		}
		if (!$this->details->FldIsDetailKey && !is_null($this->details->FormValue) && $this->details->FormValue == "") {
			ew_AddMessage($gsFormError, str_replace("%s", $this->details->FldCaption(), $this->details->ReqErrMsg));
		}

		// Validate detail grid
		$DetailTblVar = explode(",", $this->getCurrentDetailTable());
		if (in_array("images", $DetailTblVar) && $GLOBALS["images"]->DetailEdit) {
			if (!isset($GLOBALS["images_grid"])) $GLOBALS["images_grid"] = new cimages_grid(); // get detail page object
			$GLOBALS["images_grid"]->ValidateGridForm();
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

	// Update record based on key values
	function EditRow() {
		global $Security, $Language;
		$sFilter = $this->KeyFilter();
		$conn = &$this->Connection();
		$this->CurrentFilter = $sFilter;
		$sSql = $this->SQL();
		$conn->raiseErrorFn = $GLOBALS["EW_ERROR_FN"];
		$rs = $conn->Execute($sSql);
		$conn->raiseErrorFn = '';
		if ($rs === FALSE)
			return FALSE;
		if ($rs->EOF) {
			$EditRow = FALSE; // Update Failed
		} else {

			// Begin transaction
			if ($this->getCurrentDetailTable() <> "")
				$conn->BeginTrans();

			// Save old values
			$rsold = &$rs->fields;
			$this->LoadDbValues($rsold);
			$this->images->OldUploadPath = "/uploads";
			$this->images->UploadPath = $this->images->OldUploadPath;
			$rsnew = array();

			// title
			$this->title->SetDbValueDef($rsnew, $this->title->CurrentValue, "", $this->title->ReadOnly);

			// images
			if (!($this->images->ReadOnly) && !$this->images->Upload->KeepFile) {
				$this->images->Upload->DbValue = $rsold['images']; // Get original value
				if ($this->images->Upload->FileName == "") {
					$rsnew['images'] = NULL;
				} else {
					$rsnew['images'] = $this->images->Upload->FileName;
				}
			}

			// intro
			$this->intro->SetDbValueDef($rsnew, $this->intro->CurrentValue, "", $this->intro->ReadOnly);

			// full_intro
			$this->full_intro->SetDbValueDef($rsnew, $this->full_intro->CurrentValue, "", $this->full_intro->ReadOnly);

			// details
			$this->details->SetDbValueDef($rsnew, $this->details->CurrentValue, "", $this->details->ReadOnly);

			// livelink
			$this->livelink->SetDbValueDef($rsnew, $this->livelink->CurrentValue, NULL, $this->livelink->ReadOnly);
			if (!$this->images->Upload->KeepFile) {
				$this->images->UploadPath = "/uploads";
				if (!ew_Empty($this->images->Upload->Value)) {
					$rsnew['images'] = ew_UploadFileNameEx(ew_UploadPathEx(TRUE, $this->images->UploadPath), $rsnew['images']); // Get new file name
				}
			}

			// Call Row Updating event
			$bUpdateRow = $this->Row_Updating($rsold, $rsnew);
			if ($bUpdateRow) {
				$conn->raiseErrorFn = $GLOBALS["EW_ERROR_FN"];
				if (count($rsnew) > 0)
					$EditRow = $this->Update($rsnew, "", $rsold);
				else
					$EditRow = TRUE; // No field to update
				$conn->raiseErrorFn = '';
				if ($EditRow) {
					if (!$this->images->Upload->KeepFile) {
						if (!ew_Empty($this->images->Upload->Value)) {
							$this->images->Upload->SaveToFile($this->images->UploadPath, $rsnew['images'], TRUE);
						}
					}
				}

				// Update detail records
				if ($EditRow) {
					$DetailTblVar = explode(",", $this->getCurrentDetailTable());
					if (in_array("images", $DetailTblVar) && $GLOBALS["images"]->DetailEdit) {
						if (!isset($GLOBALS["images_grid"])) $GLOBALS["images_grid"] = new cimages_grid(); // Get detail page object
						$EditRow = $GLOBALS["images_grid"]->GridUpdate();
					}
				}

				// Commit/Rollback transaction
				if ($this->getCurrentDetailTable() <> "") {
					if ($EditRow) {
						$conn->CommitTrans(); // Commit transaction
					} else {
						$conn->RollbackTrans(); // Rollback transaction
					}
				}
			} else {
				if ($this->getSuccessMessage() <> "" || $this->getFailureMessage() <> "") {

					// Use the message, do nothing
				} elseif ($this->CancelMessage <> "") {
					$this->setFailureMessage($this->CancelMessage);
					$this->CancelMessage = "";
				} else {
					$this->setFailureMessage($Language->Phrase("UpdateCancelled"));
				}
				$EditRow = FALSE;
			}
		}

		// Call Row_Updated event
		if ($EditRow)
			$this->Row_Updated($rsold, $rsnew);
		$rs->Close();

		// images
		ew_CleanUploadTempPath($this->images, $this->images->Upload->Index);
		return $EditRow;
	}

	// Set up detail parms based on QueryString
	function SetUpDetailParms() {

		// Get the keys for master table
		if (isset($_GET[EW_TABLE_SHOW_DETAIL])) {
			$sDetailTblVar = $_GET[EW_TABLE_SHOW_DETAIL];
			$this->setCurrentDetailTable($sDetailTblVar);
		} else {
			$sDetailTblVar = $this->getCurrentDetailTable();
		}
		if ($sDetailTblVar <> "") {
			$DetailTblVar = explode(",", $sDetailTblVar);
			if (in_array("images", $DetailTblVar)) {
				if (!isset($GLOBALS["images_grid"]))
					$GLOBALS["images_grid"] = new cimages_grid;
				if ($GLOBALS["images_grid"]->DetailEdit) {
					$GLOBALS["images_grid"]->CurrentMode = "edit";
					$GLOBALS["images_grid"]->CurrentAction = "gridedit";

					// Save current master table to detail table
					$GLOBALS["images_grid"]->setCurrentMasterTable($this->TableVar);
					$GLOBALS["images_grid"]->setStartRecordNumber(1);
					$GLOBALS["images_grid"]->p_id->FldIsDetailKey = TRUE;
					$GLOBALS["images_grid"]->p_id->CurrentValue = $this->id->CurrentValue;
					$GLOBALS["images_grid"]->p_id->setSessionValue($GLOBALS["images_grid"]->p_id->CurrentValue);
				}
			}
		}
	}

	// Set up Breadcrumb
	function SetupBreadcrumb() {
		global $Breadcrumb, $Language;
		$Breadcrumb = new cBreadcrumb();
		$url = substr(ew_CurrentUrl(), strrpos(ew_CurrentUrl(), "/")+1);
		$Breadcrumb->Add("list", $this->TableVar, "projectslist.php", "", $this->TableVar, TRUE);
		$PageId = "edit";
		$Breadcrumb->Add("edit", $PageId, $url);
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
if (!isset($projects_edit)) $projects_edit = new cprojects_edit();

// Page init
$projects_edit->Page_Init();

// Page main
$projects_edit->Page_Main();

// Global Page Rendering event (in userfn*.php)
Page_Rendering();

// Page Rendering event
$projects_edit->Page_Render();
?>
<?php include_once "header.php" ?>
<script type="text/javascript">

// Form object
var CurrentPageID = EW_PAGE_ID = "edit";
var CurrentForm = fprojectsedit = new ew_Form("fprojectsedit", "edit");

// Validate form
fprojectsedit.Validate = function() {
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
			elm = this.GetElements("x" + infix + "_title");
			if (elm && !ew_IsHidden(elm) && !ew_HasValue(elm))
				return this.OnError(elm, "<?php echo ew_JsEncode2(str_replace("%s", $projects->title->FldCaption(), $projects->title->ReqErrMsg)) ?>");
			felm = this.GetElements("x" + infix + "_images");
			elm = this.GetElements("fn_x" + infix + "_images");
			if (felm && elm && !ew_HasValue(elm))
				return this.OnError(felm, "<?php echo ew_JsEncode2(str_replace("%s", $projects->images->FldCaption(), $projects->images->ReqErrMsg)) ?>");
			elm = this.GetElements("x" + infix + "_intro");
			if (elm && !ew_IsHidden(elm) && !ew_HasValue(elm))
				return this.OnError(elm, "<?php echo ew_JsEncode2(str_replace("%s", $projects->intro->FldCaption(), $projects->intro->ReqErrMsg)) ?>");
			elm = this.GetElements("x" + infix + "_full_intro");
			if (elm && !ew_IsHidden(elm) && !ew_HasValue(elm))
				return this.OnError(elm, "<?php echo ew_JsEncode2(str_replace("%s", $projects->full_intro->FldCaption(), $projects->full_intro->ReqErrMsg)) ?>");
			elm = this.GetElements("x" + infix + "_details");
			if (elm && !ew_IsHidden(elm) && !ew_HasValue(elm))
				return this.OnError(elm, "<?php echo ew_JsEncode2(str_replace("%s", $projects->details->FldCaption(), $projects->details->ReqErrMsg)) ?>");

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
fprojectsedit.Form_CustomValidate = 
 function(fobj) { // DO NOT CHANGE THIS LINE!

 	// Your custom validation code here, return false if invalid. 
 	return true;
 }

// Use JavaScript validation or not
<?php if (EW_CLIENT_VALIDATE) { ?>
fprojectsedit.ValidateRequired = true;
<?php } else { ?>
fprojectsedit.ValidateRequired = false; 
<?php } ?>

// Dynamic selection lists
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
<?php $projects_edit->ShowPageHeader(); ?>
<?php
$projects_edit->ShowMessage();
?>
<form name="fprojectsedit" id="fprojectsedit" class="<?php echo $projects_edit->FormClassName ?>" action="<?php echo ew_CurrentPage() ?>" method="post">
<?php if ($projects_edit->CheckToken) { ?>
<input type="hidden" name="<?php echo EW_TOKEN_NAME ?>" value="<?php echo $projects_edit->Token ?>">
<?php } ?>
<input type="hidden" name="t" value="projects">
<input type="hidden" name="a_edit" id="a_edit" value="U">
<div>
<?php if ($projects->id->Visible) { // id ?>
	<div id="r_id" class="form-group">
		<label id="elh_projects_id" class="col-sm-2 control-label ewLabel"><?php echo $projects->id->FldCaption() ?></label>
		<div class="col-sm-10"><div<?php echo $projects->id->CellAttributes() ?>>
<span id="el_projects_id">
<span<?php echo $projects->id->ViewAttributes() ?>>
<p class="form-control-static"><?php echo $projects->id->EditValue ?></p></span>
</span>
<input type="hidden" data-table="projects" data-field="x_id" name="x_id" id="x_id" value="<?php echo ew_HtmlEncode($projects->id->CurrentValue) ?>">
<?php echo $projects->id->CustomMsg ?></div></div>
	</div>
<?php } ?>
<?php if ($projects->title->Visible) { // title ?>
	<div id="r_title" class="form-group">
		<label id="elh_projects_title" for="x_title" class="col-sm-2 control-label ewLabel"><?php echo $projects->title->FldCaption() ?><?php echo $Language->Phrase("FieldRequiredIndicator") ?></label>
		<div class="col-sm-10"><div<?php echo $projects->title->CellAttributes() ?>>
<span id="el_projects_title">
<textarea data-table="projects" data-field="x_title" name="x_title" id="x_title" cols="35" rows="4" placeholder="<?php echo ew_HtmlEncode($projects->title->getPlaceHolder()) ?>"<?php echo $projects->title->EditAttributes() ?>><?php echo $projects->title->EditValue ?></textarea>
</span>
<?php echo $projects->title->CustomMsg ?></div></div>
	</div>
<?php } ?>
<?php if ($projects->images->Visible) { // images ?>
	<div id="r_images" class="form-group">
		<label id="elh_projects_images" class="col-sm-2 control-label ewLabel"><?php echo $projects->images->FldCaption() ?><?php echo $Language->Phrase("FieldRequiredIndicator") ?></label>
		<div class="col-sm-10"><div<?php echo $projects->images->CellAttributes() ?>>
<span id="el_projects_images">
<div id="fd_x_images">
<span title="<?php echo $projects->images->FldTitle() ? $projects->images->FldTitle() : $Language->Phrase("ChooseFile") ?>" class="btn btn-default btn-sm fileinput-button ewTooltip<?php if ($projects->images->ReadOnly || $projects->images->Disabled) echo " hide"; ?>">
	<span><?php echo $Language->Phrase("ChooseFileBtn") ?></span>
	<input type="file" title=" " data-table="projects" data-field="x_images" name="x_images" id="x_images"<?php echo $projects->images->EditAttributes() ?>>
</span>
<input type="hidden" name="fn_x_images" id= "fn_x_images" value="<?php echo $projects->images->Upload->FileName ?>">
<?php if (@$_POST["fa_x_images"] == "0") { ?>
<input type="hidden" name="fa_x_images" id= "fa_x_images" value="0">
<?php } else { ?>
<input type="hidden" name="fa_x_images" id= "fa_x_images" value="1">
<?php } ?>
<input type="hidden" name="fs_x_images" id= "fs_x_images" value="5000">
<input type="hidden" name="fx_x_images" id= "fx_x_images" value="<?php echo $projects->images->UploadAllowedFileExt ?>">
<input type="hidden" name="fm_x_images" id= "fm_x_images" value="<?php echo $projects->images->UploadMaxFileSize ?>">
</div>
<table id="ft_x_images" class="table table-condensed pull-left ewUploadTable"><tbody class="files"></tbody></table>
</span>
<?php echo $projects->images->CustomMsg ?></div></div>
	</div>
<?php } ?>
<?php if ($projects->intro->Visible) { // intro ?>
	<div id="r_intro" class="form-group">
		<label id="elh_projects_intro" for="x_intro" class="col-sm-2 control-label ewLabel"><?php echo $projects->intro->FldCaption() ?><?php echo $Language->Phrase("FieldRequiredIndicator") ?></label>
		<div class="col-sm-10"><div<?php echo $projects->intro->CellAttributes() ?>>
<span id="el_projects_intro">
<textarea data-table="projects" data-field="x_intro" name="x_intro" id="x_intro" cols="35" rows="4" placeholder="<?php echo ew_HtmlEncode($projects->intro->getPlaceHolder()) ?>"<?php echo $projects->intro->EditAttributes() ?>><?php echo $projects->intro->EditValue ?></textarea>
</span>
<?php echo $projects->intro->CustomMsg ?></div></div>
	</div>
<?php } ?>
<?php if ($projects->full_intro->Visible) { // full_intro ?>
	<div id="r_full_intro" class="form-group">
		<label id="elh_projects_full_intro" class="col-sm-2 control-label ewLabel"><?php echo $projects->full_intro->FldCaption() ?><?php echo $Language->Phrase("FieldRequiredIndicator") ?></label>
		<div class="col-sm-10"><div<?php echo $projects->full_intro->CellAttributes() ?>>
<span id="el_projects_full_intro">
<?php ew_AppendClass($projects->full_intro->EditAttrs["class"], "editor"); ?>
<textarea data-table="projects" data-field="x_full_intro" name="x_full_intro" id="x_full_intro" cols="35" rows="4" placeholder="<?php echo ew_HtmlEncode($projects->full_intro->getPlaceHolder()) ?>"<?php echo $projects->full_intro->EditAttributes() ?>><?php echo $projects->full_intro->EditValue ?></textarea>
<script type="text/javascript">
ew_CreateEditor("fprojectsedit", "x_full_intro", 35, 4, <?php echo ($projects->full_intro->ReadOnly || FALSE) ? "true" : "false" ?>);
</script>
</span>
<?php echo $projects->full_intro->CustomMsg ?></div></div>
	</div>
<?php } ?>
<?php if ($projects->details->Visible) { // details ?>
	<div id="r_details" class="form-group">
		<label id="elh_projects_details" class="col-sm-2 control-label ewLabel"><?php echo $projects->details->FldCaption() ?><?php echo $Language->Phrase("FieldRequiredIndicator") ?></label>
		<div class="col-sm-10"><div<?php echo $projects->details->CellAttributes() ?>>
<span id="el_projects_details">
<?php ew_AppendClass($projects->details->EditAttrs["class"], "editor"); ?>
<textarea data-table="projects" data-field="x_details" name="x_details" id="x_details" cols="35" rows="4" placeholder="<?php echo ew_HtmlEncode($projects->details->getPlaceHolder()) ?>"<?php echo $projects->details->EditAttributes() ?>><?php echo $projects->details->EditValue ?></textarea>
<script type="text/javascript">
ew_CreateEditor("fprojectsedit", "x_details", 35, 4, <?php echo ($projects->details->ReadOnly || FALSE) ? "true" : "false" ?>);
</script>
</span>
<?php echo $projects->details->CustomMsg ?></div></div>
	</div>
<?php } ?>
<?php if ($projects->livelink->Visible) { // livelink ?>
	<div id="r_livelink" class="form-group">
		<label id="elh_projects_livelink" for="x_livelink" class="col-sm-2 control-label ewLabel"><?php echo $projects->livelink->FldCaption() ?></label>
		<div class="col-sm-10"><div<?php echo $projects->livelink->CellAttributes() ?>>
<span id="el_projects_livelink">
<input type="text" data-table="projects" data-field="x_livelink" name="x_livelink" id="x_livelink" size="40" placeholder="<?php echo ew_HtmlEncode($projects->livelink->getPlaceHolder()) ?>" value="<?php echo $projects->livelink->EditValue ?>"<?php echo $projects->livelink->EditAttributes() ?>>
</span>
<?php echo $projects->livelink->CustomMsg ?></div></div>
	</div>
<?php } ?>
</div>
<?php
	if (in_array("images", explode(",", $projects->getCurrentDetailTable())) && $images->DetailEdit) {
?>
<?php if ($projects->getCurrentDetailTable() <> "") { ?>
<h4 class="ewDetailCaption"><?php echo $Language->TablePhrase("images", "TblCaption") ?></h4>
<?php } ?>
<?php include_once "imagesgrid.php" ?>
<?php } ?>
<div class="form-group">
	<div class="col-sm-offset-2 col-sm-10">
<button class="btn btn-primary ewButton" name="btnAction" id="btnAction" type="submit"><?php echo $Language->Phrase("SaveBtn") ?></button>
<button class="btn btn-default ewButton" name="btnCancel" id="btnCancel" type="button" data-href="<?php echo $projects_edit->getReturnUrl() ?>"><?php echo $Language->Phrase("CancelBtn") ?></button>
	</div>
</div>
</form>
<script type="text/javascript">
fprojectsedit.Init();
</script>
<?php
$projects_edit->ShowPageFooter();
if (EW_DEBUG_ENABLED)
	echo ew_DebugMsg();
?>
<script type="text/javascript">

// Write your table-specific startup script here
// document.write("page loaded");

</script>
<?php include_once "footer.php" ?>
<?php
$projects_edit->Page_Terminate();
?>
