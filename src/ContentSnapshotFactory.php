<?php
/**
 * Created by PhpStorm.
 * User: mooz
 * Date: 1/29/16
 * Time: 9:47 AM
 */

namespace Michaeljoyner\Edible;

use Michaeljoyner\Edible\Exceptions\InvalidStructureException;
use Symfony\Component\Yaml\Exception\ParseException;
use Symfony\Component\Yaml\Parser;

class ContentSnapshotFactory
{
    /**
     * @var Parser
     */
    private $parser;

    public function __construct(Parser $parser)
    {
        $this->parser = $parser;
    }

    public function makeSnapshotFromRepo(ContentRepository $repository)
    {
        return new ContentSnapshot($this->createConfigFromRepository($repository));
    }

    public function makeSnapshotFromYmlFile($filename)
    {
        return new ContentSnapshot($this->createConfigFromYml($filename));
    }

    public function createConfigFromRepository(ContentRepository $repo)
    {
        $collection = $repo->getAll();

        return $collection->reduce(function ($data, $page) {
            $data['pages'][$page->name] = [
                'description' => $page->description
            ];
            $page->textblocks->each(function ($textblock) use (&$data, $page) {
                $data['pages'][$page->name]['textblocks'][$textblock->name] = [
                    'description' => $textblock->description,
                    'allows_html' => !!$textblock->allows_html
                ];
            });
            $page->galleries->each(function ($gallery) use (&$data, $page) {
                $data['pages'][$page->name]['galleries'][$gallery->name] = [
                    'description' => $gallery->description,
                    'is_single'   => !!$gallery->is_single
                ];
            });

            return $data;
        }, ['pages' => []]);
    }

    public function createConfigFromYml($filename)
    {
        try {
            $structure = $this->parser->parse(file_get_contents($filename));
        } catch(ParseException $e) {
            throw new InvalidStructureException($e->getMessage());
        }

        $this->validateStructure($structure);

        return $structure;
    }

    private function validateStructure($structure)
    {
        if($this->missingPageArray($structure)) {
            throw new InvalidStructureException('The file edible.yml must contain a pages array');
        }

        foreach ($structure['pages'] as $page => $info) {
            $this->checkForAttribute('description', $page, $page, $info);

            if (array_key_exists('textblocks', $info)) {
                foreach ($info['textblocks'] as $textblockName => $textblockInfo) {
                    $this->checkForAttribute('description', $textblockName, $page, $textblockInfo);
                    $this->checkForAttribute('allows_html', $textblockName, $page, $textblockInfo);
                    $this->checkForBooleanValues('allows_html',$textblockName, $page, $textblockInfo);
                }
            }

            if (array_key_exists('galleries', $info)) {
                foreach ($info['galleries'] as $galleryName => $galleryInfo) {
                    $this->checkForAttribute('description', $galleryName, $page, $galleryInfo);
                    $this->checkForAttribute('is_single', $galleryName, $page, $galleryInfo);
                    $this->checkForBooleanValues('is_single', $galleryName, $page, $galleryInfo);
                }
            }
        }
    }

    private function missingPageArray($structure)
    {
        return (! is_array($structure)) || (! array_key_exists('pages', $structure)) || (! is_array($structure['pages']));
    }


    private function checkForAttribute($attribute, $contentName, $page, $array)
    {
        if(! array_key_exists($attribute, $array)) {
            $message = 'Invalid structure: ';
            $message .= ($contentName === $page) ? 'Page "' . $page . '" is missing the ' . $attribute . ' attribute'
                : '"' . $contentName . '" for page "' . $page . '" is missing the ' . $attribute . ' attribute';
            throw new InvalidStructureException($message);
        }
    }

    private function checkForBooleanValues($attribute, $contentName, $page, $array)
    {
        if ($array[$attribute] !== true && $array[$attribute] !== false) {
            $message = 'Invalid structure: "' . $contentName . '" for page "' . $page . '" must have a true or false value for ' . $attribute;
            throw new InvalidStructureException($message);
        }
    }

}
