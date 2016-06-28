<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Test extends CI_Controller
{
    public function index()
    {
        if (user_can('GENERATE_REPORT')) {
            print_r('OK');
        } else {
            echo 'Not ok';
        }
    }
}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */
