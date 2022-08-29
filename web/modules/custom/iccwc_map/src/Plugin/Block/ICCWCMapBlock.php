<?php

namespace Drupal\iccwc_map\Plugin\Block;

use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Language\LanguageInterface;
use Drupal\taxonomy\Entity\Term;
use Drupal\iccwc\Plugin\Block\ICCWCBlockBase;

/**
 * Provides a 'ICCWCMapBlock' Block.
 *
 * @Block(
 *   id = "iccwc_dashboard_map",
 *   admin_label = @Translation("ICCWC Map"),
 *   category = @Translation("ICCWC"),
 * )
 */
class ICCWCMapBlock extends ICCWCBlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {
    $mapDisclaimer = NULL;
    $partiesList = NULL;
    $categoriesList = NULL;
    $iccwcUtils = \Drupal::service('iccwc_map_utils.utils');
    $language = \Drupal::languageManager()->getCurrentLanguage(LanguageInterface::TYPE_CONTENT)->getId();

    if (isset($this->configuration['dataset'])) {
      $term_id = $this->configuration['dataset'];

      $entity = Term::load($term_id);
      if ($entity->hasTranslation($language)) {
        $entity = $entity->getTranslation($language);
      }
      $mapDisclaimer['#markup'] = $entity->getDescription();

      $partiesList = $iccwcUtils->getPartiesMapOverview($entity);
      $categoriesList = $iccwcUtils->getCategoriesMapOverview($entity);
    }

    $regions = $this->configuration['regions'] ?? NULL;
    $options = [
      'africa' => 'Africa',
      'asia' => 'Asia',
      'latin_america' => 'Central and South America and the Caribbean',
      'europe' => 'Europe',
      'north_america' => 'North America',
      'oceania' => 'Oceania',
    ];

    if ($regions != NULL) {
      foreach ($regions as $region) {
        $regionsSelected[$region] = $options[$region];
      }
    }

    $build = [
      '#theme' => 'iccwc_map_overview',
      '#list_of_parties' => [],
      '#categories' => $categoriesList,
      '#attached' => [
        'library' => [
          'iccwc_map/map',
        ],
        'drupalSettings' => [
          'parties' => $partiesList['fill'],
          'coordinates' => $partiesList['coord'],
        ],
      ],
      '#disclaimer' => [
        $mapDisclaimer,
      ],
      '#regions' => $regionsSelected,
    ];

    return $build;
  }

  /**
   * {@inheritdoc}
   */
  public function blockForm($form, FormStateInterface $form_state) {
    $language = \Drupal::languageManager()->getCurrentLanguage(LanguageInterface::TYPE_CONTENT)->getId();
    if (isset($this->configuration['dataset'])) {
      $term_id = $this->configuration['dataset'];

      // Default value for entity_autocomplete needs entities.
      $entity = Term::load($term_id);
      if ($entity->hasTranslation($language)) {
        $entity = $entity->getTranslation($language);
      }
    }

    // Attach extra field to block config form.
    $form['dataset'] = [
      '#type' => 'entity_autocomplete',
      '#description' => $this->t("Select first level term, and only one value."),
      '#selection_handler' => 'default',
      '#target_type' => 'taxonomy_term',
      '#title' => $this->t('Map dataset'),
      '#default_value' => $entity ?? NULL,
      '#selection_settings' => [
        'target_bundles' => ['map_datasets'],
      ],
    ];

    $form['regions'] = [
      '#title' => $this->t('Regions'),
      '#description' => $this->t('Leave empty for no regions.'),
      '#type' => 'select',
      '#multiple' => TRUE,
      '#options' => [
        'africa' => $this->t('Africa'),
        'asia' => $this->t('Asia'),
        'latin_america' => $this->t('Central and South America and the Caribbean'),
        'europe' => $this->t('Europe'),
        'north_america' => $this->t('North America'),
        'oceania' => $this->t('Oceania'),
      ],
      '#default_value' => $this->configuration['regions'] ?? NULL,
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function blockSubmit($form, FormStateInterface $form_state) {
    $this->configuration['dataset'] = $form_state->getValue('dataset');
    $this->configuration['regions'] = $form_state->getValue('regions');
  }

}
