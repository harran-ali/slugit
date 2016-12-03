<?php 

namespace Harran\Slugit\Services;

class SlugService{

	private $settings;
	private $model;

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

			if($this->settings['unique'])
			{
				$slug = $this->makeUnique($slug, $slugField);
			}
			$model->{$slugField} = $slug;
		}
		return true;
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

	/**
     * make slug unique by appending a number at the end
     *
     * @param  string $slug
     * @param  string $slugField
     * @return string
     */
	private function makeUnique($slug, $slugField){
	 	$row = $this->model->where($slugField, 'like', '%' . $slug . '%')->orderBy($slugField, 'desc')->first();
	 	if( is_null($row) ){
	 		return $slug . $this->settings['separator'] . 1;
	 	}

	 	$startingDigit = strrchr($row->{$slugField}, $this->settings['separator'] );
	 	$startingDigit = (int) str_replace($this->settings['separator'], '', $startingDigit);
	 	$slug = $this->recursiveMakeUnique($startingDigit + 1, $slug, $slugField);
	 	return $slug;
	}

	/**
     * make slug unique by appending a number at the end and checking the database recursively
     *
     * @param  int    $startingDigit
     * @param  string $slug
     * @return string
     */
	private function recursiveMakeUnique($startingDigit, $slug, $slugField){
		$buildedSlug = $slug . $this->settings['separator'] . $startingDigit;
	 	$row = $this->model->where($slugField, '=',  $buildedSlug )->orderBy($slugField, 'desc')->first();
	 	if(is_null($row))
	 	{
	 		return $buildedSlug;
	 	}
	 	$this->recursiveMakeUnique($startingDigit +1, $slug, $slugField);
	}
}