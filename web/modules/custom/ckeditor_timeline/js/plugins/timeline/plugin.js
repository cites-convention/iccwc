/**
 * @file
 * Functionality to enable timeline functionality in CKEditor.
 */

(function () {
  'use strict';

  // Register plugin.
  CKEDITOR.plugins.add('timeline', {
    hidpi: true,
    icons: 'timeline',
    init: function (editor) {
      // Add single button.
      editor.ui.addButton('Timeline', {
        command: 'addTimelineCmd',
        icon: this.path + 'icons/timeline.png',
        label: Drupal.t('Insert timeline')
      });

      // Add CSS for edition state.
      var cssPath = this.path + 'timeline.css';
      editor.on('mode', function () {
        if (editor.mode === 'wysiwyg') {
          this.document.appendStyleSheet(cssPath);
        }
      });

      // Prevent nesting DLs by disabling button.
      editor.on('selectionChange', function (evt) {
        if (editor.readOnly) {
          return;
        }
        var command = editor.getCommand('addTimelineCmd');
        var element = evt.data.path.lastElement && evt.data.path.lastElement.getAscendant('dl', true);
        if (element) {
          command.setState(CKEDITOR.TRISTATE_DISABLED);
        }
        else {
          command.setState(CKEDITOR.TRISTATE_OFF);
        }
      });

      var allowedContent = 'dl dd dt(!ckeditor-timeline)';

      // Command to insert initial structure.
      editor.addCommand('addTimelineCmd', {
        allowedContent: allowedContent,

        exec: function (editor) {
          var dl = new CKEDITOR.dom.element.createFromHtml(
            '<dl class="ckeditor-timeline">' +
              '<dt><span>20XX</span></dt>' +
              '<dd><p>20XX content 1.</p></dd>' +
              '<dt><span>20XX</span></dt>' +
              '<dd><p>Year 2 content.</p></dd>' +
            '</dl>');
          editor.insertElement(dl);
        }
      });

      // Other command to manipulate tabs.
      editor.addCommand('addTimelineItemBefore', {
        allowedContent: allowedContent,

        exec: function (editor) {
          var element = editor.getSelection().getStartElement();
          var newHeader = new CKEDITOR.dom.element.createFromHtml('<dt><span>YearX</span></dt>');
          var newContent = new CKEDITOR.dom.element.createFromHtml('<dd><p>YearX content</p></dd>');
          if (element.getAscendant('dd', true)) {
            element = element.getAscendant('dd', true).getPrevious();
          }
          else {
            element = element.getAscendant('dt', true);
          }
          newHeader.insertBefore(element);
          newContent.insertBefore(element);
        }
      });
      editor.addCommand('addTimelineItemAfter', {
        allowedContent: allowedContent,

        exec: function (editor) {
          var element = editor.getSelection().getStartElement();
          var newHeader = new CKEDITOR.dom.element.createFromHtml('<dt><span>YearX</span></dt>');
          var newContent = new CKEDITOR.dom.element.createFromHtml('<dd><p>YearX content</p></dd>');
          if (element.getAscendant('dt', true)) {
            element = element.getAscendant('dt', true).getNext();
          }
          else {
            element = element.getAscendant('dd', true);
          }
          newContent.insertAfter(element);
          newHeader.insertAfter(element);
        }
      });
      editor.addCommand('removeTimelineItem', {
        exec: function (editor) {
          var element = editor.getSelection().getStartElement();
          var a;
          if (element.getAscendant('dt', true)) {
            a = element.getAscendant('dt', true);
            a.getNext().remove();
            a.remove();
          }
          else {
            a = element.getAscendant('dd', true);
            if (a) {
              a.getPrevious().remove();
              a.remove();
            }
            else {
              element.remove();
            }
          }
        }
      });

      // Context menu.
      if (editor.contextMenu) {
        editor.addMenuGroup('timelineGroup');
        editor.addMenuItem('timelineItemBeforeItem', {
          label: Drupal.t('Add timeline item before'),
          icon: this.path + 'icons/timeline.png',
          command: 'addTimelineItemBefore',
          group: 'timelineGroup'
        });
        editor.addMenuItem('timelineItemAfterItem', {
          label: Drupal.t('Add timeline item after'),
          icon: this.path + 'icons/timeline.png',
          command: 'addTimelineItemAfter',
          group: 'timelineGroup'
        });
        editor.addMenuItem('removeTimelineItem', {
          label: Drupal.t('Remove timeline item'),
          icon: this.path + 'icons/timeline.png',
          command: 'removeTimelineItem',
          group: 'timelineGroup'
        });

        editor.contextMenu.addListener(function (element) {
          var parentEl = element.getAscendant('dl', true);
          if (parentEl && parentEl.hasClass('ckeditor-timeline')) {
            return {
              timelineItemBeforeItem: CKEDITOR.TRISTATE_OFF,
              timelineItemAfterItem: CKEDITOR.TRISTATE_OFF,
              removeTimelineItem: CKEDITOR.TRISTATE_OFF
            };
          }
        });
      }
    }
  });
})();
