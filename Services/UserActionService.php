<?php

namespace Pumukit\PaellaStatsBundle\Services;

use Doctrine\ODM\MongoDB\DocumentManager;
use Pumukit\PaellaStatsBundle\Document\UserAction;
use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;

class UserActionService
{
    private $dm;
    private $repo;
    private $repoMultimedia;
    private $repoSeries;
    private $repoAnalytics;

    public function __construct(DocumentManager $documentManager)
    {
        $this->dm = $documentManager;
        $this->repo = $this->dm->getRepository('PumukitPaellaStatsBundle:UserAction');
        $this->repoMultimedia = $this->dm->getRepository('PumukitSchemaBundle:MultimediaObject');
        $this->repoSeries = $this->dm->getRepository('PumukitSchemaBundle:Series');
        $this->repoAnalytics = $this->dm->getRepository('PumukitPaellaStatsBundle:MultimediaObjectAnalytics');
    }


    /**
     * Return an array of most viewed Multimedia Objects as results from the criteria/options.
     * @param array $criteria
     * @param array $options
     * @return array
     */
    public function getMmobjsMostViewed(array $criteria = array(), array $options = array())
    {

        $ids = array();

        $viewsCollection = $this->dm->getDocumentCollection('PumukitPaellaStatsBundle:UserAction');

        $matchExtra = array();
        $mmobjIds = $this->getMmobjIdsWithCriteria($criteria);
        $matchExtra['multimediaObject'] = array('$in' => $mmobjIds);

        $options = $this->parseOptions($options);

        $pipeline = array();
        $pipeline = $this->aggrPipeAddMatch($options['from_date'], $options['to_date'], $matchExtra);

        $pipeline[] = array('$group' => array("_id" => '$multimediaObject', "session_list" => array('$addToSet' => '$session')));
        $pipeline[] = array('$project' => array("_id" => 1, 'num_viewed' => array('$size' => '$session_list')));
        $pipeline[] = array('$sort' => array("num_viewed" => -1));

        $aggregation = $viewsCollection->aggregate($pipeline);

        $totalInAggegation = count($aggregation);
        $total = count($mmobjIds);
        $aggregation = $this->getPagedAggregation($aggregation->toArray(), $options['page'], $options['limit']);

        $mostViewed = array();
        foreach ($aggregation as $element) {
            $ids[] = $element['_id'];
            $multimediaObject = $this->repoMultimedia->find($element['_id']);
            if ($multimediaObject) {
                $mostViewed[] = array('mmobj' => $multimediaObject,
                    'num_viewed' => $element['num_viewed'],
                );
            }
        }

        //Add mmobj with zero views
        if (count($aggregation) < $options['limit']) {
            if (count($aggregation) == 0) {
                $max = min((1 + $options['page']) * $options['limit'], sizeof($mmobjIds));
                for ($i = ($options['page'] * $options['limit']); $i < $max; ++$i) {
                    $multimediaObject = $this->repoMultimedia->find($mmobjIds[$i]);                   
                    if ($multimediaObject) {
                        $mostViewed[] = array('mmobj' => $multimediaObject,
                            'num_viewed' => 0,
                        );
                    }
                }
            } else {
                foreach ($mmobjIds as $element) {
                    if (!in_array($element, $ids)) {
                        $multimediaObject = $this->repoMultimedia->find($element);
                        if ($multimediaObject) {
                            $mostViewed[] = array('mmobj' => $multimediaObject,
                                'num_viewed' => 0,
                            );
                            if (count($mostViewed) == $options['limit']) {
                                break;
                            }
                        }
                    }
                }
            }
        }

        return array($mostViewed, $total);

    }


