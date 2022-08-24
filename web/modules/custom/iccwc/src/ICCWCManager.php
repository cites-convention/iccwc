<?php

namespace Drupal\iccwc;

use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Routing\CurrentRouteMatch;

class ICCWCManager {

  /**
   * The ID of the news page.
   */
  const NEWS_PAGE = 5;

  /**
   * The ID of the search page.
   */
  const SEARCH_PAGE = 38;

  /**
   * The current route match.
   *
   * @var \Drupal\Core\Routing\CurrentRouteMatch
   */
  protected $routeMatch;

  /**
   * The entity type manager.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * ICCWCManager constructor.
   *
   * @param \Drupal\Core\Routing\CurrentRouteMatch $route_match
   *   The current route match.
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface
   *   The entity type manager.
   */
  public function __construct(CurrentRouteMatch $route_match, EntityTypeManagerInterface $entity_type_manager) {
    $this->routeMatch = $route_match;
    $this->entityTypeManager = $entity_type_manager;
  }

  /**
   * Check if the current page is the news page.
   *
   * @return bool
   *   True if the current page is the news page.
   */
  public function isNewsPage() {
    return $this->routeMatch->getRawParameter('node') == static::NEWS_PAGE;
  }

  /**
   * Check if the current page is the search page.
   *
   * @return bool
   *   True if the current page is the search page.
   */
  public function isSearchPage() {
    return $this->routeMatch->getRawParameter('node') == static::SEARCH_PAGE;
  }

  /**
   * Get the search page node.
   *
   * @return \Drupal\node\NodeInterface
   *   The search page node.
   *
   * @throws \Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException
   * @throws \Drupal\Component\Plugin\Exception\PluginNotFoundException
   */
  public function getSearchPage() {
    return $this->entityTypeManager->getStorage('node')->load(static::SEARCH_PAGE);
  }

  /**
   * Get the news page node.
   *
   * @return \Drupal\node\NodeInterface
   *   The news page node.
   *
   * @throws \Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException
   * @throws \Drupal\Component\Plugin\Exception\PluginNotFoundException
   */
  public function getNewsPage() {
    return $this->entityTypeManager->getStorage('node')->load(static::NEWS_PAGE);
  }

}
