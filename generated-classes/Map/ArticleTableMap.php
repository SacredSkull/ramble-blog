<?php

namespace Map;

use \Article;
use \ArticleQuery;
use Propel\Runtime\Propel;
use Propel\Runtime\ActiveQuery\Criteria;
use Propel\Runtime\ActiveQuery\InstancePoolTrait;
use Propel\Runtime\Connection\ConnectionInterface;
use Propel\Runtime\DataFetcher\DataFetcherInterface;
use Propel\Runtime\Exception\PropelException;
use Propel\Runtime\Map\RelationMap;
use Propel\Runtime\Map\TableMap;
use Propel\Runtime\Map\TableMapTrait;


/**
 * This class defines the structure of the 'article' table.
 *
 *
 *
 * This map class is used by Propel to do runtime db structure discovery.
 * For example, the createSelectSql() method checks the type of a given column used in an
 * ORDER BY clause to know whether it needs to apply SQL to make the ORDER BY case-insensitive
 * (i.e. if it's a text column type).
 *
 */
class ArticleTableMap extends TableMap
{
    use InstancePoolTrait;
    use TableMapTrait;

    /**
     * The (dot-path) name of this class
     */
    const CLASS_NAME = '.Map.ArticleTableMap';

    /**
     * The default database name for this class
     */
    const DATABASE_NAME = 'blog';

    /**
     * The table name for this class
     */
    const TABLE_NAME = 'article';

    /**
     * The related Propel class for this table
     */
    const OM_CLASS = '\\Article';

    /**
     * A class that can be returned by this tableMap
     */
    const CLASS_DEFAULT = 'Article';

    /**
     * The total number of columns
     */
    const NUM_COLUMNS = 13;

    /**
     * The number of lazy-loaded columns
     */
    const NUM_LAZY_LOAD_COLUMNS = 0;

    /**
     * The number of columns to hydrate (NUM_COLUMNS - NUM_LAZY_LOAD_COLUMNS)
     */
    const NUM_HYDRATE_COLUMNS = 13;

    /**
     * the column name for the id field
     */
    const COL_ID = 'article.id';

    /**
     * the column name for the title field
     */
    const COL_TITLE = 'article.title';

    /**
     * the column name for the bodyHTML field
     */
    const COL_BODYHTML = 'article.bodyHTML';

    /**
     * the column name for the body field
     */
    const COL_BODY = 'article.body';

    /**
     * the column name for the tags field
     */
    const COL_TAGS = 'article.tags';

    /**
     * the column name for the positive_votes field
     */
    const COL_POSITIVE_VOTES = 'article.positive_votes';

    /**
     * the column name for the negative_votes field
     */
    const COL_NEGATIVE_VOTES = 'article.negative_votes';

    /**
     * the column name for the theme_id field
     */
    const COL_THEME_ID = 'article.theme_id';

    /**
     * the column name for the image field
     */
    const COL_IMAGE = 'article.image';

    /**
     * the column name for the draft field
     */
    const COL_DRAFT = 'article.draft';

    /**
     * the column name for the created_at field
     */
    const COL_CREATED_AT = 'article.created_at';

    /**
     * the column name for the updated_at field
     */
    const COL_UPDATED_AT = 'article.updated_at';

    /**
     * the column name for the slug field
     */
    const COL_SLUG = 'article.slug';

    /**
     * The default string format for model objects of the related table
     */
    const DEFAULT_STRING_FORMAT = 'YAML';

    /**
     * holds an array of fieldnames
     *
     * first dimension keys are the type constants
     * e.g. self::$fieldNames[self::TYPE_PHPNAME][0] = 'Id'
     */
    protected static $fieldNames = array (
        self::TYPE_PHPNAME       => array('Id', 'Title', 'Bodyhtml', 'Body', 'Tags', 'PositiveVotes', 'NegativeVotes', 'ThemeId', 'Image', 'Draft', 'CreatedAt', 'UpdatedAt', 'Slug', ),
        self::TYPE_CAMELNAME     => array('id', 'title', 'bodyhtml', 'body', 'tags', 'positiveVotes', 'negativeVotes', 'themeId', 'image', 'draft', 'createdAt', 'updatedAt', 'slug', ),
        self::TYPE_COLNAME       => array(ArticleTableMap::COL_ID, ArticleTableMap::COL_TITLE, ArticleTableMap::COL_BODYHTML, ArticleTableMap::COL_BODY, ArticleTableMap::COL_TAGS, ArticleTableMap::COL_POSITIVE_VOTES, ArticleTableMap::COL_NEGATIVE_VOTES, ArticleTableMap::COL_THEME_ID, ArticleTableMap::COL_IMAGE, ArticleTableMap::COL_DRAFT, ArticleTableMap::COL_CREATED_AT, ArticleTableMap::COL_UPDATED_AT, ArticleTableMap::COL_SLUG, ),
        self::TYPE_FIELDNAME     => array('id', 'title', 'bodyHTML', 'body', 'tags', 'positive_votes', 'negative_votes', 'theme_id', 'image', 'draft', 'created_at', 'updated_at', 'slug', ),
        self::TYPE_NUM           => array(0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, )
    );

