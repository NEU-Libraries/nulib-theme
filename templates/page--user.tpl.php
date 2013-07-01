<?php
/**
* @file
* Zen theme's implementation to display a single Drupal page.
*
* Available variables:
*
* General utility variables:
* - $base_path: The base URL path of the Drupal installation. At the very
*   least, this will always default to /.
* - $directory: The directory the template is located in, e.g. modules/system
*   or themes/garland.
* - $is_front: TRUE if the current page is the front page.
* - $logged_in: TRUE if the user is registered and signed in.
* - $is_admin: TRUE if the user has permission to access administration pages.
*
* Site identity:
* - $front_page: The URL of the front page. Use this instead of $base_path,
*   when linking to the front page. This includes the language domain or
*   prefix.
* - $logo: The path to the logo image, as defined in theme configuration.
* - $site_name: The name of the site, empty when display has been disabled
*   in theme settings.
* - $site_slogan: The slogan of the site, empty when display has been disabled
*   in theme settings.
*
* Navigation:
* - $main_menu (array): An array containing the Main menu links for the
*   site, if they have been configured.
* - $secondary_menu (array): An array containing the Secondary menu links for
*   the site, if they have been configured.
* - $secondary_menu_heading: The title of the menu used by the secondary links.
* - $breadcrumb: The breadcrumb trail for the current page.
*
* Page content (in order of occurrence in the default page.tpl.php):
* - $title_prefix (array): An array containing additional output populated by
*   modules, intended to be displayed in front of the main title tag that
*   appears in the template.
* - $title: The page title, for use in the actual HTML content.
* - $title_suffix (array): An array containing additional output populated by
*   modules, intended to be displayed after the main title tag that appears in
*   the template.
* - $messages: HTML for status and error messages. Should be displayed
*   prominently.
* - $tabs (array): Tabs linking to any sub-pages beneath the current page
*   (e.g., the view and edit tabs when displaying a node).
* - $action_links (array): Actions local to the page, such as 'Add menu' on the
*   menu administration interface.
* - $feed_icons: A string of all feed icons for the current page.
* - $node: The node object, if there is an automatically-loaded node
*   associated with the page, and the node ID is the second argument
*   in the page's path (e.g. node/12345 and node/12345/revisions, but not
*   comment/reply/12345).
*
* Regions:
* - $page['main_top']:      The region on top main content of the current page, over the left-hand sidebar and content.
* - $page['content_top']:   The region directly over the main content of the current page.
* - $page['content']:       The main content of the current page.
* - $page['content_left']:  The left hand side main content of the current page, when divided in two columns.
* - $page['content_right']: The right hand side main content of the current page, when divided in two columns.
* - $page['sidebar_first']:  Items for the left sidebar.
* - $page['sidebar_second']: Items for the right sidebar.
* - $page['header']:        Items for the header region.
* - $page['navigation']:    Items for the navigation region.
* - $page['page_tools']:    Items for the page tools region.
* - $page['footer']:        Items for the footer region.
*
* @see template_preprocess()
* @see template_preprocess_page()
* @see zen_preprocess_page()
* @see template_process()
*/
?>
<div id="containerheader">
  <div id="header" class="clearfix">
    <div class="main-logo">
      <a href="/">
        
        <?php 
          include(__DIR__ . '/../images/library-logo/nu-libraries-lockup-color.svg');
        ?>
        
      </a>
    </div>
    <?php print render($page['header']); ?>
  </div>
</div>
<!-- /header -->

<?php if ($page['navigation']): ?>
<div class="region-navigation-wrapper">
  <?php print render($page['navigation']); ?>
</div>
<?php endif; ?>

<div id="container">
  <div id="borderWrap">
    <div id="columnWrap" class="clearfix">
      <?php print $messages; ?>
      <div id="topbar">
        <div id="topbarInner">
          <div id="topbarInner2">
            <?php if ($page['main_top']): ?>
              <?php print render($page['main_top']); ?>
              <div class="clearfix"></div>
            <?php else: ?>
              <?php print $breadcrumb; ?>
            <?php endif; ?>
          </div>
        </div>
      </div> <!-- /topbar -->

      <?php if ($page['sidebar_first']): ?>
        <div id="columnOne">
          <?php print render($page['sidebar_first']); ?>
        </div>
      <?php endif; ?>

      <div id="mainContent">
        <?php if ($page['page_tools']): ?>
          <?php print render($page['page_tools']); ?>
        <?php endif; ?>
        <?php if ($page['content_top'] && !$is_front): ?>
          <?php print render($page['content_top']); ?>
        <?php endif; ?>
        <?php if (($tabs = render($tabs)) && !$is_front): ?>
          <div class="tabs">
            <?php print $tabs; ?>
          </div>
        <?php endif; ?>
        <?php if ($page['content'] && !$is_front): ?>
          <?php print render($page['content']); ?>
        <?php endif; ?>
        <?php if ($page['content_left']): ?>
          <div id="mainLeft">
            <?php print render($page['content_left']); ?>
          </div>
        <?php endif; ?>
        <?php if ($page['content_right']): ?>
          <div id="mainRight">
            <?php print render($page['content_right']); ?>
          </div>
        <?php endif; ?>
        <?php if (!$page['sidebar_second']): ?>
          <?php print render($page['social_networking']); ?>
        <?php endif; ?>
      </div>  <!-- /mainContent -->

      <?php if ($page['sidebar_second']): ?>
        <div id="columnTwo">
          <?php print render($page['sidebar_second']); ?>
          <?php print render($page['social_networking']); ?>
        </div>
      <?php endif; ?>

    </div> <!-- /columnWrap -->
  </div> <!-- /borderWrap -->
</div> <!-- /container -->
<?php if ($page['footer']): ?>
<div id="footerWrap">
  <?php print render($page['footer']); ?>
</div>
<?php endif; ?>