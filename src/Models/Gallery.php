<?php

namespace Michaeljoyner\Edible\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia\HasMediaTrait;
use Spatie\MediaLibrary\HasMedia\Interfaces\HasMediaConversions;

class Gallery extends Model implements HasMediaConversions
{
    use HasMediaTrait;

    protected $table = 'ec_galleries';

    protected $fillable = [
        'name',
        'description',
        'is_single'
    ];

    public function page()
    {
        return $this->belongsTo(Page::class, 'ec_page_id');
    }

    public function registerMediaConversions()
    {
        $this->addMediaConversion('thumb')
            ->setManipulations(['w' => 200, 'h' => 200, 'fit' => 'crop'])
            ->performOnCollections('default');

        $this->addMediaConversion('web')
            ->setManipulations(['w' => 500, 'h' => 600])
            ->performOnCollections('default');

        $this->addMediaConversion('wide')
            ->setManipulations(['w' => 1200, 'h' => 500, 'fit' => 'max'])
            ->performOnCollections('default');
    }


    public function addImage($image)
    {
        if($this->is_single) {
            $this->clearMediaCollection();
        }
        return $this->addMedia($image)->preservingOriginal()->toMediaLibrary();
    }

    public function defaultSrc($conversion = 'web')
    {
        $image = $this->getMedia()->first();

        if(! $image) {
            return '/images/assets/default.png';
        }

        return $image->getUrl($conversion);
    }


}
