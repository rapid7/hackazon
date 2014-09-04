<?php

//require_once ('php-src/gwtphp/helpers/RPCTargetResolverStrategy.class.php');

class SimpleRPCTargetResolverStrategy implements RPCTargetResolverStrategy {
	
	function resolveRPCTarget(MappedClass $interface) {
		$target = GWTPHPContext::getInstance()->getClassLoader()->loadClass($interface->getMappedName().'Impl');
		return $target->newInstance();
	}
	
}

?>