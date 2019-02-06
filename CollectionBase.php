<?php
/**
 * Abstract collection class that can be used for different object types in WordPress.
 *
 * @package wpscholar\WordPress
 */

namespace wpscholar\WordPress;

use wpscholar\Collection;

/**
 * Class CollectionBase
 *
 * @package wpscholar\WordPress
 */
abstract class CollectionBase implements \Countable, \IteratorAggregate {

	/**
	 * A collection of IDs
	 *
	 * @var Collection
	 */
	protected $collection;

	/**
	 * Tracks whether or not the collection has been populated.
	 *
	 * @var bool
	 */
	protected $isPopulated = false;

	/**
	 * Default query args
	 *
	 * @var array
	 */
	protected $default_args = [];

	/**
	 * Required query args
	 *
	 * @var array
	 */
	protected $required_args = [];

	/**
	 * Collection constructor.
	 *
	 * @param array|string|null $args WP_Query arguments
	 */
	public function __construct( $args = null ) {

		$this->collection = Collection::make();

		if ( null !== $args ) {
			$this->fetch( $args );
		}
	}

	/**
	 * Gain access to the underlying collection
	 *
	 * @return Collection
	 */
	public function collection() {

		// If iterator isn't set, just do a fetch automatically.
		if ( ! $this->isPopulated ) {
			$this->fetch();
		}

		return $this->collection;
	}

	/**
	 * Count items
	 *
	 * @return int
	 */
	public function count() {
		return $this->collection()->count();
	}

	/**
	 * Fetch items
	 *
	 * @param array|string $args Query arguments
	 */
	abstract public function fetch( $args = [] );

	/**
	 * Populate collection from existing IDs.
	 *
	 * @param array $ids Object IDs
	 */
	public function populate( array $ids ) {
		$this->isPopulated = true;
		$this->collection  = Collection::make( $ids )->map( 'absint' )->filter();
	}

	/**
	 * Get the found IDs
	 *
	 * @return int[]
	 */
	public function ids() {
		return $this->collection->all();
	}

	/**
	 * Get the found objects
	 */
	abstract public function objects();

	/**
	 * Get iterator for collection
	 *
	 * @return \Generator|\Traversable
	 */
	public function getIterator() {
		foreach ( $this->collection() as $id ) {
			yield $this->transform( $id );
		}
	}

	/**
	 * Transform the ID into an object.
	 *
	 * @param int $id
	 *
	 * @return mixed
	 */
	abstract protected function transform( $id );

}
