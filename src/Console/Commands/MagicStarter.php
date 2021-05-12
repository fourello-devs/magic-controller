<?php


namespace FourelloDevs\MagicController\Console\Commands;


use Illuminate\Console\Command;
use Illuminate\Support\Str;
use InvalidArgumentException;

class MagicStarter extends Command
{

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'magic:start
    {model : Eloquent Model}
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

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $this->startPreparations();

        $this->startMagicShow();
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

        if ($model_name = $this->argument('model')) {
            $this->setFields($model_name);
        }
    }

    public function modelExists(): bool
    {
        if ($this->getModelClass()) {
            return class_exists($this->getModelClass());
        }
        else {
            $this->startPreparations();
            return $this->modelExists();
        }
    }

    public function startMagicShow(): void
    {
        // Create Model
        $this->generateEloquentModel();

        // Create Resource File
        $this->generateResource();

        // Create Requests
        $this->generateAPIRequests();

        // Create Events
        $this->generateEvents();

        // Create API Controller
        $this->generateController();
    }

    /***** COMMANDS *****/

    /**
     *
     */
    protected function generateEloquentModel(): void
    {
        if ($this->modelExists() === FALSE) {

            $modelClass = $this->getModelClass();
            $args = [];
            $will_generate = FALSE;

            if($this->skip_questions || $this->confirm("{$modelClass} model does not exist. Do you want to generate it?", true) || $this->hasOption('y')) {
                $args['name'] = $modelClass;
                $will_generate = TRUE;
            }

            if ($will_generate && ($this->skip_questions || $this->confirm("{$modelClass} model has no migration file yet. Do you want to generate it?", true))) {
                $args['-m'] = TRUE;
            }

            if ($will_generate) {
                $this->call('magic:model', $args);
            }
        }
        else {
            $this->error('Model already exists!');
        }
    }

    /**
     * This will auto-generate a resource class for the model.
     */
    public function generateResource(): void
    {
        if($this->skip_questions || $this->confirm("Do you want to generate RESOURCE file?", true)) {
            $this->call('magic:resource', [
                'name' => $this->getModelName() . 'Resource'
            ]);
        }
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
        if($this->skip_questions || $this->confirm("Do you want to generate REQUEST files?", true)) {
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
    }

    /**
     * This will auto-generate an event for each of the functions that you have
     * Allowing to easily extend each functions when necessary.
     */
    public function generateEvents(): void
    {
        if($this->skip_questions || $this->confirm("Do you want to generate EVENT files?", true)) {
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
    }

    public function generateController(): void
    {
        if($this->skip_questions || $this->confirm("Do you want to generate API Controller file?", true)) {
            $args = [
                'name' => $this->getModelName() . 'Controller',
                '--model' => $this->getModelName(),
            ];

            if ($this->hasOption('requestsFolder') && $folder = $this->option('requestsFolder')) {
                $args['--requestFolder'] = $folder;
            }

            if ($this->hasOption('parent') && $parent = $this->option('parent')) {
                $args['--parent'] = $parent;
            }

            $this->call('magic:controller', $args);
        }
    }

    /***** MODEL VALIDATION *****/

    /**
     * Get the fully-qualified model class name.
     *
     * @param string $model
     * @return string
     *
     * @throws InvalidArgumentException
     */
    protected function parseModel(string $model): string
    {
        if (preg_match('([^A-Za-z0-9_/\\\\])', $model)) {
            throw new InvalidArgumentException('Model name contains invalid characters.');
        }

        return $this->qualifyModel($model);
    }

    /**
     * Qualify the given model class base name.
     *
     * @param  string  $model
     * @return string
     */
    protected function qualifyModel(string $model): string
    {
        $model = ltrim($model, '\\/');

        $model = str_replace('/', '\\', $model);

        $rootNamespace = $this->rootNamespace();

        if (Str::startsWith($model, $rootNamespace)) {
            return $model;
        }

        return is_dir(app_path('Models'))
            ? $rootNamespace.'Models\\'.$model
            : $rootNamespace.$model;
    }

    /**
     * Get the root namespace for the class.
     *
     * @return string
     */
    protected function rootNamespace(): string
    {
        return $this->laravel->getNamespace();
    }
}