    /**
     * holds an array of keys for quick access to the fieldnames array
     *
     * first dimension keys are the type constants
     * e.g. self::$fieldKeys[self::TYPE_PHPNAME]['Id'] = 0
     */
    protected static $fieldKeys = array (
        self::TYPE_PHPNAME       => array('Id' => 0, 'Title' => 1, 'Bodyhtml' => 2, 'Body' => 3, 'Tags' => 4, 'PositiveVotes' => 5, 'NegativeVotes' => 6, 'ThemeId' => 7, 'Image' => 8, 'Draft' => 9, 'CreatedAt' => 10, 'UpdatedAt' => 11, 'Slug' => 12, ),
        self::TYPE_CAMELNAME     => array('id' => 0, 'title' => 1, 'bodyhtml' => 2, 'body' => 3, 'tags' => 4, 'positiveVotes' => 5, 'negativeVotes' => 6, 'themeId' => 7, 'image' => 8, 'draft' => 9, 'createdAt' => 10, 'updatedAt' => 11, 'slug' => 12, ),
        self::TYPE_COLNAME       => array(ArticleTableMap::COL_ID => 0, ArticleTableMap::COL_TITLE => 1, ArticleTableMap::COL_BODYHTML => 2, ArticleTableMap::COL_BODY => 3, ArticleTableMap::COL_TAGS => 4, ArticleTableMap::COL_POSITIVE_VOTES => 5, ArticleTableMap::COL_NEGATIVE_VOTES => 6, ArticleTableMap::COL_THEME_ID => 7, ArticleTableMap::COL_IMAGE => 8, ArticleTableMap::COL_DRAFT => 9, ArticleTableMap::COL_CREATED_AT => 10, ArticleTableMap::COL_UPDATED_AT => 11, ArticleTableMap::COL_SLUG => 12, ),
        self::TYPE_FIELDNAME     => array('id' => 0, 'title' => 1, 'bodyHTML' => 2, 'body' => 3, 'tags' => 4, 'positive_votes' => 5, 'negative_votes' => 6, 'theme_id' => 7, 'image' => 8, 'draft' => 9, 'created_at' => 10, 'updated_at' => 11, 'slug' => 12, ),
        self::TYPE_NUM           => array(0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, )
    );

    /**
     * Initialize the table attributes and columns
     * Relations are not initialized by this method since they are lazy loaded
     *
     * @return void
     * @throws PropelException
     */
    public function initialize()
    {
        // attributes
        $this->setName('article');
        $this->setPhpName('Article');
        $this->setIdentifierQuoting(false);
        $this->setClassName('\\Article');
        $this->setPackage('');
        $this->setUseIdGenerator(true);
        // columns
        $this->addPrimaryKey('id', 'Id', 'INTEGER', true, null, null);
        $this->addColumn('title', 'Title', 'VARCHAR', true, 255, null);
        $this->getColumn('title')->setPrimaryString(true);
        $this->addColumn('bodyHTML', 'Bodyhtml', 'LONGVARCHAR', false, null, null);
        $this->addColumn('body', 'Body', 'LONGVARCHAR', true, null, null);
        $this->addColumn('tags', 'Tags', 'VARCHAR', true, 255, null);
        $this->addColumn('positive_votes', 'PositiveVotes', 'INTEGER', false, null, 0);
        $this->addColumn('negative_votes', 'NegativeVotes', 'INTEGER', false, null, 0);
        $this->addForeignKey('theme_id', 'ThemeId', 'INTEGER', 'theme', 'id', true, null, 0);
        $this->addColumn('image', 'Image', 'VARCHAR', false, 255, 'default/post_img.png');
        $this->addColumn('draft', 'Draft', 'BOOLEAN', false, 1, false);
        $this->addColumn('created_at', 'CreatedAt', 'TIMESTAMP', false, null, null);
        $this->addColumn('updated_at', 'UpdatedAt', 'TIMESTAMP', false, null, null);
        $this->addColumn('slug', 'Slug', 'VARCHAR', false, 255, null);
    } // initialize()

