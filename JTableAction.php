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
    public function run($id = null)
    {
		if(is_string($this->model)) $this->model = CActiveRecord::model($this->model);
		$id = CHtml::ID_PREFIX.get_class($this->model).CHtml::$count++;
		$options = CJavaScript::encode($this->model->getOptions('_', $id));
		//$this->controller->renderText
		echo ( 
<<<HTML
<html>
  <head>

<link href="/jtable/samples/Codes/themes/redmond/jquery-ui-1.8.16.custom.css" rel="stylesheet" type="text/css" />
<link href="/jtable/samples/Codes/Scripts/jtable/themes/lightcolor/blue/jtable.css" rel="stylesheet" type="text/css" />
<script src="/jtable/samples/Codes/scripts/jquery-1.6.4.min.js" type="text/javascript"></script>
<script src="/jtable/samples/Codes/scripts/jquery-ui-1.8.16.custom.min.js" type="text/javascript"></script>
<script src="/jtable/jtable/dev/jquery.jtable.js" type="text/javascript"></script>
  </head>
  <body>
<div id="{$id}" style="width: 99%;"></div>
<script type="text/javascript">

	$(document).ready(function () {
		//Prepare jTable
		$('#{$id}').jtable({$options});
		//Load person list from server
		$('#{$id}').jtable('load');
	});

</script>
  </body>
</html>

HTML
);
	}
}
