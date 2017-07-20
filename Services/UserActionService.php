<?php

namespace Pumukit\PaellaStatsBundle\Services;

use Doctrine\ODM\MongoDB\DocumentManager;
use Pumukit\PaellaStatsBundle\Document\UserAction;

class UserActionService
{
    private $dm;
    private $repo;
    private $repoMultimedia;
    private $repoSeries;

    public function __construct(DocumentManager $documentManager)
    {
        $this->dm = $documentManager;
        $this->repo = $this->dm->getRepository('PumukitPaellaStatsBundle:UserAction');
        $this->repoMultimedia = $this->dm->getRepository('PumukitSchemaBundle:MultimediaObject');
        $this->repoSeries = $this->dm->getRepository('PumukitSchemaBundle:Series');
    }


    public function getMostViewed(array $criteria = array(), array $options = array())
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
        $pipeline[] = array('$project' => array("_id" => 1, 'numView' => array('$size' => '$session_list')));
        $pipeline[] = array('$sort' => array("numView" => -1));

        $aggregation = $viewsCollection->aggregate($pipeline);

        $totalInAggegation = count($aggregation);
        $total = 0;
        $aggregation = $this->getPagedAggregation($aggregation->toArray(), $options['page'], $options['limit']);

        $mostViewed = array();
        foreach ($aggregation as $element) {
            $ids[] = $element['_id'];
            $multimediaObject = $this->repoMultimedia->find($element['_id']);
            if ($multimediaObject) {
                $mostViewed[] = array('mmobj' => $multimediaObject,
                    'num_viewed' => $element['numView'],
                );
                $total += $element['numView'];
            }
        }

        //Add mmobj with zero views
        if (count($aggregation) < $options['limit']) {
            if (count($aggregation) == 0) {
                $max = min((1 + $options['page']) * $options['limit'], $total);
                for ($i = ($options['page'] * $options['limit']); $i < $max; ++$i) {
                    $multimediaObject = $this->repoMultimedia->find($mmobjIds[$i - $totalInAggegation]);
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

    public function getMostUsedBrowser(array $criteria = array(), array $options = array())
    {

        $ids = array();

        $viewsCollection = $this->dm->getDocumentCollection('PumukitPaellaStatsBundle:UserAction');

        $options = $this->parseOptions($options);

        $pipeline = array();
        $pipeline = $this->aggrPipeAddMatch($options['from_date'], $options['to_date']);

        $pipeline[] = array('$group' => array("_id" => '$userAgent', "session_list" => array('$addToSet' => '$session')));
        $pipeline[] = array('$project' => array("_id" => 1, 'numUses' => array('$size' => '$session_list')));
        $pipeline[] = array('$sort' => array("numUses" => -1));

        $aggregation = $viewsCollection->aggregate($pipeline);

        $total = count($aggregation);
        $aggregation = $this->getPagedAggregation($aggregation->toArray(), $options['page'], $options['limit']);

        return array($aggregation, $total);

    }

    /**
     * Returns an array of MongoIds as results from the criteria.
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


}
