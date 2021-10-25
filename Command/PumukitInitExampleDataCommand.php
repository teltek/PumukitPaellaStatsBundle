<?php

declare(strict_types=1);

namespace Pumukit\PaellaStatsBundle\Command;

use Doctrine\ODM\MongoDB\DocumentManager;
use MongoDB\BSON\ObjectId;
use Pumukit\PaellaStatsBundle\Document\UserAction;
use Pumukit\SchemaBundle\Document\MultimediaObject;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class PumukitInitExampleDataCommand extends Command
{
    private $documentManager;

    public function __construct(DocumentManager $documentManager)
    {
        $this->documentManager = $documentManager;
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setName('pumukit:paella:stats:init:example')
            ->setDescription('Load PuMuKIT example user actions data fixtures to your database')
            ->setHelp(
                <<<'EOT'
                Command to load a data set of data into a database. Useful for init a demo PuMuKIT environment.
EOT
            )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->loadUserAction($output);

        $output->writeln('');
        $output->writeln('<info>Example data load successful</info>');

        return 0;
    }

    private function loadUserAction($output): void
    {
        $mmobjRepo = $this->documentManager->getRepository(MultimediaObject::class);
        $userActionColl = $this->documentManager->getDocumentCollection(UserAction::class);

        $allMmobjs = $mmobjRepo->findStandardBy([]);
        $useragents = ['Mozilla/5.0 PuMuKIT/2.2 (UserAgent Example Data.) Gecko/20100101 Firefox/40.1',
            'Mozilla/5.0 PuMuKIT/2.2 (This is not the user agent you are looking for...) Gecko/20100101 Firefox/40.1',
            'Mozilla/5.0 PuMuKIT/2.2 (The answer to everything: 42) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/41.0.2228.0 Safari/537.36',
            'Mozilla/5.0 PuMuKIT/2.2 (Internet Explorer didn\'t survive) (Windows NT 6.1; WOW64; Trident/7.0; AS; rv:11.0) like Gecko',
        ];
        $clientips = ['213.73.40.242',
            '193.146.99.17',
            '156.35.33.105',
            '150.214.204.231',
        ];

        $sessions = ['882qa12rvak02o7qjfrfa0k8j0',
            '882qa12rvak02o7qa5g2a0k8j0',
            '882qa23fvak02o7qjfrfa0k8j0',
            '882q82crvak02o7qjfrfa0k8j0',
        ];

        $initTime = (new \DateTime('2 years ago'))->getTimestamp();
        $endTime = (new \DateTime())->getTimestamp();

        $progress = new ProgressBar($output, \count($allMmobjs));
        $output->writeln("\nCreating test views on ViewsLog...");
        $progress->setFormat('verbose');
        $progress->start();

        $logs = [];
        foreach ($allMmobjs as $id => $mmobj) {
            $clientip = $clientips[array_rand($clientips)];
            $session = $sessions[array_rand($sessions)];
            $useragent = $useragents[array_rand($useragents)];

            $progress->setProgress($id);
            for ($i = random_int(1, 1000); $i > 0; --$i) {
                $randTimestamp = random_int($initTime, $endTime);
                $in = random_int(0, $mmobj->getDuration());
                $out = $in + 5;

                $logs[] = [
                    'date' => new \MongoDate($randTimestamp),
                    'ip' => $clientip,
                    'user' => null,
                    'session' => $session,
                    'userAgent' => $useragent,
                    'multimediaObject' => new ObjectId($mmobj->getId()),
                    'series' => new ObjectId($mmobj->getSeries()->getId()),
                    'inPoint' => $in,
                    'outPoint' => $out,
                    'isLive' => false,
                    'isProcessed' => false,
                ];
                $mmobj->incNumview();
                $this->documentManager->persist($mmobj);
            }
        }
        $progress->setProgress(count($allMmobjs));
        $userActionColl->batchInsert($logs);
        $this->documentManager->flush();
        $progress->finish();
    }
}
