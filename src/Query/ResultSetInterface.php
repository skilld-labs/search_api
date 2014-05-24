<?php

/**
 * @file
 * Contains \Drupal\search_api\Query\QueryResultInterface.
 */

namespace Drupal\search_api\Query;

/**
 * Represents the result set of a search query.
 *
 * The \Traversable implementation should iterate over the returned result
 * items.
 */
interface ResultSetInterface extends \Traversable {

  /**
   * Retrieves the query executed for this search result.
   *
   * @return \Drupal\search_api\Query\QueryInterface
   *   The executed query.
   */
  public function getQuery();

  /**
   * Retrieves the total number of results that were found in this search.
   *
   * @return int|null
   *   The total number of results, if set. NULL otherwise.
   */
  public function getResultCount();

  /**
   * Sets the result count of the search.
   *
   * @param int $result_count
   *   The number of search results, in total.
   *
   * @return self
   *   The invoked object.
   */
  public function setResultCount($result_count);

  /**
   * Retrieves the query result items.
   *
   * @return \Drupal\search_api\Item\ItemInterface[]
   *   The query result items, keyed by item ID.
   */
  public function getResultItems();

  /**
   * Sets the query result items.
   *
   * @param \Drupal\search_api\Item\ItemInterface[] $result_items
   *   The query result items, keyed by item ID.
   *
   * @return self
   *   The invoked object.
   */
  public function setResultItems(array $result_items);

  /**
   * Returns the warnings triggered by the search query.
   *
   * @return array
   *   An array of translated, sanitized warning messages that may be displayed
   *   to the user.
   */
  public function getWarnings();

  /**
   * Sets the warnings triggered by the search query.
   *
   * @param array $warnings
   *   An array of translated, sanitized warning messages that may be displayed
   *   to the user.
   *
   * @return self
   *   The invoked object.
   */
  public function setWarnings(array $warnings);

  /**
   * Adds a warning message that was triggered by the search query.
   *
   * @param string $warning
   *   A translated, sanitized warning message that may be displayed to the
   *   user.
   *
   * @return self
   *   The invoked object.
   */
  public function addWarning($warning);

  /**
   * Returns the ignored search keys, if any.
   *
   * @return array
   *   A numeric array of search keys that were ignored for this search
   *   (e.g., because of being too short or stop words).
   */
  public function getIgnoredSearchKeys();

  /**
   * Sets the ignored search keys of the search query.
   *
   * @param array $ignored_search_keys
   *   An array of search keys (individual words) that were ignored in the
   *   search.
   *
   * @return self
   *   The invoked object.
   */
  public function setIgnoredSearchKeys(array $ignored_search_keys);

  /**
   * Adds an ignored search key for the search query.
   *
   * @param string $ignored_search_key
   *   A single search key (word) that was ignored in the search.
   *
   * @return self
   *   The invoked object.
   */
  public function addIgnoredSearchKey($ignored_search_key);

  /**
   * Determines whether extra data with a specific key is set on this result.
   *
   * @param string $key
   *   The extra data's key.
   *
   * @return bool
   *   TRUE if the data is set, FALSE otherwise.
   */
  public function hasExtraData($key);

  /**
   * Retrieves extra data for this search result.
   *
   * @param string $key
   *   The key of the extra data.
   * @param mixed $default
   *   (optional) The value to return if the data is not set.
   *
   * @return mixed
   *   The data set for that key, or $default if the data is not present.
   */
  public function getExtraData($key, $default = NULL);

  /**
   * Retrieves all extra data set for this search result.
   *
   * @return array
   *   An array mapping extra data keys to their data.
   */
  public function getAllExtraData();

  /**
   * Sets some extra data for this search result.
   *
   * @param string $key
   *   The key for the extra data.
   * @param mixed $data
   *   (optional) The data to set. If NULL, remove the extra data with the given
   *   key instead.
   *
   * @return self
   *   The invoked object.
   */
  public function setExtraData($key, $data = NULL);

}
