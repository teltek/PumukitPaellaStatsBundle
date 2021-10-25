<?php

declare(strict_types=1);

namespace Pumukit\PaellaStatsBundle\Services;

use Doctrine\ODM\MongoDB\DocumentManager;
use MongoDB\BSON\ObjectId;
use Pumukit\PaellaStatsBundle\Document\MultimediaObjectAudience;
use Pumukit\PaellaStatsBundle\Document\UserAction;
use Pumukit\SchemaBundle\Document\MultimediaObject;
use Pumukit\SchemaBundle\Document\Series;

class UserActionService
{
    private $documentManager;
    private $repo;
    private $repoMultimedia;
    private $repoSeries;
    private $repoAudience;

    public function __construct(DocumentManager $documentManager)
    {
        $this->documentManager = $documentManager;
        $this->repo = $this->documentManager->getRepository(UserAction::class);
        $this->repoMultimedia = $this->documentManager->getRepository(MultimediaObject::class);
        $this->repoSeries = $this->documentManager->getRepository(Series::class);
        $this->repoAudience = $this->documentManager->getRepository(MultimediaObjectAudience::class);
    }

    /**
     * Return an array of most viewed Multimedia Objects as results from the criteria/options.
     */
    public function getMmobjsMostViewed(array $criteria = [], array $options = []): array
    {
        $ids = [];

        $viewsCollection = $this->documentManager->getDocumentCollection(UserAction::class);

        $matchExtra = [];
        $mmobjIds = $this->getMmobjIdsWithCriteria($criteria);
        $matchExtra['multimediaObject'] = ['$in' => $mmobjIds];

        $options = $this->parseOptions($options);

        $pipeline = $this->aggrPipeAddMatch($options['from_date'], $options['to_date'], $matchExtra);
        $mongoProjectDate = $this->getMongoProjectDateArray($options['group_by']);
        $pipeline[] = ['$project' => [
            'date' => $mongoProjectDate,
            'session' => 1,
            'userAgent' => 1,
            'geolocation' => 1,
            'multimediaObject' => 1,
            'series' => 1,
        ]];
        $pipeline[] = ['$group' => [
            '_id' => '$multimediaObject',
            'session_list' => ['$addToSet' => [
                'session' => '$session',
                'multimediaObject' => '$multimediaObject',
                'date' => '$date',
                'city' => '$geolocation.city',
                'userAgent' => '$userAgent',
            ],
            ],
        ],
        ];
        $pipeline[] = ['$project' => ['_id' => 1, 'num_viewed' => ['$size' => '$session_list']]];
        $pipeline[] = ['$sort' => ['num_viewed' => $options['sort']]];

        $aggregation = $viewsCollection->aggregate($pipeline);
        $total = \count($mmobjIds);
        $aggregation = $this->getPagedAggregation($aggregation->toArray(), $options['page'], $options['limit']);

        $mostViewed = [];
        foreach ($aggregation as $element) {
            $ids[] = $element['_id'];
            $multimediaObject = $this->repoMultimedia->find($element['_id']);
            if ($multimediaObject) {
                $mostViewed[] = ['mmobj' => $multimediaObject,
                    'num_viewed' => $element['num_viewed'],
                ];
            }
        }

        //Add mmobj with zero views
        if (\count($aggregation) < $options['limit']) {
            if (0 === \count($aggregation)) {
                $max = min((1 + $options['page']) * $options['limit'], \count($mmobjIds));
                for ($i = ($options['page'] * $options['limit']); $i < $max; ++$i) {
                    $multimediaObject = $this->repoMultimedia->find($mmobjIds[$i]);
                    if ($multimediaObject) {
                        $mostViewed[] = ['mmobj' => $multimediaObject,
                            'num_viewed' => 0,
                        ];
                    }
                }
            } else {
                $max = min($options['limit'], $total / $options['limit'] - \count($mostViewed));

                foreach ($mmobjIds as $element) {
                    if (\count($mostViewed) >= $max) {
                        break;
                    }
                    if (!in_array($element, $ids)) {
                        $multimediaObject = $this->repoMultimedia->find($element);
                        if ($multimediaObject) {
                            $mostViewed[] = ['mmobj' => $multimediaObject,
                                'num_viewed' => 0,
                            ];
                        }
                    }
                }
            }
        }

        return [$mostViewed, $total];
    }

