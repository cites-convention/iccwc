/**
 * @file
 * Social media share.
 */

(function ($, Drupal) {

  Drupal.behaviors.horizontal_tabs = {
    attach: function (context, settings) {
      $(".node--view-mode-full [data-horizontal-tab-parent-id]").once('moveHorizontalTabContent').each(function () {
        var parent_id = $(this).attr('data-horizontal-tab-parent-id');
        var parent = $('[data-tab-content-id="' + parent_id + '"]');
        var element = $(this).detach();
        parent.append(element);
      });
    }
  };
})(jQuery, Drupal);
