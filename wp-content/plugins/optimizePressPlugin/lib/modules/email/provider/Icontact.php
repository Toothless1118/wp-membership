<?php

require_once(OP_MOD . 'email/ProviderInterface.php');
require_once(OP_LIB . 'vendor/icontact/iContactApi.php');

/**
 * iContact email integration provider
 * @author OptimizePress <info@optimizepress.com>
 */
class OptimizePress_Modules_Email_Provider_Icontact implements OptimizePress_Modules_Email_ProviderInterface
{
    const OPTION_NAME_USERNAME = 'icontact_username';
    const OPTION_NAME_PASSWORD = 'icontact_password';

    /**
     * @var OP_iContactApi
     */
    protected $client;

    /**
     * @var string|bool
     */
    protected $username;

    /**
     * @var string|bool
     */
    protected $password;

    /**
     * @var OptimizePress_Modules_Email_LoggerInterface
     */
    protected $logger;

    /**
     * Constructor, initializes $username and $password
     */
    public function __construct(OptimizePress_Modules_Email_LoggerInterface $logger)
    {
        /*
         * Fetching values from wp_options table
         */
        $this->username = op_get_option(self::OPTION_NAME_USERNAME);
        $this->password = op_get_option(self::OPTION_NAME_PASSWORD);

        /*
         * Initializing logger
         */
        $this->logger = $logger;
    }

    /**
     * Returns iContact API client
     * @return iClientApi
     */
    public function getClient()
    {
        if (null === $this->client) {
            $this->client = OP_iContactApi::getInstance($this->logger)->setConfig(array(
                'appId' => OP_ICONTACT_APP_ID,
                'apiUsername' => $this->username,
                'apiPassword' => $this->password
            ));
        }

        return $this->client;
    }

    /**
     * @see OptimizePress_Modules_Email_ProviderInterface::isEnabled()
     */
    public function isEnabled()
    {
        return $this->username === false || $this->password === false ? false : true;
    }

    /**
     * @see OptimizePress_Modules_Email_ProviderInterface::getLists()
     */
    public function getLists()
    {
        $lists = $this->getClient()->getLists();

        $this->logger->info('Lists: ' . print_r($lists, true));

        return $lists;
    }

    /**
     * @see OptimizePress_Modules_Email_ProviderInterface::getData()
     */
    public function getData()
    {
        $data = array(
            'lists' => array()
        );

        /*
         * List parsing
         */
        $lists = $this->getLists();
        if (count($lists) > 0) {
            $extras = $this->getExtraFields();
            foreach ($lists as $list) {
                $data['lists'][$list->listId] = array('name' => $list->name, 'fields' => $extras);
            }
        }

        $this->logger->info('Formatted lists: ' . print_r($data, true));

        return $data;
    }

    /**
     * Returns hardcoded extra fields that iContact supports
     * @return array
     */
    protected function getExtraFields()
    {
        return array(
            'prefix' => 'Prefix',
            'name' => 'FirstName',
            'last_name' => 'LastName',
            'sufix' => 'Suffix',
            'street' => 'Street',
            'street2' => 'Street2',
            'city' => 'City',
            'state' => 'State',
            'postal_code' => 'PostalCode',
            'phone' => 'Phone',
            'fax' => 'Fax',
            'business' => 'Business'
        );
    }

    /**
     * Searches for possible form fields from POST and adds them to the collection
     * @return null|array     Null if no value/field found
     */
    protected function prepareMergeVars()
    {
        $vars = array();
        $allowed = array_keys($this->getExtraFields());

        foreach ($allowed as $name) {
            $vars[$name] = op_post($name);
        }

        if (count($vars) === 0) {
            $vars = null;
        }

        return $vars;
    }

    /**
     * @see OptimizePress_Modules_Email_ProviderInterface::subscribe()
     */
    public function subscribe($data)
    {
        if (isset($data['list']) && isset($data['email'])) {

            $mergeVars = $this->prepareMergeVars();

            try {
                $contact = $this->getClient()->addContact(
                    $data['email'], 'normal', $mergeVars['prefix'], $mergeVars['name'], $mergeVars['last_name'], $mergeVars['sufix'], $mergeVars['street'],
                    $mergeVars['street2'], $mergeVars['city'], $mergeVars['state'], $mergeVars['postal_code'], $mergeVars['phone'], $mergeVars['fax'], $mergeVars['business']
                );
                $this->logger->notice('Adding contact status: ' . print_r($contact, true));

                $status = $this->getClient()->subscribeContactToList($contact->contactId, $data['list']);

                $this->logger->notice('Subscription status: ' . print_r($status, true));

                /*
                 * API returns empty status if user exists
                */
                if (empty($status)) {
                    if (isset($_POST['already_subscribed_url']) && op_post('already_subscribed_url') !== '') {
                        $_POST['redirect_url'] = op_post('already_subscribed_url');
                    } else {
                        if (isset($_POST['redirect_url'])) {
                            $action = sprintf(__('<a href="javascript:history.go(-1);">Return to previous page</a> or <a href="%s">continue</a>.', 'optimizepress'), op_post('redirect_url'));
                        } else {
                            $action = __('<a href="javascript:history.go(-1);">Return to previous page.</a>', 'optimizepress');
                        }
                        op_warning_screen(
                            __('This email is already subscribed...', 'optimizepress'),
                            __('Optin form - Warning', 'optimizepress'),
                            $action
                        );
                    }
                }

                return true;
            } catch (Exception $e) {
                $this->logger->error('Error ' . $e->getCode() . ': ' . $e->getMessage());

                return false;
            }
        } else {
            $this->logger->alert('Mandatory information not present [list and/or email address]');
            wp_die('Mandatory information not present [list and/or email address].');

            return false;
        }
    }

    /**
     * @see OptimizePress_Modules_Email_ProviderInterface::register()
     */
    public function register($list, $email, $fname, $lname)
    {
        $this->logger->info('Registering user: ' . print_r(func_get_args(), true));

        try {
            $contact = $this->getClient()->addContact($email, 'normal', $mergeVars['prefix'], $fname, $lname);

            $this->logger->notice('Adding contact status: ' . print_r($contact, true));

            $status = $this->getClient()->subscribeContactToList($contact->contactId, $list);

            $this->logger->notice('Registration status: ' . print_r($status, true));

            return true;
        } catch (Exception $e) {
            $this->logger->error('Error ' . $e->getCode() . ': ' . $e->getMessage());

            return false;
        }
    }

    /**
     * @see OptimizePress_Modules_Email_ProviderInterface::getListFields()
     */
    public function getListFields($listId)
    {
        $fields = $this->getExtraFields();

        $this->logger->info("Fields for list $listId: " . print_r($fields, true));

        return array('fields' => $fields);
    }

    /**
     * @see OptimizePress_Modules_Email_ProviderInterface::getItems()
     */
    public function getItems()
    {
        $data = $this->getData();

        $this->logger->info('Items: ' . print_r($data, true));

        return $data;
    }
}