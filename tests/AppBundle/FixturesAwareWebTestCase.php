<?php

namespace Tests\AppBundle;

use Symfony\Bridge\Doctrine\DataFixtures\ContainerAwareLoader;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Input\ArrayInput;
use Doctrine\Common\DataFixtures\Loader;
use AppBundle\DataFixtures\ORM\LoadTestData;
use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;

Class FixturesAwareWebTestCase extends WebTestCase
{
    protected $em;
    protected $container;
    protected $client;

    public function __construct($name = null, array $data = array(), $dataName = '')
    {
        parent::__construct($name, $data, $dataName);

        if (!static::$kernel) {
            static::$kernel = self::createKernel(array(
                'environment' => 'test',
                'debug'       => true
            ));
            static::$kernel->boot();
        }

        $this->container = static::$kernel->getContainer();
        $this->em = $this->container->get('doctrine.orm.entity_manager');
        $this->client = static::createClient();

    }

    public function setUp()
    {
        $loader   = new Loader();
        $purger   = new ORMPurger();
        $executor = new ORMExecutor($this->em, $purger);

        $testData = new LoadTestData();
        $testData->setContainer($this->container);
        $loader->addFixture($testData);

        $executor->execute($loader->getFixtures());
    }
}
