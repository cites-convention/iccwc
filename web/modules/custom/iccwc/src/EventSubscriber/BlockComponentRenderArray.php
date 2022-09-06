<?php

namespace Drupal\iccwc\EventSubscriber;

use Drupal\layout_builder\Event\SectionComponentBuildRenderArrayEvent;
use Drupal\layout_builder\LayoutBuilderEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Add classes to layout builder blocks.
 */
class BlockComponentRenderArray implements EventSubscriberInterface {

  /**
   * {@inheritdoc}
   */
  public static function getSubscribedEvents() {
    $events[LayoutBuilderEvents::SECTION_COMPONENT_BUILD_RENDER_ARRAY] = [
      'onBuildRender',
      50,
    ];
    return $events;
  }

  /**
   * Builds render arrays for block plugins and sets it on the event.
   *
   * @param \Drupal\layout_builder\Event\SectionComponentBuildRenderArrayEvent $event
   *   The section component render event.
   */
  public function onBuildRender(SectionComponentBuildRenderArrayEvent $event) {
    $border = $event->getComponent()->getThirdPartySetting('iccwc', 'border');
    $centered = $event->getComponent()->getThirdPartySetting('iccwc', 'centered');
    $horizontal_tab_id = $event->getComponent()->getThirdPartySetting('iccwc', 'horizontal_tab_id');
    $build = $event->getBuild();

    if (!empty($border)) {
      $build['#attributes']['class'][] = 'block-border';
    }
    if (!empty($centered)) {
      $build['#attributes']['class'][] = 'block-centered';
    }
    if (!empty($horizontal_tab_id)) {
      $build['#attributes']['data-horizontal-tab-parent-id'] = $horizontal_tab_id;
    }

    $event->setBuild($build);
  }

}
