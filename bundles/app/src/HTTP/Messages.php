<?php

namespace Project\App\HTTP;

use PHPixie\HTTP\Request;
use PHPixie\Validate\Form;

/**
 * Listing and posting messages
 */
class Messages extends Processor
{
    /**
     * Display latest messages
     *
     * @param Request $request HTTP request
     * @return mixed
     */
    public function defaultAction($request)
    {
        $components = $this->components();

        // Create an ORM query for massages
        $messageQuery = $components->orm()->query('message')
            ->orderDescendingBy('date');

        // Load the query inside a pager.
        // We also specify the page size and which relationships to preload
        $pager = $components->paginateOrm()
            ->queryPager($messageQuery, 10, ['user']);

        // Set the current page from the route parameter
        $page = $request->attributes()->get('page', 1);
        $pager->setCurrentPage($page);

        // Pass the data into a template and return it
        return $components->template()->get('app:messages', [
            'pager' => $pager,
            'user'  => $this->user()
        ]);
    }
}