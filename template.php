<?php
/**
* @file
* Contains theme override functions and preprocess functions for the theme.
*
* ABOUT THE TEMPLATE.PHP FILE
*
*   The template.php file is one of the most useful files when creating or
*   modifying Drupal themes. You can modify or override Drupal's theme
*   functions, intercept or make additional variables available to your theme,
*   and create custom PHP logic. For more information, please visit the Theme
*   Developer's Guide on Drupal.org: http://drupal.org/theme-guide
*
* OVERRIDING THEME FUNCTIONS
*
*   The Drupal theme system uses special theme functions to generate HTML
*   output automatically. Often we wish to customize this HTML output. To do
*   this, we have to override the theme function. You have to first find the
*   theme function that generates the output, and then "catch" it and modify it
*   here. The easiest way to do it is to copy the original function in its
*   entirety and paste it here, changing the prefix from theme_ to nulib_.
*   For example:
*
*     original: theme_breadcrumb()
*     theme override: nulib_breadcrumb()
*
*   where nulib is the name of your sub-theme. For example, the
*   zen_classic theme would define a zen_classic_breadcrumb() function.
*
*   If you would like to override either of the two theme functions used in Zen
*   core, you should first look at how Zen core implements those functions:
*     theme_breadcrumbs()      in zen/template.php
*     theme_menu_local_tasks() in zen/template.php
*
*   For more information, please visit the Theme Developer's Guide on
*   Drupal.org: http://drupal.org/node/173880
*
* CREATE OR MODIFY VARIABLES FOR YOUR THEME
*
*   Each tpl.php template file has several variables which hold various pieces
*   of content. You can modify those variables (or add new ones) before they
*   are used in the template files by using preprocess functions.
*
*   This makes THEME_preprocess_HOOK() functions the most powerful functions
*   available to themers.
*
*   It works by having one preprocess function for each template file or its
*   derivatives (called template suggestions). For example:
*     THEME_preprocess_page    alters the variables for page.tpl.php
*     THEME_preprocess_node    alters the variables for node.tpl.php or
*                              for node-forum.tpl.php
*     THEME_preprocess_comment alters the variables for comment.tpl.php
*     THEME_preprocess_block   alters the variables for block.tpl.php
*
*   For more information on preprocess functions and template suggestions,
*   please visit the Theme Developer's Guide on Drupal.org:
*   http://drupal.org/node/223440
*   and http://drupal.org/node/190815#template-suggestions
*/


/**
* Override or insert variables into the html templates.
*
* @param $variables
*   An array of variables to pass to the theme template.
* @param $hook
*   The name of the template being rendered ("html" in this case.)
*/
function nulib_preprocess_html(&$variables, $hook) {
  // We need to update the body classes in order to generate a sidebar if there are blocks in
  // the sidebar_second_top region.
  if (!empty($variables['page']['sidebar_second_top']) && empty($variables['page']['sidebar_second'])) { 
    if (in_array('one-sidebar sidebar-first', $variables['classes_array'])) {
      // If the page knows that there is one sidebar and it's the left sidebar, then we update the classes array to 
      // indicate two sidebars
      $variables['classes_array'] = array_diff($variables['classes_array'], array('one-sidebar sidebar-first'));
      $variables['classes_array'][]  = 'two-sidebars';              
    } else {
      // Since this page doesn't have any blocks in sidebar_second and the above case handles there being one sidebar,
      // then this page must have no sidebars.  We should configure it to display the right sidebar. 
      $variables['classes_array'][]  = 'one-sidebar sidebar-second';              
    }
  }
}

/**
* Override or insert variables into the page templates.
*
* @param $variables
*   An array of variables to pass to the theme template.
* @param $hook
*   The name of the template being rendered ("page" in this case.)
*/
function nulib_preprocess_page(&$variables, $hook) {
  // Remove tabs from single node poll pages for anonymous users, because
  // we do not want this functionality.
  if (!user_is_logged_in() && isset($variables['node']) && $variables['node']->type == 'poll') {
    $variables['tabs'] = FALSE;
  }
}
function nulib_preprocess_user_profile(&$variables) {
  // We are unable to set the spamspan option for the user profile email field, so we 
  // set it manually here.   
  $this_user = $variables['elements']['#account'];
  $variables['protected_email'] = spamspan($this_user->mail);
}
  
