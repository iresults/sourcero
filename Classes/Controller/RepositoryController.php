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
	 * @param string $newRepository
	 * @dontvalidate $newRepository
	 * @return void
	 */
	public function newAction($newRepository = NULL) {
		$this->view->assign('newRepository', $newRepository);
	}

	/**
	 * action create
	 *
	 * @param string $newRepository
	 * @return void
	 */
	public function createAction($newRepository) {
		$this->repositoryRepository->add($newRepository);
		$this->flashMessageContainer->add('Your new Repository was created.');
		$this->redirect('list');
	}

	/**
	 * action edit
	 *
	 * @param string $repository
	 * @return void
	 */
	public function editAction($repository) {
		$this->view->assign('repository', $repository);
	}

	/**
	 * action update
	 *
	 * @param string $repository
	 * @return void
	 */
	public function updateAction($repository) {
		$this->repositoryRepository->update($repository);
		$this->flashMessageContainer->add('Your Repository was updated.');
		$this->redirect('list');
	}

	/**
	 * action delete
	 *
	 * @param string $repository
	 * @return void
	 */
	public function deleteAction($repository) {
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
	 * Action to execute a command
	 *
	 * @param string $repository
	 * @param string $command
	 * @param array  $arguments
	 * @return void
	 * @dontvalidate
	 */
	public function executeCommandAction($repository, $command, $arguments = array()) {
		if (!is_object($repository)) {
			$repository = $this->repositoryRepository->findByUid($repository);
		}
		$this->view->assign('repository', $repository);
		$this->view->assign('command', $command);
		$this->view->assign('commandOutput', $this->_performAction($repository, $command, $arguments));
	}

	/**
	 * action push
	 *
	 * @param string $repository
	 * @return void
	 */
	public function pushAction($repository) {
		if (!is_object($repository)) {
			$repository = $this->repositoryRepository->findByUid($repository);
		}
		$this->view->assign('repository', $repository);
		$this->view->assign('commandOutput', $this->_performAction($repository, 'push', array('origin', 'master')));
	}

	/**
	 * action pull
	 *
	 * @param string $repository
	 * @return void
	 */
	public function pullAction($repository) {
		if (!is_object($repository)) {
			$repository = $this->repositoryRepository->findByUid($repository);
		}
		$this->view->assign('repository', $repository);
		$this->view->assign('commandOutput', $this->_performAction($repository, 'pull', array('origin', 'master')));
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
		if ($repository->getType() === 'git') {
			$command = 'git ' . $action . ' ';

			$username = $GLOBALS['BE_USER']->user['username'];
			$realname = $GLOBALS['BE_USER']->user['realName'];
			$email = $GLOBALS['BE_USER']->user['email'];

			$name = $username;
			if ($realname) {
				$name = $realname . ' (' . $username . ')';
			}
			$environment = array(
				'GIT_AUTHOR_NAME' => $name,
				'GIT_AUTHOR_EMAIL' => $email,
			);
		}

		foreach ($arguments as $key => $argument) {
			if (is_string($key)) {
				$command .= escapeshellarg($key) . ' ';
			}
			$command .= escapeshellarg($argument) . ' ';
		}

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

		#Ir::pd($output, $process->getErrorOutput());
		return $output;
	}

}
?>



