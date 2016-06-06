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
            ->setManipulations($this->getWideManipulation())
            ->performOnCollections('default');
    }

    protected function getWideManipulation()
    {
        return [
            'w' => config('edible.wide_width', 1200),
            'h' => config('edible.wide_height', 600),
            'fit' => 'max'
        ];
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

    public function setOrder($orderedIds)
    {
        $media = $this->getMedia();
        if (count($orderedIds) !== $media->count()) {
            throw new \Exception('incorrect amount of ids given');
        }

        for ($i = 0; $i < count($orderedIds); $i++) {
            $image = $media->where('id', intval($orderedIds[$i]))->first();
            if ($image) {
                $image->setCustomProperty('position', $i + 1);
                $image->save();
            }
        }
    }

    public function getOrdered()
    {
        $media = $this->getMedia();

        return $media->sort(function ($a, $b) {
            if ($a->getCustomProperty('position', -1) === $b->getCustomProperty('position', -1)) {
                return 0;
            }

            return $a->getCustomProperty('position', -1) < $b->getCustomProperty('position', -1) ? -1 : 1;
        })->values();
    }
}
