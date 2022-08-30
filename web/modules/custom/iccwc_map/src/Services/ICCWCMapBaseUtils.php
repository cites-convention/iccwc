<?php

namespace Drupal\iccwc_map\Services;

use Drupal\Core\Entity\EntityRepositoryInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\taxonomy\Entity\Term;
use Drupal\taxonomy\TermInterface;

/**
 * Class ICCWCMapBaseUtils custom functions to be used in block.
 *
 * @package Drupal\iccwc_map
 */
class ICCWCMapBaseUtils {

  /**
   * The entity type manager.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * The entity repository.
   *
   * @var \Drupal\Core\Entity\EntityRepositoryInterface
   */
  protected $entityRepository;

  /**
   * ICCWCMapBaseUtils constructor.
   *
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   *   Entity Type Manager Interface.
   * @param \Drupal\Core\Entity\EntityRepositoryInterface $entity_repository
   *   The entity repository.
   */
  public function __construct(EntityTypeManagerInterface $entity_type_manager, EntityRepositoryInterface $entity_repository) {
    $this->entityTypeManager = $entity_type_manager;
    $this->entityRepository = $entity_repository;
  }

  /**
   * Returns list of countries to fill or mark.
   *
   * @param \Drupal\taxonomy\Entity\Term $parent_term
   *   Taxonomy term parent for which we search data to display on map.
   *
   * @return array
   *   Array of data to be displayed on map.
   *
   * @throws \Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException
   * @throws \Drupal\Component\Plugin\Exception\PluginNotFoundException
   */
  public function getPartiesMapOverview(Term $parent_term) {
    $parties = [];
    $fill = [];
    $coordinates = [];

    $parent_tid = $parent_term->id();
    $depth = 2;
    $load_entities = FALSE;
    $child_terms = $this->entityTypeManager->getStorage('taxonomy_term')->loadTree('map_datasets', $parent_tid, $depth, $load_entities);
    foreach ($child_terms as $child_term) {
      if ($child_term->depth == 1) {
        /** @var \Drupal\taxonomy\TermInterface $term_obj */
        $term_obj = $this->entityTypeManager->getStorage('taxonomy_term')->load($child_term->tid);
        $term_obj = $this->entityRepository->getTranslationFromContext($term_obj);
        $country = $term_obj->get('field_country')->entity;
        if ($country instanceof TermInterface) {
          // Country term.
          $country = $this->entityRepository->getTranslationFromContext($country);
          if (!empty($iso3 = $country->get('field_iso3')->value)) {
            $fill[] = [
              'iso' => $iso3,
              'color' => $this->getParentColor($child_term->parents[0]),
              'description' => $term_obj->get('description')->value,
              'name' => $term_obj->get('name')->value,
              'link' => !$term_obj->get('field_link')->isEmpty()
                ? $term_obj->get('field_link')->first()->getUrl()->toString()
                : NULL,
            ];
          }
        }
        else {
          // Latitude and longitude term.
          $latitude = $term_obj->get('field_latitude')->value;
          $longitude = $term_obj->get('field_longitude')->value;

          if (!empty($latitude) && !empty($longitude)) {
            $coordinates[] = [
              'lat' => $latitude,
              'long' => $longitude,
              'color' => $this->getParentColor($child_term->parents[0]),
              'description' => $term_obj->get('description')->value,
              'name' => $term_obj->get('name')->value,
              'link' => !$term_obj->get('field_link')->isEmpty()
                ? $term_obj->get('field_link')->first()->getUrl()->toString()
                : NULL,
            ];
          }
        }
      }
    }

    $parties['fill'] = $fill;
    $parties['coordinates'] = $coordinates;

    // Taiwan needs to be colored as part of China.
    $id = array_search('CHN', array_column($parties['fill'], 'iso'));
    if ($id != NULL) {
      $parties['fill'][$id]['iso'] = 'TWN';
    }

    return $parties;
  }

  /**
   * Categories to be displayd in template with their colors.
   *
   * @param \Drupal\taxonomy\Entity\Term $parent_term
   *   Term for which we look for data under it.
   *
   * @return array
   *   List of categories for template.
   *
   * @throws \Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException
   * @throws \Drupal\Component\Plugin\Exception\PluginNotFoundException
   */
  public function getCategoriesMapOverview(Term $parent_term) {
    $parent_tid = $parent_term->id();
    $depth = 1;
    $load_entities = FALSE;
    $categories = [];
    $child_terms = $this->entityTypeManager->getStorage('taxonomy_term')->loadTree('map_datasets', $parent_tid, $depth, $load_entities);
    foreach ($child_terms as $id => $child_term) {
      $category = $this->entityTypeManager->getStorage('taxonomy_term')->load($child_term->tid);
      $category = $this->entityRepository->getTranslationFromContext($category);
      $categories[$id]['name'] = $category->get('name')->value;
      $categories[$id]['color'] = $this->getParentColor($category->id());
    }

    return $categories;
  }

  /**
   * Returns color to fill/mark point.
   *
   * @param int $parent_tid
   *   Tid of term to look for color field.
   *
   * @return mixed
   *   Hex value found in field.
   */
  public function getParentColor(int $parent_tid) {
    $term_obj = $this->entityTypeManager->getStorage('taxonomy_term')->load($parent_tid);

    return $term_obj->get('field_color')->value;
  }

}