    /**
     * Returns an array of series viewed on the given range and its number of views on that range.
     */
    public function getSeriesMostViewed(array $criteria = array(), array $options = array())
    {

        $ids = array();
        $viewsCollection = $this->dm->getDocumentCollection('PumukitPaellaStatsBundle:UserAction');

        $matchExtra = array();

        $seriesIds = $this->getSeriesIdsWithCriteria($criteria);
        $matchExtra['series'] = array('$in' => $seriesIds);

        $options = $this->parseOptions($options);

        $pipeline = array();
        $pipeline = $this->aggrPipeAddMatch($options['from_date'], $options['to_date'], $matchExtra);
        
        $pipeline[] = array ('$group' => array(     '_id' => '$series', 
                                                    'session_list' =>  array('$addToSet' => array(
                                                                                    'session' => '$session', 
                                                                                    'multimediaObject' =>  '$multimediaObject'
                                                                            ))
                                        )
                            );
        $pipeline[] = array('$project' => array("_id" => 1, 'num_viewed' => array('$size' => '$session_list')));
        $pipeline[] = array('$sort' => array("num_viewed" => -1));
        
        $aggregation = $viewsCollection->aggregate($pipeline);
        $totalInAggegation = count($aggregation);
        $total = count($seriesIds);
        $aggregation = $this->getPagedAggregation($aggregation->toArray(), $options['page'], $options['limit']);

        $mostViewed = array();
        foreach ($aggregation as $element) {
            $ids[] = $element['_id'];
            $series = $this->repoSeries->find($element['_id']);
            if ($series) {
                $mostViewed[] = array('series' => $series,
                                      'num_viewed' => $element['num_viewed'],
                );
            }
        }

        //Add series with zero views
        if (count($aggregation) < $options['limit']) {
            if (count($aggregation) == 0) {
                $max = min((1 + $options['page']) * $options['limit'], sizeof($seriesIds));
                for ($i = ($options['page'] * $options['limit']); $i < $max; ++$i) {
                    $series = $this->repoSeries->find($seriesIds[$i]);
                    if ($series) {
                        $mostViewed[] = array('series' => $series,
                                              'num_viewed' => 0,
                        );
                    }
                }
            } else {
                foreach ($seriesIds as $element) {
                    if (!in_array($element, $ids)) {
                        $series = $this->repoSeries->find($element);
                        if ($series) {
                            $mostViewed[] = array('series' => $series,
                                                  'num_viewed' => 0,
                            );
                            if (count($mostViewed) == $options['limit']) {
                                break;
                            }
                        }
                    }
                }
            }
        }

        return array($mostViewed, $total);
    }


    /**
     * Return an array of Most Used Browser as result from the criteria/options.
     * @param array $criteria
     * @param array $options
     * @return array
     */
    public function getMostUsedBrowser(array $criteria = array(), array $options = array())
    {
        $viewsCollection = $this->dm->getDocumentCollection('PumukitPaellaStatsBundle:UserAction');

        $options = $this->parseOptions($options);

        $pipeline = array();
        $pipeline = $this->aggrPipeAddMatch($options['from_date'], $options['to_date']);

        if(isset($criteria) and isset($criteria['id']) and 0 != count($criteria)) {
            $pipeline[] = array('$match' => array('multimediaObject' => new \MongoId($criteria['id'])));
        }

        $pipeline[] = array('$group' => array("_id" => '$userAgent', "session_list" => array('$addToSet' => '$session')));
        $pipeline[] = array('$project' => array("_id" => 1, 'num_viewed' => array('$size' => '$session_list')));
        $pipeline[] = array('$sort' => array("num_viewed" => -1));

        $aggregation = $viewsCollection->aggregate($pipeline);

        $total = count($aggregation);
        $aggregation = $this->getPagedAggregation($aggregation->toArray(), $options['page'], $options['limit']);

        return array($aggregation, $total);

    }


    /**
     * Return an array of Cities from which object multimedia are most visited from the criteria/options.
     * @param array $criteria
     * @param array $options
     * @return array
     */
    public function getCityFromMostViewed (array $criteria = array(), array $options = array())
    {
        $viewsCollection = $this->dm->getDocumentCollection('PumukitPaellaStatsBundle:UserAction');

        $options = $this->parseOptions($options);

        $pipeline = array();
        $pipeline = $this->aggrPipeAddMatch($options['from_date'], $options['to_date']);

        if(isset($criteria) and isset($criteria['id']) and 0 != count($criteria)) {
            $pipeline[] = array('$match' => array('multimediaObject' => new \MongoId($criteria['id'])));
        }

        $pipeline[] = array('$group' => array(
            "_id" => array(
                'continent' => '$geolocation.continent',
                'continent_code' => '$geolocation.continentCode',
                'country' => '$geolocation.country',
                'country_code' => '$geolocation.countryCode',
                'sub_country' => '$geolocation.subCountry',
                'sub_country_code' => '$geolocation.subCountryCode',
                'city' => '$geolocation.city',
                //'latitude' => '$geolocation.location.latitude',
                //'longitude' => '$geolocation.location.longitude',
                //'time_zone' => '$geolocation.location.timeZone',
                //'postal' => '$geolocation.postal'
            ),
            "session_list" => array('$addToSet' => '$session'))
        );

        $pipeline[] = array('$project' => array("_id" => 1, 'num_viewed' => array('$size' => '$session_list')));
        $pipeline[] = array('$sort' => array("num_viewed" => -1));

        $aggregation = $viewsCollection->aggregate($pipeline);

        $total = count($aggregation);
        $aggregation = $this->getPagedAggregation($aggregation->toArray(), $options['page'], $options['limit']);

        return array($aggregation, $total);
    }


