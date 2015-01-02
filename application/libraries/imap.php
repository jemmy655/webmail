<?php
if( ! defined( 'BASEPATH' ) ) exit( 'No direct script access allowed' );
    
class Imap 
{         
	// Open connect and get connect.
	function imap_open( $host, $mailbox, $username, $password )
	{
		return @imap_open( '{imap.'.$host.':993/imap/ssl}'.$mailbox, $username, $password );
	}
	// Checking validation on the page - "enter".
	function validation()
	{
		$CI =& get_instance();
		// Set rules for validation
		$CI->form_validation->set_rules('email', 'Email', 'required|valid_email');
		$CI->form_validation->set_rules('password', 'Password', 'required');
		
		// If was a mistake, go to page - "enter" and try again.
		if ($CI->form_validation->run() == FALSE)
		{
			$CI->load->view('heder');
			$CI->load->view('enter');
			$CI->load->view('footer');
		}
		// If all right, go to page - "inbox".
		else
		{
			// Get data from user.
			$username = $CI->input->post('email');
			$password = $CI->input->post('password');
			$host = $CI->input->post('select');
			
			// Set data to session.
			$session_data = array
			( 
				'email' => $username,
				'password' => $password,
				'host' => $host,
				'id' => 'true'
			);
			$CI->session->set_userdata($session_data);
			
			// Go to page - "inbox".
			redirect('main/inbox', 'refresh');
		}
	}
	// Check if have unseen message, add CSS class unseen.
	function check_unseen_msg($unseen)
	{
		if($unseen == 'U')
		{   
			$unseen = 'unseen';
		}
		else
		{
			$unseen = '';
		}
		return $unseen;
	}
	// Connect to mailbax.
	// $show - page for view
	// $folder - on post server
	// $id - id message
	function do_connect($folder, $show, $id)
	{
		$CI =& get_instance();
		
		$s_username = $CI->session->userdata('email');
		$s_password = $CI->session->userdata('password');
		$s_host = $CI->session->userdata('host');
		$s_id = $CI->session->userdata('id');
		
		$inbox = $this->imap_open($s_host, $folder, $s_username, $s_password);
		// Checking connect to server and checking user in session.
		if($inbox != FALSE && $s_id == 'true')
		{ 
			$data_array['inbox'] = $inbox;
			$data_array['host'] = $s_host;
			$data_array['id'] = $id;

			$CI->load->view( 'heder' );
			$CI->load->view( $show, $data_array );
			$CI->load->view( 'footer' );
		}
		else
		{
			imap_errors();
			imap_alerts();
			redirect( '/', 'refresh' );        
		}

	}
	// Delete message mai.ru and gmail.com.
	// $array - messages
	// $folder - on post server
	// $show - page for view
	function del_messageg($array, $folder, $show)
	{
		$CI =& get_instance();
		
		$s_username = $CI->session->userdata('email');
		$s_password = $CI->session->userdata('password');
		$s_host = $CI->session->userdata('host');
		$s_id = $CI->session->userdata('id');
		
		$inbox = $this->imap_open($s_host, $folder, $s_username, $s_password);
		// Checking connect to server and checking user in session.
		if($inbox != FALSE && $s_id == 'true')
		{         
			if (!empty($array))
			{ 
				if( ! empty($array) )
				{
					$n = count($array);
					for($i=0; $i < $n; $i++) {
						
						if($s_host == 'yandex.ru')
						{
							// Gmail/Yandex move to Trash.
							imap_mail_move($inbox, (int) $array[(int)$i], '&BCMENAQwBDsENQQ9BD0ESwQ1-');
							imap_expunge($inbox);
						}
						else
						{
							// Mail.ru delete messgage
							imap_delete($inbox, $array[$i]);              
							imap_expunge($inbox);
						}
					}
					imap_errors();
					imap_alerts();
					redirect( 'main/'.$show, 'refresh' );
				}
			}
			else
			{
				redirect( 'main/'.$show, 'refresh' );
			}
		}
		else
		{
			imap_errors();
			imap_alerts();
			redirect( '/', 'refresh' );        
		}
	}
	// Go to form for sent message.
	function form_message()
	{
		$CI =& get_instance();
		
		$s_username = $CI->session->userdata('email');
		$s_password = $CI->session->userdata('password');
		$s_host = $CI->session->userdata('host');
		$s_id = $CI->session->userdata('id');
		
		$inbox = $this->imap_open($s_host, 'INBOX', $s_username, $s_password);
		// Checking connect to server and checking user in session.
		if($inbox != FALSE && $s_id == 'true')
		{
			$data_array['info_message'] = '';
			$data_array['inbox'] = $inbox;
			$CI->load->view('heder');
			$CI->load->view('sentmaessage', $data_array);
			$CI->load->view('footer');
		}
		else
		{
			redirect( '/', 'refresh' );
		}
	}
	// Sent message.
	function sent_message()
	{		
		$CI =& get_instance();
	
		// Set rules for validation and get data.
		$CI->form_validation->set_rules('email', 'Email', 'required|valid_email');
		$CI->form_validation->set_rules('subject', 'Subject', 'required|max_length[20]');
		$CI->form_validation->set_rules('message', 'Message', 'required|max_length[999]');
		
		$s_username = $CI->session->userdata('email');
		$s_password = $CI->session->userdata('password');
		$s_host = $CI->session->userdata('host');
		$s_id = $CI->session->userdata('id');
		$inbox = $CI->imap->imap_open( $s_host, 'INBOX', $s_username, $s_password);
		
		// Checking form on validation.
		if ($CI->form_validation->run() == FALSE)
		{
			$data_array['info_message'] = '';
			$data_array['inbox'] = $inbox;
			$CI->load->view('heder');
			$CI->load->view('sentmaessage', $data_array);
			$CI->load->view('footer');
		}
		else
		{	
			// Checking connect to server and checking user in session.
			if($inbox != FALSE && $s_id == 'true')
			{
				$email = $CI->input->post('email');
				$subject = $CI->input->post('subject');
				$message = $CI->input->post('message');
				
				$CI->email->from('1482909@mail.ru', 'Игорь Башко');
				$CI->email->to($email); 
				$CI->email->subject($subject);
				$CI->email->message($message);	
				$CI->email->send();

				$data_array['info_message'] = 'The message sent sacsessful.';
				$data_array['inbox'] = $inbox;
				
				$CI->load->view('heder');
				$CI->load->view('sentmaessage', $data_array);
				$CI->load->view('footer');
			}
			else
			{
				redirect( '/', 'refresh' );
			}
		}
	}
	// Get message info.
	function message_info()
	{
		$CI =& get_instance();
		
		$s_username = $CI->session->userdata('email');
		$s_password = $CI->session->userdata('password');
		$s_host = $CI->session->userdata('host');
		$inbox = $this->imap_open($s_host, 'INBOX', $s_username, $s_password);
		if($inbox != FALSE)
		{
			return imap_mailboxmsginfo($inbox);
		}
		else
		{
			imap_errors();
			imap_alerts();
			redirect('/', 'refresh');        
		}
	}     
	// Convert to utf-8.
	function decode_utf8( $string )
	{            
		$return = '';
		$header = imap_mime_header_decode( $string );
		for( $i=0; $i<count( $header ); $i++ )
		{
			$charset = $header[ $i ]->charset;
			$text = $header[ $i ]->text;
			if ( $charset == 'default' )
			{
				$return .= $text;
			} 
			else
			{
				$return .= @iconv( $charset,'UTF-8', $text );
			}
		}
		return $return;
	}  
	// getmsg from - http://www.php.net/manual/en/function.imap-fetchstructure.php
	function getmsg($mbox,$mid) 
	{
		// input $mbox = IMAP stream, $mid = message id
		// output all the following:
		global $charset,$plainmsg,$attachments;
		$plainmsg = $charset = '';
		$attachments = array();
		
		// BODY
		$s = imap_fetchstructure($mbox,$mid);
		
		if (!@$s->parts)
		{  
			// simple
			$this->getpart($mbox,$mid,$s,0);  // pass 0 as part-number
		}
		else 
		{  
			// multipart: cycle through each part
			foreach ($s->parts as $partno0=>$p)
			{
				 $this->getpart($mbox,$mid,$p,$partno0+1);
			}
		}	
	}
	// getpart from - http://www.php.net/manual/en/function.imap-fetchstructure.php
	function getpart($mbox,$mid,$p,$partno)
	{
		// $partno = '1', '2', '2.1', '2.1.3', etc for multipart, 0 if simple
		global $plainmsg,$charset,$attachments;
		$CI =& get_instance();
		
		 // DECODE DATA
		$data = ($partno)?
			imap_fetchbody($mbox,$mid,$partno):  // multipart
			imap_body($mbox,$mid);  // simple
		// Any part may be encoded, even plain text messages, so check everything.
		if ($p->encoding==4)
		   $data = quoted_printable_decode($data);
		elseif ($p->encoding==3)
			$data = base64_decode($data);
			
		// PARAMETERS
		// get all parameters, like charset, filenames of attachments, etc.
		$params = array();
		if ($p->parameters)
			foreach ($p->parameters as $x)
			   $params[strtolower($x->attribute)] = $x->value;
		if (@$p->dparameters)
			foreach ($p->dparameters as $x)
			  $params[strtolower($x->attribute)] = $x->value;

		// ATTACHMENT
		// Any part with a filename is an attachment,
		// so an attached text file (type 0) is not mistaken as the message.
		if (@$params['filename'] || @$params['name']) 
		{
			// filename may be given as 'Filename' or 'Name' or both
			$filename = (@$params['filename'])? @$params['filename'] : @$params['name'];
			// filename may be encoded, so see imap_mime_header_decode()
			$attachments[$filename] = $data;  // this is a problem if two files have same name
		}

		// TEXT
		if ($p->type==0 && $data) 
		{
			// Messages may be split in different parts because of inline attachments,
			// so append parts together with blank row.
			if(strtolower($p->subtype)=='plain')
			{		
				$plainmsg.= trim($data) ."\n\n";		
				$str = '<pre>'.@iconv( $charset ,'UTF-8', $plainmsg ).'</pre>';
				$CI->config->set_item('message_show', $str);				
			}
			// Convert to UTF-8
			elseif($params['charset'] == 'windows-1251')
			{
				echo @iconv( $charset ,'UTF-8', $data );
			}
			// Convert to UTF-8
			elseif($params['charset'] == 'KOI8-R')
			{
				echo @mb_convert_encoding($data, 'UTF-8', 'KOI8-R');
			}
			else
			{
				echo $data ."<br><br>";// ECHO
			}
		}
		// EMBEDDED MESSAGE
		// Many bounce notifications embed the original message as type 2,
		// but AOL uses type 1 (multipart), which is not handled here.
		// There are no PHP functions to parse embedded messages,
		// so this just appends the raw source to the main message.
		elseif ($p->type==2 && $data) 
		{
			$plainmsg.= $data."\n\n";
		}

		// SUBPART RECURSION
		
		if (@$p->parts) 
		{
			foreach ($p->parts as $partno0=>$p2)
			   $this->getpart($mbox,$mid,$p2,$partno.'.'.($partno0+1));  // 1.2, 1.2.1, etc.
		}

	} 
}
?>