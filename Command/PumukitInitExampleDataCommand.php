<?php

namespace Pumukit\PaellaStatsBundle\Command;

use Doctrine\ODM\MongoDB\DocumentManager;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
//use Symfony\Component\HttpFoundation\File\UploadedFile;

use Pumukit\PaellaStatsBundle\Document\Geolocation;

class PumukitInitExampleDataCommand extends ContainerAwareCommand
{
    private $dm = null;

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
        $this->dm = $this->getContainer()->get('doctrine_mongodb')->getManager();

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
        $clientips = array('213.73.40.242',
                            '193.146.99.17',
                            '156.35.33.105',
                            '150.214.204.231',
        );

        $sessions = array('882qa12rvak02o7qjfrfa0k8j0',
                            '882qa12rvak02o7qa5g2a0k8j0',
                            '882qa23fvak02o7qjfrfa0k8j0',
                            '882q82crvak02o7qjfrfa0k8j0',
        );

        /*$geo = array(
                    array(
                        'continent' => 'Europe',
                        'continentCode' => 'EU',
                        'country' => 'Spain',
                        'countryCode' => 'ES',
                        'subCountry' => 'Barcelona',
                        'subCountryCode' => 'B',
                        'city' => 'Barcelona',
                        'location' => array(
                                        'latitude' => '41.397842',
                                        'longitude' => '2.201859',
                                        'timeZone' => 'Europe/Madrid',
                        ),
                        'postal' => '08005',
                    ), array(
                        'continent' => 'Europe',
                        'continentCode' => 'EU',
                        'country' => 'Spain',
                        'countryCode' => 'ES',
                        'subCountry' => 'Malaga',
                        'subCountryCode' => 'MA',
                        'city' => 'Malaga',
                        'location' => array(
                                        'latitude' => '36.715572',
                                        'longitude' => ' -4.422631',
                                        'timeZone' => 'Europe/Madrid',
                        ),
                        'postal' => '29001',
                    ), array(
                        'continent' => 'Europe',
                        'continentCode' => 'EU',
                        'country' => 'Spain',
                        'countryCode' => 'ES',
                        'subCountry' => 'Asturias',
                        'subCountryCode' => 'A',
                        'city' => 'Asturias',
                        'location' => array(
                                        'latitude' => '43.545657',
                                        'longitude' => ' -5.663072',
                                        'timeZone' => 'Europe/Madrid',
                        ),
                        'postal' => '33201',
                    ), array(
                        'continent' => 'Europe',
                        'continentCode' => 'EU',
                        'country' => 'Spain',
                        'countryCode' => 'ES',
                        'subCountry' => 'Galicia',
                        'subCountryCode' => 'G',
                        'city' => 'Galicia',
                        'location' => array(
                                        'latitude' => '42.982991',
                                        'longitude' => ' -7.568308',
                                        'timeZone' => 'Europe/Madrid',
                        ),
                        'postal' => '27294',
                    ),
        );*/

        $initTime = (new \DateTime('2 years ago'))->getTimestamp();
        $endTime = (new \DateTime())->getTimestamp();

        $progress = new \Symfony\Component\Console\Helper\ProgressBar($output, count($allMmobjs));
        $output->writeln("\nCreating test views on ViewsLog...");
        $progress->setFormat('verbose');
        $progress->start();

        $logs = array();
        foreach ($allMmobjs as $id => $mmobj) {
            $clientip = $clientips[array_rand($clientips)];
            //$userGeolocation = $geo[array_rand($geo)];
            $session = $sessions[array_rand($sessions)];
            $useragent = $useragents[array_rand($useragents)];

            $progress->setProgress($id);
            for ($i = rand(1, 1000); $i > 0; --$i) {
                $randTimestamp = rand($initTime, $endTime);
                $in = rand(0, $mmobj->getDuration());
                $out = $in + 5;

                $logs[] = array(
                    'date' => new \MongoDate($randTimestamp),
                    'ip' => $clientip,
                    //'geolocation' => $userGeolocation,
                    'user' => null,
                    'session' => $session,
                    'userAgent' => $useragent,
                    'multimediaObject' => new \MongoId($mmobj->getId()),
                    'series' => new \MongoId($mmobj->getSeries()->getId()),
                    'inPoint' => $in,
                    'outPoint' => $out,
                    'isLive' => false,
                    'isProcessed' => false,
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
