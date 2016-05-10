(function ($) {

  "use strict";

  Drupal.behaviors.environmentIndicatorToolbar = {
    attach: function (context, settings) {
      if (typeof drupalSettings.ikto_environment_indicator != 'undefined') {
        return;
      }
      $('#toolbar-bar', context)
          .css('background-color', settings.iktoEnvironmentIndicator.bgColor);
      $('#toolbar-bar .toolbar-item, #toolbar-bar .toolbar-item a', context)
          .css('color', settings.iktoEnvironmentIndicator.fgColor);
    }
  };

})(jQuery);
