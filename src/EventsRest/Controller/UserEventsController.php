<?php
namespace EventsRest\Controller;

use Zend\Mvc\Controller\AbstractRestfulController;

use Zend\View\Model\JsonModel;
use EventsRest\Service\UserService;
use Events\Service\EventServiceInterface;
use Base\Service\SettingsServiceInterface;

class UsereventsController extends AbstractRestfulController {

    protected $eventService;
    protected $userService;
    protected $eventSettings;

    /**
     * @param UserService $userService
     * @param EventServiceInterface $eventService
     * @param SettingsServiceInterface $settings
     */
    public function __construct(
        UserService $userService,
        EventServiceInterface $eventService,
        SettingsServiceInterface $settings){

        $this->userService = $userService;
        $this->eventService = $eventService;
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
            return new JsonModel(array('error' => "access denied"));
        }

        $data = $this->createInfoBlock($this->eventService->getEventsbyUser($user->current()->getUserId()));

        return new JsonModel(array('data' => $data));
    }

    public function get($id) {
        $data = array();

        $secret = $this->params()->fromRoute('secret');

        $user = $this->userService->findbySecret($secret);
        if(!$user->count()){
            return new JsonModel(array('error' => "access denied"));
        }

        if(!empty($id)){
            $data['data'] = $this->createInfoBlock(array($this->eventService->find($id)));
        }

        return new JsonModel($data);

    }
}