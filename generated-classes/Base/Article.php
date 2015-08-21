<?php

namespace Base;

use \Article as ChildArticle;
use \ArticleQuery as ChildArticleQuery;
use \ArticleTag as ChildArticleTag;
use \ArticleTagQuery as ChildArticleTagQuery;
use \Category as ChildCategory;
use \CategoryQuery as ChildCategoryQuery;
use \Tag as ChildTag;
use \TagQuery as ChildTagQuery;
use \View as ChildView;
use \ViewQuery as ChildViewQuery;
use \Vote as ChildVote;
use \VoteQuery as ChildVoteQuery;
use \DateTime;
use \Exception;
use \PDO;
use Map\ArticleTableMap;
use Propel\Runtime\Propel;
use Propel\Runtime\ActiveQuery\Criteria;
use Propel\Runtime\ActiveQuery\ModelCriteria;
use Propel\Runtime\ActiveRecord\ActiveRecordInterface;
use Propel\Runtime\Collection\Collection;
use Propel\Runtime\Collection\ObjectCollection;
use Propel\Runtime\Connection\ConnectionInterface;
use Propel\Runtime\Exception\BadMethodCallException;
use Propel\Runtime\Exception\LogicException;
use Propel\Runtime\Exception\PropelException;
use Propel\Runtime\Map\TableMap;
use Propel\Runtime\Parser\AbstractParser;
use Propel\Runtime\Util\PropelDateTime;

/**
 * Base class that represents a row from the 'article' table.
 *
 *
 *
* @package    propel.generator..Base
*/
abstract class Article implements ActiveRecordInterface
{
    /**
     * TableMap class name
     */
    const TABLE_MAP = '\\Map\\ArticleTableMap';


    /**
     * attribute to determine if this object has previously been saved.
     * @var boolean
     */
    protected $new = true;

    /**
     * attribute to determine whether this object has been deleted.
     * @var boolean
     */
    protected $deleted = false;

    /**
     * The columns that have been modified in current object.
     * Tracking modified columns allows us to only update modified columns.
     * @var array
     */
    protected $modifiedColumns = array();

    /**
     * The (virtual) columns that are added at runtime
     * The formatters can add supplementary columns based on a resultset
     * @var array
     */
    protected $virtualColumns = array();

    /**
     * The value for the id field.
     * @var        int
     */
    protected $id;

    /**
     * The value for the title field.
     * Note: this column has a database default value of: 'Untitled'
     * @var        string
     */
    protected $title;

    /**
     * The value for the bodyhtml field.
     * @var        string
     */
    protected $bodyhtml;

    /**
     * The value for the body field.
     * @var        string
     */
    protected $body;

    /**
     * The value for the category_id field.
     * Note: this column has a database default value of: 0
     * @var        int
     */
    protected $category_id;

    /**
     * The value for the image field.
     * Note: this column has a database default value of: 'default/post_img.png'
     * @var        string
     */
    protected $image;

    /**
     * The value for the draft field.
     * Note: this column has a database default value of: false
     * @var        boolean
     */
    protected $draft;

    /**
     * The value for the poll_question field.
     * Note: this column has a database default value of: 'false'
     * @var        string
     */
    protected $poll_question;

    /**
     * The value for the created_at field.
     * @var        \DateTime
     */
    protected $created_at;

    /**
     * The value for the updated_at field.
     * @var        \DateTime
     */
    protected $updated_at;

    /**
     * The value for the slug field.
     * @var        string
     */
    protected $slug;

    /**
     * @var        ChildCategory
     */
    protected $aCategory;

    /**
     * @var        ObjectCollection|ChildArticleTag[] Collection to store aggregation of ChildArticleTag objects.
     */
    protected $collArticleTags;
    protected $collArticleTagsPartial;

    /**
     * @var        ChildView one-to-one related ChildView object
     */
    protected $singleviewArticleForeign;

    /**
     * @var        ObjectCollection|ChildVote[] Collection to store aggregation of ChildVote objects.
     */
    protected $collVoteArticleForeigns;
    protected $collVoteArticleForeignsPartial;

    /**
     * @var        ObjectCollection|ChildTag[] Cross Collection to store aggregation of ChildTag objects.
     */
    protected $collTags;

    /**
     * @var bool
     */
    protected $collTagsPartial;

    /**
     * @var        ObjectCollection|ChildView[] Cross Collection to store aggregation of ChildView objects.
     */
    protected $collViews;

    /**
     * @var bool
     */
    protected $collViewsPartial;

    /**
     * Flag to prevent endless save loop, if this object is referenced
     * by another object which falls in this transaction.
     *
     * @var boolean
     */
    protected $alreadyInSave = false;

    /**
     * An array of objects scheduled for deletion.
     * @var ObjectCollection|ChildTag[]
     */
    protected $tagsScheduledForDeletion = null;

    /**
     * An array of objects scheduled for deletion.
     * @var ObjectCollection|ChildView[]
     */
    protected $viewsScheduledForDeletion = null;

    /**
     * An array of objects scheduled for deletion.
     * @var ObjectCollection|ChildArticleTag[]
     */
    protected $articleTagsScheduledForDeletion = null;

    /**
     * An array of objects scheduled for deletion.
     * @var ObjectCollection|ChildVote[]
     */
    protected $voteArticleForeignsScheduledForDeletion = null;

    /**
     * Applies default values to this object.
     * This method should be called from the object's constructor (or
     * equivalent initialization method).
     * @see __construct()
     */
    public function applyDefaultValues()
    {
        $this->title = 'Untitled';
        $this->category_id = 0;
        $this->image = 'default/post_img.png';
        $this->draft = false;
        $this->poll_question = 'false';
    }

    /**
     * Initializes internal state of Base\Article object.
     * @see applyDefaults()
     */
    public function __construct()
    {
        $this->applyDefaultValues();
    }

    /**
     * Returns whether the object has been modified.
     *
     * @return boolean True if the object has been modified.
     */
    public function isModified()
    {
        return !!$this->modifiedColumns;
    }

    /**
     * Has specified column been modified?
     *
     * @param  string  $col column fully qualified name (TableMap::TYPE_COLNAME), e.g. Book::AUTHOR_ID
     * @return boolean True if $col has been modified.
     */
    public function isColumnModified($col)
    {
        return $this->modifiedColumns && isset($this->modifiedColumns[$col]);
    }

    /**
     * Get the columns that have been modified in this object.
     * @return array A unique list of the modified column names for this object.
     */
    public function getModifiedColumns()
    {
        return $this->modifiedColumns ? array_keys($this->modifiedColumns) : [];
    }

    /**
     * Returns whether the object has ever been saved.  This will
     * be false, if the object was retrieved from storage or was created
     * and then saved.
     *
     * @return boolean true, if the object has never been persisted.
     */
    public function isNew()
    {
        return $this->new;
    }

    /**
     * Setter for the isNew attribute.  This method will be called
     * by Propel-generated children and objects.
     *
     * @param boolean $b the state of the object.
     */
    public function setNew($b)
    {
        $this->new = (boolean) $b;
    }

    /**
     * Whether this object has been deleted.
     * @return boolean The deleted state of this object.
     */
    public function isDeleted()
    {
        return $this->deleted;
    }

    /**
     * Specify whether this object has been deleted.
     * @param  boolean $b The deleted state of this object.
     * @return void
     */
    public function setDeleted($b)
    {
        $this->deleted = (boolean) $b;
    }

    /**
     * Sets the modified state for the object to be false.
     * @param  string $col If supplied, only the specified column is reset.
     * @return void
     */
    public function resetModified($col = null)
    {
        if (null !== $col) {
            if (isset($this->modifiedColumns[$col])) {
                unset($this->modifiedColumns[$col]);
            }
        } else {
            $this->modifiedColumns = array();
        }
    }

    /**
     * Compares this with another <code>Article</code> instance.  If
     * <code>obj</code> is an instance of <code>Article</code>, delegates to
     * <code>equals(Article)</code>.  Otherwise, returns <code>false</code>.
     *
     * @param  mixed   $obj The object to compare to.
     * @return boolean Whether equal to the object specified.
     */
    public function equals($obj)
    {
        if (!$obj instanceof static) {
            return false;
        }

        if ($this === $obj) {
            return true;
        }

        if (null === $this->getPrimaryKey() || null === $obj->getPrimaryKey()) {
            return false;
        }

        return $this->getPrimaryKey() === $obj->getPrimaryKey();
    }

    /**
     * Get the associative array of the virtual columns in this object
     *
     * @return array
     */
    public function getVirtualColumns()
    {
        return $this->virtualColumns;
    }

    /**
     * Checks the existence of a virtual column in this object
     *
     * @param  string  $name The virtual column name
     * @return boolean
     */
    public function hasVirtualColumn($name)
    {
        return array_key_exists($name, $this->virtualColumns);
    }

    /**
     * Get the value of a virtual column in this object
     *
     * @param  string $name The virtual column name
     * @return mixed
     *
     * @throws PropelException
     */
    public function getVirtualColumn($name)
    {
        if (!$this->hasVirtualColumn($name)) {
            throw new PropelException(sprintf('Cannot get value of inexistent virtual column %s.', $name));
        }

        return $this->virtualColumns[$name];
    }

    /**
     * Set the value of a virtual column in this object
     *
     * @param string $name  The virtual column name
     * @param mixed  $value The value to give to the virtual column
     *
     * @return $this|Article The current object, for fluid interface
     */
    public function setVirtualColumn($name, $value)
    {
        $this->virtualColumns[$name] = $value;

        return $this;
    }

    /**
     * Logs a message using Propel::log().
     *
     * @param  string  $msg
     * @param  int     $priority One of the Propel::LOG_* logging levels
     * @return boolean
     */
    protected function log($msg, $priority = Propel::LOG_INFO)
    {
        return Propel::log(get_class($this) . ': ' . $msg, $priority);
    }

    /**
     * Export the current object properties to a string, using a given parser format
     * <code>
     * $book = BookQuery::create()->findPk(9012);
     * echo $book->exportTo('JSON');
     *  => {"Id":9012,"Title":"Don Juan","ISBN":"0140422161","Price":12.99,"PublisherId":1234,"AuthorId":5678}');
     * </code>
     *
     * @param  mixed   $parser                 A AbstractParser instance, or a format name ('XML', 'YAML', 'JSON', 'CSV')
     * @param  boolean $includeLazyLoadColumns (optional) Whether to include lazy load(ed) columns. Defaults to TRUE.
     * @return string  The exported data
     */
    public function exportTo($parser, $includeLazyLoadColumns = true)
    {
        if (!$parser instanceof AbstractParser) {
            $parser = AbstractParser::getParser($parser);
        }

        return $parser->fromArray($this->toArray(TableMap::TYPE_PHPNAME, $includeLazyLoadColumns, array(), true));
    }

    /**
     * Clean up internal collections prior to serializing
     * Avoids recursive loops that turn into segmentation faults when serializing
     */
    public function __sleep()
    {
        $this->clearAllReferences();

        return array_keys(get_object_vars($this));
    }

    /**
     * Get the [id] column value.
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Get the [title] column value.
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Get the [bodyhtml] column value.
     *
     * @return string
     */
    public function getBodyhtml()
    {
        return $this->bodyhtml;
    }

    /**
     * Get the [body] column value.
     *
     * @return string
     */
    public function getBody()
    {
        return $this->body;
    }

    /**
     * Get the [category_id] column value.
     *
     * @return int
     */
    public function getCategoryId()
    {
        return $this->category_id;
    }

    /**
     * Get the [image] column value.
     *
     * @return string
     */
    public function getImage()
    {
        return $this->image;
    }

    /**
     * Get the [draft] column value.
     *
     * @return boolean
     */
    public function getDraft()
    {
        return $this->draft;
    }

    /**
     * Get the [draft] column value.
     *
     * @return boolean
     */
    public function isDraft()
    {
        return $this->getDraft();
    }

    /**
     * Get the [poll_question] column value.
     *
     * @return string
     */
    public function getPollQuestion()
    {
        return $this->poll_question;
    }

