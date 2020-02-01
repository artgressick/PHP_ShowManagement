<?
	include('_controller.php');
	
	function sitm() { 
		global $BF,$info;
?>
	<div class='innerbody'>
		<form action="" method="post" id="idForm" onsubmit="return error_check()">
			<table class="twoCol" id="twoCol" cellpadding="0" cellspacing="0" style='width:800px;'>
				<tr>
					<td class="tcleft">
				
						<div class="colHeader">Show Information</div>

						<?=form_text(array('caption'=>'Show Name','required'=>'true','name'=>'show_name','size'=>'30','maxlength'=>'150','value'=>$info['show_name']))?>
						<?=form_text(array('caption'=>'Start Date','required'=>'true','name'=>'start_date','size'=>'30','maxlength'=>'100','value'=>$info['start_date']))?>
						<?=form_text(array('caption'=>'End Date','required'=>'true','name'=>'end_date','size'=>'30','maxlength'=>'100','value'=>$info['end_date']))?>
						<?=form_text(array('caption'=>'Order Lock Date','required'=>'true','name'=>'lock_requests','size'=>'30','maxlength'=>'100','value'=>$info['lock_requests']))?>
						<?=form_text(array('caption'=>'Show Room Data To Presenters','required'=>'true','name'=>'show_room_data','size'=>'30','maxlength'=>'100','value'=>$info['show_room_data']))?>
						
						<?=form_text(array('caption'=>'Show Sign-off field to Presenters','required'=>'true','name'=>'show_signoff','size'=>'30','maxlength'=>'100','value'=>$info['show_signoff']))?>
					</td>
					<td class="tcgutter"></td>
					<td class="tcright">

						<div class="colHeader">Options</div>
<?					
						$status = db_query("SELECT id, status_name as name FROM show_status ORDER BY id","Getting Status");
?>
						<?=form_select($status,array('caption'=>'Show Status','required'=>'true','name'=>'status_id','value'=>$info['status_id']))?>
						
						<?=form_checkbox(array('type'=>'radio','caption'=>'Use Session Based Billing Information?','title'=>'No','name'=>'session_bill','id'=>'session_bill0','value'=>'0','required'=>'true','checked'=>(!$info['session_bill']?'true':'false')))?>&nbsp;&nbsp;&nbsp;

						<?=form_checkbox(array('type'=>'radio','title'=>'Yes','name'=>'session_bill','id'=>'session_bill1','value'=>'1','checked'=>($info['session_bill']?'true':'false')))?>

						<div class="colHeader">Main/Default Billing Information</div>
						
						<?=form_text(array('caption'=>'Name','name'=>'bill_name','size'=>'30','maxlength'=>'200','value'=>$info['bill_name']))?>
						<?=form_text(array('caption'=>'Address','name'=>'bill_address1','size'=>'30','maxlength'=>'200','value'=>$info['bill_address1']))?>
						<?=form_text(array('caption'=>'Address','nocaption'=>'true','name'=>'bill_address2','size'=>'30','maxlength'=>'200','value'=>$info['bill_address2']))?>
						<?=form_text(array('caption'=>'Address','nocaption'=>'true','name'=>'bill_address3','size'=>'30','maxlength'=>'200','value'=>$info['bill_address3']))?>
						
						<?=form_text(array('caption'=>'City / Local','name'=>'bill_local','size'=>'30','maxlength'=>'200','value'=>$info['bill_local']))?>
						<?=form_text(array('caption'=>'State','name'=>'bill_state','size'=>'30','maxlength'=>'200','value'=>$info['bill_state']))?>
						<?=form_text(array('caption'=>'Postal','name'=>'bill_postal','size'=>'30','maxlength'=>'200','value'=>$info['bill_postal']))?>
						<?=form_text(array('caption'=>'Country','name'=>'bill_country','size'=>'30','maxlength'=>'200','value'=>$info['bill_country']))?>
						<?=form_text(array('caption'=>'Phone','name'=>'bill_phone','size'=>'30','maxlength'=>'200','value'=>$info['bill_phone']))?>
						<?=form_text(array('caption'=>'Fax','name'=>'bill_fax','size'=>'30','maxlength'=>'200','value'=>$info['bill_fax']))?>
						<?=form_text(array('caption'=>'E-mail Address','name'=>'bill_email','size'=>'30','maxlength'=>'200','value'=>$info['bill_email']))?>

					</td>
				</tr>
				<tr>
					<td colspan="3">

									<div class="colHeader">Landing Page</div>
						
						<?=form_textarea(array('caption'=>'Landing Page','name'=>'landing_page','cols'=>'75','rows'=>'30','style'=>'width:100%;height:389px;','value'=>$info['landing_page']))?>
						
					</td>
				</tr>
			</table>
			<div class='FormButtons'>
				<?=form_button(array('type'=>'submit','value'=>'Update Information'))?>
				<?=form_text(array('type'=>'hidden','nocaption'=>'true','name'=>'key','value'=>$_REQUEST['key']))?>
			</div>
		</form>
	</div>

<?
	}
?>