This quickstart guide will show you the basics of developing with PHPixie and it's components.
If you have some experience with any other PHP framework soon you will feel right at home.

## Installing

First, install Composer if you don't have it already and run the following command:

```
php composer.phar create-project phpixie/project your_project_folder
```

If you are on Windows create a symlink from the `/bundles/app/web` folder to `/web/bundles/app`.
For Linux and Mac users the symlink should work automatically.

Your HTTP server should point to the `/web` directory. In case you use Nginx you will also require this rule:

```
location / {
            try_files $uri $uri/ /index.php;
}
```

And here are rules for Apache2 (put into .htaccess in project root folder):

```
RewriteEngine on
RewriteBase /
RewriteCond %{REQUEST_URI} !web/
RewriteRule (.*) /web/$1 [L]
```

Now visit http://localhost/ in your browser and you should see a greeting.

## Bundles

PHPixie 3 supports organizing your project into bundles to make it easy to reuse your code in other projects.
For example a user system that handles login and registration could be organized into a bundle and used in a 
different project.

When you create PHPixie project it comes preconfigured with a single `app` bundle in the `/bundles/app` folder.
Until your project gets more bundles you will rarely need to do anything outside this one directory.

## Processors

A familiar concept of MVC Controllers has been greatly extended in PHPixie, it now allows nesting, composition and extended flexibility.
But you can still achieve a Controller-like behavior, which is actually used by the `Hello` in the `app` bundle that showed us the greeting.

Let's now create a new `Quickstart` processor to use with the tutorial:

```php
// bundles/app/src/Project/App/HTTPProcessors/Quickstart.php

namespace Project\App\HTTPProcessors;

use PHPixie\HTTP\Request;

// we extend a class that allows Controller-like behavior
class Quickstart extends \PHPixie\DefaultBundle\Processor\HTTP\Actions
{
    /**
     * The Builder will be used to access
     * various parts of the framework later on
     * @var Project\App\HTTPProcessors\Builder
     */
    protected $builder;
    
    public function __construct($builder)
    {
        $this->builder = $builder;
    }
    
    // This is the default action
    public function defaultAction(Request $request)
    {
        return "Quickstart tutorial";
    }
    
    //We will be adding methods here in a moment
}
```

And now register it with the bundle:

```php
// bundles/app/src/Project/App/HTTPProcessor.php

//...
    protected function buildQuickstartProcessor()
    {
        return new HTTPProcessors\Quickstart(
            $this->builder
        );
    }
//...
```

Accessing http://localhost/quickstart/ now will yield a "Quickstart tutorial" message.

Now we are all set up to try out the framework!

## Routing

A popular use case is to have URLs like `/quickstart/view/4` including a name or an id of some item.
First let's create an appropriate action in our processor:

```php
// bundles/app/src/Project/App/HTTPProcessors/Quickstart.php

//...
    public function viewAction(Request $request)
    {
        //Output the 'id' parameter
        return $request->attributes()->get('id');
    }
    
//...
}
```

Now we must also configure a new route to support the `id` parameter.
For that let's first look at the route configuration file:

```php
// bundles/app/assets/config/routeResolver

return array(
    //this allows us to group routes into one
    'type'      => 'group',
    'resolvers' => array(
        
        //...We will add new routes here..
        
        //The 'default' route
        'default' => array(
        //this type of route does pattern matching
            'type'     => 'pattern',
            
            //brackets mean that the part in them is optional
            'path'     => '(<processor>(/<action>))',
            
            //Default set of parameters to use
            //E.g. if the url is simply /hello
            //The 'action' parameter will default to 'greet'
            'defaults' => array(
                'processor' => 'hello',
                'action'    => 'greet'
            )
        )
    )
);
```

The route we want to add would look like this:

```php
'view' => array(
    'type'     => 'pattern',
    
    //Since the id parameter is mandatory
    //we don't wrap it in brackets
    'path'     => 'quickstart/view/<id>',
    'defaults' => array(
        'processor' => 'quickstart',
        'action'    => 'view'
    )
)
```

> Routes are tried one by one until a match is found.
> So it is important to put specific routes before more general ones in
> the config file.

Now navigatie to http://localhost/quickstart/view/5 and you should see '5' as a response.

As a quick example of some more advanced routing features here is what you could to to prefix multiple routes
with a common pattern. Don't worry if it looks a bit complicated, you don't need it for now:

