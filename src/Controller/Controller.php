<?php

namespace App\Controller;

use App\App;

class Controller
{

     public function __construct(private App $app)
     {
          if (session_status() == PHP_SESSION_NONE) session_start();
     }

     protected function render(string $template = '', array $data = [])
     {
          extract($data);
          require_once '../templates/'.$template;
     }

     /**
     * @template T of object
     * @param class-string<T> $class
     * @return T
     */
     public function getEntity(string $class)
     {
          return $this->app->getEntity($class);
     }

}