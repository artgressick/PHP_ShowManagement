<?
	include('_controller.php');
	
	function sitm() { 
		global $BF,$info, $types, $messages;
?>
	<div class='innerbody'>
		<form action="" method="post" id="idForm" onsubmit="return error_check()">
			<table class="twoCol" id="twoCol" cellpadding="0" cellspacing="0" style='width:980px;'>
				<tr>
					<td class="tcleft">
						<div class="full_colHeader">Add/Edit Questions Below</div>
<?
		$q = "SELECT id,deleted,required,fieldtype_id,sort_order,question,options
			FROM pt_questions
			WHERE producttype_id = '".$info['producttype_id']."' AND sessiontype_id = '".$info['sessiontype_id']."' AND show_id = '".$_SESSION['show_id']."'
			ORDER BY sort_order,fieldtype_id,question";
		$results = db_query($q,"getting any questions");
?>
					<div id='questions' style='margin-bottom: 10px;'>
<?
		$i = 1;
		while($row = mysqli_fetch_assoc($results)) {
?>
										
						<table cellspacing="0" cellpadding="0" class='questions' id='question<?=$i?>'<?=($row['deleted']?" style='display:none;'":'')?>>
							<tr>
								<td class='lheader'><strong>Question <?=$i?></strong></td>
								<td class='loption'><input type='text' name='question<?=$i?>' id='question<?=$i?>' style='width: 325px;' value='<?=$row['question']?>' /></td>
								<td class='rheader'>Required Field</td>
								<td class='roption'><input type='checkbox' name='required<?=$i?>' id='required<?=$i?>'<?=($row['required'] == 1 && $row['fieldtype_id'] != 1 ? ' checked="checked"' : '').($row['fieldtype_id']==1?' disabled="disabled"':'')?>  /></td>
							</tr>
							<tr>
								<td class='lheader'>Answer Option Types:</td>
								<td class='loption'><select name='fieldtype_id<?=$i?>' id='fieldtype_id<?=$i?>' onchange='showOptions(this.value,<?=$i?>)'><?=str_replace('value="'.$row['fieldtype_id'].'"','value="'.$row['fieldtype_id'].'" selected="selected"',$types)?></td>
								<td class='rheader'>Display Order</td>
								<td class='roption'><input type='text' name='sort_order<?=$i?>' id='sort_order<?=$i?>' value='<?=$row['sort_order']?>' style='width: 25px;' /></td>
							</tr>
							<tr>
								<td colspan='4' id='options<?=$i?>' class='additional'>
									<div id='optionset<?=$i?>'>
										<?=$messages[$row['fieldtype_id']]?>
<?			
				if($row['options'] != "") {
?>
										<table id='optionsetTbl<?=$i?>' cellpadding="0" cellspacing="0">
<?					$tmp_options = explode('|||',$row['options']);
					$len = count($tmp_options);
					$k = 1;
					while($k <= $len) {
?>
											<tr>
												<td class='optionlabel'>Option <?=$k?>:</td>
												<td class='optionBox' id='optionBox<?=$i?>-<?=$k?>'><input type='text' name='optionval<?=$i?>-<?=$k?>' id='optionval<?=$i?>-<?=$k?>' value='<?=$tmp_options[$k-1]?>' /></td>
												<td class='optionExtra'><input type='button' id='removeOption<?=$i?>-<?=$k?>' onclick='javascript:eraseOption("<?=$i?>-<?=$k?>")' value='Remove Option' /></td>
											</tr>
<?						$k++;
					}
?>
										</table>
										</div><input type='hidden' name='optionval<?=$i?>' id='optionval<?=$i?>' value='<?=$len?>' /><div style='padding: 5px 10px;'><input type='button' onclick='javascript:newOption(<?=$i?>);' value='Add Another Option' /></div> 
<?
				}
?>
									</div>
								</td>
							</tr>
						</table>
						<input type="hidden" name="bDeleted<?=$i?>" id="bDeleted<?=$i?>" value="<?=($row['bDeleted']?'1':'0')?>" />
						<div style="text-align: right; padding-top:2px;"><input type='button' onclick="javascript:eraseQuestion(<?=$i?>);" id="addremove<?=$i?>" value='<?=($row['bDeleted']?'Re-Add Question '.$i:'Remove Question '.$i)?>' /></div>
						<input type="hidden" name="QID-<?=$i?>" value="<?=$row['id']?>" />
<?	
			$i++;
		} 
?>
						</div>
						<div><input type='button' value='Add New Question' onclick='javascript:addNew();' /></div>
					</td>
				</tr>
			</table>
			<div class='FormButtons'>
				<?=form_button(array('type'=>'submit','value'=>'Save Questions'))?>
				<?=form_text(array('type'=>'hidden','nocaption'=>'true','name'=>'id','value'=>$_REQUEST['id']))?>
				<input type='hidden' name='count' id='count' value='<?=--$i?>' />
			</div>
		</form>
	</div>

<?
	}
?>