/**
* Override or insert variables into the node templates.
*
* @param $variables
*   An array of variables to pass to the theme template.
* @param $hook
*   The name of the template being rendered ("node" in this case.)
*/
function nulib_preprocess_node(&$variables, $hook) { 
  // Look for preprocess functions on a per content-type basis
  $function = __FUNCTION__ . '_' . $variables['node']->type;
  if (function_exists($function)) {
    $function($variables, $hook);
  }
  if ($variables['page']) { 
    // If we are displayiing this node as its own page, make the node body resizable.
    $variables['classes_array'][] = 'resizable';
  }
}
function nulib_preprocess_node_event(&$variables, $hook) {
  if (!$variables['teaser']) {
    $node = $variables['node'];    
    
    $variables['img_type'] = field_fetch_value_fetch('node', $variables['node'], 'field_ev_image_orientation', 0);
    
    // Get the "to" value for this event
    $to = field_fetch_value_fetch('node', $variables['node'], 'field_ev_date', 0, 'value2');
    $to_for_comparison = substr($to, 0, 10);
    
    // Get today's date
    $now = format_date(time(), 'custom', 'Y-m-d');
    
    // If the event ends today or in the future, create our links
    if (strtotime($now) <= strtotime($to_for_comparison)) {
      // Get the "to" value as a UTC string
      $to = strtotime($to . ' UTC');
      $to = format_date($to, 'custom', 'Ymd\THis', variable_get('date_default_timezone', 0));
                         
      // Get the "from" value as a UTC string
      $from = field_fetch_value_fetch('node', $variables['node'], 'field_ev_date', 0);
      $from = strtotime($from . ' UTC');
      $from = format_date($from, 'custom', 'Ymd\THis', variable_get('date_default_timezone', 0));
      
      // Transform the title so that it doesn't break links
      $event_title = str_replace(' ','+',$node->title);
      
      // Create the "Add to Google Calendar" link
      $variables['google_link'] = "http://www.google.com/calendar/event?action=TEMPLATE&text=$event_title&dates=$from/$to&location=NU+Library&trp=false&sprop=website:library.northeastern.edu&sprop;=name:NU+Library";
      
      
      // Create the "Register this event" link if the node indicates we should
      $rsvp = field_fetch_value_fetch('node', $variables['node'], 'field_ev_rsvp', 0);
      if ($rsvp) {
        $variables['register_link'] = "/news-events/calendar/register-for-an-event?event=$event_title";  
      }
    }
  }
}
function nulib_preprocess_node_showcase_onecol(&$variables, $hook) {
  if ($variables['page']) {
    // Load up an array with the string values needed for this showcases's admin nav.
    $options = array(
      'item_name'          => 'field_sc_onecol_item',
      'title_field'        => 'field_sc_onecol_item_caption',
      'admin_nav_label'    => 'carousel',
      'key'                => 'admin_nav'
      );
    _build_field_collection_admin_nav($variables, $options);
  }
  
  // Build carousel navigation dots for the number of items we have.
  $item_num = count($variables['field_sc_onecol_item']);
  $variables['carousel_nav'] = _build_carousel_nav($item_num);
}
function nulib_preprocess_node_showcase_twocol(&$variables, $hook) {
  if ($variables['page']) {
    // Load up an array with the string values needed for this showcases's admin nav.
    $options = array(
      'item_name'          => 'field_sc_twocol_item',
      'title_field'        => 'field_sc_twocol_item_headline',
      'admin_nav_label'    => 'carousel',
      'key'                => 'admin_nav'
      );
    _build_field_collection_admin_nav($variables, $options);
  }
  
  // Build carousel navigation dots for the number of items we have.
  $item_num = count($variables['field_sc_twocol_item']);
  $variables['carousel_nav'] = _build_carousel_nav($item_num);
}
function nulib_preprocess_node_exhibit(&$variables, $hook) { 
  if ($variables['page']) {
    if (user_is_logged_in()){
      // Load up an array with the string values needed for this exhibit's admin nav for the carousel.
      $options = array(
        'item_name'          => 'field_ex_car_item',
        'admin_nav_label'    => 'carousel',
        'key'                => 'carousel_admin_nav'
        );
      _build_field_collection_admin_nav($variables, $options);

      // Load up an array with the string values needed for this exhibit's admin nav for the tabs.
      $options = array(
        'item_name'          => 'field_ex_tab_item',
        'title_field'        => 'field_ex_tab_item_title',
        'admin_nav_label'    => 'tab',
        'key'                => 'tab_admin_nav'
        );
      _build_field_collection_admin_nav($variables, $options);
    }

    // Check to see if there are any carousel items for this exhibit
    if (isset($variables['field_ex_car_item'])) {
      // Build carousel navigation dots for the number of carousel items we have.
      $item_num = count($variables['field_ex_car_item']);
      $variables['carousel_nav'] = _build_carousel_nav($item_num);
    }

    // Check to see if there are any tabs for this exhibit
    if (isset($variables['field_ex_tab_item'])) {    
      $items = $variables['field_ex_tab_item'];
      $tab_areas = array();
      $tab_titles = array();
      if (count($items) == 1) {
        $fc = field_collection_item_load($items[0]['value']);
        $tab_title = field_fetch_value_fetch('field_collection', $fc, 'field_ex_tab_item_title', 0);  
        $tab_area = field_fetch_value_fetch('field_collection', $fc, 'field_ev_tab_item_area', 0);
        $tab_areas[] = "<div class='tab-title-single'>$tab_title</div>$tab_area";
      } else {
        // Go through each item and create the HTML for its body. Then, send the titles to a helper funcion to assemble.
        foreach($items as $idx => $item) {
          $fc = field_collection_item_load($item['value']);
          $tab_area     = field_fetch_value_fetch('field_collection', $fc, 'field_ev_tab_item_area', 0);
          $tab_titles[] = field_fetch_value_fetch('field_collection', $fc, 'field_ex_tab_item_title', 0);
          // We always begin with the first tab selected.
          $selected = ($idx == 0) ? ' selected' : '';
          $j = $idx + 1;
          $tab_areas[] = "<div id='tab-$j' class='tabBody clearfix$selected'>$tab_area</div>"; 
        }
        $variables['tab_nav']  = _build_tab_nav($tab_titles);
      }
      $variables['tab_area'] = implode($tab_areas); 
    }
  }
}
function nulib_preprocess_node_take_action(&$variables, $hook) {
  if ($variables['page']) {
    // Load up an array with the string values needed for this take action widget's admin nav.
    $options = array(
      'item_name'          => 'field_ta_item',
      'title_field'        => 'field_ta_item_button_text',
      'admin_nav_label'    => 'take action',
      'key'                => 'admin_nav'
      );
    _build_field_collection_admin_nav($variables, $options);
  }

  // Check to see if there are any items for this widget
  if (isset($variables['field_ta_item'])) {
    // Gather the items for the widget
    $items = $variables['field_ta_item'];
    // We need to separate the items into two types, ones with expandable items and ones which are plain links
    $expandable_items = array();
    $plain_items = array();
    foreach($items as $idx => $item) {
      // Use the Field Collection module to load this entity
      $fc = field_collection_item_load($item['value']);
      $expandable = field_fetch_value_fetch('field_collection', $fc, 'field_ta_item_expandable_panel', 0);
      // Build the HTML for each item
      $button_text = field_fetch_value_fetch('field_collection', $fc, 'field_ta_item_button_text', 0);
      if ($expandable) {
        $button = "<a href='#' class='opener'>$button_text</a>";
        $contents = "<div  class='detail'>" . field_fetch_value_fetch('field_collection', $fc, 'field_ta_item_text', 0) . '</div>';
        $expandable_items[] = $button . $contents;
      } else {
        $link = field_fetch_value_fetch('field_collection', $fc, 'field_ta_item_link', 0, 'url');
        $plain_link = l($button_text, $link, array('html' => TRUE));
        $icon_class = field_fetch_value_fetch('field_collection', $fc, 'field_ta_item_icon_type', 0);
        $plain_items[] = _make_li($plain_link, $icon_class);
      }
    }
    $expandable_item_num = count($expandable_items);
    if ($expandable_item_num > 0) {
      // On the home page, the first expandable item is open
      $class = ($variables['is_front']) ? "open" : "closed"; 
      $expandable_items[0] = _make_li($expandable_items[0], "first $class");
      for ($j = 1; $j <= $expandable_item_num - 2; $j++) {
        $expandable_items[$j] = _make_li($expandable_items[$j], 'closed');
      }  
      $expandable_items[$expandable_item_num - 1] = _make_li($expandable_items[$expandable_item_num - 1], 'last closed');
      $variables['accordion'] = "<ul class='accordion'>" . implode($expandable_items) . "</ul>";
    }  
    if (count($plain_items) > 0) {
      $variables['largeList'] = "<ul class='largeList'>" . implode($plain_items) . "</ul>";
    }  
  }
}
function nulib_preprocess_node_link_list(&$variables, $hook) {
  // Link lists are styled differently based on type, so we set the classes array here. 
  $type = field_fetch_value_fetch('node', $variables['node'], 'field_ll_type', 0);  
  $variables['type'] = $type;
  $variables['classes_array'][] = $type;
  if ($type == 'also_in_the_library') {
    $variables['classes_array'][] = 'textureBox';   
  } else {
    $variables['classes_array'][] = 'textbox';   
  }
}
function nulib_preprocess_node_spotlight(&$variables, $hook) {
  $variables['sl_type'] = field_fetch_value_fetch('node', $variables['node'], 'field_sl_type', 0);
  
  // Spotlight widgets have different h-tags for their header based on their placement, so we set this here.
  // We also need this value on the template to determine which image to use, if this spotlight is type image.
  $variables['sl_placement'] = field_fetch_value_fetch('node', $variables['node'], 'field_sl_placement', 0);
  if ($variables['sl_placement'] == 'body') {
    $variables['spotlight_body'] = '<h2>Spotlight</h2>';
  } else {
    $variables['spotlight_sidebar'] = '<h4>Spotlight</h4>';
  }  
}
function nulib_preprocess_node_search_box(&$variables, $hook) {
  $external_links = field_fetch_value_fetch('node', $variables['node'], 'field_sb_external_links');   
  if (count($external_links) > 0){
    $variables['classes_array'][] = 'external_links';
  }
}
function nulib_preprocess_node_rotating_feature(&$variables, $hook) {
  $variables['rf_type']        = field_fetch_value_fetch('node', $variables['node'], 'field_rf_type', 0);
  $variables['rf_orientation'] = field_fetch_value_fetch('node', $variables['node'], 'field_rf_orientation', 0);
}
function nulib_preprocess_node_giving_item(&$variables, $hook) {
  $variables['giv_type'] = field_fetch_value_fetch('node', $variables['node'], 'field_giv_type', 0);
}

