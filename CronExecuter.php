<?php
/**
 *
 */

namespace SymfonyContrib\Bundle\CronBundle;

use Doctrine\Orm\EntityManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use SymfonyContrib\Bundle\CronBundle\Entity\Cron;
use SymfonyContrib\Bundle\CronBundle\Entity\Repository\CronRepository;

class CronExecuter implements ContainerAwareInterface
{
    /**
     * @var ContainerInterface
     */
    public $container;

    /**
     * @var EntityManager
     */
    public $em;

    /**
     * @var CronRepository
     */
    public $repo;

    public function __construct()
    {}

    public function helloWorld()
    {
        $path = '/var/www/sites/sexology/app/logs/hello_world.txt';
        file_put_contents($path, 'hello world');
    }

    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    public function getEm()
    {
        return $this->em = $this->em ?: $this->container->get('doctrine.orm.entity_manager');
    }

    /**
     * @return CronRepository
     */
    public function getRepo()
    {
        return $this->repo = $this->repo ?: $this->getEm()->getRepository('CronBundle:Cron');
    }

    public function runDue()
    {
        $due = $this->getRepo()->findDue();

        foreach ($due as $cron) {
            $this->runCron($cron);
        }
    }

    public function runAll()
    {
        $all = $this->getRepo()->findAll();

        foreach ($all as $cron) {
            $this->runCron($cron);
        }
    }

    public function runByName($name)
    {
        $cron = $this->getRepo()->findOneBy(['name' => $name]);

        $this->runCron($cron);
    }

    public function runCron(Cron $cron)
    {
        $em = $this->em;

        $cron->setStatus('running');
        $em->flush($cron);

        list($service, $method) = explode(':', $cron->getJob());

        $service = $this->container->get($service);

        if ($service && $method) {
            $timeStart = microtime(true);
            $return    = $service->{$method}();
            usleep(1000);
            $timeEnd   = microtime(true);
        } else {
            throw new \Exception('Missing service or method.');
        }

        $cron->setStatus('completed');
        $cron->setDurationLast(round(($timeEnd - $timeStart) * 1000));
        $em->flush($cron);
    }


}
