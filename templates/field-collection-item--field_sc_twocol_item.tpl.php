<?php

/**
* @file
* Default theme implementation for field-collection items.
*
* Available variables:
* - $content: An array of comment items. Use render($content) to print them all, or
*   print a subset such as render($content['field_example']). Use
*   hide($content['field_example']) to temporarily suppress the printing of a
*   given element.
* - $title: The (sanitized) field-collection item label.
* - $url: Direct url of the current entity if specified.
* - $page: Flag for the full page state.
* - $classes: String of classes that can be used to style contextually through
*   CSS. It can be manipulated through the variable $classes_array from
*   preprocess functions. By default the following classes are available, where
*   the parts enclosed by {} are replaced by the appropriate values:
*   - entity-field-collection-item
*   - field-collection-item-{field_name}
*
* Other variables:
* - $classes_array: Array of html class attribute values. It is flattened
*   into a string within the variable $classes.
*
* @see template_preprocess()
* @see template_preprocess_entity()
* @see template_process()
*/
?>
<div class="item">
  <div class="image">
    <?php
      if (render($content['field_sc_twocol_item_type']) == 'image'):
        print render($content['field_sc_twocol_item_image']);
      else:
        print render($content['field_sc_twocol_item_video']);
      endif;
    ?>
  </div>
  <div class="text">
    <h3><?php print render($content['field_sc_twocol_item_headline']); ?></h3>
    <?php print render($content['field_sc_twocol_item_text']); ?>
    <?php print render($content['field_sc_twocol_item_link']); ?>
  </div>
</div>