    /*
     * Process the data from the Mongo "UserAction" Document to generate the statistics of the multimedia objects.
     * @return array
     */
    public function getUnprocessedUserAction(){

        $elemProcessed = array();

        $qb = $this->repo->createQueryBuilder();
        $mObjects = $qb->field('isProcessed')->equals(false)->getQuery()->execute();

        foreach ($mObjects as $mObject) {

            $id = $mObject->getId();
            $objectId = $mObject->getMultimediaObject();
            $inPoint = $mObject->getInPoint();
            $outPoint = $mObject->getOutPoint();

            $elemProcessed[] = $objectId;

            $this->dm->persist($mObject);

            for($i = $inPoint; $i < $outPoint; $i++){

                $process_qb = $this->repoAnalytics->createQueryBuilder();
                $process_qb ->findAndUpdate()
                            ->upsert(true)
                            ->field('multimediaObject')->equals($objectId)
                            ->field('analytics.'.$i)->inc(1)
                            ->getQuery()->execute();
            }

            $update_qb = $this->repo->createQueryBuilder();
            $update_qb  ->findAndUpdate()
                ->field('_id')->equals($id)
                ->field('isProcessed')->set(true)
                ->getQuery()->execute();
        }

        $this->dm->flush();

        return array(array_values(array_unique($elemProcessed)), sizeof($elemProcessed));
    }


    /*
     * Return the series of a given $multimediaObjectId
     * @param string $multimediaObjectId
     * @return string
    */
    public function getSerieFromVideo($multimediaObjectId)
    {
        $qb = $this->repoMultimedia->createQueryBuilder();

        return $qb->distinct('series')->field('_id')->equals($multimediaObjectId)->getQuery()->getSingleResult();
    }


    /**
     * Returns an array of MongoIds from MultimediaObject repository as results from the criteria.
     */
    private function getMmobjIdsWithCriteria($criteria)
    {
        $qb = $this->repoMultimedia->createStandardQueryBuilder();
        if ($criteria) {
            $mmobjIds = $qb->addAnd($criteria);
        }

        return $qb->distinct('_id')->getQuery()->execute()->toArray();
    }

    /**
     * Returns an array of MongoIds from Series repository as results from the criteria.
     */
    private function getSeriesIdsWithCriteria($criteria)
    {
        $qb = $this->repoSeries->createQueryBuilder();
        if ($criteria) {
            $mmobjIds = $qb->addAnd($criteria);
        }

        return $qb->distinct('_id')->getQuery()->execute()->toArray();
    }


    /**
     * Returns an array with the total number of views (all mmobjs) on a certain date range, grouped by hour/day/month/year.
     *
     * If $options['criteria_mmobj'] exists, a query will be executed to filter using the resulting mmobj ids.
     * If $options['criteria_series'] exists, a query will be executed to filter using the resulting series ids.
     */
    public function getTotalViewedGrouped(array $options = array())
    {
        return $this->getGroupedByAggrPipeline($options);
    }


