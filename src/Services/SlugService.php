<?php 

namespace Harran\Slugit\Services;

class SlugService{

	private $settings;
	private $model;
	private $finalSlug;

	public function __construct(){
		$this->settings = config()->get('slugit');
	}

	/**
     * Generate the slug for a specific model
     *
     * @param  Illuminate\Database\Eloquent\Model  $model
     * @param  array  $sttings
     * @return true
     */
	public function generate($model, $sttings){

		$this->model = $model;
		foreach ($sttings as $slugField => $source) {
			$slug = $this->buildSlugBase($model->{$source});
			if( !empty($model->{$slugField}) )  // needs a config option
				return true;
			if($this->settings['unique'])
			{
				$slug = $this->makeUnique($slug, $slugField);
			}
			$model->{$slugField} = $this->finalSlug[0];
		}
	}

	/**
     * make slug unique by appending a number at the end
     *
     * @param  string $slug
     * @param  string $slugField
     * @return string
     */
	private function makeUnique($slug, $slugField, $startingDigit = 1){
	 	$row = $this->model->where($slugField, '=',  $slug  )->orderBy($slugField, 'desc')->first();
	 	if( is_null($row) ){
	 		$this->finalSlug[] = $slug;
	 		return $slug;
	 	}

	 	// 
	 	$buildedSlug = $slug . $this->settings['separator'] . $startingDigit;
	 	$row = $this->model->where($slugField, '=',  $buildedSlug )->first();

	 	if( !is_null($row) )
	 	{
	 		$this->makeUnique( $slug, $slugField, $startingDigit +1 );
	 	}

	 	$this->finalSlug[] = $buildedSlug;
	 	return $buildedSlug;

	}


	/**
     * convert a string to slug format
     *
     * @param  string $string
     * @return string
     */
	private function buildSlugBase($string){
	 	//remove all special chars
	    $string = preg_replace('~[^\pL\d ]+~u', '', $string);

	 	//convert white multiple spaces to one
	 	$string = preg_replace('!\s+!', ' ', $string);

	 	//trim white spaces 
	 	$string = trim($string);

	 	//string to lower case 
	 	$string = mb_strtolower($string);

	 	// generate the slug
	 	$string = str_replace(' ', $this->settings['separator'] , $string);

	    return $string;
	}
}