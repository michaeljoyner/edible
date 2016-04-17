<?php
/**
 * Created by PhpStorm.
 * User: mooz
 * Date: 4/17/16
 * Time: 7:42 AM
 */

namespace Michaeljoyner\Edible;


use Symfony\Component\Yaml\Parser;

class ContentWriterFactory
{
    public static function makeWriter()
    {
        $fileSnapshot = (new ContentSnapshotFactory(new Parser()))->makeSnapshotFromYmlFile(static::getEdibleFilePath());
        $databaseSnapshot = (new ContentSnapshotFactory(new Parser()))->makeSnapshotFromRepo(new ContentRepository());
        return new ContentWriter($fileSnapshot, $databaseSnapshot);
    }

    private static function getEdibleFilePath()
    {
        if(function_exists('base_path')) {
            return base_path('edible.yaml');
        }

        return './edible.yaml';
    }
}