<?php
use Michaeljoyner\Edible\ContentRepository;
use Michaeljoyner\Edible\ContentSnapshotFactory;
use Michaeljoyner\Edible\ContentWriter;
use Symfony\Component\Yaml\Parser;
use Michaeljoyner\Edible\Models\Page;
use Michaeljoyner\Edible\Models\Textblock;
use Michaeljoyner\Edible\Models\Gallery;

/**
 * Created by PhpStorm.
 * User: mooz
 * Date: 1/29/16
 * Time: 1:08 PM
 */
class ContentWriterTest extends TestCase
{

    /**
     * @test
     */
    public function it_correctly_writes_the_database_representation_of_the_editable_content_structure()
    {
        $fileSnapshot = (new ContentSnapshotFactory(new Parser()))->makeSnapshotFromYmlFile('tests/assets/basic.yml');
        $databaseSnapshot = (new ContentSnapshotFactory(new Parser()))->makeSnapshotFromRepo(new ContentRepository());
        $writer = new ContentWriter($fileSnapshot, $databaseSnapshot);

        $writer->setContentStructure();

        $this->seeBasicConfigInDatabase();
    }

    /**
     * @test
     */
    public function it_correctly_revises_current_content_structure_with_new_file_structure()
    {
        $this->it_correctly_writes_the_database_representation_of_the_editable_content_structure();
        $fileSnapshot = (new ContentSnapshotFactory(new Parser()))->makeSnapshotFromYmlFile('tests/assets/revised.yml');
        $databaseSnapshot = (new ContentSnapshotFactory(new Parser()))->makeSnapshotFromRepo(new ContentRepository());
        $writer = new ContentWriter($fileSnapshot, $databaseSnapshot);

        $writer->setContentStructure();

        $this->seeRevisionsInDatabase();
    }

    /**
     * @test
     */
    public function it_can_show_the_additions_it_will_make_in_array_form()
    {
        $fileSnapshot = (new ContentSnapshotFactory(new Parser()))->makeSnapshotFromYmlFile('tests/assets/basic.yml');
        $databaseSnapshot = (new ContentSnapshotFactory(new Parser()))->makeSnapshotFromRepo(new ContentRepository());
        $writer = new ContentWriter($fileSnapshot, $databaseSnapshot);

        $expected = [
            ['home', 'intro, spiel', 'slider'],
            ['about', 'intro, spiel', 'slider'],
            ['contact', '', '']
        ];

        $this->assertEquals($expected, $writer->additions());
    }

    /**
     * @test
     */
    public function it_can_show_deletetions_it_will_make_in_array_form()
    {
        $fileSnapshot = (new ContentSnapshotFactory(new Parser()))->makeSnapshotFromYmlFile('tests/assets/revised.yml');
        $databaseSnapshot = (new ContentSnapshotFactory(new Parser()))->makeSnapshotFromYmlFile('tests/assets/basic.yml');
        $writer = new ContentWriter($fileSnapshot, $databaseSnapshot);

        $expected = [
            ['about', 'intro, spiel', 'slider'],
            ['home', 'spiel', '']
        ];

        $this->assertEquals($expected, $writer->deletions());
    }

    /**
     *@test
     */
    public function the_writer_will_not_overwrite_or_change_content_that_is_not_removed_in_the_yml_file()
    {
        //set up existing content
        $home = Page::create(['name' => 'home', 'description' => 'The homepage']);
        $home->addTextblock('intro', 'The homepage intro', false, 'welcome to my homepage');
        //set up writer and write new content structure
        $fileSnapshot = (new ContentSnapshotFactory(new Parser()))->makeSnapshotFromYmlFile('tests/assets/basic.yml');
        $databaseSnapshot = (new ContentSnapshotFactory(new Parser()))->makeSnapshotFromRepo(new ContentRepository());
        $writer = new ContentWriter($fileSnapshot, $databaseSnapshot);
        $writer->setContentStructure();
        //the content value for the homepage intro should still exist
        $this->assertEquals('welcome to my homepage', Page::where('name', 'home')->first()->textblocks->where('name', 'intro')->first()->content);
    }

    protected function seeBasicConfigInDatabase()
    {
        // $this->seeInDatabase('ec_pages', [
        //     'name' => 'home'
        // ]);
        $this->assertCount(1, Page::where('name', 'home')->get());
        $this->seeInDatabase('ec_pages', [
            'name' => 'about'
        ]);
        $this->seeInDatabase('ec_pages', [
            'name' => 'contact'
        ]);
        $this->seeInDatabase('ec_textblocks', [
            'name'        => 'intro',
            'description' => 'The homepage intro',
            'allows_html' => 0
        ]);
        $this->seeInDatabase('ec_textblocks', [
            'name'        => 'intro',
            'description' => 'The about page intro',
            'allows_html' => 0
        ]);
        $this->seeInDatabase('ec_textblocks', [
            'name'        => 'spiel',
            'description' => 'Company story',
            'allows_html' => 1
        ]);
        $this->seeInDatabase('ec_textblocks', [
            'name'        => 'spiel',
            'description' => 'My story',
            'allows_html' => 1
        ]);
        $this->seeInDatabase('ec_galleries', [
            'name'        => 'slider',
            'description' => 'Homepage banner slide images',
            'is_single'   => 0
        ]);
        $this->seeInDatabase('ec_galleries', [
            'name'        => 'slider',
            'description' => 'About page banner slide images',
            'is_single'   => 0
        ]);
    }

    protected function seeRevisionsInDatabase()
    {
        $this->seeInDatabase('ec_pages', [
            'name' => 'home'
        ]);
        $this->notSeeInDatabase('ec_pages', [
            'name' => 'about'
        ]);
        $this->seeInDatabase('ec_pages', [
            'name' => 'contact'
        ]);
        $this->seeInDatabase('ec_textblocks', [
            'name'        => 'intro',
            'description' => 'The homepage intro',
            'allows_html' => 0
        ]);
        $this->seeInDatabase('ec_textblocks', [
            'name'        => 'intro',
            'description' => 'The Contact intro',
            'allows_html' => 0
        ]);
        $this->seeInDatabase('ec_textblocks', [
            'name'        => 'outro',
            'description' => 'The homepage outro',
            'allows_html' => 1
        ]);
        $this->notSeeInDatabase('ec_textblocks', [
            'name'        => 'intro',
            'description' => 'The about page intro',
            'allows_html' => 0
        ]);
        $this->notSeeInDatabase('ec_textblocks', [
            'name'        => 'spiel',
            'description' => 'Company story',
            'allows_html' => 1
        ]);
        $this->notSeeInDatabase('ec_textblocks', [
            'name'        => 'spiel',
            'description' => 'My story',
            'allows_html' => 1
        ]);
        $this->seeInDatabase('ec_galleries', [
            'name'        => 'slider',
            'description' => 'Homepage banner slide images',
            'is_single'   => 0
        ]);
        $this->notSeeInDatabase('ec_galleries', [
            'name'        => 'slider',
            'description' => 'About page banner slide images',
            'is_single'   => 0
        ]);
    }



}