/**
 * implements hook_preprocess_node_resource()
 *
 * Builds the custom link for the title of the node and addes the icons.
 */

function nulib_preprocess_node_resource(&$variables, $hook) {
  $resource_url = $variables['field_resource_link_plain']['und'][0]['value'];
  $icons = field_fetch_value_fetch('node', $variables['node'], 'field_resource_icon'); 
  $icons = implode(array_map('_make_icon', $icons));
  $variables['title_line'] = 
    "<h2>". l($variables['title'], $resource_url, array('html'=>True,)) . "</h2>" .
    $icons;
  $variables['more_info'] = 
      field_fetch_value_fetch('node', $variables['node'], 'field_resource_description', 0);
  $detailed_subjects = field_fetch_value_fetch('node', $variables['node'], 'field_resource_detailed_subjects', NULL); 
  if (!$variables['page'] && count($detailed_subjects) > 0) { 
    $detailed_subjects = implode(', ', array_map('_get_term_name', $detailed_subjects)); 
    $variables['more_info'] .= 
      "<div class='field field-name-field-resource-type field-type-taxonomy-term-reference field-label-inline clearfix'>
        <div class='field-label'>Detailed subjects:&nbsp;</div>
        <div class='field-items'>" . $detailed_subjects. "</div>
      </div>";
  } 
}

