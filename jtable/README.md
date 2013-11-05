yii.extension
=============

jtable 
===

please copy the files into you extension folder of you system.
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
config it in mian.php
```php
	'import'=>array(
		'application.models.*',
		'application.components.*',
		'application.extensions.jtable.*',
	),
```
