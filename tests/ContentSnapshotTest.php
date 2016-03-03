<?php
use Michaeljoyner\Edible\ContentSnapshot;
use Symfony\Component\Yaml\Parser;

/**
 * Created by PhpStorm.
 * User: mooz
 * Date: 1/28/16
 * Time: 8:30 AM
 */
class ContentSnapshotTest extends TestCase
{
    /**
     *@test
     */
    public function it_knows_if_it_has_a_page_in_store()
    {
        $store = new ContentSnapshot($this->getConfig());

        $this->assertTrue($store->hasPage('home'));
        $this->assertTrue($store->hasPage('about'));
        $this->assertTrue($store->hasPage('contact'));
        $this->assertFalse($store->hasPage('shop'));
    }

    /**
     *@test
     */
    public function it_can_return_a_list_of_pages_in_the_store()
    {
        $store = new ContentSnapshot($this->getConfig());

        $this->assertEquals(['home', 'about', 'contact'], $store->listPages());
    }

    /**
     * @test
     */
    public function it_knows_if_it_has_a_given_textblock_in_the_store()
    {
        $store = new ContentSnapshot($this->getConfig());

        $this->assertTrue($store->hasTextblock('intro', 'home'));
        $this->assertTrue($store->hasTextblock('spiel', 'home'));
        $this->assertTrue($store->hasTextblock('intro', 'about'));
        $this->assertTrue($store->hasTextblock('spiel', 'about'));
        $this->assertFalse($store->hasTextblock('outro', 'home'));
        $this->assertFalse($store->hasTextblock('outro', 'contact'));
    }

    /**
     * @test
     */
    public function it_can_return_a_list_of_textblocks_for_each_page()
    {
        $store = new ContentSnapshot($this->getConfig());

        $expected = [
            'home' => ['intro', 'spiel'],
            'about' => ['intro', 'spiel'],
            'contact' => []
        ];

        $this->assertEquals($expected, $store->listTextblocks());
    }

    /**
     *@test
     */
    public function it_knows_if_the_store_has_a_gallery_by_the_given_name_for_a_given_page()
    {
        $store = new ContentSnapshot($this->getConfig());

        $this->assertTrue($store->hasGallery('slider', 'home'));
        $this->assertTrue($store->hasGallery('slider', 'about'));
        $this->assertFalse($store->hasGallery('porn', 'contact'));
    }

    /**
     *@test
     */
    public function it_returns_a_list_of_galleries_for_each_page()
    {
        $store = new ContentSnapshot($this->getConfig());

        $expected = [
            'home' => ['slider'],
            'about' => ['slider'],
            'contact' => []
        ];

        $this->assertEquals($expected, $store->listGalleries());
    }

    /**
     *@test
     */
    public function the_snapshot_can_return_the_description_for_given_page()
    {
        $snapshot = new ContentSnapshot($this->getConfig());

        $this->assertEquals('The homepage', $snapshot->getPageDescription('home'));
    }

    /**
     *@test
     */
    public function the_snapshot_can_return_the_info_for_a_given_textblock_on_a_given_page()
    {
        $snapshot = new ContentSnapshot($this->getConfig());

        $this->assertEquals(['description' => 'The homepage intro', 'allows_html' => false], $snapshot->getTextblockInfo('intro', 'home'));
    }

    /**
     *@test
     */
    public function the_snapshot_can_return_the_info_for_a_given_gallery_on_a_given_page()
    {
        $snapshot = new ContentSnapshot($this->getConfig());

        $this->assertEquals(['description' => 'Homepage banner slide images', 'is_single' => false], $snapshot->getGalleryInfo('slider', 'home'));
    }

    private function getConfig($filename = 'tests/assets/basic.yml')
    {
        $parser = new Parser();
        return $parser->parse(file_get_contents($filename));
    }
}