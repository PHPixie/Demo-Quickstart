<?php

namespace Project\App\HTTP;

use PHPixie\AuthLogin\Providers\Password;
use PHPixie\HTTP\Request;
use PHPixie\Validate\Form;
use Project\App\ORM\User\UserRepository;
use PHPixie\App\ORM\User;

/**
 * Handles user sign in and registration
 */
class Auth extends Processor
{
    /**
     * Sign in page
     * @param Request $request HTTP request
     * @return mixed
     */
    public function defaultAction($request)
    {
        // If the user is already logged in redirect him to the frontpage
        if($this->user()) {
            return $this->redirect('app.frontpage');
        }

        $components = $this->components();

        // Initialize the template, login and registration forms
        $template = $components->template()->get('app:login', [
            'user' => $this->user()
        ]);

        $loginForm = $this->loginForm();
        $template->loginForm = $loginForm;


        // If the form was not submitted just render the page
        if($request->method() !== 'POST') {
            return $template;
        }

        $data = $request->data();

        // Otherwise process login
        $loginForm->submit($data->get());

        // If the login form is valid and the user successfully logged in then redirect to the frontpage
        if($loginForm->isValid() && $this->processLogin($loginForm)) {
            return $this->redirect('app.frontpage');
        }

        // If the login wasn't successful render the page again
        return $template;
    }

    /**
     * Process login
     *
     * @param Form $loginForm
     * @return bool If the user was successfully logged in
     */
    protected function processLogin($loginForm)
    {
        // Try to sign in the user
        $user = $this->passwordProvider()->login(
            $loginForm->email,
            $loginForm->password
        );

        // If the sign in was not successful add an error to the form and return false
        if($user === null) {
            $loginForm->result()->addMessageError("Invalid email or password");
            return false;
        }

        return true;
    }

    /**
     * Log the user out
     * @return mixed
     */
    public function logoutAction()
    {
        // Get auth domain and forget the user
        $domain = $this->components()->auth()->domain();
        $domain->forgetUser();

        // Then redirect to the frontpage
        return $this->redirect('app.frontpage');
    }

    /**
     * Build the login form
     * @return Form
     */
    protected function loginForm()
    {
        $validate = $this->components()->validate();
        $validator = $validate->validator();

        // We use a document validator (it's the one you'll be using the most)
        $document = $validator->rule()->addDocument();

        // Both fields are required
        $document->valueField('email')
            ->required("Email is required");

        $document->valueField('password')
            ->required("Password is required");

        // Wrap the validator inside a form
        return $validate->form($validator);
    }

    /**
     * The password login provider that we configured
     * /assets/config/auth.php
     * @return Password
     */
    protected function passwordProvider()
    {
        $domain = $this->components()->auth()->domain();
        return $domain->provider('password');
    }
}