<?php

namespace Drupal\iccwc\Plugin\BootstrapStyles\Style;

use Drupal\bootstrap_styles\Style\StylePluginBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Class ColumnBorder.
 *
 * @Style(
 *   id = "column_border",
 *   title = @Translation("Column Border"),
 *   group_id = "border",
 *   weight = 2
 * )
 */
class ColumnBorder extends StylePluginBase {

  /**
   * {@inheritdoc}
   */
  public function buildStyleFormElements(array &$form, FormStateInterface $form_state, $storage) {
    $form['column_border'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Column border'),
      '#default_value' => !empty($storage['column_border']),
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitStyleFormElements(array $group_elements) {
    return [
      'column_border' => $group_elements['column_border'],
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function build(array $build, array $storage, $theme_wrapper = NULL) {
    if (!empty($storage['column_border'])) {
      $build = $this->addClassesToBuild($build, ['column-border'], $theme_wrapper);
    }
    return $build;
  }

}
