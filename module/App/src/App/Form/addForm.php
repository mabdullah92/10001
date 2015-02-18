<?php
namespace App\Form;

use Zend\Form\Form;
// use Zend\View\Helper\Placeholder;
class addForm extends Form
{

    public function __construct($name = null)
    {
        parent::__construct('Profile');
        $this->setAttribute('method', 'post');
        $this->setAttribute('enctype', 'multipart/form-data');
        $this->setAttribute('class', 'form-inline');
        $this->add(array(
            'name' => 'login_name',
            'attributes' => array(
                'type' => 'text',
                'id' => 'loginname',
                'placeholder' => 'User Name'
            )
        )
        // 'options' => array(
        // 'label' => 'User Name: '
        // )
        );
        
        $this->add(array(
            'name' => 'login_pwd',
            'attributes' => array(
                'type' => 'password',
                'id' => 'login_pwd',
                'placeholder' => 'Password'
            )
        )
            
        // ,
        // 'options' => array(
        // 'label' => 'Password : ',
        // 'style'=>'width:100px;'
        // )
        );
        $this->add(array(
            'name' => 'add_pwd',
            'attributes' => array(
                'type' => 'password',
                'id' => 'add_pwd',
                'placeholder' => 'Password'
            )
        )
        
            // ,
            // 'options' => array(
            // 'label' => 'Password : ',
            // 'style'=>'width:100px;'
            // )
        );
        $this->add(array(
            'name' => 'username',
            'attributes' => array(
                'type' => 'text',
                'id' => 'username',
                'placeholder' => 'User Name'
            )
            
        )
        // ,
        // 'options' => array(
        // 'label' => 'User Name:'
        // )
        );
        $this->add(array(
            'name' => 'editdisp',
            'attributes' => array(
                'type' => 'text',
                'id' => 'editdisp',
                'placeholder' => 'User Name'
            )
        )
        // ,
        // 'options' => array(
        // 'label' => 'User Name:'
        // )
        );
        $this->add(array(
            'name' => 'delete',
            'attributes' => array(
                'type' => 'text',
                'placeholder' => 'Delete Name'
            )
        )
        // ,
        // 'options' => array(
        // 'label' => 'Delete Name: '
        // )
        );
        $this->add(array(
            'name' => 'show',
            'attributes' => array(
                'type' => 'text',
                'placeholder' => 'Show Name'
            )
        )
        // ,
        // 'options' => array(
        // 'label' => 'Show Name: '
        // )
        );
        $this->add(array(
            'name' => 'child',
            'attributes' => array(
                'type' => 'text',
                'placeholder' => 'Child Name'
            )
        )
        // ,
        // 'options' => array(
        // 'label' => 'Child Name: '
        // )
        );
        $this->add(array(
            'name' => 'chat_name',
            'attributes' => array(
                'type' => 'text',
                'placeholder' => 'Name here',
                'id' => 'chat_name'
            )
        )
        // ,
        // 'options' => array(
        // 'label' => 'Name here: '
        // )
        );
        $this->add(array(
            'name' => 'chat_box',
            'attributes' => array(
                'type' => 'text',
                'placeholder' => 'Type here',
                'id' => 'chat_box'
            )
        )
        // ,
        // 'options' => array(
        // 'label' => 'Type here: '
        // )
        );
        $this->add(array(
            'name' => 'stateEdit',
            'attributes' => array(
                'type' => 'hidden',
                'id' => 'stateEdit'
            )
        ));
        $this->add(array(
            'name' => 'submit',
            'attributes' => array(
                'type' => 'submit',
                'value' => 'Submit',
                'class' => 'btn btn-success',
                'style' => 'margin-left:73px'
            )
        ));
        $this->add(array(
            'name' => 'add',
            'attributes' => array(
                'type' => 'button',
                'value' => 'Add User',
                'class' => 'btn btn-success',
                'style' => 'margin-left:73px',
                'onclick' => 'return addUser()'
            )
        ));
        $this->add(array(
            'name' => 'login',
            'attributes' => array(
                'type' => 'button',
                'value' => 'Sign in',
                'class' => 'btn btn-success',
                'style' => 'margin-left:73px',
                'onclick' => 'return loginUser()'
            )
        ));
        $this->add(array(
            'name' => 'btn_edit',
            'attributes' => array(
                'type' => 'button',
                'value' => 'Update',
                'class' => 'btn btn-success',
                'style' => 'margin-left:73px',
                'onclick' => 'return editUser()'
            )
        ));
    }
}