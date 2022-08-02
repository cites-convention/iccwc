<?php

namespace Drupal\iccwc\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Cache\Cache;

/**
 * Provides a 'ICCWC Search form' Block.
 *
 * @Block(
 *   id = "iccwc_search_form",
 *   admin_label = @Translation("ICCWC Search form"),
 *   category = @Translation("ICCWC"),
 * )
 */
class SearchBlock extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {
    $form = \Drupal::formBuilder()->getForm(\Drupal\iccwc\Form\SearchForm::class);

    $form['form_token']['#access'] = FALSE;
    $form['form_id']['#access'] = FALSE;
    $form['form_build_id']['#access'] = FALSE;

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function getCacheMaxAge() {
    return 0;
  }

}
