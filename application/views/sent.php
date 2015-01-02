<!-- Notice, this file must be in charset: utf-8 -->
<?php
    $CI =& get_instance();         
    $msg_info = $CI->imap->message_info($inbox);
?>
<div class="container">

	<div class="row">
		<div class="col-md-3">
			<div class="top-side"><h2>Webmail</h2></div>
		</div>
		<div class="col-md-9">
		<div class="top-side">
			<a href="<? echo base_url();?>main/form_message/" class="btn btn-default loader">Compose mail</a>
			<form class="biv-btn" action="" method="post" enctype="multipart/form-data">
				<select name="msg_sort" class="form-control biv-btn" onchange="this.form.submit()">
					<option>Sort by date</option>
					<option value="1">Newest first</option>
					<option value="2">Oldest first</option>
				</select>
			</form>
				
		</div>
		</div>
		<div class="col-md-3">
			<ul class="nav">
			<li><a href="<? echo base_url();?>main/inbox/" class="loader">Inbox&nbsp;&#40;<?php echo $msg_info->Unread; ?>&#41;</a></li>
			<li><a href="<? echo base_url();?>main/sent/" class="loader">Sent Mail</a></li>
            <li><a href="<? echo base_url();?>main/do_logout/" class="loader">Log aut</a></li>
			</ul>
		</div>
		<div class="col-md-9">
		<div class="table-responsive">
        <?php echo form_open('main/delete_sent');?>
        <input class="btn btn-default btn-del" type="submit" name="submit" value="Delete selected email"/>
			<table class="table table-striped table-bordered">
				<thead>
					<tr>
						<th></th>
						<th>To</th>
						<th>Subject</th>
						<th>Date</th>
					</tr>
				</thead>
				<tbody>             
                    <?php
                    // Sort messages by date.
					$msg_id_sort = $this->input->post("msg_sort") ? $this->input->post("msg_sort") : 1;
                    $array_sort_messages = imap_sort($inbox, SORTDATE, $msg_id_sort);
					$num = imap_num_msg($inbox);
                    for ($i=0; $i<$num; $i++)
                    {
                        $headers[] = imap_header($inbox, $array_sort_messages[$i]);
						// If have unseen message, add CSS class unseen.
						$unseen = $CI->imap->check_unseen_msg($headers[$i]->Unseen);
						$address = $CI->imap->decode_utf8(isset($headers[$i]->fromaddress)? $headers[$i]->fromaddress : '');
                    ?>              
						<tr class="<?php echo $unseen; ?>">
							<td>
								<input type="checkbox" value="<?php echo $headers[$i]->Msgno; ?>" name="id[]"/>                                                      
							</td>
							<td><?php echo $address; ?></td>
							<td><a href="<? echo base_url();?>main/show_message/sent/<?php echo trim($headers[$i]->Msgno); ?>" class="loader" ><?php echo $CI->imap->decode_utf8(isset($headers[$i]->subject)? $headers[$i]->subject : 'no subject'); ?></a></td>
							<td><?php $date = new DateTime($headers[$i]->date); echo $date->format('d.m.Y H:i:s'); ?></td>
						</tr>                      
                    <?php
                    }
                        imap_close($inbox);
                    ?>
				</tbody>
			</table>		
            <?php echo form_close(); ?> 								
			</div>
		</div>
	</div>
</div>