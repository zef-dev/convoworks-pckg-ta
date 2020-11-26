<?php declare(strict_types=1);

namespace Convo\Pckg\Ta;

use Convo\Core\Factory\AbstractPackageDefinition;

class TaPackageDefinition extends AbstractPackageDefinition
{
    const NAMESPACE	=	'convo-ta';

    /**
     * @var \Convo\Core\Factory\PackageProviderFactory
     */
    private $_packageProviderFactory;

    /**
     * @var TaDao
     */
    private $_taDao;

    public function __construct( \Psr\Log\LoggerInterface $logger, TaDao $taDao)
    {
        $this->_taDao    =   $taDao;
        parent::__construct( $logger, self::NAMESPACE, __DIR__);
    }


    /**
     * {@inheritDoc}
     * @see \Convo\Core\Factory\AbstractPackageDefinition::_initDefintions()
     */
    protected function _initDefintions()
    {
        return array(
            new \Convo\Core\Factory\ComponentDefinition(
                $this->getNamespace(),
                '\Convo\Pckg\Ta\TaBotUserCounterElement',
                'User counter element',
                'Element which counts unique users in a period. It also dumps current status info into workflow as parameter',
                array(
                    'status_var' => array(
                        'editor_type' => 'text',
                        'editor_properties' => array(),
                        'defaultValue' => 'status',
                        'name' => 'Status variable name',
                        'description' => 'Name under which to provide full status',
                        'valueType' => 'string'
                    ),
                    'interval_in_minutes' => array(
                        'editor_type' => 'text',
                        'editor_properties' => array(),
                        'defaultValue' => 15,
                        'name' => 'Track past minutes',
                        'description' => 'Inform how many users asked for status in given minutes ammount',
                        'valueType' => 'int'
                    ),
                    '_workflow' => 'read',
                    '_system' => true,
                    '_factory' => new class ( $this->_taDao) implements \Convo\Core\Factory\IComponentFactory
                    {
                        private $_taDao;
                        public function __construct( TaDao $taDao)
                        {
                            $this->_taDao	=	$taDao;
                        }
                        public function createComponent( $properties, $service)
                        {
                            return new TaDao( $properties, $this->_taDao);
                        }
                    }
                )
            )
        );
    }
}
