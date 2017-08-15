<?php

namespace Odan\Test;

use Aura\SqlQuery\QueryFactory;

/**
 * QueryTest
 */
class QueryTest extends BaseTest
{
    /**
     * Test create object.
     *
     * @return void
     */
    public function testInstance()
    {
        $object = $this->getQuery();
        $this->assertInstanceOf(QueryFactory::class, $object);
    }
}
