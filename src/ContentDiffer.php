<?php
/**
 * Created by PhpStorm.
 * User: mooz
 * Date: 1/29/16
 * Time: 10:49 AM
 */

namespace Michaeljoyner\Edible;


class ContentDiffer
{

    private $fileSnapshot;
    private $databaseSnapshot;

    public function __construct(ContentSnapshot $fileSnapshot, ContentSnapshot $databaseSnapshot)
    {
        $this->fileSnapshot = $fileSnapshot;
        $this->databaseSnapshot = $databaseSnapshot;
    }

    public function newPages()
    {
        return $this->diffPages($this->fileSnapshot, $this->databaseSnapshot);
    }

    public function deprecatedPages()
    {
        return $this->diffPages($this->databaseSnapshot, $this->fileSnapshot);
    }

    protected function diffPages($minuend, $subtrahend)
    {
        $list = [];
        foreach($minuend->listPages() as $page) {
            if(! in_array($page, $subtrahend->listPages())) {
                $list[] = $page;
            }
        }
        return $list;
    }

    public function newTextblocks()
    {
        return $this->diffTextblocks($this->fileSnapshot, $this->databaseSnapshot);
    }

    public function deprecatedTextblocks()
    {
        return $this->diffTextblocks($this->databaseSnapshot, $this->fileSnapshot);
    }

    public function newGalleries()
    {
        return $this->diffGalleries($this->fileSnapshot, $this->databaseSnapshot);
    }

    public function deprecatedGalleries()
    {
        return $this->diffGalleries($this->databaseSnapshot, $this->fileSnapshot);
    }

    protected function diffTextblocks($minuend, $subtrahend)
    {
        return $this->diffContentItems($minuend->listTextblocks(), $subtrahend->listTextblocks());
    }

    protected function diffGalleries($minuend, $subtrahend)
    {
        return $this->diffContentItems($minuend->listGalleries(), $subtrahend->listGalleries());
    }

    protected function diffContentItems($minuend, $subtrahend)
    {
        $list = [];
        foreach($minuend as $pageName => $items) {
            if(! empty($items)) {
                foreach($items as $item) {
                    if((! array_key_exists($pageName, $subtrahend)) || (! in_array($item, $subtrahend[$pageName]))) {
                        $list[$pageName][] = $item;
                    }
                }
            }
        }
        return $list;
    }

}
