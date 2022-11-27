/**
 * @file
 * Social media share.
 */

(function ($, Drupal) {

  Drupal.behaviors.social_media_share = {
    attach: function (context, settings) {
      $('.print-button', context).once('printjs').each(function () {
        init();
      });

      function init() {
        let eventClick = '.print-button';
        if (drupalSettings.hasOwnProperty('printjs') && drupalSettings.printjs.length && drupalSettings.printjs.printjs_id.length) {
          eventClick += ' ,' + drupalSettings.printjs.printjs_id;
        }

        $(eventClick).on('click', function (e) {
          e.preventDefault();
          let css = [];
          let config = $(this).data();
          $("head").find("link[rel='stylesheet']").attr("href", function (i, value) {
            css.push(value);
            return value;
          });
          config.css = css;
          config.type = 'html';
          config.printable = 'main';
          if (config.printable) {
            printJS(config);
          } else {
            window.print();
          }
        });
      }

    }
  };
})(jQuery, Drupal);
