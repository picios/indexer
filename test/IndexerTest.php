<?php

namespace Picios\Indexer\Test;

use PHPUnit\Framework\TestCase;
use Picios\Indexer\Indexer;

class IndexerTest extends TestCase
{
    public function testIndexer()
    {
        $indexer = new Indexer();
        $indexer->setStopwords(['lorem', 'dolor']);
        $fullIndex = $indexer->getFullIndex('Lorem ipsum dolor sit amet. Lorem ipsum dolor sit amet.');

        $this->assertEquals('ipsum sit amet', $fullIndex);
    }

}