    /**
     * Returns an aggregation pipeline array with all necessary data to form a num_views array grouped by hour/day/...
     */
    public function getGroupedByAggrPipeline($options = array(), $matchExtra = array())
    {
        $viewsLogColl = $this->dm->getDocumentCollection('PumukitPaellaStatsBundle:UserAction');
        $options = $this->parseOptions($options);

        if (!$matchExtra) {
            if ($options['criteria_series']) {
                $seriesIds = $this->getSeriesIdsWithCriteria($options['criteria_series']);
                $matchExtra['series'] = array('$in' => $seriesIds);
            }
            if ($options['criteria_mmobj']) {
                $mmobjIds = $this->getMmobjIdsWithCriteria($options['criteria_mmobj']);
                $matchExtra['multimediaObject'] = array('$in' => $mmobjIds);
            }
        }

        $pipeline = $this->aggrPipeAddMatch($options['from_date'], $options['to_date'], $matchExtra);

        $mongoProjectDate = $this->getMongoProjectDateArray($options['group_by']);
        $pipeline[] = array('$project' => array('date' => $mongoProjectDate, 'session' => 1, 'multimediaObject' => 1));
        $pipeline[] = array('$group' => array(
                                            "_id" => array( 
                                                'multimediaObject' => '$multimediaObject',
                                                'session' => '$session',
                                                'date' => '$date'
                                            ),
                                            'session_list' => array('$addToSet' => '$session')
                                        )
                            );
        $pipeline[] = array('$project' => array('_id' => '$_id.date', 'views' => array('$size' => '$session_list')));
        $pipeline[] = array('$group' => array('_id' => '$_id', 'numView' => array('$sum' => '$views')));
        $pipeline[] = array('$sort' => array('_id' => $options['sort']));

        $aggregation = $viewsLogColl->aggregate($pipeline);

        $total = count($aggregation);
        $aggregation = $this->getPagedAggregation($aggregation->toArray(), $options['page'], $options['limit']);

        return array($aggregation, $total);
    }


    /**
     * Returns a 'paged' result of the aggregation array.
     *
     * @param aggregation The aggregation array to be paged
     * @param page The page to be returned
     * @param limit The number of elements to be returned
     *
     * @return array aggregation
     */
    public function getPagedAggregation(array $aggregation, $page = 0, $limit = 10)
    {
        $offset = $page * $limit;

        return array_splice($aggregation, $offset, $limit);
    }

    /**
     * Parses the options array to add all default options (if not added);.
     */
    private function parseOptions(array $options = array())
    {
        $options['group_by'] = isset($options['group_by']) ? $options['group_by'] : 'month';
        $options['limit'] = isset($options['limit']) ? $options['limit'] : 100;
        $options['sort'] = isset($options['sort']) ? $options['sort'] : -1;
        $options['page'] = isset($options['page']) ? $options['page'] : 0;
        $options['from_date'] = isset($options['from_date']) ? $options['from_date'] : null;
        $options['to_date'] = isset($options['to_date']) ? $options['to_date'] : null;
        $options['criteria_series'] = isset($options['criteria_series']) ? $options['criteria_series'] : array();
        $options['criteria_mmobj'] = isset($options['criteria_mmobj']) ? $options['criteria_mmobj'] : array();

        return $options;
    }

    /**
     * Returns the pipe with a match.
     */
    private function aggrPipeAddMatch(\DateTime $fromDate = null, \DateTime $toDate = null, $matchExtra = array(), $pipeline = array())
    {
        $date = array();
        if ($fromDate) {
            $fromMongoDate = new \MongoDate($fromDate->format('U'), $fromDate->format('u'));
            $date['$gte'] = $fromMongoDate;
        }
        if ($toDate) {
            $toMongoDate = new \MongoDate($toDate->format('U'), $toDate->format('u'));
            $date['$lte'] = $toMongoDate;
        }
        if (count($date) > 0) {
            $date = array('date' => $date);
        }
        if (count($matchExtra) > 0 || count($date) > 0) {
            $pipeline[] = array('$match' => array_merge($matchExtra, $date));
        }

        return $pipeline;
    }


    /**
     * Returns an array for a mongo $project pipeline to create a date-formatted string with just the required fields.
     * It is used for grouping results in date ranges (hour/day/month/year).
     */
    private function getMongoProjectDateArray($groupBy, $dateField = '$date')
    {
        $mongoProjectDate = array();
        switch ($groupBy) {
            case 'hour':
                $mongoProjectDate[] = 'H';
                $mongoProjectDate[] = array('$substr' => array($dateField, 0, 2));
                $mongoProjectDate[] = 'T';
            case 'day':
                $mongoProjectDate[] = array('$substr' => array($dateField, 8, 2));
                $mongoProjectDate[] = '-';
            default: //If it doesn't exists, it's 'month'
            case 'month':
                $mongoProjectDate[] = array('$substr' => array($dateField, 5, 2));
                $mongoProjectDate[] = '-';
            case 'year':
                $mongoProjectDate[] = array('$substr' => array($dateField, 0, 4));
                break;
        }

        return array('$concat' => array_reverse($mongoProjectDate));
    }


}
