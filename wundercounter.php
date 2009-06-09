<?php

/*
Plugin Name: WunderCounter
Plugin URI: http://www.wundercounter.com/
Description: Incorporate the WunderCounter into your wordpress blog.
Version: 1.0
Author: WunderSolutions
Author URI: http://www.wundersolutions.com/
*/

/*
 * Note, you have to leave the above block in as wordpress parses it and
 * uses it to identify this file as a plugin.
 */

if(!class_exists('MultiWidget')):

/*

Adapted from Alex Tingles MultiWidget Class
 
 
Original Notice:

Copyright (c) 2008 Alex Tingle.
(Based upon pattern for multi-widget, in Wordpress 2.6 wp-includes/widget.php.)

This program is free software; you can redistribute it and/or
modify it under the terms of the GNU General Public License
as published by the Free Software Foundation; either version 2
of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.

*/

class WunderPluginBase {
    
    var $plugin_url = '';
    var $plugin_dir = '';
    
    // if your subclass has a method "register_hooks" it'll be auto-run
    // on instantiation.  We'll amend this in the PluginWidget class to also
    // automatically register the widget
    var $run_on_construct = array('register_hooks');
    
    /**
     * Constructor
     *
     * @param   string      $plugin_path    The pathname to the plugin (just pass __FILE__ to the constructor)
     */
    function WunderPluginBase ($plugin_file = '') {
        
        if(!strlen($plugin_file))
            die(__CLASS__ . ' requires a plugin file; just pass __FILE__');
        
        $this->plugin_url = WP_PLUGIN_URL.'/'.str_replace(basename( $plugin_file ),"",plugin_basename( $plugin_file )); ;
        $this->plugin_dir = WP_PLUGIN_DIR . '/' . str_replace(basename( $plugin_file ),"",plugin_basename( $plugin_file ));
        $this->plugin_dir = preg_replace('/\\/','/',$this->plugin_dir); # os-compat
        
        // if you defined a register_hooks method in a subclass we'll run it
        // on instantiation

        foreach ( $this->run_on_construct as $method ) {
            if( method_exists($this,$method) ) {
                call_user_func(array($this, $method)); // equivalent to $this->$method()
                
            }
        }
        
    }
    
    /**
     * Returns the URL to the root of the plugin
     *
     * @param   string      $path   (optional) path to append to the end of the url
     * @return  string              A URL relative to the plugin's path
     */
    // lazy load the plugin's base url
    function plugin_url($path = '', $relative = FALSE) {
        if(!isset($path))
            $path = '';
        else            
            $path = preg_replace('/^\/+/','',$path);
            
        $url = $this->plugin_url . $path;

        // convert to a pseudo-relative path (maeaning I've pulled off the http and domain)
        if($relative)
            $url = preg_replace('/^[a-z]+:\/\/[^\/]+\//','/',$url);
            
        return $url;
    }
    
    function plugin_dir($path = '') {
        if(!isset($path))
            $path = '';

        if ( !is_string($pathname) )
            die("pathname must be a string");

        // force all slashes to be forward (os-compat) and remove leading slashes from path
        $path = preg_replace(array('/\\/','/^\/+/'),array('/',''),$pathname);
        return $this->plugin_dir . $path;
    }
 
    /* generate a <link> tag for css
     * often used when putting a hook into wp_head filter (for adding css to blog pages)
     * or admin_head (for adding css to admin pages)
     */
    function css_link_tag($css = '') {
        if(!isset($css) || $css == '')
            return '';
        
        return sprintf("<link type='text/css' rel='stylesheet' href='%s' />\n", $css);
    }

    // from http://ca2.php.net/manual/en/function.is-array.php#90929
    // return tru if the array is associative (a perl hash)
    function is_assoc(&$array) {
        return (is_array($array) && 0 !== count(array_diff_key($array, array_keys(array_keys($array)))));
    }
    
