<?php
use Michaeljoyner\Edible\ContentRepository;

/**
 * Created by PhpStorm.
 * User: mooz
 * Date: 1/26/16
 * Time: 9:11 AM
 */
class ContentPagesTest extends TestCase
{


    /**
     *@test
     */
    public function a_textblock_can_be_added_to_a_page()
    {
        $this->page = ContentRepository::makePage('test');
        $this->page->addTextblock('intro', 'an introduction', true);

        $this->seeInDatabase('ec_textblocks', [
            'ec_page_id' => $this->page->id,
            'name' => 'intro',
            'description' => 'an introduction'
        ]);
    }

    /**
     *@test
     */
    public function a_textblocks_name_must_be_unique_to_its_page()
    {
        $this->page = ContentRepository::makePage('test');
        $this->page->addTextblock('intro', 'an introduction', true);

        $this->setExpectedException(\Exception::class);
        $this->page->addTextblock('intro', 'an introduction', true);
    }

    /**
     *@test
     */
    public function a_gallery_can_be_added_to_a_page()
    {
        $this->page = ContentRepository::makePage('test');
        $this->page->addGallery('my gallery', 'some images');

        $this->seeInDatabase('ec_galleries', [
            'ec_page_id' => $this->page->id,
            'name' => 'my gallery',
            'description' => 'some images'
        ]);
    }

    /**
     *@test
     */
    public function a_gallery_must_have_a_unique_name_for_its_page()
    {
        $this->page = ContentRepository::makePage('test');
        $this->page->addGallery('intro', 'an introduction');

        $this->setExpectedException(\Exception::class);
        $this->page->addGallery('intro', 'an introduction');
    }

    /**
     * @test
     */
    public function the_content_of_a_pages_textblock_can_be_retrieved_by_name()
    {
        $page = ContentRepository::makePage('test');
        $page->addTextblock('intro', 'an introduction', false, 'Once I was a warthog');

        $this->assertEquals('Once I was a warthog', $page->textFor('intro'));
    }

    /**
     *@test
     */
    public function text_for_returns_a_default_value_if_supplied_and_if_no_content_value_exists()
    {
        $page = ContentRepository::makePage('test');
        $page->addTextblock('intro', 'an introduction', false, '');

        $this->assertEquals('this is the default', $page->textFor('intro', 'this is the default'));
        $this->assertEquals('also a default', $page->textFor('nonexisting block', 'also a default'));
    }

    // /**
    //  *@test
    //  */
    // public function a_collection_of_a_pages_gallery_images_can_be_retrieved_by_name_from_the_page()
    // {
    //     $page = ContentRepository::makePage('test');
    //     $gallery = $page->addGallery('pics', 'images');
    //
    //     $gallery->addImage('tests/testpic1.png');
    //     $gallery->addImage('tests/testpic2.png');
    //
    //     $result = $page->imagesOf('pics');
    //
    //     $this->assertCount(2, $result);
    //     $this->assertInstanceOf(\Illuminate\Support\Collection::class, $result);
    //
    //     $gallery->clearMediaCollection();
    // }
}
