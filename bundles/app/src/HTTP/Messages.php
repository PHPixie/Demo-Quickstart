<?php

namespace Project\App\HTTP;

use PHPixie\HTTP\Request;
use PHPixie\Validate\Form;

/**
 * Listing and posting messages.
 * Remember to register it in \Project\App\HTTP
 */
class Messages extends Processor
{
    /**
     * Display messages
     *
     * @param Request $request HTTP request
     * @return mixed
     */
    public function defaultAction($request)
    {
        $components = $this->components();

        // Find all messages
        $messages = $components->orm()->query('message')
            ->orderDescendingBy('date')
            ->find();

        // Pass the data into a template and return it
        return $components->template()->get('app:messages', [
            'messages' => $messages
        ]);
    }
}