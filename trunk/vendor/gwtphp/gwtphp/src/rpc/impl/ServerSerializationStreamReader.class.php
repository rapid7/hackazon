<?php
/*
 * GWTPHP is a port to PHP of the GWT RPC package.
 * 
 * <p>This framework is based on GWT (see {@link http://code.google.com/webtoolkit/ gwt-webtoolkit} for details).</p>
 * <p>Design, strategies and part of the methods documentation are developed by Google Team  </p>
 * 
 * <p>PHP port, extensions and modifications by Rafal M.Malinowski. All rights reserved.<br>
 * Additional modifications, GWT generators and linkers by Yifei Teng. All rights reserved.<br>
 * For more information, please see {@link https://github.com/tengyifei/gwtphp}</p>
 * 
 * 
 * <p>Licensed under the Apache License, Version 2.0 (the "License"); you may not
 * use this file except in compliance with the License. You may obtain a copy of
 * the License at</p>
 * 
 * {@link http://www.apache.org/licenses/LICENSE-2.0 http://www.apache.org/licenses/LICENSE-2.0}
 * 
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS, WITHOUT
 * WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied. See the
 * License for the specific language governing permissions and limitations under
 * the License.
 */

/**
 * @package gwtphp.rpc.impl
 */

require_once (GWTPHP_DIR . '/rpc/impl/AbstractSerializationStreamReader.class.php');
require_once (GWTPHP_DIR . '/util/TypeConversionUtil.class.php');
require_once (GWTPHP_DIR . '/rpc/impl/SerializabilityUtil.class.php');

define ( 'CHAR_SEPARATOR', "|" );

function unescape_unicode($match) {
	return mb_convert_encoding(pack('H*', $match[1]), 'UTF-8', 'UCS-2BE');
}

function unescape_gwt($match){
	if ($match[0][1]=='!')
		return CHAR_SEPARATOR;
	if ($match[0][1]=='0')
		return "\0";
	return $match[0];
}

/**
 * Used to accumulate elements while deserializing array types. The generic
 * type of the BoundedList will vary from the component type of the array it
 * is intended to create when the array is of a primitive type.
 * 
 * @param <T> The type of object used to hold the data in the buffer
 */
final class BoundedList {
	/**
	 *
	 * @var MappedClass
	 */
	private $componentType;
	/**
	 * 
	 *
	 * @var int
	 */
	private $expectedSize;
	/**
	 *
	 * @var array
	 */
	private $list = array ();
	
	public function __construct(MappedClass $componentType, $expectedSize) {
		$this->componentType = $componentType;
		$this->expectedSize = $expectedSize;
	}
	
	public function add($o) {
		//TODO: check if $o type of componentType
		assert ( count ( $this->list ) < $this->getExpectedSize () );
		
		//assert size() < getExpectedSize();
		//return super.add(o);
		$this->list [] = $o;
	}
	/**
	 *
	 * @return MappedClass
	 */
	public function getComponentType() {
		return $this->componentType;
	}
	/**
	 *
	 * @return int
	 */
	public function getExpectedSize() {
		return $this->expectedSize;
	}
	/**
	 *
	 * @return int
	 */
	public function size() {
		return count ( $this->list );
	}
	
	public function removeFirst() {
		return array_shift ( $this->list );
	}
}

final class ServerSerializationStreamReader extends AbstractSerializationStreamReader {
	
	/**
	 *
	 * @var array of strings
	 */
	private $tokenList;
	/**
	 *
	 * @var int
	 */
	private $tokenListIndex;
	
	/**
	 *
	 * @var array of strings
	 */
	private $stringTable;
	
	/**
	 * @var Logger
	 */
	private $logger;
	
	/**
	 * 
	 * @var MappedClassLoader
	 */
	private $mappedClassLoader;
	
	/**
	 * 
	 * @var SerializationPolicyProvider
	 */
	private $serializationPolicyProvider;
	
	/**
	 * 
	 * @var SerializationPolicy
	 */
	private $serializationPolicy;
	
	private static /*MapClass, ValueReader*/ $CLASS_TO_VECTOR_READER = null; //new IdentityHashMap Class, ValueReader>();
	

