<?php

class UsersController extends AppController {

    public function beforeFilter() {
        parent::beforeFilter();
        $this->Auth->allow('add');
    }

    public function login() {
        if ($this->request->is('post')) {
            pr($this->request->data);
            if ($this->request->data['User']['username'] == Configure::read('super_user.username') && $this->request->data['User']['password'] == Configure::read('super_user.password')) {
                if ($this->Auth->login($this->request->data['User'])) {
                    return $this->redirect($this->Auth->redirectUrl());
                }
            }
//            if ($this->Auth->login()) {
////                return $this->redirect($this->Auth->redirectUrl());
//            }
            $this->Session->setFlash(__('Invalid username or password, try again'));
        }
    }

    public function logout() {
        return $this->redirect($this->Auth->logout());
    }

//    public function add() {
//        return;
//        if ($this->request->is('post')) {
//            $this->User->create();
//            if ($this->User->save($this->request->data)) {
//                $this->Session->setFlash(__('The user has been saved'));
//                return $this->redirect(array('action' => 'index'));
//            }
//            $this->Session->setFlash(
//                    __('The user could not be saved. Please, try again.')
//            );
//        }
//    }
}