    /**
     * Returns an array of series viewed on the given range and its number of views on that range.
     */
    public function getSeriesMostViewed(array $criteria = [], array $options = []): array
    {
        $ids = [];
        $viewsCollection = $this->documentManager->getDocumentCollection(UserAction::class);

        $matchExtra = [];

        $seriesIds = $this->getSeriesIdsWithCriteria($criteria);
        $matchExtra['series'] = ['$in' => $seriesIds];

        $options = $this->parseOptions($options);

        $pipeline = $this->aggrPipeAddMatch($options['from_date'], $options['to_date'], $matchExtra);
        $mongoProjectDate = $this->getMongoProjectDateArray($options['group_by']);
        $pipeline[] = ['$project' => ['date' => $mongoProjectDate, 'session' => 1, 'userAgent' => 1, 'geolocation' => 1, 'multimediaObject' => 1, 'series' => 1]];
        $pipeline[] = ['$group' => [
            '_id' => '$series',
            'session_list' => ['$addToSet' => [
                'session' => '$session',
                'multimediaObject' => '$multimediaObject',
                'date' => '$date',
                'city' => '$geolocation.city',
                'userAgent' => '$userAgent',
            ],
            ],
        ],
        ];
        $pipeline[] = ['$project' => ['_id' => 1, 'num_viewed' => ['$size' => '$session_list']]];
        $pipeline[] = ['$sort' => ['num_viewed' => $options['sort']]];

        $aggregation = $viewsCollection->aggregate($pipeline);
        $total = \count($seriesIds);
        $aggregation = $this->getPagedAggregation($aggregation->toArray(), $options['page'], $options['limit']);

        $mostViewed = [];
        foreach ($aggregation as $element) {
            $ids[] = $element['_id'];
            $series = $this->repoSeries->find($element['_id']);
            if ($series) {
                $mostViewed[] = ['series' => $series,
                    'num_viewed' => $element['num_viewed'],
                ];
            }
        }

        //Add series with zero views
        if (\count($aggregation) < $options['limit']) {
            if (0 === \count($aggregation)) {
                $max = min((1 + $options['page']) * $options['limit'], \count($seriesIds));
                for ($i = ($options['page'] * $options['limit']); $i < $max; ++$i) {
                    $series = $this->repoSeries->find($seriesIds[$i]);
                    if ($series) {
                        $mostViewed[] = ['series' => $series,
                            'num_viewed' => 0,
                        ];
                    }
                }
            } else {
                $max = min($options['limit'], $total / $options['limit'] - \count($mostViewed));

                foreach ($seriesIds as $element) {
                    if (\count($mostViewed) >= $max) {
                        break;
                    }
                    if (!in_array($element, $ids)) {
                        $series = $this->repoSeries->find($element);
                        if ($series) {
                            $mostViewed[] = [
                                'series' => $series,
                                'num_viewed' => 0,
                            ];
                        }
                    }
                }
            }
        }

        return [$mostViewed, $total];
    }

