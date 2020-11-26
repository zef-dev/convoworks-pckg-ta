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
            ),
            new \Convo\Core\Factory\ComponentDefinition(
                $this->getNamespace(),
                '\Convo\Pckg\Trivia\TriviaScoresReader',
                'Trivia Scores',
                '',
                array(
                    'players' => array(
                        'editor_type' => 'text',
                        'editor_properties' => array(),
                        'defaultValue' => '',
                        'name' => 'players',
                        'description' => 'Players array. Collection of player names and their scores',
                        'valueType' => 'string'
                    ),
                    'status_var' => array(
                        'editor_type' => 'text',
                        'editor_properties' => array(),
                        'defaultValue' => '',
                        'name' => 'Status variable name',
                        'description' => 'Name under which to provide full iteration status (name, score)',
                        'valueType' => 'string'
                    ),
                    'name_field' => array(
                        'editor_type' => 'text',
                        'editor_properties' => array(),
                        'defaultValue' => '',
                        'name' => 'Name field',
                        'description' => 'Name of the player name field in the players array',
                        'valueType' => 'string'
                    ),
                    'score_field' => array(
                        'editor_type' => 'text',
                        'editor_properties' => array(),
                        'defaultValue' => '',
                        'name' => 'Score field',
                        'description' => 'Name of the player score field in the players array',
                        'valueType' => 'string'
                    ),
                    'single' => [
                        'editor_type' => 'service_components',
                        'editor_properties' => [
                            'allow_interfaces' => [\Convo\Core\Workflow\IConversationElement::class],
                            'multiple' => true
                        ],
                        'defaultValue' => [],
                        'defaultOpen' => false,
                        'name' => 'Single',
                        'description' => 'Flow to be executed when the player scores are unique',
                        'valueType' => 'class'
                    ],
                    'multiple' => [
                        'editor_type' => 'service_components',
                        'editor_properties' => [
                            'allow_interfaces' => [\Convo\Core\Workflow\IConversationElement::class],
                            'multiple' => true,
                        ],
                        'defaultValue' => [],
                        'defaultOpen' => false,
                        'name' => 'Multiple',
                        'description' => 'Flow to be executed when multiple players have the same score',
                        'valueType' => 'class'
                    ],
                    'all' => [
                        'editor_type' => 'service_components',
                        'editor_properties' => [
                            'allow_interfaces' => [\Convo\Core\Workflow\IConversationElement::class],
                            'multiple' => true,
                        ],
                        'defaultValue' => [],
                        'defaultOpen' => false,
                        'name' => 'All',
                        'description' => 'Flow to be executed when all players have the same score',
                        'valueType' => 'class'
                    ],
                    '_help' =>  array(
                        'type' => 'file',
                        'filename' => 'trivia-scores-reader.html'
                    ),
                    '_workflow' => 'read',
                )
            ),
        );
    }
}
