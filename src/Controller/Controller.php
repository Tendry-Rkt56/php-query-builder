<?php

namespace App\Controller;

class Controller
{

     public function __construct()
     {
          if (session_status() == PHP_SESSION_NONE) session_start();
     }

}