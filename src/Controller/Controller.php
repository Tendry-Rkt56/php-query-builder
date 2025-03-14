<?php

namespace App\Controller;

class Controller
{

     public function __construct()
     {
          if (session_status() == PHP_SESSION_NONE) session_start();
     }

     protected function render(string $template = '', array $data = [])
     {
          extract($data);
          require_once '../templates/'.$template;
     }

}