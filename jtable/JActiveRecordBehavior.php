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

class JActiveRecordBehavior extends CBehavior
{
	public $totalRecordCount = 0;
	public function getData()
	{
		$criteria=new CDbCriteria;
		foreach($this->owner->attributes as $k=>$c)
			if (isset($_GET[$k]) || isset($_POST[$k])) 
            {
                $c = $this->owner->tableSchema->getColumn($k);
            	$criteria->compare($k,Yii::app()->request->getParam($k), !('string' != $c->type || $c->isForeignKey || $c->isPrimaryKey));
            }

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
	public function getActions($relation = false)
	{
		$C = Yii::app()->controller;
		$route = lcfirst(get_class($this->owner));
		$params = array();
		if ($relation) $params[$relation] = '__relation__';
		$paramsId = array_merge(array('id' => '__key__'), $params);
		return array(
			'listAction' => array('url' => $C->createUrl($route, $params), 'type' => 'GET'),
			'createAction' => array('url' => $C->createUrl($route, $params), 'type' => 'POST'),
			'updateAction' => array('url' => str_replace('__key__', '{key}', $C->createUrl($route, $paramsId)), 'type' => 'PUT'),
			'deleteAction' => array('url' => str_replace('__key__', '{key}', $C->createUrl($route, $paramsId)), 'type' => 'DELETE'),
		);
	}

	public function getOptions($relation = false, $id = '', $depth = 1, $pageSize = 10)
	{
		// columns
		$fields = $this->getFields($relation);
		
        $relations = array();
        $name =get_class($this->owner);
        $key = $this->owner->getPrimaryKey();
		if (!$key) $key = 'id';
        
		/* relations */
		if ($depth > 0)
		foreach($this->owner->relations() as $k=>$r)
		{
			if ('CHasManyRelation' === $r[0])
			{
				$model = CActiveRecord::model($r[1]);
				if (!isset($model->getOptions)) $model->attachBehavior('jtable', new JActiveRecordBehavior);
				$o = CJavaScript::encode($model->getOptions($r[2], $id, --$depth, $pageSize));
                
				$assets = CHtml::asset(dirname(__FILE__) . '/assets/');
				$relations[$k] = array('title' => '','width' => '3%','sorting' => false,'edit' => false,'create' => false,
					'display' => 	new CJavaScriptExpression(
<<<JS
js:function(data){
	var \$img = \$('<img src="$assets/list_metro.png" title="Edit {$r[1]}" />');
	//Open child table when user clicks the image
	var o = {$o};
	for (var i in o.actions) if (data.record.{$key}) o.actions[i].url = o.actions[i].url.replace(/__relation__/g, data.record.{$key});
	o.title += ' belongs to {$name} #' + data.record.{$key}
	\$img.toggle(function () {
		\$('#{$id}').jtable('openChildTable',
			\$img.closest('tr'),
			o, 
			function (data) { data.childTable.jtable('load');}
		)
	}, function (){ $('#{$id}').jtable('closeChildTable', \$img.closest('tr'));})
	return \$img;
}
JS
));
			}
		}
		
		return array(
			'title' => 'Table of '. $name,
			'paging' => true,
			'pageSize' => $pageSize,
			'sorting' => true,
			'defaultSorting' => $key. ' DESC',
			'actions' => $this->owner->getActions($relation),
			'fields' => array_merge($relations, $fields)
		);
	}
	public function getFields($relation = false)
	{
		$fields = array();
		foreach($this->owner->tableSchema->columns as $k=>$c)
		{
			$title = $this->owner->getAttributeLabel($c->name);
			if ($c->isPrimaryKey)
				$fields[$k] = array('title' => $title, 'key' => true, 'create' => false,'edit'=> false,'list' => true);
			else if ($c->name == $relation)
				$fields[$k] = array('title' => $title, 'key' => false, 'create' => false);
			else $fields[$k] = array('title' => $title, 'list' => 'password' === $k ? false : true);
			$fields[$k]['display'] = "js:function (data){ return formatData(data, '$k');}";
		}
		return $fields;
	}
}
