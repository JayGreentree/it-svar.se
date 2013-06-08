<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Low Variables Language file
 *
 * @package         low-variables-ee_addon
 * @author          Lodewijk Schutte <hi@gotolow.com>
 * @link            http://gotolow.com/addons/low-variables
 * @copyright       Copyright (c) 2009-2011, Low
 */

$lang = array(

//----------------------------------------
// Required for MODULES page
//----------------------------------------

"low_variables_module_name" =>
"Low Variables",

"low_variables_module_description" =>
"Global settings and content",

//----------------------------------------
// Home screen
//----------------------------------------

"groups" =>
"Groups",

"show_all" =>
"Show all",

"ungrouped" =>
"Ungrouped",

"manage_variables" =>
"Manage variables",

"no_variables_found" =>
"No variables found",

"low_variables_save" =>
"Save changes",

"variable_managers" =>
"Variable Managers",

"low_variables_docs" =>
"Documentation",

"manage_this_variable" =>
"Manage this variable",

//----------------------------------------
// Feedback
//----------------------------------------

"low_variables_saved" =>
"Changes saved",

"low_variables_saved_except" =>
"Changes saved, except for:",

//----------------------------------------
// Manage variables list screen
//----------------------------------------

"create_new" =>
"Create new variable",

// Table headers

"variable_name" =>
"Variable Name",

"variable_data" =>
"Variable Data",

"variable_code" =>
"Variable Code",

"variable_label" =>
"Variable Label",

"variable_type" =>
"Variable&nbsp;Type",

"is_hidden_th" =>
"Hidden",

"early_parsing_th" =>
"Early",

"save_as_file_th" =>
"File",

"clone" =>
"Clone",

// List options

"change_sort_order" =>
"Change sort order",

"with_selected" =>
"With selected: ",

"show-hide" =>
"Show/Hide",

"show" =>
"Show",

"hide" =>
"Hide",

"early_parsing" =>
"Early parsing",

"enable_early_parsing" =>
"Enable early parsing",

"disable_early_parsing" =>
"Disable early parsing",

"enable_save_as_file" =>
"Enable save as file",

"disable_save_as_file" =>
"Disable save as file",

"change_type_to" =>
"Change variable type to...",

"change_group_to" =>
"Change group to...",

//----------------------------------------
// Manage variables change order screen
//----------------------------------------

"variable_order" =>
"Variable display order",

//----------------------------------------
// Delete vars
//----------------------------------------

"low_variables_delete_confirmation" =>
"Delete confirm",

"low_variables_delete_confirmation_one" =>
"Are you sure you want to delete this variable?",

"low_variables_delete_confirmation_many" =>
"Are you sure you want to delete these variables?",

"low_variables_deleted" =>
"Variables deleted",

//----------------------------------------
// Delete group
//----------------------------------------

"low_variables_group_delete_confirmation" =>
"Delete group confirm",

"low_variables_group_delete_confirmation_one" =>
"Are you sure you want to delete this variable group? Variables in this group will be ungrouped.",

"low_variables_group_delete_confirmation_many" =>
"Are you sure you want to delete these variable groups?",

"low_variable_group_deleted" =>
"Variable group deleted",

//----------------------------------------
// Create/Edit Variable screen
//----------------------------------------

"edit_variable" =>
"Edit variable",

// General items

"variable_name_help" =>
"Name of the variable you can use in your templates, e.g.: <em>lv_myvar</em>",

"variable_label_help" =>
"Label of the variable users see in the Low Variables module home page",

"variable_notes" =>
"Variable Notes",

"variable_notes_help" =>
"Add notes or instructions to this variable &mdash; markup is allowed",

"early_parsing_help" =>
"Enable to parse variable early in the parsing order",

"early_parsing_disabled_msg" =>
"Early parsing is disabled in the Extension Settings",

"is_hidden" =>
"Hide variable",

"is_hidden_help" =>
"Only show variable to variable managers",

"is_hidden_label" =>
"Hide from non-managers",

"save_as_file" =>
"Save as file",

"save_as_file_help" =>
"Save and sync this variable’s value as a file on the server",

"save_as_file_label" =>
"Save as file",

"save_as_file_disabled_msg" =>
"The Save As Files option is disabled in the Extension Settings",

"variable_type_help" =>
"Choose type of input field",

"settings_for" =>
"Settings for:",

"creation_options" =>
"Creation options",

"initiate_variable_data" =>
"Initiate variable data",

"variable_data_help" =>
"Enter the initial value of this variable",

"variable_suffix" =>
"Variable suffix",

"variable_suffix_help" =>
"If entered, Low Variables will create a new variable for each given suffix.<br />Separate suffixes with spaces, e.g.: <em>en es nl</em>",

//----------------------------------------
// Settings per type
//----------------------------------------

"variable_checkbox_label" =>
"Checkbox label",

"variable_options" =>
"Options",

"variable_options_help" =>
"Put each item on a single line",

"allow_multiple_items" =>
"Select multiple items?",

"allow_multiple_items_label" =>
"Allow for multiple items to be selected",

"separator_character" =>
"Character to separate multiple values",

"multi_interface" =>
"Select interface for multiple select",

"select" =>
"Multiple Select element",

"drag-list" =>
"Drag and drop lists",

"drag-list-thumbs" =>
"Drag and drop lists with thumbnails",

"newline" =>
"New line",

"pipe" =>
"Pipe line",

"comma" =>
"Comma",

"variable_maxlength" =>
"Maximum number of characters",

"variable_size" =>
"Input size",

"large" =>
"Large",

"medium" =>
"Medium",

"small" =>
"Small",

"x-small" =>
"Extra small",

"variable_pattern" =>
"Pattern to match",

"variable_pattern_help" =>
"If entered, the value will be validated using this Regular Expression",

"variable_rows" =>
"Number of rows",

"text_direction" =>
"Text direction",

"text_direction_ltr" =>
"Left to Right",

"text_direction_rtl" =>
"Right to Left",

"enable_code_format" =>
"Enable code formatting?",

"use_code_format" =>
"Use code formatting",

"none" =>
"None",

"select_any" =>
"-- Any --",

// Select Categories

"allow_multiple_categories" =>
"Select multiple categories?",

"allow_multiple_categories_label" =>
"Allow for multiple categories to be selected",

"category_groups" =>
"Select category groups",

"no_category_groups_selected" =>
"No category groups selected",

// Select Channels

"allow_multiple_channels" =>
"Select multiple channels?",

"allow_multiple_channels_label" =>
"Allow for multiple channels to be selected",

"channel_ids" =>
"Select channels",

"no_channels_selected" =>
"No channels selected",

// Select Entries

"future_entries" =>
"Future entries",

"show_future" =>
"Show future entries",

"expired_entries" =>
"Expired entries",

"show_expired" =>
"Show expired entries",

"channels" =>
"Channels",

"categories" =>
"Categories",

"statuses" =>
"Statuses",

"open" =>
"Open",

"closed" =>
"Closed",

"orderby" =>
"Order by",

"entry_date" =>
"Entry date",

"title" =>
"Title",

"in" =>
"in",

"order_asc" =>
"Ascending order",

"order_desc" =>
"Descending order",

"limit" =>
"Limit entries to",

"allow_multiple_entries" =>
"Select multiple entries?",

"allow_multiple_entries_label" =>
"Allow for multiple entries to be selected",

"no_channel_selected" =>
"No channel selected",

// Select Files

"allow_multiple_files" =>
"Select multiple files?",

"allow_multiple_files_label" =>
"Allow for multiple files to be selected",

"file_folders" =>
"Get files from these folders:",

"upload_folder" =>
"Upload folder",

"upload_folder_help" =>
"Select upload destination for new files",

"no_folders_selected" =>
"No file folders selected",

"no_uploads" =>
"No uploads",

"upload_new_file" =>
"Upload new file",

"save_to_upload" =>
"Save changes to upload file.",

"folder_not_found" =>
"Upload folder not found",

// Table

"columns" =>
"Columns",

"columns_help" =>
"Separate each column with a pipe line: <em>|</em>",

"add_row" =>
"Add row",

//----------------------------------------
// Variable groups
//----------------------------------------

"variable_group" =>
"Group",

"manage_groups" =>
"Manage groups",

"create_new_group" =>
"Create new group",

"edit_group" =>
"Edit variable group",

"delete_group" =>
"Delete variable group",

"group_label" =>
"Group label",

"group_notes" =>
"Group notes",

"group_notes_help" =>
"Add notes or instructions to this group &mdash; markup is allowed",

"save_as_new_group_label" =>
"Save as new group",

"new_group_options" =>
"New group options",

"duplicate_variables" =>
"Duplicate variables in the new group?",

"duplicate_variables_label" =>
"Duplicate variables",

"group_variable_suffix_help" =>
"Enter a suffix that will be applied to each of the duplicated variables in the new group. <em>Required when duplicating variables.</em>",

"suffix_options" =>
"Suffix options",

"append_suffix" =>
"Append suffix",

"replace_suffix" =>
"Replace suffix",

"low_variable_groups_saved" =>
"Variable groups saved",

"low_variables_moved" =>
"Moved variables to group",

"group_saved" =>
"Saved changes to group",

"no_group_label" =>
"No group label given.",

"suffix_required" =>
"Suffix is required when duplicating variables.",

//----------------------------------------
// Feedback messages
//----------------------------------------

"unknown_type" =>
"Unknown type",

"invalid_variable_name" =>
"The variable name is invalid",

"variable_name_already_exists" =>
"Variable name already exists",

"invalid_value" =>
"Invalid value",

"settings_not_found" =>
"Could not find settings! Check the Low Variables extension.",

//----------------------------------------
// Required for EXTENSION page
//----------------------------------------

"extension_settings" =>
"Extension settings",

"license_key" =>
"License key",

"license_key_help" =>
"Enter you license key you received.",

"can_manage" =>
"Select Variable Managers",

"can_manage_help" =>
"Select member groups allowed to manage the variables.",

"clear_cache" =>
"Clear cache",

"clear_cache_help" =>
"Optionally clear the cache when saving variable values.",

"clear_cache_opt_out" =>
"Ask (opt-out)",

"clear_cache_opt_in" =>
"Ask (opt-in)",

"register_globals" =>
"Enable early parsing?",

"register_globals_help" =>
"Choose if variables can be parsed early in the parsing order; before or after existing Snippets and config-variables.",

"register_globals_before" =>
"Yes, before Snippets",

"register_globals_after" =>
"Yes, after Snippets",

"register_member_data" =>
"Add member data to early parsed variables?",

"register_member_data_help" =>
"If set to Yes, all {logged_in_&hellip;} variables will be parsed early in the parsing order.",

"save_as_files" =>
"Save as files?",

"save_as_files_help" =>
"If set to Yes, you can choose which variables to save as files on your server.",

"file_path" =>
"File path",

"file_path_help" =>
"If you want to save variables as files, enter the file path here.",

"variable_types" =>
"Variable types",

"variable_types_help" =>
"Select which variable types are enabled.",

"settings_saved" =>
"Extension settings saved",

//----------------------------------------
// Required for FIELDTYPE page
//----------------------------------------

"lv_ft_multiple" =>
"Allow multiple variables to be selected",

"lv_ft_groups" =>
"Select variables from these groups",

"no_variable_group_selected" =>
"No variable group selected",

/* END */
''=>''
);
