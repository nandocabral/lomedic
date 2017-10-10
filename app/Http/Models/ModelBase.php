<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\HtmlString;

class ModelBase extends Model
{
	/**
	 * Los atributos que seran visibles en index-datable
	 * @var null|array
	 */
	protected $fields = null;

	/**
	 * Atributos de carga optimizada
	 * @var array
	 */
	protected $eagerLoaders = [];

	/**
	 * Indicates if the model should be timestamped.
	 *
	 * @var bool
	 */
	public $timestamps = false;
	
	public function __construct($attributes = []) {
	    $this->rules = !empty($this->rules) ? $this->rules + $this->getRulesDefaults() : $this->getRulesDefaults();
        return parent::__construct($attributes);
	}

	/**
	 * Obtenemos atributos a incluir en append/appends()
	 * @return array
	 */
	public function getAppendableFields()
	{
		return array_where(array_diff(array_keys($this->getFields()), array_keys($this->getColumnsDefaultsValues())), function ($value, $key) {
			return !str_contains($value, '.');
		});
	}

	/**
	 * Obtenemos atributos para smart-datatable
	 * @return array
	 */
	public function getFields()
	{
		if (!$this->fields) {
			throw new \Exception('Undefined $fields in ' . class_basename($this) . ' model.');
		}
		return $this->fields;
	}

	/**
	 * Obtenemos Eager-Loaders
	 * @return array
	 */
	public function getEagerLoaders()
	{
		return $this->eagerLoaders;
	}

	/**
	 * Accessor - Obtenemos columna 'activo' formateado en texto
	 * @return string
	 */
	public function getActivoTextAttribute()
	{
		return $this->activo ? 'Activo' : 'Inactivo';
	}

	/**
	 * Accessor - Obtenemos columna 'activo' formateado en HTML
	 * @return string
	 */
	public function getActivoSpanAttribute()
	{
		# Retornamos HTML-Element
		$format = new HtmlString('<span class=' . ($this->activo ? 'toast_success' : 'toast_error') . ">&nbsp;$this->activo_text&nbsp;</span>");
		# Si Ajax, retornamos HTML-String
		if (request()->ajax()) {
			return $format->toHtml();
		}
		return $format;
	}

	/**
	 * Obtenemos columnas-defaults de modelo
	 * @return array
	 */
	public function getColumnsDefaultsValues()
	{
		$columns = $this->getConnection()->getDoctrineSchemaManager()->listTableDetails($this->getTable())->getColumns();
		return array_map(function($column) {
			return $column->getDefault();
		}, $columns );
	}
	
	/**
	 * Obtenemos informacion-base de modelo para las reglas
	 * @return array
	 */
	public function getRulesDefaults()
	{
	    $types = ['Integer'=>'Integer','Decimal'=>'Digits','Date'=>'Date','Time'=>'Sometimes'];
	    $columns = $this->getConnection()->getDoctrineSchemaManager()->listTableDetails($this->getTable())->getColumns();
	    
	    $propertys = array_map(function($column) {
	        return [
	            'required' => $column->getNotnull(),
	            'type' => $column->getType(),
	            'length' => !empty($column->getLength()) ? $column->getLength() : $column->getPrecision(),
	            'decimal' => $column->getScale(),
	            'comment'  => $column->getComment(),
	        ];
	    }, $columns );
	        
        $rules = [];
        foreach($propertys as $col=>$prop) {
            if(in_array($col, $this->fillable))
            {
                $rules[$col] = [];
                $type = (string)$prop['type'];
                
                if($type == 'Boolean')
                {
                    array_push($rules[$col],'min:0');
                    array_push($rules[$col],'max:1');
                }
                elseif(in_array($type,['Integer','Decimal'])) {
                    array_push($rules[$col],'digits_between:1,'.$prop['length']);
                }
                else {
                    array_push($rules[$col],'max:'.$prop['length']);
                }
                
                if(isset($types[$type])) {
                    if($types[$type] == 'Digits') {
                        array_push($rules[$col],$types[$type].':'.$prop['decimal']);
                    }
                    else {
                        array_push($rules[$col],$types[$type]);
                    }
                }
                
                if($prop['required'] == true && $type != 'Boolean') {
                    array_push($rules[$col],'required');
                }
                
                if(!empty($prop['comment'])) {
                    array_push($rules[$col],$prop['comment']);
                }
            }
        }
        return $rules;
	}
	/*
	public function array_diff_assoc_recursive($array1, $array2) {
	    $difference=array();
	    foreach($array1 as $key => $value) {
	        if( is_array($value) ) {
	            if( !isset($array2[$key]) || !is_array($array2[$key]) ) {
	                $difference[$key] = $value;
	            } else {
	                $new_diff = array_diff_assoc_recursive($value, $array2[$key]);
	                if( !empty($new_diff) )
	                    $difference[$key] = $new_diff;
	            }
	        } else if( !array_key_exists($key,$array2) || $array2[$key] !== $value ) {
	            $difference[$key] = $value;
	        }
	    }
	    return $difference;
	}
	*/
}