    /**
     * Build the RelationMap objects for this table relationships
     */
    public function buildRelations()
    {
        $this->addRelation('Theme', '\\Theme', RelationMap::MANY_TO_ONE, array (
  0 =>
  array (
    0 => ':theme_id',
    1 => ':id',
  ),
), 'CASCADE', null, null, false);
        $this->addRelation('View', '\\View', RelationMap::ONE_TO_MANY, array (
  0 =>
  array (
    0 => ':article_id',
    1 => ':id',
  ),
), 'CASCADE', null, 'Views', false);
    } // buildRelations()

    /**
     *
     * Gets the list of behaviors registered for this table
     *
     * @return array Associative array (name => parameters) of behaviors
     */
    public function getBehaviors()
    {
        return array(
            'timestampable' => array('create_column' => 'created_at', 'update_column' => 'updated_at', 'disable_created_at' => 'false', 'disable_updated_at' => 'false', ),
            'query_cache' => array('backend' => 'custom', 'lifetime' => '3600', ),
            'sluggable' => array('slug_column' => 'slug', 'slug_pattern' => '', 'replace_pattern' => '/\W+/', 'replacement' => '-', 'separator' => '-', 'permanent' => 'false', 'scope_column' => '', ),
        );
    } // getBehaviors()
    /**
     * Method to invalidate the instance pool of all tables related to article     * by a foreign key with ON DELETE CASCADE
     */
    public static function clearRelatedInstancePool()
    {
        // Invalidate objects in related instance pools,
        // since one or more of them may be deleted by ON DELETE CASCADE/SETNULL rule.
        ViewTableMap::clearInstancePool();
    }

    /**
     * Retrieves a string version of the primary key from the DB resultset row that can be used to uniquely identify a row in this table.
     *
     * For tables with a single-column primary key, that simple pkey value will be returned.  For tables with
     * a multi-column primary key, a serialize()d version of the primary key will be returned.
     *
     * @param array  $row       resultset row.
     * @param int    $offset    The 0-based offset for reading from the resultset row.
     * @param string $indexType One of the class type constants TableMap::TYPE_PHPNAME, TableMap::TYPE_CAMELNAME
     *                           TableMap::TYPE_COLNAME, TableMap::TYPE_FIELDNAME, TableMap::TYPE_NUM
     *
     * @return string The primary key hash of the row
     */
    public static function getPrimaryKeyHashFromRow($row, $offset = 0, $indexType = TableMap::TYPE_NUM)
    {
        // If the PK cannot be derived from the row, return NULL.
        if ($row[TableMap::TYPE_NUM == $indexType ? 0 + $offset : static::translateFieldName('Id', TableMap::TYPE_PHPNAME, $indexType)] === null) {
            return null;
        }

        return (string) $row[TableMap::TYPE_NUM == $indexType ? 0 + $offset : static::translateFieldName('Id', TableMap::TYPE_PHPNAME, $indexType)];
    }

    /**
     * Retrieves the primary key from the DB resultset row
     * For tables with a single-column primary key, that simple pkey value will be returned.  For tables with
     * a multi-column primary key, an array of the primary key columns will be returned.
     *
     * @param array  $row       resultset row.
     * @param int    $offset    The 0-based offset for reading from the resultset row.
     * @param string $indexType One of the class type constants TableMap::TYPE_PHPNAME, TableMap::TYPE_CAMELNAME
     *                           TableMap::TYPE_COLNAME, TableMap::TYPE_FIELDNAME, TableMap::TYPE_NUM
     *
     * @return mixed The primary key of the row
     */
    public static function getPrimaryKeyFromRow($row, $offset = 0, $indexType = TableMap::TYPE_NUM)
    {
        return (int) $row[
            $indexType == TableMap::TYPE_NUM
                ? 0 + $offset
                : self::translateFieldName('Id', TableMap::TYPE_PHPNAME, $indexType)
        ];
    }
    
    /**
     * The class that the tableMap will make instances of.
     *
     * If $withPrefix is true, the returned path
     * uses a dot-path notation which is translated into a path
     * relative to a location on the PHP include_path.
     * (e.g. path.to.MyClass -> 'path/to/MyClass.php')
     *
     * @param boolean $withPrefix Whether or not to return the path with the class name
     * @return string path.to.ClassName
     */
    public static function getOMClass($withPrefix = true)
    {
        return $withPrefix ? ArticleTableMap::CLASS_DEFAULT : ArticleTableMap::OM_CLASS;
    }

