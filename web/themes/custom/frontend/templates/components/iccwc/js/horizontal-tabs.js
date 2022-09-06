/**
 * @file
 * Social media share.
 */

(function ($, Drupal) {

  Drupal.behaviors.horizontal_tabs = {
    attach: function (context, settings) {
      $("[data-horizontal-tab-parent-id]").once('moveHorizontalTabContent').each(function () {
        var parent_id = $(this).attr('data-horizontal-tab-parent-id');
        var parent = $('[data-tab-content-id="' + parent_id + '"]');
        console.log(parent);
        var element = $(this).detach();
        parent.append(element);
      });
    }
  };
})(jQuery, Drupal);
