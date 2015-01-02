<?php
    $CI =& get_instance();         
    $msg_info = $CI->imap->message_info($inbox);
?>
<div class="container">
	<div class="row">
		<div class="col-md-12">
			<div class="top-side"><h2>Webmail</h2></div>
		</div>
		<div class="col-md-3">
			<ul class="nav">
				<li><a href="<? echo base_url();?>main/inbox/" class="loader">Inbox&nbsp;&#40;<?php echo $msg_info->Unread; ?>&#41;</a></li>
				<li><a href="<? echo base_url();?>main/sent/" class="loader">Sent Mail</a></li>
				<li><a href="<? echo base_url();?>main/do_logout/" class="loader">Log aut</a></li>
			</ul>
		</div>
		<div class="col-md-9">
			<?php $headers[] = imap_header($inbox, $id); ?>
			<ul class="nav callout">
				<!-- ?????????????????????????? FROM/TO -->
				<li><label>From: &nbsp </label> <?php echo $CI->imap->decode_utf8(isset($headers[0]->fromaddress )? $headers[0]->fromaddress  : ''); ?></li>
				<li><label>Subject: &nbsp </label><?php echo $CI->imap->decode_utf8(isset($headers[0]->subject)? $headers[0]->subject : 'no subject'); ?></li>
				<li><label>Date: &nbsp </label><?php $date = new DateTime(@$headers[0]->date); echo $date->format('d.m.Y H:i:s'); ?></li>
			</ul>
			<?php imap_close($inbox); ?>
			<div class="message">
				<?php echo $CI->imap->getmsg($inbox , $id );?>
				<hr>
				<div id="show">
					<div>
						<p><a href="#">Hide email</a></p>
						<?php echo $CI->config->item('message_show'); ?>
					</div>
				</div>		
				<p><a href="#show">Show letter</a></p>
			</div>		
		</div>
	</div>
</div>