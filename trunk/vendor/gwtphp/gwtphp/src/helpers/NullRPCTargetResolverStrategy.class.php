<?php

require_once ('php-src/gwtphp/helpers/RPCTargetResolverStrategy.class.php');

class NullRPCTargetResolverStrategy implements RPCTargetResolverStrategy {
	
	/**
	 * 
	 * @see RPCTargetResolverStrategy::resolveRPCTarget()
	 */
	function resolveRPCTarget(MappedClass $interface) {
		if ($interface->getReflectionClass()->isInterface()) {
			 require_once(GWTPHP_DIR.'/maps/com/google/gwt/user/client/rpc/IncompatibleRemoteServiceException.class.php');
	                    throw new IncompatibleRemoteServiceException(
			            "Blocked attempt to access interface '"
                    	                  . $interface->getName()
                    	                  . "' where class expected; this is either misconfiguration or a hack attempt");
                    
		}
		return null;
	}
}

?>