/**
* Build the admin navigation for field collections.
*
* @param $variables
*   An array of variables to pass to the theme template.
* @param $options
*   An array holding the item_name and title_field for the type of field collection we are dealing with.
*/
function _build_field_collection_admin_nav(&$variables, $options) {
  $item_name = $options['item_name'];  
  $node = $variables['node'];      
  $label = $options['admin_nav_label'];
  $edit_section = '';
  $edit_links = array();

  // Get the current page's path to use as the return destination of all the links we create
  $dest = drupal_get_destination();

  // Check to make this node has at least one item
  if ($node->$item_name) {
    // Gather the items in the field_collection
    $items = $variables[$item_name];
    foreach($items as $idx => $item) {
      // Use the Field Collection module to load this entity
      $fc = field_collection_item_load($item['value']);
      if ($fc) {
        // Check to make sure the user can edit this entity
        if (field_collection_item_access('edit', $fc)) {
          // Grab the value of the field we should use as the link title
          $title = (isset($options['title_field'])) ? field_fetch_value_fetch('field_collection', $fc, $options['title_field'], 0) : NULL;
          if (!$title) {
            // Some field collections don't have good fields to use as titles here, so if we haven't
            // found a title, just use a default one. 
            $j = $idx + 1;
            $title = "Item $j";
          }

          // Add the links to our array of links
          $edit_link = l('edit', $fc->path() . '/' . 'edit', array('query' => $dest));
          $delete_link = l('delete', $fc->path() . '/' . 'delete', array('query' => $dest));
          $edit_links[] = "$title:&nbsp;&nbsp;&nbsp;&nbsp;$edit_link&nbsp;&nbsp;$delete_link";
        }
      }  
    }
  }
  
  $path = 'field-collection/' . str_replace('_', '-', $item_name) . '/add/node/' . $node->nid;   
  $edit_links[] = l('add new item', $path, array('query' => $dest)); 
  
  // Build and return the HTML for a nice display of the admin links
  $edit_links = array_map('_make_li', $edit_links);
  $variables[$options['key']] = "<h4>Edit $label items</h4><ul>" . implode($edit_links) . '</ul>';
}

/**
* Create the HTML for the carousel navigation dots.
*
* @param $item_num
*   The number of dots to display.
*/
function _build_carousel_nav($item_num){
  if ($item_num == 1) {return;}
  $carousel_nav = '<ul class="carouselNav">';
  for ($i = 1; $i <= $item_num - 1; $i++) {
    // Display one dot for each item
    $carousel_nav .= "<li><a href='#'>$i</a></li>";
  }
  $carousel_nav .= "<li class='last'><a href='#'>$item_num</a></li>";
  $carousel_nav .= '</ul>';
  return $carousel_nav;
}

