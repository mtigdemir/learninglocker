<?php namespace Controllers\xAPI;

use Illuminate\Routing\Controller;

class BaseController extends Controller {


	public function returnJSON( $results=array(), $additional_params=array(), $extra=array(), $debug=array() ){

        $json = array(
            'version'   =>  \Config::get('api.using_version'),
            'route'     =>  \Request::path()
        );


        $json['url_params'] = \Route::getCurrentRoute()->parameters();

        $params = \Input::all();

        if( sizeof($additional_params) > 0 ){
            $params = array_merge( $params, $additional_params);
        }
        $json['params'] = $params;


        if( sizeof($extra)>0){
            $json = array_merge( $json, $extra );
        }
        
        $json['results'] = $results;

        if( \Config::get('app.debug') ){
            $json['debug'] = array(
                'sql'   =>  \DB::getQueryLog()
            );

            $json['debug'] = array_merge($json['debug'], $debug );
        }


        return \Response::json( $json );
        
    }

	/**
	* GENERIC MODEL HANDLING
	**/

	protected function findModel( $modelType, $id, $with=array() ){
		$model = $modelType::with( $with )->find($id);

		if( is_null($model)){
			\App::abort(404, 'Model not found');

		} else {
			return $model;
		}
	}

	protected function returnModel($model){
		return $this->returnJSON( $model->toArray() );
	}

	protected function returnSuccessError( $success, $message, $code ){
		return \Response::json( array( 'success'  => $success, 
									   'message'  => $message), 
										$code );
	}

}