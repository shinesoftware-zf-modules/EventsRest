<?php
namespace EventsRest\Controller;

use Zend\Mvc\Controller\AbstractRestfulController;

use Zend\View\Model\JsonModel;
use EventsRest\Service\UserService;
use Events\Service\EventCategoryServiceInterface;
use Base\Service\SettingsServiceInterface;

class CategoryController extends AbstractRestfulController {
    
    protected $dataService;
    protected $userService;
    protected $eventSettings;

    /**
     * @param UserService $userService
     * @param EventCategoryServiceInterface $dataService
     * @param SettingsServiceInterface $settings
     */
    public function __construct(
            UserService $userService,
            EventCategoryServiceInterface $dataService,
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

    private function createInfoBlock($events){

        $i = 0;
        $data = array();

        foreach($events as $result) {
            $data[$i]['id'] = $result->getId();
            $data[$i]['title'] = $result->getCategory();
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

        $data = $this->createInfoBlock($this->dataService->findVisible());

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