<?php
/*
 * Created on Oct 26, 2013
 *
 * @author Lloyd Zhou
 * @email lloyd.zhou@newbiiz.com
 */
?>
<?php
class JRestAction extends CAction
{
    public $model;
    /*
     * this array need to reset. 
     */
    public $routes = array(
        'GET' => array('getData', 'id' => 'findByPk'),
        'PUT' => array('id' => 'save'),
        'POST' => array('save'),
        'DELETE' => array('id' => 'delete'),
    );
    public function run($id = null)
    {
        /* 
         * Set handle function to get the RROR CODE, and the ERROR MESSAGE.
         * We can easy to send error code from model to this action. 
         * */
        set_error_handler(function ($errno, $errstr, $file, $line){
            call_user_func('JRestAction::renderJSON', null, true, $errstr. ' in file: '. $file. ' on line'. $line, 500);
        });
        set_exception_handler(function ($e){
            call_user_func('JRestAction::renderJSON', null, true, $e->getMessage(), property_exists($e, 'statusCode') ? $e->statusCode : false);
        });
		
        $request = Yii::app()->request;
		//if (!$id) $id = $request->getRestParam('id');
		$type = $request->requestType;
        /* get the active record by using $id, if $id not set create a new object.*/
        $model = $id ? CActiveRecord::model($this->model)->findByPk($id) : new $this->model;
        /* 
         * if can not find the record by $id, throw Exception with message and code. 
         * also can using "trigger_error" to handle it, but can not set the special ERROR CODE.
         */
        if (!$model) throw new Exception ('record not found.', -100); 
        if (array_key_exists($type, $this->routes))
        {
            if ('PUT' == $type) $model->attributes = $request->getRestParams();//$request->getPut($this->model);
            if ('POST' == $type) $model->attributes = $_POST;//$request->getPost($this->model);
            $result = ($id && array_key_exists('id', $this->routes[$type]))
                    ? ('GET' == $type && 'findByPk' == $this->routes[$type]['id']) 
                        ? $model 
                        : call_user_func(array($model, $this->routes[$type]['id']),$id)
                    : call_user_func(array($model, current($this->routes[$type])));
            if ($result) 
                self::renderJSON(is_bool($result) ? $model : $result, false, 'get response for request '. $type. ' successed.', isset($model->totalRecordCount) ? $model->totalRecordCount : false);
            else 
                self::renderJSON(null, true, $model ? CHtml::errorSummary($model) : '');
        }
        trigger_error ('invalid request.');
    }
    public static function renderJSON($data, $error = false, $message = '', $totalRecordCount = false)
    {
		if ($error) 
			echo CJSON::encode(array('Result' => 'ERROR', 'Message' => $message));
        if ($totalRecordCount)
			echo CJSON::encode(array('Result' => 'OK', 'Records' => $data, 'TotalRecordCount' => $totalRecordCount));
		else 
	        echo CJSON::encode(array('Result' => 'OK', 'Record' => $data));
        Yii::app()->end();
    }
}
