<?php

namespace Drupal\iccwc_map\Services;

use Drupal\Core\Database\Connection;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\taxonomy\Entity\Term;
use Drupal\language\ConfigurableLanguageManager;

/**
 * Class ICCWCMapBaseUtils custom functions to be used in block.
 *
 * @package Drupal\iccwc_map
 */
class ICCWCMapBaseUtils {

  /**
   * Database connection.
   *
   * @var \Drupal\Core\Database\Connection
   */
  private $connection;

  /**
   * The entity type manager.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * The configurable language manager.
   *
   * @var \Drupal\language\ConfigurableLanguageManagerInterface
   */
  protected $languageManager;

  /**
   * ICCWCMapBaseUtils constructor.
   *
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   *   Entity Type Manager Interface.
   * @param \Drupal\Core\Database\Connection $connection
   *   Database connection.
   * @param \Drupal\language\ConfigurableLanguageManager $language_manager
   *   The configurable language manager.
   */
  public function __construct(EntityTypeManagerInterface $entity_type_manager, Connection $connection, ConfigurableLanguageManager $language_manager) {
    $this->connection = $connection;
    $this->entityTypeManager = $entity_type_manager;
    $this->languageManager = $language_manager;
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
    $language = $this->languageManager->getCurrentLanguage()->getId();

    $parent_tid = $parent_term->id();
    $depth = 2;
    $load_entities = FALSE;
    $child_terms = $this->entityTypeManager->getStorage('taxonomy_term')->loadTree('map_datasets', $parent_tid, $depth, $load_entities);
    foreach ($child_terms as $id => $child_term) {
      if ($child_term->depth == 1) {
        $term_obj = $this->entityTypeManager->getStorage('taxonomy_term')->load($child_term->tid);
        if ($term_obj->hasTranslation($language)) {
          $term_obj = $term_obj->getTranslation($language);
        }
        $country_tid = $term_obj->get("field_country")->getValue();
        if (!empty($country_tid)) {
          // Country term.
          $country = $this->entityTypeManager->getStorage('taxonomy_term')->load($country_tid[0]['target_id']);
          if ($country->hasTranslation($language)) {
            $country = $country->getTranslation($language);
          }
          if (!empty($iso3 = $country->get('field_iso3')->value)) {
            $fill[] = [
              'iso' => $iso3,
              'color' => $this->getParentColor($child_term->parents[0]),
              'description' => $term_obj->get('description')->value,
              'name' => $term_obj->get('name')->value,
            ];
          }
        }
        else {
          // Latitude and longitude term.
          $lat = $term_obj->get("field_latitude")->getValue();
          $long = $term_obj->get("field_longitude")->getValue();

          if (!empty($lat) && !empty($long)) {
            $coord[] = [
              'lat' => $lat[0]['value'],
              'long' => $long[0]['value'],
              'color' => $this->getParentColor($child_term->parents[0]),
              'description' => $term_obj->get('description')->value,
              'name' => $term_obj->get('name')->value,
            ];
          }
        }
      }
    }

    $parties['fill'] = $fill;
    $parties['coord'] = $coord;

    // A temporary fix for issue #11129 from minamata.
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
    $language = $this->languageManager->getCurrentLanguage()->getId();
    $parent_tid = $parent_term->id();
    $depth = 1;
    $load_entities = FALSE;
    $categories = [];
    $child_terms = $this->entityTypeManager->getStorage('taxonomy_term')->loadTree('map_datasets', $parent_tid, $depth, $load_entities);
    foreach ($child_terms as $id => $child_term) {
      $categ = $this->entityTypeManager->getStorage('taxonomy_term')->load($child_term->tid);
      if ($categ->hasTranslation($language)) {
        $categ = $categ->getTranslation($language);
      }
      $categories[$id]['name'] = $categ->get('name')->value;
      $categories[$id]['color'] = $this->getParentColor($categ->id());
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
    $language = $this->languageManager->getCurrentLanguage()->getId();
    $term_obj = $this->entityTypeManager->getStorage('taxonomy_term')->load($parent_tid);
    if ($term_obj->hasTranslation($language)) {
      $term_obj = $term_obj->getTranslation($language);
    }

    return $term_obj->get("field_color")->value;
  }

}
