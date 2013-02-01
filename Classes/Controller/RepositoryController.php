<?php

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2013 Andreas Thurnheer-Meier <tma@iresults.li>, iresults
 *  Daniel Corn <cod@iresults.li>, iresults
 *
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  The GNU General Public License can be found at
 *  http://www.gnu.org/copyleft/gpl.html.
 *
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/

ini_set('display_errors', TRUE);
Ir::forceDebug();

Tx_CunddComposer_Autoloader::register();
use Symfony\Component\Process\Process;


/**
 *
 *
 * @package sourcero
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 *
 */
class Tx_Sourcero_Controller_RepositoryController extends Tx_Extbase_MVC_Controller_ActionController {

	/**
	 * repositoryRepository
	 *
	 * @var Tx_Sourcero_Domain_Repository_RepositoryRepository
	 */
	protected $repositoryRepository;

	/**
	 * injectRepositoryRepository
	 *
	 * @param Tx_Sourcero_Domain_Repository_RepositoryRepository $repositoryRepository
	 * @return void
	 */
	public function injectRepositoryRepository(Tx_Sourcero_Domain_Repository_RepositoryRepository $repositoryRepository) {
		$this->repositoryRepository = $repositoryRepository;
	}

	/**
	 * action list
	 *
	 * @return void
	 */
	public function listAction() {
		$repositories = $this->repositoryRepository->findAll();
		$this->view->assign('repositories', $repositories);
	}

	/**
	 * action show
	 *
	 * @param string $repository
	 * @return void
	 */
	public function showAction($repository) {
		if (!is_object($repository)) {
			$repository = $this->repositoryRepository->findByUid($repository);
		}
		$this->view->assign('repository', $repository);
		$this->view->assign('commandOutput', $this->_performAction($repository, 'status'));
	}

	/**
	 * action log
	 *
	 * @param string $repository
	 * @return void
	 */
	public function logAction($repository) {
		if (!is_object($repository)) {
			$repository = $this->repositoryRepository->findByUid($repository);
		}
		$this->view->assign('repository', $repository);
		$this->view->assign('commandOutput', $this->_performAction($repository, 'log'));
	}

	/**
	 * action add
	 *
	 * @param string $repository
	 * @param string $add What to add
	 * @return void
	 */
	public function addAction($repository, $add = NULL) {
		if (!is_object($repository)) {
			$repository = $this->repositoryRepository->findByUid($repository);
		}
		$this->view->assign('repository', $repository);

		if ($add) {
			$this->view->assign('commandOutput', $this->_performAction($repository, 'add', array($add)));
		} else {
			$this->view->assign('commandOutput', 'Nothing to add');
		}
	}

	/**
	 * action new
	 *
	 * @param Tx_Sourcero_Domain_Model_Repository $newRepository
	 * @dontvalidate $newRepository
	 * @return void
	 */
	public function newAction(Tx_Sourcero_Domain_Model_Repository $newRepository = NULL) {
		$this->view->assign('newRepository', $newRepository);
	}

	/**
	 * action create
	 *
	 * @param Tx_Sourcero_Domain_Model_Repository $newRepository
	 * @return void
	 */
	public function createAction(Tx_Sourcero_Domain_Model_Repository $newRepository) {
		$this->repositoryRepository->add($newRepository);
		$this->flashMessageContainer->add('Your new Repository was created.');
		$this->redirect('list');
	}

	/**
	 * action edit
	 *
	 * @param Tx_Sourcero_Domain_Model_Repository $repository
	 * @return void
	 */
	public function editAction(Tx_Sourcero_Domain_Model_Repository $repository) {
		$this->view->assign('repository', $repository);
	}

	/**
	 * action update
	 *
	 * @param Tx_Sourcero_Domain_Model_Repository $repository
	 * @return void
	 */
	public function updateAction(Tx_Sourcero_Domain_Model_Repository $repository) {
		$this->repositoryRepository->update($repository);
		$this->flashMessageContainer->add('Your Repository was updated.');
		$this->redirect('list');
	}

	/**
	 * action delete
	 *
	 * @param Tx_Sourcero_Domain_Model_Repository $repository
	 * @return void
	 */
	public function deleteAction(Tx_Sourcero_Domain_Model_Repository $repository) {
		$this->repositoryRepository->remove($repository);
		$this->flashMessageContainer->add('Your Repository was removed.');
		$this->redirect('list');
	}

	/**
	 * action commit
	 *
	 * @param string $repository
	 * @return void
	 */
	public function commitAction($repository) {
		if (!is_object($repository)) {
			$repository = $this->repositoryRepository->findByUid($repository);
		}
		$this->view->assign('repository', $repository);
	}

	/**
	 * action performCommit
	 *
	 * @param string $repository
	 * @param string $commitMessage
	 * @return void
	 * @dontvalidate
	 */
	public function performCommitAction($repository, $commitMessage) {
		if (!is_object($repository)) {
			$repository = $this->repositoryRepository->findByUid($repository);
		}
		$this->view->assign('repository', $repository);
		$this->view->assign('commitMessage', $commitMessage);
		$this->view->assign('commandOutput', $this->_performAction($repository, 'commit', array('-a', '-m' => $commitMessage)));
	}

	/**
	 * action push
	 *
	 * @return void
	 */
	public function pushAction() {

	}

	/**
	 * action pull
	 *
	 * @return void
	 */
	public function pullAction() {

	}

	/**
	 * Performs the given action
	 * @param  Tx_Sourcero_Domain_Model_Repository $repository
	 * @param  string $action    Action to perform
	 * @param  array  $arguments Additional arguments
	 * @param  boolean	$error 	 Reference that will be set to TRUE if an error occured
	 * @return string            Command output
	 */
	protected function _performAction($repository, $action, $arguments = array(), &$error = FALSE) {
		$command = '';
		Ir::pd($repository);
		if ($repository->getType() === 'git') {
			$command = 'git ' . $action . ' ';
			$environment = array(
				'GIT_AUTHOR_NAME' => 'Sourcero',
			);
		}

		foreach ($arguments as $key => $argument) {
			if (is_string($key)) {
				$command .= escapeshellarg($key) . ' ';
			}
			$command .= escapeshellarg($argument) . ' ';
		}

		#$command .= '2>&1';

		Ir::pd($command, $repository->getPath());

		$workingDir = $repository->getPath();
		$process = new Process($command);
		$process->setWorkingDirectory($workingDir);
		$process->setTimeout(3600);
		$process->setEnv($environment);
		$process->run();
		if (!$process->isSuccessful()) {
			$error = TRUE;
			return $process->getErrorOutput();
		}
		$error = FALSE;

		$output = $process->getOutput();
		Ir::pd($output, $process->getErrorOutput());
		return $output;

		return $process->getOutput();
	}

}
?>