/**
* Create the HTML for the tab header nav.
*
* @param $tab_titles
*   The titles to be displayed on the tabs.
*/
function _build_tab_nav($tab_titles) {
  $tab_nav_items = array();
  foreach($tab_titles as $idx => $tab_title) {
    // We always begin with the first tab selected.
    $selected = ($idx == 0) ? ' class="selected"' : '';
    $j = $idx + 1;
    // Build a list item for each title
    $tab_nav_items[] = "<li><a href='#tab-$j'$selected>$tab_title</a></li>";
  }
  // Return the assembled unordered list.
  return '<ul class="tabNav clearfix">' . implode($tab_nav_items) . '</ul>'; 
}

/**
* Override or insert variables into the comment templates.
*
* @param $variables
*   An array of variables to pass to the theme template.
* @param $hook
*   The name of the template being rendered ("comment" in this case.)
*/
/* -- Delete this line if you want to use this function
function nulib_preprocess_comment(&$variables, $hook) {
$variables['sample_variable'] = t('Lorem ipsum.');
}
// */

/**
* Override or insert variables into the block templates.
*
* @param $variables
*   An array of variables to pass to the theme template.
* @param $hook
*   The name of the template being rendered ("block" in this case).
*/
function nulib_preprocess_block(&$variables, $hook) {
  // Figure out which block we are dealing with. 
  $elements = $variables['elements'];
  if (isset($elements['#bundle'])) {
    $key = $elements['#bundle'];
  } elseif (isset($elements['content']['#bundle'])) {
    $key = $elements['content']['#bundle'];
  } elseif (isset($elements['#block'])) {
    $key = $elements['#block']->module;
  }
  if (isset($key)){ 
    $block = $variables['block'];
    switch ($key) {
      case 'showcase_twocol':
        // We never want to display the subject for two column showcases, because they are shown at the top
        // of the page.
        $variables['block'] = _set_block_subject($block);
        break;
      case 'take_action':
        // We never want to display the subject for a Take Action widget.
        $variables['block'] = _set_block_subject($block);
        break;
      case 'link_list':
        // We never want to display the subject for a link list.
        $variables['block'] = _set_block_subject($block);
        break;
      case 'search_box':
        // We never want to display the subject for a search box.
        $variables['block'] = _set_block_subject($block);
        break;
      case 'spotlight':
        // We never want to display the subject for a spotlight, since that is manually set to "Spotlight" when we preprocess the node.
        $variables['block'] = _set_block_subject($block);
        // Spotlight widgets are styled based on where they are placed, so place a class in the the array to help with that. 
        $region = $variables['block']->region;
        $styles = array('content' => 'spotMain', 'content_top' => 'spotMain', 'sidebar_second' => 'textbox');
        if (isset($styles[$region])) {
          $variables['classes_array'][] = $styles[$region];
        } 
        break;
      case 'primo_search_block':

        array_push($variables['classes_array'],'bigSearch');
        break;
      case 'jump':
        $variables['bigBrowse'] = "";
        if ($block->delta != 'menu-menu-audience') {
          $variables['classes_array'][] = 'bigSearch';
          $variables['bigBrowse'] = 'bigBrowse';
        } 
        break;
    }
  }
}

function nulib_preprocess_views_view(&$variables) {
  $view_name = $variables['view']->name;
  // This nodequeue view creates the rotating features widget.
  if ($view_name == 'nodequeue_1') {
    $item_num = count($variables['view']->result);
    // Build carousel navigation dots for the number of items we have.
    $variables['carousel_nav'] = _build_carousel_nav($item_num);
  }
}  
/**
* Themes field collection items printed using the field_collection_view formatter, via the Field Collection module.
*/
function nulib_field_collection_view($variables) {
  $element = $variables['element'];

  //Default implementation:
  //return '<div' . drupal_attributes($element['#attributes']) . '>' . $element['#children'] . '</div>';
  
  // Instead, we strip out the containing div, so that all of iFactory's javascript implementations work.
  return $element['#children'];
}

/**
* Themes embedded blocks printed using the Insert Block module.
 */
function nulib_insert_block_block($vars) {
  $content = '';
  // Commented out the next three lines, to remove the subject, as we are handling that via the node template.
  // if (!empty($vars['block']['subject'])) {
  //   $content .= '<h2>'. $vars['block']['subject'] .'</h2>';
  // }
  if (!empty($vars['block']['content'])) {
    $content .= render($vars['block']['content']);
  }
  return $content;
}

/**
* Override theme_breadcrumb.
 */
