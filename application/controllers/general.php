<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class General extends CI_Controller
{
    public function upload()
    {
        header('Content-type: application/json');

        $scope = $this->input->post('_scope') ? $this->input->post('_scope') : 'general';
        $name = $this->input->post('_name') ? $this->input->post('_name') : 'file';

        $valid_exts = array('jpeg', 'jpg', 'png', 'gif', 'pdf', 'zip', 'doc', 'docx', 'xls', 'xlsx');                                          // valid extensions
        $max_size = 10 * 1024 * 1024;                                                               // max file size (10MB)
        $rel_path = $scope.DIRECTORY_SEPARATOR;
        $abs_path = FCPATH.'uploads'.DIRECTORY_SEPARATOR.$rel_path; // upload directory
        $path = '';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (@is_uploaded_file($_FILES[$name]['tmp_name'])) {
                $ext = strtolower(pathinfo($_FILES[$name]['name'], PATHINFO_EXTENSION));         // get uploaded file extension

                if (in_array($ext, $valid_exts) and $_FILES[$name]['size'] < $max_size) {
                    // looking for format and size validity

                    $file = uniqid().'.'.$ext;

                    if (move_uploaded_file($_FILES[$name]['tmp_name'], $abs_path.$file)) {
                        // move uploaded file from temp to uploads directory

                        $status = 'ok';
                        $path = $rel_path.$file;
                        $msg = 'File Successfully Uploaded!';
                    } else {
                        $status = 'error';
                        $msg = 'Upload Fail: Unknown Error Occurred!';
                    }
                } else {
                    $status = 'error';
                    $msg = 'Upload Fail: Unsupported File Format or It is too large to Upload!';
                }
            } else {
                $status = 'error';
                $msg = 'Upload Fail: File Not Uploaded!';
            }
        } else {
            $status = 'error';
            $msg = 'Bad Request!';
        }

        echo json_encode(array('path' => $path, 'status' => $status, 'msg' => $msg));
    }
}
