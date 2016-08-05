<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace EventsRest\Controller;

use EventsRest\Service\UserService;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Base\Service\SettingsServiceInterface;
use Base\Service\MailService;

class RegisterController extends AbstractActionController
{
    protected $restfulusers;
    protected $settings;
    protected $form;
    protected $filter;
    protected $translator;
    protected $mailService;

    /**
     * preDispatch of the page
     *
     * (non-PHPdoc)
     * @see Zend\Mvc\Controller.AbstractActionController::onDispatch()
     */
    public function onDispatch(\Zend\Mvc\MvcEvent $e)
    {
        $this->translator = $e->getApplication()->getServiceManager()->get('translator');
        return parent::onDispatch($e);
    }


    /**
     * RegisterController constructor.
     * @param UserService $restfulusers
     * @param \EventsRest\Form\RegisterForm $form
     * @param \EventsRest\Form\RegisterFilter $formfilter
     * @param SettingsServiceInterface $settings
     */
    public function __construct(UserService $restfulusers,
                                \EventsRest\Form\RegisterForm $form,
                                \EventsRest\Form\RegisterFilter $formfilter,
                                \Base\Service\MailService $mailService,
                                SettingsServiceInterface $settings)
    {
        $this->restfulusers = $restfulusers;
        $this->form = $form;
        $this->filter = $formfilter;
        $this->settings = $settings;
        $this->mailService = $mailService;
    }

    public function indexAction()
    {

        $form = $this->form;

        $viewModel = new ViewModel(array(
            'form' => $form,
        ));

        $viewModel->setTemplate('events-rest/index/register');
        return $viewModel;
    }


    /**
     * Prepare the data and then save them
     *
     * @return \Zend\View\Model\ViewModel
     */
    public function saveAction()
    {

        $request = $this->getRequest();

        if (!$this->request->isPost()) {
            return $this->redirect()->toRoute('register');
        }

        $inputFilter = $this->filter;
        $form = $this->form;
        $post = array_merge_recursive(
            $request->getPost()->toArray(),
            $request->getFiles()->toArray()
        );

        $form->setData($post);

        $form->setInputFilter($inputFilter);

        if (!$form->isValid()) {
            $viewModel = new ViewModel(array('error' => true, 'form' => $form));
            $viewModel->setTemplate('events-rest/index/register');
            return $viewModel;
        }

        // Get the posted vars
        $data = $form->getData();

        // set the input filter
        $form->setInputFilter($inputFilter);

        $userId = $this->zfcUserAuthentication()->getIdentity()->getId();
        $data->setUserId($userId);
        $data->setInsert(true);
        $data->setDelete(true);
        $data->setList(true);
        $data->setEnabled(true);
        $data->setRequests(0);

        // Save the data in the database
        $record = $this->restfulusers->save($data);
        $website_name = $this->settings->getValueByParameter("base", "name");
        $website_email = $this->settings->getValueByParameter("base", "email");
        $website_slogan = $this->settings->getValueByParameter("base", "slogan");
        $subject = sprintf($this->translator->translate('API Request for %s project'), $website_name);

        $this->mailService->send($website_email, $data->getEmail(), $subject,
            array('record' => array('description' => $data->getDescription(),
                'subject' => $subject,
                'apitoken' => $record->getSecret(),
                'website' => $website_name,
                'slogan' => $website_slogan,
            )
            ), 'emails/register');

        $this->flashMessenger()->setNamespace('success')->addMessage($this->translator->translate('The request has been sent to the technical support.'));

        return $this->redirect()->toRoute('register');
    }
}

