<?php
/**
 * Created by PhpStorm.
 * User: mooz
 * Date: 1/28/16
 * Time: 8:42 AM
 */

namespace Michaeljoyner\Edible;


class ContentSnapshot
{

    private $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function getPageDescription($pageName)
    {
        if ($this->hasPage($pageName) && $this->pageHasDescription($pageName)) {
            return $this->data['pages'][$pageName]['description'];
        }

    }

    public function getTextblockInfo($textblockName, $pageName)
    {
        if ($this->hasTextblock($textblockName, $pageName)) {
            return $this->data['pages'][$pageName]['textblocks'][$textblockName];
        }

    }

    public function getGalleryInfo($galleryName, $pageName)
    {
        if ($this->hasGallery($galleryName, $pageName)) {
            return $this->data['pages'][$pageName]['galleries'][$galleryName];
        }
    }

    public function hasPage($pageName)
    {
        return array_key_exists($pageName, $this->data['pages']);
    }

    public function listPages()
    {
        return array_keys($this->data['pages']);
    }

    public function hasTextblock($textblockName, $pageName)
    {
        return $this->pageHasCollectionItem('textblocks', $textblockName, $pageName);
    }

    public function listTextblocks()
    {
        return $this->listCollectionsPerPage('textblocks');
    }

    public function hasGallery($galleryName, $pageName)
    {
        return $this->pageHasCollectionItem('galleries', $galleryName, $pageName);
    }

    public function listGalleries()
    {
        return $this->listCollectionsPerPage('galleries');
    }

    protected function pageHasCollectionItem($collectionName, $itemName, $pageName)
    {
        if ((!$this->hasPage($pageName)) || (!$this->pageHasCollectionOf($collectionName, $pageName))) {
            return false;
        }

        return array_key_exists($itemName, $this->data['pages'][$pageName][$collectionName]);
    }

    protected function listCollectionsPerPage($collectionName)
    {
        $list = [];
        foreach ($this->data['pages'] as $pageName => $info) {
            $list[$pageName] = $this->pageHasCollectionOf($collectionName,
                $pageName) ? $this->listCollectionItems($pageName, $collectionName) : [];
        }

        return $list;
    }

    protected function listCollectionItems($pageName, $collectionName)
    {
        return array_keys($this->data['pages'][$pageName][$collectionName]);
    }

    protected function pageHasCollectionOf($collectionName, $pageName)
    {
        return array_key_exists($collectionName, $this->data['pages'][$pageName]);
    }

    private function pageHasDescription($pageName)
    {
        return array_key_exists('description', $this->data['pages'][$pageName]);
    }
}
