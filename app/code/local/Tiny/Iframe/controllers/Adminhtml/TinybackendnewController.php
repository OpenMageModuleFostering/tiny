<?php
class Tiny_Iframe_Adminhtml_TinybackendnewController extends Mage_Adminhtml_Controller_Action
{
	public function indexAction()
    {
       $this->loadLayout();
	   $this->_title($this->__("Tiny"));
	   $this->renderLayout();
    }
}