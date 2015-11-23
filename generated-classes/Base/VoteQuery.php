<?php

namespace Base;

use \Vote as ChildVote;
use \VoteQuery as ChildVoteQuery;
use \Exception;
use \PDO;
use Map\VoteTableMap;
use Propel\Runtime\Propel;
use Propel\Runtime\ActiveQuery\Criteria;
use Propel\Runtime\ActiveQuery\ModelCriteria;
use Propel\Runtime\ActiveQuery\ModelJoin;
use Propel\Runtime\Collection\ObjectCollection;
use Propel\Runtime\Connection\ConnectionInterface;
use Propel\Runtime\Exception\PropelException;

/**
 * Base class that represents a query for the 'vote' table.
 *
 *
 *
 * @method     ChildVoteQuery orderByArticleid($order = Criteria::ASC) Order by the articleID column
 * @method     ChildVoteQuery orderByIp($order = Criteria::ASC) Order by the ip column
 * @method     ChildVoteQuery orderByVote($order = Criteria::ASC) Order by the vote column
 *
 * @method     ChildVoteQuery groupByArticleid() Group by the articleID column
 * @method     ChildVoteQuery groupByIp() Group by the ip column
 * @method     ChildVoteQuery groupByVote() Group by the vote column
 *
 * @method     ChildVoteQuery leftJoin($relation) Adds a LEFT JOIN clause to the query
 * @method     ChildVoteQuery rightJoin($relation) Adds a RIGHT JOIN clause to the query
 * @method     ChildVoteQuery innerJoin($relation) Adds a INNER JOIN clause to the query
 *
 * @method     ChildVoteQuery leftJoinWith($relation) Adds a LEFT JOIN clause and with to the query
 * @method     ChildVoteQuery rightJoinWith($relation) Adds a RIGHT JOIN clause and with to the query
 * @method     ChildVoteQuery innerJoinWith($relation) Adds a INNER JOIN clause and with to the query
 *
 * @method     ChildVoteQuery leftJoinVoteArticle($relationAlias = null) Adds a LEFT JOIN clause to the query using the VoteArticle relation
 * @method     ChildVoteQuery rightJoinVoteArticle($relationAlias = null) Adds a RIGHT JOIN clause to the query using the VoteArticle relation
 * @method     ChildVoteQuery innerJoinVoteArticle($relationAlias = null) Adds a INNER JOIN clause to the query using the VoteArticle relation
 *
 * @method     ChildVoteQuery joinWithVoteArticle($joinType = Criteria::INNER_JOIN) Adds a join clause and with to the query using the VoteArticle relation
 *
 * @method     ChildVoteQuery leftJoinWithVoteArticle() Adds a LEFT JOIN clause and with to the query using the VoteArticle relation
 * @method     ChildVoteQuery rightJoinWithVoteArticle() Adds a RIGHT JOIN clause and with to the query using the VoteArticle relation
 * @method     ChildVoteQuery innerJoinWithVoteArticle() Adds a INNER JOIN clause and with to the query using the VoteArticle relation
 *
 * @method     ChildVoteQuery leftJoinView($relationAlias = null) Adds a LEFT JOIN clause to the query using the View relation
 * @method     ChildVoteQuery rightJoinView($relationAlias = null) Adds a RIGHT JOIN clause to the query using the View relation
 * @method     ChildVoteQuery innerJoinView($relationAlias = null) Adds a INNER JOIN clause to the query using the View relation
 *
 * @method     ChildVoteQuery joinWithView($joinType = Criteria::INNER_JOIN) Adds a join clause and with to the query using the View relation
 *
 * @method     ChildVoteQuery leftJoinWithView() Adds a LEFT JOIN clause and with to the query using the View relation
 * @method     ChildVoteQuery rightJoinWithView() Adds a RIGHT JOIN clause and with to the query using the View relation
 * @method     ChildVoteQuery innerJoinWithView() Adds a INNER JOIN clause and with to the query using the View relation
 *
 * @method     \ArticleQuery|\ViewQuery endUse() Finalizes a secondary criteria and merges it with its primary Criteria
 *
 * @method     ChildVote findOne(ConnectionInterface $con = null) Return the first ChildVote matching the query
 * @method     ChildVote findOneOrCreate(ConnectionInterface $con = null) Return the first ChildVote matching the query, or a new ChildVote object populated from the query conditions when no match is found
 *
 * @method     ChildVote findOneByArticleid(int $articleID) Return the first ChildVote filtered by the articleID column
 * @method     ChildVote findOneByIp(string $ip) Return the first ChildVote filtered by the ip column
 * @method     ChildVote findOneByVote(int $vote) Return the first ChildVote filtered by the vote column *

 * @method     ChildVote requirePk($key, ConnectionInterface $con = null) Return the ChildVote by primary key and throws \Propel\Runtime\Exception\EntityNotFoundException when not found
 * @method     ChildVote requireOne(ConnectionInterface $con = null) Return the first ChildVote matching the query and throws \Propel\Runtime\Exception\EntityNotFoundException when not found
 *
 * @method     ChildVote requireOneByArticleid(int $articleID) Return the first ChildVote filtered by the articleID column and throws \Propel\Runtime\Exception\EntityNotFoundException when not found
 * @method     ChildVote requireOneByIp(string $ip) Return the first ChildVote filtered by the ip column and throws \Propel\Runtime\Exception\EntityNotFoundException when not found
 * @method     ChildVote requireOneByVote(int $vote) Return the first ChildVote filtered by the vote column and throws \Propel\Runtime\Exception\EntityNotFoundException when not found
 *
 * @method     ChildVote[]|ObjectCollection find(ConnectionInterface $con = null) Return ChildVote objects based on current ModelCriteria
 * @method     ChildVote[]|ObjectCollection findByArticleid(int $articleID) Return ChildVote objects filtered by the articleID column
 * @method     ChildVote[]|ObjectCollection findByIp(string $ip) Return ChildVote objects filtered by the ip column
 * @method     ChildVote[]|ObjectCollection findByVote(int $vote) Return ChildVote objects filtered by the vote column
 * @method     ChildVote[]|\Propel\Runtime\Util\PropelModelPager paginate($page = 1, $maxPerPage = 10, ConnectionInterface $con = null) Issue a SELECT query based on the current ModelCriteria and uses a page and a maximum number of results per page to compute an offset and a limit
 *
 */