	private static function initStatics() {
		if (self::$CLASS_TO_VECTOR_READER === null) {
			self::$CLASS_TO_VECTOR_READER = array (
					'[' . TypeSignatures::$BOOLEAN => VectorReader::BOOLEAN_VECTOR(), 
					'[' . TypeSignatures::$BYTE => VectorReader::BYTE_VECTOR(), 
					'[' . TypeSignatures::$CHAR => VectorReader::CHAR_VECTOR(), 
					'[' . TypeSignatures::$DOUBLE => VectorReader::DOUBLE_VECTOR() ,
					'[' . TypeSignatures::$FLOAT => VectorReader::FLOAT_VECTOR(), 
					'[' . TypeSignatures::$INT => VectorReader::INT_VECTOR(), 
					'[' . TypeSignatures::$LONG => VectorReader::LONG_VECTOR(), 
					'[Ljava.lang.Object;' => VectorReader::OBJECT_VECTOR(), 
					'[' . TypeSignatures::$SHORT => VectorReader::SHORT_VECTOR(), 
					'[Ljava.lang.String;' => VectorReader::STRING_VECTOR() );
		}
	}
	
	public function __construct(MappedClassLoader $mappedClassLoader, SerializationPolicyProvider $serializationPolicyProvider) {
		self::initStatics();
		$this->mappedClassLoader = $mappedClassLoader;
		$this->serializationPolicyProvider = $serializationPolicyProvider;
		$this->serializationPolicy = RPC::getDefaultSerializationPolicy ();
		
		$this->logger = Logger::getLogger ( 'gwtphp.rpc.impl.ServerSerializationStreamReader' );
	}
	
	public function prepareToRead($encodedTocens = null) {
		$this->tokenList = array ();
		$this->tokenListIndex = 0;
		$this->stringTable = null;
		
		$idx = 0;
		$nextIdx = 0;
		
		while ( false != ($nextIdx = strpos ( $encodedTocens, CHAR_SEPARATOR, $idx )) ) {
			$current = substr ( $encodedTocens, $idx, $nextIdx - $idx );
			$this->tokenList [] = ($current);
			$idx = $nextIdx + 1;
		}
		
		$this->logger->debug ( $this->tokenList );
		parent::prepareToRead ();
		
		$this->logger->debug ( "Version " . $this->getVersion () );
		$this->logger->debug ( "Flags " . $this->getFlags () );
		
		$this->deserializeStringTable ();
		
		$this->logger->debug ( $this->stringTable );
		
		// If this stream encodes resource file information, read it and get a
		// SerializationPolicy
		if ($this->hasSerializationPolicyInfo ()) {
			$moduleBaseURL = $this->readString ();
			$strongName = $this->readString ();
			$this->logger->debug ( 'ModuleBaseURL ' . $moduleBaseURL );
			$this->logger->debug ( 'StrongName ' . $strongName );
			
		// not implemented yet, assumed StandardSerializationPolicy		
		//			if ($this->serializationPolicyProvidero !== null) {
		//				$this->serializationPolicy = $this->serializationPolicyProvider->getSerializationPolicy($moduleBaseURL, $strongName);
		//
		//				if ($this->serializationPolicy === null) {
		//					throw new NullPointerException(
		//					"serializationPolicyProvider.getSerializationPolicy()");
		//				}
		//			}
		}
	
	}
	
	/**
	 * @return void
	 */
	private function deserializeStringTable() { // TODO -> gwt1.5 BoundedList here
		$typeNameCount = $this->readInt (); // array length - ignored in php
		$this->stringTable = array ();
		
		for($typeNameIndex = 0; $typeNameIndex < $typeNameCount; ++ $typeNameIndex) {
			$rawString = $this->extract();
			$rawString = preg_replace_callback("/\\\\u([0-9a-f]{4})/i", 'unescape_unicode', $rawString);
			$rawString = preg_replace_callback('#\\\\.#', 'unescape_gwt', $rawString);
			$this->stringTable [$typeNameIndex] = $rawString;
		}
	}
	
	/**
	 * @todo zamienic na OOP (enums)
	 * @throws SerializationException
	 * @param MappedClass $type  the type to deserialize
	 * @return Object
	 */
	public function deserializeValue(MappedClass $type) {
		$this->logger->debug ( print_r ( $type, true ) );
		//TODO gwt-1.5 convert switch to more OO
		switch ($type->getSignature ()) {
			case TypeSignatures::$BOOLEAN :
				return $this->readBoolean ();
			case TypeSignatures::$BYTE :
				return $this->readByte ();
			case TypeSignatures::$CHAR :
				return $this->readChar ();
			case TypeSignatures::$DOUBLE :
				return $this->readDouble ();
			case TypeSignatures::$FLOAT :
				return $this->readFloat ();
			case TypeSignatures::$INT :
				return $this->readInt ();
			case TypeSignatures::$LONG :
				return $this->readLong ();
			case TypeSignatures::$SHORT :
				return $this->readShort ();
			case 'java.lang.String' :
				return $this->readString ();
			default :
				return $this->readObject ();
		}
	
	}
	