    /**
     * Return an array of Most Used Agents as result from the criteria/options.
     */
    public function getMostUsedAgents(array $criteria = [], array $options = []): array
    {
        $viewsCollection = $this->documentManager->getDocumentCollection(UserAction::class);

        $options = $this->parseOptions($options);

        $matchExtra = [];

        if (isset($criteria['criteria_series'])) {
            if (isset($criteria['criteria_series']['id'])) {
                $matchExtra['series'] = ['$in' => [new ObjectId($criteria['criteria_series']['id'])]];
            } elseif (isset($criteria['criteria_series']['$text']['$search'])) {
                $seriesIds = $this->getSeriesIdsWithCriteria($criteria['criteria_series']);
                $matchExtra['series'] = ['$in' => $seriesIds];
            }
        } elseif (isset($criteria['criteria_mmobj'])) {
            if (isset($criteria['criteria_mmobj']['id'])) {
                $matchExtra['multimediaObject'] = ['$in' => [new ObjectId($criteria['criteria_mmobj']['id'])]];
            } elseif (isset($criteria['criteria_mmobj']['$text']['$search'])) {
                $objsIds = $this->getMmobjIdsWithCriteria($criteria['criteria_mmobj']);
                $matchExtra['multimediaObject'] = ['$in' => $objsIds];
            }
        }

        $pipeline = $this->aggrPipeAddMatch($options['from_date'], $options['to_date'], $matchExtra);
        $mongoProjectDate = $this->getMongoProjectDateArray($options['group_by']);

        $pipeline[] = ['$project' => ['date' => $mongoProjectDate, 'session' => 1, 'userAgent' => 1, 'geolocation' => 1, 'multimediaObject' => 1, 'series' => 1]];
        $pipeline[] = ['$group' => [
            '_id' => '$userAgent',
            'session_list' => ['$addToSet' => [
                'session' => '$session',
                'multimediaObject' => '$multimediaObject',
                'date' => '$date',
                'city' => '$geolocation.city',
                'userAgent' => '$userAgent',
            ],
            ],
        ],
        ];
        $pipeline[] = ['$project' => ['_id' => 1, 'num_viewed' => ['$size' => '$session_list']]];
        $pipeline[] = ['$sort' => ['num_viewed' => $options['sort']]];

        $aggregation = $viewsCollection->aggregate($pipeline);

        $total = \count($aggregation);
        $aggregation = $this->getPagedAggregation($aggregation->toArray(), $options['page'], $options['limit']);

        return [$aggregation, $total];
    }

    /**
     * Return an array of Cities from which object multimedia are most visited from the criteria/options.
     */
    public function getCityFromMostViewed(array $criteria = [], array $options = []): array
    {
        $viewsCollection = $this->documentManager->getDocumentCollection(UserAction::class);

        $options = $this->parseOptions($options);

        $matchExtra = [];

        if (isset($criteria['criteria_series'])) {
            if (isset($criteria['criteria_series']['id'])) {
                $matchExtra['series'] = ['$in' => [new ObjectId($criteria['criteria_series']['id'])]];
            } elseif (isset($criteria['criteria_series']['$text']['$search'])) {
                $seriesIds = $this->getSeriesIdsWithCriteria($criteria['criteria_series']);
                $matchExtra['series'] = ['$in' => $seriesIds];
            }
        } elseif (isset($criteria['criteria_mmobj'])) {
            if (isset($criteria['criteria_mmobj']['id'])) {
                $matchExtra['multimediaObject'] = ['$in' => [new ObjectId($criteria['criteria_mmobj']['id'])]];
            } elseif (isset($criteria['criteria_mmobj']['$text']['$search'])) {
                $objsIds = $this->getMmobjIdsWithCriteria($criteria['criteria_mmobj']);
                $matchExtra['multimediaObject'] = ['$in' => $objsIds];
            }
        }

        $pipeline = $this->aggrPipeAddMatch($options['from_date'], $options['to_date'], $matchExtra);
        $mongoProjectDate = $this->getMongoProjectDateArray($options['group_by']);
        $pipeline[] = ['$project' => ['date' => $mongoProjectDate, 'session' => 1, 'userAgent' => 1, 'geolocation' => 1, 'multimediaObject' => 1, 'series' => 1]];
        $pipeline[] = ['$group' => [
            '_id' => [
                'city' => '$geolocation.city',
            ],
            'session_list' => ['$addToSet' => [
                'session' => '$session',
                'multimediaObject' => '$multimediaObject',
                'date' => '$date',
                'city' => '$geolocation.city',
                'userAgent' => '$userAgent',
            ],
            ],
        ],
        ];
        $pipeline[] = ['$project' => ['_id' => 1, 'num_viewed' => ['$size' => '$session_list']]];
        $pipeline[] = ['$sort' => ['num_viewed' => $options['sort']]];

        $aggregation = $viewsCollection->aggregate($pipeline);

        $total = \count($aggregation);
        $aggregation = $this->getPagedAggregation($aggregation->toArray(), $options['page'], $options['limit']);

        return [$aggregation, $total];
    }

