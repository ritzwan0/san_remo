<?php
/**Ritz
 *  @author Xavier Sanz <xsanz@pss.com>
 *  @copyright Copyright (c) 2017 PSS (http://www.pss.com)
 *  @package PSS
 */

namespace PSS\CRM\Helper;

class TicketService
{

    private $dataHelper;

    const CRM_USER = 'crm/ticket_service/';


    /**
     * UserService constructor.
     * @param Data $dataHelper
     */

    public function __construct(
        \PSS\CRM\Helper\Data $dataHelper
    ){
        $this->dataHelper = $dataHelper;
    }

    public function getWSDLUrl() {
        return $this->dataHelper->getConfig($this::CRM_USER . 'wsdl');
    }

    public function getSSLVerify() {
        return $this->dataHelper->getConfig($this::CRM_USER . 'verify_ssl');
    }

    public function getAuthenticate() {
        return $this->dataHelper->getConfig($this::CRM_USER . 'authenticate');
    }

    public function getUser() {
        return $this->dataHelper->getConfig($this::CRM_USER . 'user');
    }

    public function getPassword() {
        return $this->dataHelper->getConfig($this::CRM_USER . 'password');
    }

    public function getPISource() {
        return $this->dataHelper->getConfig($this::CRM_USER . 'processinfo_source');
    }

    public function getPIOrigin() {
        return $this->dataHelper->getConfig($this::CRM_USER . 'processinfo_origin');
    }

    public function getPIUser() {
        return $this->dataHelper->getConfig($this::CRM_USER . 'processinfo_user');
    }

    public function getDebugXML() {
        return $this->dataHelper->getConfig($this::CRM_USER . 'debugXML');
    }

    public function getUseQueue() {
        return $this->dataHelper->getConfig($this::CRM_USER . 'useQueue');
    }

    public function getDebug() {
        return $this->dataHelper->getConfig($this::CRM_USER . 'debug');
    }

    public function getDebugEmail() {
        return $this->dataHelper->getConfig($this::CRM_USER . 'email');
    }

    public function encryptPassword($password) {
        return $this->dataHelper->encryptPassword($password);
    }
    /**
     * Get Points from the webservice Response
     * @param $responseWS
     * @return float|int
     */
    public function extractPointsFromWebserviceResponse($responseWS) {
        $generatePoints = 0;
        if(!isset($responseWS['WcfGestionarTicketResult']['a:tickets']['a:Ticket']['a:puntosGenerados'])) {
            return $generatePoints;
        }
        $generatePoints = (int)$responseWS['WcfGestionarTicketResult']['a:tickets']['a:Ticket']['a:puntosGenerados'];
        return $generatePoints;
    }
}