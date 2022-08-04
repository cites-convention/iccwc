<?php

namespace Drupal\iccwc\Plugin\Field\FieldFormatter;

use Drupal\slick\Plugin\Field\FieldFormatter\SlickEntityFormatterBase;

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