function nulib_breadcrumb($variables) {
  $breadcrumb = $variables['breadcrumb'];
  if (!empty($breadcrumb)) {
    // Grab the last breadcrumb -- we want to style it differently
    $last = array_pop($breadcrumb); 
    
    // Wrap each element of the array in li-tags
    $breadcrumb = array_map('_make_li', $breadcrumb);
    // Do the same to the last breadcrumb, but give it a special class
    $breadcrumb[] = "<li class='last'>$last</li>";
    // Provide a navigational heading to give context for breadcrumb links to
    // screen-reader users. Make the heading invisible with .element-invisible.
    $output = '<h2 class="element-invisible">' . t('You are here') . '</h2>';
    // Assemble the array into an unordered list
    $output .= '<ul id="breadcrumb">' . implode($breadcrumb) . '</ul>';
    return $output;
  }
}

/**
* Override theme_menu_link
 */
function nulib_menu_link(array $variables) {
  $element = $variables['element'];
  $sub_menu = '';
  if ($element['#below']) {
    $disclosure = '<span href="" class="toggle">+/-</span>';
    $sub_menu = drupal_render($element['#below']);
    $classes = $element['#attributes']['class'];
    $classes[] = 'expandable';    
    if (in_array('active-trail', $classes) && !in_array('active', $classes)) {
      $classes[] = 'open';
    } else {
      $classes[] = 'closed';
    }
    if (in_array('activeTrail', $classes)) {
      $classes[] = 'containsActive';
    }
    $element['#attributes']['class'] = $classes;
  } else {
    $disclosure = '';
  }
  $output = l($element['#title'], $element['#href'], $element['#localized_options']);
  return '<li' . drupal_attributes($element['#attributes']) . '>' . $disclosure . $output . $sub_menu . "</li>\n";
}

/**
 * Theme function for 'local' phone number field formatter.
 */
function nulib_cck_phone_formatter_local_phone_number($element) {
  $phone = '';

  // Display a local phone number without country code.
  if (!empty($element['number'])) {
    // Call country local formatter if exist
    $function = $element['country_codes'] . '_formatter_local';
    if (function_exists($function)) {
      $phone = $function($element);
    }
    else {
      // Default implementation is:
      // $phone = $element['number']);
      // Custom implentation styles the number in the form (xxx)-xxx-xxxx
      $phone = _make_phonenumber($element['number']);
    }

    // Extension
    if (!empty($element['extension'])) {
      $phone = $phone . theme('phone_number_extension', $element['extension']);
    }
  }

  return $phone;
}

/**
 * Override theme function for jump form.
 *
 * @param array $form
 * @return string
 */
function nulib_jump_quickly_form($variables) {
  $form = $variables['form'];
  $output = '<div class="container-inline">';
  $output .= drupal_render($form['jump_goto']);
  $output .= '</div>';
  return $output;
}
/**
 * Returns HTML for an individual feed item for display in the block.
 *
 * @param $variables
 *   An associative array containing:
 *   - item: The item to be displayed.
 *   - feed: Not used.
 *
 * @ingroup themeable
 */
function nulib_aggregator_block_item($variables) { 
  // The default implementation of this function returns the external link to the item.
  // return '<a href="' . check_url($variables['item']->link) . '">' . check_plain($variables['item']->title) . "</a>\n";
  
  // Our site calls instead for the date, author and a bit of the description to be presented.
  $item = $variables['item'];
  $title = $item->title;
  $author = $item->author;
  $description = $item->description;
  
  // Pull out the author thumbnail, which is placed on the beginning of the description.
  $thumbnail_end = strpos($description, '/>');
  $thumbnail = substr($description, 0, $thumbnail_end + 2);
  if (strpos($thumbnail, 'snippet_thumbnail') == 12) {
    // Sanity check.  This means the thumbnail is formatted how we expect it to be.
    // If the thumbnail is formatted properly, pull it out for display at the top of the block.
    $description = substr($description, $thumbnail_end + 3);
  } else {
    // If the thumbnail isn't formatted as we expect, leave it in the description.
    $thumbnail = '';
  }
  
  // Truncate the description at 400 characters. 
  $description = truncate_utf8($description, 190, TRUE, FALSE);
  // If the description does not end with a paragraph, then add an ellipsis.
  if (substr($description, -4, 4) != '</p>') {
    $description .= '&#8230;';
  }
  $link = $item->link;       
  // Transform the Unix timestamp into a nice date format
  $date = date('M j, Y', $item->timestamp);
  return "
    <div class='image'>$thumbnail</div>
    <div class='text'>
      <h4><a href='$link'>$title</a></h4>
      <p class='details'><time>$date</time><br />$author<br>
      <div class='description'>$description</div>
      <p><a href='$link' class='arrow'>Read More</a></p>
      <div class='clearfix'></div>
    </div>"; 
  
}
function nulib_more_link ($array) {
  // When this is the more link being constructed for the Snell Snippets widget, 
  // return the empty string so that it is not presented.
   if (stristr( $array['url'], 'aggregator')) {
      return "";
   }
}

