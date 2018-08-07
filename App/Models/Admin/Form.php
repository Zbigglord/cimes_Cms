<?php
/**
 * Created by Zbigglord
 * Company: HELLUX
 * Date: 2018-01-11
 * Time: 09:20
 */

namespace App\Models\Admin;

use Core\Model;
use Core\Session;

class Form extends \Core\Model{

    public $action;
    public $name;
    public $id;
    public $method;
    public $class;
    public $collumns;
    public $hasFieldsets;
    public $hasLegends;
    public $legend;
    public $needCaptcha;
    public $inputData = array(

        'label'                     => '',
        'label-class'               => 'tooltip-labels',
        'label-data-placement'      => 'top',
        'label-data-toggle'         => 'tooltip',
        'label-title'               => '',
        'label-data-original-title' => 'enter tooltip',
        'type'                      => 'text',
        'class'                     => 'form-control',
        'id'                        => '',
        'name'                      => '',
        'value'                     => '',
        'values'                    => array(),
        'required'                  => FALSE,
        'checked'                   => FALSE,
        'placeholder'               => '',
        'disabled'                  => FALSE,
        'rows'                      => 5,//textarea
        'maxlength'                 => 255,
        'data-show-tick'            => '',
        'data-live-search'          => '',
        'min'                       => 1,
        'max'                       => 10,
        'step'                      => 1,
        'datepicker'                => TRUE,
        'onclick'                   => ''

    );

    public function __construct($class = '', $id = '', $name = '', $action = '#', $method = 'POST', $hasFieldsets = FALSE, $hasLegends = FALSE, $needCaptcha = FALSE){

        $this->class = $class;
        $this->id = $id;
        $this->name = $name;
        $this->action = $action;
        $this->method = $method;
        $this->hasFieldsets = $hasFieldsets;
        $this->hasLegends = $hasLegends;
        $this->needCaptcha = $needCaptcha;

    }

    public function getAction(){
        return $this->action;
    }

    public function setAction($action){
        $this->action = $action;
    }

    public function getName(){
        return $this->name;
    }

    public function setName($name){
        $this->name = $name;
    }

    public function getId(){
        return $this->id;
    }

    public function setId($id){
        $this->id = $id;
    }

    public function getMethod(){
        return $this->method;
    }

    public function setMethod($method){
        $this->method = $method;
    }

    public function getClass()
    {
        return $this->class;
    }

    public function setClass($class)
    {
        $this->class = $class;
    }

    public function getHasFieldsets()
    {
        return $this->hasFieldsets;
    }

    public function setHasFieldsets($hasFieldsets)
    {
        $this->hasFieldsets = $hasFieldsets;
    }

    public function getHasLegends()
    {
        return $this->hasLegends;
    }

    public function setHasLegends($hasLegends)
    {
        $this->hasLegends = $hasLegends;
    }

    public function getLegend()
    {
        return $this->legend;
    }

    public function setLegend($legend)
    {
        $this->legend = $legend;
    }

    public function getInputData()
    {
        return $this->inputData;
    }

    public function setInputData($inputData)
    {
        $this->inputData = $inputData;
    }

    public function getNeedCaptcha()
    {
        return $this->needCaptcha;
    }

    public function setNeedCaptcha($needCaptcha)
    {
        $this->needCaptcha = $needCaptcha;
    }

    public function startForm(){

        $class = $this->getClass();
        $name = $this->getName();
        $id = $this->getId();
        $action = $this->getAction();
        $method = $this->getMethod();

        $form =  '<form ';
        if($class != ''){
            $form = $form.'class="'.$class.'" ';
        }

        if($id != ''){
            $form = $form.'id="'.$id.'" ';
        }

        if($name != ''){
            $form = $form.'name="'.$name.'" ';
        }

        $form = $form.'action="'.$action.'" ';
        $form = $form.'method="'.$method.'">';

        return $form;

    }//END function startForm()

    public function endForm(){

        $endform = '</form>';
        return $endform;

    }

    public function startFieldset(){

        $fieldset = '<fieldset>';
        return $fieldset;

    }

    public function endFieldset(){

        $fieldset = '</fieldset>';
        return $fieldset;

    }

    public function legend($legend){

        if($this->getHasLegends() == TRUE){
            $output = '<legend>'.$legend.'</legend>';
            return $output;
        }else{
            return FALSE;
        }

    }

