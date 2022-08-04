<?php

namespace Drupal\iccwc\Plugin\Field\FieldFormatter;

use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\FormatterBase;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\slick\Plugin\Field\FieldFormatter\SlickEntityFormatterBase;
use Drupal\slick\SlickDefault;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\slick\Plugin\Field\FieldFormatter\SlickFormatterTrait;
use Drupal\slick\Plugin\Field\FieldFormatter\SlickFormatterViewTrait;

/**
 * Plugin implementation of the 'Slick Paragraph' formatter.
 *
 * @FieldFormatter(
 *   id = "slick_paragraph",
 *   label = @Translation("Slick Paragraph"),
 *   field_types = {
 *     "entity_reference_revisions",
 *   },
 *   quickedit = {"editor" = "disabled"}
 * )
 */
class SlickParagraphFormatter extends SlickEntityFormatterBase {

}
