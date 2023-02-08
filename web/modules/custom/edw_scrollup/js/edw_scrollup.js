(function ($, Drupal, drupalSettings) {

  Drupal.behaviors.edwScrollup = {

    attach: function (context, settings) {
      $(document).ready(function(){
        if(drupalSettings.edw_scrollup_title == '' || drupalSettings.edw_scrollup_title == null){
          var scroll_title = '';
        } else {
          var scroll_title = drupalSettings.edw_scrollup_title;
        }

        if(drupalSettings.edw_scrollup_hover_title == '' || drupalSettings.edw_scrollup_hover_title == null){
          var scroll_hover_title = '';
        } else {
          var scroll_hover_title = drupalSettings.edw_scrollup_hover_title;
        }

        $('body').append('<a aria-label="Scroll Up" href="#" title="'+scroll_hover_title+'" class="scrollup"><div class="scroll-title">'+scroll_title+'</div></a>');
        var position = drupalSettings.edw_scrollup_position;
        var button_bg_color = drupalSettings.edw_scrollup_button_bg_color;
        var hover_button_bg_color = drupalSettings.edw_scrollup_button_hover_bg_color;
        var scroll_window_position = parseInt(drupalSettings.edw_scrollup_window_position);
        var scroll_speed = parseInt(drupalSettings.edw_scrollup_speed);

        if (position == 1) {
          $('.scrollup').css({"right":"3%","background-color":button_bg_color});
        } else {
          $('.scrollup').css({"left":"3%","background-color":button_bg_color});
        }

        $(".scrollup").hover(function(){
          $(this).css("background-color", hover_button_bg_color);
        }, function(){
          $(this).css("background-color", button_bg_color);
        });

        $(window).scroll(function () {
          if ($(this).scrollTop() > scroll_window_position) {
            $('.scrollup').fadeIn();
          } else {
            $('.scrollup').fadeOut();
          }
        });

        $(".scrollup").click(function(){
          $("html, body").animate({
            scrollTop: 0
          }, scroll_speed);
          return false;
        });
      });
    }
  };
})(jQuery, Drupal, drupalSettings);
