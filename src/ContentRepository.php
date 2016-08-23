<?php
/**
 * Created by PhpStorm.
 * User: mooz
 * Date: 1/26/16
 * Time: 8:56 AM
 */

namespace Michaeljoyner\Edible;

use Michaeljoyner\Edible\Models\Page;


class ContentRepository
{
    public static function makePage($name, $description = null)
    {
        return Page::create(['name' => $name, 'description' => $description]);
    }

    public function getAll()
    {
        return Page::with('textblocks', 'galleries')->get();
    }

    public function getPageByName($name)
    {
        return Page::with('textblocks', 'galleries')->where('name', $name)->first();
    }

    public function listPages()
    {
        return Page::lists('name')->toArray();
    }

    public function pageExists($pageName)
    {
        return !! Page::where('name', $pageName)->count();
    }

    public function deleteByName($pageName)
    {
        $page = Page::where('name', $pageName)->firstOrFail();
        if($page->textblocks->count() > 0) {
            $page->textblocks->each(function($textblock) {
                $textblock->delete();
            });
        }
        if($page->galleries->count() > 0) {
            $page->galleries->each(function($gallery) {
                $gallery->delete();
            });
        }
        return $page->delete();
    }

    public function getPageListWithUrls()
    {
        return collect(Page::all()->map(function($page) {
            return [
                'name' => $page->name,
                'url' => '/package-edible/pages/'.$page->id
            ];
        })->toArray());
    }

}