abstract class VoteQuery extends ModelCriteria
{
    protected $entityNotFoundExceptionClass = '\\Propel\\Runtime\\Exception\\EntityNotFoundException';

    /**
     * Initializes internal state of \Base\VoteQuery object.
     *
     * @param     string $dbName The database name
     * @param     string $modelName The phpName of a model, e.g. 'Book'
     * @param     string $modelAlias The alias for the model in this query, e.g. 'b'
     */
    public function __construct($dbName = 'blog', $modelName = '\\Vote', $modelAlias = null)
    {
        parent::__construct($dbName, $modelName, $modelAlias);
    }

    /**
     * Returns a new ChildVoteQuery object.
     *
     * @param     string $modelAlias The alias of a model in the query
     * @param     Criteria $criteria Optional Criteria to build the query from
     *
     * @return ChildVoteQuery
     */
    public static function create($modelAlias = null, Criteria $criteria = null)
    {
        if ($criteria instanceof ChildVoteQuery) {
            return $criteria;
        }
        $query = new ChildVoteQuery();
        if (null !== $modelAlias) {
            $query->setModelAlias($modelAlias);
        }
        if ($criteria instanceof Criteria) {
            $query->mergeWith($criteria);
        }

        return $query;
    }

    /**
     * Find object by primary key.
     * Propel uses the instance pool to skip the database if the object exists.
     * Go fast if the query is untouched.
     *
     * <code>
     * $obj = $c->findPk(array(12, 34), $con);
     * </code>
     *
     * @param array[$articleID, $ip] $key Primary key to use for the query
     * @param ConnectionInterface $con an optional connection object
     *
     * @return ChildVote|array|mixed the result, formatted by the current formatter
     */
    public function findPk($key, ConnectionInterface $con = null)
    {
        if ($key === null) {
            return null;
        }
        if ((null !== ($obj = VoteTableMap::getInstanceFromPool(serialize(array((string) $key[0], (string) $key[1]))))) && !$this->formatter) {
            // the object is already in the instance pool
            return $obj;
        }
        if ($con === null) {
            $con = Propel::getServiceContainer()->getReadConnection(VoteTableMap::DATABASE_NAME);
        }
        $this->basePreSelect($con);
        if ($this->formatter || $this->modelAlias || $this->with || $this->select
         || $this->selectColumns || $this->asColumns || $this->selectModifiers
         || $this->map || $this->having || $this->joins) {
            return $this->findPkComplex($key, $con);
        } else {
            return $this->findPkSimple($key, $con);
        }
    }

