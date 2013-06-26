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
<?php 
$type = render($content['field_ex_car_item_type']); 
$item_classes = ($type == 'image') ? "$type verticalCenter" : $type;
?>
<div class='item <?php print $item_classes; ?>'>
<?php
  switch ($type) {
    case 'image':
      print render($content['field_ex_car_item_image']);
      break;
    case 'image_plus_text':
    ?>
      <div class='image'><?php print render($content['field_ex_car_item_image_w_text']); ?></div>
      <dl class="text">
        <dt><?php print render($content['field_ex_car_item_header']); ?></dt>
        <dd>
          <div class='subhead_1'><?php print render($content['field_ex_car_item_header_1']); ?></div>
          <div class='subhead_2'><?php print render($content['field_ex_car_item_header_2']); ?></div>
          <div class='desc'><?php print render($content['field_ex_car_item_text']); ?></div>
          <?php print render($content['field_ex_car_item_link']); ?>
        </dd>
      </dl>
    <?php  
      break;
    case 'video':
      print render($content['field_ex_car_item_video']);
      break;  
  }
?>  
</div>