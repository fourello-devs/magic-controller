<?php

namespace FourelloDevs\MagicController\Console\Commands;

use Illuminate\Routing\Console\ControllerMakeCommand;
use Illuminate\Support\Facades\File;

/**
 * @author Jeremy Layson <jeremy.b.layson@gmail.com>
 * @since 2020/11/29
 *
 * Usage
 *
 * This generator extends the php artisan make:controller --model={model} --api command
 * It will generate the necessary skeleton to create a CRUD and should contain all the basics for the task
 * It also generates
 *
 * php artisan magic:controller UserController --model=User --requestsFolder=Some\Folder
 * name (UserController) - The name of the controller that you want to generate
 *      You can also pass a directory like V2\UserController for your convenience
 *
 * --model (User) - The Eloquent Model to be injected in the controller
 * --requestsFolder (\) - A custom path that you want to use for both Requests and Events file
 *      You must add a backslash on both ends to make this work (e.g. \V2\ or \Admin\)
 */
class ExtendedMakeController extends ControllerMakeCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'magic:controller
    {name : Controller name to be generated}
    {--requestsFolder= : Custom root folder for auto-generated requests}
    {--parent}
    {--model=}
    ';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create model, model controller, request, and event classes automagically!';

    public function getStub(): string
    {
        $stub = '/../../../stubs/controller.custom.stub';

        // Create Requests
        $this->generateAPIRequests();

        // Create Events
        $this->generateEvents();

        if (File::exists($path = __DIR__ . $stub) === false) {
            return parent::getStub();
        }

        return $path;
    }

    /**
     * Assume User model for example
     *
     * The requests will be generated on App\Http\Requests\User\{Method}User (e.g. IndexUser)
     * By default, the requests will be TRUE (instead of Laravel's default of FALSE)
     * This allows you to work on the Controller by default
     * See /stubs/request.custom.stub for more information
     *
     * The requestsFolder parameter will be inserted before the Model folder
     * to allow to create versioned or custom requests
     */
    protected function generateAPIRequests(): void
    {
        $model = $this->option('model');
        $folder = $this->option('requestsFolder');

        $names = [
            $folder . $model . '/Index' . $model,
            $folder . $model . '/Show' . $model,
            $folder . $model . '/Create' . $model,
            $folder . $model . '/Update' . $model,
            $folder . $model . '/Delete' . $model,
        ];

        foreach ($names as $name) {
            $this->call('make:request', [
                'name' => $name,
            ]);
        }
    }

    /**
     * This will auto-generate an event for each of the functions that you have
     * Allowing to easily extend each functions when necessary
     */
    public function generateEvents()
    {
        $model = $this->option('model');
        $folder = $this->option('requestsFolder');

        $names = [
            $folder . $model . '/' . $model . 'Collected',
            $folder . $model . '/' . $model . 'Fetched',
            $folder . $model . '/' . $model . 'Created',
            $folder . $model . '/' . $model . 'Updated',
            $folder . $model . '/' . $model . 'Deleted',
        ];

        foreach ($names as $name) {
            $this->call('make:event', [
                'name' => $name,
            ]);
        }
    }

    /**
     * DO NOT TOUCH IF YOU DON'T KNOW WHAT YOU'RE DOING
     * Build the model replacement values.
     *
     * @param  array  $replace
     * @return array
     */
    protected function buildModelReplacements(array $replace)
    {
        $modelClass = $this->parseModel($this->option('model'));

        if (! class_exists($modelClass)) {
            if ($this->confirm("A {$modelClass} model does not exist. Do you want to generate it?", true)) {
                $this->call('make:model', ['name' => $modelClass]);
            }
        }

        return array_merge($replace, [
            '{{ requestFolder }}' => $this->option('requestsFolder'),
            'DummyFullModelClass' => $modelClass,
            '{{ namespacedModel }}' => $modelClass,
            '{{namespacedModel}}' => $modelClass,
            'DummyModelClass' => class_basename($modelClass),
            '{{ model }}' => class_basename($modelClass),
            '{{model}}' => class_basename($modelClass),
            'DummyModelVariable' => lcfirst(class_basename($modelClass)),
            '{{ modelVariable }}' => lcfirst(class_basename($modelClass)),
            '{{modelVariable}}' => lcfirst(class_basename($modelClass)),
        ]);
    }
}
