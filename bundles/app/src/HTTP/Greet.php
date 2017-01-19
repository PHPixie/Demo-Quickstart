<?php

namespace Project\App\HTTP;

use PHPixie\HTTP\Request;

/**
 * Simple greeting web page
 */
class Greet extends Processor
{
    /**
     * Default action
     * @param Request $request HTTP request
     * @return mixed
     */
    public function defaultAction($request)
    {
        $template = $this->components()->template();

        $container = $template->get('app:greet');
        $container->message = "Have fun coding!";
        return $container;
    }
}