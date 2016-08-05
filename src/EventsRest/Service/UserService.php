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
* @package Events
* @subpackage Service
* @author Michelangelo Turillo <mturillo@shinesoftware.com>
* @copyright 2014 Michelangelo Turillo.
* @license http://www.opensource.org/licenses/bsd-license.php BSD License
* @link http://shinesoftware.com
* @version @@PACKAGE_VERSION@@
*/

namespace EventsRest\Service;

use Zend\EventManager\EventManager;

use EventsRest\Entity\EventsRestCredentials;
use Zend\Db\TableGateway\TableGateway;
use Zend\Stdlib\Hydrator\ClassMethods;
use Zend\EventManager\EventManagerAwareInterface;
use Zend\EventManager\EventManagerInterface;
use GoogleMaps;

class UserService implements UserInterface, EventManagerAwareInterface
{
	protected $dbAdapter;
	protected $tableGateway;
	protected $translator;
	protected $eventManager;
	protected $settings;

    /**
     * @param \Zend\Db\Adapter\Adapter $dbAdapter
     * @param TableGateway $tableGateway
     * @param \Zend\Mvc\I18n\Translator $translator
     * @param \Base\Service\SettingsServiceInterface $settings
     */
	public function __construct(\Zend\Db\Adapter\Adapter $dbAdapter,
                                TableGateway $tableGateway,
                                \Zend\Mvc\I18n\Translator $translator,
                                \Base\Service\SettingsServiceInterface $settings ){
		$this->dbAdapter = $dbAdapter;
		$this->tableGateway = $tableGateway;
		$this->translator = $translator;
		$this->settings = $settings;
	}

	/**
     * @inheritDoc
     */
    public function findAll()
    {
    	$records = $this->tableGateway->select(function (\Zend\Db\Sql\Select $select) {
        	$select->join('profile', 'user_id = profile.user_id', array ('name', 'slug'), 'left');
        });
        
        return $records;
    }

	/**
     * @inheritDoc
     */
    public function findProfile($user_id)
    {
    	$records = $this->tableGateway->select(function (\Zend\Db\Sql\Select $select) use ($user_id) {
        	$select->join('profile', 'events_restful_credentials.user_id = profile.user_id', array ('profile_id' => 'id', 'profile_name' => 'name'), 'left');
            $select->where(array('profile.user_id' => $user_id));
        });

        return $records->current();
    }

    /**
     * @inheritDoc
     */
    public function findbySecret($secret)
    {
    	$records = $this->tableGateway->select(function (\Zend\Db\Sql\Select $select) use ($secret) {
        	$select->where(array('secret' => $secret));
        });

		if($records->count()){
			$this->addRestfulRequest($records->current()->getId());
		}

        return $records;
    }

    /**
	 * Update the request counter
	 *
     * @inheritDoc
     */
    public function addRestfulRequest($id)
    {
		$hydrator = new ClassMethods(true);

		$record = $this->find($id);

        if($record){
			$record->setRequests($record->getRequests() + 1);

			$data = $hydrator->extract($record);
			$this->tableGateway->update($data, array (
				'id' => $id
			));

		}

        return $id;
    }

    /**
     * @inheritDoc
     */
    public function find($id)
    {
    	if(!is_numeric($id)){
    		return false;
    	}

    	$rowset = $this->tableGateway->select(array('id' => $id));
    	$row = $rowset->current();
    	return $row;
    }

    /**
     * @inheritDoc
     */
    public function delete($id)
    {
    	$this->tableGateway->delete(array(
    			'id' => $id
    	));
    }

    /**
     * Save the event in the database
     * 
     * @inheritDoc
     */
    public function save(\EventsRest\Entity\EventsRestfulCredentials $record)
    {
    	$hydrator = new ClassMethods(true);
    	
    	// extract the data from the object
    	$data = $hydrator->extract($record);
    	$id = (int) $record->getId();

        $profile = $this->findProfile($data['user_id']);

        if(empty($data['secret'])){
            $data['secret'] = hash_hmac("sha256", $profile->profile_name, date('Y-m-d'));
        }

        $this->getEventManager()->trigger(__FUNCTION__ . '.pre', null, array('data' => $data));  // Trigger an event

    	if ($id == 0) {
    		unset($data['id']);
    		$data['createdat'] = date('Y-m-d H:i:s');
    		$data['updatedat'] = date('Y-m-d H:i:s');

    		$this->tableGateway->insert($data); // add the record
    		$id = $this->tableGateway->getLastInsertValue();
    	} else {
    		$rs = $this->find($id);
    		if (!empty($rs)) {
    			$data['updatedat'] = date('Y-m-d H:i:s');
    			unset( $data['createdat']);
       			$this->tableGateway->update($data, array (
    					'id' => $id
    			));
    		} else {
    			throw new \Exception('Record ID does not exist');
    		}
    	}
    	
    	$record = $this->find($id);
    	$this->getEventManager()->trigger(__FUNCTION__ . '.post', null, array('id' => $id, 'data' => $data, 'record' => $record));  // Trigger an event
    	return $record;
    }

    
	/* (non-PHPdoc)
     * @see \Zend\EventManager\EventManagerAwareInterface::setEventManager()
     */
     public function setEventManager (EventManagerInterface $eventManager){
         $eventManager->addIdentifiers(get_called_class());
         $this->eventManager = $eventManager;
     }

	/* (non-PHPdoc)
     * @see \Zend\EventManager\EventsCapableInterface::getEventManager()
     */
     public function getEventManager (){
       if (null === $this->eventManager) {
            $this->setEventManager(new EventManager());
        }

        return $this->eventManager;
     }

}