	/**
	 * 
	 *
	 * @return string
	 */
	private function extract() {
		return $this->tokenList [$this->tokenListIndex ++];
	}
	
	/**
	 * @throws SerializationException
	 * @return boolean
	 */
	public function readBoolean() {
		return ( boolean ) !$this->extract () == "0";
	}
	
	/**
	 * @throws SerializationException
	 * @return byte
	 */
	public function readByte() {
		return TypeConversionUtil::parseByte($this->extract ());
	}
	
	/**
	 * @throws SerializationException
	 * @return char
	 */
	public function readChar() {
		 // just use an int, it's more foolproof
		return TypeConversionUtil::parseInt($this->extract ());
	}
	
	/**
	 * @throws SerializationException
	 * @return double
	 */
	function readDouble() {
		return TypeConversionUtil::parseDouble ( $this->extract () );
		//		$_ = $this->extract ();
	//		switch ($_) {
	//			case 'NaN' :
	//				return NAN;
	//			case 'Infinity' :
	//				return INF;
	//			case '-Infinity' :
	//				return - INF;
	//			default :
	//				return doubleval ( $_ );
	//		}
	}
	
	/**
	 * @throws SerializationException
	 * @return float
	 */
	public function readFloat() {
		return TypeConversionUtil::parseFloat ( $this->extract () );
		//		$_ = $this->extract ();
	//		switch ($_) {
	//			case 'NaN' :
	//				return NAN;
	//			case 'Infinity' :
	//				return INF;
	//			case '-Infinity' :
	//				return - INF;
	//			default :
	//				return floatval ( $_ );
	//		}
	//return floatval(($_ == 'NaN' ) ? NAN : $_);
	}
	
	/**
	 * @throws SerializationException
	 * @return int
	 */
	public function readInt() {
		return TypeConversionUtil::parseInt ( $this->extract () );
	}
	
	/**
	 * @throws SerializationException
	 * @return float
	 */
	function readLong() {
		//return TypeConversionUtil::hex2dec ( $this->extract () );
		return TypeConversionUtil::base64toDec ( $this->extract () );
	}
	
	/**
	 * @throws SerializationException
	 * @return short
	 */
	function readShort() {
		return TypeConversionUtil::parseShort($this->extract ());
	}
	
	/**
	 * @throws SerializationException
	 * @return string
	 */
	public function readString() {
		return $this->getString ( $this->readInt () );
	}
	
