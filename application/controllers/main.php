<?php
    if(!defined('BASEPATH')) exit('No direct script access allowed');
    
    // This is controller.
    class Main extends CI_Controller 
	{
        
         public function __construct()
         {
            parent::__construct();        
         }
         
        // Show views page - "enter" and try to enter in own mail box.
        public function index()
        {         
            $this->load->view('heder');
            $this->load->view('enter');
            $this->load->view('footer');
        }
        // Checking validation on the page - "enter".
        public function validation()
        {
			$this->imap->validation();			
		}
		// Show inbox message.
        public function inbox()
        {           	    
			$this->imap->do_connect('INBOX', 'inbox', 0);			
        }
		// Show sent message on post servers.
        public function sent()
        {                 
            $s_host = $this->session->userdata( 'host' );
			
            // This is name folder - "Sent" for different servers.
            $folder_mail_ru = '&BB4EQgQ,BEAEMAQyBDsENQQ9BD0ESwQ1-';
            $folder_yandex_com = '&BB4EQgQ,BEAEMAQyBDsENQQ9BD0ESwQ1-';
    
            // To identify server, because the name of the folder "Sent" are different everywhere.
            if($s_host == 'mail.ru')
            {
				$this->imap->do_connect($folder_mail_ru, 'sent', 0);
            }
            else
            {
				$this->imap->do_connect($folder_yandex_com, 'sent', 0);
            }
        }
        // Delete inbox message.
        public function delete_inbox()
        {
			$this->imap->del_messageg($this->input->post("id"), 'INBOX', 'inbox');
        }
		// Delete sent message.      
        public function delete_sent()
        {
            $s_host = $this->session->userdata('host');
			
			$this->imap->del_messageg($this->input->post("id"), '&BB4EQgQ,BEAEMAQyBDsENQQ9BD0ESwQ1-', 'sent');
            
        }
		// Go to form for sent message.
        public function form_message()
        {
			$this->imap->form_message();
			//echo $this->input->post('user_login');
		}
		// Sent message.
        public function sent_message()
        {			
			$this->imap->sent_message();
		}
        // Show message.
        public function show_message()
        {
			$folder = $this->uri->segment(3);
			$id = $this->uri->segment(4);
			// Check which folder to open the message to view.
			if($folder != 'sent')
			{
				$this->imap->do_connect('INBOX', 'show', $id);
			}
			else
			{
				$this->imap->do_connect('&BB4EQgQ,BEAEMAQyBDsENQQ9BD0ESwQ1-', 'show', $id);
			}
        }
        // Log out
        public function do_logout()
        {                 
            $this->session->sess_destroy();
            redirect( '/', 'refresh' ); 
        }
    }
?>