    public function addInput($type,$inputData){

        $input = '';
        $input_id = $inputData['input_id'];
        $label = isset($inputData['input_label']) ? $inputData['input_label'] : '';
        $label_class = isset($inputData['input_label_class']) ? $inputData['input_label_class'] : '';
        $label_data_placement = isset($inputData['input_label_data_placement']) ? $inputData['input_label_data_placement'] : '';
        $label_data_toggle = isset($inputData['input_label_data_toggle']) ? $inputData['input_label_data_toggle'] : '';
        $label_title = isset($inputData['input_label_title']) ? $inputData['input_label_title'] : '';
        $label_data_original_title = isset($inputData['input_label_data_original_title']) ? $inputData['input_label_data_original_title'] : '';
        $class = isset($inputData['input_class']) ? $inputData['input_class'] : '';
        $id = isset($inputData['input_id_char']) ? $inputData['input_id_char'] : '';
        $name = isset($inputData['input_name']) ? $inputData['input_name'] : '';
        $value = isset($inputData['input_value']) ? $inputData['input_value'] : '';
        $values = isset($inputData['input_values']) ? $inputData['input_values'] : '';//todo make it work as array
        $required = isset($inputData['input_required']) ? $inputData['input_required'] : FALSE;
        if($required == 0){$required = FALSE;}
        $checked = isset($inputData['input_checked']) ? $inputData['input_checked'] : FALSE;
        $data_show_tick = isset($inputData['input_data_show_tick']) ? $inputData['input_data_show_tick'] : FALSE;
        $data_live_search = isset($inputData['input_data_live_search']) ? $inputData['input_data_live_search'] : FALSE;
        $live_search_placeholder = isset($inputData['input_data_live_search_placeholder']) ? $inputData['input_data_live_search_placeholder'] : '';
        $placeholder = isset($inputData['input_placeholder']) ? $inputData['input_placeholder'] : '';
        $maxlength = isset($inputData['input_maxlength']) ? $inputData['input_maxlength'] : '';
        if($maxlength = 0){$maxlength = '';}
        $disabled = isset($inputData['input_disabled']) ? $inputData['input_disabled'] : FALSE;
        if($disabled == 0){$disabled = FALSE;}
        $rows = isset($inputData['input_rows']) ? $inputData['input_rows'] : '';
        $min = isset($inputData['input_min']) ? $inputData['input_min'] : '';
        $max = isset($inputData['input_max']) ? $inputData['input_max'] : '';
        $step = isset($inputData['input_step']) ? $inputData['input_step'] : '';
        $datepicker = isset($inputData['input_datepicker']) ? $inputData['input_datepicker'] : FALSE;
        $onclick = isset($inputData['input_onclick']) ? $inputData['input_onclick'] : '';

        switch($type){

            case 'hidden':

                $input = '<input type="hidden" ';

                if($id != ''){

                    $input = $input.'id ="'.$id.'" ';

                }

                $input = $input.'name ="'.$name.'" ';
                $input = $input.'value ="'.$value.'"/>';

                break;

            case 'text':

                if($label != ''){

                    $input = '<label for="'.$name.'" ';

                    if($label_class != ''){

                        $input = $input.'class="'.$label_class.'" ';

                        if($label_data_placement != ''){

                            $input = $input.'data-placement="'.$label_data_placement.'" ';

                        }

                        if($label_data_toggle != ''){

                            $input = $input.'data-toggle="'.$label_data_toggle.'" ';

                        }

                        if($label_data_original_title != ''){

                            $input = $input.'title="'.$label_title.'" ';
                            $input = $input.'data-original-title="'.$label_data_original_title.'" ';

                        }

                    }

                    $input = $input.'>';

                    if($required != FALSE){

                        $input = $input.$label.'<span class="label-required"> *</span></label>';

                    }else{

                        $input = $input.$label.'</label>';

                    }

                }

                $input = $input.'<input type="'.$type.'" ';

                if($class != ''){

                    $input = $input.'class ="'.$class.'" ';

                }

                if($id != ''){

                    $input = $input.'id ="'.$id.'" ';

                }

                $input = $input.'name ="'.$name.'" ';
                $input = $input.'value ="'.$value.'" ';

                if($required != FALSE){

                    $input = $input.' required ';

                }

                if($placeholder !=''){

                    $input = $input.'placeholder="'.$placeholder.'" ';

                }

                if($maxlength != 255){

                    $input = $input.'maxlength="'.$maxlength.'" ';

                }

                if($disabled != FALSE){

                    $input = $input.'disabled ';

                }

                $input = $input.'/>';

                break;

            case 'email':

                if($label != ''){

                    $input = '<label for="'.$name.'" ';

                    if($label_class != ''){

                        $input = $input.'class="'.$label_class.'" ';

                        if($label_data_placement != ''){

                            $input = $input.'data-placement="'.$label_data_placement.'" ';

                        }

                        if($label_data_toggle != ''){

                            $input = $input.'data-toggle="'.$label_data_toggle.'" ';

                        }

                        if($label_data_original_title != ''){

                            $input = $input.'title="'.$label_title.'" ';
                            $input = $input.'data-original-title="'.$label_data_original_title.'" ';

                        }

                    }

                    $input = $input.'>';

                    if($required != FALSE){

                        $input = $input.$label.'<span class="label-required"> *</span></label>';

                    }else{

                        $input = $input.$label.'</label>';

                    }

                }

                $input = $input.'<input type="'.$type.'" ';

                if($class != ''){

                    $input = $input.'class ="'.$class.'" ';

                }

                if($id != ''){

                    $input = $input.'id ="'.$id.'" ';

                }

                $input = $input.'name ="'.$name.'" ';
                $input = $input.'value ="'.$value.'" ';

                if($required != FALSE){

                    $input = $input.' required ';

                }

                if($placeholder !=''){

                    $input = $input.'placeholder="'.$placeholder.'" ';

                }

                if($maxlength != 255){

                    $input = $input.'maxlength="'.$maxlength.'" ';

                }

                if($disabled != FALSE){

                    $input = $input.'disabled ';

                }

                $input = $input.'/>';

                break;

            case 'password':

                if($label != ''){

                    $input = '<label for="'.$name.'" ';

                    if($label_class != ''){

                        $input = $input.'class="'.$label_class.'" ';

                        if($label_data_placement != ''){

                            $input = $input.'data-placement="'.$label_data_placement.'" ';

                        }

                        if($label_data_toggle != ''){

                            $input = $input.'data-toggle="'.$label_data_toggle.'" ';

                        }

                        if($label_data_original_title != ''){

                            $input = $input.'title="'.$label_title.'" ';
                            $input = $input.'data-original-title="'.$label_data_original_title.'" ';

                        }

                    }

                    $input = $input.'>';

                    if($required != FALSE){

                        $input = $input.$label.'<span class="label-required"> *</span></label>';

                    }else{

                        $input = $input.$label.'</label>';

                    }

                }

                $input = $input.'<input type="'.$type.'" ';

                if($class != ''){

                    $input = $input.'class ="'.$class.'" ';

                }

                if($id != ''){

                    $input = $input.'id ="'.$id.'" ';

                }

                $input = $input.'name ="'.$name.'" ';
                $input = $input.'value ="'.$value.'" ';

                if($required != FALSE){

                    $input = $input.' required ';

                }

                if($placeholder !=''){

                    $input = $input.'placeholder="'.$placeholder.'" ';

                }

                if($maxlength != 255){

                    $input = $input.'maxlength="'.$maxlength.'" ';

                }

                if($disabled != FALSE){

                    $input = $input.'disabled ';

                }

                $input = $input.'/>';

                break;

            case 'textarea':

                if($label != ''){

                    $input = '<label for="'.$name.'" ';

                    if($label_class != ''){

                        $input = $input.'class="'.$label_class.'" ';

                        if($label_data_placement != ''){

                            $input = $input.'data-placement="'.$label_data_placement.'" ';

                        }

                        if($label_data_toggle != ''){

                            $input = $input.'data-toggle="'.$label_data_toggle.'" ';

                        }

                        if($label_data_original_title != ''){

                            $input = $input.'title="'.$label_title.'" ';
                            $input = $input.'data-original-title="'.$label_data_original_title.'" ';

                        }

                    }

                    $input = $input.'>';

                    if($required != FALSE){

                        $input = $input.$label.'<span class="label-required"> *</span></label>';

                    }else{

                        $input = $input.$label.'</label>';

                    }

                }

                $input = $input.'<textarea ';

                if($class != ''){

                    $input = $input.'class ="'.$class.'" ';

                }

                if($id != ''){

                    $input = $input.'id ="'.$id.'" ';

                }

                $input = $input.'name ="'.$name.'" ';

                if($required != FALSE){

                    $input = $input.' required ';

                }

                if($placeholder !=''){

                    $input = $input.'placeholder="'.$placeholder.'" ';

                }

                if($rows != 5){

                    $input = $input.'rows="'.$rows.'" ';

                }

                if($maxlength){

                    $input = $input.'maxlength="'.$maxlength.'" ';

                }

                if($disabled != FALSE){

                    $input = $input.'disabled ';

                }

                $input = $input.'>';

                if($value !=''){

                    $input = $input.$value;

                }

                $input = $input.'</textarea>';

                break;

            case 'tel':

                if($label != ''){

                    $input = '<label for="'.$name.'" ';

                    if($label_class != ''){

                        $input = $input.'class="'.$label_class.'" ';

                        if($label_data_placement != ''){

                            $input = $input.'data-placement="'.$label_data_placement.'" ';

                        }

                        if($label_data_toggle != ''){

                            $input = $input.'data-toggle="'.$label_data_toggle.'" ';

                        }

                        if($label_data_original_title != ''){

                            $input = $input.'title="'.$label_title.'" ';
                            $input = $input.'data-original-title="'.$label_data_original_title.'" ';

                        }

                    }

                    $input = $input.'>';

                    if($required != FALSE){

                        $input = $input.$label.'<span class="label-required"> *</span></label>';

                    }else{

                        $input = $input.$label.'</label>';

                    }

                }

                $input = $input.'<input type="'.$type.'" ';

                if($class != ''){

                    $input = $input.'class ="'.$class.'" ';

                }

                if($id != ''){

                    $input = $input.'id ="'.$id.'" ';

                }

                $input = $input.'name ="'.$name.'" ';
                $input = $input.'value ="'.$value.'" ';

                if($required != FALSE){

                    $input = $input.' required ';

                }

                if($placeholder !=''){

                    $input = $input.'placeholder="'.$placeholder.'" ';

                }

                if($maxlength != 255){

                    $input = $input.'maxlength="'.$maxlength.'" ';

                }

                if($disabled != FALSE){

                    $input = $input.'disabled ';

                }

                $input = $input.'/>';

                break;

            case 'url':

                if($label != ''){

                    $input = '<label for="'.$name.'" ';

                    if($label_class != ''){

                        $input = $input.'class="'.$label_class.'" ';

                        if($label_data_placement != ''){

                            $input = $input.'data-placement="'.$label_data_placement.'" ';

                        }

                        if($label_data_toggle != ''){

                            $input = $input.'data-toggle="'.$label_data_toggle.'" ';

                        }

                        if($label_data_original_title != ''){

                            $input = $input.'title="'.$label_title.'" ';
                            $input = $input.'data-original-title="'.$label_data_original_title.'" ';

                        }

                    }

                    $input = $input.'>';

                    if($required != FALSE){

                        $input = $input.$label.'<span class="label-required"> *</span></label>';

                    }else{

                        $input = $input.$label.'</label>';

                    }

                }

                $input = $input.'<input type="'.$type.'" ';

                if($class != ''){

                    $input = $input.'class ="'.$class.'" ';

                }

                if($id != ''){

                    $input = $input.'id ="'.$id.'" ';

                }

                $input = $input.'name ="'.$name.'" ';
                $input = $input.'value ="'.$value.'" ';

                if($required != FALSE){

                    $input = $input.' required ';

                }

                if($placeholder !=''){

                    $input = $input.'placeholder="'.$placeholder.'" ';

                }

                if($maxlength != 255){

                    $input = $input.'maxlength="'.$maxlength.'" ';

                }

                if($disabled != FALSE){

                    $input = $input.'disabled ';

                }

                $input = $input.'/>';

                break;

            case 'color':

                if($label != ''){

                    $input = '<label for="'.$name.'" ';

                    if($label_class != ''){

                        $input = $input.'class="'.$label_class.'" ';

                        if($label_data_placement != ''){

                            $input = $input.'data-placement="'.$label_data_placement.'" ';

                        }

                        if($label_data_toggle != ''){

                            $input = $input.'data-toggle="'.$label_data_toggle.'" ';

                        }

                        if($label_data_original_title != ''){

                            $input = $input.'title="'.$label_title.'" ';
                            $input = $input.'data-original-title="'.$label_data_original_title.'" ';

                        }

                    }

                    $input = $input.'>';

                    if($required != FALSE){

                        $input = $input.$label.'<span class="label-required"> *</span></label>';

                    }else{

                        $input = $input.$label.'</label>';

                    }

                }

                $input = $input.'<input type="'.$type.'" ';

                if($class != ''){

                    $input = $input.'class ="'.$class.'" ';

                }

                if($id != ''){

                    $input = $input.'id ="'.$id.'" ';

                }

                $input = $input.'name ="'.$name.'" ';
                $input = $input.'value ="'.$value.'" ';

                if($required != FALSE){

                    $input = $input.' required ';

                }

                $input = $input.'/>';

                break;

            case 'number':

                if($label != ''){

                    $input = '<label for="'.$name.'" ';

                    if($label_class != ''){

                        $input = $input.'class="'.$label_class.'" ';

                        if($label_data_placement != ''){

                            $input = $input.'data-placement="'.$label_data_placement.'" ';

                        }

                        if($label_data_toggle != ''){

                            $input = $input.'data-toggle="'.$label_data_toggle.'" ';

                        }

                        if($label_data_original_title != ''){

                            $input = $input.'title="'.$label_title.'" ';
                            $input = $input.'data-original-title="'.$label_data_original_title.'" ';

                        }

                    }

                    $input = $input.'>';

                    if($required != FALSE){

                        $input = $input.$label.'<span class="label-required"> *</span></label>';

                    }else{

                        $input = $input.$label.'</label>';

                    }

                }

                $input = $input.'<input type="'.$type.'" ';

                if($class != ''){

                    $input = $input.'class ="'.$class.'" ';

                }

                if($id != ''){

                    $input = $input.'id ="'.$id.'" ';

                }

                $input = $input.'name ="'.$name.'" ';
                $input = $input.'value ="'.$value.'" ';

                if($required != FALSE){

                    $input = $input.' required ';

                }

                if($min){

                    $input = $input.'min="'.$min.'" ';

                }

                if($max){

                    $input = $input.'max="'.$max.'" ';

                }

                if($step){

                    $input = $input.'step="'.$step.'" ';

                }

                if($disabled != FALSE){

                    $input = $input.'disabled ';

                }

                $input = $input.'/>';

                break;

            case 'date':

                if($label != ''){

                    $input = '<label for="'.$name.'" ';

                    if($label_class != ''){

                        $input = $input.'class="'.$label_class.'" ';

                        if($label_data_placement != ''){

                            $input = $input.'data-placement="'.$label_data_placement.'" ';

                        }

                        if($label_data_toggle != ''){

                            $input = $input.'data-toggle="'.$label_data_toggle.'" ';

                        }

                        if($label_data_original_title != ''){

                            $input = $input.'title="'.$label_title.'" ';
                            $input = $input.'data-original-title="'.$label_data_original_title.'" ';

                        }

                    }

                    $input = $input.'>';

                    if($required != FALSE){

                        $input = $input.$label.'<span class="label-required"> *</span></label>';

                    }else{

                        $input = $input.$label.'</label>';

                    }

                }

                $input = $input.'<input type="'.$type.'" ';

                if($class != ''){

                    $input = $input.'class ="'.$class.'" ';

                }

                if($id != ''){

                    $input = $input.'id ="'.$id.'" ';

                }

                $input = $input.'name ="'.$name.'" ';
                $input = $input.'value ="'.$value.'" ';

                if($required != FALSE){

                    $input = $input.' required ';

                }

                if($min){

                    $input = $input.'min="'.$min.'" ';

                }

                if($max){

                    $input = $input.'max="'.$max.'" ';

                }

                if($step){

                    $input = $input.'step="'.$step.'" ';

                }

                if($disabled != FALSE){

                    $input = $input.'disabled ';

                }

                if($datepicker){

                    $input = $input.'datepicker ';

                }

                $input = $input.' />';

                break;

            case 'checkbox':

                $input = '<div class="radio-holder">';

                $input = $input.'<input type="'.$type.'" ';

                if($class != ''){

                    $input = $input.'class ="'.$class.'" ';

                }

                if($id != ''){

                    $input = $input.'id ="'.$id.'" ';

                }

                $input = $input.'name ="'.$name.'" ';

                if($required != FALSE){

                    $input = $input.' required ';

                }

                if($disabled != FALSE){

                    $input = $input.'disabled ';

                }

                $input = $input.'/>';

                if($label != ''){

                    $input = $input.'<label for="'.$name.'" ';

                    if($label_class != ''){

                        $input = $input.'class="'.$label_class.'" ';

                        if($label_data_placement != ''){

                            $input = $input.'data-placement="'.$label_data_placement.'" ';

                        }

                        if($label_data_toggle != ''){

                            $input = $input.'data-toggle="'.$label_data_toggle.'" ';

                        }

                        if($label_data_original_title != ''){

                            $input = $input.'title="'.$label_title.'" ';
                            $input = $input.'data-original-title="'.$label_data_original_title.'" ';

                        }

                    }

                    $input = $input.'>';

                    if($required != FALSE){

                        $input = $input.$label.'<span class="label-required"> *</span></label>';

                    }else{

                        $input = $input.$label.'</label>';

                    }

                }

                $input = $input.'</div>';

                break;

            case 'radio':

                break;

            case 'select':

                    $input = '<label for="'.$name.'" ';

                    if($label_class != ''){

                        $input = $input.'class="'.$label_class.'" ';

                        if($label_data_placement != ''){

                            $input = $input.'data-placement="'.$label_data_placement.'" ';

                        }

                        if($label_data_toggle != ''){

                            $input = $input.'data-toggle="'.$label_data_toggle.'" ';

                        }

                        if($label_data_original_title != ''){

                            $input = $input.'title="'.$label_title.'" ';
                            $input = $input.'data-original-title="'.$label_data_original_title.'" ';

                        }

                    }

                    $input = $input.'>';

                    if($required != FALSE){

                        $input = $input.$label.'<span class="label-required"> *</span></label>';

                    }else{

                        $input = $input.$label.'</label>';

                    }

                $input = $input.'<select ';

                if($class != ''){

                    $input = $input.'class ="'.$class.'" ';

                }

                if($id != ''){

                    $input = $input.'id ="'.$id.'" ';

                }

                $input = $input.'name ="'.$name.'" ';

                if($data_live_search != FALSE){

                    $input = $input.'data-live-search ="'.$data_live_search.'" data-live-search-placeholder="'.$live_search_placeholder.'"';

                }

                if($data_show_tick != FALSE){

                    $input = $input.'data-show-tick ="'.$data_show_tick.'" ';

                }

                if($required != FALSE){

                    $input = $input.' required ';

                }


                if($disabled != FALSE){

                    $input = $input.'disabled ';

                }

                $input = $input.' />';

                 $options = self::getSelectOptionsBySelect($input_id);

                $input = $input.'<option data-subtext="" value="0" data-tokens="">'.AMENU_PICK_ONE.'</option>';

                    foreach($options['options'] as $option){

                        $input = $input.'<option data-subtext="'.$option['option_data_subtext'].'" value="'.$option['option_value'].'" data-tokens="'.$option['option_data_tokens'].'">'.$option['option_text'].'</option>';

                    }

                $input = $input.'</select>';

                break;

            case 'file':

                break;

            case 'submit':

                $input = $input.'<input type="submit" ';

                if($class != ''){

                    $input = $input.'class ="'.$class.'" ';

                }

                if($id != ''){

                    $input = $input.'id ="'.$id.'" ';

                }

                $input = $input.'name ="'.$name.'" ';
                $input = $input.'value ="'.$value.'" ';

                if($disabled != FALSE){

                    $input = $input.'disabled ';

                }

                if($onclick !=''){

                    $input = $input.'onclick="'.$onclick.'" ';

                }

                $input = $input.'/>';

                break;

        }//END SWITCH

        return $input;

    }

