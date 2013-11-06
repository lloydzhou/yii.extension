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
	public $depth = 1;
	public $baseUrl = 'http://stt2013yii.newbiiz.com';
	public $baseImageUrl = 'http://stt2013yii.newbiiz.com';
    public function run($pageSize = 10)
    {
		
		if(is_string($this->model)) $this->model = CActiveRecord::model($this->model);
		$id = CHtml::ID_PREFIX.get_class($this->model).CHtml::$count++;
		if (!isset($this->model->getOptions)) $this->model->attachBehavior('jtable', new JActiveRecordBehavior);
		$options = CJavaScript::encode($this->model->getOptions('', $id, $this->depth, $pageSize));
		$assets = CHtml::asset(dirname(__FILE__) . '/assets/');
		$cs = Yii::app()->clientScript;
		$cs->registerCoreScript('jquery');
		$cs->registerCoreScript('jquery.ui');
		$cs->registerScriptFile($assets. '/jquery.jtable.restful.js');
		$cs->registerCssFile($assets. '/themes/lightcolor/blue/jtable.css');
		$cs->registerCssFile($cs->getCoreScriptUrl(). '/jui/css/base/jquery-ui.css');
		$this->controller->renderText ( 
<<<HTML
<div id="{$id}" style="width: 99%;"></div>
<script type="text/javascript">
	var formatData = function (data, key)
	{	
		var val = data.record[key], baseUrl = "{$this->baseUrl}", baseImg = "{$this->baseImageUrl}";
		return (val) ? (/\.(jpg|png|gif)/.test(val)) ? '<img src="_"/>'.replace('_', baseImg + val) 
			: (/^\//.test(val)) ? '<a href="___">__</a>'.replace('___', baseUrl + val).replace('__', val) : val : '';
	}
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