    /**
     * Find object by primary key using raw SQL to go fast.
     * Bypass doSelect() and the object formatter by using generated code.
     *
     * @param     mixed $key Primary key to use for the query
     * @param     ConnectionInterface $con A connection object
     *
     * @throws \Propel\Runtime\Exception\PropelException
     *
     * @return ChildVote A model object, or null if the key is not found
     */
    protected function findPkSimple($key, ConnectionInterface $con)
    {
        $sql = 'SELECT articleID, ip, vote FROM vote WHERE articleID = :p0 AND ip = :p1';
        try {
            $stmt = $con->prepare($sql);
            $stmt->bindValue(':p0', $key[0], PDO::PARAM_INT);
            $stmt->bindValue(':p1', $key[1], PDO::PARAM_STR);
            $stmt->execute();
        } catch (Exception $e) {
            Propel::log($e->getMessage(), Propel::LOG_ERR);
            throw new PropelException(sprintf('Unable to execute SELECT statement [%s]', $sql), 0, $e);
        }
        $obj = null;
        if ($row = $stmt->fetch(\PDO::FETCH_NUM)) {
            /** @var ChildVote $obj */
            $obj = new ChildVote();
            $obj->hydrate($row);
            VoteTableMap::addInstanceToPool($obj, serialize(array((string) $key[0], (string) $key[1])));
        }
        $stmt->closeCursor();

        return $obj;
    }

    /**
     * Find object by primary key.
     *
     * @param     mixed $key Primary key to use for the query
     * @param     ConnectionInterface $con A connection object
     *
     * @return ChildVote|array|mixed the result, formatted by the current formatter
     */
    protected function findPkComplex($key, ConnectionInterface $con)
    {
        // As the query uses a PK condition, no limit(1) is necessary.
        $criteria = $this->isKeepQuery() ? clone $this : $this;
        $dataFetcher = $criteria
            ->filterByPrimaryKey($key)
            ->doSelect($con);

        return $criteria->getFormatter()->init($criteria)->formatOne($dataFetcher);
    }

    /**
     * Find objects by primary key
     * <code>
     * $objs = $c->findPks(array(array(12, 56), array(832, 123), array(123, 456)), $con);
     * </code>
     * @param     array $keys Primary keys to use for the query
     * @param     ConnectionInterface $con an optional connection object
     *
     * @return ObjectCollection|array|mixed the list of results, formatted by the current formatter
     */
    public function findPks($keys, ConnectionInterface $con = null)
    {
        if (null === $con) {
            $con = Propel::getServiceContainer()->getReadConnection($this->getDbName());
        }
        $this->basePreSelect($con);
        $criteria = $this->isKeepQuery() ? clone $this : $this;
        $dataFetcher = $criteria
            ->filterByPrimaryKeys($keys)
            ->doSelect($con);

        return $criteria->getFormatter()->init($criteria)->format($dataFetcher);
    }

    /**
     * Filter the query by primary key
     *
     * @param     mixed $key Primary key to use for the query
     *
     * @return $this|ChildVoteQuery The current query, for fluid interface
     */
    public function filterByPrimaryKey($key)
    {
        $this->addUsingAlias(VoteTableMap::COL_ARTICLEID, $key[0], Criteria::EQUAL);
        $this->addUsingAlias(VoteTableMap::COL_IP, $key[1], Criteria::EQUAL);

        return $this;
    }

    /**
     * Filter the query by a list of primary keys
     *
     * @param     array $keys The list of primary key to use for the query
     *
     * @return $this|ChildVoteQuery The current query, for fluid interface
     */
    public function filterByPrimaryKeys($keys)
    {
        if (empty($keys)) {
            return $this->add(null, '1<>1', Criteria::CUSTOM);
        }
        foreach ($keys as $key) {
            $cton0 = $this->getNewCriterion(VoteTableMap::COL_ARTICLEID, $key[0], Criteria::EQUAL);
            $cton1 = $this->getNewCriterion(VoteTableMap::COL_IP, $key[1], Criteria::EQUAL);
            $cton0->addAnd($cton1);
            $this->addOr($cton0);
        }

        return $this;
    }