	/**
	 *Deserialize an object with the given type signature.
	 * 
	 * @throws SerializationException
	 * @param string $typeSignature  the type signature to deserialize
	 * @return Object the deserialized object
	 */
	protected function deserialize($typeSignature) {
		$this->logger->debug ( "deserialize :" . $typeSignature );
		
		$serializedInstRef = SerializabilityUtil::decodeSerializedInstanceReference ( $typeSignature );
		
		$this->logger->debug ( "serializedInstRef : " . $serializedInstRef->getName () . " " . $serializedInstRef->getSignature () );
		/*MappedClass*/	$instanceClass = $this->mappedClassLoader->loadMappedClass ( $serializedInstRef->getName () );
		$instanceClass->setCRC ( $serializedInstRef->getSignature () );
		assert ($this->serializationPolicy !== null);
		$this->serializationPolicy->validateDeserialize ( $instanceClass ); // {90%}
		

		$this->validateTypeVersions ( $instanceClass, $serializedInstRef ); // {cut}
		// Class customSerializer = SerializabilityUtil.hasCustomFieldSerializer(instanceClass);
		// instance = instantiate(customSerializer, instanceClass);
		// rememberDecodedObject(instance);
		$customSerializer = SerializabilityUtil::hasCustomFieldSerializer ( $instanceClass ); // {100%}
		

		$index = $this->reserveDecodedObjectIndex ();
		
		$instance = $this->instantiate ( $customSerializer, $instanceClass ); // {100%}
		$this->rememberDecodedObject ( $index, $instance );
		
		$replacement = $this->deserializeImpl ( $customSerializer, $instanceClass, $instance );
		
		// It's possible that deserializing an object requires the original proxy
		// object to be replaced.
		if ($instance !== $replacement) {
			$this->rememberDecodedObject ( $index, $instance );
			$instance = $replacement;
		}
		
		return $instance;
		
	//$instance = $customSerializer->instantiate($this);
	//$instance = $this->deserializeImpl($customSerializer, $serializedInstRef->getName());
	//$instance = $this->deserializeImpl($customSerializer, $serializedInstRef->getName(), $instance);
	

	//return $instance;
	

	}
	/**
	 *
	 * @param ReflectionClass $customSerializer
	 * @param MappedClass $instanceClass
	 * @return Object
	 * @throws InstantiationException
	 * @throws IllegalAccessException
	 * @throws IllegalArgumentException
	 * @throws InvocationTargetException
	 */
	private function instantiate(ReflectionClass $customSerializer = null, MappedClass $instanceClass) {
		if ($customSerializer != null) {
			try {
				/*ReflectionMethod*/
				//TODO gwt-1.5 costumeserializer nie musi miec metody instantiate, wiec trzeba wziac zbadac zadeklarowane metody i jesli nie ma instantiate to idziemy dalej (teraz bedzie error gdy brak)
				$instantiate = $customSerializer->getMethod ( 'instantiate' ); //, SerializationStreamReader.class);
				return $instantiate->invoke ( null, $this );
			} catch ( ReflectionException $ex ) {
				// purposely ignored
			}
		}
		
		if ($instanceClass->isArray ()) {
			$length = $this->readInt ();
			/*MappedClass*/
			//TODO: gwt-1.5 
			$componentType = $instanceClass->getComponentType ();
			// We don't pre-allocate the array; this prevents an allocation attack
			return new BoundedList ( $componentType, $length );
			//removed require_once (GWTPHP_DIR . '/util/ArrayUtil.class.php');
		//removed return ArrayUtil::initArrayWithSize ( $length ); //Array.newInstance(componentType, length);
		} else {
			return $instanceClass->newInstance ();
		}
	}
	
	private function validateTypeVersions(MappedClass $mappedClass, SerializedInstanceReference $serializedInstRef) {
		// TODO: implement this method (when add some tool to create automaticly maping between php and java classes,
	// we will be able to process CRC for java classes)
	}
	
	private function deserializeImpl(ReflectionClass $customSerializer = null, MappedClass $instanceClass, &$instance) //,      Object $instance)
//throws NoSuchMethodException, IllegalArgumentException,
	// IllegalAccessException, InvocationTargetException,
	// SerializationException, ClassNotFoundException
	{
		if ($customSerializer != null) {
			$this->deserializeWithCustomFieldDeserializer ( $customSerializer, $instanceClass, $instance );
		} else if ($instanceClass->isArray ()) {
			$instance = $this->deserializeArray ( $instanceClass, $instance );
		} else {
			//TODO gwt-1.5 to dodac enum i zamienic deserializeWithDefaultFieldDeserializer na deserializeClass
			$this->deserializeClass ( $instanceClass, $instance );
		}
		
		return $instance;
	}
	