    /**
     * Populates an object of the default type or an object that inherit from the default.
     *
     * @param array  $row       row returned by DataFetcher->fetch().
     * @param int    $offset    The 0-based offset for reading from the resultset row.
     * @param string $indexType The index type of $row. Mostly DataFetcher->getIndexType().
                                 One of the class type constants TableMap::TYPE_PHPNAME, TableMap::TYPE_CAMELNAME
     *                           TableMap::TYPE_COLNAME, TableMap::TYPE_FIELDNAME, TableMap::TYPE_NUM.
     *
     * @throws PropelException Any exceptions caught during processing will be
     *                         rethrown wrapped into a PropelException.
     * @return array           (Article object, last column rank)
     */
    public static function populateObject($row, $offset = 0, $indexType = TableMap::TYPE_NUM)
    {
        $key = ArticleTableMap::getPrimaryKeyHashFromRow($row, $offset, $indexType);
        if (null !== ($obj = ArticleTableMap::getInstanceFromPool($key))) {
            // We no longer rehydrate the object, since this can cause data loss.
            // See http://www.propelorm.org/ticket/509
            // $obj->hydrate($row, $offset, true); // rehydrate
            $col = $offset + ArticleTableMap::NUM_HYDRATE_COLUMNS;
        } else {
            $cls = ArticleTableMap::OM_CLASS;
            /** @var Article $obj */
            $obj = new $cls();
            $col = $obj->hydrate($row, $offset, false, $indexType);
            ArticleTableMap::addInstanceToPool($obj, $key);
        }

        return array($obj, $col);
    }

    /**
     * The returned array will contain objects of the default type or
     * objects that inherit from the default.
     *
     * @param DataFetcherInterface $dataFetcher
     * @return array
     * @throws PropelException Any exceptions caught during processing will be
     *                         rethrown wrapped into a PropelException.
     */
    public static function populateObjects(DataFetcherInterface $dataFetcher)
    {
        $results = array();
    
        // set the class once to avoid overhead in the loop
        $cls = static::getOMClass(false);
        // populate the object(s)
        while ($row = $dataFetcher->fetch()) {
            $key = ArticleTableMap::getPrimaryKeyHashFromRow($row, 0, $dataFetcher->getIndexType());
            if (null !== ($obj = ArticleTableMap::getInstanceFromPool($key))) {
                // We no longer rehydrate the object, since this can cause data loss.
                // See http://www.propelorm.org/ticket/509
                // $obj->hydrate($row, 0, true); // rehydrate
                $results[] = $obj;
            } else {
                /** @var Article $obj */
                $obj = new $cls();
                $obj->hydrate($row);
                $results[] = $obj;
                ArticleTableMap::addInstanceToPool($obj, $key);
            } // if key exists
        }

        return $results;
    }
    /**
     * Add all the columns needed to create a new object.
     *
     * Note: any columns that were marked with lazyLoad="true" in the
     * XML schema will not be added to the select list and only loaded
     * on demand.
     *
     * @param Criteria $criteria object containing the columns to add.
     * @param string   $alias    optional table alias
     * @throws PropelException Any exceptions caught during processing will be
     *                         rethrown wrapped into a PropelException.
     */
    public static function addSelectColumns(Criteria $criteria, $alias = null)
    {
        if (null === $alias) {
            $criteria->addSelectColumn(ArticleTableMap::COL_ID);
            $criteria->addSelectColumn(ArticleTableMap::COL_TITLE);
            $criteria->addSelectColumn(ArticleTableMap::COL_BODYHTML);
            $criteria->addSelectColumn(ArticleTableMap::COL_BODY);
            $criteria->addSelectColumn(ArticleTableMap::COL_TAGS);
            $criteria->addSelectColumn(ArticleTableMap::COL_POSITIVE_VOTES);
            $criteria->addSelectColumn(ArticleTableMap::COL_NEGATIVE_VOTES);
            $criteria->addSelectColumn(ArticleTableMap::COL_THEME_ID);
            $criteria->addSelectColumn(ArticleTableMap::COL_IMAGE);
            $criteria->addSelectColumn(ArticleTableMap::COL_DRAFT);
            $criteria->addSelectColumn(ArticleTableMap::COL_CREATED_AT);
            $criteria->addSelectColumn(ArticleTableMap::COL_UPDATED_AT);
            $criteria->addSelectColumn(ArticleTableMap::COL_SLUG);
        } else {
            $criteria->addSelectColumn($alias . '.id');
            $criteria->addSelectColumn($alias . '.title');
            $criteria->addSelectColumn($alias . '.bodyHTML');
            $criteria->addSelectColumn($alias . '.body');
            $criteria->addSelectColumn($alias . '.tags');
            $criteria->addSelectColumn($alias . '.positive_votes');
            $criteria->addSelectColumn($alias . '.negative_votes');
            $criteria->addSelectColumn($alias . '.theme_id');
            $criteria->addSelectColumn($alias . '.image');
            $criteria->addSelectColumn($alias . '.draft');
            $criteria->addSelectColumn($alias . '.created_at');
            $criteria->addSelectColumn($alias . '.updated_at');
            $criteria->addSelectColumn($alias . '.slug');
        }
    }

