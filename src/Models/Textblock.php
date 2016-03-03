<?php

namespace Michaeljoyner\Edible\Models;

use Illuminate\Database\Eloquent\Model;

class Textblock extends Model
{
    protected $table = 'ec_textblocks';

    protected $fillable = [
        'name',
        'description',
        'allows_html',
        'content'
    ];

    public function page()
    {
        return $this->belongsTo(Page::class, 'ec_page_id');
    }

    public function setContent($content)
    {
        $this->content = $content;
        $this->save();
    }
}
