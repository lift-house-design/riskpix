<?php if  ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * The Signup controller - handles all account registration processes.
 *
 * @author Nick Niebaum <nick@nickniebaum.com>
 */
class Signup extends App_Controller
{
    // private $_form_fill=array(
    //     'index'=>array(
    //         'company'=>'Nick Niebaum Enterprises',
    //         'name'=>'Nick Niebaum',
    //         'email'=>'nickniebaum@gmail.com',
    //         'confirm_email'=>'nickniebaum@gmail.com',
    //         'password'=>'password',
    //         'confirm_password'=>'password',
    //     ),
    //     'step2'=>array(
    //         'address'=>'1723 17th St., Apt. A',
    //         'city'=>'Nitro',
    //         'state'=>'WV',
    //         'zip'=>'25143',
    //         'phone'=>'(304) 871-6066',
    //         'mobile'=>'(304) 866-000',
    //     ),
    //     'step3'=>array(
    //         'pricing'=>'3',
    //         'discount'=>'DISC333',
    //     ),
    // );

    public function __construct()
    {
        $this->models[]='pricing';
        $this->models[]='discount';
        parent::__construct();
        $this->load->library('session');
        $this->data['has_errors']=FALSE;
    }

    // public function start_testing($step=1)
    // {
    //     $signup_pages=array(
    //         'signup',
    //         'signup/step2',
    //         'signup/step3',
    //         'signup/confirm',
    //     );

    //     $registration_data=array(
    //             'user' => array(
    //                 'email' => 'nickniebaum@gmail.com',
    //                 'password' => 'password',
    //                 'first_name' => 'Nick',
    //                 'last_name' => 'Niebaum',
    //                 'phone' => '(304) 871-6067',
    //             ),
    //             'company' => array(
    //                 'c_name' => 'Nick Niebaum Enterprises',
    //                 'c_address' => '1723 17th. St., Apt. #A',
    //                 'c_city' => 'Nitro',
    //                 'c_state' => 'WV',
    //                 'c_zipcode' => '25143',
    //                 'c_phone_main' => '(304) 871-6066',
    //             ),
    //             'billing' => array(),
    //     );

    //     $this->session->set_userdata('registration',$registration_data);
    //     $this->session->set_userdata('registration_step',4);

