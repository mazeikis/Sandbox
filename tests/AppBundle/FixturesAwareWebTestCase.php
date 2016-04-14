<?php

namespace Tests\AppBundle;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Input\ArrayInput;

Class FixturesAwareWebTestCase extends WebTestCase
{
    public function setUp()
    {
        $client = static::createClient();
        $kernel = $client->getContainer()->get('kernel');
        $application = new Application($kernel);
        $application->setAutoExit(false);

        $input = new ArrayInput(array('command' => 'doctrine:fixtures:load', '-n'));
        $application->run($input);
    }
}