    function make_option_list(&$items = array(), $selected = '') {
        if(!is_array($items) || !count($items) || !is_string($selected))
            return '';
        
        $html = '';
        $is_hash = $this->is_assoc($items);
        
        foreach ( $items as $key => $value ) {
            // if items is indexed, the value and the label are the same
            $val    = $is_hash ? $key : $value;
            $label  = $value;
            $html .= sprintf(
                "<option value='%s' %s>%s</option>\n",
                wp_specialchars($val),
                ( $val == $selected ? 'selected="selected"' : ''),
                wp_specialchars($label)
            );
        }
        return $html;        
    }
}

/** This class wraps up lots of secret knowledge about how to make
 *  Wordpress "multi" widgets. These are widgets that allow more than one
 *  instance to be created. The standard "Text" widget is a simple example.
 *
 *  You must extend this class and over-ride three member functions.
 *  Scroll to the bottom of the file for a fully working example.
 */

class WunderPluginWidget extends WunderPluginBase
{
    //
    // Interesting member variables.
    
    var $id_base;         ///< Root id for all widgets of this type.
    var $name;            ///< Name for this widget type.
    var $widget_options;  ///< Option array passed to wp_register_sidebar_widget()
    var $control_options; ///< Option array passed to wp_register_widget_control()
    
    var $number =false; ///< Unique ID number of the current instance.
    var $id =false; ///< Unique ID string of the current instance (id_base-number)

    /*
     * list of methods to run on construct
     * the _widgets_init automatically registers the widget and the widget control
     *
     * as per a non-widgetized plugin, register hooks is where you put calls to
     * other add_hook and add_filter items
     */
    var $run_on_construct = array('_widgits_init','register_hooks');


    //
    // Member functions that you must over-ride.
    
    /** Echo the actual widget content. Subclasses should over-ride this function
     *  to generate their widget code. */
    function widget($args,$instance)
    {
      die('function MultiWidget::widget() must be over-ridden in a sub-class.');
    }


    /** Update a particular instance.
     *  This function should check that $new_instance is set correctly.
     *  The newly calculated value of $instance should be returned. */
    function control_update($new_instance, $old_instance)
    {
        die('function MultiWidget::control_update() must be over-ridden in a sub-class.');
    }


    /** Echo a control form for the current instance. */
    function control_form($instance)
    {
        die('function MultiWidget::control_form() must be over-ridden in a sub-class.');
    }


    //
    // Functions you'll need to call.
    
    /** CONSTRUCTOR
    *   widget_options: passed to wp_register_sidebar_widget()
    *   - description
    *   - classname
    *   control_options: passed to wp_register_widget_control()
    *   - width
    *   - height
    */
    function WunderPluginWidget(
        $plugin_pathname, // the full pathname of the main plugin file (just pass __file__)
        $id_base,         // a unique id by which this widget will be known
        $name,            // English name of the Widget
        $widget_options = array(), // usually just 'description'
        $control_options = array() // widget control box special stuff (often just height and width)
    ) {
        $this->id_base      = $id_base; // a unique id for the plugin
        $this->name         = $name; // name of the plugin as shown on the plugins screen
        $this->option_name  = 'multiwidget_'.$id_base; // becomes the stored key in the options database for this widget
        
        $this->widget_options = wp_parse_args(
            $widget_options,
            array('classname'=>$this->option_name)
        );
        $this->control_options = wp_parse_args(
            $control_options,
            array('id_base'=>$this->id_base)
        );
        // Set true when we update the data after a POST submit - makes sure we
        // don't do it twice.
        $this->updated = false;
        
        //parent::WunderPluginBase($plugin_pathname);
        $this->WunderPluginBase($plugin_pathname);
    
    }
    
