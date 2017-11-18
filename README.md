We will be creating a simple message posting site with user authorization, registration, forms, social login and console 
commands. This may seem like a lot to learn at once, this is why this tutorial is split into separate commits, and at each
stage you will have a completely functioning website. You can see the entire commit history here:
https://github.com/PHPixie/Demo-Quickstart/commits/master and now, let's begin:

##1. Creating a project
> Before starting with this site come say "Hello" in our [chat](https://gitter.im/PHPixie/Hotline), 99% of all the problems
that you might encounter get solved there in seconds.

First you need to get composer [Composer](https://getcomposer.org/download/), after it installs run:

```
php composer.phar create-project phpixie/project
```

This will create a *project* directory with the application skeleton and a single 'app' bundle. Bundles are collections of
code, assets, configs and templates that represent some part of your application. They are easily portable between projects 
composer. We will be working with only a single bundle that will contain the entire application.

If you are on Windows you will see an error during *create-project*, it's so because the PHP `symlink` function is not
supported on windows. Just create a symlink from *web/bundles/app* to *bundles/app/web* manually and carry on. Later in
this guide we will also show another way of dealing with this problem.

Next you need to confiugure your webserver and point it at the */web* folder inside the project. If you are using Apache
make sure you have the `rewrite` module enabled, on Nginx you will need a configuration similar to this:

```
server {
    listen *:80;
    server_name localhost;
    root      /path_to_project/web/;
    location / {
        try_files $uri $uri/ /index.php$is_args$args;
    }

    location ~ ^/.+\.php(/|$) {
        try_files $uri /index.php =404;
        fastcgi_split_path_info ^(.+\.php)(/.+)$;
        
        # or unix:/var/run/php5-fpm.sock if you use PHP 5
        fastcgi_pass unix:/var/run/php7-fpm.sock;
        
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
        include fastcgi_params;
    }
}
```
If everything went smooth upon visitng *http://localhost/* you should see a greeting. Also test that URL rewriting works
properly by checking *http://localhost/greet*, you should see the same exact page.

**[Project state at this stage (Commit 1)](https://github.com/PHPixie/Demo-Quickstart/tree/8702c5a5f732540d973770edb3604fa719aadef4)**


##2. Viewing messages

Let's start with connectiing to the database, for that we edit the */assets/config/database.php* file. To test the
connection, run these two commands from the project folder:

```
./console framework:database drop   # drops the database if it exists
./console framework:database create # creates the database if it doesn't exist
```

Next we create a migration with out database structure in */assets/migrate/migrations/1_users_and_messages.sql*.
Migrations make it easy for you to apply changes to the database without having to touch them manually.

```php
CREATE TABLE users(
  id INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
  name VARCHAR(255) NOT NULL,
  email VARCHAR(255) UNIQUE,
  passwordHash VARCHAR(255)
);

-- statement

CREATE TABLE messages(
  id INT PRIMARY KEY AUTO_INCREMENT,
  userId INT NOT NULL,
  text VARCHAR(255) NOT NULL,
  date DATETIME NOT NULL,

  FOREIGN KEY (userId)
      REFERENCES users(id)
);
```

Note that we use `-- statement` to separate the queries.

Now let's add some data to fill the database, for that we create files in */assets/migrate/seeds/* folder, with the file
names matching the names of database tables, e.g.:

```php
// /assets/migrate/seeds/messages.php

return [
    [
        'id'     => 1,
        'userId' => 1,
        'text'   => "Hello World!",
        'date'   => '2016-12-01 10:15:00'
    ],
    // ....
]
```

You can see the full content of these files in the repository. Now let's run two more commands:

```
./console framework:migrate  # apply migrations to the database
./console framework:seed     # insert seed data
```

Now we can start with our first web page.First let's take a look at */bundles/app/assets/config/routeResolver.php* that
contains route configuration. Routes control which URLs get handled by which processors. We are going to add a new
processor *messages* that will take care of displaying our messages. Let's define it as the default one and also add
a shortcut route for the frontpage:

```php
return array(
    'type'      => 'group',
    'defaults'  => array('action' => 'default'),
    'resolvers' => array(
        
        'action' => array(
            'path' => '<processor>/<action>'
        ),

        'processor' => array(
            'path'     => '(<processor>)',
            'defaults' => array('processor' => 'messages')
        ),

        // Frontpage route
        'frontpage' => array(
            'path' => '',
            'defaults' => ['processor' => 'messages']
        )
    )
);
```

Next, the main HTML layout. We start with the general parent layout */bundles/app/assets/template/layout.php* by including
Bootstrap 4 and our own `main.css` file.

```php
<!DOCTYPE html>
<html lang="en">
<head>
	<!-- Bootstrap 4 -->
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
	<meta http-equiv="x-ua-compatible" content="ie=edge">
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-alpha.6/css/bootstrap.min.css">

	<!-- Our own CSS, about that later -->
	<link rel="stylesheet" href="/bundles/app/main.css">

	<!-- If a child layout doesn't set the page title, we just use 'Quickstart' -->        
	<title><?=$_($this->get('pageTitle', 'Quickstart'))?></title>
</head>
<body>

<!-- Navigation -->
<nav class="navbar navbar-toggleable-md navbar-light bg-faded">
	<div class="container">

	    <!-- Link to the frontpage -->   
		<a class="navbar-brand  mr-auto" href="<?=$this->httpPath('app.frontpage')?>">Quickstart</a>
	</div>
</nav>

<!-- Here is where the child template content will be inserted -->   
<?php $this->childContent(); ?>


<!-- Bootstrap dependencies -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/tether/1.3.7/js/tether.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-alpha.6/js/bootstrap.min.js"></script>

</body>
</html>
```

Where to create the *main.css* file. Since we want to keep all the relevant files inside the bundle, it will be the
*/bundles/app/web/* folder. When Composer creates the project it will automatically create a symlink from
*/web/bundles/app* to this folder. As mentioned this doesn't work on Windows, so you can either create the symlink 
manually, or use the *framework:installWebAssets* command's *copy* option to copy the folder instead of linking to it.

```
# copies files from bundles' web folder to /web/bundles
./console framework:installWebAssets --copy 
```

Now for the actual code, we create a new processor in */bundles/app/src/HTTP/Messages.php*:

```php
namespace Project\App\HTTP;

use PHPixie\HTTP\Request;

/**
 * Lists the messages
 */
class Messages extends Processor
{
    /**
     * @param Request $request HTTP request
     * @return mixed
     */
    public function defaultAction($request)
    {
        $components = $this->components();

        // Get all the messages
        $messages = $components->orm()->query('message')
            ->orderDescendingBy('date')
            ->find();

        // Render the template
        return $components->template()->get('app:messages', [
            'messages' => $messages
        ]);
    }
}
```

**Important: don't forget to register it in /bundles/app/src/HTTP.php**:

```php
namespace Project\App;

class HTTP extends \PHPixie\DefaultBundle\HTTP
{
    // maps processor names to their classes
    protected $classMap = array(
        'messages' => 'Project\App\HTTP\Messages'
    );
}
```
Almost done, we just need to create the *app:messages* template that the processor uses, that's the easiest part:

```php
<?php
// Define parent layout
$this->layout('app:layout');

// Set the pageTitle variable
// that is used by the parent template uses to display page title
$this->set('pageTitle', "Messages");
?>

<div class="container content">
    <!-- Display the messages -->
    <?php foreach($messages as $message): ?>

        <blockquote class="blockquote">
            <!-- You should output text using $_() to prevent XSS attacks -->
            <p class="mb-0"><?=$_($message->text)?></p>
            <footer class="blockquote-footer">
                posted at <?=$this->formatDate($message->date, 'j M Y, H:i')?>
            </footer>
        </blockquote>

    <?php endforeach; ?>
</div>
```

And done, now by visiting http://localhost/ we should see all our messages from the database.

**[Project state at this stage (Commit 2)](https://github.com/PHPixie/Demo-Quickstart/tree/361acb0dacfe5e3a89a58400420292dad2acbe3a)**

##3. ORM relationships and pagination

To display the name of the user who created the message we need to define a relationship between the tables. In our
migration we defined a *userId* field for our messages, so this will be a One-To-Many relationship.

```php
// bundles/app/assets/config/orm.php

return [
    'relationships' => [
        // Each user can have multiple messages
        [
            'type'  => 'oneToMany',
            'owner' => 'user',
            'items' => 'message'
        ]
    ]
];
```

To page the results let's add an optional *page* parameter to our route:

```php
// /bundles/app/assets/config/routeResolver.php

return array(
    // ....
    'resolvers' => array(
        'messages' => array(
            'path' => 'page(/<page>)',
            'defaults' => ['processor' => 'messages']
        ),
       // ....
    )
);
```

And now slightly change the *Message* processor:

```php
public function defaultAction($request)
{
    $components = $this->components();

    // create an ORM query for messages
    $messageQuery = $components->orm()->query('message')
        ->orderDescendingBy('date');

    // Pass the query to the pager while also specifying
    // the amount of messages per page and which relationships to preload
    $pager = $components->paginateOrm()
        ->queryPager($messageQuery, 10, ['user']);

    // Set the current page number based on the URL parameter
    $page = $request->attributes()->get('page', 1);
    $pager->setCurrentPage($page);

    // And render the template
    return $components->template()->get('app:messages', [
        'pager' => $pager
    ]);
}
```

Now we can use `$pager->getCurrentItems()` in the template to get just the messages for the current page,
and `$message->user()` to get the user who created the message. There is no point of copying the entire *app:messages*
template here again, you can see it in the repository.

**[Project state at this stage(Commit 3)](https://github.com/PHPixie/Demo-Quickstart/tree/a3cd3aa05d79db09b54a1a9ff49feba998d51a88)**

##4. User authentication

Before letting our users to post their own messages we need to authenticate them. For that we need to extend the *user*
entity and repository classes. An important distinction is that an Entity represents a single user, while a Repository
provides ways of searching and creating these entities. To enable password authentication we need to implement special
interfaces, it's actually pretty simple:

```php
// /bundles/app/src/ORM/User.php
namespace Project\App\ORM;

use Project\App\ORM\Model\Entity;
/** This interface allows authorization using a password */
use PHPixie\AuthLogin\Repository\User as LoginUser;

/**
 * User Entity
 */
class User extends Entity implements LoginUser
{
    /**
     * Returns the user's password hash.
     * In our case it's just his 'passwordHash' field.
     * @return string|null
     */
    public function passwordHash()
    {
        return $this->getField('passwordHash');
    }
}
```

Now the repository:

```php
namespace Project\App\ORM\User;

use Project\App\ORM\Model\Repository;
use Project\App\ORM\User;
/** This interface allows authorization using a password */
use PHPixie\AuthLogin\Repository as LoginUserRepository;

/**
 * User Repository
 */
class UserRepository extends Repository implements LoginUserRepository
{
    /**
     * Finds a user by his id
     * @param mixed $id
     * @return User|null
     */
    public function getById($id)
    {
        return $this->query()
            ->in($id)
            ->findOne();
    }
    /**
     * Searches for a user by something that is considered his login.
     * In our case it is his email, but you can also search by multiple fields
     * to allow login with both email and username, etc.
     * @param mixed $login
     * @return User|null
     */
    public function getByLogin($login)
    {
        return $this->query()
            ->where('email', $login)
            ->findOne();
    }
}
```

**Important: don't forget to register these classes in /bundles/app/src/ORM.php**

```php
namespace Project\App;

/**
 * Here we define our wrapper classes
 */
class ORM extends \PHPixie\DefaultBundle\ORM
{
    protected $entityMap = array(
        'user' => 'Project\App\ORM\User'
    );

    protected $repositoryMap = [
        'user' => 'Project\App\ORM\User\UserRepository'
    ];
}
```

Now let's configure authentication in  */assets/config/auth.php*:

```php
// /assets/config/auth.php
return [
    'domains' => [
        'default' => [

            // use the ORM user repository
            'repository' => 'framework.orm.user',

            // Here we define the ways with which a user can authenticate
            'providers'  => [

                // Enable session support
                'session' => [
                    'type' => 'http.session'
                ],

                // And password login
                'password' => [
                    'type' => 'login.password',

                    // When a password login is successful persist the user in the session
                    'persistProviders' => ['session']
                ]
            ]
        ]
    ]
];
```

Now the actual processor for the login page:

```php
namespace Project\App\HTTP;

use PHPixie\AuthLogin\Providers\Password;
use PHPixie\HTTP\Request;
use PHPixie\Validate\Form;
use Project\App\ORM\User\UserRepository;
use PHPixie\App\ORM\User;

/**
 * User authentication
 */
class Auth extends Processor
{
    /**
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

        // Build the template and the form
        $template = $components->template()->get('app:login', [
            'user' => $this->user()
        ]);

        $loginForm = $this->loginForm();
        $template->loginForm = $loginForm;


        // If the form was not submitted then just render the template
        if($request->method() !== 'POST') {
            return $template;
        }

        $data = $request->data();

        // Otherwise process the login
        $loginForm->submit($data->get());

        // If the form is valid and the user logged in successfully redirect him top the frontpage
        if($loginForm->isValid() && $this->processLogin($loginForm)) {
            return $this->redirect('app.frontpage');
        }

        // Otherwise just render the page
        return $template;
    }

    /**
     * Login processing
     *
     * @param Form $loginForm
     * @return bool Whether the user has logged in successfully
     */
    protected function processLogin($loginForm)
    {
        // Attempt to login the user
        $user = $this->passwordProvider()->login(
            $loginForm->email,
            $loginForm->password
        );

        // If the password was wrong or the user doesn't exist then add an error to the form
        if($user === null) {
            $loginForm->result()->addMessageError("Invalid email or password");
            return false;
        }

        return true;
    }

    /**
     * Logout
     * @return mixed
     */
    public function logoutAction()
    {
        // Get the auth domain and log the user out
        $domain = $this->components()->auth()->domain();
        $domain->forgetUser();

        // Then redirect him back to the frontpage
        return $this->redirect('app.frontpage');
    }

    /**
     * Build login form
     * @return Form
     */
    protected function loginForm()
    {
        $validate = $this->components()->validate();
        $validator = $validate->validator();

        // We use the document validator,
        // it's the one you will be using in most cases
        $document = $validator->rule()->addDocument();

        // Both fields are required
        $document->valueField('email')
            ->required("Email is required");

        $document->valueField('password')
            ->required("Password is required");

        // Return the form for this validator
        return $validate->form($validator);
    }

    /**
     * 'password' auth provider that we configured in /assets/config/auth.php
     * @return Password
     */
    protected function passwordProvider()
    {
        $domain = $this->components()->auth()->domain();
        return $domain->provider('password');
    }
}
```

Now all that's left is to create the HTML template, instead of copy pasting the entire code here let's look at a single
form field:

```php
<-- Add the has-danger class if the field is not valud -->
<div class="form-group <?=$this->if($loginForm->fieldError('email'), "has-danger")?>">

    <-- The field itself, that also keeps the previously entered value -->
    <input name="email" type="text" value="<?=$_($loginForm->fieldValue('email'))?>"
            class="form-control" placeholder="Username">

    <-- Output the error if there is one -->
    <?php if($error = $loginForm->fieldError('email')): ?>
        <div class="form-control-feedback"><?=$error?></div>
    <?php endif;?>

</div>
```

Then add the routes and the links for the login/logout pages to the page header and done, our password authentication 
is now complete. You can try loging in as user `trixie` with password `1`.

**[Project state at this stage (Commit 4)](https://github.com/PHPixie/Demo-Quickstart/tree/92fc8e0e314a30424e2cfba616932c2d9a294faf)**

##5. User registration

The registration form is very similar to the login one, let's look at the changes to the *Auth* processor:

```php
/**
 * Registration form
 * @return Form
 */
protected function registerForm()
{
    $validate = $this->components()->validate();
    $validator = $validate->validator();
    $document = $validator->rule()->addDocument();

    // By default the validator will only accept the fields that were defined.
    // This call disables this behaviour and allows extra fields in the data.
    // In our case the extra field is the hidden "register" field that is used
    // to distinguish whether we are processing user login or registration.
    $document->allowExtraFields();

    // Name is required
    $document->valueField('name')
        ->required("Name is required")
        ->addFilter()
        ->minLength(3)
        ->message("Username must contain at least 3 characters");

    // Email is also required and must be valid
    $document->valueField('email')
        ->required("Email is required")
        ->filter('email', "Please provide a valid email");

   // Required and must be at least 8 characters long
   $document->valueField('password')
        ->required("Password is required")
        ->addFilter()
            ->minLength(8)
            ->message("Password must contain at least 8 characters");

   // Also a required field
   $document->valueField('passwordConfirm')
        ->required("Please repeat your password");

   // In this callback we check that password and its confirmation fields match
   $validator->rule()->callback(function($result, $value) {
        // If they don't we add an error to the form
        if($value['password'] !== $value['passwordConfirm']) {
            $result->field('passwordConfirm')->addMessageError("Passwords don't match");
        }
  });

  // Build form for this validator
  return $validate->form($validator);
}

/**
 * Handles registration
 * @param Form $registerForm
 * @return bool Whether the registration was successful
 */
protected function processRegister($registerForm)
{
    /** @var UserRepository $userRepository */
    $userRepository = $this->components()->orm()->repository('user');

    // If the email is already taken add an error to the form
    if($userRepository->getByLogin($registerForm->email)) {
        $registerForm->result()->field('email')->addMessageError("This email is already taken");
        return false;
    }

    // Hash the password and create the user
    
    $provider = $this->passwordProvider();
    $user = $userRepository->create([
        'name'  => $registerForm->name,
        'email' => $registerForm->email,
        'passwordHash' => $provider->hash($registerForm->password)
    ]);
    $user->save();

    // And then manually log him in
    $provider->setUser($user);
    return true;
}
```

One thing of note here is that we added a hidden *register* field to the HTML to distinguish between the login and
registration forms.

**[Project state at this stage (Commit 5)](https://github.com/PHPixie/Demo-Quickstart/tree/7e3cf803c02f579ff6e29f6b64369d6ceb439970)**

##6. Social login

Now let's enable Facebook and Twitter login. We start by adding two fields *facebookId* and *twitterId* to the *users*
table in a new migration:

```sql
/* /assets/migrate/migrations/2_social_login.sql */

ALTER TABLE users ADD COLUMN twitterId VARCHAR(255) AFTER passwordHash;

-- statement

ALTER TABLE users ADD COLUMN facebookId VARCHAR(255) AFTER twitterId;
```

Now we need to register our application on these websites to get the API keys. It's important to provide correct callback
URLs during registration, in our case it will be *http://localhost.com/socialAuth/callback/twitter* for Twitter, and
*http://localhost.com/socialAuth/callback/facebook* for Facebook. We will add the routes and logic behind these URLs later,
but now let's put our API keys in the configuration.

```php
// /assets/config/social.php

return [
    'facebook' => [
        'type'      => 'facebook',
        'appId'     => 'YOUR APP ID',
        'appSecret' => 'YOUR APP SECRET'
    ],
    'twitter' => [
        'type'           => 'twitter',
        'consumerKey'    => 'YOUR APP ID',
        'consumerSecret' => 'YOUR APP SECRET'
    ]
];
```

And enable social login in the *auth.php* config file we edited before:

```php
// /assets/config/auth.php
<?php

return [
    'domains' => [
        'default' => [
            // ....
            'providers'  => [
                //.....
                
                // Enable social login
                'social' => [
                    'type' => 'social.oauth',

                    // After login remember the user in his session
                    'persistProviders' => ['session']
                ]
            ]
        ]
    ]
];
```

That's it we are donw with the configuration and can start writing code. Remember how we had to implement an interface
in the user repository class to enable password login. Now we implement one more:

```php
namespace Project\App\ORM\User;

// ....

/** This interface allows social login */
use PHPixie\AuthSocial\Repository as SocialRepository;

class UserRepository extends Repository implements LoginUserRepository, SocialRepository
{
    // ....
    
    /**
     * Finds the user by his social date retrieved from the social network.
     * Returns null if such user does not exist.
     *
     * @param SocialUser $socialUser
     * @return User|null
     */
    public function getBySocialUser($socialUser)
    {
        // Get the name of the field that stores user's social id,
        // e.g. twitterId or facebookId
        $providerName = $socialUser->providerName();
        $field = $this->socialIdField($providerName);

        // And then search by that field
        return $this->query()->where($field, $socialUser->id())->findOne();
    }

    /**
     * Gets the name of the field that stores the users social id for a particular network.
     * In our case we just add 'Id' to the name of the provider.
     *
     * @param string $providerName
     * @return string
     */
    public function socialIdField($providerName)
    {
        return $providerName.'Id';
    }
}
```

And now the new social authentication processor:

```php
namespace Project\App\HTTP\Auth;

use PHPixie\App\ORM\User;
use PHPixie\AuthSocial\Providers\OAuth as OAuthProvider;
use PHPixie\HTTP\Request;
use Project\App\ORM\User\UserRepository;
use Project\App\HTTP\Processor;
use PHPixie\Social\OAuth\User as SocialUser;

/**
 * Handles social login
 */
class Social extends Processor
{
    /**
     * Redirects the user tot he external login page,
     * e.g. Twitter or Facebook
     *
     * @param Request $request HTTP request
     * @return mixed
     */
    public function defaultAction($request)
    {
        $provider = $request->attributes()->get('provider');
        
        // If the 'provider' parameter is empty then redirect the user back to login page
        if(empty($provider)) {
            return $this->redirect('app.processor', ['processor' => 'auth']);
        }
        
        // Build an external login URL and redirect the user there
        $callbackUrl = $this->buildCallbackUrl($provider);
        $url = $this->oauthProvider()->loginUrl($provider, $callbackUrl);
        return $this->responses()->redirect($url);
    }
    
    /**
     * Handle login callback.
     * This handles the callback URLs that we specified when registering our application,
     * e.g. http://localhost.com/socialAuth/callback/twitter
     *
     * @param Request $request HTTP request
     * @return mixed
     */
    public function callbackAction($request)
    {
        $provider = $request->attributes()->getRequired('provider');
        
        // We need to build the callback URL again, this is required for obtaining the OAuth token
        $callbackUrl = $this->buildCallbackUrl($provider);
        $query = $request->query()->get();
        
        // And here is the handling itself.
        // If this user's social id is already in the database he will be automatically logged in.
        // In either case we will get his profile information in $userData
        $userData = $this->oauthProvider()->handleCallback($provider, $callbackUrl, $query);
        
        // If something went wrong, e.g. the user denied to authorize our application,
        // then redirect him back to the login page.
        if($userData === null) {
            return $this->redirect('app.processor', ['processor' => 'auth']);
        }
        
        // If the user has autorized our app, but he is not logged in yet
        // then he must be a new user andf must be registered.
        if($this->user() === null) {
            $user = $this->registerNewUser($userData);
            
            // Manually log him in after registration
            $this->oauthProvider()->setUser($user);
        }
        
        // Now he is definitely authenticated so we redirect him back to the frontpage
        return $this->redirect('app.frontpage');
    }
    
    /**
     * Register a new user from his social profile data.
     *
     * @param SocialUser $socialUser
     * @return mixed
     */
    protected function registerNewUser($socialUser)
    {
        /** @var UserRepository $userRepository */
        $userRepository = $this->components()->orm()->repository('user');
        
        // Get user's profile name from his social data.
        // Since this field can be different on different sites
        // we move this logic to a separate method.
        $profileName =  $this->getProfileName($socialUser);
        
        // Get the name of the database field to save his social id in
        $socialIdField = $userRepository->socialIdField($socialUser->providerName());
        
        // And create a new user
        $user = $userRepository->create([
            'name'         => $profileName,
            $socialIdField => $socialUser->id()
        ]);
        $user->save();
        
        return $user;
    }
    /**
     * Gets user's profile name from his social data
     *
     * @param SocialUser $socialUser
     * @return mixed
     */
    protected function getProfileName($socialUser)
    {
        // In our case both Twitter and Facebook use the same field, so it's simple:
        return $socialUser->loginData()->name;
    }

    /**
     * build the callback URL where the social network
     * will redirect the user after his login attempt.
     *
     * @param $provider
     * @return string
     */
    protected function buildCallbackUrl($provider)
    {
        return $this->frameworkHttp()->generateUri('app.socialAuthCallback', [
            'provider' => $provider
        ])->__toString();
    }
    
    /**
     * Get the OAuth authentication provider
     *
     * @return OAuthProvider
     */
    protected function oauthProvider()
    {
        $domain = $this->components()->auth()->domain();
        return $domain->provider('social');
    }
}
```

Then we configure new routes and add social login links to the login page:

```php
// /bundles/app/assets/templates/login.php

<?php $url = $this->httpPath('app.socialAuth', ['provider' => 'twitter']); ?>
<a class="btn btn-lg btn-primary btn-block" href="<?=$url?>">Login with Twitter</a>

<?php $url = $this->httpPath('app.socialAuth', ['provider' => 'facebook']); ?>
<a class="btn btn-lg btn-primary btn-block" href="<?=$url?>">Login with Facebook</a>
```

**[Project state at this stage (Commit 6)](https://github.com/PHPixie/Demo-Quickstart/tree/a34107cd33f0b56a249d299bd92788dac09d5294)**

##7. Posting messages

This is very simple, just one more form, but in this case submitted via AJAX. There only important thing to mention here
is using blocks in our templates for appending scripts to the bottom of the page. First add the *scripts* block to the
parent *layout* template:

```php
<!-- /bundles/app/assets/templates/layout.php -->

<!-- Allow child templates to append scripts to the bottom of the page -->
<?=$this->block('scripts')?>
```

Now in the *messages* template we can add some content to this block:

```php
<!-- /bundles/app/assets/templates/messages.php -->

<?php $this->startBlock('scripts'); ?>
    <script>
        $(function() {
            // Init the form handler
            <?php $url = $this->httpPath('app.action', ['processor' => 'messages', 'action' => 'post']);?>
            $('#messageForm').messageForm("<?=$_($url)?>");
        });
    </script>
<?php $this->endBlock(); ?>
```

You can append content to the block multiple times. It is also possible to only add content to the block if it's still
empty. Let's look at these examples:

```php
<?php $this->startBlock('test'); ?>
Hello
<?php $this->endBlock(); ?>

<?php $this->startBlock('test'); ?>
World
<?php $this->endBlock(); ?>

<?=$this->block('test')?>
<!-- Result -->
Hello
World
```

```php
<!--
    If the second parameter is`true` and the block already has some content added,
    then the `startBlock` fuction will return false and the content inside the `if` will be skipped.
-->
<?php if($this->startBlock('test', true)): ?>
Hello
<?php $this->endBlock();endif; ?>

<?php if($this->startBlock('test', true)): ?>
World
<?php $this->endBlock();endif; ?>

<?=$this->block('test')?>
<!-- Result -->
Hello
```

Let's also look at how the *Messages* processor returns a JSON response:

```php
 public function postAction($request)
 {
    // ....
    
    // Turns the ORM entity into a plain PHP object.
    // The `true` parameter makes all the loaded relationships to also be converted to plain objects,
    // although in our case there are none.
    //
    // If a processor returns a plain object or an array
    // then PHPixie will automatically encode it into JSON.
    return $message->asObject(true);
 }
```

**[Project state at this stage (Commit 7)](https://github.com/PHPixie/Demo-Quickstart/tree/48edabf4d69ee848346432300d357e5e96656780)

##8. Console commands

Now let's add some console commands. It's very similar to the web processors:

```php
namespace Project\App\Console;

use PHPixie\Console\Command\Config;
use PHPixie\Slice\Data;
/**
 * Lists messages
 */
class Messages extends Command
{
    /**
     * Command Configuration
     * @param Config $config
     */
    protected function configure($config)
    {
        // Description
        $config->description("Print latest messages");
        
        // Add an option to filter by user's id
        $config->option('userId')
            ->description("Only print messages of this user");
            
        // Add an argument to limit the amount of messages to display
        $config->argument('limit')
            ->description("Maximum number of messages to display, default is 5");
    }
    /**
     * @param Data $argumentData
     * @param Data $optionData
     */
    public function run($argumentData, $optionData)
    {
        // Get the number of messages to display
        $limit = $argumentData->get('limit', 5);
        
        // Build the query
        $query = $this->components()->orm()->query('message')
            ->orderDescendingBy('date')
            ->limit($limit);
            
        // If the `userId` option was set then add the condition to the query
        $userId = $optionData->get('userId');
        if($userId) {
            $query->relatedTo('user', $userId);
        }
        
        // Get messages as array
        $messages = $query->find(['user'])->asArray();
        
        // If none were found
        if(empty($messages)) {
            $this->writeLine("No messages found");
        }
        
        // Output the messages
        foreach($messages as $message) {
            $dateTime = new \DateTime($message->date);
            $this->writeLine($message->text);
            $this->writeLine(sprintf(
                "by %s on %s",
                $message->user()->name,
                $dateTime->format('j M Y, H:i')
            ));
            $this->writeLine();
        }
    }
}
```

```php
namespace Project\App\Console;

use PHPixie\Console\Command\Config;
use PHPixie\Database\Driver\PDO\Connection;
use PHPixie\Slice\Data;

/**
 * Outputs message statistics
 */
class Stats extends Command
{
    /**
     * Command configuration
     * @param Config $config
     */
    protected function configure($config)
    {
        $config->description("Display statistics");
    }

    /**
     * @param Data $argumentData
     * @param Data $optionData
     */
    public function run($argumentData, $optionData)
    {
        // Get the Database component
        $database = $this->components()->database();

        /** @var Connection $connection */
        $connection = $database->get();

        // Count all messages
        $total = $connection->countQuery()
            ->table('messages')
            ->execute();

        $this->writeLine("Total messages: $total");

        // Get message counts for all users
        $stats = $connection->selectQuery()
            ->fields([
                'name' => 'u.name',
                // sqlExpression allows adding raw SQL
                'count' => $database->sqlExpression('COUNT(1)'),
            ])
            ->table('messages', 'm')
            ->join('users', 'u')
                ->on('m.userId', 'u.id')
            ->groupBy('u.id')
            ->execute();

        foreach($stats as $row) {
            $this->writeLine("{$row->name}: {$row->count}");
        }
    }
}
```

Don't forget to register them in the *Project\App\Console* class:

```php
namespace Project\App;

class Console extends \PHPixie\DefaultBundle\Console
{
    /**
     * Here we define console commands
     * @var array
     */
    protected $classMap = array(
        'messages' => 'Project\App\Console\Messages',
        'stats'    => 'Project\App\Console\Stats'
    );
}
```

Done, now let's try the commands in the console:

```
# ./console

Available commands:

app:messages                  Print latest messages
app:stats                     Display statistics

# ....
```

```
# ./console help app:messages

app:messages [ --userId=VALUE ] [ LIMIT ]
Print latest messages

Options:
userId    Only print messages of this user

Arguments:
LIMIT    Maximum number of messages to display, default is 5
```

```
# ./console help app:stats

app:stats
Display statistics
```

And the results:

```
# ./console app:messages 2

Simplicity is the ultimate sophistication. -- Leonardo da Vinci
by Trixie on 7 Dec 2016, 16:40

Simplicity is prerequisite for reliability. -- Edsger W. Dijkstra
by Trixie on 7 Dec 2016, 15:05
```

```
# ./console app:stats

Total messages: 14
Pixie: 3
Trixie: 11
```

**[Project state at this stage (Commit 8)](https://github.com/PHPixie/Demo-Quickstart/tree/df8d0e75aea57d0dfebf7deea23909b33513e352)**

##9. Using configuration parameters

Most likely you will want to extract some configuration parameters to a separate file to make changing them easier.
Just put them in */assets/parameters.php* and reference them using `%`. 

```php
// /assets/parameters.php

return [
    'database' => [
        'name'     => 'phpixie',
        'user'     => 'phpixie',
        'password' => 'phpixie'
    ],

    'social' => [
        'facebookId'     => 'YOUR APP ID',
        'facebookSecret' => 'YOUR APP SECRET',

        'twitterId'     => 'YOUR APP ID',
        'twitterSecret' => 'YOUR APP SECRET',
    ]
];
```

And now the changed configuration files:

```php
// /assets/config/database.php

return [
    // Database configuration
    'default' => [
        // Referencing parameters from /assets/parameters.php
        'database' => '%database.name%',
        'user'     => '%database.user%',
        'password' => '%database.password%',
        'adapter'  => 'mysql',
        'driver'   => 'pdo'
    ]
];
```

```php
// /assets/config/social.php

return [
    'facebook' => [
        'type'      => 'facebook',
        'appId'     => '%social.facebookId%',
        'appSecret' => '%social.facebookSecret%'
    ],
    'twitter' => [
        'type'           => 'twitter',
        'consumerKey'    => '%social.twitterId%',
        'consumerSecret' => '%social.twitterSecret%'
    ]
];
```

Now you will only have to edit or replace this one file during deployment. And since it is just a PHP file you can also
use `if` and `switch` statements inside it to return different parameters based on different conditions.

**[Final project source](https://github.com/PHPixie/Demo-Quickstart)**
