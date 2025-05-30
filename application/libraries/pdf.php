<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require_once FCPATH . 'vendor/autoload.php';

use Dompdf\Dompdf;

class Pdf extends Dompdf
{
    public function __construct()
    {
        parent::__construct();
    }
}
