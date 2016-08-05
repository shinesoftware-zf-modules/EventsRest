<?php
namespace EventsRest\Controller;

use Zend\Mvc\Controller\AbstractRestfulController;
use Zend\View\Model\JsonModel;
use EventsRest\Service\UserService;
use Base\Service\LanguagesServiceInterface;
use Base\Service\SettingsServiceInterface;

class LanguageController extends AbstractRestfulController {
    
    protected $dataService;
    protected $userService;
    protected $eventSettings;
    
    /**
     * Class constructor 
     *  
     * @param EventServiceInterface $dataService
     * @param SettingsServiceInterface $settings
     */
    public function __construct(
            UserService $userService,
            LanguagesServiceInterface $dataService,
            SettingsServiceInterface $settings){
        
        $this->userService = $userService;
        $this->dataService = $dataService;
        $this->eventSettings = $settings;
        
    }

    /**
     * If the user call the http://www.itango.it/v1/events/ without secret and id parameter
     * it will be redirected in the blankpage action
     *
     * @return JsonModel
     */
    public function blankpageAction() {
        return new JsonModel(array('error' => "wrong request"));
    }

    private function createInfoBlock($records){

        $data = array();
        $i = 0;

        foreach($records as $result) {
            $data[$i] = (array)$result;
            $i++;
        }

        return $data;
    }

    public function getList() {
        $secret = $this->params()->fromRoute('secret');

        $user = $this->userService->findbySecret($secret);
        if(!$user->count()){
            return new JsonModel(array("status"=>"fail","code"=>"999", "error" => "Invalid API Token"));
        }

        $data = $this->createInfoBlock($this->dataService->findActive());

        return new JsonModel(array('data' => $data));
    }
    
    public function get($id) {
        $data = array();

        $secret = $this->params()->fromRoute('secret');

        $user = $this->userService->findbySecret($secret);
        if(!$user->count()){
            return new JsonModel(array("status"=>"fail","code"=>"999", "error" => "Invalid API Token"));
        }

        if(!empty($id)){
            $data['data'] = $this->createInfoBlock(array($this->dataService->find($id)));
        }
        
        return new JsonModel($data);
            
    }
    
}