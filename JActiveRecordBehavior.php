<?php
/*
 * Created on Oct 26, 2013
 *
 * @author Lloyd Zhou
 * @email lloyd.zhou@newbiiz.com
 */
?>
<?php
class JPagination extends CPagination
{
	public function __construct($itemCount=0)
	{
		parent::__construct($itemCount);
		$this->pageSize = (int)Yii::app()->request->getParam('jtPageSize', 10);
	}

	public function getOffset()
	{
		return (int)Yii::app()->request->getParam('jtStartIndex', 0);
	}
}
//http://localhost/jtable/samples/Codes/PersonActionsPagedSorted.php?action=list&jtStartIndex=0&jtPageSize=5&jtSorting=Name%20ASC
class JActiveRecordBehavior extends CBehavior
{
	public $totalRecordCount = 0;
	public function getData()
	{
		$this->owner->attributes = $_GET;
		var_dump($_GET);
		var_dump($this->owner->attributes);
		$criteria=new CDbCriteria;
		foreach($this->owner->tableSchema->columns as $k=>$c)
			$criteria->compare($k,$this->owner->$k);
		//$criteria->compare('password',$this->owner->password);
		
		$dp = new CActiveDataProvider($this->owner, array(
			'criteria'=>$criteria,
			'sort'=>array(
				'defaultOrder'=> Yii::app()->request->getParam('jtSorting'),
			),
			'pagination' =>new JPagination,
		));
		$datas = $dp->getData();
		$this->totalRecordCount = $dp->pagination->getItemCount();
		return $datas;
	}
	public function getOptions($relation = false, $id = '', $depth = 1)
	{
		$C = Yii::app()->controller;
		$name = get_class($this->owner);
		$route = lcfirst($name);
		$options = array(
			'title' => 'Table of '. $name,
			'paging' => true,
			'pageSize' => 10,
			'sorting' => true,
			'defaultSorting' => 'id desc',
			'actions' => array(
				'listAction' => array('url' => $C->createUrl($route), 'type' => 'GET'),
				'createAction' => array('url' => $C->createUrl($route), 'type' => 'POST'),
				'updateAction' => array('url' => $C->createUrl($route, array('id' => '')). '{key}', 'type' => 'PUT'),
				'deleteAction' => array('url' => $C->createUrl($route, array('id' => '')). '{key}', 'type' => 'DELETE'),
			),
			'fields' => array()
		);
		foreach($this->owner->tableSchema->columns as $k=>$c)
		{
			$title = $this->owner->getAttributeLabel($c->name);
			if ($c->isPrimaryKey || $c->name == $relation)
				$options['fields'][$k] = array('title' => $title, 'key' => true, 'create' => false,'edit'=> false,'list' => false);
			else $options['fields'][$k] = array('title' => $title);
		}
		
		if ($depth)
		foreach($this->owner->relations() as $k=>$r)
		{
			$o = CJavaScript::encode(CActiveRecord::model($r[1])->getOptions($r[2], $id, --$depth));
			$options['fields'][$k] = array('title' => '','width' => '5%','sorting' => false,'edit' => false,'create' => false,
				'display' => 	new CJavaScriptExpression(
<<<JS
js:function(data){
	var \$img = \$('<img src="/jtable/samples/Codes/scripts/list_metro.png" title="Edit {$r[1]}" />');
	//Open child table when user clicks the image
	\$img.click(function () {
		var o = {$o};
		for (var i in o.actions) if (data.record.{$r[2]}) o.actions[i].url += '&{$r[2]}=' + data.record.{$r[2]};
		\$('#{$id}').jtable('openChildTable',
			\$img.closest('tr'),
			o, 
			function (data) { data.childTable.jtable('load');}
		)
	})
	return \$img;
}
JS
));
			
			//var_dump($k);
		}
		return $options;
	}
	public function getJTableFields()
	{
		$fields = array();
		return $fields;
	}
}
