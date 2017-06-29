<?php

require_once(OP_MOD . 'email/ProviderInterface.php');
require_once(OP_MOD . 'email/provider/OfficeAutopilot.php');
require_once(OP_LIB . 'vendor/officeautopilot/OAPAPI.php');

/**
 * Ontraport email integration provider
 * @author OptimizePress <info@optimizepress.com>
 */
class OptimizePress_Modules_Email_Provider_Ontraport extends OptimizePress_Modules_Email_Provider_OfficeAutopilot implements OptimizePress_Modules_Email_ProviderInterface
{
    const OPTION_NAME_APP_ID    = 'ontraport_app_id';
    const OPTION_NAME_API_KEY   = 'ontraport_api_key';

    protected $host             = 'http://api.moon-ray.com/';

    /**
     * @var OptimizePress_Modules_Email_LoggerInterface
     */
    protected $logger;

    public function __construct(OptimizePress_Modules_Email_LoggerInterface $logger)
    {
        $this->appId    = op_get_option(self::OPTION_NAME_APP_ID);
        $this->apiKey   = op_get_option(self::OPTION_NAME_API_KEY);

        /*
         * Initializing logger
         */
        $this->logger = $logger;
    }

    public function getClient()
    {
        if (null === $this->client) {
            $this->client = new OP_OAPAPI(array('AppID' =>  $this->appId, 'Key' => $this->apiKey, 'Host' => $this->host));
            $this->client->set_logger($this->logger);
        }

        return $this->client;
    }
}