    /*database*/

public static function insertForm($new_form){

    $db = Model::getDB();

    $form_title = $new_form['form_title'];
    $form_category = $new_form['form_category'];
    $form_id_char = $new_form['form_id_char'];
    $form_name = $new_form['form_name'];
    $form_class = $new_form['form_class'];
    $form_action = $new_form['form_action'];
    $form_method = $new_form['form_method'];
    $form_text_before = $new_form['form_text_before'];
    $form_text_after = $new_form['form_text_after'];
    if($form_method > 1){
        $form_method = 'GET';
    }else{
        $form_method = 'POST';
    }
    $form_author = Session::get('user_nick');

    $exists = self::checkIfFormExists($form_id_char);

    if($exists != TRUE){

        $insert_query = ("INSERT INTO forms_created (form_id_char, form_title, form_name, form_class, form_action, form_method, form_created, form_author, form_text_before, form_text_after,form_category) VALUES ('$form_id_char','$form_title','$form_name','$form_class','$form_action','$form_method',NOW(),'$form_author','$form_text_before','$form_text_after','$form_category')");
        $success = $db->query($insert_query);
        if(!$success){
            Session::setFlash(ATXT_ERROR_RUNNING_QUERY . '[' . $db->error . ']','error');
            die(ATXT_ERROR_RUNNING_QUERY . '[' . $db->error . ']');
        }

        $db->close();
        return $db->insert_id;

    }else{

        $db->close();
        return 0;

    }
}//END static function insertForm($data)

