<?php
namespace EventsRest\Controller;

use Zend\Mvc\Controller\AbstractRestfulController;
use OAuth2\Server as OAuth2Server;
use Zend\View\Model\JsonModel;
use EventsRest\Service\UserService;
use Events\Service\EventServiceInterface;
use Base\Service\SettingsServiceInterface;

class EventsController extends AbstractRestfulController {
    
    protected $eventService;
    protected $userService;
    protected $eventSettings;
    protected $formfilter;
    protected $form;

    /**
     * Class constructor 
     *  
     * @param EventServiceInterface $eventService
     * @param \Events\Form\EventForm $form
     * @param \Events\Form\EventFilter $filter
     * @param SettingsServiceInterface $settings
     */
    public function __construct(
            UserService $userService,
            EventServiceInterface $eventService,
            \Events\Form\EventForm $form,
            \Events\Form\EventFilter $formfilter, 
            SettingsServiceInterface $settings){

        $this->userService = $userService;
        $this->eventService = $eventService;
        $this->form = $form;
        $this->formfilter = $formfilter;
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
            $data[$i]['title'] = $result->getTitle();
            $data[$i]['start'] = $result->getStart();
            $data[$i]['end'] = $result->getEnd();
            $data[$i]['address'] = $result->getAddress();
            $data[$i]['city'] = $result->getCity();
            $data[$i]['parent_id'] = $result->getParentId();
            $data[$i]['url'] = $result->getSlug() . ".html";
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

        $data = $this->createInfoBlock($this->eventService->getEventsbyUser($user->current()->getUserId()));

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
            $data['data'] = $this->createInfoBlock(array($this->eventService->find($id)));
        }
        
        return new JsonModel($data);
            
    }
    
    public function create($data) {
        $form = $this->form;

        $secret = $this->params()->fromRoute('secret');

        $user = $this->userService->findbySecret($secret);
        if(!$user->count()){
            return new JsonModel(array("status"=>"fail","code"=>"999", "error" => "Invalid API Token"));
        }

        $event = new \Events\Entity\Event();
        
        $form->setInputFilter($this->formfilter);
        $form->setData($data);

        try {
            if ($form->isValid()) {
                $event->exchangeArray($form->getData());
                $event->setUserId($user->current()->getUserId());

                $record = $this->eventService->save($event);
            }else{
                return new JsonModel(array(
                    'data' => array(),
                    'error' => $form->getInputFilter()->getMessages(),
                ));
            }
            
            return $this->get($record->getId());
            
        }catch(\Exception $e){
            return new JsonModel(array(
                    'data' => array(),
                    'error' => $e->getMessage(),
            ));
        }
    }
    
    public function update($id, $data) {
        $form = $this->form;

        $secret = $this->params()->fromRoute('secret');

        $user = $this->userService->findbySecret($secret);
        if(!$user->count()){
            return new JsonModel(array("status"=>"fail","code"=>"999", "error" => "Invalid API Token"));
        }

        if(empty($id) || !is_numeric($id)){
            return new JsonModel(array(
                    'data' => array(),
                    'error' => 'ID is a mandatory paramenter!'
            ));
        }
        
        $event = $this->eventService->find($id);

        // complete the information sent from the user with previous stored data
        $event->exchangeArray($data);
        $form->bind($event);
        $form->setInputFilter($this->formfilter);
        
        if ($form->isValid()) {
            $record = $this->eventService->save($event);
        }else{
            return new JsonModel(array(
                'data' => array(),
                'error' => $form->getInputFilter()->getMessages(),
            ));
        }
        
        return $this->get($record->getId());
    }
    
    public function delete($id) {

        $secret = $this->params()->fromRoute('secret');

        $user = $this->userService->findbySecret($secret);
        if(!$user->count()){
            return new JsonModel(array("status"=>"fail","code"=>"999", "error" => "Invalid API Token"));
        }
        
        $event = $this->eventService->find($id);
        $this->eventService->delete($id);
        
        return new JsonModel(array(
                'data' => 'deleted',
        ));
    }
}