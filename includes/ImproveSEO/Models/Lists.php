<?php

namespace ImproveSEO\Models;

class Lists extends AbstractModel
{
	public $table = 'improveseo_lists';

	public $timestamps = false;

	protected $fillable = array(
		'name',
		'list',
		'size'
	);

	public function setNameAttribute($value) 
	{
		$value = str_replace(' ', '-', strtolower($value));
		return $value;
	}
}