    /**
     * Run on object instanciation and registers the widget
     */
    function _widgets_init() {
        add_action( 'widgets_init', array($this,'_register') );
    }
    
    
    /** Helper function to be called by control_form().
    *  Returns an HTML name for the field. */
    function get_field_name($field_name)
    {
        return 'widget-'.$this->id_base.'['.$this->number.']['.$field_name.']';
    }
    
    
    /** Helper function to be called by control_form().
    *  Returns an HTML id for the field. */
    function get_field_id($field_name)
    {
        return 'widget-'.$this->id_base.'-'.$this->number.'-'.$field_name;
    }
    
    
    /** Registers this widget-type.
    *  Must be called during the 'widget_init' action. */
    function _register()
    {
        if( !$all_instances = get_option($this->option_name) )
            $all_instances = array();
        
        $registered = false;
        foreach( array_keys($all_instances) as $number )
        {
            // Old widgets can have null values for some reason
            if( !isset($all_instances[$number]['__multiwidget']) )
                continue;
            $this->_set($number);
            $registered = true;
            $this->_register_one($number);
        }
        
        // If there are none, we register the widget's existance with a
        // generic template
        if( !$registered )
        {
            $this->_set(1);
            $this->_register_one();
        }
    }
    
    
    //
    // PRIVATE FUNCTIONS. Don't worry about these.
    
    function _set($number)
    {
        $this->number = $number;
        $this->id = $this->id_base.'-'.$number;
    }
    
    
    function _get_widget_callback()
    {
        return array(&$this,'widget_callback');
    }
    
    
    function _get_control_callback()
    {
        return array(&$this,'control_callback');
    }
    
    
    /** Generate the actual widget content.
    *  Just finds the instance and calls widget().
    *  Do NOT over-ride this function. */
    function widget_callback($args, $widget_args = 1)
    {
        if( is_numeric($widget_args) )
            $widget_args = array( 'number' => $widget_args );
        $widget_args = wp_parse_args( $widget_args, array( 'number' => -1 ) );
        $this->_set( $widget_args['number'] );
        
        // Data is stored as array:
        //  array( number => data for that instance of the widget, ... )
        $all_instances = get_option($this->option_name);
        if( isset($all_instances[$this->number]) )
            $this->widget($args,$all_instances[$this->number]);
    }
    
    
    /** Deal with changed settings and generate the control form.
    *  Do NOT over-ride this function. */
    function control_callback($widget_args = 1)
    {
        global $wp_registered_widgets;
        
        if( is_numeric($widget_args) )
            $widget_args = array( 'number' => $widget_args );
        $widget_args = wp_parse_args( $widget_args, array( 'number' => -1 ) );
        
        // Data is stored as array:
        //  array( number => data for that instance of the widget, ... )
        $all_instances = get_option($this->option_name);
        if( !is_array($all_instances) )
            $all_instances = array();
        
        // We need to update the data
        if( !$this->updated && !empty($_POST['sidebar']) )
        {
            // Tells us what sidebar to put the data in
            $sidebar = (string) $_POST['sidebar'];
            
            $sidebars_widgets = wp_get_sidebars_widgets();
            if( isset($sidebars_widgets[$sidebar]) )
                $this_sidebar =& $sidebars_widgets[$sidebar];
            else
                $this_sidebar = array();
            
            foreach( $this_sidebar as $_widget_id )
            {
                // Remove all widgets of this type from the sidebar.  We'll add the
                // new data in a second.  This makes sure we don't get any duplicate
                // data since widget ids aren't necessarily persistent across multiple
                // updates
                if(
                    $this->_get_widget_callback() == $wp_registered_widgets[$_widget_id]['callback']
                    && isset($wp_registered_widgets[$_widget_id]['params'][0]['number'])
                ) {
                    $number = $wp_registered_widgets[$_widget_id]['params'][0]['number'];
                    if( !in_array( $this->id_base.'-'.$number, $_POST['widget-id'] ) )
                    {
                        // the widget has been removed.
                        unset($all_instances[$number]);
                    }
                }
            }
    
            foreach( (array)$_POST['widget-'.$this->id_base] as $number=>$new_instance)
            {
                $this->_set($number);
                if( isset($all_instances[$number]) )
                    $instance = $this->control_update($new_instance,$all_instances[$number]);
                else
                    $instance = $this->control_update($new_instance,array());
                
                if( !empty($instance) )
                {
                    $instance['__multiwidget'] = $number;
                    $all_instances[$number] = $instance;
                }
            }
    
            update_option($this->option_name, $all_instances);
            $this->updated = true; // So that we don't go through this more than once
        }
    
        // Here we echo out the form
        if( -1 == $widget_args['number'] )
        {
            // We echo out a template for a form which can be converted to a
            // specific form later via JS
            $this->_set('%i%');
            $instance = array();
        }
        else
        {
            $this->_set($widget_args['number']);
            $instance = $all_instances[ $widget_args['number'] ];
        }
        $this->control_form($instance);
    }
    
    
    /** Helper function: Registers a single instance. */
    function _register_one($number = -1)
    {
        wp_register_sidebar_widget(
            $this->id,
            $this->name,
            $this->_get_widget_callback(),
            $this->widget_options,
            array( 'number' => $number )
        );
        wp_register_widget_control(
            $this->id,
            $this->name,
            $this->_get_control_callback(),
            $this->control_options,
            array( 'number' => $number )
        );
    }

} // end class MultiWidget

