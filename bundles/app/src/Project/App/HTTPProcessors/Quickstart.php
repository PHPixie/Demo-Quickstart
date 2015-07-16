<?php

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

    public function viewAction(Request $request)
    {
        //Output the 'id' parameter
        return $request->attributes()->get('id');
    }
    
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
    
    public function ormAction(Request $request)
    {
        $orm = $this->builder->components()->orm();

        $projects = $orm->query('project')->find();

        //Convert enttities to simple PHP objects
        return $projects->asArray(true);
    }
}