<?php

use Illuminate\Config\Repository;
use Illuminate\Support\MessageBag;
use Stevenmaguire\Foundation\FormBuilder as FormBuilder;
use Mockery as m;

class When_Building_Email_Test extends PHPUnit_Framework_TestCase {

    protected $html;
    protected $url;
    protected $csrfToken;
    protected $translator;    
    protected $errors;

    public function setUp()
    {
        $this->html = new Illuminate\Html\HtmlBuilder;
        $this->url = m::mock('Illuminate\Routing\UrlGenerator');
        $this->csrfToken = '';
        $this->translator = m::mock('Illuminate\Translation\Translator');
        $this->errors = m::mock('Illuminate\Support\MessageBag');

        $this->form = new FormBuilder($this->html,$this->url,$this->csrfToken,$this->translator,$this->errors);
    }

    public function test_Return_Textbox_Input_Tag_With_Matching_Name() 
    {
        $name = 'tim';
        $this->errors->shouldReceive('has')->with($name)->twice()->andReturn(false);
        
        $result = $this->form->email($name);
        $this->assertTrue(strpos($result,'name="'.$name.'"') !== false
            && strpos($result,'<input') !== false
            && strpos($result,'type="email"') !== false);
    }       

    public function test_Return_Textbox_Text_Tag_With_Matching_Name_And_Error_Class_While_Errors() 
    {
        $name = 'tim';
        $errors = array('Error message');
        $this->errors->shouldReceive('has')->with($name)->twice()->andReturn(true);
        $this->errors->shouldReceive('get')->with($name)->once()->andReturn($errors);
        
        $result = $this->form->email($name);

        $this->assertTrue(strpos($result,'name="'.$name.'"') !== false
            && strpos($result,'<input') !== false
            && strpos($result,'type="email"') !== false
            && strpos($result,'class="error"') !== false
            && strpos($result,'<small class="error">'.implode(' ',$errors).'</small>') !== false);
    } 

    public function tearDown()
    {
        m::close();
    }
}
