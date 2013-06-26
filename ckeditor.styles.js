/*
$Id: ckeditor.styles.js,v 1.1.2.2 2009/12/16 11:32:04 wwalc Exp $
Copyright (c) 2003-2009, CKSource - Frederico Knabben. All rights reserved.
For licensing, see LICENSE.html or http://ckeditor.com/license
*/

/*
 * This file is used/requested by the 'Styles' button.
 * 'Styles' button is not enabled by default in DrupalFull and DrupalFiltered toolbars.
 */
CKEDITOR.addStylesSet( 'drupal',
[
  { name : 'Paragraph',             element : 'p' },
  { name : 'div',                   element : 'div' },
  { name : 'Heading 2',             element : 'h2' },
  { name : 'Heading 3',             element : 'h3' },
  { name : 'Heading 4',             element : 'h4' },
  { name : 'Heading 5',             element : 'h5' },
  { name : 'Heading 6',             element : 'h6' },
  { name : 'span',                  element : 'span' },
  { name : 'Image wrapper',         element : 'div', attributes : { 'class' : 'photo' } },
  { name : 'Image wrapper (left)',  element : 'div', attributes : { 'class' : 'photo left' } },   
  { name : 'Image wrapper (right)', element : 'div', attributes : { 'class' : 'photo right' } },   
  { name : 'Drop below picture',    element : 'div',  attributes : { 'class' : 'clear-both' } },
  { name : 'Red',                   element : 'span', attributes : { 'class' : 'red' } },
  { name : 'Black',                 element : 'span', attributes : { 'class' : 'black' } },
  { name : 'Light black',           element : 'span', attributes : { 'class' : 'black-light' } },
  { name : 'Dark gray',             element : 'span', attributes : { 'class' : 'black-dark' } },
  { name : 'Dark gray',             element : 'span', attributes : { 'class' : 'gray-dark-1' } },
  { name : 'Medium gray',           element : 'span', attributes : { 'class' : 'gray-dark-2' } },
  { name : 'Medium gray',           element : 'span', attributes : { 'class' : 'gray-medium-1' } },
  { name : 'Light gray',            element : 'span', attributes : { 'class' : 'gray-medium-2' } },
  { name : 'Light gray',            element : 'span', attributes : { 'class' : 'gray-light-1' } },
  { name : 'Light gray',            element : 'span', attributes : { 'class' : 'gray-light-2' } },
  { name : 'Light gray',            element : 'span', attributes : { 'class' : 'gray-light-3' } },
  { name : 'Dark taupe',            element : 'span', attributes : { 'class' : 'taupe-dark' } },
  { name : 'Medium taupe',          element : 'span', attributes : { 'class' : 'taupe-medium' } },
  { name : 'Light taupe',           element : 'span', attributes : { 'class' : 'taupe-light-1' } },
  { name : 'Light taupe',           element : 'span', attributes : { 'class' : 'taupe-light-2' } },
]);