    /**
     * Get the [optionally formatted] temporal [created_at] column value.
     *
     *
     * @param      string $format The date/time format string (either date()-style or strftime()-style).
     *                            If format is NULL, then the raw DateTime object will be returned.
     *
     * @return string|DateTime Formatted date/time value as string or DateTime object (if format is NULL), NULL if column is NULL, and 0 if column value is 0000-00-00 00:00:00
     *
     * @throws PropelException - if unable to parse/validate the date/time value.
     */
    public function getCreatedAt($format = NULL)
    {
        if ($format === null) {
            return $this->created_at;
        } else {
            return $this->created_at instanceof \DateTime ? $this->created_at->format($format) : null;
        }
    }

    /**
     * Get the [optionally formatted] temporal [updated_at] column value.
     *
     *
     * @param      string $format The date/time format string (either date()-style or strftime()-style).
     *                            If format is NULL, then the raw DateTime object will be returned.
     *
     * @return string|DateTime Formatted date/time value as string or DateTime object (if format is NULL), NULL if column is NULL, and 0 if column value is 0000-00-00 00:00:00
     *
     * @throws PropelException - if unable to parse/validate the date/time value.
     */
    public function getUpdatedAt($format = NULL)
    {
        if ($format === null) {
            return $this->updated_at;
        } else {
            return $this->updated_at instanceof \DateTime ? $this->updated_at->format($format) : null;
        }
    }

    /**
     * Get the [slug] column value.
     *
     * @return string
     */
    public function getSlug()
    {
        return $this->slug;
    }

    /**
     * Set the value of [id] column.
     *
     * @param int $v new value
     * @return $this|\Article The current object (for fluent API support)
     */
    public function setId($v)
    {
        if ($v !== null) {
            $v = (int) $v;
        }

        if ($this->id !== $v) {
            $this->id = $v;
            $this->modifiedColumns[ArticleTableMap::COL_ID] = true;
        }

        return $this;
    } // setId()

    /**
     * Set the value of [title] column.
     *
     * @param string $v new value
     * @return $this|\Article The current object (for fluent API support)
     */
    public function setTitle($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->title !== $v) {
            $this->title = $v;
            $this->modifiedColumns[ArticleTableMap::COL_TITLE] = true;
        }

