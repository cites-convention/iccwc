<?php

namespace Drupal\iccwc\Plugin\BootstrapStyles\StylesGroup;

use Drupal\bootstrap_styles\StylesGroup\StylesGroupPluginBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Bootstrap Styles category title.
 *
 * @StylesGroup(
 *   id = "title",
 *   title = @Translation("Title"),
 *   weight = 0,
 *   icon = "bootstrap_styles/images/plugins/typography-icon.svg"
 * )
 */
class Title extends StylesGroupPluginBase {

  /**
   * {@inheritdoc}
   */
  public function buildConfigurationForm(array $form, FormStateInterface $form_state) {
    $form['title'] = [
      '#type' => 'details',
      '#title' => $this->t('Title'),
      '#open' => FALSE,
    ];

    return $form;
  }

}