	private function deserializeWithCustomFieldDeserializer(ReflectionClass $customSerializer, MappedClass $instanceClass, &$instance) // throws ClassNotFoundException, NoSuchMethodException, IllegalAccessException, InvocationTargetException
{		// TODO: gwt 1-5 tutaj nigdy array nie trafai assert not array,
		//	return $customSerializer->deserialize()
		//	throw new Exception('Unsuporter operation exception');
		if ($instanceClass->isArray ()) {
			/*MappedClass*/
			$componentType = $instanceClass->getComponentType ();
			if (! $componentType->isPrimitive ()) {
				$instanceClass = array (); //Class.forName("[Ljava.lang.Object;");
			}
		}
		/*MappedMethod*/
		$deserialize = $customSerializer->getMethod ( "deserialize" );
		//SerializationStreamReader.class, instanceClass);
		

		/* this wont works, must send $instance as reference in array like in next line
		   $deserialize->invoke(null, $this, &$instance);
		*/
		$deserialize->invokeArgs ( null, array ($this, &$instance ) );
	}
	/**
	 * 
	 * @param MappedClass $instanceClass
	 * @param Object $instance
	 */
	private function deserializeClass(MappedClass $instanceClass, &$instance) {
		/*MappedField[]*/$serializableFields = SerializabilityUtil::applyFieldSerializationPolicy ( $instanceClass );
		
		foreach ( $serializableFields as $declField ) {
			assert ($declField != null);
			$value = $this->deserializeValue ( $declField->getType () );
			
			$propName = $declField->getName ();
			$rClass = $instanceClass->getReflectionClass ();
			if ($rClass == null) {
				require_once (GWTPHP_DIR . '/maps/java/lang/ClassNotFoundException.class.php');
				throw new ClassNotFoundException ( 'MappedClass: ' . $instanceClass->getSignature () . ' do not contains ReflectionClass infomration' );
			}
			
			if (! $rClass->hasProperty ( $propName )) {
				require_once (GWTPHP_DIR . '/maps/java/lang/SerializationException.class.php');
				throw new SerializationException ( 'MappedClass: ' . $instanceClass->getSignature () . ' do not contains property: ' . $propName . ' Did you mapped all properties?' );
			}
			
			$rProperty = $rClass->getProperty ( $propName );
			if ($rProperty->isPublic ()) {
				$rProperty->setValue ( $instance, $value );
			} else { // not public access to property, we try invoke setter method
				$propNameSetter = 'set' . strtoupper ( $propName [0] ) . substr ( $propName, 1, strlen ( $propName ) );
				if (! $rClass->hasMethod ( $propNameSetter )) {
					require_once (GWTPHP_DIR . '/maps/java/lang/SerializationException.class.php');
					throw new SerializationException ( 'MappedClass: ' . $instanceClass->getSignature () . ' do not contains setter method for private property: ' . $propName . '. Mapped object should be in pojo style?' );
				}
				$rMethod = $rClass->getMethod ( $propNameSetter );
				if ($rMethod->isPublic ()) {
					$rMethod->invoke ( $instance, $value );
				} else {
					require_once (GWTPHP_DIR . '/maps/java/lang/SerializationException.class.php');
					throw new SerializationException ( 'MappedClass: ' . $instanceClass->getSignature () . ' do not contains public setter method for private property: ' . $propName . '. Mapped object should be in pojo style?' );
				
				}
			}
			
		/*
				Object value = deserializeValue(declField.getType());

				boolean isAccessible = declField.isAccessible();
				boolean needsAccessOverride = !isAccessible
				  && !Modifier.isPublic(declField.getModifiers());
				if (needsAccessOverride) {
				// Override access restrictions
				declField.setAccessible(true);
				}
				
				declField.set(instance, value);
				
				if (needsAccessOverride) {
				// Restore access restrictions
				declField.setAccessible(isAccessible);
				}
			*/
		}
		
		$superClass = $instanceClass->getSuperclass ();
		if ($superClass != null && $this->getSerializationPolicy ()->shouldDeserializeFields ( $superClass )) {
			$this->deserializeImpl ( SerializabilityUtil::hasCustomFieldSerializer ( $superClass ), $superClass, $instance );
		}
		/*
		Class<?> superClass = instanceClass.getSuperclass();
	    if (serializationPolicy.shouldDeserializeFields(superClass)) {
	      deserializeImpl(SerializabilityUtil.hasCustomFieldSerializer(superClass),
	          superClass, instance);
	    }
		*/
	}
	