    public static function editForm($form){

        $db = Model::getDB();

        $form_id = $form['form_id'];
        $form_category = $form['form_category'];
        $form_title = $form['form_title'];
        $form_id_char = $form['form_id_char'];
        $form_name = $form['form_name'];
        $form_class = $form['form_class'];
        $form_action = $form['form_action'];
        $form_method = $form['form_method'];
        $form_text_before = $form['form_text_before'];
        $form_text_after = $form['form_text_after'];
        if($form_method > 1){
            $form_method = 'GET';
        }else{
            $form_method = 'POST';
        }

            $edit_query = ("UPDATE forms_created SET form_title='$form_title', form_name='$form_name', form_class='$form_class', form_action='$form_action', form_method='$form_method', form_edited=NOW(), form_text_before='$form_text_before', form_text_after='$form_text_after', form_category = '$form_category' WHERE form_id='$form_id'");
            $success = $db->query($edit_query);
            if(!$success){
                Session::setFlash(ATXT_ERROR_RUNNING_QUERY . '[' . $db->error . ']','error');
                die(ATXT_ERROR_RUNNING_QUERY . '[' . $db->error . ']');
            }

            $db->close();
            return TRUE;

    }//END static function insertForm($data)

    public static function getEmptyForm($form_id){//get only form data without inputs

        $db = Model::getDB();
        $form = array();
        $query = ("SELECT * FROM forms_created WHERE form_id = '$form_id'");

        $success = $db->query($query);
        if(!$success){
            Session::setFlash(ATXT_ERROR_RUNNING_QUERY . '[' . $db->error . ']','error');
            die(ATXT_ERROR_RUNNING_QUERY . '[' . $db->error . ']');
        }

        $found = $success-> num_rows;
        if($found > 0){
            while($result = $success->fetch_assoc()){
                $form['form_id'] = $result['form_id'] ;
                $form['form_id_char'] = $result['form_id_char'] ;
                $form['form_title'] = $result['form_title'] ;
                $form['form_name'] = $result['form_name'] ;
                $form['form_class'] = $result['form_class'] ;
                $form['form_action'] = $result['form_action'] ;
                $form['form_method'] = $result['form_method'] ;
                $form['form_created'] = $result['form_created'] ;
                $form['form_author'] = $result['form_author'] ;
                $form['form_has_inputs'] = $result['form_has_inputs'] ;
                $form['form_text_before'] = $result['form_text_before'] ;
                $form['form_text_after'] = $result['form_text_after'] ;
            }
        }

        $db->close();
        return $form;

    }//END function getEmptyForm($form_id)