    /**
     * Returns the TableMap related to this object.
     * This method is not needed for general use but a specific application could have a need.
     * @return TableMap
     * @throws PropelException Any exceptions caught during processing will be
     *                         rethrown wrapped into a PropelException.
     */
    public static function getTableMap()
    {
        return Propel::getServiceContainer()->getDatabaseMap(ArticleTableMap::DATABASE_NAME)->getTable(ArticleTableMap::TABLE_NAME);
    }

    /**
     * Add a TableMap instance to the database for this tableMap class.
     */
    public static function buildTableMap()
    {
        $dbMap = Propel::getServiceContainer()->getDatabaseMap(ArticleTableMap::DATABASE_NAME);
        if (!$dbMap->hasTable(ArticleTableMap::TABLE_NAME)) {
            $dbMap->addTableObject(new ArticleTableMap());
        }
    }

    /**
     * Performs a DELETE on the database, given a Article or Criteria object OR a primary key value.
     *
     * @param mixed               $values Criteria or Article object or primary key or array of primary keys
     *              which is used to create the DELETE statement
     * @param  ConnectionInterface $con the connection to use
     * @return int             The number of affected rows (if supported by underlying database driver).  This includes CASCADE-related rows
     *                         if supported by native driver or if emulated using Propel.
     * @throws PropelException Any exceptions caught during processing will be
     *                         rethrown wrapped into a PropelException.
     */
     public static function doDelete($values, ConnectionInterface $con = null)
     {
        if (null === $con) {
            $con = Propel::getServiceContainer()->getWriteConnection(ArticleTableMap::DATABASE_NAME);
        }

        if ($values instanceof Criteria) {
            // rename for clarity
            $criteria = $values;
        } elseif ($values instanceof \Article) { // it's a model object
            // create criteria based on pk values
            $criteria = $values->buildPkeyCriteria();
        } else { // it's a primary key, or an array of pks
            $criteria = new Criteria(ArticleTableMap::DATABASE_NAME);
            $criteria->add(ArticleTableMap::COL_ID, (array) $values, Criteria::IN);
        }

        $query = ArticleQuery::create()->mergeWith($criteria);

        if ($values instanceof Criteria) {
            ArticleTableMap::clearInstancePool();
        } elseif (!is_object($values)) { // it's a primary key, or an array of pks
            foreach ((array) $values as $singleval) {
                ArticleTableMap::removeInstanceFromPool($singleval);
            }
        }

        return $query->delete($con);
    }

    /**
     * Deletes all rows from the article table.
     *
     * @param ConnectionInterface $con the connection to use
     * @return int The number of affected rows (if supported by underlying database driver).
     */
    public static function doDeleteAll(ConnectionInterface $con = null)
    {
        return ArticleQuery::create()->doDeleteAll($con);
    }

    /**
     * Performs an INSERT on the database, given a Article or Criteria object.
     *
     * @param mixed               $criteria Criteria or Article object containing data that is used to create the INSERT statement.
     * @param ConnectionInterface $con the ConnectionInterface connection to use
     * @return mixed           The new primary key.
     * @throws PropelException Any exceptions caught during processing will be
     *                         rethrown wrapped into a PropelException.
     */
    public static function doInsert($criteria, ConnectionInterface $con = null)
    {
        if (null === $con) {
            $con = Propel::getServiceContainer()->getWriteConnection(ArticleTableMap::DATABASE_NAME);
        }

        if ($criteria instanceof Criteria) {
            $criteria = clone $criteria; // rename for clarity
        } else {
            $criteria = $criteria->buildCriteria(); // build Criteria from Article object
        }

        if ($criteria->containsKey(ArticleTableMap::COL_ID) && $criteria->keyContainsValue(ArticleTableMap::COL_ID) ) {
            throw new PropelException('Cannot insert a value for auto-increment primary key ('.ArticleTableMap::COL_ID.')');
        }


        // Set the correct dbName
        $query = ArticleQuery::create()->mergeWith($criteria);

        // use transaction because $criteria could contain info
        // for more than one table (I guess, conceivably)
        return $con->transaction(function () use ($con, $query) {
            return $query->doInsert($con);
        });
    }

} // ArticleTableMap
// This is the static code needed to register the TableMap for this table with the main Propel class.
//
ArticleTableMap::buildTableMap();
