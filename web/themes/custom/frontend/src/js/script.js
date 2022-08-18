/**
 * @file
 * ICCWC Frontend JS.
 */

(function ($, Drupal) {

  /**
   * ICCWC behavior.
   *
   * @type {Drupal~behavior}
   */
  Drupal.behaviors.iccwc = {
    attach(context, settings) {
      $('.slideshow-flipster').flipster();
    },
  };
})(jQuery, Drupal);
