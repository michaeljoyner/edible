<?php

namespace Michaeljoyner\Edible\Models;

use Illuminate\Database\Eloquent\Model;

class Page extends Model
{
    protected $table = 'ec_pages';

    protected $fillable = [
        'name',
        'description'
    ];

    public function textblocks()
    {
        return $this->hasMany(Textblock::class, 'ec_page_id');
    }

    public function addTextblock($name, $description, $allows_html = false, $content = null)
    {
        if($this->textblocks()->where('name', $name)->first()) {
            throw new \Exception('Textblocks must have a unique name for a page');
        }
        return $this->textblocks()->create([
            'name' => $name,
            'description' => $description,
            'allows_html' => $allows_html,
            'content' => $content
        ]);
    }

    public function textFor($textblockName, $default = '')
    {
        try {
            $textblock = $this->textblocks()->where('name', $textblockName)->firstOrFail();
        } catch (\Exception $e) {
            return $default;
        }

        return empty($textblock->content) ? $default : $textblock->content;
    }

    public function galleries()
    {
        return $this->hasMany(Gallery::class, 'ec_page_id');
    }

    public function addGallery($name, $description, $is_single = false)
    {
        if($this->galleries()->where('name', $name)->first()) {
            throw new \Exception('Galleries must have a unique name for a page');
        }

        return $this->galleries()->create([
            'name' => $name,
            'description' => $description,
            'is_single' => $is_single
        ]);
    }

    public function imagesOf($galleryName)
    {
        try {
            $gallery = $this->galleries()->where('name', $galleryName)->firstOrFail();
        } catch (\Exception $e) {
            return collect([]);
        }

        return $gallery->getOrdered();
    }
}
