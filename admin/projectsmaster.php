<?php

// title
?>
<?php if ($projects->Visible) { ?>
<!-- <h4 class="ewMasterCaption"><?php echo $projects->TableCaption() ?></h4> -->
<table id="tbl_projectsmaster" class="table table-bordered table-striped ewViewTable">
	<tbody>
<?php if ($projects->title->Visible) { // title ?>
		<tr id="r_title">
			<td><?php echo $projects->title->FldCaption() ?></td>
			<td<?php echo $projects->title->CellAttributes() ?>>
<span id="el_projects_title">
<span<?php echo $projects->title->ViewAttributes() ?>>
<?php echo $projects->title->ListViewValue() ?></span>
</span>
</td>
		</tr>
<?php } ?>
	</tbody>
</table>
<?php } ?>
