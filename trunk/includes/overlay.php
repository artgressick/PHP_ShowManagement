<? 	# To have multiple types of deletes available... including permDelete which is a permanent wipe from the DB!
	$postType = (isset($postType) && $postType != "" ? $postType : 'delete'); ?>
	<div id='overlaypage' class='overlaypage'>
		<div id='gray' class='gray'></div>
		<div id='message' class='message'>
			<div class='warning' id='warning'>
				<div class='red'>WARNING!!</div>
				<div class='body'>
					<div>You are about to remove: <br />
		
						Name: <span id='delName' style='color: blue;'></span><br />
						<input type='hidden' value='' id='idDel' name='idDel' />
						<input type='hidden' value='' id='chrKEY' name='chrKEY' />
						<input type='hidden' value='' id='tblName' name='tblName' />
						<input type='hidden' value='' id='tblcount' name='tblcount' />
					</div>
					<div style='margin-top: 20px; '><strong>Are you sure you want to do this? It cannot be undone!</strong><br />
						<input type='button' value='Yes' onclick="javascript:delItem('<?=$BF?>ajax_delete.php?postType=<?=$postType?>&amp;tbl=<?=$tableName?>&amp;user_id=<?=$_SESSION['user_id']?>&amp;id=');" /> &nbsp;&nbsp; <input type='button' value='No' onclick="javascript:revert();" />
					</div>
				</div>
			</div>
		</div>
	</div>
