<?php if  ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * The Signup controller - handles all account registration processes.
 *
 * @author Nick Niebaum <nick@nickniebaum.com>
 */
class Signup extends App_Controller
{
    public function __construct()
    {
        $this->models=array_merge($this->models,array('pricing'));
        parent::__construct();
        $this->load->library('session');
        $this->data['has_errors']=FALSE;
    }

    public function index()
    {
        // We'll do it your way for now, Bain.
        config_merge('meta',array(
            'title' => 'Sign Up | Step 1 | RISKPIX',
            'description' => 'Find out more about our custom underwriting solutions.'
        ));

        // Who knows?
        $this->data['body_class'] = 'bg5';

        $validation_rules=array(
            array(
                'field'=>'company',
                'label'=>'Company',
                'rules'=>'trim|required',
            ),
            array(
                'field'=>'name',
                'label'=>'Name',
                'rules'=>'trim|required',
            ),
            array(
                'field'=>'email',
                'label'=>'E-mail',
                'rules'=>'trim|required|email|is_unique[user.email]',
            ),
            array(
                'field'=>'email2',
                'label'=>'Confirm E-mail',
                'rules'=>'trim|required|email|matches[email]',
            ),
            array(
                'field'=>'password',
                'label'=>'Password',
                'rules'=>'trim|required',
            ),
            array(
                'field'=>'password2',
                'label'=>'Confirm Password',
                'rules'=>'trim|required|matches[password]',
            ),
        );

        // http://benramsey.com/blog/2013/03/introducing-array-column-in-php-5-dot-5/
        $data_keys=array_column($validation_rules,'field');

        // Load the form validation library
        $this->load->library('form_validation');
        // And set validation rules
        $this->form_validation->set_rules($validation_rules);
        $this->form_validation->set_message('is_unique','%s is in use by another member. If you are the owner of that account, please consider resetting your password if you have forgot it.');
        // If the form was submitted and no errors were found
        if($this->form_validation->run()!==FALSE)
        {
            // Create a data structure to save the data
            $registration_data=array(
                'step1'=>$this->input->post($data_keys),
            );
            // Save it to the session
            $this->session->set_userdata('registration',$registration_data);
            // And proceed
            redirect('signup/step2');
        }
        // If the form was submitted with errors
        elseif($this->input->post())
        {
            // Set a flag to display them in the view
            $this->data['has_errors']=TRUE;
        }
    }

    public function step2()
    {
        // Retrieve the previous step data
        $registration_data=$this->session->userdata('registration');
        // Check that the previous step has been completed
        if($registration_data===FALSE || empty($registration_data['step1']))
        {
            redirect('signup');
        }

        config_merge('meta',array(
            'title' => 'Sign Up | Step 2 | RISKPIX',
            'description' => 'Find out more about our custom underwriting solutions.'
        ));

        $this->data['body_class'] = 'bg5';
        $this->data['states']=states_array(array(''=>'State'));

        $validation_rules=array(
            array(
                'field'=>'address',
                'label'=>'Billing Address',
                'rules'=>'trim|required',
            ),
            array(
                'field'=>'city',
                'label'=>'City',
                'rules'=>'trim|required',
            ),
            array(
                'field'=>'state',
                'label'=>'State',
                'rules'=>'trim|required|exact_length[2]',
            ),
            array(
                'field'=>'zip',
                'label'=>'Zip Code',
                'rules'=>'trim|required|min_length[5]',
            ),
            array(
                'field'=>'phone',
                'label'=>'Company Phone',
                'rules'=>'trim|required|valid_phone',
            ),
            array(
                'field'=>'mobile',
                'label'=>'Mobile Phone',
                'rules'=>'trim|valid_phone',
            ),
        );

        // http://benramsey.com/blog/2013/03/introducing-array-column-in-php-5-dot-5/
        $data_keys=array_column($validation_rules,'field');

        // Load the form validation library
        $this->load->library('form_validation');
        // And set validation rules
        $this->form_validation->set_rules($validation_rules);
        // If the form was submitted and no errors were found
        if($this->form_validation->run()!==FALSE)
        {
            // Add the collected information to the data structure
            $registration_data['step2']=$this->input->post($data_keys);
            // Save it to the session
            $this->session->set_userdata('registration',$registration_data);
            // And proceed
            redirect('signup/step3');
        }
        // If the form was submitted with errors
        elseif($this->input->post())
        {
            // Set a flag to display them in the view
            $this->data['has_errors']=TRUE;
        }



        // echo 'We have data..?';
        // var_dump($this->session->userdata('registration'));
        // exit;
/*
        echo($this->session->flashdata('cid'));

        //$this->load->library('session');
        config_merge('meta',array(
            'title' => 'Sign Up | RISKPIX',
            'description' => 'Find out more about our custom underwriting solutions.'
        ));

        $rules = array(
            array('address', 'required'),
            array('city', 'required'),
            array('state', 'required'),
            array('zip', 'required'),
            array('phone', 'required'),
            array('phone', 'phone'),
            array('mobile', 'phone')
        );
        //  $this->data['body_class'] = 'bg5';
        $this->load->library('valid');

        // did we get some datas?
        $post = $this->input->post();
        if(!$post) // nope
        {
            $this->valid->fill_empty($this->data, $rules);
            return;
        }
        $err = $this->valid->validate($post, $rules);

        if($err)
        {
            $this->errors[] = $err;
            $this->data = array_merge($this->data, $post);
            return;
        } else {

            //echo $this->session->flashdata('cid');

            //save data
            $cid = $this->session->flashdata('cid');
            $this->company->update($cid,array(
                'c_address'=>$post['address'],
                'c_city'=>$post['city'],
                'c_state'=>$post['state'],
                'c_zipcode'=>$post['zip'],
                'c_phone_main'=>$post['phone']
            ));
            $uid = $this->session->flashdata('uid');
            $this->user->update($uid,array(
                'phone'=>$post['phone'],
            ));

            //$this->valid->make_empty($this->data, $rules);
            //$this->notifications[] = 'Your message has been received! You will be contacted shortly.';

            echo($cid);

            //redirect('/signup3');

        }
*/
    }

