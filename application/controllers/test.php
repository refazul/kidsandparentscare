<?php

class Test extends CI_Controller
{
    public function index()
    {
        $this->load->view('header');
        $this->load->view('menu');
        $this->load->view('wrap_begin');

        $this->load->view('wrap_end');
        $this->load->view('footer');
    }
}