/**
 * Theme function to allow any menu tree to be themed as a Nice menu.
 *
 * @param $id
 *   The Nice menu ID.
 * @param $menu_name
 *   The top parent menu name from which to build the full menu.
 * @param $mlid
 *   The menu ID from which to build the displayed menu.
 * @param $direction
 *   Optional. The direction the menu expands. Default is 'right'.
 * @param $depth
 *   The number of children levels to display. Use -1 to display all children
 *   and use 0 to display no children.
 * @param $menu
 *   Optional. A custom menu array to use for theming --
 *   it should have the same structure as that returned
 *  by menu_tree_all_data(). Default is the standard menu tree.
 *
 * @return
 *   An HTML string of Nice menu links.
 */
function nulib_nice_menus($variables) {
  $output = array(
    'content' => '',
    'subject' => '',
  );

  $menu_name = $variables['menu_name'];
  if ($menu_name == 'main-menu') {
    // We will theme the main menu as an iFactory mega menu.
    if ($menu_tree = nice_mega_menu_menus_tree('main-menu')) {
      if ($menu_tree['content']) {
        $output['content'] = '<ul>' . $menu_tree['content'] . '</ul>' . "\n";
      }
    }
  } else {
    // Use the default implementation of this theme function.
    $id = $variables['id'];
    $mlid = $variables['mlid'];
    $direction = $variables['direction'];
    $depth = $variables['depth'];
    $menu = $variables['menu'];   
    if ($menu_tree = theme('nice_menus_tree', array('menu_name' => $menu_name, 'mlid' => $mlid, 'depth' => $depth, 'menu' => $menu))) {
      if ($menu_tree['content']) {
        $output['content'] = '<ul class="nice-menu nice-menu-' . $direction . '" id="nice-menu-' . $id . '">' . $menu_tree['content'] . '</ul>' . "\n";
        $output['subject'] = $menu_tree['subject'];
      }
    }
  }
  return $output;
}

/**
 * Returns HTML for an image with an appropriate icon for the given file.
 *
 * @param $variables
 *   An associative array containing:
 *   - file: A file object for which to make an icon.
 *   - icon_directory: (optional) A path to a directory of icons to be used for
 *     files. Defaults to the value of the "file_icon_directory" variable.
 *
 * @ingroup themeable
 */
function nulib_file_icon($variables) {
  $file = $variables['file'];  
  // If we are dealing with a PDF, then direct the File module to find the file icon in the theme icons folder.
  // This is the only part of the standard function that we've changed.
  if ($variables['file']->filemime == 'application/pdf') {
    $icon_directory = 'sites/all/themes/nulib/images/icons/';
  } else {
    $icon_directory = $variables['icon_directory'];
  }

  $mime = check_plain($file->filemime);
  $icon_url = file_icon_url($file, $icon_directory);
  return '<img class="file-icon" alt="" title="' . $mime . '" src="' . $icon_url . '" />';
}

/**
* List all field render functions below.
* Many functions simply return the markup, which keeps layers of drupal-created divs 
* from breaking iFactory's layout.
* In other cases, we want to wrap the field in HTML for styling purposes.
*/
function nulib_field__field_sc_onecol_item_type($variables) {
  return _field_markup($variables);
}
function nulib_field__field_sc_twocol_item_type($variables) {
  return _field_markup($variables);
}
function nulib_field__field_sc_twocol_item_text($variables) {
  return _field_markup($variables);
}
function nulib_field__field_ex_car_item_type($variables) {
  return _field_markup($variables);
}
function nulib_field__field_ll_link($variables) {
  // Turn the link list into an unordered list
  $type = field_fetch_value_fetch('node', $variables['element']['#object'], 'field_ll_type', 0);   
  $links = array(); 
  foreach ($variables['items'] as $item) {
    $links[]  = _make_li(render($item));
  }
  $links = implode($links);
  // Return the link list with a class based on its type, if needed.
  if ($type == 'also_in_the_library') {
    return "<ul class='smallList'>$links</ul>";
  } else {
    return "<ul>$links</ul>";
  }
}

