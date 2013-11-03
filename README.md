yii.extension
=============

jtable 

===
config it in controller
```php
	public function actions()
	{
		return array(
			'user'=>array(
				'class' => 'JRestAction',
				'model' => 'User',
			),
			'useradmin'=>array(
				'class' => 'JTableAction',
				'model' => 'User',
			),
			'post'=>array(
				'class' => 'JRestAction',
				'model' => 'Post',
			),
			'comment'=>array(
				'class' => 'JRestAction',
				'model' => 'Comment',
			),
		);
	}
```