    public function step3()
    {
        // Retrieve the previous step data
        $registration_data=$this->session->userdata('registration');
        // Check that the previous step has been completed
        if($registration_data===FALSE || empty($registration_data['step2']))
        {
            redirect('signup');
        }

        config_merge('meta',array(
            'title' => 'Sign Up | Step 3 | RISKPIX',
            'description' => 'Find out more about our custom underwriting solutions.'
        ));

        // $this->load->model('pricing');

        $this->data['body_class'] = 'bg5';
        $this->data['pricing_options']=$this->pricing->get_pricing_options();

        $validation_rules=array(
            array(
                'field'=>'pricing',
                'label'=>'Monthly Plan',
                'rules'=>'trim|required',
            ),
            array(
                'field'=>'discount',
                'label'=>'Discount Code',
                'rules'=>'trim',
            ),
        );

        // http://benramsey.com/blog/2013/03/introducing-array-column-in-php-5-dot-5/
        $data_keys=array_column($validation_rules,'field');

        // Load the form validation library
        $this->load->library('form_validation');
        // And set validation rules
        $this->form_validation->set_rules($validation_rules);
        // If the form was submitted and no errors were found
        if($this->form_validation->run()!==FALSE)
        {
            // Add the collected information to the data structure
            $registration_data['step3']=$this->input->post($data_keys);
            // Save it to the session
            $this->session->set_userdata('registration',$registration_data);
            // And proceed
            redirect('signup/confirm');
        }
        // If the form was submitted with errors
        elseif($this->input->post())
        {
            // Set a flag to display them in the view
            $this->data['has_errors']=TRUE;
        }
/*
        config_merge('meta',array(
            'title' => 'Sign Up | RISKPIX',
            'description' => 'Find out more about our custom underwriting solutions.'
        ));

        $rules = array(
            array('pricing', 'required'),
            array('terms', 'required'),
            );

        $this->data['pricing'] = $this->pricing->get_pricing();

        //  $this->data['body_class'] = 'bg5';
        $this->load->library('valid');

        // did we get some datas?
        $post = $this->input->post();
        if(!$post) // nope
        {
            $this->valid->fill_empty($this->data, $rules);
            return;
        }
        $err = $this->valid->validate($post, $rules);

        if($err)
        {
            $this->errors[] = $err;
            $this->data = array_merge($this->data, $post);
            $this->data['pricing'] = $pricing;
            return;
        } else {

            //save data
            
            // $this->company->update(1,array(
            //     'c_address'=>$post['address'],
            //     'c_city'=>$post['city'],
            //     'c_state'=>$post['state'],
            //     'c_zipcode'=>$post['zip'],
            //     'c_phone_main'=>$post['phone']
            // ));
            // $this->user->update(42,array(
            //     'phone'=>$post['phone'],
            // ));
            

            $this->valid->make_empty($this->data, $rules);
            //$this->notifications[] = 'Your message has been received! You will be contacted shortly.';

            redirect('/signuppay');

        }

        $this->valid->make_empty($this->data, $rules);
        //$this->notifications[] = 'Your message has been received! You will be contacted shortly.';
        //redirect('/authentication/log_in');
*/
    }

    public function confirm()
    {
        // Retrieve the previous step data
        $registration_data=$this->session->userdata('registration');
        // Check that the previous step has been completed
        if($registration_data===FALSE || empty($registration_data['step3']))
        {
            redirect('signup');
        }
        var_dump($registration_data);
/*
        $is_test_transaction = true;
        $stripe_public_key = 'pk_live_uKzioeuq4V96VGQDFkaEZcKj';
        $test_stripe_public_key = 'pk_test_kL6fXiWecirqZRc8zNicu0oX';
        $test_card_number = '4242424242424242';
        $test_cvc_code = '123';
        $test_stripe_secret_key = 'sk_test_9YTdExpSIuVADgrueOS2d4VD';
        $stripe_secret_key = 'sk_live_iQ4yNAvvCwT9vzvsTYSm6WY9';

        $this->data['is_test_transaction'] = $is_test_transaction;
        $this->data['stripe_public_key'] = $stripe_public_key;
        $this->data['test_stripe_public_key'] = $test_stripe_public_key;
        $this->data['test_card_number'] = $test_card_number;
        $this->data['test_cvc_code'] = $test_cvc_code;

        //----STRIPE----//
        $this->load->library('stripe_api');
        //$this->merchant->load('stripe');

                // Set your secret key: remember to change this to your live secret key in production
                // See your keys here https://dashboard.stripe.com/account
                if ($is_test_transaction == true) {
                    Stripe::setApiKey($test_stripe_secret_key);
                } else {
                    Stripe::setApiKey($stripe_secret_key);
                }

                // Get the credit card details submitted by the form
                $token = $_POST['stripeToken'];

                // Create the charge on Stripe's servers - this will charge the user's card
                try {
                $charge = Stripe_Charge::create(array(
                  "amount" => 1000, // amount in cents, again
                  "currency" => "usd",
                  "card" => $token,
                  "description" => "jd@test.com")
                );
                } catch(Stripe_CardError $e) {
                  // The card has been declined
                }
*/
    }
}

/* End of file signup.php */
/* Location: ./application/controllers/signup.php */