        return $this;
    } // setTitle()

    /**
     * Set the value of [bodyhtml] column.
     *
     * @param string $v new value
     * @return $this|\Article The current object (for fluent API support)
     */
    public function setBodyhtml($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->bodyhtml !== $v) {
            $this->bodyhtml = $v;
            $this->modifiedColumns[ArticleTableMap::COL_BODYHTML] = true;
        }

        return $this;
    } // setBodyhtml()

    /**
     * Set the value of [body] column.
     *
     * @param string $v new value
     * @return $this|\Article The current object (for fluent API support)
     */
    public function setBody($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->body !== $v) {
            $this->body = $v;
            $this->modifiedColumns[ArticleTableMap::COL_BODY] = true;
        }

        return $this;
    } // setBody()

    /**
     * Set the value of [category_id] column.
     *
     * @param int $v new value
     * @return $this|\Article The current object (for fluent API support)
     */
    public function setCategoryId($v)
    {
        if ($v !== null) {
            $v = (int) $v;
        }

        if ($this->category_id !== $v) {
            $this->category_id = $v;
            $this->modifiedColumns[ArticleTableMap::COL_CATEGORY_ID] = true;
        }

        if ($this->aCategory !== null && $this->aCategory->getId() !== $v) {
            $this->aCategory = null;
        }

        return $this;
    } // setCategoryId()

    /**
     * Set the value of [image] column.
     *
     * @param string $v new value
     * @return $this|\Article The current object (for fluent API support)
     */
    public function setImage($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->image !== $v) {
            $this->image = $v;
            $this->modifiedColumns[ArticleTableMap::COL_IMAGE] = true;
        }

        return $this;
    } // setImage()

    /**
     * Sets the value of the [draft] column.
     * Non-boolean arguments are converted using the following rules:
     *   * 1, '1', 'true',  'on',  and 'yes' are converted to boolean true
     *   * 0, '0', 'false', 'off', and 'no'  are converted to boolean false
     * Check on string values is case insensitive (so 'FaLsE' is seen as 'false').
     *
     * @param  boolean|integer|string $v The new value
     * @return $this|\Article The current object (for fluent API support)
     */
    public function setDraft($v)
    {
        if ($v !== null) {
            if (is_string($v)) {
                $v = in_array(strtolower($v), array('false', 'off', '-', 'no', 'n', '0', '')) ? false : true;
            } else {
                $v = (boolean) $v;
            }
        }

        if ($this->draft !== $v) {
            $this->draft = $v;
            $this->modifiedColumns[ArticleTableMap::COL_DRAFT] = true;
        }

        return $this;
    } // setDraft()

    /**
     * Set the value of [poll_question] column.
     *
     * @param string $v new value
     * @return $this|\Article The current object (for fluent API support)
     */
    public function setPollQuestion($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->poll_question !== $v) {
            $this->poll_question = $v;
            $this->modifiedColumns[ArticleTableMap::COL_POLL_QUESTION] = true;
        }

        return $this;
    } // setPollQuestion()

    /**
     * Sets the value of [created_at] column to a normalized version of the date/time value specified.
     *
     * @param  mixed $v string, integer (timestamp), or \DateTime value.
     *               Empty strings are treated as NULL.
     * @return $this|\Article The current object (for fluent API support)
     */
    public function setCreatedAt($v)
    {
        $dt = PropelDateTime::newInstance($v, null, 'DateTime');
        if ($this->created_at !== null || $dt !== null) {
            if ($this->created_at === null || $dt === null || $dt->format("Y-m-d H:i:s") !== $this->created_at->format("Y-m-d H:i:s")) {
                $this->created_at = $dt === null ? null : clone $dt;
                $this->modifiedColumns[ArticleTableMap::COL_CREATED_AT] = true;
            }
        } // if either are not null

        return $this;
    } // setCreatedAt()

    /**
     * Sets the value of [updated_at] column to a normalized version of the date/time value specified.
     *
     * @param  mixed $v string, integer (timestamp), or \DateTime value.
     *               Empty strings are treated as NULL.
     * @return $this|\Article The current object (for fluent API support)
     */
    public function setUpdatedAt($v)
    {
        $dt = PropelDateTime::newInstance($v, null, 'DateTime');
        if ($this->updated_at !== null || $dt !== null) {
            if ($this->updated_at === null || $dt === null || $dt->format("Y-m-d H:i:s") !== $this->updated_at->format("Y-m-d H:i:s")) {
                $this->updated_at = $dt === null ? null : clone $dt;
                $this->modifiedColumns[ArticleTableMap::COL_UPDATED_AT] = true;
            }
        } // if either are not null

        return $this;
    } // setUpdatedAt()

    /**
     * Set the value of [slug] column.
     *
     * @param string $v new value
     * @return $this|\Article The current object (for fluent API support)
     */
    public function setSlug($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->slug !== $v) {
            $this->slug = $v;
            $this->modifiedColumns[ArticleTableMap::COL_SLUG] = true;
        }

        return $this;
    } // setSlug()

    /**
     * Indicates whether the columns in this object are only set to default values.
     *
     * This method can be used in conjunction with isModified() to indicate whether an object is both
     * modified _and_ has some values set which are non-default.
     *
     * @return boolean Whether the columns in this object are only been set with default values.
     */
    public function hasOnlyDefaultValues()
    {
            if ($this->title !== 'Untitled') {
                return false;
            }

            if ($this->category_id !== 0) {
                return false;
            }

            if ($this->image !== 'default/post_img.png') {
                return false;
            }

            if ($this->draft !== false) {
                return false;
            }

            if ($this->poll_question !== 'false') {
                return false;
            }

        // otherwise, everything was equal, so return TRUE
        return true;
    } // hasOnlyDefaultValues()

    /**
     * Hydrates (populates) the object variables with values from the database resultset.
     *
     * An offset (0-based "start column") is specified so that objects can be hydrated
     * with a subset of the columns in the resultset rows.  This is needed, for example,
     * for results of JOIN queries where the resultset row includes columns from two or
     * more tables.
     *
     * @param array   $row       The row returned by DataFetcher->fetch().
     * @param int     $startcol  0-based offset column which indicates which restultset column to start with.
     * @param boolean $rehydrate Whether this object is being re-hydrated from the database.
     * @param string  $indexType The index type of $row. Mostly DataFetcher->getIndexType().
                                  One of the class type constants TableMap::TYPE_PHPNAME, TableMap::TYPE_CAMELNAME
     *                            TableMap::TYPE_COLNAME, TableMap::TYPE_FIELDNAME, TableMap::TYPE_NUM.
     *
     * @return int             next starting column
     * @throws PropelException - Any caught Exception will be rewrapped as a PropelException.
     */
    public function hydrate($row, $startcol = 0, $rehydrate = false, $indexType = TableMap::TYPE_NUM)
    {
        try {

            $col = $row[TableMap::TYPE_NUM == $indexType ? 0 + $startcol : ArticleTableMap::translateFieldName('Id', TableMap::TYPE_PHPNAME, $indexType)];
            $this->id = (null !== $col) ? (int) $col : null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 1 + $startcol : ArticleTableMap::translateFieldName('Title', TableMap::TYPE_PHPNAME, $indexType)];
            $this->title = (null !== $col) ? (string) $col : null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 2 + $startcol : ArticleTableMap::translateFieldName('Bodyhtml', TableMap::TYPE_PHPNAME, $indexType)];
            $this->bodyhtml = (null !== $col) ? (string) $col : null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 3 + $startcol : ArticleTableMap::translateFieldName('Body', TableMap::TYPE_PHPNAME, $indexType)];
            $this->body = (null !== $col) ? (string) $col : null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 4 + $startcol : ArticleTableMap::translateFieldName('CategoryId', TableMap::TYPE_PHPNAME, $indexType)];
            $this->category_id = (null !== $col) ? (int) $col : null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 5 + $startcol : ArticleTableMap::translateFieldName('Image', TableMap::TYPE_PHPNAME, $indexType)];
            $this->image = (null !== $col) ? (string) $col : null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 6 + $startcol : ArticleTableMap::translateFieldName('Draft', TableMap::TYPE_PHPNAME, $indexType)];
            $this->draft = (null !== $col) ? (boolean) $col : null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 7 + $startcol : ArticleTableMap::translateFieldName('PollQuestion', TableMap::TYPE_PHPNAME, $indexType)];
            $this->poll_question = (null !== $col) ? (string) $col : null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 8 + $startcol : ArticleTableMap::translateFieldName('CreatedAt', TableMap::TYPE_PHPNAME, $indexType)];
            if ($col === '0000-00-00 00:00:00') {
                $col = null;
            }
            $this->created_at = (null !== $col) ? PropelDateTime::newInstance($col, null, 'DateTime') : null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 9 + $startcol : ArticleTableMap::translateFieldName('UpdatedAt', TableMap::TYPE_PHPNAME, $indexType)];
            if ($col === '0000-00-00 00:00:00') {
                $col = null;
            }
            $this->updated_at = (null !== $col) ? PropelDateTime::newInstance($col, null, 'DateTime') : null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 10 + $startcol : ArticleTableMap::translateFieldName('Slug', TableMap::TYPE_PHPNAME, $indexType)];
            $this->slug = (null !== $col) ? (string) $col : null;
            $this->resetModified();

            $this->setNew(false);

            if ($rehydrate) {
                $this->ensureConsistency();
            }

            return $startcol + 11; // 11 = ArticleTableMap::NUM_HYDRATE_COLUMNS.

        } catch (Exception $e) {
            throw new PropelException(sprintf('Error populating %s object', '\\Article'), 0, $e);
        }
    }

    /**
     * Checks and repairs the internal consistency of the object.
     *
     * This method is executed after an already-instantiated object is re-hydrated
     * from the database.  It exists to check any foreign keys to make sure that
     * the objects related to the current object are correct based on foreign key.
     *
     * You can override this method in the stub class, but you should always invoke
     * the base method from the overridden method (i.e. parent::ensureConsistency()),
     * in case your model changes.
     *
     * @throws PropelException
     */
    public function ensureConsistency()
    {
        if ($this->aCategory !== null && $this->category_id !== $this->aCategory->getId()) {
            $this->aCategory = null;
        }
    } // ensureConsistency

    /**
     * Reloads this object from datastore based on primary key and (optionally) resets all associated objects.
     *
     * This will only work if the object has been saved and has a valid primary key set.
     *
     * @param      boolean $deep (optional) Whether to also de-associated any related objects.
     * @param      ConnectionInterface $con (optional) The ConnectionInterface connection to use.
     * @return void
     * @throws PropelException - if this object is deleted, unsaved or doesn't have pk match in db
     */
    public function reload($deep = false, ConnectionInterface $con = null)
    {
        if ($this->isDeleted()) {
            throw new PropelException("Cannot reload a deleted object.");
        }

        if ($this->isNew()) {
            throw new PropelException("Cannot reload an unsaved object.");
        }

        if ($con === null) {
            $con = Propel::getServiceContainer()->getReadConnection(ArticleTableMap::DATABASE_NAME);
        }

        // We don't need to alter the object instance pool; we're just modifying this instance
        // already in the pool.

        $dataFetcher = ChildArticleQuery::create(null, $this->buildPkeyCriteria())->setFormatter(ModelCriteria::FORMAT_STATEMENT)->find($con);
        $row = $dataFetcher->fetch();
        $dataFetcher->close();
        if (!$row) {
            throw new PropelException('Cannot find matching row in the database to reload object values.');
        }
        $this->hydrate($row, 0, true, $dataFetcher->getIndexType()); // rehydrate

        if ($deep) {  // also de-associate any related objects?

            $this->aCategory = null;
            $this->collArticleTags = null;

            $this->singleviewArticleForeign = null;

            $this->collVoteArticleForeigns = null;

            $this->collTags = null;
            $this->collViews = null;
        } // if (deep)
    }

    /**
     * Removes this object from datastore and sets delete attribute.
     *
     * @param      ConnectionInterface $con
     * @return void
     * @throws PropelException
     * @see Article::setDeleted()
     * @see Article::isDeleted()
     */
    public function delete(ConnectionInterface $con = null)
    {
        if ($this->isDeleted()) {
            throw new PropelException("This object has already been deleted.");
        }

        if ($con === null) {
            $con = Propel::getServiceContainer()->getWriteConnection(ArticleTableMap::DATABASE_NAME);
        }

        $con->transaction(function () use ($con) {
            $deleteQuery = ChildArticleQuery::create()
                ->filterByPrimaryKey($this->getPrimaryKey());
            $ret = $this->preDelete($con);
            if ($ret) {
                $deleteQuery->delete($con);
                $this->postDelete($con);
                $this->setDeleted(true);
            }
        });
    }

    /**
     * Persists this object to the database.
     *
     * If the object is new, it inserts it; otherwise an update is performed.
     * All modified related objects will also be persisted in the doSave()
     * method.  This method wraps all precipitate database operations in a
     * single transaction.
     *
     * @param      ConnectionInterface $con
     * @return int             The number of rows affected by this insert/update and any referring fk objects' save() operations.
     * @throws PropelException
     * @see doSave()
     */
    public function save(ConnectionInterface $con = null)
    {
        if ($this->isDeleted()) {
            throw new PropelException("You cannot save an object that has been deleted.");
        }

        if ($con === null) {
            $con = Propel::getServiceContainer()->getWriteConnection(ArticleTableMap::DATABASE_NAME);
        }

        return $con->transaction(function () use ($con) {
            $isInsert = $this->isNew();
            $ret = $this->preSave($con);
            // sluggable behavior

            if ($this->isColumnModified(ArticleTableMap::COL_SLUG) && $this->getSlug()) {
                $this->setSlug($this->makeSlugUnique($this->getSlug()));
            } else {
                $this->setSlug($this->createSlug());
            }
            if ($isInsert) {
                $ret = $ret && $this->preInsert($con);
                // timestampable behavior

                if (!$this->isColumnModified(ArticleTableMap::COL_CREATED_AT)) {
                    $this->setCreatedAt(time());
                }
                if (!$this->isColumnModified(ArticleTableMap::COL_UPDATED_AT)) {
                    $this->setUpdatedAt(time());
                }
            } else {
                $ret = $ret && $this->preUpdate($con);
                // timestampable behavior
                if ($this->isModified() && !$this->isColumnModified(ArticleTableMap::COL_UPDATED_AT)) {
                    $this->setUpdatedAt(time());
                }
            }
            if ($ret) {
                $affectedRows = $this->doSave($con);
                if ($isInsert) {
                    $this->postInsert($con);
                } else {
                    $this->postUpdate($con);
                }
                $this->postSave($con);
                ArticleTableMap::addInstanceToPool($this);
            } else {
                $affectedRows = 0;
            }

            return $affectedRows;
        });
    }

    /**
     * Performs the work of inserting or updating the row in the database.
     *
     * If the object is new, it inserts it; otherwise an update is performed.
     * All related objects are also updated in this method.
     *
     * @param      ConnectionInterface $con
     * @return int             The number of rows affected by this insert/update and any referring fk objects' save() operations.
     * @throws PropelException
     * @see save()
     */
    protected function doSave(ConnectionInterface $con)
    {
        $affectedRows = 0; // initialize var to track total num of affected rows
        if (!$this->alreadyInSave) {
            $this->alreadyInSave = true;

            // We call the save method on the following object(s) if they
            // were passed to this object by their corresponding set
            // method.  This object relates to these object(s) by a
            // foreign key reference.

            if ($this->aCategory !== null) {
                if ($this->aCategory->isModified() || $this->aCategory->isNew()) {
                    $affectedRows += $this->aCategory->save($con);
                }
                $this->setCategory($this->aCategory);
            }

            if ($this->isNew() || $this->isModified()) {
                // persist changes
                if ($this->isNew()) {
                    $this->doInsert($con);
                    $affectedRows += 1;
                } else {
                    $affectedRows += $this->doUpdate($con);
                }
                $this->resetModified();
            }

            if ($this->tagsScheduledForDeletion !== null) {
                if (!$this->tagsScheduledForDeletion->isEmpty()) {
                    $pks = array();
                    foreach ($this->tagsScheduledForDeletion as $entry) {
                        $entryPk = [];

                        $entryPk[0] = $this->getId();
                        $entryPk[1] = $entry->getId();
                        $pks[] = $entryPk;
                    }

                    \ArticleTagQuery::create()
                        ->filterByPrimaryKeys($pks)
                        ->delete($con);

                    $this->tagsScheduledForDeletion = null;
                }

            }

            if ($this->collTags) {
                foreach ($this->collTags as $tag) {
                    if (!$tag->isDeleted() && ($tag->isNew() || $tag->isModified())) {
                        $tag->save($con);
                    }
                }
            }


            if ($this->viewsScheduledForDeletion !== null) {
                if (!$this->viewsScheduledForDeletion->isEmpty()) {
                    $pks = array();
                    foreach ($this->viewsScheduledForDeletion as $entry) {
                        $entryPk = [];

                        $entryPk[0] = $this->getId();
                        $entryPk[1] = $entry->getIpAddress();
                        $pks[] = $entryPk;
                    }

                    \VoteQuery::create()
                        ->filterByPrimaryKeys($pks)
                        ->delete($con);

                    $this->viewsScheduledForDeletion = null;
                }

            }

            if ($this->collViews) {
                foreach ($this->collViews as $view) {
                    if (!$view->isDeleted() && ($view->isNew() || $view->isModified())) {
                        $view->save($con);
                    }
                }
            }


            if ($this->articleTagsScheduledForDeletion !== null) {
                if (!$this->articleTagsScheduledForDeletion->isEmpty()) {
                    \ArticleTagQuery::create()
                        ->filterByPrimaryKeys($this->articleTagsScheduledForDeletion->getPrimaryKeys(false))
                        ->delete($con);
                    $this->articleTagsScheduledForDeletion = null;
                }
            }

            if ($this->collArticleTags !== null) {
                foreach ($this->collArticleTags as $referrerFK) {
                    if (!$referrerFK->isDeleted() && ($referrerFK->isNew() || $referrerFK->isModified())) {
                        $affectedRows += $referrerFK->save($con);
                    }
                }
            }

            if ($this->singleviewArticleForeign !== null) {
                if (!$this->singleviewArticleForeign->isDeleted() && ($this->singleviewArticleForeign->isNew() || $this->singleviewArticleForeign->isModified())) {
                    $affectedRows += $this->singleviewArticleForeign->save($con);
                }
            }

            if ($this->voteArticleForeignsScheduledForDeletion !== null) {
                if (!$this->voteArticleForeignsScheduledForDeletion->isEmpty()) {
                    \VoteQuery::create()
                        ->filterByPrimaryKeys($this->voteArticleForeignsScheduledForDeletion->getPrimaryKeys(false))
                        ->delete($con);
                    $this->voteArticleForeignsScheduledForDeletion = null;
                }
            }

            if ($this->collVoteArticleForeigns !== null) {
                foreach ($this->collVoteArticleForeigns as $referrerFK) {
                    if (!$referrerFK->isDeleted() && ($referrerFK->isNew() || $referrerFK->isModified())) {
                        $affectedRows += $referrerFK->save($con);
                    }
                }
            }

            $this->alreadyInSave = false;

        }

        return $affectedRows;
    } // doSave()

    /**
     * Insert the row in the database.
     *
     * @param      ConnectionInterface $con
     *
     * @throws PropelException
     * @see doSave()
     */
    protected function doInsert(ConnectionInterface $con)
    {
        $modifiedColumns = array();
        $index = 0;

        $this->modifiedColumns[ArticleTableMap::COL_ID] = true;
        if (null !== $this->id) {
            throw new PropelException('Cannot insert a value for auto-increment primary key (' . ArticleTableMap::COL_ID . ')');
        }

         // check the columns in natural order for more readable SQL queries
        if ($this->isColumnModified(ArticleTableMap::COL_ID)) {
            $modifiedColumns[':p' . $index++]  = 'id';
        }
        if ($this->isColumnModified(ArticleTableMap::COL_TITLE)) {
            $modifiedColumns[':p' . $index++]  = 'title';
        }
        if ($this->isColumnModified(ArticleTableMap::COL_BODYHTML)) {
            $modifiedColumns[':p' . $index++]  = 'bodyHTML';
        }
        if ($this->isColumnModified(ArticleTableMap::COL_BODY)) {
            $modifiedColumns[':p' . $index++]  = 'body';
        }
        if ($this->isColumnModified(ArticleTableMap::COL_CATEGORY_ID)) {
            $modifiedColumns[':p' . $index++]  = 'category_id';
        }
        if ($this->isColumnModified(ArticleTableMap::COL_IMAGE)) {
            $modifiedColumns[':p' . $index++]  = 'image';
        }
        if ($this->isColumnModified(ArticleTableMap::COL_DRAFT)) {
            $modifiedColumns[':p' . $index++]  = 'draft';
        }
        if ($this->isColumnModified(ArticleTableMap::COL_POLL_QUESTION)) {
            $modifiedColumns[':p' . $index++]  = 'poll_question';
        }
        if ($this->isColumnModified(ArticleTableMap::COL_CREATED_AT)) {
            $modifiedColumns[':p' . $index++]  = 'created_at';
        }
        if ($this->isColumnModified(ArticleTableMap::COL_UPDATED_AT)) {
            $modifiedColumns[':p' . $index++]  = 'updated_at';
        }
        if ($this->isColumnModified(ArticleTableMap::COL_SLUG)) {
            $modifiedColumns[':p' . $index++]  = 'slug';
        }

        $sql = sprintf(
            'INSERT INTO article (%s) VALUES (%s)',
            implode(', ', $modifiedColumns),
            implode(', ', array_keys($modifiedColumns))
        );

        try {
            $stmt = $con->prepare($sql);
            foreach ($modifiedColumns as $identifier => $columnName) {
                switch ($columnName) {
                    case 'id':
                        $stmt->bindValue($identifier, $this->id, PDO::PARAM_INT);
                        break;
                    case 'title':
                        $stmt->bindValue($identifier, $this->title, PDO::PARAM_STR);
                        break;
                    case 'bodyHTML':
                        $stmt->bindValue($identifier, $this->bodyhtml, PDO::PARAM_STR);
                        break;
                    case 'body':
                        $stmt->bindValue($identifier, $this->body, PDO::PARAM_STR);
                        break;
                    case 'category_id':
                        $stmt->bindValue($identifier, $this->category_id, PDO::PARAM_INT);
                        break;
                    case 'image':
                        $stmt->bindValue($identifier, $this->image, PDO::PARAM_STR);
                        break;
                    case 'draft':
                        $stmt->bindValue($identifier, (int) $this->draft, PDO::PARAM_INT);
                        break;
                    case 'poll_question':
                        $stmt->bindValue($identifier, $this->poll_question, PDO::PARAM_STR);
                        break;
                    case 'created_at':
                        $stmt->bindValue($identifier, $this->created_at ? $this->created_at->format("Y-m-d H:i:s") : null, PDO::PARAM_STR);
                        break;
                    case 'updated_at':
                        $stmt->bindValue($identifier, $this->updated_at ? $this->updated_at->format("Y-m-d H:i:s") : null, PDO::PARAM_STR);
                        break;
                    case 'slug':
                        $stmt->bindValue($identifier, $this->slug, PDO::PARAM_STR);
                        break;
                }
            }
            $stmt->execute();
        } catch (Exception $e) {
            Propel::log($e->getMessage(), Propel::LOG_ERR);
            throw new PropelException(sprintf('Unable to execute INSERT statement [%s]', $sql), 0, $e);
        }

        try {
            $pk = $con->lastInsertId();
        } catch (Exception $e) {
            throw new PropelException('Unable to get autoincrement id.', 0, $e);
        }
        $this->setId($pk);

        $this->setNew(false);
    }

    /**
     * Update the row in the database.
     *
     * @param      ConnectionInterface $con
     *
     * @return Integer Number of updated rows
     * @see doSave()
     */
    protected function doUpdate(ConnectionInterface $con)
    {
        $selectCriteria = $this->buildPkeyCriteria();
        $valuesCriteria = $this->buildCriteria();

        return $selectCriteria->doUpdate($valuesCriteria, $con);
    }

    /**
     * Retrieves a field from the object by name passed in as a string.
     *
     * @param      string $name name
     * @param      string $type The type of fieldname the $name is of:
     *                     one of the class type constants TableMap::TYPE_PHPNAME, TableMap::TYPE_CAMELNAME
     *                     TableMap::TYPE_COLNAME, TableMap::TYPE_FIELDNAME, TableMap::TYPE_NUM.
     *                     Defaults to TableMap::TYPE_PHPNAME.
     * @return mixed Value of field.
     */
    public function getByName($name, $type = TableMap::TYPE_PHPNAME)
    {
        $pos = ArticleTableMap::translateFieldName($name, $type, TableMap::TYPE_NUM);
        $field = $this->getByPosition($pos);

        return $field;
    }

    /**
     * Retrieves a field from the object by Position as specified in the xml schema.
     * Zero-based.
     *
     * @param      int $pos position in xml schema
     * @return mixed Value of field at $pos
     */
    public function getByPosition($pos)
    {
        switch ($pos) {
            case 0:
                return $this->getId();
                break;
            case 1:
                return $this->getTitle();
                break;
            case 2:
                return $this->getBodyhtml();
                break;
            case 3:
                return $this->getBody();
                break;
            case 4:
                return $this->getCategoryId();
                break;
            case 5:
                return $this->getImage();
                break;
            case 6:
                return $this->getDraft();
                break;
            case 7:
                return $this->getPollQuestion();
                break;
            case 8:
                return $this->getCreatedAt();
                break;
            case 9:
                return $this->getUpdatedAt();
                break;
            case 10:
                return $this->getSlug();
                break;
            default:
                return null;
                break;
        } // switch()
    }

    /**
     * Exports the object as an array.
     *
     * You can specify the key type of the array by passing one of the class
     * type constants.
     *
     * @param     string  $keyType (optional) One of the class type constants TableMap::TYPE_PHPNAME, TableMap::TYPE_CAMELNAME,
     *                    TableMap::TYPE_COLNAME, TableMap::TYPE_FIELDNAME, TableMap::TYPE_NUM.
     *                    Defaults to TableMap::TYPE_PHPNAME.
     * @param     boolean $includeLazyLoadColumns (optional) Whether to include lazy loaded columns. Defaults to TRUE.
     * @param     array $alreadyDumpedObjects List of objects to skip to avoid recursion
     * @param     boolean $includeForeignObjects (optional) Whether to include hydrated related objects. Default to FALSE.
     *
     * @return array an associative array containing the field names (as keys) and field values
     */
    public function toArray($keyType = TableMap::TYPE_PHPNAME, $includeLazyLoadColumns = true, $alreadyDumpedObjects = array(), $includeForeignObjects = false)
    {

        if (isset($alreadyDumpedObjects['Article'][$this->hashCode()])) {
            return '*RECURSION*';
        }
        $alreadyDumpedObjects['Article'][$this->hashCode()] = true;
        $keys = ArticleTableMap::getFieldNames($keyType);
        $result = array(
            $keys[0] => $this->getId(),
            $keys[1] => $this->getTitle(),
            $keys[2] => $this->getBodyhtml(),
            $keys[3] => $this->getBody(),
            $keys[4] => $this->getCategoryId(),
            $keys[5] => $this->getImage(),
            $keys[6] => $this->getDraft(),
            $keys[7] => $this->getPollQuestion(),
            $keys[8] => $this->getCreatedAt(),
            $keys[9] => $this->getUpdatedAt(),
            $keys[10] => $this->getSlug(),
        );

        $utc = new \DateTimeZone('utc');
        if ($result[$keys[8]] instanceof \DateTime) {
            // When changing timezone we don't want to change existing instances
            $dateTime = clone $result[$keys[8]];
            $result[$keys[8]] = $dateTime->setTimezone($utc)->format('Y-m-d\TH:i:s\Z');
        }

        if ($result[$keys[9]] instanceof \DateTime) {
            // When changing timezone we don't want to change existing instances
            $dateTime = clone $result[$keys[9]];
            $result[$keys[9]] = $dateTime->setTimezone($utc)->format('Y-m-d\TH:i:s\Z');
        }

        $virtualColumns = $this->virtualColumns;
        foreach ($virtualColumns as $key => $virtualColumn) {
            $result[$key] = $virtualColumn;
        }

        if ($includeForeignObjects) {
            if (null !== $this->aCategory) {

                switch ($keyType) {
                    case TableMap::TYPE_CAMELNAME:
                        $key = 'category';
                        break;
                    case TableMap::TYPE_FIELDNAME:
                        $key = 'category';
                        break;
                    default:
                        $key = 'Category';
                }

                $result[$key] = $this->aCategory->toArray($keyType, $includeLazyLoadColumns,  $alreadyDumpedObjects, true);
            }
            if (null !== $this->collArticleTags) {

                switch ($keyType) {
                    case TableMap::TYPE_CAMELNAME:
                        $key = 'articleTags';
                        break;
                    case TableMap::TYPE_FIELDNAME:
                        $key = 'article_tags';
                        break;
                    default:
                        $key = 'ArticleTags';
                }

                $result[$key] = $this->collArticleTags->toArray(null, false, $keyType, $includeLazyLoadColumns, $alreadyDumpedObjects);
            }
            if (null !== $this->singleviewArticleForeign) {

                switch ($keyType) {
                    case TableMap::TYPE_CAMELNAME:
                        $key = 'view';
                        break;
                    case TableMap::TYPE_FIELDNAME:
                        $key = 'view';
                        break;
                    default:
                        $key = 'View';
                }

                $result[$key] = $this->singleviewArticleForeign->toArray($keyType, $includeLazyLoadColumns, $alreadyDumpedObjects, true);
            }
            if (null !== $this->collVoteArticleForeigns) {

                switch ($keyType) {
                    case TableMap::TYPE_CAMELNAME:
                        $key = 'votes';
                        break;
                    case TableMap::TYPE_FIELDNAME:
                        $key = 'votes';
                        break;
                    default:
                        $key = 'Votes';
                }

                $result[$key] = $this->collVoteArticleForeigns->toArray(null, false, $keyType, $includeLazyLoadColumns, $alreadyDumpedObjects);
            }
        }

        return $result;
    }

    /**
     * Sets a field from the object by name passed in as a string.
     *
     * @param  string $name
     * @param  mixed  $value field value
     * @param  string $type The type of fieldname the $name is of:
     *                one of the class type constants TableMap::TYPE_PHPNAME, TableMap::TYPE_CAMELNAME
     *                TableMap::TYPE_COLNAME, TableMap::TYPE_FIELDNAME, TableMap::TYPE_NUM.
     *                Defaults to TableMap::TYPE_PHPNAME.
     * @return $this|\Article
     */
    public function setByName($name, $value, $type = TableMap::TYPE_PHPNAME)
    {
        $pos = ArticleTableMap::translateFieldName($name, $type, TableMap::TYPE_NUM);

        return $this->setByPosition($pos, $value);
    }

    /**
     * Sets a field from the object by Position as specified in the xml schema.
     * Zero-based.
     *
     * @param  int $pos position in xml schema
     * @param  mixed $value field value
     * @return $this|\Article
     */
    public function setByPosition($pos, $value)
    {
        switch ($pos) {
            case 0:
                $this->setId($value);
                break;
            case 1:
                $this->setTitle($value);
                break;
            case 2:
                $this->setBodyhtml($value);
                break;
            case 3:
                $this->setBody($value);
                break;
            case 4:
                $this->setCategoryId($value);
                break;
            case 5:
                $this->setImage($value);
                break;
            case 6:
                $this->setDraft($value);
                break;
            case 7:
                $this->setPollQuestion($value);
                break;
            case 8:
                $this->setCreatedAt($value);
                break;
            case 9:
                $this->setUpdatedAt($value);
                break;
            case 10:
                $this->setSlug($value);
                break;
        } // switch()

        return $this;
    }

    /**
     * Populates the object using an array.
     *
     * This is particularly useful when populating an object from one of the
     * request arrays (e.g. $_POST).  This method goes through the column
     * names, checking to see whether a matching key exists in populated
     * array. If so the setByName() method is called for that column.
     *
     * You can specify the key type of the array by additionally passing one
     * of the class type constants TableMap::TYPE_PHPNAME, TableMap::TYPE_CAMELNAME,
     * TableMap::TYPE_COLNAME, TableMap::TYPE_FIELDNAME, TableMap::TYPE_NUM.
     * The default key type is the column's TableMap::TYPE_PHPNAME.
     *
     * @param      array  $arr     An array to populate the object from.
     * @param      string $keyType The type of keys the array uses.
     * @return void
     */
    public function fromArray($arr, $keyType = TableMap::TYPE_PHPNAME)
    {
        $keys = ArticleTableMap::getFieldNames($keyType);

        if (array_key_exists($keys[0], $arr)) {
            $this->setId($arr[$keys[0]]);
        }
        if (array_key_exists($keys[1], $arr)) {
            $this->setTitle($arr[$keys[1]]);
        }
        if (array_key_exists($keys[2], $arr)) {
            $this->setBodyhtml($arr[$keys[2]]);
        }
        if (array_key_exists($keys[3], $arr)) {
            $this->setBody($arr[$keys[3]]);
        }
        if (array_key_exists($keys[4], $arr)) {
            $this->setCategoryId($arr[$keys[4]]);
        }
        if (array_key_exists($keys[5], $arr)) {
            $this->setImage($arr[$keys[5]]);
        }
        if (array_key_exists($keys[6], $arr)) {
            $this->setDraft($arr[$keys[6]]);
        }
        if (array_key_exists($keys[7], $arr)) {
            $this->setPollQuestion($arr[$keys[7]]);
        }
        if (array_key_exists($keys[8], $arr)) {
            $this->setCreatedAt($arr[$keys[8]]);
        }
        if (array_key_exists($keys[9], $arr)) {
            $this->setUpdatedAt($arr[$keys[9]]);
        }
        if (array_key_exists($keys[10], $arr)) {
            $this->setSlug($arr[$keys[10]]);
        }
    }

     /**
     * Populate the current object from a string, using a given parser format
     * <code>
     * $book = new Book();
     * $book->importFrom('JSON', '{"Id":9012,"Title":"Don Juan","ISBN":"0140422161","Price":12.99,"PublisherId":1234,"AuthorId":5678}');
     * </code>
     *
     * You can specify the key type of the array by additionally passing one
     * of the class type constants TableMap::TYPE_PHPNAME, TableMap::TYPE_CAMELNAME,
     * TableMap::TYPE_COLNAME, TableMap::TYPE_FIELDNAME, TableMap::TYPE_NUM.
     * The default key type is the column's TableMap::TYPE_PHPNAME.
     *
     * @param mixed $parser A AbstractParser instance,
     *                       or a format name ('XML', 'YAML', 'JSON', 'CSV')
     * @param string $data The source data to import from
     * @param string $keyType The type of keys the array uses.
     *
     * @return $this|\Article The current object, for fluid interface
     */
    public function importFrom($parser, $data, $keyType = TableMap::TYPE_PHPNAME)
    {
        if (!$parser instanceof AbstractParser) {
            $parser = AbstractParser::getParser($parser);
        }

        $this->fromArray($parser->toArray($data), $keyType);

        return $this;
    }

    /**
     * Build a Criteria object containing the values of all modified columns in this object.
     *
     * @return Criteria The Criteria object containing all modified values.
     */
    public function buildCriteria()
    {
        $criteria = new Criteria(ArticleTableMap::DATABASE_NAME);

        if ($this->isColumnModified(ArticleTableMap::COL_ID)) {
            $criteria->add(ArticleTableMap::COL_ID, $this->id);
        }
        if ($this->isColumnModified(ArticleTableMap::COL_TITLE)) {
            $criteria->add(ArticleTableMap::COL_TITLE, $this->title);
        }
        if ($this->isColumnModified(ArticleTableMap::COL_BODYHTML)) {
            $criteria->add(ArticleTableMap::COL_BODYHTML, $this->bodyhtml);
        }
        if ($this->isColumnModified(ArticleTableMap::COL_BODY)) {
            $criteria->add(ArticleTableMap::COL_BODY, $this->body);
        }
        if ($this->isColumnModified(ArticleTableMap::COL_CATEGORY_ID)) {
            $criteria->add(ArticleTableMap::COL_CATEGORY_ID, $this->category_id);
        }
        if ($this->isColumnModified(ArticleTableMap::COL_IMAGE)) {
            $criteria->add(ArticleTableMap::COL_IMAGE, $this->image);
        }
        if ($this->isColumnModified(ArticleTableMap::COL_DRAFT)) {
            $criteria->add(ArticleTableMap::COL_DRAFT, $this->draft);
        }
        if ($this->isColumnModified(ArticleTableMap::COL_POLL_QUESTION)) {
            $criteria->add(ArticleTableMap::COL_POLL_QUESTION, $this->poll_question);
        }
        if ($this->isColumnModified(ArticleTableMap::COL_CREATED_AT)) {
            $criteria->add(ArticleTableMap::COL_CREATED_AT, $this->created_at);
        }
        if ($this->isColumnModified(ArticleTableMap::COL_UPDATED_AT)) {
            $criteria->add(ArticleTableMap::COL_UPDATED_AT, $this->updated_at);
        }
        if ($this->isColumnModified(ArticleTableMap::COL_SLUG)) {
            $criteria->add(ArticleTableMap::COL_SLUG, $this->slug);
        }

        return $criteria;
    }

    /**
     * Builds a Criteria object containing the primary key for this object.
     *
     * Unlike buildCriteria() this method includes the primary key values regardless
     * of whether or not they have been modified.
     *
     * @throws LogicException if no primary key is defined
     *
     * @return Criteria The Criteria object containing value(s) for primary key(s).
     */
    public function buildPkeyCriteria()
    {
        $criteria = ChildArticleQuery::create();
        $criteria->add(ArticleTableMap::COL_ID, $this->id);

        return $criteria;
    }

    /**
     * If the primary key is not null, return the hashcode of the
     * primary key. Otherwise, return the hash code of the object.
     *
     * @return int Hashcode
     */
    public function hashCode()
    {
        $validPk = null !== $this->getId();

        $validPrimaryKeyFKs = 0;
        $primaryKeyFKs = [];

        if ($validPk) {
            return crc32(json_encode($this->getPrimaryKey(), JSON_UNESCAPED_UNICODE));
        } elseif ($validPrimaryKeyFKs) {
            return crc32(json_encode($primaryKeyFKs, JSON_UNESCAPED_UNICODE));
        }

        return spl_object_hash($this);
    }

    /**
     * Returns the primary key for this object (row).
     * @return int
     */
    public function getPrimaryKey()
    {
        return $this->getId();
    }

    /**
     * Generic method to set the primary key (id column).
     *
     * @param       int $key Primary key.
     * @return void
     */
    public function setPrimaryKey($key)
    {
        $this->setId($key);
    }

    /**
     * Returns true if the primary key for this object is null.
     * @return boolean
     */
    public function isPrimaryKeyNull()
    {
        return null === $this->getId();
    }

    /**
     * Sets contents of passed object to values from current object.
     *
     * If desired, this method can also make copies of all associated (fkey referrers)
     * objects.
     *
     * @param      object $copyObj An object of \Article (or compatible) type.
     * @param      boolean $deepCopy Whether to also copy all rows that refer (by fkey) to the current row.
     * @param      boolean $makeNew Whether to reset autoincrement PKs and make the object new.
     * @throws PropelException
     */
    public function copyInto($copyObj, $deepCopy = false, $makeNew = true)
    {
        $copyObj->setTitle($this->getTitle());
        $copyObj->setBodyhtml($this->getBodyhtml());
        $copyObj->setBody($this->getBody());
        $copyObj->setCategoryId($this->getCategoryId());
        $copyObj->setImage($this->getImage());
        $copyObj->setDraft($this->getDraft());
        $copyObj->setPollQuestion($this->getPollQuestion());
        $copyObj->setCreatedAt($this->getCreatedAt());
        $copyObj->setUpdatedAt($this->getUpdatedAt());
        $copyObj->setSlug($this->getSlug());

        if ($deepCopy) {
            // important: temporarily setNew(false) because this affects the behavior of
            // the getter/setter methods for fkey referrer objects.
            $copyObj->setNew(false);

            foreach ($this->getArticleTags() as $relObj) {
                if ($relObj !== $this) {  // ensure that we don't try to copy a reference to ourselves
                    $copyObj->addArticleTag($relObj->copy($deepCopy));
                }
            }

            $relObj = $this->getviewArticleForeign();
            if ($relObj) {
                $copyObj->setviewArticleForeign($relObj->copy($deepCopy));
            }

            foreach ($this->getVoteArticleForeigns() as $relObj) {
                if ($relObj !== $this) {  // ensure that we don't try to copy a reference to ourselves
                    $copyObj->addVoteArticleForeign($relObj->copy($deepCopy));
                }
            }

        } // if ($deepCopy)

        if ($makeNew) {
            $copyObj->setNew(true);
            $copyObj->setId(NULL); // this is a auto-increment column, so set to default value
        }
    }

    /**
     * Makes a copy of this object that will be inserted as a new row in table when saved.
     * It creates a new object filling in the simple attributes, but skipping any primary
     * keys that are defined for the table.
     *
     * If desired, this method can also make copies of all associated (fkey referrers)
     * objects.
     *
     * @param  boolean $deepCopy Whether to also copy all rows that refer (by fkey) to the current row.
     * @return \Article Clone of current object.
     * @throws PropelException
     */
    public function copy($deepCopy = false)
    {
        // we use get_class(), because this might be a subclass
        $clazz = get_class($this);
        $copyObj = new $clazz();
        $this->copyInto($copyObj, $deepCopy);

        return $copyObj;
    }

    /**
     * Declares an association between this object and a ChildCategory object.
     *
     * @param  ChildCategory $v
     * @return $this|\Article The current object (for fluent API support)
     * @throws PropelException
     */
    public function setCategory(ChildCategory $v = null)
    {
        if ($v === null) {
            $this->setCategoryId(0);
        } else {
            $this->setCategoryId($v->getId());
        }

        $this->aCategory = $v;

        // Add binding for other direction of this n:n relationship.
        // If this object has already been added to the ChildCategory object, it will not be re-added.
        if ($v !== null) {
            $v->addArticle($this);
        }


        return $this;
    }


    /**
     * Get the associated ChildCategory object
     *
     * @param  ConnectionInterface $con Optional Connection object.
     * @return ChildCategory The associated ChildCategory object.
     * @throws PropelException
     */
    public function getCategory(ConnectionInterface $con = null)
    {
        if ($this->aCategory === null && ($this->category_id !== null)) {
            $this->aCategory = ChildCategoryQuery::create()->findPk($this->category_id, $con);
            /* The following can be used additionally to
                guarantee the related object contains a reference
                to this object.  This level of coupling may, however, be
                undesirable since it could result in an only partially populated collection
                in the referenced object.
                $this->aCategory->addArticles($this);
             */
        }

        return $this->aCategory;
    }


    /**
     * Initializes a collection based on the name of a relation.
     * Avoids crafting an 'init[$relationName]s' method name
     * that wouldn't work when StandardEnglishPluralizer is used.
     *
     * @param      string $relationName The name of the relation to initialize
     * @return void
     */
    public function initRelation($relationName)
    {
        if ('ArticleTag' == $relationName) {
            return $this->initArticleTags();
        }
        if ('VoteArticleForeign' == $relationName) {
            return $this->initVoteArticleForeigns();
        }
    }

    /**
     * Clears out the collArticleTags collection
     *
     * This does not modify the database; however, it will remove any associated objects, causing
     * them to be refetched by subsequent calls to accessor method.
     *
     * @return void
     * @see        addArticleTags()
     */
    public function clearArticleTags()
    {
        $this->collArticleTags = null; // important to set this to NULL since that means it is uninitialized
    }

    /**
     * Reset is the collArticleTags collection loaded partially.
     */
    public function resetPartialArticleTags($v = true)
    {
        $this->collArticleTagsPartial = $v;
    }

    /**
     * Initializes the collArticleTags collection.
     *
     * By default this just sets the collArticleTags collection to an empty array (like clearcollArticleTags());
     * however, you may wish to override this method in your stub class to provide setting appropriate
     * to your application -- for example, setting the initial array to the values stored in database.
     *
     * @param      boolean $overrideExisting If set to true, the method call initializes
     *                                        the collection even if it is not empty
     *
     * @return void
     */
    public function initArticleTags($overrideExisting = true)
    {
        if (null !== $this->collArticleTags && !$overrideExisting) {
            return;
        }
        $this->collArticleTags = new ObjectCollection();
        $this->collArticleTags->setModel('\ArticleTag');
    }

    /**
     * Gets an array of ChildArticleTag objects which contain a foreign key that references this object.
     *
     * If the $criteria is not null, it is used to always fetch the results from the database.
     * Otherwise the results are fetched from the database the first time, then cached.
     * Next time the same method is called without $criteria, the cached collection is returned.
     * If this ChildArticle is new, it will return
     * an empty collection or the current collection; the criteria is ignored on a new object.
     *
     * @param      Criteria $criteria optional Criteria object to narrow the query
     * @param      ConnectionInterface $con optional connection object
     * @return ObjectCollection|ChildArticleTag[] List of ChildArticleTag objects
     * @throws PropelException
     */
    public function getArticleTags(Criteria $criteria = null, ConnectionInterface $con = null)
    {
        $partial = $this->collArticleTagsPartial && !$this->isNew();
        if (null === $this->collArticleTags || null !== $criteria  || $partial) {
            if ($this->isNew() && null === $this->collArticleTags) {
                // return empty collection
                $this->initArticleTags();
            } else {
                $collArticleTags = ChildArticleTagQuery::create(null, $criteria)
                    ->filterByArticle($this)
                    ->find($con);

                if (null !== $criteria) {
                    if (false !== $this->collArticleTagsPartial && count($collArticleTags)) {
                        $this->initArticleTags(false);

                        foreach ($collArticleTags as $obj) {
                            if (false == $this->collArticleTags->contains($obj)) {
                                $this->collArticleTags->append($obj);
                            }
                        }

                        $this->collArticleTagsPartial = true;
                    }

                    return $collArticleTags;
                }

                if ($partial && $this->collArticleTags) {
                    foreach ($this->collArticleTags as $obj) {
                        if ($obj->isNew()) {
                            $collArticleTags[] = $obj;
                        }
                    }
                }

                $this->collArticleTags = $collArticleTags;
                $this->collArticleTagsPartial = false;
            }
        }

        return $this->collArticleTags;
    }

    /**
     * Sets a collection of ChildArticleTag objects related by a one-to-many relationship
     * to the current object.
     * It will also schedule objects for deletion based on a diff between old objects (aka persisted)
     * and new objects from the given Propel collection.
     *
     * @param      Collection $articleTags A Propel collection.
     * @param      ConnectionInterface $con Optional connection object
     * @return $this|ChildArticle The current object (for fluent API support)
     */
    public function setArticleTags(Collection $articleTags, ConnectionInterface $con = null)
    {
        /** @var ChildArticleTag[] $articleTagsToDelete */
        $articleTagsToDelete = $this->getArticleTags(new Criteria(), $con)->diff($articleTags);


        //since at least one column in the foreign key is at the same time a PK
        //we can not just set a PK to NULL in the lines below. We have to store
        //a backup of all values, so we are able to manipulate these items based on the onDelete value later.
        $this->articleTagsScheduledForDeletion = clone $articleTagsToDelete;

        foreach ($articleTagsToDelete as $articleTagRemoved) {
            $articleTagRemoved->setArticle(null);
        }

        $this->collArticleTags = null;
        foreach ($articleTags as $articleTag) {
            $this->addArticleTag($articleTag);
        }

        $this->collArticleTags = $articleTags;
        $this->collArticleTagsPartial = false;

        return $this;
    }

    /**
     * Returns the number of related ArticleTag objects.
     *
     * @param      Criteria $criteria
     * @param      boolean $distinct
     * @param      ConnectionInterface $con
     * @return int             Count of related ArticleTag objects.
     * @throws PropelException
     */
    public function countArticleTags(Criteria $criteria = null, $distinct = false, ConnectionInterface $con = null)
    {
        $partial = $this->collArticleTagsPartial && !$this->isNew();
        if (null === $this->collArticleTags || null !== $criteria || $partial) {
            if ($this->isNew() && null === $this->collArticleTags) {
                return 0;
            }

            if ($partial && !$criteria) {
                return count($this->getArticleTags());
            }

            $query = ChildArticleTagQuery::create(null, $criteria);
            if ($distinct) {
                $query->distinct();
            }

            return $query
                ->filterByArticle($this)
                ->count($con);
        }

        return count($this->collArticleTags);
    }

    /**
     * Method called to associate a ChildArticleTag object to this object
     * through the ChildArticleTag foreign key attribute.
     *
     * @param  ChildArticleTag $l ChildArticleTag
     * @return $this|\Article The current object (for fluent API support)
     */
    public function addArticleTag(ChildArticleTag $l)
    {
        if ($this->collArticleTags === null) {
            $this->initArticleTags();
            $this->collArticleTagsPartial = true;
        }

        if (!$this->collArticleTags->contains($l)) {
            $this->doAddArticleTag($l);
        }

        return $this;
    }

    /**
     * @param ChildArticleTag $articleTag The ChildArticleTag object to add.
     */
    protected function doAddArticleTag(ChildArticleTag $articleTag)
    {
        $this->collArticleTags[]= $articleTag;
        $articleTag->setArticle($this);
    }

    /**
     * @param  ChildArticleTag $articleTag The ChildArticleTag object to remove.
     * @return $this|ChildArticle The current object (for fluent API support)
     */
    public function removeArticleTag(ChildArticleTag $articleTag)
    {
        if ($this->getArticleTags()->contains($articleTag)) {
            $pos = $this->collArticleTags->search($articleTag);
            $this->collArticleTags->remove($pos);
            if (null === $this->articleTagsScheduledForDeletion) {
                $this->articleTagsScheduledForDeletion = clone $this->collArticleTags;
                $this->articleTagsScheduledForDeletion->clear();
            }
            $this->articleTagsScheduledForDeletion[]= clone $articleTag;
            $articleTag->setArticle(null);
        }

        return $this;
    }


    /**
     * If this collection has already been initialized with
     * an identical criteria, it returns the collection.
     * Otherwise if this Article is new, it will return
     * an empty collection; or if this Article has previously
     * been saved, it will retrieve related ArticleTags from storage.
     *
     * This method is protected by default in order to keep the public
     * api reasonable.  You can provide public methods for those you
     * actually need in Article.
     *
     * @param      Criteria $criteria optional Criteria object to narrow the query
     * @param      ConnectionInterface $con optional connection object
     * @param      string $joinBehavior optional join type to use (defaults to Criteria::LEFT_JOIN)
     * @return ObjectCollection|ChildArticleTag[] List of ChildArticleTag objects
     */
    public function getArticleTagsJoinTag(Criteria $criteria = null, ConnectionInterface $con = null, $joinBehavior = Criteria::LEFT_JOIN)
    {
        $query = ChildArticleTagQuery::create(null, $criteria);
        $query->joinWith('Tag', $joinBehavior);

        return $this->getArticleTags($query, $con);
    }

    /**
     * Gets a single ChildView object, which is related to this object by a one-to-one relationship.
     *
     * @param  ConnectionInterface $con optional connection object
     * @return ChildView
     * @throws PropelException
     */
    public function getviewArticleForeign(ConnectionInterface $con = null)
    {

        if ($this->singleviewArticleForeign === null && !$this->isNew()) {
            $this->singleviewArticleForeign = ChildViewQuery::create()->findPk($this->getPrimaryKey(), $con);
        }

        return $this->singleviewArticleForeign;
    }

    /**
     * Sets a single ChildView object as related to this object by a one-to-one relationship.
     *
     * @param  ChildView $v ChildView
     * @return $this|\Article The current object (for fluent API support)
     * @throws PropelException
     */
    public function setviewArticleForeign(ChildView $v = null)
    {
        $this->singleviewArticleForeign = $v;

        // Make sure that that the passed-in ChildView isn't already associated with this object
        if ($v !== null && $v->getViewArticle(null, false) === null) {
            $v->setViewArticle($this);
        }

        return $this;
    }

    /**
     * Clears out the collVoteArticleForeigns collection
     *
     * This does not modify the database; however, it will remove any associated objects, causing
     * them to be refetched by subsequent calls to accessor method.
     *
     * @return void
     * @see        addVoteArticleForeigns()
     */
    public function clearVoteArticleForeigns()
    {
        $this->collVoteArticleForeigns = null; // important to set this to NULL since that means it is uninitialized
    }

    /**
     * Reset is the collVoteArticleForeigns collection loaded partially.
     */
    public function resetPartialVoteArticleForeigns($v = true)
    {
        $this->collVoteArticleForeignsPartial = $v;
    }

    /**
     * Initializes the collVoteArticleForeigns collection.
     *
     * By default this just sets the collVoteArticleForeigns collection to an empty array (like clearcollVoteArticleForeigns());
     * however, you may wish to override this method in your stub class to provide setting appropriate
     * to your application -- for example, setting the initial array to the values stored in database.
     *
     * @param      boolean $overrideExisting If set to true, the method call initializes
     *                                        the collection even if it is not empty
     *
     * @return void
     */
    public function initVoteArticleForeigns($overrideExisting = true)
    {
        if (null !== $this->collVoteArticleForeigns && !$overrideExisting) {
            return;
        }
        $this->collVoteArticleForeigns = new ObjectCollection();
        $this->collVoteArticleForeigns->setModel('\Vote');
    }

    /**
     * Gets an array of ChildVote objects which contain a foreign key that references this object.
     *
     * If the $criteria is not null, it is used to always fetch the results from the database.
     * Otherwise the results are fetched from the database the first time, then cached.
     * Next time the same method is called without $criteria, the cached collection is returned.
     * If this ChildArticle is new, it will return
     * an empty collection or the current collection; the criteria is ignored on a new object.
     *
     * @param      Criteria $criteria optional Criteria object to narrow the query
     * @param      ConnectionInterface $con optional connection object
     * @return ObjectCollection|ChildVote[] List of ChildVote objects
     * @throws PropelException
     */
    public function getVoteArticleForeigns(Criteria $criteria = null, ConnectionInterface $con = null)
    {
        $partial = $this->collVoteArticleForeignsPartial && !$this->isNew();
        if (null === $this->collVoteArticleForeigns || null !== $criteria  || $partial) {
            if ($this->isNew() && null === $this->collVoteArticleForeigns) {
                // return empty collection
                $this->initVoteArticleForeigns();
            } else {
                $collVoteArticleForeigns = ChildVoteQuery::create(null, $criteria)
                    ->filterByVoteArticle($this)
                    ->find($con);

                if (null !== $criteria) {
                    if (false !== $this->collVoteArticleForeignsPartial && count($collVoteArticleForeigns)) {
                        $this->initVoteArticleForeigns(false);

                        foreach ($collVoteArticleForeigns as $obj) {
                            if (false == $this->collVoteArticleForeigns->contains($obj)) {
                                $this->collVoteArticleForeigns->append($obj);
                            }
                        }

                        $this->collVoteArticleForeignsPartial = true;
                    }

                    return $collVoteArticleForeigns;
                }

                if ($partial && $this->collVoteArticleForeigns) {
                    foreach ($this->collVoteArticleForeigns as $obj) {
                        if ($obj->isNew()) {
                            $collVoteArticleForeigns[] = $obj;
                        }
                    }
                }

                $this->collVoteArticleForeigns = $collVoteArticleForeigns;
                $this->collVoteArticleForeignsPartial = false;
            }
        }

        return $this->collVoteArticleForeigns;
    }

    /**
     * Sets a collection of ChildVote objects related by a one-to-many relationship
     * to the current object.
     * It will also schedule objects for deletion based on a diff between old objects (aka persisted)
     * and new objects from the given Propel collection.
     *
     * @param      Collection $voteArticleForeigns A Propel collection.
     * @param      ConnectionInterface $con Optional connection object
     * @return $this|ChildArticle The current object (for fluent API support)
     */
    public function setVoteArticleForeigns(Collection $voteArticleForeigns, ConnectionInterface $con = null)
    {
        /** @var ChildVote[] $voteArticleForeignsToDelete */
        $voteArticleForeignsToDelete = $this->getVoteArticleForeigns(new Criteria(), $con)->diff($voteArticleForeigns);


        //since at least one column in the foreign key is at the same time a PK
        //we can not just set a PK to NULL in the lines below. We have to store
        //a backup of all values, so we are able to manipulate these items based on the onDelete value later.
        $this->voteArticleForeignsScheduledForDeletion = clone $voteArticleForeignsToDelete;

        foreach ($voteArticleForeignsToDelete as $voteArticleForeignRemoved) {
            $voteArticleForeignRemoved->setVoteArticle(null);
        }

        $this->collVoteArticleForeigns = null;
        foreach ($voteArticleForeigns as $voteArticleForeign) {
            $this->addVoteArticleForeign($voteArticleForeign);
        }

        $this->collVoteArticleForeigns = $voteArticleForeigns;
        $this->collVoteArticleForeignsPartial = false;

        return $this;
    }

    /**
     * Returns the number of related Vote objects.
     *
     * @param      Criteria $criteria
     * @param      boolean $distinct
     * @param      ConnectionInterface $con
     * @return int             Count of related Vote objects.
     * @throws PropelException
     */
    public function countVoteArticleForeigns(Criteria $criteria = null, $distinct = false, ConnectionInterface $con = null)
    {
        $partial = $this->collVoteArticleForeignsPartial && !$this->isNew();
        if (null === $this->collVoteArticleForeigns || null !== $criteria || $partial) {
            if ($this->isNew() && null === $this->collVoteArticleForeigns) {
                return 0;
            }

            if ($partial && !$criteria) {
                return count($this->getVoteArticleForeigns());
            }

            $query = ChildVoteQuery::create(null, $criteria);
            if ($distinct) {
                $query->distinct();
            }

            return $query
                ->filterByVoteArticle($this)
                ->count($con);
        }

        return count($this->collVoteArticleForeigns);
    }

    /**
     * Method called to associate a ChildVote object to this object
     * through the ChildVote foreign key attribute.
     *
     * @param  ChildVote $l ChildVote
     * @return $this|\Article The current object (for fluent API support)
     */
    public function addVoteArticleForeign(ChildVote $l)
    {
        if ($this->collVoteArticleForeigns === null) {
            $this->initVoteArticleForeigns();
            $this->collVoteArticleForeignsPartial = true;
        }

        if (!$this->collVoteArticleForeigns->contains($l)) {
            $this->doAddVoteArticleForeign($l);
        }

        return $this;
    }

    /**
     * @param ChildVote $voteArticleForeign The ChildVote object to add.
     */
    protected function doAddVoteArticleForeign(ChildVote $voteArticleForeign)
    {
        $this->collVoteArticleForeigns[]= $voteArticleForeign;
        $voteArticleForeign->setVoteArticle($this);
    }

    /**
     * @param  ChildVote $voteArticleForeign The ChildVote object to remove.
     * @return $this|ChildArticle The current object (for fluent API support)
     */
    public function removeVoteArticleForeign(ChildVote $voteArticleForeign)
    {
        if ($this->getVoteArticleForeigns()->contains($voteArticleForeign)) {
            $pos = $this->collVoteArticleForeigns->search($voteArticleForeign);
            $this->collVoteArticleForeigns->remove($pos);
            if (null === $this->voteArticleForeignsScheduledForDeletion) {
                $this->voteArticleForeignsScheduledForDeletion = clone $this->collVoteArticleForeigns;
                $this->voteArticleForeignsScheduledForDeletion->clear();
            }
            $this->voteArticleForeignsScheduledForDeletion[]= clone $voteArticleForeign;
            $voteArticleForeign->setVoteArticle(null);
        }

        return $this;
    }


    /**
     * If this collection has already been initialized with
     * an identical criteria, it returns the collection.
     * Otherwise if this Article is new, it will return
     * an empty collection; or if this Article has previously
     * been saved, it will retrieve related VoteArticleForeigns from storage.
     *
     * This method is protected by default in order to keep the public
     * api reasonable.  You can provide public methods for those you
     * actually need in Article.
     *
     * @param      Criteria $criteria optional Criteria object to narrow the query
     * @param      ConnectionInterface $con optional connection object
     * @param      string $joinBehavior optional join type to use (defaults to Criteria::LEFT_JOIN)
     * @return ObjectCollection|ChildVote[] List of ChildVote objects
     */
    public function getVoteArticleForeignsJoinView(Criteria $criteria = null, ConnectionInterface $con = null, $joinBehavior = Criteria::LEFT_JOIN)
    {
        $query = ChildVoteQuery::create(null, $criteria);
        $query->joinWith('View', $joinBehavior);

        return $this->getVoteArticleForeigns($query, $con);
    }

    /**
     * Clears out the collTags collection
     *
     * This does not modify the database; however, it will remove any associated objects, causing
     * them to be refetched by subsequent calls to accessor method.
     *
     * @return void
     * @see        addTags()
     */
    public function clearTags()
    {
        $this->collTags = null; // important to set this to NULL since that means it is uninitialized
    }

    /**
     * Initializes the collTags crossRef collection.
     *
     * By default this just sets the collTags collection to an empty collection (like clearTags());
     * however, you may wish to override this method in your stub class to provide setting appropriate
     * to your application -- for example, setting the initial array to the values stored in database.
     *
     * @return void
     */
    public function initTags()
    {
        $this->collTags = new ObjectCollection();
        $this->collTagsPartial = true;

        $this->collTags->setModel('\Tag');
    }

    /**
     * Checks if the collTags collection is loaded.
     *
     * @return bool
     */
    public function isTagsLoaded()
    {
        return null !== $this->collTags;
    }

    /**
     * Gets a collection of ChildTag objects related by a many-to-many relationship
     * to the current object by way of the article_tag cross-reference table.
     *
     * If the $criteria is not null, it is used to always fetch the results from the database.
     * Otherwise the results are fetched from the database the first time, then cached.
     * Next time the same method is called without $criteria, the cached collection is returned.
     * If this ChildArticle is new, it will return
     * an empty collection or the current collection; the criteria is ignored on a new object.
     *
     * @param      Criteria $criteria Optional query object to filter the query
     * @param      ConnectionInterface $con Optional connection object
     *
     * @return ObjectCollection|ChildTag[] List of ChildTag objects
     */
    public function getTags(Criteria $criteria = null, ConnectionInterface $con = null)
    {
        $partial = $this->collTagsPartial && !$this->isNew();
        if (null === $this->collTags || null !== $criteria || $partial) {
            if ($this->isNew()) {
                // return empty collection
                if (null === $this->collTags) {
                    $this->initTags();
                }
            } else {

                $query = ChildTagQuery::create(null, $criteria)
                    ->filterByArticle($this);
                $collTags = $query->find($con);
                if (null !== $criteria) {
                    return $collTags;
                }

                if ($partial && $this->collTags) {
                    //make sure that already added objects gets added to the list of the database.
                    foreach ($this->collTags as $obj) {
                        if (!$collTags->contains($obj)) {
                            $collTags[] = $obj;
                        }
                    }
                }

                $this->collTags = $collTags;
                $this->collTagsPartial = false;
            }
        }

        return $this->collTags;
    }

    /**
     * Sets a collection of Tag objects related by a many-to-many relationship
     * to the current object by way of the article_tag cross-reference table.
     * It will also schedule objects for deletion based on a diff between old objects (aka persisted)
     * and new objects from the given Propel collection.
     *
     * @param  Collection $tags A Propel collection.
     * @param  ConnectionInterface $con Optional connection object
     * @return $this|ChildArticle The current object (for fluent API support)
     */
    public function setTags(Collection $tags, ConnectionInterface $con = null)
    {
        $this->clearTags();
        $currentTags = $this->getTags();

        $tagsScheduledForDeletion = $currentTags->diff($tags);

        foreach ($tagsScheduledForDeletion as $toDelete) {
            $this->removeTag($toDelete);
        }

        foreach ($tags as $tag) {
            if (!$currentTags->contains($tag)) {
                $this->doAddTag($tag);
            }
        }

        $this->collTagsPartial = false;
        $this->collTags = $tags;

        return $this;
    }

    /**
     * Gets the number of Tag objects related by a many-to-many relationship
     * to the current object by way of the article_tag cross-reference table.
     *
     * @param      Criteria $criteria Optional query object to filter the query
     * @param      boolean $distinct Set to true to force count distinct
     * @param      ConnectionInterface $con Optional connection object
     *
     * @return int the number of related Tag objects
     */
    public function countTags(Criteria $criteria = null, $distinct = false, ConnectionInterface $con = null)
    {
        $partial = $this->collTagsPartial && !$this->isNew();
        if (null === $this->collTags || null !== $criteria || $partial) {
            if ($this->isNew() && null === $this->collTags) {
                return 0;
            } else {

                if ($partial && !$criteria) {
                    return count($this->getTags());
                }

                $query = ChildTagQuery::create(null, $criteria);
                if ($distinct) {
                    $query->distinct();
                }

                return $query
                    ->filterByArticle($this)
                    ->count($con);
            }
        } else {
            return count($this->collTags);
        }
    }

    /**
     * Associate a ChildTag to this object
     * through the article_tag cross reference table.
     *
     * @param ChildTag $tag
     * @return ChildArticle The current object (for fluent API support)
     */
    public function addTag(ChildTag $tag)
    {
        if ($this->collTags === null) {
            $this->initTags();
        }

        if (!$this->getTags()->contains($tag)) {
            // only add it if the **same** object is not already associated
            $this->collTags->push($tag);
            $this->doAddTag($tag);
        }

        return $this;
    }

    /**
     *
     * @param ChildTag $tag
     */
    protected function doAddTag(ChildTag $tag)
    {
        $articleTag = new ChildArticleTag();

        $articleTag->setTag($tag);

        $articleTag->setArticle($this);

        $this->addArticleTag($articleTag);

        // set the back reference to this object directly as using provided method either results
        // in endless loop or in multiple relations
        if (!$tag->isArticlesLoaded()) {
            $tag->initArticles();
            $tag->getArticles()->push($this);
        } elseif (!$tag->getArticles()->contains($this)) {
            $tag->getArticles()->push($this);
        }

    }

    /**
     * Remove tag of this object
     * through the article_tag cross reference table.
     *
     * @param ChildTag $tag
     * @return ChildArticle The current object (for fluent API support)
     */
    public function removeTag(ChildTag $tag)
    {
        if ($this->getTags()->contains($tag)) { $articleTag = new ChildArticleTag();

            $articleTag->setTag($tag);
            if ($tag->isArticlesLoaded()) {
                //remove the back reference if available
                $tag->getArticles()->removeObject($this);
            }

            $articleTag->setArticle($this);
            $this->removeArticleTag(clone $articleTag);
            $articleTag->clear();

            $this->collTags->remove($this->collTags->search($tag));

            if (null === $this->tagsScheduledForDeletion) {
                $this->tagsScheduledForDeletion = clone $this->collTags;
                $this->tagsScheduledForDeletion->clear();
            }

            $this->tagsScheduledForDeletion->push($tag);
        }


        return $this;
    }

    /**
     * Clears out the collViews collection
     *
     * This does not modify the database; however, it will remove any associated objects, causing
     * them to be refetched by subsequent calls to accessor method.
     *
     * @return void
     * @see        addViews()
     */
    public function clearViews()
    {
        $this->collViews = null; // important to set this to NULL since that means it is uninitialized
    }

    /**
     * Initializes the collViews crossRef collection.
     *
     * By default this just sets the collViews collection to an empty collection (like clearViews());
     * however, you may wish to override this method in your stub class to provide setting appropriate
     * to your application -- for example, setting the initial array to the values stored in database.
     *
     * @return void
     */
    public function initViews()
    {
        $this->collViews = new ObjectCollection();
        $this->collViewsPartial = true;

        $this->collViews->setModel('\View');
    }

    /**
     * Checks if the collViews collection is loaded.
     *
     * @return bool
     */
    public function isViewsLoaded()
    {
        return null !== $this->collViews;
    }

    /**
     * Gets a collection of ChildView objects related by a many-to-many relationship
     * to the current object by way of the vote cross-reference table.
     *
     * If the $criteria is not null, it is used to always fetch the results from the database.
     * Otherwise the results are fetched from the database the first time, then cached.
     * Next time the same method is called without $criteria, the cached collection is returned.
     * If this ChildArticle is new, it will return
     * an empty collection or the current collection; the criteria is ignored on a new object.
     *
     * @param      Criteria $criteria Optional query object to filter the query
     * @param      ConnectionInterface $con Optional connection object
     *
     * @return ObjectCollection|ChildView[] List of ChildView objects
     */
    public function getViews(Criteria $criteria = null, ConnectionInterface $con = null)
    {
        $partial = $this->collViewsPartial && !$this->isNew();
        if (null === $this->collViews || null !== $criteria || $partial) {
            if ($this->isNew()) {
                // return empty collection
                if (null === $this->collViews) {
                    $this->initViews();
                }
            } else {

                $query = ChildViewQuery::create(null, $criteria)
                    ->filterByVoteArticle($this);
                $collViews = $query->find($con);
                if (null !== $criteria) {
                    return $collViews;
                }

                if ($partial && $this->collViews) {
                    //make sure that already added objects gets added to the list of the database.
                    foreach ($this->collViews as $obj) {
                        if (!$collViews->contains($obj)) {
                            $collViews[] = $obj;
                        }
                    }
                }

                $this->collViews = $collViews;
                $this->collViewsPartial = false;
            }
        }

        return $this->collViews;
    }

    /**
     * Sets a collection of View objects related by a many-to-many relationship
     * to the current object by way of the vote cross-reference table.
     * It will also schedule objects for deletion based on a diff between old objects (aka persisted)
     * and new objects from the given Propel collection.
     *
     * @param  Collection $views A Propel collection.
     * @param  ConnectionInterface $con Optional connection object
     * @return $this|ChildArticle The current object (for fluent API support)
     */
    public function setViews(Collection $views, ConnectionInterface $con = null)
    {
        $this->clearViews();
        $currentViews = $this->getViews();

        $viewsScheduledForDeletion = $currentViews->diff($views);

        foreach ($viewsScheduledForDeletion as $toDelete) {
            $this->removeView($toDelete);
        }

        foreach ($views as $view) {
            if (!$currentViews->contains($view)) {
                $this->doAddView($view);
            }
        }

        $this->collViewsPartial = false;
        $this->collViews = $views;

        return $this;
    }

    /**
     * Gets the number of View objects related by a many-to-many relationship
     * to the current object by way of the vote cross-reference table.
     *
     * @param      Criteria $criteria Optional query object to filter the query
     * @param      boolean $distinct Set to true to force count distinct
     * @param      ConnectionInterface $con Optional connection object
     *
     * @return int the number of related View objects
     */
    public function countViews(Criteria $criteria = null, $distinct = false, ConnectionInterface $con = null)
    {
        $partial = $this->collViewsPartial && !$this->isNew();
        if (null === $this->collViews || null !== $criteria || $partial) {
            if ($this->isNew() && null === $this->collViews) {
                return 0;
            } else {

                if ($partial && !$criteria) {
                    return count($this->getViews());
                }

                $query = ChildViewQuery::create(null, $criteria);
                if ($distinct) {
                    $query->distinct();
                }

                return $query
                    ->filterByVoteArticle($this)
                    ->count($con);
            }
        } else {
            return count($this->collViews);
        }
    }

    /**
     * Associate a ChildView to this object
     * through the vote cross reference table.
     *
     * @param ChildView $view
     * @return ChildArticle The current object (for fluent API support)
     */
    public function addView(ChildView $view)
    {
        if ($this->collViews === null) {
            $this->initViews();
        }

        if (!$this->getViews()->contains($view)) {
            // only add it if the **same** object is not already associated
            $this->collViews->push($view);
            $this->doAddView($view);
        }

        return $this;
    }

    /**
     *
     * @param ChildView $view
     */
    protected function doAddView(ChildView $view)
    {
        $vote = new ChildVote();

        $vote->setView($view);

        $vote->setVoteArticle($this);

        $this->addVoteArticleForeign($vote);

        // set the back reference to this object directly as using provided method either results
        // in endless loop or in multiple relations
        if (!$view->isVoteArticlesLoaded()) {
            $view->initVoteArticles();
            $view->getVoteArticles()->push($this);
        } elseif (!$view->getVoteArticles()->contains($this)) {
            $view->getVoteArticles()->push($this);
        }

    }

    /**
     * Remove view of this object
     * through the vote cross reference table.
     *
     * @param ChildView $view
     * @return ChildArticle The current object (for fluent API support)
     */
    public function removeView(ChildView $view)
    {
        if ($this->getViews()->contains($view)) { $vote = new ChildVote();

            $vote->setView($view);
            if ($view->isVoteArticlesLoaded()) {
                //remove the back reference if available
                $view->getVoteArticles()->removeObject($this);
            }

            $vote->setVoteArticle($this);
            $this->removeVoteArticleForeign(clone $vote);
            $vote->clear();

            $this->collViews->remove($this->collViews->search($view));

            if (null === $this->viewsScheduledForDeletion) {
                $this->viewsScheduledForDeletion = clone $this->collViews;
                $this->viewsScheduledForDeletion->clear();
            }

            $this->viewsScheduledForDeletion->push($view);
        }


        return $this;
    }

    /**
     * Clears the current object, sets all attributes to their default values and removes
     * outgoing references as well as back-references (from other objects to this one. Results probably in a database
     * change of those foreign objects when you call `save` there).
     */
    public function clear()
    {
        if (null !== $this->aCategory) {
            $this->aCategory->removeArticle($this);
        }
        $this->id = null;
        $this->title = null;
        $this->bodyhtml = null;
        $this->body = null;
        $this->category_id = null;
        $this->image = null;
        $this->draft = null;
        $this->poll_question = null;
        $this->created_at = null;
        $this->updated_at = null;
        $this->slug = null;
        $this->alreadyInSave = false;
        $this->clearAllReferences();
        $this->applyDefaultValues();
        $this->resetModified();
        $this->setNew(true);
        $this->setDeleted(false);
    }

    /**
     * Resets all references and back-references to other model objects or collections of model objects.
     *
     * This method is used to reset all php object references (not the actual reference in the database).
     * Necessary for object serialisation.
     *
     * @param      boolean $deep Whether to also clear the references on all referrer objects.
     */
    public function clearAllReferences($deep = false)
    {
        if ($deep) {
            if ($this->collArticleTags) {
                foreach ($this->collArticleTags as $o) {
                    $o->clearAllReferences($deep);
                }
            }
            if ($this->singleviewArticleForeign) {
                $this->singleviewArticleForeign->clearAllReferences($deep);
            }
            if ($this->collVoteArticleForeigns) {
                foreach ($this->collVoteArticleForeigns as $o) {
                    $o->clearAllReferences($deep);
                }
            }
            if ($this->collTags) {
                foreach ($this->collTags as $o) {
                    $o->clearAllReferences($deep);
                }
            }
            if ($this->collViews) {
                foreach ($this->collViews as $o) {
                    $o->clearAllReferences($deep);
                }
            }
        } // if ($deep)

        $this->collArticleTags = null;
        $this->singleviewArticleForeign = null;
        $this->collVoteArticleForeigns = null;
        $this->collTags = null;
        $this->collViews = null;
        $this->aCategory = null;
    }

    /**
     * Return the string representation of this object
     *
     * @return string The value of the 'title' column
     */
    public function __toString()
    {
        return (string) $this->getTitle();
    }

    // timestampable behavior

    /**
     * Mark the current object so that the update date doesn't get updated during next save
     *
     * @return     $this|ChildArticle The current object (for fluent API support)
     */
    public function keepUpdateDateUnchanged()
    {
        $this->modifiedColumns[ArticleTableMap::COL_UPDATED_AT] = true;

        return $this;
    }

    // sluggable behavior

    /**
     * Create a unique slug based on the object
     *
     * @return string The object slug
     */
    protected function createSlug()
    {
        $slug = $this->createRawSlug();
        $slug = $this->limitSlugSize($slug);
        $slug = $this->makeSlugUnique($slug);

        return $slug;
    }

    /**
     * Create the slug from the appropriate columns
     *
     * @return string
     */
    protected function createRawSlug()
    {
        return $this->cleanupSlugPart($this->__toString());
    }

    /**
     * Cleanup a string to make a slug of it
     * Removes special characters, replaces blanks with a separator, and trim it
     *
     * @param     string $slug        the text to slugify
     * @param     string $replacement the separator used by slug
     * @return    string               the slugified text
     */
    protected static function cleanupSlugPart($slug, $replacement = '-')
    {
        // transliterate
        if (function_exists('iconv')) {
            $slug = iconv('utf-8', 'us-ascii//TRANSLIT', $slug);
        }

        // lowercase
        if (function_exists('mb_strtolower')) {
            $slug = mb_strtolower($slug);
        } else {
            $slug = strtolower($slug);
        }

        // remove accents resulting from OSX's iconv
        $slug = str_replace(array('\'', '`', '^'), '', $slug);

        // replace non letter or digits with separator
        $slug = preg_replace('/\W+/', $replacement, $slug);

        // trim
        $slug = trim($slug, $replacement);

        if (empty($slug)) {
            return 'n-a';
        }

        return $slug;
    }


    /**
     * Make sure the slug is short enough to accommodate the column size
     *
     * @param    string $slug            the slug to check
     *
     * @return string                        the truncated slug
     */
    protected static function limitSlugSize($slug, $incrementReservedSpace = 3)
    {
        // check length, as suffix could put it over maximum
        if (strlen($slug) > (255 - $incrementReservedSpace)) {
            $slug = substr($slug, 0, 255 - $incrementReservedSpace);
        }

        return $slug;
    }


    /**
     * Get the slug, ensuring its uniqueness
     *
     * @param    string $slug            the slug to check
     * @param    string $separator       the separator used by slug
     * @param    int    $alreadyExists   false for the first try, true for the second, and take the high count + 1
     * @return   string                   the unique slug
     */
    protected function makeSlugUnique($slug, $separator = '-', $alreadyExists = false)
    {
        if (!$alreadyExists) {
            $slug2 = $slug;
        } else {
            $slug2 = $slug . $separator;

            $count = \ArticleQuery::create()
                ->filterBySlug($this->getSlug())
                ->filterByPrimaryKey($this->getPrimaryKey())
            ->count();

            if (1 == $count) {
                return $this->getSlug();
            }
        }

        $adapter = \Propel\Runtime\Propel::getServiceContainer()->getAdapter('blog');
        $col = 'q.Slug';
        $compare = $alreadyExists ? $adapter->compareRegex($col, '?') : sprintf('%s = ?', $col);

        $query = \ArticleQuery::create('q')
            ->where($compare, $alreadyExists ? '^' . $slug2 . '[0-9]+$' : $slug2)
            ->prune($this)
        ;

        if (!$alreadyExists) {
            $count = $query->count();
            if ($count > 0) {
                return $this->makeSlugUnique($slug, $separator, true);
            }

            return $slug2;
        }

        $adapter = \Propel\Runtime\Propel::getServiceContainer()->getAdapter('blog');
        // Already exists
        $object = $query
            ->addDescendingOrderByColumn($adapter->strLength('slug'))
            ->addDescendingOrderByColumn('slug')
        ->findOne();

        // First duplicate slug
        if (null == $object) {
            return $slug2 . '1';
        }

        $slugNum = substr($object->getSlug(), strlen($slug) + 1);
        if (0 == $slugNum[0]) {
            $slugNum[0] = 1;
        }

        return $slug2 . ($slugNum + 1);
    }

    /**
     * Code to be run before persisting the object
     * @param  ConnectionInterface $con
     * @return boolean
     */
    public function preSave(ConnectionInterface $con = null)
    {
        return true;
    }

    /**
     * Code to be run after persisting the object
     * @param ConnectionInterface $con
     */
    public function postSave(ConnectionInterface $con = null)
    {

    }

    /**
     * Code to be run before inserting to database
     * @param  ConnectionInterface $con
     * @return boolean
     */
    public function preInsert(ConnectionInterface $con = null)
    {
        return true;
    }

    /**
     * Code to be run after inserting to database
     * @param ConnectionInterface $con
     */
    public function postInsert(ConnectionInterface $con = null)
    {

    }

    /**
     * Code to be run before updating the object in database
     * @param  ConnectionInterface $con
     * @return boolean
     */
    public function preUpdate(ConnectionInterface $con = null)
    {
        return true;
    }

    /**
     * Code to be run after updating the object in database
     * @param ConnectionInterface $con
     */
    public function postUpdate(ConnectionInterface $con = null)
    {

    }

    /**
     * Code to be run before deleting the object in database
     * @param  ConnectionInterface $con
     * @return boolean
     */
    public function preDelete(ConnectionInterface $con = null)
    {
        return true;
    }

    /**
     * Code to be run after deleting the object in database
     * @param ConnectionInterface $con
     */
    public function postDelete(ConnectionInterface $con = null)
    {

    }


    /**
     * Derived method to catches calls to undefined methods.
     *
     * Provides magic import/export method support (fromXML()/toXML(), fromYAML()/toYAML(), etc.).
     * Allows to define default __call() behavior if you overwrite __call()
     *
     * @param string $name
     * @param mixed  $params
     *
     * @return array|string
     */
    public function __call($name, $params)
    {
        if (0 === strpos($name, 'get')) {
            $virtualColumn = substr($name, 3);
            if ($this->hasVirtualColumn($virtualColumn)) {
                return $this->getVirtualColumn($virtualColumn);
            }

            $virtualColumn = lcfirst($virtualColumn);
            if ($this->hasVirtualColumn($virtualColumn)) {
                return $this->getVirtualColumn($virtualColumn);
            }
        }

        if (0 === strpos($name, 'from')) {
            $format = substr($name, 4);

            return $this->importFrom($format, reset($params));
        }

        if (0 === strpos($name, 'to')) {
            $format = substr($name, 2);
            $includeLazyLoadColumns = isset($params[0]) ? $params[0] : true;

            return $this->exportTo($format, $includeLazyLoadColumns);
        }

        throw new BadMethodCallException(sprintf('Call to undefined method: %s.', $name));
    }

}