```php
array(
    
    //Define a common prefix for routes
    'type'      => 'prefix',
    'path'   => 'user/<userId>/',
    'resolver' => array(
        'type'      => 'group',
        'resolvers' => array(
        
            //would handle /user/5/friends to Friends::userFriends()
            'friends' => array(
                'path'  => 'friends',
                'defaults' => array(
                    'processor' => 'friends',
                    'action'    => 'usersFriends'
                )
            ),
            
            //would handle /user/5/friends to Profile::userProfile()
            'profile' => array(
                'path'  => 'profile',
                'defaults' => array(
                    'processor' => 'profile',
                    'action'    => 'userProfile'
                )
            )
        )
    )
);
````

Such an approach allows you to avoid specifying redundant parts in your routes and also
makes changing them easier and less error-prone.

## Input and output

As you probably already noticed each action takes a `Request` as a parameter and returns a response.
Accessing request data can be done simply by:

```php
//$_GET['name'] 
$request->query()->get('name');

//$_POST['name'] 
$request->data()->get('name');

//Getting a routing attribute
$request->attributes()->get('name');
```

And now something more advanced:

```
$data = $request->data();

//Providing a default value
$data->get('name', 'Trixie');

//Throw an exception if 'name' is missing
$data->getRequired('name');

//Accessing a nested field
$data->get('users.pixie.name');

//You can also 'slice' the data to avoid long paths
$pixie = $data->slice('users.pixie');
$pixie->get('name');

//Getting data as array
$data->get();

//Getting all set keys
$data->keys();

//You can also iterate over it directly
foreach($data as $key => $value) {

}

//If you like this syntax take a look at the phpixie/slice library
//You can use it with any other array-like data
```

> JSON requests are also automatically parsed into $request->data()

As for the output it's even simpler:

```php
//A simple string message
return 'hello';

//To build a properly encoded JSON response
//just return any array or object
return array('success' => true);

//Or build custom responses
//Using the HTTP library
$http = $this->builder->components()->http();
$httpResponses = $http->responses();

//Redirect
return $httpResponses->redirect('http://phpixie.com/');

//Set custom status and headers
return $httpResponses->stringResponse('Not found', $headers = array(), 404);

//Initialize a file download
return $httpResponses->downloadFile('pixie.jpg', 'image/png', $filePath);

//Initialize a file download from string
//Useful for CSVs
return $httpResponses->download('report.csv', 'text/csv', $contents);
```

## Templating

PHPixie comes with a powerful templating engine that supports layouts, blocks, partials, custom extensions and formats.
By default the bundle is configured to locate templates from the `bundles/app/assets/templates` folder.
It already contains two templates used to display the default greeting by the `Greet` processor. 

Let's start by creating a simple template:

```html
<!-- bundles/app/assets/templates/quickstart/message.php -->

<!--
The $_() function is used to HTML encode your strings.
Which makes sure string containing special symbols 
don't break your layout and prevents some XSS attacks
-->
<p><?php echo $_($message); ?></p>
```

Now lets add another action to the Quickstart processor to render it:

```php
// bundles/app/src/Project/App/HTTPProcessors/Quickstart.php

//...
    public function renderAction(Request $request)
    {
        $template = $this->builder->components()->template();
        
        return $template->render(
            'app:quickstart/message',
            array(
                'message' => 'hello'
            )
        );
    }
//...
}
```

Visit http://localhost/quickstart/render and see the result

If you prefer adding variables to the template one by one instead of passing an array in one go
you can also use the alternative approach:

```php
$template = $this->components()->template();

$container = $template->get('app:quickstart/message');
$container->message = 'hello';
return $container->render();

//Or simply return the container
//and it will be rendered automatically
return $container;
```

### Layouts

First we'll add a layout:

```php
<!-- bundles/app/assets/templates/quickstart/layout.php -->

<h1>Quickstart</h1>

<div>
    <!-- This will be replaced by the child template -->
    <?php echo $this->childContent(); ?>
</div>
```

and update the `message` template:

```html
<!-- bundles/app/assets/templates/quickstart/message.php -->

<?php $this->layout('app:quickstart/layout');?>

<p><?php echo $_($message); ?></p>
```

Now http://localhost/quickstart/render will display the template wrapped inside the layout.

Layouts also support blocks to allow child templates to override and append content to their layouts.

```php
<!-- bundles/app/assets/templates/quickstart/layout.php -->

<!-- Define a 'header' block -->
<?php $this->startBlock('header'); ?>
    <h1>Quickstart</h1>
<?php $this->endBlock(); ?>

<!-- And output it -->
<?php echo $this->block('header'); ?>

<div>
    <!-- This will be replaced by the child template -->
    <?php echo $this->childContent(); ?>
