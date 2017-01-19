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

        $registerForm = $this->registerForm();
        $template->registerForm = $registerForm;

        // If the form was not submitted just render the page
        if($request->method() !== 'POST') {
            return $template;
        }

        $data = $request->data();

        // If we are processing registration
        if($data->get('register')) {
            $registerForm->submit($data->get());

            // If the form is valid and the user was registered then redirect to the frontpage
            if ($registerForm->isValid() && $this->processRegister($registerForm)) {
                return $this->redirect('app.frontpage');
            }

        } else {
            // Otherwise process login
            $loginForm->submit($data->get());

            // If the login form is valid and the user successfully logged in then redirect to the frontpage
            if($loginForm->isValid() && $this->processLogin($loginForm)) {
                return $this->redirect('app.frontpage');
            }
        }

        // If there was no redirect just render the form
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
     * Process registration
     * @param Form $registerForm
     * @return bool Whether the user was successfully registered
     */
    protected function processRegister($registerForm)
    {
        /** @var UserRepository $userRepository */
        $userRepository = $this->components()->orm()->repository('user');

        // Check if the email already exists and if so add an error to the form
        if($userRepository->getByLogin($registerForm->email)) {
            $registerForm->result()->field('email')->addMessageError("This email is already taken");
            return false;
        }

        // Hash password and create the user
        $provider = $this->passwordProvider();

        $user = $userRepository->create([
            'name'  => $registerForm->name,
            'email' => $registerForm->email,
            'passwordHash' => $provider->hash($registerForm->password)
        ]);
        $user->save();

        // Manually log the user in
        $provider->setUser($user);

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
     * Build the registration form
     * @return Form
     */
    protected function registerForm()
    {
        $validate = $this->components()->validate();
        $validator = $validate->validator();
        $document = $validator->rule()->addDocument();

        // By default the validator won't allow any fields that were not defined.
        // This call turns off this validation and allows extra fields to be passed.
        // In our case the extra field is the hidden "register" field.
        $document->allowExtraFields();

        // Name is required and must be at least 3 characters long
        $document->valueField('name')
            ->required("Name is required")
            ->addFilter()
            ->minLength(3)
            ->message("Username must contain at least 3 characters");

        // Email is required and must be a valid email
        $document->valueField('email')
            ->required("Email is required")
            ->filter('email', "Please provide a valid email");

        $document->valueField('password')
            ->required("Password is required")
            ->addFilter()
            ->minLength(8)
            ->message("Password must contain at least 8 characters");

        $document->valueField('passwordConfirm')
            ->required("Please repeat your password");

        // In this callback rule we check that password confirmation matches the password
        $validator->rule()->callback(function($result, $value) {
            // If they don't match we add an error to the field
            if($value['password'] !== $value['passwordConfirm']) {
                $result->field('passwordConfirm')->addMessageError("Passwords don't match");
            }
        });

        // Build a form for this validator
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