    /**
     * Process the data from the Mongo "UserAction" Document to generate the audience of the multimedia objects.
     */
    public function processAudienceUserAction(): array
    {
        $elemProcessed = [];

        $qb = $this->repo->createQueryBuilder();
        $mObjects = $qb->field('isProcessed')->equals(false)->getQuery()->execute();

        foreach ($mObjects as $mObject) {
            $id = $mObject->getId();
            $objectId = $mObject->getMultimediaObject();
            $inPoint = (int) ($mObject->getInPoint());
            $outPoint = (int) ($mObject->getOutPoint());

            $elemProcessed[] = $objectId;

            $this->documentManager->persist($mObject);

            for ($i = $inPoint; $i < $outPoint; ++$i) {
                $process_qb = $this->repoAudience->createQueryBuilder();
                $process_qb->findAndUpdate()
                    ->upsert(true)
                    ->field('multimediaObject')->equals($objectId)
                    ->field('audience.'.$i)->inc(1)
                    ->getQuery()->execute();
            }

            $update_qb = $this->repo->createQueryBuilder();
            $update_qb->findAndUpdate()
                ->field('_id')->equals($id)
                ->field('isProcessed')->set(true)
                ->getQuery()->execute();
        }

        $this->documentManager->flush();

        return [array_values(array_unique($elemProcessed)), \count($elemProcessed)];
    }

    /**
     * Return the series of a given $multimediaObjectId.
     *
     * @param mixed $multimediaObjectId
     */
    public function getSerieFromVideo($multimediaObjectId)
    {
        $qb = $this->repoMultimedia->createQueryBuilder();

        return $qb->distinct('series')->field('_id')->equals($multimediaObjectId)->getQuery()->getSingleResult();
    }

    /**
     * Returns an array with the total number of views (all mmobjs) on a certain date range, grouped by hour/day/month/year.
     *
     * If $options['criteria_mmobj'] exists, a query will be executed to filter using the resulting mmobj ids.
     * If $options['criteria_series'] exists, a query will be executed to filter using the resulting series ids.
     */
    public function getTotalViewedGrouped(array $options = []): array
    {
        return $this->getGroupedByAggrPipeline($options);
    }

    /**
     * Returns an aggregation pipeline array with all necessary data to form a num_views array grouped by hour/day/...
     *
     * @param mixed $matchExtra
     */
    public function getGroupedByAggrPipeline(array $options = [], $matchExtra = []): array
    {
        $viewsLogColl = $this->documentManager->getDocumentCollection(UserAction::class);
        $options = $this->parseOptions($options);

        if (!$matchExtra) {
            if ($options['criteria_series']) {
                $seriesIds = $this->getSeriesIdsWithCriteria($options['criteria_series']);
                $matchExtra['series'] = ['$in' => $seriesIds];
            }
            if ($options['criteria_mmobj']) {
                $mmobjIds = $this->getMmobjIdsWithCriteria($options['criteria_mmobj']);
                $matchExtra['multimediaObject'] = ['$in' => $mmobjIds];
            }
        }

        $pipeline = $this->aggrPipeAddMatch($options['from_date'], $options['to_date'], $matchExtra);

        $mongoProjectDate = $this->getMongoProjectDateArray($options['group_by']);
        $pipeline[] = ['$project' => ['date' => $mongoProjectDate, 'session' => 1, 'multimediaObject' => 1, 'series' => 1, 'userAgent' => 1, 'geolocation' => 1]];
        $pipeline[] = ['$group' => [
            '_id' => '$date',
            'session_list' => ['$addToSet' => [
                'session' => '$session',
                'multimediaObject' => '$multimediaObject',
                'date' => '$date',
                'city' => '$geolocation.city',
                'userAgent' => '$userAgent',
            ],
            ],
        ],
        ];
        $pipeline[] = ['$project' => ['_id' => 1, 'views' => ['$size' => '$session_list']]];
        $pipeline[] = ['$group' => ['_id' => '$_id', 'num_viewed' => ['$sum' => '$views']]];
        $pipeline[] = ['$sort' => ['_id' => $options['sort']]];

        $aggregation = $viewsLogColl->aggregate($pipeline);

        $total = \count($aggregation);
        $aggregation = $this->getPagedAggregation($aggregation->toArray(), $options['page'], $options['limit']);

        return [$aggregation, $total];
    }

