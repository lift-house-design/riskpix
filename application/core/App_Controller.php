<?php
/**
 * A base controller for CodeIgniter with view autoloading, layout support,
 * model loading, helper loading, asides/partials and per-controller 404
 *
 * @link http://github.com/jamierumbelow/codeigniter-base-controller
 * @copyright Copyright (c) 2012, Jamie Rumbelow <http://jamierumbelow.net>
 */

class App_Controller extends CI_Controller
{

    /* --------------------------------------------------------------
     * VARIABLES
     * ------------------------------------------------------------ */
    
    protected $js=array();
    protected $min_js=array(
        'jquery-1.10.2.min.js',
        'modernizr-2.6.2.min.js',
        'jquery.placeholder.js',
        'application.js',
    );
    
    protected $css = array();
    protected $min_css = array(
        'normalize.css',
        'application.css',
        'mobile.css'
    );
    protected $less_css = array();

    protected $asides = array(
        'topbar'=>'topbar',
        'notifications'=>'notifications',
        'footer'=>'footer',
        'bottombar'=>'bottombar',
        'seo'=>'seo',
        'analytics' => 'analytics',
    );

    protected $helpers = array('config');

    /**
     * The current request's view. Automatically guessed
     * from the name of the controller and action
     */
    protected $view = '';

    /**
     * An array of variables to be passed through to the
     * view, layout and any asides
     */
    protected $data = array();

    /**
     * The name of the layout to wrap around the view.
     */
    protected $layout;

    /**
     * A list of models to be autoloaded
     */
    protected $models = array('user','configuration');

    /**
     * A formatting string for the model autoloading feature.
     * The percent symbol (%) will be replaced with the model name.
     */
    protected $model_string = '%_model';
    
    protected $title;
    
    protected $authenticate=FALSE;

    protected $authentication_redirect='/authentication/log_in';

    protected $errors;

    protected $notifications;
    /* --------------------------------------------------------------
     * GENERIC METHODS
     * ------------------------------------------------------------ */

    /**
     * Initialise the controller, tie into the CodeIgniter superobject
     * and try to autoload the models and helpers
     */
    public function __construct()
    {
        parent::__construct();

        $this->_load_helpers();
        $this->_load_models();
        $this->_save_target_url();
        $this->_load_configuration();
    }

    protected function _save_target_url()
    {
        $url = $this->input->cookie('target_url');
        if(empty($url) && substr($_SERVER['REQUEST_URI'],2,1) == '/')
            $this->input->set_cookie('target_url', $_SERVER['REQUEST_URI']);
    }

    // load custom configuration from database
    protected function _load_configuration()
    {
        $this->configuration->load();
    }

    /*
     * Output json and die, great for AJAX handlers
     */ 
    public function _json($data)
    {
        header('Content-type: application/json');
        echo json_encode($data);
        die;
    }

    /**
     * Ensure the user has access to this page
     */
    protected function authenticate()
    {
        if(empty($this->authenticate))
        { // no authentication required
            return TRUE;
        }
        elseif(!$this->user->logged_in)
        { // user not logged in
            return FALSE;
        }
        elseif(!is_array($this->authenticate))
        {
            $this->authenticate = array($this->authenticate);
        }

        foreach($this->authenticate as $allowed_role)
        {
           if($this->user->has_role($allowed_role)===TRUE)
           {
                return TRUE;
            }
        }
    
        // Do not authenticate if they do not have a valid role or
        // a role is not correctly set
        return FALSE;
    }

    /* --------------------------------------------------------------
     * VIEW RENDERING
     * ------------------------------------------------------------ */
    
