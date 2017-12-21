<?php

namespace Pumukit\PaellaStatsBundle\Command;

use Doctrine\ODM\MongoDB\DocumentManager;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\HttpFoundation\File\UploadedFile;
//use ZipArchive;
use Pumukit\SchemaBundle\Document\MultimediaObject;
use Pumukit\SchemaBundle\Document\Series;
use Pumukit\SchemaBundle\Document\Pic;
//use Pumukit\SchemaBundle\Document\Tag;
//use Pumukit\SchemaBundle\Document\Person;
//use Pumukit\SchemaBundle\Document\Role;
//use Pumukit\StatsBundle\Document\ViewsLog;

class PumukitInitExampleDataCommand extends ContainerAwareCommand
{
    private $dm = null;
    //private $repo = null;

    protected function configure()
    {
        $this
            ->setName('pumukit:init:paellastatsexample')
            ->setDescription('Load Pumukit example user actions data fixtures to your database')
            ->setHelp(<<<'EOT'

            Command to load a data set of data into a database. Useful for init a demo Pumukit environment.
EOT
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        //$newFile = $this->getContainer()->getParameter('kernel.cache_dir').'/tmp_file.zip';
        $this->dm = $this->getContainer()->get('doctrine_mongodb')->getManager();
        //$this->repo = $this->dm->getRepository('PumukitSchemaBundle:Tag');

        $this->loadUserAction($this->dm, $output);

        $output->writeln('');
        $output->writeln('<info>Example data load successful</info>');
    }

    private function loadUserAction(DocumentManager $dm, $output)
    {
        $mmobjRepo = $dm->getRepository('PumukitSchemaBundle:MultimediaObject');
        $userActionColl = $dm->getDocumentCollection('PumukitPaellaStatsBundle:UserAction');

        $allMmobjs = $mmobjRepo->findStandardBy(array());
        $useragents = array('Mozilla/5.0 PuMuKIT/2.2 (UserAgent Example Data.) Gecko/20100101 Firefox/40.1',
                             'Mozilla/5.0 PuMuKIT/2.2 (This is not the user agent you are looking for...) Gecko/20100101 Firefox/40.1',
                             'Mozilla/5.0 PuMuKIT/2.2 (The answer to everything: 42) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/41.0.2228.0 Safari/537.36',
                             'Mozilla/5.0 PuMuKIT/2.2 (Internet Explorer didn\'t survive) (Windows NT 6.1; WOW64; Trident/7.0; AS; rv:11.0) like Gecko',
        );
        $clientips = array(	'123.213.231.132',
                            '0.0.0.1',
                            '12.12.12.21',
                            '74.125.224.72',
        );
		
		$sessions = array(	'882qa12rvak02o7qjfrfa0k8j0',
                            '882qa12rvak02o7qa5g2a0k8j0',
                            '882qa23fvak02o7qjfrfa0k8j0',
                            '882q82crvak02o7qjfrfa0k8j0',
        );

        $initTime = (new \DateTime('2 years ago'))->getTimestamp();
        $endTime = (new \DateTime())->getTimestamp();

        $clientip = $clientips[array_rand($clientips)];
        $useragent = $useragents[array_rand($useragents)];
		$session = $sessions[array_rand($sessions)];

        $progress = new \Symfony\Component\Console\Helper\ProgressBar($output, count($allMmobjs));
        $output->writeln("\nCreating test views on ViewsLog...");
        $progress->setFormat('verbose');
        $progress->start();

        $logs = array();
        foreach ($allMmobjs as $id => $mmobj) {
            $progress->setProgress($id);
            for ($i = rand(1, 1000); $i > 0; --$i) {
                $randTimestamp = rand($initTime, $endTime);
                $in = rand(0, $mmobj->getDuration());
                $out = $in + 5;

                $logs[] = array(
                    'date' => new \MongoDate($randTimestamp),
                    'ip' => $clientip,
                    "user" => null,
                    "session" => $session,
                    'userAgent' => $useragent,
                    'multimediaObject' => new \MongoId($mmobj->getId()),
                    'series' => new \MongoId($mmobj->getSeries()->getId()),
                    "inPoint" => $in,
                    "outPoint" => $out,
                    "isLive" => false,
                    "isProcessed" => false,
                );
                $mmobj->incNumview();
                $dm->persist($mmobj);
            }
        }
        $progress->setProgress(count($allMmobjs));
        $userActionColl->batchInsert($logs);
        $dm->flush();
        $progress->finish();
    }


}
