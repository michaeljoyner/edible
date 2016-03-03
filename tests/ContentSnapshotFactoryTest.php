<?php
use Michaeljoyner\Edible\ContentRepository;
use Michaeljoyner\Edible\ContentSnapshotFactory;
use Michaeljoyner\Edible\Exceptions\InvalidStructureException;
use Symfony\Component\Yaml\Parser;
use Michaeljoyner\Edible\Models\Page;

/**
 * Created by PhpStorm.
 * User: mooz
 * Date: 1/29/16
 * Time: 9:35 AM
 */
class ContentSnapshotFactoryTest extends TestCase
{
    /**
     * @test
     */
    public function it_builds_a_snapshot_config_array_from_the_content_repository()
    {
        $this->seedDatabaseWithBasicContentStructure();
        $repo = new ContentRepository();
        $expected = $this->getExpectedArrayValue();
        $factory = new ContentSnapshotFactory(new Parser());

        $actual = $factory->createConfigFromRepository($repo);

        $this->assertEquals($expected, $actual);
    }

    /**
     *@test
     */
    public function it_builds_up_a_snapshot_config_from_a_yml_file()
    {
        $filename = 'tests/assets/basic.yml';
        $expected = $this->getExpectedArrayValue();
        $factory = new ContentSnapshotFactory(new Parser());

        $actual = $factory->createConfigFromYml($filename);
        $this->assertEquals($expected, $actual);
    }

    /**
     *@test
     */
    public function it_throws_an_exception_for_missing_attributes()
    {
        $factory = new ContentSnapshotFactory(new Parser());

        $this->setExpectedException(InvalidStructureException::class);

        $factory->makeSnapshotFromYmlFile('tests/assets/missingattributes.yml');
    }

    /**
     *@test
     */
    public function it_throws_the_correct_exception_for_an_invalid_yml_file()
    {
        $factory = new ContentSnapshotFactory(new Parser());

        $this->setExpectedException(InvalidStructureException::class);

        $factory->makeSnapshotFromYmlFile('tests/assets/invalid.yml');
    }

    /**
     *@test
     */
    public function it_throws_an_exception_if_there_is_no_pages_array_described_in_the_yml_file()
    {
        $factory = new ContentSnapshotFactory(new Parser());

        $this->setExpectedException(InvalidStructureException::class);

        $factory->makeSnapshotFromYmlFile('tests/assets/nopages.yml');
    }

    private function seedDatabaseWithBasicContentStructure()
    {
        //build up database content
        $home = Page::create(['name' => 'home', 'description' => 'The homepage']);
        $about = Page::create(['name' => 'about', 'description' => 'The about page']);
        Page::create(['name' => 'contact', 'description' => 'The contact page']);
        $home->addTextblock('intro', 'The homepage intro');
        $home->addTextblock('spiel', 'Company story', true);
        $home->addGallery('slider', 'Homepage banner slide images');
        $about->addTextblock('intro', 'The about page intro');
        $about->addTextblock('spiel', 'My story', true);
        $about->addGallery('slider', 'About page banner slide images');
    }

    private function getExpectedArrayValue()
    {
        return [
            'pages' => [
                'home'    => [
                    'description' => 'The homepage',
                    'textblocks'  => [
                        'intro' => [
                            'description' => 'The homepage intro',
                            'allows_html' => false
                        ],
                        'spiel' => [
                            'description' => 'Company story',
                            'allows_html' => true
                        ]
                    ],
                    'galleries'   => [
                        'slider' => [
                            'description' => 'Homepage banner slide images',
                            'is_single'   => false
                        ]
                    ]
                ],
                'about'   => [
                    'description' => 'The about page',
                    'textblocks'  => [
                        'intro' => [
                            'description' => 'The about page intro',
                            'allows_html' => false
                        ],
                        'spiel' => [
                            'description' => 'My story',
                            'allows_html' => true
                        ]
                    ],
                    'galleries'   => [
                        'slider' => [
                            'description' => 'About page banner slide images',
                            'is_single'   => false
                        ]
                    ]
                ],
                'contact' => [
                    'description' => 'The contact page'
                ]
            ]
        ];
    }
}