    //     redirect($signup_pages[$step-1]);
    // }

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
                'field'=>'confirm_email',
                'label'=>'Confirm E-mail',
                'rules'=>'trim|required|email|matches[email]',
            ),
            array(
                'field'=>'password',
                'label'=>'Password',
                'rules'=>'trim|required',
            ),
            array(
                'field'=>'confirm_password',
                'label'=>'Confirm Password',
                'rules'=>'trim|required|matches[password]',
            ),
        );

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
                'user'=>$this->input->post(array(
                    'email',
                    'password',
                )),
                'company'=>$this->input->post(array(
                    'company'=>'c_name',
                )),
                'plan'=>array(),
            );

            // Add first and last name as separate data fields
            $name=explode(' ',$this->input->post('name'));
            $registration_data['user']['first_name']=$name[0];
            $registration_data['user']['last_name']=isset($name[1]) ? $name[1] : '';
            
            // Save it to the session
            $this->session->set_userdata('registration',$registration_data);
            $this->session->set_userdata('registration_step',2);
            
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
        // Check that the previous step has been completed
        if($this->session->userdata('registration_step') < 2)
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

        // Load the form validation library
        $this->load->library('form_validation');
        // And set validation rules
        $this->form_validation->set_rules($validation_rules);
        // If the form was submitted and no errors were found
        if($this->form_validation->run()!==FALSE)
        {
            // Retrieve the previous step data
            $registration_data=$this->session->userdata('registration');
            // Add the collected information to the data structure
            $registration_data['company']=array_merge($registration_data['company'],$this->input->post(array(
                'address'=>'c_address',
                'city'=>'c_city',
                'state'=>'c_state',
                'zip'=>'c_zipcode',
                'phone'=>'c_phone_main',
            )));
            $registration_data['user']=array_merge($registration_data['user'],$this->input->post(array(
                'mobile'=>'phone',
            )));

            // Save it to the session
            $this->session->set_userdata('registration',$registration_data);
            $this->session->set_userdata('registration_step',3);

            // And proceed
            redirect('signup/step3');
        }
        // If the form was submitted with errors
        elseif($this->input->post())
        {
            // Set a flag to display them in the view
            $this->data['has_errors']=TRUE;
        }
    }

    public function step3()
    {
        // Check that the previous step has been completed
        if($this->session->userdata('registration_step') < 3)
        {
            redirect('signup');
        }

        config_merge('meta',array(
            'title' => 'Sign Up | Step 3 | RISKPIX',
            'description' => 'Find out more about our custom underwriting solutions.'
        ));

        $this->data['body_class'] = 'bg5';
        $this->data['pricing_options']=$this->pricing->get_dropdown();
        $this->data['rollover_expirations']=$this->pricing->get_rollover_expirations();

        $validation_rules=array(
            array(
                'field'=>'pricing',
                'label'=>'Monthly Plan',
                'rules'=>'trim|required',
            ),
            array(
                'field'=>'discount',
                'label'=>'Discount Code',
                'rules'=>'trim|callback_discount_exists',
            ),
        );

        // Load the form validation library
        $this->load->library('form_validation');
        // And set validation rules
        $this->form_validation->set_rules($validation_rules);
        $this->form_validation->set_message('discount_exists','There are no discounts with that code to apply.');
        // If the form was submitted and no errors were found
        if($this->form_validation->run()!==FALSE)
        {
            // Retrieve the previous step data
            $registration_data=$this->session->userdata('registration');

            if($discount_code=$this->input->post('discount'))
            {
                $discount=$this->discount->get_by_code($discount_code);
            }
            else
            {
                $discount=array();
            }

            if($pricing_id=$this->input->post('pricing'))
            {
                $pricing=$this->pricing->get($pricing_id);
            }
            else
            {
                $pricing=array();
            }

            if(!empty($discount))
            {
                $registration_data['plan']=array(
                    'volume'=>$discount['dc_volume'],
                    'price'=>$discount['dc_price'],
                    'rollover'=>FALSE,
                    'rollover_months'=>NULL,
                    'discount'=>$discount['dc_code'],
                );
            }
            elseif(!empty($pricing))
            {
                $registration_data['plan']=array(
                    'volume'=>$pricing['p_volume'],
                    'price'=>$pricing['p_price'],
                    'rollover'=>($pricing['p_roll_over']=='roll'),
                    'rollover_months'=>$pricing['p_roll_months'],
                    'discount'=>FALSE,
                );
            }

            if(!empty($registration_data['plan']))
            {
                // Save it to the session
                $this->session->set_userdata('registration',$registration_data);
                $this->session->set_userdata('registration_step',4);

                // And proceed
                redirect('signup/confirm');
            }
            else
            {
                $this->data['has_errors']=TRUE;
                $this->form_validation->set_error('An unknown error occurred determining your plan.');
            }
        }
        // If the form was submitted with errors
        elseif($this->input->post())
        {
            // Set a flag to display them in the view
            $this->data['has_errors']=TRUE;
        }
    }

    public function discount_exists($str)
    {
        if(empty($str))
        {
            return TRUE;
        }
        else
        {
            $discount=$this->discount->get_by_code($str);
            return !empty($discount);
        }
    }

    public function confirm()
    {
        // Check that the previous step has been completed
        if($this->session->userdata('registration_step') < 4)
        {
            redirect('signup');
        }

        // Retrieve the previous step data
        $registration_data=$this->session->userdata('registration');
        $user_data=$registration_data['user'];
        $company_data=$registration_data['company'];
        $plan_data=$registration_data['plan'];

        $validation_rules=array(
            array(
                'field'=>'accept_terms',
                'label'=>'Terms & Conditions',
                'rules'=>'required',
            ),
        );

        $this->load->library('form_validation');
        $this->form_validation->set_rules($validation_rules);
        // If the form was submitted and no errors were found
        if($this->form_validation->run()!==FALSE)
        {
            redirect('signup/success');
        }
        elseif($this->input->post())
        {
            $this->data['has_errors']=TRUE;
        }

        $this->data['user_data']=$user_data;
        $this->data['company_data']=$company_data;
        $this->data['plan_data']=$plan_data;
        $this->data['total']=$plan_data['volume']*$plan_data['price'];
        $this->data['states']=states_array(array(''=>'State'));
        $this->data['months']=array('01','02','03','04','05','06','07','08','09','10','11','12');
        $this->data['years']=range(date('Y'),date('Y')+10);

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

    public function success()
    {
        config_merge('meta',array(
            'title' => 'Sign Up | Registration Successful | RISKPIX',
            'description' => 'Find out more about our custom underwriting solutions.'
        ));
    }

    public function test1()
    {
        $this->view=FALSE;
        $a=$this->discount->get_by(array('dc_code'=>'asdf'));
        var_dump($a);
    }
}

/* End of file signup.php */
/* Location: ./application/controllers/signup.php */