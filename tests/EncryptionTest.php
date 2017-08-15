<?php

namespace Odan\Test;

use Odan\Database\Encryption;

/**
 * @coversDefaultClass \Odan\Database\Encryption
 */
class EncryptionTest extends BaseTest
{
    /**
     * @var Encryption
     */
    protected $encryption;

    /**
     * Return Data object
     *
     * @return Encryption
     */
    public function getEncryption()
    {
        if ($this->encryption === null) {
            $this->encryption = new Encryption($this->getConnection());
        }
        return $this->encryption;
    }

    /**
     * Test create object.
     *
     * @return void
     */
    public function testInstance()
    {
        $object = $this->getEncryption();
        $this->assertInstanceOf(Encryption::class, $object);
    }

    /**
     * Test compress method
     *
     * @return void
     * @covers ::compress
     * @covers ::uncompress
     */
    public function testCompress()
    {
        $db = $this->getConnection();
        $enc = $this->getEncryption();

        $result = $enc->compress('test');
        $result = strtoupper(bin2hex($result));
        $resultFromDb = $db->queryValue("SELECT HEX(COMPRESS('test')) AS result;", 'result');
        $this->assertEquals($resultFromDb, $result);

        // MySQL TO_BASE64 function does not exist
        // https://github.com/travis-ci/travis-ci/issues/4088
        $result = $enc->compress(null);
        $result2 = $db->queryValue("SELECT HEX(COMPRESS(NULL)) AS result;", 'result');
        $this->assertEquals(true, $result === $result2);

        $result = $enc->uncompress(hex2bin('04000000789C2B492D2E0100045D01C1'));
        $resultFromDb = $db->queryValue("SELECT UNCOMPRESS(UNHEX('04000000789C2B492D2E0100045D01C1')) AS result;", 'result');
        $this->assertEquals($resultFromDb, $result);

        $result = $enc->uncompress(null);
        $resultFromDb = $db->queryValue("SELECT UNCOMPRESS(NULL) AS result;", 'result');
        $this->assertEquals(true, $resultFromDb === $result);

        // MySQL TO_BASE64 function does not exist
        // https://github.com/travis-ci/travis-ci/issues/4088
    }
}
