<?php
namespace Paginator;

abstract class AbstractPaginatedQueryRequest 
{
	/**
	 * Page number to be displayed eg. 5
	 * @var int
	 * */
	protected $page;
	
	/**
	 * The number of items per page eg. 25
	 * @var int
	 */
	protected $itemCount;
	
	/**
	 * Is the search enabled
	 * @var boolean
	 */
	protected $searchEnabled;
	
	/**
	 * Parameters by which the data are filtered eg. array('groupOp' => 'AND', 'rules' => array('field'=>'plant', 'op'=>'eq', 'data'=>500))
	 * @var SearchGroup
	 */
	protected $searchParams;
	
	/**
	 * Order specifications eg. array(0 => 'plantNr ASC', 1 => 'workCenterCode DESC')
	 * @var array
	 */
	protected $orderSpecs;
	
	/** @var bool */
	protected $resultShouldBePaginated = true;
	
	/**
	 * Always order by this spec
	 * @var array
	 */
	protected $mandatoryOrderSpecs;
	
	
	/**
	 * 
	 * @param int $page
	 * @param int $itemCount
	 * @param string|bool $searchEnabled
	 * @param \stdClass|array $searchParams
	 * @param \stdClass|array $orderSpecs
	 * @param string|bool $resultShouldBePaginated
	 * @param \stdClass|array $mandatoryOrderSpecs
	 */
	public function __construct(
		int $page,
		int $itemCount,
		$searchEnabled,
	    $searchParams,
	    $orderSpecs,
		$resultShouldBePaginated,
        $mandatoryOrderSpecs = null
	){
		$this->page = $page;
		$this->itemCount = $itemCount;
		$this->setOrderSpecs($orderSpecs);
		$this->setSearchEnabled($searchEnabled);
		$this->setResultShouldBePaginated($resultShouldBePaginated);
		$this->setSearchParams($searchParams);		
		$this->setMandatoryOrderSpecs($mandatoryOrderSpecs);
	}

	/**
	 * 
	 * @param bool|string $resultShouldBePaginated
	 */
    public function setResultShouldBePaginated($resultShouldBePaginated)
    {
        if (is_string($resultShouldBePaginated) && strtolower($resultShouldBePaginated) === "true"){
            $resultShouldBePaginated = true;
        }elseif (is_string($resultShouldBePaginated) && strtolower($resultShouldBePaginated) === "false"){
            $resultShouldBePaginated = false;
        }else{
            $resultShouldBePaginated = boolval($resultShouldBePaginated);
        }
        $this->resultShouldBePaginated = $resultShouldBePaginated;
    }

    /**
     * 
     * @param bool|string $searchEnabled
     */
    public function setSearchEnabled($searchEnabled)
    {
        if (is_string($searchEnabled) && strtolower($searchEnabled) === "true"){
            $searchEnabled = true;
        }elseif (is_string($searchEnabled) && strtolower($searchEnabled) === "false"){
            $searchEnabled = false;
        }else{
            $searchEnabled = boolval($searchEnabled);
        }
        $this->searchEnabled = $searchEnabled;
    }

    /**
	 * 
	 * @param \stdClass|array $searchParams
	 */
	public function setSearchParams($searchParams)
	{
	    
	    $searchParams = is_array($searchParams) ? $searchParams : json_decode(json_encode($searchParams), true);
	    
	    if (isset($searchParams['rules']) && count($searchParams['rules']) > 0){
	        $rules = [];
	        foreach ($searchParams['rules'] as $rule){
	            $rules[] = new SearchRule($rule['field'], $rule['op'], $rule['data']);
	        }
	        $this->searchParams = new SearchGroup($searchParams['groupOp'], $rules);
	    } else if(isset($searchParams['groups'])){
	        $rules = [];
	        $this->searchParams = new SearchGroup($searchParams['groupOp'], $rules);
	    }
	    
	    if (isset($searchParams['groups']) && count($searchParams['groups']) > 0)
	    {
	        foreach($searchParams['groups'] as $group)
	        {
	            $subRules = [];
	            foreach($group['rules'] as $rule)
	            {
	                $subRules[] = new SearchRule($rule['field'], $rule['op'], $rule['data']);
	            }
	            $this->searchParams->addGroup(new SearchGroup($group['groupOp'], $subRules));
	        }
	    }
	}
	
	
	/**
	 * @return int
	 */
	public function getPage() {
		return $this->page;
	}
	
	/**
	 * @return int
	 */
	public function getItemCount() {
		return $this->itemCount;
	}
	
	/**
	 * @return boolean
	 */
	public function getSearchEnabled() {
		return $this->searchEnabled;
	}
	
	/**
	 * @return SearchGroup
	 */
	public function getSearchParams() {
		return $this->searchParams;
	}
	
	/**
	 * 
	 * @return array
	 */
	public function getOrderSpecs() {
		return $this->orderSpecs;
	}
	
	/**
	 * @return boolean
	 */
	public function getResultShouldBePaginated() {
		return $this->resultShouldBePaginated;
	}
	
	/**
	 * 
	 * @return array|null
	 */
	public function getMandatoryOrderSpecs()
	{
	    return $this->mandatoryOrderSpecs;
	}
	
	public function setMandatoryOrderSpecs($mandatoryOrderSpecs)
	{
	    
	    if (!empty($mandatoryOrderSpecs)) {
	        $mandatoryOrderSpecs = is_array($mandatoryOrderSpecs) ? $mandatoryOrderSpecs : json_decode(json_encode($mandatoryOrderSpecs), true);
	    }
	    else {
	        $mandatoryOrderSpecs = [];
	    }
	    
	    $this->mandatoryOrderSpecs = $mandatoryOrderSpecs;
	}
    

    public function setOrderSpecs($orderSpecs)
    {
        if (!empty($orderSpecs)) {
            $orderSpecs = is_array($orderSpecs) ? $orderSpecs : json_decode(json_encode($orderSpecs), true);
        }
        else {
            $orderSpecs = [];
        }
        
        $this->orderSpecs = $orderSpecs;
    }
	
}
