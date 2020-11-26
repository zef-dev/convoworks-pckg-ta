<?php declare(strict_types=1);

namespace Convo\Pckg\Ta;

use Convo\Core\Params\IServiceParamsScope;

/**
 * @author Tole
 * Will register user requests.
 */
class TaBotUserCounterElement extends \Convo\Core\Workflow\AbstractWorkflowComponent implements \Convo\Core\Workflow\IConversationElement
{
    /**
     * @var string
     */
    private $_statusVar;

    /**
     * @var int
     */
    private $_interval;
    
    /**
     * @var TaDao
     */
    private $_taDao;

    
    public function __construct( $properties, TaDao $taDao)
    {
    	parent::__construct( $properties);
    	
        $this->_statusVar   =   $properties['status_var'];
        $this->_interval    =   $properties['interval_in_minutes'] * 60;
        $this->_taDao       =   $taDao;
    }
    
    /**
     * {@inheritDoc}
     * @see \Convo\Core\Workflow\IConversationElement::read()
     */
    public function read( \Convo\Core\Workflow\IConvoRequest $request, \Convo\Core\Workflow\IConvoResponse $response)
    {
        $status =   $this->_taDao->register( $request, $this->evaluateString( $this->_interval));
        
        $param_name =   $this->evaluateString( $this->_statusVar);
        
        $this->getService()->getServiceParams( IServiceParamsScope::SCOPE_TYPE_SESSION)->setServiceParam( $param_name, $status);

        return;
    }

    public function __toString() {
        return parent::__toString().'['.$this->_statusVar.']['.$this->_interval.']['.$this->_taDao.']';
    }
}