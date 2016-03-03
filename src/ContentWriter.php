<?php
/**
 * Created by PhpStorm.
 * User: mooz
 * Date: 1/29/16
 * Time: 1:20 PM
 */

namespace Michaeljoyner\Edible;

use Michaeljoyner\Edible\Models\Page;


class ContentWriter
{

    private $fileSnapshot;
    private $databaseSnapshot;
    private $differ;

    public function __construct(ContentSnapshot $fileSnapshot, ContentSnapshot $databaseSnapshot)
    {
        $this->fileSnapshot = $fileSnapshot;
        $this->databaseSnapshot = $databaseSnapshot;
        $this->differ = new ContentDiffer($this->fileSnapshot, $this->databaseSnapshot);
    }

    public function setContentStructure()
    {
        $this->setNewPages()
            ->setNewTextblocks()
            ->setNewGalleries()
            ->removeDeprecatedGalleries()
            ->removeDeprecatedTextblocks()
            ->removeDeprecatedPages();
    }

    protected function setNewPages()
    {
        foreach ($this->differ->newPages() as $page) {
            Page::create(['name' => $page, 'description' => $this->fileSnapshot->getPageDescription($page)]);
        }

        return $this;
    }

    protected function setNewTextblocks()
    {
        foreach ($this->differ->newTextblocks() as $pageName => $textblocks) {
            $page = Page::where('name', $pageName)->first();
            foreach ($textblocks as $textblock) {
                $textblockInfo = $this->fileSnapshot->getTextblockInfo($textblock, $pageName);
                $page->addTextblock($textblock, $textblockInfo['description'], $textblockInfo['allows_html']);
            }
        }

        return $this;
    }

    protected function setNewGalleries()
    {
        foreach ($this->differ->newGalleries() as $pageName => $galleries) {
            $page = Page::where('name', $pageName)->first();
            foreach ($galleries as $gallery) {
                $galleryInfo = $this->fileSnapshot->getGalleryInfo($gallery, $pageName);
                $page->addGallery($gallery, $galleryInfo['description'], $galleryInfo['is_single']);
            }
        }

        return $this;
    }

    protected function removeDeprecatedGalleries()
    {
        foreach ($this->differ->deprecatedGalleries() as $pageName => $galleries) {
            $page = Page::where('name', $pageName)->first();
            foreach ($galleries as $gallery) {
                $doomed = $page->galleries->where('name', $gallery)->first();
                $doomed->delete();
            }
        }

        return $this;
    }

    protected function removeDeprecatedTextblocks()
    {
        foreach ($this->differ->deprecatedTextblocks() as $pageName => $textblocks) {
            $page = Page::where('name', $pageName)->first();
            foreach ($textblocks as $textblock) {
                $doomed = $page->textblocks->where('name', $textblock)->first();
                $doomed->delete();
            }
        }

        return $this;
    }

    protected function removeDeprecatedPages()
    {
        foreach ($this->differ->deprecatedPages() as $pageName) {
            $page = Page::where('name', $pageName)->first();
            $page->delete();
        }

        return $this;
    }

    public function additions()
    {
        return $this->changeSummaryArray(
            $this->differ->newPages(),
            $this->differ->newTextblocks(),
            $this->differ->newGalleries()
        );
    }

    public function deletions()
    {
        return $this->changeSummaryArray(
            $this->differ->deprecatedPages(),
            $this->differ->deprecatedTextblocks(),
            $this->differ->deprecatedGalleries()
        );
    }

    protected function changeSummaryArray($diffPages, $diffTextblocks, $diffGalleries)
    {
        $pages = [];
        foreach ($diffPages as $page) {
            $this->initialiseSummaryPage($pages, $page);
        }
        foreach ($diffTextblocks as $page => $textblocks) {
            $this->initialiseSummaryPage($pages, $page);
            $pages[$page]['textblocks'] = implode(', ', $textblocks);
        }
        foreach ($diffGalleries as $page => $galleries) {
            $this->initialiseSummaryPage($pages, $page);
            $pages[$page]['galleries'] = implode(', ', $galleries);
        }
        $data = [];
        foreach ($pages as $page => $content) {
            $data[] = [
                $page,
                $content['textblocks'],
                $content['galleries']
            ];
        }

        return $data;
    }

    private function initialiseSummaryPage(&$summary, $page)
    {
        if(! array_key_exists($page, $summary)) {
            $summary[$page] = [
                'textblocks' => '',
                'galleries'  => ''
            ];
        }
    }

}
