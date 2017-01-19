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

    /**
     * Post a new message via AJAX
     *
     * @param Request $request HTTP request
     * @return mixed
     * @throws \Exception If the user is not logged in
     */
    public function postAction($request)
    {
        // Check if the user is logged in
        $user = $this->user();
        if($user === null) {
            throw new \Exception("User is not logged in");
        }

        // Get the form and load it with data
        $form = $this->postForm();
        $form->submit($request->data()->get());

        // If the form is invalid echo the error from the 'text' field and
        // return a HTTP 500 status so that jQuery knows an error happened
        if(!$form->isValid()) {
            $message = $form->fieldError('text');
            return $this->responses()->response($message, [], 500);
        }

        // Otherwise create a new message
        $date = new \DateTime();
        $message = $this->components()->orm()->createEntity('message', [
            'text'   => $form->text,
            'date'   => $date->format('Y-m-d H:i:s'),
            'userId' => $user->id()
        ]);

        $message->save();

        // Return message entity as simple object.
        // It will automatically be converted into JSON
        return $message->asObject(true);
    }

    /**
     * New message form
     *
     * @return Form
     */
    protected function postForm()
    {
        $validate = $this->components()->validate();

        // Create a new validator
        $validator = $validate->validator();

        // We use a document validator (it's the one you'll be using the most)
        $document = $validator->rule()->addDocument();

        // Define a single field 'text'
        $document->valueField('text')
            // Mark field as required and specify the error text if its missing
            ->required("Please enter something")

            // Add a filter and specify the error message if it doesn't pass.
            // Note that each filter can have multiple rules.
            ->addFilter()
            ->minLength(3)
            ->maxLength(144)
            ->message("Your message must be between 3 to 144 characters long");

        // Return a validator for this form
        return $validate->form($validator);
    }
}