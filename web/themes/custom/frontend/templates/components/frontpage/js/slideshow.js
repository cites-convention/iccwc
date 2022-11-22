/**
 * @file
 * ICCWC Slideshow JS.
 */

(function ($, Drupal) {

  /**
   * Slideshow behavior.
   *
   * @type {Drupal~behavior}
   */
  Drupal.behaviors.iccwc_slideshow = {
    attach(context, settings) {
      $('.slideshow-flipster').flipster({
        touch: true,
      });
    },
  };
})(jQuery, Drupal);
