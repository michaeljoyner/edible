<?php
use Michaeljoyner\Edible\ContentRepository;
use Michaeljoyner\Edible\Models\Page;

/**
 * Created by PhpStorm.
 * User: mooz
 * Date: 1/26/16
 * Time: 8:34 AM
 */
class ContentTest extends TestCase
{

    /**
     * @test
     */
    public function a_new_page_can_be_created()
    {
        $contentRepo = new ContentRepository;
        $contentRepo::makePage('home');

        $this->assertCount(1, Page::where('name', 'home')->get());
    }

    /**
     * @test
     */
    public function a_page_can_be_retrieved_from_the_content_repository()
    {
        $contentRepo = new ContentRepository;
        $contentRepo::makePage('home');
        $contentRepo::makePage('about');

        $page = $contentRepo->getPageByName('home');

        $this->assertEquals('home', $page->name);
    }

    /**
     *@test
     */
    public function the_content_repo_can_list_the_current_pages()
    {
        $contentRepo = new ContentRepository;
        $contentRepo::makePage('home');
        $contentRepo::makePage('about');

        $this->assertEquals(['about', 'home'], $contentRepo->listPages());
    }

    /**
     *@test
     */
    public function the_content_repo_can_tell_if_a_page_exists()
    {
        $contentRepo = new ContentRepository;
        $contentRepo::makePage('home');

        $this->assertTrue($contentRepo->pageExists('home'));
        $this->assertFalse($contentRepo->pageExists('login'));
    }

    /**
     *@test
     */
    public function the_content_repo_can_delete_a_page()
    {
        $contentRepo = new ContentRepository;
        $contentRepo::makePage('home');

        $this->assertCount(1, Page::where('name', 'home')->get());

        $contentRepo->deleteByName('home');

        $this->assertCount(0, Page::where('name', 'home')->get());
    }

    /**
     *@test
     */
    public function the_content_repo_can_fetch_all_pages_with_textblocks_and_galleries()
    {
        $repo = new ContentRepository();
        $home = Page::create(['name' => 'home', 'description' => 'The homepage']);
        $about = Page::create(['name' => 'about', 'description' => 'The about page']);
        $contact = Page::create(['name' => 'contact', 'description' => 'The contact page']);
        $home->addTextblock('intro', 'The homepage intro');
        $home->addTextblock('spiel', 'Company story', true);
        $home->addGallery('slider', 'Homepage banner slide images');
        $about->addTextblock('intro', 'The about page intro');
        $about->addTextblock('spiel', 'My story', true);
        $about->addGallery('slider', 'About page banner slide images');

        $collection = $repo->getAll();

        $this->assertCount(3, $collection);
        $this->assertCount(2, $collection->where('name', 'home')->first()->textblocks);
        $this->assertCount(2, $collection->where('name', 'about')->first()->textblocks);
        $this->assertCount(0, $collection->where('name', 'contact')->first()->textblocks);
        $this->assertCount(1, $collection->where('name', 'home')->first()->galleries);
        $this->assertCount(1, $collection->where('name', 'about')->first()->galleries);
        $this->assertCount(0, $collection->where('name', 'contact')->first()->galleries);
    }

    /**
     *@test
     */
    public function the_content_repo_can_return_a_collection_of_all_page_names_and_urls()
    {
        $repo = new ContentRepository();
        $home = Page::create(['name' => 'home', 'description' => 'The homepage']);
        $about = Page::create(['name' => 'about', 'description' => 'The about page']);

        $expected = collect([
            ['name' => 'home', 'url' => '/admin/site-content/pages/'.$home->id],
            ['name' => 'about', 'url' => '/admin/site-content/pages/'.$about->id]
        ]);

        $this->assertEquals($expected, $repo->getPageListWithUrls());
    }



}