    protected function _load_data()
    {
        // Trying to purge this LESS crap out of the project
        if(!empty($this->less_css))
        {
            echo 'LESS CSS FILES FOUND..';
            var_dump($this->less_css);
            exit;
        }

        // Set the basic data
        $this->data['css'] = array_merge($this->css,$this->min_css);


        $this->data['js'] = array_merge($this->js,$this->min_js);

        $this->data['site_name'] = $this->config->item('site_name');
        $this->data['meta'] = $this->config->item('meta');
        $this->data['copyright'] = $this->config->item('copyright');
        $this->data['ga_code'] = $this->config->item('ga_code');
        
        // Set the global data
        $this->data['slug_id_string']=implode('-',$this->uri->rsegment_array());
        $this->data['logged_in']=$this->user->logged_in;
        $this->data['user']=$this->session->userdata('user');
        $this->data['errors']=$this->errors;
        $this->data['notifications']=$this->notifications;
    }
    
    /**
     * Override CodeIgniter's despatch mechanism and route the request
     * through to the appropriate action. Support custom 404 methods and
     * autoload the view into the layout.
     */
    public function _remap($method)
    {

        if (method_exists($this, $method))
        {
            call_user_func_array(array($this, $method), array_slice($this->uri->rsegments, 2));

            if($this->authenticate()===FALSE)
            {
                redirect($this->authentication_redirect);
            }
        }
        else
        {
            if (method_exists($this, '_404'))
            {
                call_user_func_array(array($this, '_404'), array($method));
            }
            else
            {
                show_404(strtolower(get_class($this)).'/'.$method);
            }
        }

        $this->_load_view();
    }

    /**
     * Automatically load the view, allowing the developer to override if
     * he or she wishes, otherwise being conventional.
     */
    protected function _load_view()
    {
        // Check for authentication
        if($this->authenticate===TRUE && $this->user->logged_in!==TRUE)
        {
            redirect('login');
        }
        
        // If $this->view == FALSE, we don't want to load anything
        if ($this->view !== FALSE)
        {
            // Populate data that exists for every page
            $this->_load_data();

            // Do we have any asides? Load them.
            if (!empty($this->asides))
            {
                foreach ($this->asides as $name => $file)
                {
                    $this->data['yield_'.$name] = $this->load->view('asides/' . $file, $this->data, TRUE);
                }
            }
            // If $this->view isn't empty, load it. If it isn't, try and guess based on the controller and action name
            $view = (!empty($this->view)) ? $this->view : $this->router->directory . $this->router->class . '/' . $this->router->method;

            // Load the view into $yield
            $this->data['yield'] = $this->load->view($view, $this->data, TRUE);

            $layout = FALSE;

            // If we didn't specify the layout, try to guess it
            if (!isset($this->layout))
            {
                if (file_exists(APPPATH . 'views/layouts/' . $this->router->class . '.php'))
                {
                    $layout = 'layouts/' . $this->router->class;
                }
                else
                {
                    $layout = 'layouts/application';
                }
            }

            // If we did, use it
            else if ($this->layout !== FALSE)
            {
                $layout = $this->layout;
            }

            // If $layout is FALSE, we're not interested in loading a layout, so output the view directly
            if ($layout == FALSE)
            {
                $this->output->set_output($this->data['yield']);
            }

            // Otherwise? Load away :)
            else
            {
                $this->load->view($layout, $this->data);
            }
        }
    }

    /* --------------------------------------------------------------
     * MODEL LOADING
     * ------------------------------------------------------------ */

    /**
     * Load models based on the $this->models array
     */
    private function _load_models()
    {
        foreach ($this->models as $model)
        {
            $this->load->model($this->_model_name($model), $model);
        }
    }

    /**
     * Returns the loadable model name based on
     * the model formatting string
     */
    protected function _model_name($model)
    {
        return str_replace('%', $model, $this->model_string);
    }

    /* --------------------------------------------------------------
     * HELPER LOADING
     * ------------------------------------------------------------ */

    /**
     * Load helpers based on the $this->helpers array
     */
    private function _load_helpers()
    {
        foreach ($this->helpers as $helper)
        {
            $this->load->helper($helper);
        }
    }
}