	/**
	 * 
	 * @deprecated 
	 * @param MappedClass $instanceClass
	 * @param Object $instance
	 */
	// TODO: exceptions catch and rethrow
	private function deserializeWithDefaultFieldDeserializer(MappedClass $instanceClass, &$instance) {
		/*MappedField[]*/$declFields = $instanceClass->getDeclaredFields ();
		/*MappedField[]*/$serializableFields = SerializabilityUtil::applyFieldSerializationPolicy ( $declFields );
		
		foreach ( $serializableFields as $declField ) {
			$value = $this->deserializeValue ( $declField->getType () );
			
			$propName = $declField->getName ();
			$rClass = $instanceClass->getReflectionClass ();
			if ($rClass == null) {
				require_once (GWTPHP_DIR . '/maps/java/lang/ClassNotFoundException.class.php');
				throw new ClassNotFoundException ( 'MappedClass: ' . $instanceClass->getSignature () . ' do not contains ReflectionClass infomration' );
			}
			
			if (! $rClass->hasProperty ( $propName )) {
				require_once (GWTPHP_DIR . '/maps/java/lang/SerializationException.class.php');
				throw new SerializationException ( 'MappedClass: ' . $instanceClass->getSignature () . ' do not contains property: ' . $propName . ' Did you mapped all properties?' );
			}
			
			$rProperty = $rClass->getProperty ( $propName );
			if ($rProperty->isPublic ()) {
				$rProperty->setValue ( $instance, $value );
			} else { // not public access to property, we try invoke setter method
				$propNameSetter = 'set' . strtoupper ( $propName [0] ) . substr ( $propName, 1, strlen ( $propName ) );
				if (! $rClass->hasMethod ( $propNameSetter )) {
					require_once (GWTPHP_DIR . '/maps/java/lang/SerializationException.class.php');
					throw new SerializationException ( 'MappedClass: ' . $instanceClass->getSignature () . ' do not contains setter method for private property: ' . $propName . '. Mapped object should be in pojo style?' );
				}
				$rMethod = $rClass->getMethod ( $propNameSetter );
				if ($rMethod->isPublic ()) {
					$rMethod->invoke ( $instance, $value );
				} else {
					require_once (GWTPHP_DIR . '/maps/java/lang/SerializationException.class.php');
					throw new SerializationException ( 'MappedClass: ' . $instanceClass->getSignature () . ' do not contains public setter method for private property: ' . $propName . '. Mapped object should be in pojo style?' );
				
				}
			}
			
		/*
				Object value = deserializeValue(declField.getType());

				boolean isAccessible = declField.isAccessible();
				boolean needsAccessOverride = !isAccessible
				  && !Modifier.isPublic(declField.getModifiers());
				if (needsAccessOverride) {
				// Override access restrictions
				declField.setAccessible(true);
				}
				
				declField.set(instance, value);
				
				if (needsAccessOverride) {
				// Restore access restrictions
				declField.setAccessible(isAccessible);
				}
			*/
		}
		
		$superClass = $instanceClass->getSuperclass ();
		if ($superClass != null && $this->getSerializationPolicy ()->shouldDeserializeFields ( $superClass )) {
			$this->deserializeImpl ( SerializabilityUtil::hasCustomFieldSerializer ( $superClass ), $superClass, $instance );
		}
		/*
		Class<?> superClass = instanceClass.getSuperclass();
	    if (serializationPolicy.shouldDeserializeFields(superClass)) {
	      deserializeImpl(SerializabilityUtil.hasCustomFieldSerializer(superClass),
	          superClass, instance);
	    }
		*/
	}
	
	/**
	 * Gets a string out of the string table.
	 * 
	 * @param int $index the index of the string to get
	 * @return string
	 */
	protected function getString($index) {
		if ($index == 0) {
			return null;
		}
		$this->logger->info ( "getString($index) where sizeof(this->stringTable)=" . sizeof ( $this->stringTable ) );
		if ($index > sizeof ( $this->stringTable ))
			throw new Exception ( '$index > sizeof($this->stringTable' );
			// index is 1-based
		assert ( $index > 0 );
		assert ( $index <= sizeof ( $this->stringTable ) );
		return ( string ) $this->stringTable [$index - 1];
	}
	
	/**
	 * @param MappedClass $instanceClass
	 * @param Object $instance
	 */
	private function deserializeArray(MappedClass $instanceClass, BoundedList &$instance) {
		assert ( $instanceClass->isArray () );
		//assert($instance instanceof BoundedList);
		$s = $instanceClass->getSignature ();
		/*VectorReader */		$instanceReader = self::$CLASS_TO_VECTOR_READER [$s];
		if ($instanceReader !== null) {
			return $instanceReader->read ( $this, $instance );
		} else {
			return VectorReader::OBJECT_VECTOR()->read ( $this, $instance );
		}
	
	}
	
	/**
	 *
	 * @param MappedClassLoader $mappedClassLoader
	 * @return void
	 */
	public function setMappedClassLoader(MappedClassLoader $mappedClassLoader) {
		$this->mappedClassLoader = $mappedClassLoader;
	}
	/**
	 *
	 * @return MappedClassLoader
	 */
	public function getMappedClassLoader() {
		return $this->mappedClassLoader;
	}
	/**
	 *
	 * @param SerializationPolicyProvider $serializationPolicyProvider
	 * @return void
	 */
	public function setSerializationPolicyProvider($serializationPolicyProvider) {
		$this->serializationPolicyProvider = $serializationPolicyProvider;
	}
	/**
	 *
	 * @return SerializationPolicyProvider
	 */
	public function getSerializationPolicyProvider() {
		return $this->serializationPolicyProvider;
	}
	
