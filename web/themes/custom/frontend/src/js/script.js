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
      // Lazy load twitter timeline.
      if ($('.twitter-timeline').length > 0) {
        let $window = $(window).on('scroll', function loadTwitterTimeline(e){
          var element_position = $('.twitter-timeline').offset().top;
          var y_scroll_pos = window.pageYOffset + window.innerHeight;

          if (y_scroll_pos > element_position - 700) {
            var element = document.createElement("script");
            element.src = "//platform.twitter.com/widgets.js";
            document.body.appendChild(element);
            $window.off('scroll', loadTwitterTimeline);
          }
        });
      }
    },
  };
})(jQuery, Drupal);
