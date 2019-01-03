<?php

namespace App\Tests;

use App\Kernel;
use Doctrine\DBAL\Connection;
use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\DependencyInjection\ContainerInterface;

abstract class AppTestCase extends KernelTestCase
{
    /**
     * @var EntityManager
     */
    protected $em;

    /**
     * @var Connection
     */
    protected $conn;

    /**
     * @var EntityFactory
     */
    protected $entityFactory;

    public function setUp()
    {
        parent::setUp();

        static::bootKernel();
        $this->setUpSqlDb();

        $this->entityFactory = new EntityFactory();
    }

    protected function tearDown()
    {
        parent::tearDown();

        $this->tearDownSqlDb();
    }

    /**
     * Assert value in json equals to expected
     *
     * @param        $expected
     * @param        $json
     * @param string $path
     */
    protected function assertJsonValue($expected, $json, $path = '')
    {
        self::assertJson($json, 'Not valid json');

        $json = json_decode($json, true);

        if (is_object($json)) {
            $json = (array)$json;
        }

        if (!is_array($json)) {
            self::fail('Path ' . $path . ' not found in JSON');
        }

        $path = trim($path, '/');

        if ($path !== '') {
            $pathParts = explode('/', $path);

            foreach ($pathParts as $pathPart) {
                if (is_object($json)) {
                    $json = (array)$json;
                }

                if (array_key_exists($pathPart, $json)) {
                    $json = $json[$pathPart];
                } else {
                    self::fail('Path ' . $path . ' not found in JSON');
                }
            }
        }

        $this->assertEquals($expected, $json);
    }

    /**
     * Assert JSON path exists
     *
     * @param string $json
     * @param string $path
     *
     * @return bool
     */
    protected function assertJsonKey(string $json, string $path): bool
    {
        self::assertJson($json, 'Not valid json');

        $json = json_decode($json, true);

        if (is_object($json)) {
            $json = (array)$json;
        }

        if (!is_array($json)) {
            self::fail('Path ' . $path . ' not found in JSON');
        }

        $path = trim($path, '/');

        if ($path !== '') {
            $pathParts = explode('/', $path);

            foreach ($pathParts as $pathPart) {
                if (is_object($json)) {
                    $json = (array)$json;
                }

                if (array_key_exists($pathPart, $json)) {
                    $json = $json[$pathPart];
                } else {
                    self::fail('Path ' . $path . ' not found in JSON');
                }
            }
        }

        return true;
    }

    /**
     * Assert value in json equals to expected
     *
     * @param        $expected
     * @param        $json
     * @param string $path
     */
    protected function assertJsonCount($expected, $json, $path = '')
    {
        self::assertJson($json, 'Not valid json');

        $json = json_decode($json, true);

        if (is_object($json)) {
            $json = (array)$json;
        }
        if ($path !== '') {
            $pathParts = explode('/', $path);

            foreach ($pathParts as $pathPart) {
                if (is_object($json)) {
                    $json = (array)$json;
                }

                if (array_key_exists($pathPart, $json)) {
                    $json = $json[$pathPart];
                } else {
                    self::fail('Path ' . $path . ' not found in JSON');
                }
            }
        }

        $this->assertCount($expected, $json);
    }

    /**
     * @return ContainerInterface
     */
    protected function getContainer(): ContainerInterface
    {
        $container = static::$kernel->getContainer();

        return $container;
    }

    private function setUpSqlDb()
    {
        /** @var EntityManager $em */
        $this->em = static::$kernel
            ->getContainer()
            ->get('doctrine.orm.entity_manager');

        $conn = $this->em->getConnection();
        $conn->beginTransaction();
        $this->conn = clone $conn;
    }

    private function tearDownSqlDb()
    {
        if ($this->conn) {
            $this->conn->rollBack();
            $this->conn->close();
        }
    }

    /**
     * @inheritDoc
     */
    protected static function getKernelClass()
    {
        return Kernel::class;
    }
}
