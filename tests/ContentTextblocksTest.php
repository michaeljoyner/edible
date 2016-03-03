<?php
use Michaeljoyner\Edible\ContentRepository;

/**
 * Created by PhpStorm.
 * User: mooz
 * Date: 1/27/16
 * Time: 12:21 AM
 */
class ContentTextblocksTest extends TestCase
{

    /**
     *@test
     */
    public function a_textblocks_content_can_be_set()
    {
        $page = ContentRepository::makePage('home');
        $textblock = $page->addTextblock('intro', 'the introduction', false);
        $textblock->setContent('Once upon a time, in a land far, far away...');

        $this->seeInDatabase('ec_textblocks', [
            'id' => $textblock->id,
            'name' => 'intro',
            'content' => 'Once upon a time, in a land far, far away...'
        ]);

    }

}
