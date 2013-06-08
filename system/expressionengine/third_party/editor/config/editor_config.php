<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

if (isset($this->EE) == FALSE) $this->EE =& get_instance(); // For EE 2.2.0+

$config['editor_defaults']['editor_settings'] = 'predefined';
$config['editor_defaults']['editor_conf'] = '';
$config['editor_defaults']['height'] = '200';
$config['editor_defaults']['direction'] = 'ltr';
$config['editor_defaults']['toolbar'] = 'yes';
$config['editor_defaults']['source'] = 'yes';
$config['editor_defaults']['focus'] = 'no';
$config['editor_defaults']['autoresize'] = 'yes';
$config['editor_defaults']['fixed'] = 'no';
$config['editor_defaults']['convertlinks'] = 'yes';
$config['editor_defaults']['convertdivs'] = 'yes';
$config['editor_defaults']['overlay'] = 'yes';
$config['editor_defaults']['observeimages'] = 'yes';
$config['editor_defaults']['shortcuts'] = 'yes';
$config['editor_defaults']['air'] = 'no';
$config['editor_defaults']['wym'] = 'no';
$config['editor_defaults']['protocol'] = 'yes';
$config['editor_defaults']['allowedtags_option'] = 'default';
$config['editor_defaults']['allowedtags'] = array();
$config['editor_defaults']['formattingtags'] = array('p', 'blockquote', 'pre', 'h1', 'h2', 'h3', 'h4');
$config['editor_defaults']['buttons'] = array();
$config['editor_defaults']['language'] = 'en';
$config['editor_defaults']['css_file'] = '';

$config['editor_defaults']['upload_service'] = 'local';
$config['editor_defaults']['file_upload_location'] = '';
$config['editor_defaults']['image_upload_location'] = '';
$config['editor_defaults']['image_browsing'] = 'yes';
$config['editor_defaults']['image_subdir'] = 'yes';

$config['editor_defaults']['s3']['file']['bucket'] = '';
$config['editor_defaults']['s3']['image']['bucket'] = '';
$config['editor_defaults']['s3']['aws_access_key'] = '';
$config['editor_defaults']['s3']['aws_secret_key'] = '';

$config['editor_default_buttons'] = array(
'html', '|', 'formatting', '|', 'bold', 'italic', '|',
'unorderedlist', 'orderedlist', 'outdent', 'indent', '|',
'image', 'video', 'file', 'link', '|', 'alignleft', 'aligncenter', 'alignright', '|',
'horizontalrule');