class WunderCounter extends WunderPluginWidget {
    
    var $settings_defaults   = array(
        'type'      => 'invisible',
        'style'     => '',
        'username'  => '',
        'page'      => '',
    );
    
    var $styles = array(
        'default'           => 'Default Style',
        'odometer'          => 'Odometer',
        '57chevy'           => '\'57 Chevy',
        'odometerblack'     => 'Odometer (Black)',
        'odometerwhite'     => 'Odometer (White)',
        'brush'             => 'Brush',
        'chalk'             => 'Chalk',
        'nextgen'           => 'Next Generation',
        'flame'             => 'Flame',
        'katt151'           => 'Katt 151',
        'microsc'           => 'Microscopic',
        'miniscu'           => 'Miniscule',
        'stencil'           => 'Stencil',
        'punk'              => 'Punk',
        'rosewd'            => 'Rosewood',
    );
    
    var $background = array(
        'transparent',
        'black',
        'white',
        'red',
        'gray',
    );
    
    var $text_colour = array(
        'black',
        'white',
        'red',
        'gray',
    );
    
    function WunderCounter() {
        $this->WunderPluginWidget(
            __FILE__,
            'wundercounter',
            'WunderCounter',
            array('description' => __('Incorporates WonderCounter into your Wordpress Site including counter widget.'))
        );
    }
    
    function register_hooks() {
        //add_filter('the_content',array($this,'include_counter'));
        add_action('init',array(&$this,'init'));
        add_action('wp_dashboard_setup',array(&$this,'dashboard_setup'));
    }
    
        
    function init() {
        add_submenu_page('plugins.php','WunderCounter', 'WunderCounter', 10, __FILE__, array(&$this,'admin_menu'));
        
    }

    
    function dashboard_setup() {
        if( (float) get_bloginfo('version') >= 2.7)
            wp_add_dashboard_widget('wundercounter-dashboard', 'WunderCounter', array(&$this,'dashboard_widget'));
    }
    
    function dashboard_widget() {
        ?>
        <p>WunderCounter is up and running.</p>
        <?php
    }
    