</div>
```

This allows us to add content to the block in the child template:
```php
<!-- bundles/app/assets/templates/quickstart/message.php -->
<?php $this->layout('app:quickstart/layout');?>

<?php $this->startBlock('header'); ?>
    <h2>Message</h2>
<?php $this->endBlock(); ?>

<p><?php echo $_($message); ?></p>
```

By default if multiple templates add content to the same block the content will be appended.
But we can also tell the layout to only add content if it has not been defined by the child template.
This way adding content to it from the child template will override parent content.

```php
<!-- bundles/app/assets/templates/quickstart/layout.php -->

<?php $this->startBlock('header', true); ?>
    <h1>Quickstart</h1>
<?php $this->endBlock(); ?>

<!-- ... -->
```

Or you may decide to prepend blocks in reverse order, for this use:
```php
$this->startBlock('header', false, true);
```

### Includes

You can also include a subtemplate directly by using:

```php
<?php include $this->resolve('some:template');?>
```

### URL generation

You can generate route URLs by using `httpPath` and `httpUri`:

```php
<?php $url=$this->httpPath(
        'app.default',
        array(
            'processor' => 'hello',
            'action'    => 'greet'
        )
    );
    ?>
<a href="<?php echo $_($url); ?>">Hello</a>
```

## Database and ORM

Database connections are defined globally for the entire project not separate bundles.
Here is how you would connect t single MySQL database:

```php
// assets/config/database.php

return array(
    'default' => array(
        'driver' => 'pdo',
        'connection' => 'mysql:host=localhost;dbname=quickstart',
        'user'     => 'pixie',
        'password' => 'pixie'
    )
);
```

> PHPixie does not only support relational databases, but also **MongoDB**.
> You can define relationships between your MongoDB collections and relational tables
> and query them using the same query builder without having to learn anything else.
> At the moment no other ORM offers this level of seamless integration.

Let's populate the database with some data. Imagine you are creating a todo app that allows the user
to create projects and assign tasks to those project. Here is how the database might look like:

```sql
CREATE TABLE `projects`(
    `id`         INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `name`       VARCHAR(255),
    `tasksTotal` INT DEFAULT 0,
    `tasksDone`  INT DEFAULT 0
);

CREATE TABLE `tasks`(
    `id`        INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `projectId` INT NOT NULL,
    `name`      VARCHAR(255),
    `isDone`    BOOLEAN DEFAULT 0
);

INSERT INTO `projects` VALUES
(1, 'Quickstart', 4, 3),
(2, 'Build a website', 3, 0);

INSERT INTO `tasks` VALUES
(1, 1, 'Installing', 1),
(2, 1, 'Routing', 1),
(3, 1, 'Templating', 1),
(4, 1, 'Database', 0),

(5, 2, 'Design', 0),
(6, 2, 'Develop', 0),
(7, 2, 'Deploy', 0);
```

We can already use ORM to query these items from the database. Lets add an `orm` action to our processor:


```php
// bundles/app/src/Project/App/HTTPProcessors/Quickstart.php

//...
    public function ormAction(Request $request)
    {
        $orm = $this->builder->->components()->orm();
        
        $projects = $orm->query('project')->find();
        
        //Convert enttities to simple PHP objects
        return $projects->asArray(true);
    }
//...
```

Now by visiting http://localhost/quickstart/orm you will get a JSON response with project data.
Before we take a deeper look at what you can do with the PHPixie ORM lets configure a one-to-many
relationship between projects and tasks.

```php
// bundles/app/assets/config/orm.php

<?php

return array(
    'relationships' => array(
        array(
            'type'  => 'oneToMany',
            'owner' => 'project',
            'items' => 'task',
            
            //When a project is deleted
            //also delete all its tasks
            'itemsOptions' => array(
                'onOwnerDelete' => 'delete'
            )
        )
    )
);
```
### Entities

Creating, updating and deleting entities is straightforward:

```php
$orm = $this->builder->->components()->orm();

//Create an Entity
$projectRepository = $orm->repository('project');
$project = $projectRepository->create();

//Using a shortcut
$project = $orm->createEntity('project');

//Edit and save the project
$project->name = 'Buy Groceries';
$project->save();

$task = $orm->createEntity('task');
$task->name = 'Milk';
$task->save();

//Attach task to a project
$project->tasks->add($task);

//Deleting a project
$project->delete();