    public static function checkIfFormExists($form_id_char){//there won't be two the same forms with identical id_char

        $db = Model::getDB();
        $search_query = ("SELECT * FROM forms_created WHERE form_id_char = '$form_id_char'");
        $success = $db->query($search_query);
        if(!$success){
            Session::setFlash(ATXT_ERROR_RUNNING_QUERY . '[' . $db->error . ']','error');
            die(ATXT_ERROR_RUNNING_QUERY . '[' . $db->error . ']');
        }

        $found = $success-> num_rows;
        if($found > 0){
            $db->close();
            return TRUE;
        }else{
            $db->close();
            return FALSE;
        }

    }//END function checkIfFormExists($form_id_char)

    public static function checkIfInputExists($form_id,$input_name){//there won't be two the same forms with identical id_char

        $db = Model::getDB();
        $search_query = ("SELECT * FROM forms_created_inputs WHERE input_form_id = '$form_id' AND input_name = '$input_name'");
        $success = $db->query($search_query);
        if(!$success){
            Session::setFlash(ATXT_ERROR_RUNNING_QUERY . '[' . $db->error . ']','error');
            die(ATXT_ERROR_RUNNING_QUERY . '[' . $db->error . ']');
        }

        $found = $success-> num_rows;
        if($found > 0){
            $db->close();
            return TRUE;
        }else{
            $db->close();
            return FALSE;
        }

    }//END function checkIfFormExists($form_id_char)

    public static function getLastFormId(){//get last row and return id to increment for the very next form char id to generate / returns 0 if nothing in table

        $db = Model::getDB();
        $last_id = 0;
        $search_query = ("SELECT * FROM forms_created ORDER BY form_id LIMIT 1");
        $success = $db->query($search_query);
        if (!$success) {
            Session::setFlash(ATXT_ERROR_RUNNING_QUERY . '[' . $db->error . ']', 'error');
            die(ATXT_ERROR_RUNNING_QUERY . '[' . $db->error . ']');
        }

        $found = $success->num_rows;
        if ($found > 0) {
            while ($result = $success->fetch_assoc()) {
                $last_id = $result['form_id'];
            }
        }

        $db->close();
        return $last_id;

    }//END function getLastFormId()

    public static function getForms(){

        $db = Model::getDB();
        $forms = array();
        $get_query = ("SELECT * FROM forms_created ORDER BY form_edited DESC");
        $success = $db->query($get_query);
        if (!$success) {
            Session::setFlash(ATXT_ERROR_RUNNING_QUERY . '[' . $db->error . ']', 'error');
            die(ATXT_ERROR_RUNNING_QUERY . '[' . $db->error . ']');
        }

        $found = $success->num_rows;
        if ($found > 0) {
            while ($result = $success->fetch_assoc()) {
                array_push($forms,$result);
            }

            $db->close();
            return $forms;
        }else{

            $db->close();
            return FALSE;
        }

    }//END function getForms()

    public static function getFormsByCategory($category_id,$show_parent = 0){//gets only list of forms from given category

        $db = Model::getDB();
        $forms = array();
        $parent = array();

        if($show_parent == 1){

            $parent_query = ("SELECT * FROM categories WHERE id = '$category_id'");
            $success = $db->query($parent_query);
            if (!$success) {
                Session::setFlash(ATXT_ERROR_RUNNING_QUERY . '[' . $db->error . ']', 'error');
                die(ATXT_ERROR_RUNNING_QUERY . '[' . $db->error . ']');
            }

            $found = $success->num_rows;
            if ($found > 0) {
                while ($result = $success->fetch_assoc()) {
                    $parent['parent'] = $result;
                }

                $get_query = ("SELECT * FROM forms_created WHERE form_category = '$category_id' ORDER BY form_edited DESC");
                $success = $db->query($get_query);
                if (!$success) {
                    Session::setFlash(ATXT_ERROR_RUNNING_QUERY . '[' . $db->error . ']', 'error');
                    die(ATXT_ERROR_RUNNING_QUERY . '[' . $db->error . ']');
                }

                $found = $success->num_rows;
                if ($found > 0) {
                    while ($result = $success->fetch_assoc()) {
                        array_push($forms,$result);
                    }

                    $parent['parent']['forms'] = $forms;
                    $db->close();
                    return $parent;
                }else{

                    $db->close();
                    return FALSE;
                }

            }

        }else{

            $get_query = ("SELECT * FROM forms_created WHERE form_category = '$category_id' ORDER BY form_edited DESC");
            $success = $db->query($get_query);
            if (!$success) {
                Session::setFlash(ATXT_ERROR_RUNNING_QUERY . '[' . $db->error . ']', 'error');
                die(ATXT_ERROR_RUNNING_QUERY . '[' . $db->error . ']');
            }

            $found = $success->num_rows;
            if ($found > 0) {
                while ($result = $success->fetch_assoc()) {
                    array_push($forms,$result);
                }

                $db->close();
                return $forms;
            }else{

                $db->close();
                return FALSE;
            }

        }


    }//END function getForms()

    public static function getFormInputs($form_id){

        $db = Model::getDB();
        $inputs = array();
        $container = self::getEmptyForm($form_id);
        if($container['form_has_inputs'] != 0){

            $get_query = ("SELECT * FROM forms_created_inputs WHERE input_form_id = '$form_id' ORDER BY input_display_order DESC");
            $success = $db->query($get_query);
            if (!$success) {
                Session::setFlash(ATXT_ERROR_RUNNING_QUERY . '[' . $db->error . ']', 'error');
                die(ATXT_ERROR_RUNNING_QUERY . '[' . $db->error . ']');
            }

            while ($result = $success->fetch_assoc()) {
                array_push($inputs,$result);
            }

            $container['inputs'] = $inputs;

        }

        $db->close();
        return $container;
    }//END getFormInputs($form_id)

    public static function getInputTypes(){

        $db = Model::getDB();
        $types = array();
        $get_query = ("SELECT * FROM forms_created_inputs_types");
        $success = $db->query($get_query);
        if (!$success) {
            Session::setFlash(ATXT_ERROR_RUNNING_QUERY . '[' . $db->error . ']', 'error');
            die(ATXT_ERROR_RUNNING_QUERY . '[' . $db->error . ']');
        }

        while ($result = $success->fetch_assoc()) {
            array_push($types,$result);
        }

        $db->close();
        return $types;

    }//END function function getInputTypes()

    public static function getInputType($type_id){

        $db = Model::getDB();
        $type = '';
        $get_query = ("SELECT * FROM forms_created_inputs_types WHERE type_id = '$type_id'");
        $success = $db->query($get_query);
        if (!$success) {
            Session::setFlash(ATXT_ERROR_RUNNING_QUERY . '[' . $db->error . ']', 'error');
            die(ATXT_ERROR_RUNNING_QUERY . '[' . $db->error . ']');
        }

        while ($result = $success->fetch_assoc()) {
            $type = $result['type_name'];
        }

        $db->close();
        return $type;

    }//END function function getInputTypes()

