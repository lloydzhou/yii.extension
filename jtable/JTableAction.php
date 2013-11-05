<?php
/*
 * Created on Oct 26, 2013
 *
 * @author Lloyd Zhou
 * @email lloyd.zhou@newbiiz.com
 */
?>
<?php
class JTableAction extends CAction
{
    public $model;
    public function run($depth = 1, $pageSize = 10)
    {
		//echo 'JTableAction';Yii::app()->end();
		if(is_string($this->model)) $this->model = CActiveRecord::model($this->model);
		$id = CHtml::ID_PREFIX.get_class($this->model).CHtml::$count++;
		if (!isset($this->model->getOptions)) $this->model->attachBehavior('jtable', new JActiveRecordBehavior);
		$options = CJavaScript::encode($this->model->getOptions('', $id, $depth, $pageSize));
		$assets = CHtml::asset(dirname(__FILE__) . '/assets/');
		$cs = Yii::app()->clientScript;
		$cs->registerCoreScript('jquery');
		$cs->registerCoreScript('jquery.ui');
		//var_dump($cs->getCoreScriptUrl());
		$cs->addPackage('jtable', array(
			'js' => array($assets. '/jquery.jtable.restful.js'),
			'css' => array($assets. '/themes/metro/blue/jtable.css'),
			//'depends' => array('jquery.ui'),
		));
		$cs->registerScriptFile($assets. '/jquery.jtable.restful.js');
		$cs->registerCssFile($assets. '/themes/lightcolor/blue/jtable.min.css');
		$cs->registerCssFile($cs->getCoreScriptUrl(). '/jui/css/base/jquery-ui.css');
		$this->controller->renderText ( 
<<<HTML
<div id="{$id}" style="width: 99%;"></div>
<script type="text/javascript">

	$(document).ready(function () {
		//Prepare jTable
		$('#{$id}').jtable({$options});
		//Load person list from server
		$('#{$id}').jtable('load');
	});

</script>

HTML
);
	}
}
