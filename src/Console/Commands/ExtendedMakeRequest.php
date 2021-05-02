<?php


namespace FourelloDevs\MagicController\Console\Commands;


use Illuminate\Foundation\Console\RequestMakeCommand;
use Illuminate\Support\Facades\File;

/**
 * Class ExtendedMakeRequest
 * @package FourelloDevs\MagicController\Console\Commands
 *
 * @author James Carlo Luchavez <carlo.luchavez@fourello.com>
 * @since 2021/05/03
 */
class ExtendedMakeRequest extends RequestMakeCommand
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'magic:request';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new form request with authorize as true.';

    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected $type = 'Request';

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub(): string
    {
        $stub = '/../../../stubs/request.custom.stub';

        if (File::exists($path = __DIR__ . $stub) === false) {
            return parent::getStub();
        }

        return $path;
    }
}
