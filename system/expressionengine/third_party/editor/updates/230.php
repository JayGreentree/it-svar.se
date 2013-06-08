<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class EditorUpdate_230
{

    /**
     * Constructor
     *
     * @access public
     *
     * Calls the parent constructor
     */
    public function __construct()
    {
        $this->EE =& get_instance();
    }

    // ********************************************************************************* //

    public function do_update()
    {
        if ($this->EE->db->table_exists('editor_buttons') == FALSE)
        {
            //----------------------------------------
            // EXP_EDITOR_BUTTONS
            //----------------------------------------
            $ci = array(
                'id'                => array('type' => 'INT',       'unsigned' => TRUE, 'auto_increment' => TRUE),
                'site_id'           => array('type' => 'SMALLINT',  'unsigned' => TRUE, 'default' => 1),
                'config_id'         => array('type' => 'INT',       'unsigned' => TRUE, 'default' => 1),
                'lowvar_id'         => array('type' => 'INT',       'unsigned' => TRUE, 'default' => 1),
                'matrixcol_id'      => array('type' => 'INT',       'unsigned' => TRUE, 'default' => 1),
                'button_class'      => array('type' => 'VARCHAR',   'constraint' => 250, 'default' => ''),
                'button_settings'   => array('type' => 'TEXT'),
            );

            $this->EE->dbforge->add_field($ci);
            $this->EE->dbforge->add_key('id', TRUE);
            $this->EE->dbforge->add_key('button_class');
            $this->EE->dbforge->add_key('config_id');
            $this->EE->dbforge->create_table('editor_buttons', TRUE);
        }
    }

    // ********************************************************************************* //

}

/* End of file 400.php */
/* Location: ./system/expressionengine/third_party/channel_images/updates/400.php */