//Iterating over projects and tasks
$projects = $orm->query('project')->find();
foreach($projects as $project) {
    foreach($project->tasks() as $task) {
        //...
    }
}
```

### Querying

Here is just some things that you can do with the ORM queries now:

```php
$orm = $this->builder->->components()->orm();

//Find project by name
$orm->query('project')->where('name', 'Quickstart')->findOne();

//Query by id
$orm->query('project')->in($id)->findOne();

//Query by multiple ids
$orm->query('project')->in($ids)->findOne();

//Multiple conditions
$orm->query('project')
    ->where('tasksTotal', '>', 2)
    ->or('tasksDone', '<', 5)
    ->find();
    
//Conditions groups
//WHERE name = 'Quickstart' OR ( ... )
$orm->query('project')
    ->where('name', 'Quickstart')
    ->or(function($query) {
        $querty
            ->where('tasksTotal', '>', 2)
            ->or('tasksDone', '<', 5);
    })
    ->find();

//Alternative syntax for
//nested conditions
$orm->query('project')
    ->where('name', 'Quickstart')
    ->startWhereConditionGroup('or')
        ->where('tasksTotal', '>', 2)
        ->or('tasksDone', '<', 5)
    ->endGroup()
    ->find();
    
//Compare columns using '*' in operators
//Find projects where tasksTotal = tasksDone
$orm->query('project')
    ->where('tasksTotal', '=*', 'tasksDone')
    ->find();

//Find projects that have at least a single task
$orm->query('project')
    ->relatedTo('task')
    ->find();
    
//Find a project related to a specific task
$orm->query('project')
    ->where('tasks.name', 'Routing')
    ->find();

//Or like this
$orm->query('project')
    ->orRelatedTo('task', function($query) {
        $query->where('name', 'Routing');
    })
    ->find();

//Load projects while preloading
//all of their tasks
$orm->query('project')->find(array('task'));

//Update all projects
$orm->query('project')->update(array(
    'tasksDone' => 0
));

//Count completed projects
//and reuse the same query
//Useful for pagination
$query = $orm->query('project')
    ->where('tasksTotal', '=*', 'tasksDone');
    
$count = $query->count();

$query
    ->limit(5)
    ->offset(0)
    ->find();
```

The ORM component features multiple optimizations to help you reduce the number of queries.
For example it is possible to attach multiple tasks to a single project without getting them all one by one.

```php
$orm = $this->builder->->components()->orm();

//Query representing the first project in the database
$projectQuery = $orm->query('project');

//Query representing the first 5 tasks in the database
$tasksQuery = $orm->query('task')->limit(5);

//No database query has been executed yet

//Associate tasks with the project
//in a single query
$projectQuery->tasks->add($tasksQuery);
```

> Using queries instead of adding entities one by one greatly reduced
> the amount of database queries required. Especially in the case of many-to-many relationships.
> As with MongoDB support, no other PHP ORM has this feature at this point.

### Extending entities

You might want to extend the entity classes for your models to add additional functionality.
Instead of doing this directly and thus coupling your code to the ORM and the database PHPixie
allows you to define wrappers that wrap around actual ORM entities but are entirely decoupled from it.
This also means that your ORM classes will be easy to test and won't require fixtures.

Here is a simple wrapper:

```php
// bundles/app/src/Project/App/ORMWrappers/Project.php;

namespace Project\App\ORMWrappers;

class Project extends \PHPixie\ORM\Wrappers\Type\Database\Entity
{
    //We add a simple method that will tell us
    //whether the project is considered done
    public function isDone()
    {
        return $this->tasksDone === $this->tasksTotal;
    }
}
```

Now we need to register this wrapper with the bundle:

```php
// bundles/app/src/Project/App/ORMWrappers.php;
namespace Project\App;

class ORMWrappers extends \PHPixie\ORM\Wrappers\Implementation
{
    //Names of the entities we want to wrap
    protected $databaseEntities = array(
        'project'
    );
    
    public function projectEntity($entity)
    {
        return new ORMWrappers\Project($entity);
    }
}
```

Now lets try using it

```php
//Find the first project
$project = $orm->query('project')->findOne();

//Check if it is done
$project->isDone();
```

> You can also provide wrappers for Queries and Repositories to extend their behaviour,
> like for example providing a method that will automatically add multiple conditions
> to the query you are building.

## There's more

The entire code for this project can be found in the [Demo-Quickstart](https://github.com/phpixie/demo-quickstart) repository.

Now you have everything you need to get started with PHPixie 3, but there is much more it can offer you.
Each of the PHPixie components has an extensive list of features that are beyond the scope of this quickstart
and can be used separately without the rest of the framework.
