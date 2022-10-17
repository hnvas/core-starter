<?php
declare(strict_types = 1);

namespace App\Tests\Functional\Util;

use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Symfony\Bridge\Doctrine\DataFixtures\ContainerAwareLoader;

trait FixtureAwareTrait
{

    protected ?ORMExecutor          $fixtureExecutor = null;
    protected ?ContainerAwareLoader $fixtureLoader   = null;

    protected function loadFixtures(array $fixtures): void
    {
        $container = self::$kernel->getContainer();

        foreach ($fixtures as $fixtureClass) {
            /** @var FixtureInterface $fixture */
            $fixture = $container->get($fixtureClass);
            $this->addFixture($fixture);
        }
    }

    protected function addFixture(FixtureInterface $fixture): void
    {
        $this->getFixtureLoader()->addFixture($fixture);
    }

    protected function executeFixtures(): void
    {
        $this->getFixtureExecutor()->execute(
            $this->getFixtureLoader()->getFixtures()
        );
    }

    private function getFixtureExecutor(): ORMExecutor
    {
        if (!$this->fixtureExecutor) {
            /** @var \Doctrine\ORM\EntityManager $entityManager */
            $entityManager = self::$kernel->getContainer()
                                          ->get('doctrine')
                                          ->getManager();

            $this->fixtureExecutor = new ORMExecutor(
                $entityManager,
                new ORMPurger($entityManager)
            );
        }

        return $this->fixtureExecutor;
    }

    private function getFixtureLoader(): ContainerAwareLoader
    {
        if (!$this->fixtureLoader) {
            $this->fixtureLoader = new ContainerAwareLoader(self::$kernel->getContainer());
        }

        return $this->fixtureLoader;
    }
}