    function defaults($options) {
        
        // make sure we at least have something
        if     ( !isset($options)    ) { $instance = array(); }
        elseif ( !is_array($options) ) { $instance = (array) $instance; }


        $defaults = array(
            'username'          => '',
            'type'              => 'invisible',
            'style'             => 'default',
            'complexity'        => 'simple',
            'background'        => 'transparent',
            'text_colour'       => 'black',
            'simple_type'       => 'single_id', // single_id|track_all
            'simple_id'         => 'my_blog',   // the "tag" under which the hit counter counts when single_id is chosen
            'adv_base_id'       => 'my_blog',
            // the order of when these checks are done is important
            'adv_home_type'     => 'base', // base|single_id|url  is_front_page
            'adv_home_id'       => 'myblog_home',
            'adv_page_type'     => 'base', // base|single_id|url|composed
            'adv_page_id'       => 'myblog_page',
            'adv_archive_type'  => 'base', //base|single_id|url|composed
            'adv_archive_id'    => 'myblog_archive',
            'adv_search_type'   => 'base', // base|single|url|composed
            'adv_search_id'     => 'myblog_search',
            'adv_entry_type'    => 'base', //base|single|url|composed
            'adv_entry_id'      => 'myblog_entry',
            'adv_default_type'  => 'base', //base|single|url
            'adv_default_id'    => 'myblog_default'
            
        );

        // any are FALSE set to default (since 0 is invalid for any numerics we're safe)        
        foreach ($defaults as $key => $val ) {
            // if key not exists, value isn't set or it evaluates to false, set the default
            if( !( array_key_exists($key,$options) && isset($options[$key]) &&  $options[$key]) )
                $options[$key] = $val;
        }
        
        // MUST validate the style to ensure we have a safe file name to load
        if ( !array_key_exists($options['style'],self::$styles) )
            $options['style'] = $defaults['style'];
            
        // make sure we have a integer showposts
        if (!is_int($options['showposts']))
            $options['showposts'] = $defaults['showposts'];

        // make sure categories is an array            
        if (!is_array($options['categories']))
            $options['categories'] = $defaults['categories'];
            
        return $instance;
    }
    
    
    function admin_page() {
        
        // get base options
        $options = get_option($this->id_base);
        // IF UPDATE
        
        //DISPLAY
        ?>
<div class="wrap">
    <h2>WunderCounter Settings</h2>
    
    <form method="post" action="options.php">
        <?php wp_nonce_field('wundercounter-settings'); ?>
    
        <table class="form-table" style='width: auto;'>

            <tr valign="top">
                <th scope="row">Username:<br /><span style='font-size: .8em'>(this is your WunderCounter user name; this plugin will not function without a username)</span></th>
                <td colspan='2'>
                    <input type='text' name='username' id='username'
                </td>
            </tr>
                    
            <tr valign="top">
                <th scope="row" rowspan='3'>Type of Counter:</th>
                <td>
                    <input type='radio' name='type' id='type1' value='invisible' />
                </td>
                <td>
                    Invisible Counter
                </td>
            </tr>
            <tr valign='top'>
                <td>
                    <input type='radio' name='type' id='type2' value='visible-auto' />
                </td>
                <td>
                    Visible Counter (automatic)<br />
                    <span style='font-size: .8em;'>Counter is automatically inserted at the end of each page just above the footer</span>
                </td>
            </tr>
            <tr valign='top'>
                <td>
                    <input type='radio' name='type' id='type3' value='visible-manual' />
                </td>
                <td>
                    Visible Counter (manual)<br />
                    <span style='font-size: .8em;'>You must place the WunderCounter Widget into the sidebars manually.</span>
                </td>
            </tr>

            <tr valign="top">
                <th scope="row">Style:<br /><span style='font-size: .8em'>(only applies when using the 'default' style)</span></th>
                <td colspan='2'>
                    <select name='background' id='background'>
                        <?php echo $this->make_option_list($this->background,''); ?>
                    </select>
                </td>
            </tr>
            
            <tr valign='top' id='background-color-row'>
                <th scope='row'>Background Color</th>
                <td colspan='2'>
                    <select name='text_colour' id='text_colour'>
                        <?php echo $this->make_option_list($this->text_colour,''); ?>
                    </select>
                </td>
            </tr>
    
            <tr valign='top'>
                <th rowspan='2' scope='row'>Settings Level:</th>
                <td><input type='radio' name='complexity' value='simple'></td>
                <td>Simple</td>
            </tr>
    
            <tr valign='top'>
                <th rowspan='2' scope='row'>Settings Level:</th>
                <td><input type='radio' name='complexity' value='advanced'></td>
                <td>Advanced</td>
            </tr>
    
        </table>
    
        
    
        <input type="hidden" name="action" value="update" />
        <!-- <input type="hidden" name="page_options" value="new_option_name,some_other_option,option_etc" /> -->
    
        <p class="submit">
            <input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />
        </p>
    
    </form>
</div>
        
        <?php
    }
    
    /***************************
     * Widget based code
     */
    // Echo the actual widget content. Subclasses should over-ride this function
    // to generate their widget code.
    function widget($args,$instance)
    {
        extract($args,EXTR_SKIP);
        echo $before_widget;
        echo   $before_title . $instance['title'] . $after_title;
        //echo   $instance['content'];
        echo $after_widget;
    }


