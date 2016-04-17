<?php
use Michaeljoyner\Edible\ContentWriter;
use Michaeljoyner\Edible\ContentWriterFactory;

/**
 * Created by PhpStorm.
 * User: mooz
 * Date: 4/17/16
 * Time: 7:37 AM
 */
class ContentWriterFactoryTest extends TestCase
{
    /**
     *@test
     */
    public function it_produces_a_valid_content_writer_instance()
    {
        $writer = ContentWriterFactory::makeWriter();

        $this->assertInstanceOf(ContentWriter::class, $writer);
    }
}