	/**
	 *
	 * @param SerializationPolicy $serializationPolicy
	 * @return void
	 */
	public function setSerializationPolicy($serializationPolicy) {
		$this->serializationPolicy = $serializationPolicy;
	}
	/**
	 *
	 * @return SerializationPolicy
	 */
	public function getSerializationPolicy() {
		return $this->serializationPolicy;
	}

}

abstract class VectorReader {
	
	/**
	 * @return mixed
	 * @throws SerializationException
	 */
	protected abstract function readSingleValue(ServerSerializationStreamReader $stream);
	/** 
	 *
	 * @param array $array
	 * @param int $index
	 * @param mixed $value
	 * @return void
	 */
	protected abstract function setSingleValue(&$array, $index, $value);
	
	/**
	 * @return VectorReader
	 */
	public static function BOOLEAN_VECTOR() {
		return BooleanVectorReader::getInstance ();
	}
	/**
	 * @return VectorReader
	 */
	public static function BYTE_VECTOR() {
		return ByteVectorReader::getInstance ();
	}
	/**
	 * @return VectorReader
	 */
	public static function CHAR_VECTOR() {
		return CharVectorReader::getInstance ();
	}
	/**
	 * @return VectorReader
	 */
	public static function DOUBLE_VECTOR() {
		return DoubleVectorReader::getInstance ();
	}
	/**
	 * @return VectorReader
	 */
	public static function FLOAT_VECTOR() {
		return FloatVectorReader::getInstance ();
	}
	/**
	 * @return VectorReader
	 */
	public static function INT_VECTOR() {
		return IntVectorReader::getInstance ();
	}
	/**
	 * @return VectorReader
	 */
	public static function LONG_VECTOR() {
		return LongVectorReader::getInstance ();
	}
	/**
	 * @return VectorReader
	 */
	public static function OBJECT_VECTOR() {
		return ObjectVectorReader::getInstance ();
	}
	/**
	 * @return VectorReader
	 */
	public static function SHORT_VECTOR() {
		return ShortVectorReader::getInstance ();
	}
	/**
	 * @return VectorReader
	 */
	public static function STRING_VECTOR() {
		return StringVectorReader::getInstance ();
	}

	/**
	 * Convert a BoundedList to an array of the correct type. This
	 * implementation consumes the BoundedList.
	 * @return Object
	 * @throws SerializationException
	 */
	protected function toArray(MappedClass $componentType, BoundedList $buffer) {
		if ($buffer->getExpectedSize () != $buffer->size ()) {
			throw new SerializationException ( "Inconsistent number of elements received. Received " + $buffer->size () + " but expecting " + $buffer->getExpectedSize () );
		}
		
		$arr = array (); //Array.newInstance(componentType, buffer.size());
		

		for($i = 0, $n = $buffer->size (); $i < $n; ++ $i) {
			$this->setSingleValue ( $arr, $i, $buffer->removeFirst () );
		}
		
		return $arr;
	}
	/**
	 * 
	 *
	 * @param ServerSerializationStreamReader $stream
	 * @param BoundedList $instance
	 * @return Object
	 * @throws SerializationException
	 */
	function read(ServerSerializationStreamReader $stream, BoundedList $instance) {
		for($i = 0, $n = $instance->getExpectedSize (); $i < $n; ++ $i) {
			$instance->add ( $this->readSingleValue ( $stream ) );
		}
		
		return $this->toArray ( $instance->getComponentType (), $instance );
	}
}

class BooleanVectorReader extends VectorReader {
	private static $instance = null;
	private function __construct() {
	}
	private function __clone() {
	}
	protected static function getInstance() {
		return (self::$instance === null ? self::$instance = new self ( ) : self::$instance);
	}
	
	protected function readSingleValue(ServerSerializationStreamReader $stream) {
		return $stream->readBoolean ();
	}
	
	protected function setSingleValue(&$array, $index, $value) {
		$array [$index] =  TypeConversionUtil::parseBoolean($value);
	}
}

class ByteVectorReader extends VectorReader {
	private static $instance = null;
	private function __construct() {
	}
	private function __clone() {
	}
	protected static function getInstance() {
		return (self::$instance === null ? self::$instance = new self ( ) : self::$instance);
	}
	
