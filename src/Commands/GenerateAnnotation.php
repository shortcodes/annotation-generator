<?php

namespace Shortcodes\AnnotationGenerator\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class GenerateAnnotations extends Command
{
    protected $signature = 'make:annotation {name} {--tag=} {--nomodel}';

    protected $description = 'Generate Swagger annotations';

    private $filesToCreate = [
        'Index',
        'Store',
        'Show',
        'Update',
        'Delete',
    ];

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {

        $name = $this->argument('name');
        $tag = $this->option('tag');
        $noModel = $this->option('nomodel');
        $nameKebab = Str::kebab(Str::plural($name));
        $nameCammel = Str::camel($name);

        if (!$name) {
            $this->info('You have to provide name.');
            return;
        }

        if (!$tag) {
            $tag = $nameKebab;
            return;
        }

        $rootPath = app_path() . '/Swagger/Actions/' . $name;
        $client = Storage::createLocalDriver(['root' => $rootPath]);

        if(!$noModel){

            $rootPathModels = app_path() . '/Swagger/Models' ;
            $clientModels = Storage::createLocalDriver(['root' => $rootPathModels]);

            $clientModels->put($name . '.php',   $this->stubModel($name));
            $this->info('File ' . $name . '.php' . ' created!');

        }

        foreach ($this->filesToCreate as $file) {
            $method = 'stub' . $file;
            $client->put($file . '.php', $this->$method($name,$nameCammel, $nameKebab, $tag));
            $this->info('File ' . $file . '.php' . ' created!');

        }
    }

    private function stubModel($name)
    {
        return <<<EOT
<?php

namespace App\Swagger\Models;

/**
 * Class $name
 *
 * @OA\Schema(
 *     title="$name",
 *     description="$name model",
 * )
 */
class $name
{
    /**
     * @OA\Property(
     *     format="string",
     *     description="Example property",
     *     title="Name",
     * )
     *
     * @var string
     */
    private \$property;

}

EOT;
    }

    private function stubIndex($name,$nameCammel, $nameKebab, $tag)
    {
        return <<<EOT
<?php
        
namespace App\Swagger\Actions\$name;

/**
 * @OA\Get(
 *     path="/{$nameKebab}",
 *     summary="Return list of {$name}",
 *     tags={"{$tag}"},
 *     operationId="index-{$nameKebab}",
 *
 *     @OA\Response(
 *         response=200,
 *         description="Success",
 *         @OA\MediaType( mediaType="application/json")
 *     ),
 *     security={
 *         {"apiKey": {},"apiToken": {}}
 *     }
 * )
 */

EOT;
    }

    private function stubShow($name, $nameCammel, $nameKebab, $tag)
    {
        return <<<EOT
<?php
        
namespace App\Swagger\Actions\\$name;

/**
 * @OA\Get(
 *     path="/{$nameKebab}/{$nameCammel}",
 *     summary="Return {$name} of provided ID",
 *     tags={"{$tag}"},
 *     operationId="show-{$nameKebab}",
 *     
*      @OA\Parameter(
 *         name="$nameCammel",
 *         in="path",
 *         description="{$name} id",
 *         required=true,
 *         @OA\Schema(
 *             type="integer"
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Success",
 *         @OA\MediaType( mediaType="application/json")
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Not Found",
 *         @OA\MediaType( mediaType="application/json")
 *     ),
 *     security={
 *         {"apiKey": {},"apiToken": {}}
 *     }
 * )
 */

EOT;
    }

    private function stubStore($name,$nameCammel, $nameKebab, $tag)
    {
        return <<<EOT
<?php
        
namespace App\Swagger\Actions\\$name;

/**
 * @OA\Post(
 *     path="/{$nameKebab}",
 *     summary="Creates new {$name}",
 *     tags={"{$tag}"},
 *     operationId="create-{$nameKebab}",
 *     @OA\RequestBody(
 *         description="$name model",
 *         required=true,
 *         @OA\JsonContent(ref="#/components/schemas/$name")
 *     ),
 *     @OA\Response(
 *         response=201,
 *         description="Created",
 *         @OA\MediaType( mediaType="application/json")
 *     ),
 *     @OA\Response(
 *         response=422,
 *         description="Unprocessable Entity",
 *         @OA\MediaType( mediaType="application/json")
 *     ),
 *     security={
 *         {"apiKey": {},"apiToken": {}}
 *     }
 * )
 */

EOT;
    }

    private function stubUpdate($name,$nameCammel, $nameKebab, $tag)
    {
        return <<<EOT
<?php
        
namespace App\Swagger\Actions\\$name;

/**
 * @OA\Patch(
 *     path="/{$nameKebab}/{$nameCammel}",
 *     summary="Update {$name} of provided ID",
 *     tags={"{$tag}"},
 *     operationId="update-{$nameKebab}",
 *     
*      @OA\Parameter(
 *         name="$nameCammel",
 *         in="path",
 *         description="{$name} id",
 *         required=true,
 *         @OA\Schema(
 *             type="integer"
 *         )
 *     ),
 *     @OA\RequestBody(
 *         description="$name model",
 *         required=true,
 *         @OA\JsonContent(ref="#/components/schemas/$name")
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Success",
 *         @OA\MediaType( mediaType="application/json")
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Not Found",
 *         @OA\MediaType( mediaType="application/json")
 *     ),
 *     @OA\Response(
 *         response=422,
 *         description="Unprocessable Entity",
 *         @OA\MediaType( mediaType="application/json")
 *     ),
 *     security={
 *         {"apiKey": {},"apiToken": {}}
 *     }
 * )
 */

EOT;
    }

    private function stubDelete($name,$nameCammel, $nameKebab, $tag)
    {
        return <<<EOT
<?php
        
namespace App\Swagger\Actions\\$name;

/**
 * @OA\Delete(
 *     path="/{$nameKebab}/{$nameCammel}",
 *     summary="Delete {$name} of provided ID",
 *     tags={"{$tag}"},
 *     operationId="delete-{$nameKebab}",
 *     
*      @OA\Parameter(
 *         name="$nameCammel",
 *         in="path",
 *         description="{$name} id",
 *         required=true,
 *         @OA\Schema(
 *             type="integer"
 *         )
 *     ),
 *     @OA\Response(
 *         response=204,
 *         description="Success",
 *         @OA\MediaType( mediaType="application/json")
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Not Found",
 *         @OA\MediaType( mediaType="application/json")
 *     ),
 *     security={
 *         {"apiKey": {},"apiToken": {}}
 *     }
 * )
 */

EOT;
    }

}
