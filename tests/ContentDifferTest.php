<?php
use Michaeljoyner\Edible\ContentDiffer;
use Michaeljoyner\Edible\ContentSnapshotFactory;
use Symfony\Component\Yaml\Parser;

/**
 * Created by PhpStorm.
 * User: mooz
 * Date: 1/29/16
 * Time: 10:26 AM
 */
class ContentDifferTest extends TestCase
{
    /**
     *@test
     */
    public function it_can_list_new_textblocks_per_page()
    {
        $fileSnapshot = $this->getFileSnapshot();
        $databaseSnapshot = $this->getFakeDatabaseSnapshot();
        $differ = new ContentDiffer($fileSnapshot, $databaseSnapshot);

        $this->assertEquals(['home' => ['outro'], 'contact' => ['intro']], $differ->newTextblocks());
    }

    /**
     *@test
     */
    public function it_can_list_deprecated_textblocks_per_page()
    {
        $fileSnapshot = $this->getFileSnapshot();
        $databaseSnapshot = $this->getFakeDatabaseSnapshot();
        $differ = new ContentDiffer($fileSnapshot, $databaseSnapshot);

        $this->assertEquals(['home' => ['spiel'], 'about' => ['intro', 'spiel']], $differ->deprecatedTextblocks());
    }

    /**
     *@test
     */
    public function it_can_list_new_galleries_per_page()
    {
        $fileSnapshot = $this->getFileSnapshot();
        $databaseSnapshot = $this->getFakeDatabaseSnapshot();
        $differ = new ContentDiffer($fileSnapshot, $databaseSnapshot);

        $this->assertEquals(['contact' => ['slider']], $differ->newGalleries());
    }

    /**
     *@test
     */
    public function it_can_list_deprecated_galleries_per_page()
    {
        $fileSnapshot = $this->getFileSnapshot();
        $databaseSnapshot = $this->getFakeDatabaseSnapshot();
        $differ = new ContentDiffer($fileSnapshot, $databaseSnapshot);

        $this->assertEquals(['about' => ['slider']], $differ->deprecatedGalleries());
    }

    /**
     *@test
     */
    public function it_can_list_completely_new_pages()
    {
        $fileSnapshot = $this->getFileSnapshot();
        $databaseSnapshot = $this->getFakeDatabaseSnapshot();
        $differ = new ContentDiffer($fileSnapshot, $databaseSnapshot);

        $this->assertEquals([], $differ->newPages());

        $databaseSnapshot2 = $this->getFileSnapshot();
        $fileSnapshot2 = $this->getFakeDatabaseSnapshot();
        $differ2 = new ContentDiffer($fileSnapshot2, $databaseSnapshot2);

        $this->assertEquals(['about'], $differ2->newPages());
    }

    /**
     *@test
     */
    public function it_can_list_deprecated_pages()
    {
        $fileSnapshot = $this->getFileSnapshot();
        $databaseSnapshot = $this->getFakeDatabaseSnapshot();
        $differ = new ContentDiffer($fileSnapshot, $databaseSnapshot);

        $this->assertEquals(['about'], $differ->deprecatedPages());
    }

    private function getFileSnapshot()
    {
        $factory = new ContentSnapshotFactory(new Parser());
        return $factory->makeSnapshotFromYmlFile('tests/assets/revised.yml');
    }

    private function getFakeDatabaseSnapshot()
    {
        $factory = new ContentSnapshotFactory(new Parser());
        return $factory->makeSnapshotFromYmlFile('tests/assets/basic.yml');
    }

}
