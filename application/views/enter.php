<!-- Notice, this file must be in charset: utf-8 -->
<div class="container">
<div class="wrap-form">
	<?php if(validation_errors()) { ?>
	<div class="alert alert-danger">
		<button type="button" class="close" data-dismiss="alert">&times;</button>
		<strong><?php echo validation_errors(); ?></strong>
	</div>
	<?php } ?>
	<?php echo form_open( 'main/validation', array('class' => 'form-signin'));?>
	<h2 class="form-signin-heading">Please sign in</h2>
	<input type="text" name="email" class="form-control" placeholder="Email address" required autofocus>
	<input type="password" name="password" class="form-control" placeholder="Password" required>
	<select name="select" class="form-control">
		<option>mail.ru</option>
		<option>yandex.ru</option>
	</select>
	<input class="btn btn-lg btn-primary btn-enter" id="enter" type="submit" name="submit" value="Sign in"/>
	<?php echo form_close(); ?> 
</div>   

</div> <!-- /container -->