    // Update a particular instance.
    // This function should check that $new_instance is set correctly.
    // The newly calculated value of $instance should be returned.
    function control_update($new_instance, $old_instance)
    {
        if( !isset($new_instance['title']) ) // user clicked cancel
            return false;
        $instance = $old_instance;
        $instance['title']      = wp_specialchars( $new_instance['title'] );
        //$instance['content']    = wp_specialchars( $new_instance['content'] );
        return $instance;
    }


    // Echo a control form for the current instance.
    // The form has inputs with names like widget-ID_BASE[$number][FIELD_NAME]
    // so that all data for that instance of the widget are stored in one
    // $_POST variable: $_POST['widget-ID_BASE'][$number]
    function control_form($instance)
    {
?>
    <p>

     <label for="<?php echo $this->get_field_id('title') ?>">
      <?php _e('Title:'); ?>
      <input class="widefat" id="<?php echo $this->get_field_id('title') ?>"
       name="<?php echo $this->get_field_name('title') ?>" type="text"
       value="<?php echo htmlspecialchars($instance['title'],ENT_QUOTES) ?>" />
     </label>
    <!--
     <label for="<?php echo $this->get_field_id('content') ?>">
      <?php _e('Content:'); ?>
      <input class="widefat" id="<?php echo $this->get_field_id('content') ?>"
       name="<?php echo $this->get_field_name('content') ?>" type="text"
       value="<?php echo htmlspecialchars($instance['content'],ENT_QUOTES) ?>" />
     </label>
    -->
     <input type="hidden" id="<?php echo $this->get_field_id('submit') ?>"
      name="<?php echo $this->get_field_name('submit') ?>" value="1" />

    </p>
<?php
    }
    
}

/*

//
// Example MultiWidget. Use this as a template for your own.
//

class ExampleMultiWidget extends MultiWidget
{
  function ExampleMultiWidget()
  {
    $this->MultiWidget(
        'example-multi', // id_base
        'ExampleMulti', // name
        array('description'=>__('Widget which allows multiple instances'))
      );
  }


  // Echo the actual widget content. Subclasses should over-ride this function
  // to generate their widget code.
  function widget($args,$instance)
  {
    extract($args,EXTR_SKIP);
    echo $before_widget;
    echo   $before_title . $instance['title'] . $after_title;
    echo   $instance['content'];
    echo $after_widget;
  }


  // Update a particular instance.
  // This function should check that $new_instance is set correctly.
  // The newly calculated value of $instance should be returned.
  function control_update($new_instance, $old_instance)
  {
    if( !isset($new_instance['title']) ) // user clicked cancel
        return false;
    $instance = $old_instance;
    $instance['title'] = wp_specialchars( $new_instance['title'] );
    $instance['content'] = wp_specialchars( $new_instance['content'] );
    return $instance;
  }


  // Echo a control form for the current instance.
  // The form has inputs with names like widget-ID_BASE[$number][FIELD_NAME]
  // so that all data for that instance of the widget are stored in one
  // $_POST variable: $_POST['widget-ID_BASE'][$number]
  function control_form($instance)
  {
?>
    <p>

     <label for="<?php echo $this->get_field_id('title') ?>">
      <?php _e('Title:'); ?>
      <input class="widefat" id="<?php echo $this->get_field_id('title') ?>"
       name="<?php echo $this->get_field_name('title') ?>" type="text"
       value="<?php echo htmlspecialchars($instance['title'],ENT_QUOTES) ?>" />
     </label>

     <label for="<?php echo $this->get_field_id('content') ?>">
      <?php _e('Content:'); ?>
      <input class="widefat" id="<?php echo $this->get_field_id('content') ?>"
       name="<?php echo $this->get_field_name('content') ?>" type="text"
       value="<?php echo htmlspecialchars($instance['content'],ENT_QUOTES) ?>" />
     </label>

     <input type="hidden" id="<?php echo $this->get_field_id('submit') ?>"
      name="<?php echo $this->get_field_name('submit') ?>" value="1" />

    </p>
<?php
  }

} // end class ExampleMultiWidget


// Finally create an object for the widget-type and register it.
$example_multi = new ExampleMultiWidget();
add_action( 'widgets_init', array($example_multi,'register') );

*/
 
endif ?>

