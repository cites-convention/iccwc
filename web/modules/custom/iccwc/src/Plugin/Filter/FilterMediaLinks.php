<?php

namespace Drupal\iccwc\Plugin\Filter;

use Drupal\filter\FilterProcessResult;
use Drupal\filter\Plugin\FilterBase;

/**
 * Provides a filter to fix linked drupal media.
 *
 * By default, the FilterAutoP filter breaks links containing <drupal-media>.
 * A workaround for this is to wrap these links in divs.
 *
 * @Filter(
 *   id = "marine_media_link",
 *   title = @Translation("Fix media links"),
 *   type = Drupal\filter\Plugin\FilterInterface::TYPE_TRANSFORM_IRREVERSIBLE,
 *   settings = {
 *   },
 *   weight = -50
 * )
 */
class FilterMediaLinks extends FilterBase {

  /**
   * {@inheritdoc}
   */
  public function process($text, $langcode) {
    // Find all links containing drupal media and wrap them in divs.
    preg_match_all('/(<a.*?href=".*?".*?>.*?<\/a>)/s', $text, $matches);
    if (empty($matches[1])) {
      return new FilterProcessResult($text);
    }
    foreach ($matches[1] as $link) {
      if (strpos($link, '<drupal-media') === FALSE) {
        continue;
      }

      $text = str_replace($link, '<div>' . $link . '</div>', $text);
    }

    return new FilterProcessResult($text);
  }

}