    public static function getInputById($form_id,$input_id){

        $db = Model::getDB();
        $input = array();
        $get_query = ("SELECT * FROM forms_created_inputs WHERE input_form_id = '$form_id' AND input_id = '$input_id'");
        $success = $db->query($get_query);
        if (!$success) {
            Session::setFlash(ATXT_ERROR_RUNNING_QUERY . '[' . $db->error . ']', 'error');
            die(ATXT_ERROR_RUNNING_QUERY . '[' . $db->error . ']');
        }

        while ($result = $success->fetch_assoc()) {
            array_push($input, $result);
        }

        $db->close();
        return $input;

    }//END function function getInputTypes()

    public static function inputSaveStandard($input = array()){

        $input_form_id = $input['new-form-created-id'];
        $input_type = $input['new-form-input-type'];
        $input_label = isset($input['input-label']) ? $input['input-label'] : '';
        if($input_label != ''){

            $input_has_label = 1;

        }else{

            $input_has_label = 0;

        }
        $input_label_class = isset($input['input-label-class']) ? $input['input-label-class'] : '';
        $input_label_data_placement = isset($input['input-label-data-placement']) ? $input['input-label-data-placement'] : '';
        $input_label_data_toggle = isset($input['input-label-data-toggle']) ? $input['input-label-data-toggle'] : '';
        $input_label_data_original_title = isset($input['input-label-data-original-title']) ? $input['input-label-data-original-title'] : '';
        $input_label_title = isset($input['input-label-title']) ? $input['input-label-title'] : '';
        $input_name = $input['input-name'];
        $input_id_char = isset($input['input-id']) ? $input['input-id'] : '';
        $input_class = isset($input['input-class']) ? $input['input-class'] : '';
        $input_value = isset($input['input-value']) ? $input['input-value'] : '';
        $input_required = isset($input['input-required']) ? 1 : 0;
        $input_checked = isset($input['input-checked']) ? 1 : 0;
        $input_placeholder = isset($input['input-placeholder']) ? $input['input-placeholder'] : '';
        $input_disabled = isset($input['input-disabled']) ? 1 : 0;
        $input_maxlength = isset($input['input-maxlength']) ? $input['input-maxlength'] : 120;
        $input_display = isset($input['input-display-type']) ? $input['input-display-type'] : 'inline';
        $input_data_live_search = isset($input['input-live-search']) ? $input['input-live-search'] : 0;
        $input_data_live_search_placeholder = isset($input['input-live-search-placeholder']) ? $input['input-live-search-placeholder'] : '';
        $input_data_show_tick = isset($input['input-tick']) ? $input['input-tick'] : 0;

        $db = Model::getDB();

        $input_exists = self::checkIfInputExists($input_form_id,$input_name);

        if($input_exists != FALSE){

            $db->close();
            return FALSE;

        }else{

        $save_query = ("INSERT INTO forms_created_inputs
                        (input_form_id,
                         input_type,
                         input_has_label,
                         input_label,
                         input_label_class,
                         input_label_data_placement,
                         input_label_data_toggle,
                         input_label_data_original_title,
                         input_label_title,
                         input_name,
                         input_id_char,
                         input_class,
                         input_value,
                         input_required,
                         input_checked,
                         input_placeholder,
                         input_disabled,
                         input_maxlength,
                         input_data_show_tick,
                         input_data_live_search,
                         input_display,
                         input_data_live_search_placeholder)
                         VALUES (
                          '$input_form_id',
                          '$input_type',
                          '$input_has_label',
                          '$input_label',
                          '$input_label_class',
                          '$input_label_data_placement',
                          '$input_label_data_toggle',
                          '$input_label_data_original_title',
                          '$input_label_title',
                          '$input_name',
                          '$input_id_char',
                          '$input_class',
                          '$input_value',
                          '$input_required',
                          '$input_checked',
                          '$input_placeholder',
                          '$input_disabled',
                          '$input_maxlength',
                         '$input_data_show_tick',
                         '$input_data_live_search',
                          '$input_display',
                          '$input_data_live_search_placeholder')
                        ");

        $success = $db->query($save_query);
        if (!$success) {
            Session::setFlash(ATXT_ERROR_RUNNING_QUERY . '[' . $db->error . ']', 'error');
            die(ATXT_ERROR_RUNNING_QUERY . '[' . $db->error . ']');
        }

        $form_query = ("UPDATE forms_created SET form_has_inputs = 1 WHERE form_id = '$input_form_id'");
        $success = $db->query($form_query);
        if (!$success) {
            Session::setFlash(ATXT_ERROR_RUNNING_QUERY . '[' . $db->error . ']', 'error');
            die(ATXT_ERROR_RUNNING_QUERY . '[' . $db->error . ']');
        }

        $db->close();
        return TRUE;
      }
    }//END function function inputSaveStandart($data = array())

    public static function inputSaveHidden($input = array()){

        $input_form_id = $input['new-form-created-id'];
        $input_type = $input['new-form-input-type'];
        $input_has_label = 0;
        $input_name = $input['input-name'];
        $input_id_char = isset($input['input-id']) ? $input['input-id'] : '';
        $input_class = isset($input['input-class']) ? $input['input-class'] : '';
        $input_value = isset($input['input-value']) ? $input['input-value'] : '';

        $db = Model::getDB();

        $input_exists = self::checkIfInputExists($input_form_id,$input_name);

        if($input_exists != FALSE){

            $db->close();
            return FALSE;

        }else{

            $save_query = ("INSERT INTO forms_created_inputs
                        (input_form_id,
                         input_type,
                         input_has_label,
                         input_name,
                         input_id_char,
                         input_class,
                         input_value)
                         VALUES (
                          '$input_form_id',
                          '$input_type',
                          '$input_has_label',
                          '$input_name',
                          '$input_id_char',
                          '$input_class',
                          '$input_value')
                        ");

            $success = $db->query($save_query);
            if (!$success) {
                Session::setFlash(ATXT_ERROR_RUNNING_QUERY . '[' . $db->error . ']', 'error');
                die(ATXT_ERROR_RUNNING_QUERY . '[' . $db->error . ']');
            }

            $form_query = ("UPDATE forms_created SET form_has_inputs = 1 WHERE form_id = '$input_form_id'");
            $success = $db->query($form_query);
            if (!$success) {
                Session::setFlash(ATXT_ERROR_RUNNING_QUERY . '[' . $db->error . ']', 'error');
                die(ATXT_ERROR_RUNNING_QUERY . '[' . $db->error . ']');
            }

            $db->close();
            return TRUE;
        }
    }//END function inputSaveHidden($input = array())

    public static function inputSaveNumber($input = array()){

        $input_form_id = $input['new-form-created-id'];
        $input_type = $input['new-form-input-type'];
        $input_label = isset($input['input-label']) ? $input['input-label'] : '';
        if($input_label != ''){

            $input_has_label = 1;

        }else{

            $input_has_label = 0;

        }
        $input_label_class = isset($input['input-label-class']) ? $input['input-label-class'] : '';
        $input_label_data_placement = isset($input['input-label-data-placement']) ? $input['input-label-data-placement'] : '';
        $input_label_data_toggle = isset($input['input-label-data-toggle']) ? $input['input-label-data-toggle'] : '';
        $input_label_data_original_title = isset($input['input-label-data-original-title']) ? $input['input-label-data-original-title'] : '';
        $input_label_title = isset($input['input-label-title']) ? $input['input-label-title'] : '';
        $input_name = $input['input-name'];
        $input_id_char = isset($input['input-id']) ? $input['input-id'] : '';
        $input_class = isset($input['input-class']) ? $input['input-class'] : '';
        $input_value = isset($input['input-value']) ? $input['input-value'] : '';
        $input_min = isset($input['input-min']) ? $input['input-min'] : '';
        $input_max = isset($input['input-max']) ? $input['input-max'] : '';
        $input_step = isset($input['input-step']) ? $input['input-step'] : '';
        $input_required = isset($input['input-required']) ? 1 : 0;
        $input_disabled = isset($input['input-disabled']) ? 1 : 0;

        $db = Model::getDB();

        $input_exists = self::checkIfInputExists($input_form_id,$input_name);

        if($input_exists != FALSE){

            $db->close();
            return FALSE;

        }else{

            $save_query = ("INSERT INTO forms_created_inputs
                        (input_form_id,
                         input_type,
                         input_has_label,
                         input_label,
                         input_label_class,
                         input_label_data_placement,
                         input_label_data_toggle,
                         input_label_data_original_title,
                         input_label_title,
                         input_name,
                         input_id_char,
                         input_class,
                         input_value,
                         input_required,
                         input_disabled,
                         input_min,
                         input_max,
                         input_step)
                         VALUES (
                          '$input_form_id',
                          '$input_type',
                          '$input_has_label',
                          '$input_label',
                          '$input_label_class',
                          '$input_label_data_placement',
                          '$input_label_data_toggle',
                          '$input_label_data_original_title',
                          '$input_label_title',
                          '$input_name',
                          '$input_id_char',
                          '$input_class',
                          '$input_value',
                          '$input_required',
                          '$input_disabled',
                          '$input_min',
                          '$input_max',
                          '$input_step')
                        ");

            $success = $db->query($save_query);
            if (!$success) {
                Session::setFlash(ATXT_ERROR_RUNNING_QUERY . '[' . $db->error . ']', 'error');
                die(ATXT_ERROR_RUNNING_QUERY . '[' . $db->error . ']');
            }

            $form_query = ("UPDATE forms_created SET form_has_inputs = 1 WHERE form_id = '$input_form_id'");
            $success = $db->query($form_query);
            if (!$success) {
                Session::setFlash(ATXT_ERROR_RUNNING_QUERY . '[' . $db->error . ']', 'error');
                die(ATXT_ERROR_RUNNING_QUERY . '[' . $db->error . ']');
            }

            $db->close();
            return TRUE;
        }
    }//END function function inputSaveStandart($data = array())

        public static function getSelectOptions($form_id, $select_id){

            $db = Model::getDB();
            $select = array();
            $get_query = ("SELECT * FROM forms_created_inputs WHERE input_form_id = '$form_id' AND input_id = '$select_id' AND input_type = '13'");
            $success = $db->query($get_query);
            if (!$success) {
                Session::setFlash(ATXT_ERROR_RUNNING_QUERY . '[' . $db->error . ']', 'error');
                die(ATXT_ERROR_RUNNING_QUERY . '[' . $db->error . ']');
            }

            $found = $success->num_rows;
            if ($found > 0) {
                while ($result = $success->fetch_assoc()) {
                    array_push($select,$result);
                }
            }

            $options_query = ("SELECT * FROM forms_created_select_options WHERE option_select_id = '$select_id'");
            $success = $db->query($options_query);
            if (!$success) {
                Session::setFlash(ATXT_ERROR_RUNNING_QUERY . '[' . $db->error . ']', 'error');
                die(ATXT_ERROR_RUNNING_QUERY . '[' . $db->error . ']');
            }

            $found = $success->num_rows;
            if ($found > 0) {

                $select['has_options'] = 1;
                $select['options'] = array();

                while ($result = $success->fetch_assoc()) {
                    array_push($select['options'],$result);
                }

            }else{

                $select['has_options'] = 0;
                $select['options'] = array();

            }

            $db->close();
            return $select;

        }//END function getForms()

    public static function getSelectOptionsBySelect($select_id){

        $db = Model::getDB();
        $select = array();
        $get_query = ("SELECT * FROM forms_created_inputs WHERE input_id = '$select_id' AND input_type = '13'");
        $success = $db->query($get_query);
        if (!$success) {
            Session::setFlash(ATXT_ERROR_RUNNING_QUERY . '[' . $db->error . ']', 'error');
            die(ATXT_ERROR_RUNNING_QUERY . '[' . $db->error . ']');
        }

        $found = $success->num_rows;
        if ($found > 0) {
            while ($result = $success->fetch_assoc()) {
                array_push($select,$result);
            }
        }

        $options_query = ("SELECT * FROM forms_created_select_options WHERE option_select_id = '$select_id'");
        $success = $db->query($options_query);
        if (!$success) {
            Session::setFlash(ATXT_ERROR_RUNNING_QUERY . '[' . $db->error . ']', 'error');
            die(ATXT_ERROR_RUNNING_QUERY . '[' . $db->error . ']');
        }

        $found = $success->num_rows;
        if ($found > 0) {

            $select['has_options'] = 1;
            $select['options'] = array();

            while ($result = $success->fetch_assoc()) {
                array_push($select['options'],$result);
            }

        }else{

            $select['has_options'] = 0;
            $select['options'] = array();

        }

        $db->close();
        return $select;

    }//END function getForms()

    public static function selectInsertNewOption($data = array()){

        $db = Model::getDB();
        $option_select_id = $data['option_select_id'];
        $option_value = $data['option_value'];
        $option_data_subtext = $data['option_data_subtext'];
        $option_data_tokens = $data['option_data_tokens'];
        $option_text = $data['option_text'];

        $exists = self::checkIfOptionExists($data);
        if($exists == TRUE){

            $db->close();
            Session::setFlash(ATXT_FORMS_SELECT_OPTION_EXISTS, 'error');
            return FALSE;

        }

        $insert_query = ("INSERT INTO forms_created_select_options (
                           option_select_id,
                           option_value,
                           option_data_subtext,
                           option_data_tokens,
                           option_text
                           ) VALUES (
                            '$option_select_id',
                            '$option_value',
                            '$option_data_subtext',
                            '$option_data_tokens',
                            '$option_text'
                           )");
        $success = $db->query($insert_query);
        if (!$success) {
            Session::setFlash(ATXT_ERROR_RUNNING_QUERY . '[' . $db->error . ']', 'error');
            die(ATXT_ERROR_RUNNING_QUERY . '[' . $db->error . ']');
        }

        $db->close();
        return TRUE;

    }//end function selectInsertNewOption($data = array())

    public static function checkIfOptionExists($data = array()){

        $db = Model::getDB();
        $option_select_id = $data['option_select_id'];
        $option_value = $data['option_value'];

        $check_query = ("SELECT * FROM forms_created_select_options WHERE option_select_id = '$option_select_id' AND option_value = '$option_value'");

        $success = $db->query($check_query);
        if (!$success) {
            Session::setFlash(ATXT_ERROR_RUNNING_QUERY . '[' . $db->error . ']', 'error');
            die(ATXT_ERROR_RUNNING_QUERY . '[' . $db->error . ']');
        }

        $found = $success->num_rows;
        if ($found > 0) {//if there is already option with same value

            $db->close();
            return TRUE;

        }else{

            $db->close();
            return FALSE;

        }

    }//END function checkIfOptionExists($data = array())

    public static function deleteSelectOption($select_id, $option_id){

        $db = Model::getDB();
        $delete_query = ("DELETE FROM forms_created_select_options WHERE option_select_id = '$select_id' AND option_id = '$option_id'");

        $success = $db->query($delete_query);
        if (!$success) {
            Session::setFlash(ATXT_ERROR_RUNNING_QUERY . '[' . $db->error . ']', 'error');
            die(ATXT_ERROR_RUNNING_QUERY . '[' . $db->error . ']');
        }

        $db->close();
        return TRUE;

    }//END function deleteSelectOption($select_id, $option_id)

    public static function getSelectOptionById($option_id){

        $db = Model::getDB();
        $option = array();
        $delete_query = ("SELECT * FROM forms_created_select_options WHERE option_id = '$option_id'");

        $success = $db->query($delete_query);
        if (!$success) {
            Session::setFlash(ATXT_ERROR_RUNNING_QUERY . '[' . $db->error . ']', 'error');
            die(ATXT_ERROR_RUNNING_QUERY . '[' . $db->error . ']');
        }

        $found = $success->num_rows;

        if ($found > 0){

            while($result =  $success->fetch_assoc()){
                 array_push($option,$result);
            }

            $db->close();
            return $option;

        }

        $db->close();
        return FALSE;

    }//END function function getSelectOptionById($option_id)

    public static function selectEditOption($data = array()){

        $db = Model::getDB();
        $option_select_id = $data['option_select_id'];
        $option_value = $data['option_value'];
        $option_data_subtext = $data['option_data_subtext'];
        $option_data_tokens = $data['option_data_tokens'];
        $option_text = $data['option_text'];
        $option_id = $data['option_id'];

        $exists = self::checkIfOptionExists($data);
        if($exists == FALSE){

            $db->close();
            Session::setFlash(ATXT_FORMS_SELECT_OPTION_NOT_EXISTS, 'error');
            return FALSE;

        }

        $edit_query = ("UPDATE forms_created_select_options SET
                           option_select_id = '$option_select_id',
                           option_value = '$option_value',
                           option_data_subtext = '$option_data_subtext',
                           option_data_tokens = '$option_data_tokens',
                           option_text = '$option_text' WHERE option_id = '$option_id'");
        $success = $db->query($edit_query);
        if (!$success) {
            Session::setFlash(ATXT_ERROR_RUNNING_QUERY . '[' . $db->error . ']', 'error');
            die(ATXT_ERROR_RUNNING_QUERY . '[' . $db->error . ']');
        }

        $db->close();
        return TRUE;

    }//end function selectInsertNewOption($data = array())

    public static function getInputFormTitle($input_id){

        $db = Model::getDB();
        $form_name = '';
        $form_id = '';
        $get_query = ("SELECT * FROM forms_created_inputs WHERE input_id = '$input_id'");
        $success = $db->query($get_query);
        if (!$success) {
            Session::setFlash(ATXT_ERROR_RUNNING_QUERY . '[' . $db->error . ']', 'error');
            die(ATXT_ERROR_RUNNING_QUERY . '[' . $db->error . ']');
        }
        $found = $success->num_rows;

        if ($found > 0){

            while($result =  $success->fetch_assoc()){
                $form_id = $result['input_form_id'];
            }

        }

        $name_query = ("SELECT * FROM forms_created WHERE form_id = '$form_id'");
        $success = $db->query($name_query);
        if (!$success) {
            Session::setFlash(ATXT_ERROR_RUNNING_QUERY . '[' . $db->error . ']', 'error');
            die(ATXT_ERROR_RUNNING_QUERY . '[' . $db->error . ']');
        }
        $found = $success->num_rows;

        if ($found > 0){

            while($result =  $success->fetch_assoc()){
                $form_name = $result['form_title'];
            }

        }

        $db->close();
        return $form_name;

    }//end function selectInsertNewOption($data = array())

    public static function editInputForm($data = array()){

        $db = Model::getDB();
        $input_id = $data['input-id'];
        $input_label = $data['input-label'];
        $input_has_label = $data['input-has-label'];
        $input_label_class = $data['input-label-class'];
        $input_label_data_placement = $data['input-label-data-placement'];
        $input_label_data_toggle = $data['input-label-data-toggle'];
        $input_label_data_original_title = $data['input-label-data-original-title'];
        $input_label_title = $data['input-label-title'];
        $input_name = $data['input-name'];
        $input_id_char = $data['input-id-char'];
        $input_class = $data['input-class'];
        $input_value = $data['input-value'];
        $input_placeholder = $data['input-placeholder'];
        $input_maxlength = $data['input-maxlength'];
        $input_required = $data['input-required'];
        $input_disabled = $data['input-disabled'];
        $input_checked = $data['input-checked'];
        $input_display = $data['input-display'];
        $input_values = $data['input-values'];
        $input_rows = $data['input-rows'];
        $input_data_show_tick = $data['input-data-show-tick'];
        $input_data_live_search = $data['input-data-live-search'];
        $input_data_live_search_placeholder = $data['input-data-live-search-placeholder'];
        $input_min = $data['input-min'];
        $input_max = $data['input-max'];
        $input_step = $data['input-step'];
        $input_datepicker = $data['input-datepicker'];
        $input_onclick = $data['input-onclick'];

        $edit_query = ("UPDATE forms_created_inputs SET
                           input_has_label = '$input_has_label',
                           input_label = '$input_label',
                           input_label_class = '$input_label_class',
                           input_label_data_placement = '$input_label_data_placement',
                           input_label_data_toggle = '$input_label_data_toggle',
                           input_label_data_original_title = '$input_label_data_original_title',
                           input_label_title = '$input_label_title',
                           input_name = '$input_name',
                           input_id_char = '$input_id_char',
                           input_class = '$input_class',
                           input_value = '$input_value',
                           input_placeholder = '$input_placeholder',
                           input_maxlength = '$input_maxlength',
                           input_required = '$input_required',
                           input_disabled = '$input_disabled',
                           input_checked = '$input_checked',
                           input_display = '$input_display',
                           input_values = '$input_values',
                           input_rows = '$input_rows',
                           input_data_show_tick = '$input_data_show_tick',
                           input_data_live_search = '$input_data_live_search',
                           input_data_live_search_placeholder = '$input_data_live_search_placeholder',
                           input_min = '$input_min',
                           input_max = '$input_max',
                           input_step = '$input_step',
                           input_datepicker = '$input_datepicker',
                           input_onclick = '$input_onclick'
                           WHERE input_id = '$input_id'");

        $success = $db->query($edit_query);
        if (!$success) {
            Session::setFlash(ATXT_ERROR_RUNNING_QUERY . '[' . $db->error . ']', 'error');
            die(ATXT_ERROR_RUNNING_QUERY . '[' . $db->error . ']');
        }

        $db->close();
        return TRUE;

    }//END function editInputForm($data = array())


}//END CLASS