	protected function readSingleValue(ServerSerializationStreamReader $stream) {
		return $stream->readByte(); // there are no 'byte' type in php
	}
	
	protected function setSingleValue(&$array, $index, $value) {
		$array [$index] =  TypeConversionUtil::parseByte($value); // there are no 'byte' type in php
	}
}

class CharVectorReader extends VectorReader {
	private static $instance = null;
	private function __construct() {
	}
	private function __clone() {
	}
	protected static function getInstance() {
		return (self::$instance === null ? self::$instance = new self ( ) : self::$instance);
	}
	
	protected function readSingleValue(ServerSerializationStreamReader $stream) {
		return $stream->readChar(); // there are no 'char' type in php
	}
	
	protected function setSingleValue(&$array, $index, $value) {
		$array [$index] =  TypeConversionUtil::parseChar($value); // there are no 'char' type in php
	}
}

class DoubleVectorReader extends VectorReader {
	private static $instance = null;
	private function __construct() {
	}
	private function __clone() {
	}
	protected static function getInstance() {
		return (self::$instance === null ? self::$instance = new self ( ) : self::$instance);
	}
	
	protected function readSingleValue(ServerSerializationStreamReader $stream) {
		return $stream->readDouble ();
	}
	
	protected function setSingleValue(&$array, $index, $value) {
		$array [$index] = TypeConversionUtil::parseDouble ( $value );
	}
}

class FloatVectorReader extends VectorReader {
	private static $instance = null;
	private function __construct() {
	}
	private function __clone() {
	}
	protected static function getInstance() {
		return (self::$instance === null ? self::$instance = new self ( ) : self::$instance);
	}
	
	protected function readSingleValue(ServerSerializationStreamReader $stream) {
		return $stream->readFloat ();
	}
	
	protected function setSingleValue(&$array, $index, $value) {
		$array [$index] = TypeConversionUtil::parseFloat ( $value );
	}
}

class IntVectorReader extends VectorReader {
	private static $instance = null;
	private function __construct() {
	}
	private function __clone() {
	}
	protected static function getInstance() {
		return (self::$instance === null ? self::$instance = new self ( ) : self::$instance);
	}
	
	protected function readSingleValue(ServerSerializationStreamReader $stream) {
		return $stream->readInt ();
	}
	
	protected function setSingleValue(&$array, $index, $value) {
		$array [$index] = TypeConversionUtil::parseInt($value);
	}
}

class LongVectorReader extends VectorReader {
	private static $instance = null;
	private function __construct() {
	}
	private function __clone() {
	}
	protected static function getInstance() {
		return (self::$instance === null ? self::$instance = new self ( ) : self::$instance);
	}
	
	protected function readSingleValue(ServerSerializationStreamReader $stream) {
		return $stream->readLong ();
	}
	
	protected function setSingleValue(&$array, $index, $value) {
		$array [$index] = TypeConversionUtil::parseLong($value); // there are no 'long' type in php
	}
}

class ObjectVectorReader extends VectorReader {
	private static $instance = null;
	private function __construct() {
	}
	private function __clone() {
	}
	protected static function getInstance() {
		return (self::$instance === null ? self::$instance = new self ( ) : self::$instance);
	}
	
	protected function readSingleValue(ServerSerializationStreamReader $stream) {
		return $stream->readObject ();
	}
	
	protected function setSingleValue(&$array, $index, $value) {
		$array [$index] = $value;
	}
}

class ShortVectorReader extends VectorReader {
	private static $instance = null;
	private function __construct() {
	}
	private function __clone() {
	}
	protected static function getInstance() {
		return (self::$instance === null ? self::$instance = new self ( ) : self::$instance);
	}
	
	protected function readSingleValue(ServerSerializationStreamReader $stream) {
		return $stream->readShort(); //there are no 'short' type in php
	}
	
	protected function setSingleValue(&$array, $index, $value) {
		$array [$index] = TypeConversionUtil::parseShort($value);
	}
}

class StringVectorReader extends VectorReader {
	private static $instance = null;
	private function __construct() {
	}
	private function __clone() {
	}
	protected static function getInstance() {
		return (self::$instance === null ? self::$instance = new self ( ) : self::$instance);
	}
	
	protected function readSingleValue(ServerSerializationStreamReader $stream) {
		return $stream->readString ();
	}
	
	protected function setSingleValue(&$array, $index, $value) {
		$array [$index] = $value;
	}
}

?>
