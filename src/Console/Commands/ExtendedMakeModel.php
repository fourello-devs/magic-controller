<?php


namespace FourelloDevs\MagicController\Console\Commands;

use Illuminate\Foundation\Console\ModelMakeCommand;
use Illuminate\Support\Facades\File;

/**
 * Class ExtendedMakeModel
 * @package FourelloDevs\MagicController\Console\Commands
 *
 * @author James Carlo Luchavez <carlo.luchavez@fourello.com>
 * @since 2021-05-11
 */
class ExtendedMakeModel extends ModelMakeCommand
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'magic:model';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new model using custom stub.';

    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected $type = 'Model';

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub(): string
    {
        $stub = '/../../../stubs/model.custom.stub';

        if (File::exists($path = __DIR__ . $stub) === false) {
            return parent::getStub();
        }

        return $path;
    }
}