    /**
     * Returns a 'paged' result of the aggregation array.
     */
    public function getPagedAggregation(array $aggregation, int $page = 0, int $limit = 10): array
    {
        $offset = $page * $limit;

        return array_splice($aggregation, $offset, $limit);
    }

    /**
     * Returns an array of MongoIds from MultimediaObject repository as results from the criteria.
     */
    private function getMmobjIdsWithCriteria(?array $criteria)
    {
        $qb = $this->repoMultimedia->createStandardQueryBuilder();
        if ($criteria) {
            $qb->addAnd($criteria);
        }

        return $qb->distinct('_id')->getQuery()->execute()->toArray();
    }

    /**
     * Returns an array of MongoIds from Series repository as results from the criteria.
     */
    private function getSeriesIdsWithCriteria(?array $criteria)
    {
        $qb = $this->repoSeries->createQueryBuilder();
        if ($criteria) {
            $qb->addAnd($criteria);
        }

        return $qb->distinct('_id')->getQuery()->execute()->toArray();
    }

    /**
     * Parses the options array to add all default options (if not added);.
     */
    private function parseOptions(array $options = []): array
    {
        $options['group_by'] = $options['group_by'] ?? 'month';
        $options['limit'] = $options['limit'] ?? 100;
        $options['sort'] = $options['sort'] ?? -1;
        $options['page'] = $options['page'] ?? 0;
        $options['from_date'] = $options['from_date'] ?? null;
        $options['to_date'] = $options['to_date'] ?? null;
        $options['criteria_series'] = $options['criteria_series'] ?? [];
        $options['criteria_mmobj'] = $options['criteria_mmobj'] ?? [];

        return $options;
    }

    /**
     * Returns the pipe with a match.
     *
     * @param mixed $matchExtra
     * @param mixed $pipeline
     */
    private function aggrPipeAddMatch(\DateTime $fromDate = null, \DateTime $toDate = null, $matchExtra = [], $pipeline = [])
    {
        $date = [];
        if ($fromDate) {
            $fromMongoDate = new \MongoDate((int) $fromDate->format('U'), (int) $fromDate->format('u'));
            $date['$gte'] = $fromMongoDate;
        }
        if ($toDate) {
            $toMongoDate = new \MongoDate((int) $toDate->format('U'), (int) $toDate->format('u'));
            $date['$lte'] = $toMongoDate;
        }
        if (\count($date) > 0) {
            $date = ['date' => $date];
        }
        if (\count($matchExtra) > 0 || \count($date) > 0) {
            $pipeline[] = ['$match' => array_merge($matchExtra, $date)];
        }

        return $pipeline;
    }

    /**
     * Returns an array for a mongo $project pipeline to create a date-formatted string with just the required fields.
     * It is used for grouping results in date ranges (hour/day/month/year).
     *
     * @param mixed $dateField
     */
    private function getMongoProjectDateArray(string $groupBy, $dateField = '$date'): array
    {
        $mongoProjectDate = [];

        switch ($groupBy) {
            case 'hour':
                $mongoProjectDate[] = 'H';
                $mongoProjectDate[] = ['$substr' => [$dateField, 0, 2]];
                $mongoProjectDate[] = 'T';
                // no break
            case 'day':
                $mongoProjectDate[] = ['$substr' => [$dateField, 8, 2]];
                $mongoProjectDate[] = '-';
                // no break
            default: //If it doesn't exists, it's 'month'
            case 'month':
                $mongoProjectDate[] = ['$substr' => [$dateField, 5, 2]];
                $mongoProjectDate[] = '-';
                // no break
            case 'year':
                $mongoProjectDate[] = ['$substr' => [$dateField, 0, 4]];

                break;
        }

        return ['$concat' => array_reverse($mongoProjectDate)];
    }
}
