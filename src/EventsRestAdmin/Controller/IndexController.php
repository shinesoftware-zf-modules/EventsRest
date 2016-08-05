<?php

/**
* Copyright (c) 2014 Shine Software.
* All rights reserved.
*
* Redistribution and use in source and binary forms, with or without
* modification, are permitted provided that the following conditions
* are met:
*
* * Redistributions of source code must retain the above copyright
* notice, this list of conditions and the following disclaimer.
*
* * Redistributions in binary form must reproduce the above copyright
* notice, this list of conditions and the following disclaimer in
* the documentation and/or other materials provided with the
* distribution.
*
* * Neither the names of the copyright holders nor the names of the
* contributors may be used to endorse or promote products derived
* from this software without specific prior written permission.
*
* THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS
* "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT
* LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS
* FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE
* COPYRIGHT OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT,
* INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING,
* BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
* LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER
* CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT
* LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN
* ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE
* POSSIBILITY OF SUCH DAMAGE.
*
* @package EventsRest
* @subpackage Controller
* @author Michelangelo Turillo <mturillo@shinesoftware.com>
* @copyright 2014 Michelangelo Turillo.
* @license http://www.opensource.org/licenses/bsd-license.php BSD License
* @link http://shinesoftware.com
* @version @@PACKAGE_VERSION@@
*/

namespace EventsRestAdmin\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Base\Model\UrlRewrites as UrlRewrites;
use Base\Hydrator\Strategy\DateTimeStrategy;

class IndexController extends AbstractActionController
{
	protected $eventService;
	protected $datagrid;
	protected $form;
	protected $filter;
	protected $settings;
	protected $translator;
	
	/**
	 * preDispatch of the page
	 *
	 * (non-PHPdoc)
	 * @see Zend\Mvc\Controller.AbstractActionController::onDispatch()
	 */
	public function onDispatch(\Zend\Mvc\MvcEvent $e){
	    $this->translator = $e->getApplication()->getServiceManager()->get('translator');
	    return parent::onDispatch( $e );
	}

	/**
	 * Class constructor
	 *
	 * @param \EventsRest\Service\UserService $recordService
	 * @param \EventsRest\Form\EventsRestfulCredentialsForm $form
	 * @param \EventsRest\Form\EventsRestfulCredentialsFilter $formfilter
	 * @param \ZfcDatagrid\Datagrid $datagrid
	 * @param \Base\Service\SettingsServiceInterface $settings
	 */
	public function __construct(\EventsRest\Service\UserService $recordService,
								\EventsRest\Form\EventsRestfulCredentialsForm $form,
								\EventsRest\Form\EventsRestfulCredentialsFilter $formfilter,
								\ZfcDatagrid\Datagrid $datagrid, 
								\Base\Service\SettingsServiceInterface $settings)
	{
		$this->eventService = $recordService;
		$this->datagrid = $datagrid;
		$this->form = $form;
		$this->filter = $formfilter;
		$this->settings = $settings;
	}
	
	/**
	 * List of all records
	 */
	public function indexAction ()
	{
		// prepare the datagrid
		$this->datagrid->render();
	
		// get the datagrid ready to be shown in the template view
		$response = $this->datagrid->getResponse();
	
		if ($this->datagrid->isHtmlInitReponse()) {
			$view = new ViewModel();
			$view->addChild($response, 'grid');
			return $view;
		} else {
			return $response;
		}
	}
	
    /**
     * Add new information
     */
    public function addAction ()
    {
    	$form = $this->form;
    	$viewModel = new ViewModel(array (
    			'form' => $form,
    	));
    	
    	$viewModel->setTemplate('events-rest-admin/index/edit');
    	return $viewModel;
    }
    
    /**
     * Edit the main event information
     */
    public function editAction ()
    {
    	$id = $this->params()->fromRoute('id');
    	
    	$form = $this->form;
    
    	// Get the record by its id
    	$rsevent = $this->eventService->find($id);
    	
    	if(empty($rsevent)){
    	    $this->flashMessenger()->setNamespace('danger')->addMessage('The record has been not found!');
    	    return $this->redirect()->toRoute('zfcadmin/events/default');
    	}

    	// Bind the data in the form
    	if (! empty($rsevent)) {
    		$form->bind($rsevent);
    	}
    	
    	$viewModel = new ViewModel(array (
    	        'form' => $form,
    	));
    
    	return $viewModel;
    }
    
    
    /**
     * Prepare the data and then save them
     *
     * @return \Zend\View\Model\ViewModel
     */
    public function processAction ()
    {
    	$urlRewrite = new UrlRewrites();
    	$request = $this->getRequest();
    	
    	if (! $this->request->isPost()) {
    		return $this->redirect()->toRoute(NULL, array (
    				'controller' => 'events-rest-admin',
    				'action' => 'index'
    		));
    	}
    
    	$post = $this->request->getPost();
		$inputFilter = $this->filter;

    	$form = $this->form;
		$form->setData($post);

		$form->setInputFilter($inputFilter);

    	if (!$form->isValid()) {

    		// Get the record by its id
    		$viewModel = new ViewModel(array (
    				'error' => true,
    				'form' => $form,
    		));

    		$viewModel->setTemplate('events-rest-admin/index/edit');
    		return $viewModel;
    	}

    	// Get the posted vars
    	$data = $form->getData();
    	 
    	// set the input filter
    	$form->setInputFilter($inputFilter);

		// Save the data in the database
    	$record = $this->eventService->save($data);

    	$this->flashMessenger()->setNamespace('success')->addMessage($this->translator->translate('The information have been saved.'));
    
    	return $this->redirect()->toRoute(NULL, array (
    			'controller' => 'index',
				'action' => 'Edit',
    			'id' => $record->getId()
    	));
    }
    
    /**
     * Delete the records 
     *
     * @return \Zend\Http\Response
     */
    public function deleteAction ()
    {
    	$id = $this->params()->fromRoute('id');
    
    	if (is_numeric($id)) {
    
    		// Delete the record informaiton
    		$this->eventService->delete($id);
    
    		// Go back showing a message
    		$this->flashMessenger()->setNamespace('success')->addMessage($this->translator->translate('The record has been deleted!'));
    		return $this->redirect()->toRoute('zfcadmin/events-restful-credentials');
    	}
    
    	$this->flashMessenger()->setNamespace('danger')->addMessage($this->translator->translate('The record has been not deleted!'));
    	return $this->redirect()->toRoute('zfcadmin/events-restful-credentials');
    }
    
}
