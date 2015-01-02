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
			<?php if (validation_errors()) { ?>
			<div class="alert alert-danger">
				<button type="button" class="close" data-dismiss="alert">&times;</button>
				<strong><?php echo validation_errors(); ?></strong>
			</div>
			<?php } ?>
			<?php if ($info_message != '') { ?>
			<div class="alert alert-success">
				<button type="button" class="close" data-dismiss="alert">&times;</button>
				<strong><?php echo $info_message; ?></strong>
			</div>
			<?php } ?>
			<div class="biv-background">
				<?php echo form_open('main/sent_message', array('class' => 'blok-center'));?>
				  <div class="form-group">
					<label for="InputEmail1">Email address</label>
					  <input type="email" name="email" class="form-control" id="InputEmail1" placeholder="Email">
				  </div>
				  <div class="form-group">
					<label>Subject</label>
					  <input type="text" name="subject" class="form-control" placeholder="Subject">
					<label>Text</label>
					<textarea name="message" class="form-control" rows="3"></textarea>
				  </div>
				  <div class="form-group">
					  <input class="btn btn-default" type="submit" name="submit" value="Send">
					  <input type="reset" class="btn btn-default" value="Cancel">
				  </div> 
				<?php echo form_close(); ?>
			</div>
		</div>
	</div>
</div>