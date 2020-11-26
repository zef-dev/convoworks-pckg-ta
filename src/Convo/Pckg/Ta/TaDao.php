<?php declare(strict_types=1);

namespace Convo\Pckg\Ta;

/**
 * @author Tole
 * Data storage layer for storing users.
 */
class TaDao
{
    /**
     * @var string
     */
    private $_basePath;
    
    public function __construct( $basePath)
    {
        $this->_basePath =   $basePath;
    }
    
    public function register( \Convo\Core\Workflow\IConvoRequest $request, $interval)
    {
        $data   =   $this->_getData( $request->getServiceId());
        
        $MIN    =   time() - $interval; 
        
        $data   =   array_values( array_filter( $data, function( $val) use ( $request, $MIN) {
            // remove old
            if ( $val['time'] < $MIN) {
                return false;
            }
            
            // remove self
            return $val['installation_id'] !== $request->getInstallationId();
        }));
        $count  =   count( $data);
        // register self
        $data[] =   [
            'installation_id' => $request->getInstallationId(),
            'time' => time()
        ];
        
        $this->_storeData( $request->getServiceId(), $data);
        
        return $count;
    }   
    
    private function _getData( $serviceId) {
        $filename   =   $this->_ensureStorage( $serviceId);
        $content    =   file_get_contents( $filename);
        $data       =   json_decode( $content, true);
        return $data;
    }
    private function _storeData( $serviceId, $data) {
        $filename   =   $this->_ensureStorage( $serviceId);
        file_put_contents( $filename, json_encode( $data, JSON_PRETTY_PRINT));
    }
    
    private function _ensureStorage( $serviceId)
    {
        $folder     =   $this->_basePath.'/tribes-data';
        $filename   =   $folder.'/'.$serviceId.'.json';
        
        if ( !is_dir( $folder)) {
            mkdir( $folder, 0755, true);
        }
        
        if ( !is_dir( $folder)) {
            throw new \Exception( 'Could not create folder ['.$folder.']');
        }
        
        if ( !is_file( $filename)) {
            file_put_contents( $filename, '[]');
        }
        
        if ( !is_file( $filename)) {
            throw new \Exception( 'Could not create service data file ['.$filename.']');
        }
        
        return $filename;
    }
  
    public function __toString() {
        return get_class( $this).'['.$this->_basePath.']';
    }
}