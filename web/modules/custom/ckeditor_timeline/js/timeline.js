(function ($, Drupal, drupalSettings) {

  Drupal.behaviors.ckeditorTimeline = {

    attach: function (context, settings) {
      $('.ckeditor-timeline').once('attachSlider').each(function () {
        var sliderMarkup = '<ul class="timeline-slider">'
        var selectMarkup = '<div class="timeline-select-wrapper">' + Drupal.t('Jump to') + '<select class="timeline-select">';

        $(this).find('dt').each(function () {
          sliderMarkup += '<li><a href="#" data-scroll-year="' + $(this).text() + '">' + $(this).text() + '</a></li>';
          selectMarkup += '<option value="' + $(this).text() + '">' + $(this).text() + '</option>';
        });

        sliderMarkup += '</ul>';
        selectMarkup += '</select></div>';

        $(this).parent().prepend(sliderMarkup);
        $(this).parent().prepend(selectMarkup);

      });

      $('.timeline-slider').once('timelineSliderEvents').each(function () {
        $(this).find('a').click(function (e) {
          e.preventDefault();

          var parent_wrapper = $(this).closest('.timeline-slider').parent();
          var year = $(this).attr('data-scroll-year');
          var year_element = parent_wrapper.find('dt:contains(' + year + ')');

          parent_wrapper.find('select').val(year);

          parent_wrapper.find('.active').removeClass('active');
          $(this).addClass('active');
          $([document.documentElement, document.body]).animate({
            scrollTop: year_element.offset().top - 150
          }, 800);
        });
      });

      $('.timeline-select').once('timelineSliderEvents').each(function () {
        $(this).change(function () {
          var year = $(this).val();
          var parent_wrapper = $(this).closest('.timeline-select-wrapper').parent();
          var year_element = parent_wrapper.find('dt:contains(' + year + ')');

          parent_wrapper.find('.active').removeClass('active');
          parent_wrapper.find('[data-scroll-year="' + year + '"]').addClass('active');

          $([document.documentElement, document.body]).animate({
            scrollTop: year_element.offset().top - 150
          }, 800);
        });
      });
    }
  };
})(jQuery, Drupal, drupalSettings);
