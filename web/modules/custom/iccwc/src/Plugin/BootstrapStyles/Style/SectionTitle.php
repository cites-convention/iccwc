<?php

namespace Drupal\iccwc\Plugin\BootstrapStyles\Style;

use Drupal\bootstrap_styles\Style\StylePluginBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Bootstrap Styles setting for section title.
 *
 * @Style(
 *   id = "section_title",
 *   title = @Translation("Section title"),
 *   group_id = "title",
 *   weight = 1
 * )
 */
class SectionTitle extends StylePluginBase {

  /**
   * {@inheritdoc}
   */
  public function buildStyleFormElements(array &$form, FormStateInterface $form_state, $storage) {
    $form['section_title'] = [
      '#type' => 'textfield',
      '#default_value' => $storage['section_title'],
      '#title' => $this->t('Section title'),
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitStyleFormElements(array $group_elements) {
    return [
      'section_title' => $group_elements['section_title'],
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function build(array $build, array $storage, $theme_wrapper = NULL) {
    // phpcs:ignore
    if (!empty($storage['section_title'])) {
      $build['#section_title'] = $this->t($storage['section_title']);
    }
    return $build;
  }

}
