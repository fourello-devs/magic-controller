<?php

namespace FourelloDevs\MagicController\Console\Commands;

use Illuminate\Routing\Console\ControllerMakeCommand;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

/**
 * @author Jeremy Layson <jeremy.b.layson@gmail.com>
 * @author James Carlo Luchavez <carlo.luchavez@fourello.com>
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
    {--y : Skip all questions}
    ';

    /**
     * @var string
     */
    protected $model_name;

    /**
     * @var string
     */
    protected $model_class;

    /**
     * @var
     */
    protected $skip_questions = FALSE;

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create model, controller, request, event, and resource classes automagically!';

    public function getStub(): string
    {
        $stub = '/../../../stubs/controller.custom.stub';

        if (File::exists($path = __DIR__ . $stub) === FALSE) {
            return parent::getStub();
        }

        return $path;
    }

    /**
     * Assume User model for example
     *
     * The requests will be generated on App\Http\Requests\User\{Method}User (e.g. IndexUser)
     * By default, the requests will be TRUE (instead of Laravel's default of FALSE)
     * This allows you to work on the Controller by default.
     * See /stubs/request.custom.stub for more information.
     *
     * The requestsFolder parameter will be inserted before the Model folder
     * to allow to create versioned or custom requests.
     */
    protected function generateAPIRequests(): void
    {
        $model = $this->getModelName();
        $folder = $this->option('requestsFolder');

        $names = [
            $folder . $model . '/Index' . $model,
            $folder . $model . '/Show' . $model,
            $folder . $model . '/Create' . $model,
            $folder . $model . '/Update' . $model,
            $folder . $model . '/Delete' . $model,
        ];

        foreach ($names as $name) {
            $this->call('magic:request', [
                'name' => $name,
            ]);
        }
    }

    /**
     * This will auto-generate an event for each of the functions that you have
     * Allowing to easily extend each functions when necessary.
     */
    public function generateEvents(): void
    {
        $model = $this->getModelName();
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
     * This will auto-generate a resource class for the model.
     */
    public function generateResource(): void
    {
        $model = $this->getModelName();

        $this->call('magic:resource', [
            'name' => $model . 'Resource'
        ]);
    }

    /**
     * @param string $name
     * @return string
     */
    protected function buildClass($name): string
    {
        // Start Preparations
        $this->startPreparations();

        $controllerNamespace = $this->getNamespace($name);

        $replace = [];

        if ($this->option('parent')) {
            $replace = $this->buildParentReplacements();
        }

        // Run buildModelReplacements method even if model option is not supplied
        $replace = $this->buildModelReplacements($replace);

        // Do more stuff
        $this->startMagicShow();

        $replace["use {$controllerNamespace}\Controller;\n"] = '';

        return str_replace(
            array_keys($replace), array_values($replace), parent::buildClass($name)
        );
    }

    /**
     * DO NOT TOUCH IF YOU DON'T KNOW WHAT YOU'RE DOING
     * Build the model replacement values.
     *
     * @param  array  $replace
     * @return array
     */
    protected function buildModelReplacements(array $replace): array
    {
        $modelClass = $this->getModelClass();

        if (class_exists($modelClass) === FALSE) {

            $args = [];
            $will_generate = FALSE;

            if($this->skip_questions || $this->confirm("{$modelClass} model does not exist. Do you want to generate it?", true) || $this->hasOption('y')) {
                $args['name'] = $modelClass;
                $will_generate = TRUE;
            }

            if ($this->skip_questions || $this->confirm("{$modelClass} model has no migration file yet. Do you want to generate it?", true)) {
                $args['-m'] = TRUE;
            }

            if ($will_generate) {
                try {
                    $this->call('magic:model', $args);
                }catch (\Exception $e) {
                    $this->error($e->getMessage());
                }
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

    /***** GETTERS & SETTERS *****/

    /**
     * @return string|null
     */
    public function getModelName(): ?string
    {
        return $this->model_name;
    }

    /**
     * @return string|null
     */
    public function getModelClass(): ?string
    {
        return $this->model_class;
    }

    public function setFields(string $model): void
    {
        $this->model_name = Str::studly(Str::singular($model));
        $this->model_class = $this->parseModel($this->model_name);
    }

    /***** OTHER METHODS *****/

    public function startPreparations(): void
    {
        $this->skip_questions = $this->option('y');

        if ($model_name = $this->option('model')) {
            $this->setFields($model_name);
        }

        if (empty($this->getModelName()) && $model_name = $this->argument('name')) {
            $model_name = Str::before(strtolower($model_name), 'controller');
            $model_name && $this->setFields($model_name);
        }
    }

    public function startMagicShow(): void
    {
        // Create Requests
        if($this->skip_questions || $this->confirm("Do you want to generate REQUEST files?", true)) {
            $this->generateAPIRequests();
        }

        // Create Events
        if($this->skip_questions || $this->confirm("Do you want to generate EVENT files?", true)) {
            $this->generateEvents();
        }

        // Create Resource File
        if($this->skip_questions || $this->confirm("Do you want to generate RESOURCE file?", true)) {
            $this->generateResource();
        }
    }
}