    /**
     * Filter the query on the articleID column
     *
     * Example usage:
     * <code>
     * $query->filterByArticleid(1234); // WHERE articleID = 1234
     * $query->filterByArticleid(array(12, 34)); // WHERE articleID IN (12, 34)
     * $query->filterByArticleid(array('min' => 12)); // WHERE articleID > 12
     * </code>
     *
     * @see       filterByVoteArticle()
     *
     * @param     mixed $articleid The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return $this|ChildVoteQuery The current query, for fluid interface
     */
    public function filterByArticleid($articleid = null, $comparison = null)
    {
        if (is_array($articleid)) {
            $useMinMax = false;
            if (isset($articleid['min'])) {
                $this->addUsingAlias(VoteTableMap::COL_ARTICLEID, $articleid['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($articleid['max'])) {
                $this->addUsingAlias(VoteTableMap::COL_ARTICLEID, $articleid['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(VoteTableMap::COL_ARTICLEID, $articleid, $comparison);
    }

    /**
     * Filter the query on the ip column
     *
     * Example usage:
     * <code>
     * $query->filterByIp('fooValue');   // WHERE ip = 'fooValue'
     * $query->filterByIp('%fooValue%'); // WHERE ip LIKE '%fooValue%'
     * </code>
     *
     * @param     string $ip The value to use as filter.
     *              Accepts wildcards (* and % trigger a LIKE)
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return $this|ChildVoteQuery The current query, for fluid interface
     */
    public function filterByIp($ip = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($ip)) {
                $comparison = Criteria::IN;
            } elseif (preg_match('/[\%\*]/', $ip)) {
                $ip = str_replace('*', '%', $ip);
                $comparison = Criteria::LIKE;
            }
        }

        return $this->addUsingAlias(VoteTableMap::COL_IP, $ip, $comparison);
    }

    /**
     * Filter the query on the vote column
     *
     * Example usage:
     * <code>
     * $query->filterByVote(1234); // WHERE vote = 1234
     * $query->filterByVote(array(12, 34)); // WHERE vote IN (12, 34)
     * $query->filterByVote(array('min' => 12)); // WHERE vote > 12
     * </code>
     *
     * @param     mixed $vote The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return $this|ChildVoteQuery The current query, for fluid interface
     */
    public function filterByVote($vote = null, $comparison = null)
    {
        if (is_array($vote)) {
            $useMinMax = false;
            if (isset($vote['min'])) {
                $this->addUsingAlias(VoteTableMap::COL_VOTE, $vote['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($vote['max'])) {
                $this->addUsingAlias(VoteTableMap::COL_VOTE, $vote['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(VoteTableMap::COL_VOTE, $vote, $comparison);
    }

    /**
     * Filter the query by a related \Article object
     *
     * @param \Article|ObjectCollection $article The related object(s) to use as filter
     * @param string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @throws \Propel\Runtime\Exception\PropelException
     *
     * @return ChildVoteQuery The current query, for fluid interface
     */
    public function filterByVoteArticle($article, $comparison = null)
    {
        if ($article instanceof \Article) {
            return $this
                ->addUsingAlias(VoteTableMap::COL_ARTICLEID, $article->getId(), $comparison);
        } elseif ($article instanceof ObjectCollection) {
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }

            return $this
                ->addUsingAlias(VoteTableMap::COL_ARTICLEID, $article->toKeyValue('PrimaryKey', 'Id'), $comparison);
        } else {
            throw new PropelException('filterByVoteArticle() only accepts arguments of type \Article or Collection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the VoteArticle relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return $this|ChildVoteQuery The current query, for fluid interface
     */
    public function joinVoteArticle($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('VoteArticle');

        // create a ModelJoin object for this join
        $join = new ModelJoin();
        $join->setJoinType($joinType);
        $join->setRelationMap($relationMap, $this->useAliasInSQL ? $this->getModelAlias() : null, $relationAlias);
        if ($previousJoin = $this->getPreviousJoin()) {
            $join->setPreviousJoin($previousJoin);
        }

        // add the ModelJoin to the current object
        if ($relationAlias) {
            $this->addAlias($relationAlias, $relationMap->getRightTable()->getName());
            $this->addJoinObject($join, $relationAlias);
        } else {
            $this->addJoinObject($join, 'VoteArticle');
        }

        return $this;
    }

    /**
     * Use the VoteArticle relation Article object
     *
     * @see useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return \ArticleQuery A secondary query class using the current class as primary query
     */
    public function useVoteArticleQuery($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        return $this
            ->joinVoteArticle($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'VoteArticle', '\ArticleQuery');
    }

    /**
     * Filter the query by a related \View object
     *
     * @param \View|ObjectCollection $view The related object(s) to use as filter
     * @param string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @throws \Propel\Runtime\Exception\PropelException
     *
     * @return ChildVoteQuery The current query, for fluid interface
     */
    public function filterByView($view, $comparison = null)
    {
        if ($view instanceof \View) {
            return $this
                ->addUsingAlias(VoteTableMap::COL_IP, $view->getIpAddress(), $comparison);
        } elseif ($view instanceof ObjectCollection) {
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }

            return $this
                ->addUsingAlias(VoteTableMap::COL_IP, $view->toKeyValue('PrimaryKey', 'IpAddress'), $comparison);
        } else {
            throw new PropelException('filterByView() only accepts arguments of type \View or Collection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the View relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return $this|ChildVoteQuery The current query, for fluid interface
     */
    public function joinView($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('View');

        // create a ModelJoin object for this join
        $join = new ModelJoin();
        $join->setJoinType($joinType);
        $join->setRelationMap($relationMap, $this->useAliasInSQL ? $this->getModelAlias() : null, $relationAlias);
        if ($previousJoin = $this->getPreviousJoin()) {
            $join->setPreviousJoin($previousJoin);
        }

        // add the ModelJoin to the current object
        if ($relationAlias) {
            $this->addAlias($relationAlias, $relationMap->getRightTable()->getName());
            $this->addJoinObject($join, $relationAlias);
        } else {
            $this->addJoinObject($join, 'View');
        }

        return $this;
    }

    /**
     * Use the View relation View object
     *
     * @see useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return \ViewQuery A secondary query class using the current class as primary query
     */
    public function useViewQuery($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        return $this
            ->joinView($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'View', '\ViewQuery');
    }

    /**
     * Exclude object from result
     *
     * @param   ChildVote $vote Object to remove from the list of results
     *
     * @return $this|ChildVoteQuery The current query, for fluid interface
     */
    public function prune($vote = null)
    {
        if ($vote) {
            $this->addCond('pruneCond0', $this->getAliasedColName(VoteTableMap::COL_ARTICLEID), $vote->getArticleid(), Criteria::NOT_EQUAL);
            $this->addCond('pruneCond1', $this->getAliasedColName(VoteTableMap::COL_IP), $vote->getIp(), Criteria::NOT_EQUAL);
            $this->combine(array('pruneCond0', 'pruneCond1'), Criteria::LOGICAL_OR);
        }

        return $this;
    }

    /**
     * Deletes all rows from the vote table.
     *
     * @param ConnectionInterface $con the connection to use
     * @return int The number of affected rows (if supported by underlying database driver).
     */
    public function doDeleteAll(ConnectionInterface $con = null)
    {
        if (null === $con) {
            $con = Propel::getServiceContainer()->getWriteConnection(VoteTableMap::DATABASE_NAME);
        }

        // use transaction because $criteria could contain info
        // for more than one table or we could emulating ON DELETE CASCADE, etc.
        return $con->transaction(function () use ($con) {
            $affectedRows = 0; // initialize var to track total num of affected rows
            $affectedRows += parent::doDeleteAll($con);
            // Because this db requires some delete cascade/set null emulation, we have to
            // clear the cached instance *after* the emulation has happened (since
            // instances get re-added by the select statement contained therein).
            VoteTableMap::clearInstancePool();
            VoteTableMap::clearRelatedInstancePool();

            return $affectedRows;
        });
    }

    /**
     * Performs a DELETE on the database based on the current ModelCriteria
     *
     * @param ConnectionInterface $con the connection to use
     * @return int             The number of affected rows (if supported by underlying database driver).  This includes CASCADE-related rows
     *                         if supported by native driver or if emulated using Propel.
     * @throws PropelException Any exceptions caught during processing will be
     *                         rethrown wrapped into a PropelException.
     */
    public function delete(ConnectionInterface $con = null)
    {
        if (null === $con) {
            $con = Propel::getServiceContainer()->getWriteConnection(VoteTableMap::DATABASE_NAME);
        }

        $criteria = $this;

        // Set the correct dbName
        $criteria->setDbName(VoteTableMap::DATABASE_NAME);

        // use transaction because $criteria could contain info
        // for more than one table or we could emulating ON DELETE CASCADE, etc.
        return $con->transaction(function () use ($con, $criteria) {
            $affectedRows = 0; // initialize var to track total num of affected rows

            VoteTableMap::removeInstanceFromPool($criteria);

            $affectedRows += ModelCriteria::delete($con);
            VoteTableMap::clearRelatedInstancePool();

            return $affectedRows;
        });
    }

} // VoteQuery
