<?php

namespace Drupal\asu_react_core\Utils;

use Drupal\Core\Url;

/**
 * Class ReactComponentHelperFunctionss.
 */
class ReactComponentHelperFunctions {

  public function getImagesItems($block, $rand_id) {
    $image_carousel = new \stdClass();
    $image_carousel->items = [];

    if ($block->field_type) {
      $image_carousel->type = $block->field_type->value;
    }

    foreach ($block->field_carousel_card as $paragraph_ref) {
      $image_carousel->items[] = $paragraph_ref->entity->uuid();
    }

    $settings = [];
    $settings['components'][$block->bundle()][$rand_id] = $image_carousel;

    return $settings;
  }

  public function getCardContent($paragraph) {
    if (empty($paragraph)) {
      return;
    }
    $id = $paragraph->uuid();
    $card = new \stdClass();
    $card->id = $id;

    switch ($paragraph->getType()) {
      case 'card':
      case 'card_with_icon':
        $card->cardType = 'default';
        break;
      case 'card_degree':
        $card->cardType = 'degree';
        break;
      case 'card_event':
        $card->cardType = 'event';
        break;
      case 'card_story':
        $card->cardType = 'story';
        break;
    }

    if ($paragraph->field_media->target_id && $paragraph->field_media->entity->field_media_image->target_id) {
      $card->imageSource = file_create_url($paragraph->field_media->entity->field_media_image->entity->getFileUri());
      $card->imageAltText = $paragraph->field_media->entity->field_media_image->alt;
    }
    if ($paragraph->field_heading->value) {
      $card->title = $paragraph->field_heading->value;
    }
    if ($paragraph->field_body->value) {
      $card->content = $paragraph->field_body->value;
    }
    if ($paragraph->field_cta && $paragraph->field_cta->entity) {
      $cta = new \stdClass();
      $cta->label = $paragraph->field_cta->entity->field_cta_link->title;
      $link = Url::fromUri($paragraph->field_cta->entity->field_cta_link->uri);
      $cta->href = $link->toString();
      $color = $this->getButtonColor($paragraph->field_cta->entity->field_cta_link->options,'maroon' );
      $cta->color = $color;
      $cta->size = 'default';
      $card->buttons[] = $cta;
    }
    if ($paragraph->field_cta_secondary && $paragraph->field_cta_secondary->entity) {
      $cta = new \stdClass();
      $cta->label = $paragraph->field_cta_secondary->entity->field_cta_link->title;
      $link = Url::fromUri($paragraph->field_cta_secondary->entity->field_cta_link->uri);
      $cta->href = $link->toString();
      $color = $this->getButtonColor($paragraph->field_cta_secondary->entity->field_cta_link->options,'gold' );
      $cta->color = $color;
      $cta->size = 'small';
      $card->buttons[] = $cta;
    }
    if ($paragraph->field_link && $paragraph->field_link->title && $paragraph->field_link->uri) {
      $card->linkLabel = $paragraph->field_link->title;
      $link = Url::fromUri($paragraph->field_link->uri);
      $card->linkUrl = $link->toString();
    }
    foreach ($paragraph->field_tags as $term) {
      $tag = new \stdClass();
      $tag->label = $term->entity->name->value;
      $tag->href = Url::fromRoute('entity.taxonomy_term.canonical', ['taxonomy_term' => $term->entity->tid->value]);
      $card->tags[] = $tag;
    }

    if (isset($paragraph->field_icon)) {
      $icon_name = $paragraph->field_icon->icon_name;
      $icon_style = $paragraph->field_icon->style;
      $card->icon = [$icon_style, $icon_name];
    }

    $card->clickable = false;
    if ($paragraph->field_clickable->value && isset($paragraph->field_card_link)){
      $card->clickable = true;
      $card->clickHref = $paragraph->field_card_link->value;
    }

    $settings = [];
    $settings['components']['card'][$id] = $card;

    return $settings;
  }

  function getButtonColor($options, $default) {
    $color = $default;
    if (isset($options['attributes']['class'])) {
      //class structure from custom widget 'btn-size btn-color btn'
      $class = explode( ' ', $options['attributes']['class']);
      $color = substr($class[1], 4);
    }
    return $color;
  }

}