function nulib_field__field_sl_video($variables) {
  $placement = field_fetch_value_fetch('node', $variables['element']['#object'], 'field_sl_placement', 0);
  $markup = _field_markup($variables);
  // Return the video with a class based on its placement, if needed.
  if ($placement == 'body') {
    return "<div class='photo left'>$markup</div>"; 
  } else {
    return $markup;
  }
}
function nulib_field__field_sl_header($variables){
  $placement = field_fetch_value_fetch('node', $variables['element']['#object'], 'field_sl_placement', 0);
  $markup = _field_markup($variables);
  // Return the header with a h-tag based on its placement, if needed.
  if ($placement == 'body') {
    return "<h3>$markup</h3>";
  } else {
    return $markup;
  }
}
function nulib_field__field_rf_caption($variables) {
  $markup = _field_markup($variables);
  return "<div class='caption'>$markup</div>";
}
function nulib_field__field_rf_subhead($variables){
  // Style the subhead based whether there is a link for it to use.
  $subhead = _field_markup($variables);
  $link = field_fetch_value_fetch('node', $variables['element']['#object'], 'field_rf_link', 0, 'display_url');
  if ($link) {
    return "<h4><a href='$link'>$subhead</a></h4>";
  } else {
    return "<h4>$subhead</h4>";
  }
}
function nulib_field__field_tip_name($variables) {
  return _field_markup($variables);
}
function nulib_field__field_tip_class($variables) {
  return _field_markup($variables);
}
function nulib_field__field_resource_subject($variables){
  $output = '';

  // Render the label, if it's not hidden.
  if (!$variables['label_hidden']) {
    $output .= '<div class="field-label"' . $variables['title_attributes'] . '>' . $variables['label'] . ':&nbsp;</div>';
  }

  // Render the items.
  $output .= '<div class="field-items"' . $variables['content_attributes'] . '>';
  $subjects = array(); 
  foreach ($variables['items'] as $item) {
    $subjects[]  = $item['#markup'];
  }
  $output .= implode(', ', $subjects);
  $output .= '</div>';

  // Render the top-level DIV.
  $output = '<div class="' . $variables['classes'] . '"' . $variables['attributes'] . '>' . $output . '</div>';

  return $output;
} 
function nulib_field__field_user_det_subject($variables){      
  $output = '';

  // Render the label, if it's not hidden.
  if (!$variables['label_hidden']) {
    $output .= '<div class="field-label"' . $variables['title_attributes'] . '>Subjects:&nbsp;</div>';
  }

  // Render the items.
  $output .= '<div class="field-items"' . $variables['content_attributes'] . '>';
  $subjects = array(); 
  foreach ($variables['items'] as $item) {
    $subjects[]  = $item['#markup'];
  }
  $output .= implode(', ', $subjects);
  $output .= '</div>';

  // Render the top-level DIV.
  $output = '<div class="' . $variables['classes'] . '"' . $variables['attributes'] . '>' . $output . '</div>';

  return $output;
}
// The spamspan option with the Drupal admin gui is broken, so we use the functionality manually here.   
function nulib_field__field_pr_contact_email($variables){      
  return spamspan(_field_markup($variables));
}
// Utility functions
function _make_phonenumber($phonenum) {
  return "(" . substr($phonenum, 0, 3) . ")" . " " . substr($phonenum, 3, 3) . "-" . substr($phonenum, 6, 4);
}
function _make_li($text, $class = NULL) {
  $class_string = $class ? " class='$class'" : '';
  return "<li$class_string>$text</li>";
}
function _set_block_subject($block, $subject = ''){
  $block->subject = $subject;
  return $block;
}
function _field_markup($vars){
  return $vars['element'][0]['#markup'];
}
function _get_term_name($term) {
  return $term->name;
}
function _make_icon($icon) {
  return "<div class='$icon'>$icon</div>";
}
function _get_image_with_style($vars, $field_name, $style_name, $entity){
  // Use the existing image values for uri and alt, but set image style as directed
  $uri   = field_fetch_value_fetch($entity, $vars['element']['#object'], $field_name, 0, 'uri');
  $alt   = field_fetch_value_fetch($entity, $vars['element']['#object'], $field_name, 0, 'alt');
  $title = field_fetch_value_fetch($entity, $vars['element']['#object'], $field_name, 0, 'title');
  $options = array(
    'style_name' => $style_name,
    'path'       => $uri,
    'alt'        => $alt,
    'title'      => $title,
  );
  // Return the HTML for the properly styled image
  return theme_image_style($options); 
}

/**
 * Implements hook_form_alter().
 *
 * Sets a placeholder for the exposed search form for search_api_views.
 */
function nulib_form_alter(&$form, &$form_state, $form_id) { 
  if($form_id == "views_exposed_form"){
    if(isset($form['keywords'])){
      if ($form['#action'] == "/search-site"){
        $form['keywords']['#attributes']['placeholder'] = t('Search Library Site ...');
      }
      if ($form['#action'] == "/search-staff"){
        $form['keywords']['#attributes']['placeholder'] = t('Search Library Staff ...');
      }
    }
  }
}

function nulib_field__field_resource_mobile_link_plain__resource(&$variables){
  $url = $variables['items'][0]['#markup'];
  $linkoptions = array(
    'attributes' => array(
      'class' => 'mobile-link',
      )
    );
  $link = l(t('go to mobile site'),$url,$linkoptions);
  return '<div class="field_resource_mobile_link_plain__resource">'.$link.'</div>';
}


