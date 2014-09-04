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

require_once(GWTPHP_DIR.'/util/HashMapUtil.class.php');
require_once(GWTPHP_DIR.'/util/IdentityHashMapUtil.class.php');
require_once(GWTPHP_DIR.'/util/CharacterUtil.class.php');

require_once(GWTPHP_DIR.'/rpc/impl/AbstractSerializationStreamWriter.class.php');

//define('CHAR_UFFFF' , "\xEF\xBF\xBF");
/**
   * This defines the character used by JavaScript to mark the start of an
   * escape sequence.
   * '\\'
   */
define('JS_ESCAPE_CHAR' , "\x5C");

/**
   * This defines the character used to enclose JavaScript strings.
   * '\"'
   */
define('JS_QUOTE_CHAR' , "\x22");
//TODO: const - change to
define('NON_BREAKING_HYPHEN' , "\xE2\x80\x91");
define('NUMBER_OF_JS_ESCAPED_CHARS' , 128);

//require_once(GWTPHP_DIR.'/lang/TypeSignatures.class.php');
/**
 * For internal use only. Used for server call serialization. This class is
 * carefully matched with the client-side version.
 */
class ServerSerializationStreamWriter extends AbstractSerializationStreamWriter {

  /**
   * Map of {@link Class} vector objects to {@link VectorWriter}s.
   */
	private static /*MapClass, VectorWriter*/ $CLASS_TO_VECTOR_WRITER = null; //new IdentityHashMap Class, ValueReader>();
	
  
	
	private static $JS_CHARS_ESCAPED = array (
											 "\x00" => '0',
											 "\x08" => 'b',
											 "\x09" => 't',
											 "\x0A" => 'n',
											 "\x0C" => 'f',
											 "\x0D" => 'r',
											 JS_ESCAPE_CHAR => JS_ESCAPE_CHAR,
											 JS_QUOTE_CHAR => JS_QUOTE_CHAR
											 );
											 
	  /**
   * Index into this array using a nibble, 4 bits, to get the corresponding
   * hexa-decimal character representation.
   */
  private static  $NIBBLE_TO_HEX_CHAR = array (
      '0', '1', '2', '3', '4', '5', '6', '7', '8', '9', 'A', 'B', 'C', 'D',
      'E', 'F');
	/**
	 * @var int
	 */
	private $objectCount;

	/**
	 * @var IdentityHashMapUtil<Object, Integer>
	 */
	private $objectMap ;
	/**
	 * @var HashMapUtil<String, Integer>
	 */
	private  $stringMap ;
	/**
	 * @var array<String>
	 */
	private  $stringTable = array();
	/**
	 * @var array<String> 
	 */
	private $tokenList = array();
	/**
	 * @var int
	 */
	private $tokenListCharCount;
	/**
	 * @var SerializationPolicy
	 */
	private $serializationPolicy;
	
	private static function initStatics() {
		if (self::$CLASS_TO_VECTOR_WRITER === null) {
			self::$CLASS_TO_VECTOR_WRITER = array (
					'[' . TypeSignatures::$BOOLEAN => VectorWriter::BOOLEAN_VECTOR(), 
					'[' . TypeSignatures::$BYTE => VectorWriter::BYTE_VECTOR(), 
					'[' . TypeSignatures::$CHAR => VectorWriter::CHAR_VECTOR(), 
					'[' . TypeSignatures::$DOUBLE => VectorWriter::DOUBLE_VECTOR() ,
					'[' . TypeSignatures::$FLOAT => VectorWriter::FLOAT_VECTOR(), 
					'[' . TypeSignatures::$INT => VectorWriter::INT_VECTOR(), 
					'[' . TypeSignatures::$LONG => VectorWriter::LONG_VECTOR(), 
					'[Ljava.lang.Object;' => VectorWriter::OBJECT_VECTOR(), 
					'[' . TypeSignatures::$SHORT => VectorWriter::SHORT_VECTOR(), 
					'[Ljava.lang.String;' => VectorWriter::STRING_VECTOR() );
		}
	}
	
	/**
	 * 
	 *
	 * @param SerializationPolicy $serializationPolicy
	 */
	public function __construct(SerializationPolicy $serializationPolicy) {
		self::initStatics();
		$this->objectMap = new IdentityHashMapUtil/*<Object, Integer>*/();
		$this->stringMap = new HashMapUtil/*<String, Integer>*/();
		$this->serializationPolicy = $serializationPolicy;
	}

	
	
	public function prepareToWrite() {
		$this->objectCount = 0;
		$this->objectMap->clear();

		$this->tokenListCharCount = 0;
		$this->stringMap->clear();

		$this->stringTable = array();
		$this->tokenList = array();

	}

	/**
	 * @param Object $value
	 * @param MappedClass $type
	 * @throws SerializationException
	 */
	public function serializeValue($value, MappedClass $type = null) {
		$logger = Logger::getLogger('gwtphp.rpc.RPC');
		if (!$type->isPrimitive()) $type = $this->rectifyType($value, $type);
		
		if (gettype($value)==="object")
			$logger->debug("ImplSerialize object: " . get_class($value));
		else
			$logger->debug("ImplSerialize " . gettype($value) . ", " . $type->getSignature());
		
		switch ($type->getSignature()) {
			case TypeSignatures::$BOOLEAN:
				$this->writeBoolean(((boolean) $value));
				break;
			case TypeSignatures::$BYTE:
				$this->writeByte($value);
				break;
			case TypeSignatures::$CHAR:
				$this->writeChar($value);
				break;
			case TypeSignatures::$DOUBLE:
				$this->writeDouble($value);
				break;
			case TypeSignatures::$FLOAT:
				$this->writeFloat($value);
				break;
			case TypeSignatures::$INT:
				$this->writeInt($value);
				break;
			case TypeSignatures::$LONG:
				$this->writeLong($value);
				break;
			case TypeSignatures::$SHORT:
				$this->writeShort($value);
				break;
			case "java.lang.String":
				$this->writeString($value);
				break;
			default:
				$this->writeObject($value,$type);

		}
		

	}
	
	public function writeLong($fieldValue) {
		//echo "fieldValue = $fieldValue and is type ".gettype($fieldValue).($fieldValue>0?' PLUS ':' MINUS');
		class_exists('TypeConversionUtil') || include(GWTPHP_DIR.'/util/TypeConversionUtil.class.php');
		//$this->writeString((string)TypeConversionUtil::dec2hex($fieldValue));
		$this->append(TypeConversionUtil::dectoBase64($fieldValue));
	}
	
	/**
  * @Override
  * @return void
  * @param string
  */
	protected function append($token) {
		$this->tokenList[] = $token;
		if ($token != null) {
			$this->tokenListCharCount += count($token);
		}
	}


	/**
   * Build an array of JavaScript string literals that can be decoded by the
   * client via the eval function.
   * 
   * NOTE: We build the array in reverse so the client can simply use the pop
   * function to remove the next item from the list.
   * @Override
   * @return string
   */  
	public function toString() {
		// Build a JavaScript string (with escaping, of course).
		// We take a guess at how big to make to buffer to avoid numerous resizes.
		//
		//int capacityGuess = 2 * tokenListCharCount + 2 * tokenList.size();
		//StringBuffer buffer = new StringBuffer(capacityGuess);
		$buffer = '[';
		$this->writePayload($buffer);
		$this->writeStringTable($buffer);
		$this->writeHeader($buffer);
		$buffer .= ']';
		return $buffer;
	}

	/**
   * Notice that the field are written in reverse order that the client can just
   * pop items out of the stream.
   */
	private function writeHeader(&$buffer) {
		$buffer.=',';
		$buffer.=$this->getFlags();
		$buffer.=',';
		$buffer.=$this->getVersion();
	}

	private function writePayload(&$buffer) {
		for ($i = count($this->tokenList) - 1; $i >= 0; --$i) {
			$token = $this->tokenList[$i];
			$buffer.= $token;
			if ($i > 0) {
				$buffer.=',';
			}
		}
	}

	private function writeStringTable(&$buffer) {
		if (count($this->tokenList) > 0) {
			$buffer.=',';
		}
		$buffer.='[';
		for ($i = 0, $c = count($this->stringTable); $i < $c; ++$i) {
			if ($i > 0) {
				$buffer.=',';
			}
			$buffer.= ServerSerializationStreamWriter::escapeString($this->stringTable[$i]);
		}
		$buffer.=']';
	}




	/**
   * This method takes a string and outputs a JavaScript string literal. The
   * data is surrounded with quotes, and any contained characters that need to
   * be escaped are mapped onto their escape sequence.
   * 
   * Assumptions: We are targeting a version of JavaScript that that is later
   * than 1.3 that supports unicode strings.
   */
	public static function escapeString($toEscape) {
		return json_encode($toEscape);
	}

  /**
   * Add a string to the string table and return its index.
   * 
   * @param string $string the string to add
   * @return int the index to the string
   */
	protected function addString($string){
		if ($string === null) {
			return 0;
		}
		//Integer
		$o = $this->stringMap->get($string);
		if ($o != null) {
			return $o;
		}
		$this->stringTable[]=$string;
		// index is 1-based
		$index = count($this->stringTable);//.size();
		$this->stringMap->put($string, $index);
		return $index;
	}
	/**
   * Get the index for an object that may have previously been saved via
   * {@link #saveIndexForObject(Object)}.
   * 
   * @param Object instance the object to save
   * @return int the index associated with this object, or -1 if this object hasn't
   *         been seen before
   */
	protected function  getIndexForObject($instance){
		/*Integer*/
		$o = $this->objectMap->get($instance);
		if ($o !== null) {
			return $o;
		}
		return -1;
	}

	/**
   * Compute and return the type signature for an object.
   * 
   * @param Object instance the instance to inspect
   * @return String the type signature of the instance
   */
	protected function getObjectTypeSignature($instance){
		if ($this->shouldEnforceTypeVersioning()) {
			//return SerializabilityUtil::encodeSerializedInstanceReference(instance.getClass());
		} else {
			//return SerializabilityUtil::getSerializedTypeName(instance.getClass());
		}
	}

	/**
   * Remember this object as having been seen before.
   * 
   * @param Object instance the object to remember
   */
	protected function saveIndexForObject($instance){
		$this->objectMap->put($instance,$this->objectCount++);
	}

	/**
   * Serialize an object into the stream.
   * 
   * @param Object instance the object to serialize
   * @param String typeSignature the type signature of the object
   * @throws SerializationException
   */
	protected function serialize($instance, $typeSignature,MappedClass $clazz){
		assert ($instance !== null);

		/*Class<?>*/
		// $clazz = instance.getClass();
		$this->serializationPolicy->validateSerialize($clazz);

		$this->serializeImpl($instance, $clazz);
	}
	/**
   * 
   *
   * @param Object $instance
   * @param MappedClass $instanceClass
   * @throws SerializationException
   */
	private function serializeImpl($instance, MappedClass $instanceClass)
	{
		assert($instance !== null);

		/*ReflectionClass*/
		$customSerializer = SerializabilityUtil::hasCustomFieldSerializer($instanceClass);
		if ($customSerializer != null) {
			$this->serializeWithCustomSerializer($customSerializer, $instance, $instanceClass);
		} else if ($instanceClass->isArray ()) {
			$this->serializeArray ( $instanceClass, $instance );
		} else {
			// Arrays are serialized using custom serializers so we should never get
			// here for array types.
			//
			// assert (!$customSerializer->isArray());
			$this->serializeClass($instance, $instanceClass);
		}
	}
	/**
   * 
   *
   * @param ReflectionClass $customSerializer
   * @param Object $instance
   * @param MappedClass $instanceClass
   * @throws SerializationException 
   */
	private function serializeWithCustomSerializer(ReflectionClass $customSerializer,
	$instance, MappedClass $instanceClass) {

		/*ReflectionMethod */
		$serialize=null;
		try {
			//if ($instanceClass->isArray()) {
			/*MappedClass*/
			//$componentType = $instanceClass->getComponentType();
			//if (!$componentType->isPrimitive()) {
			//  $instanceClass = array(); //Class.forName("[Ljava.lang.Object;");
			// }

			//$serialize->invoke(null, $this, $instance,$instanceClass);
			//}

			$serialize = $customSerializer->getMethod("serialize");
			//SerializationStreamWriter.class, instanceClass);

			$serialize->invoke(null, $this, $instance,$instanceClass);

			/*} catch (SecurityException $e) {
			throw new SerializationException($e);

			} catch (NoSuchMethodException $e) {
			throw new SerializationException($e);

			} catch (IllegalArgumentException $e) {
			throw new SerializationException(e);

			} catch (IllegalAccessException $e) {
			throw new SerializationException($e);

			} catch (InvocationTargetException $e) {
			throw new SerializationException($e);
			*/
		} catch (ClassNotFoundException $e) {			
  	  		require_once(GWTPHP_DIR.'/maps/java/lang/SerializationException.class.php');
			throw new SerializationException($e->getMessage());
		}
	}
	
	  /**
   * Serialize an instance that is an array. Will default to serializing the
   * instance as an Object vector if the instance is not a vector of primitives,
   * Strings or Object.
   * 
   * @param MappedClass instanceClass
   * @param Object instance
   * @throws SerializationException
   */
  private function serializeArray(MappedClass $instanceClass, $instance) {
    assert ($instanceClass->isArray());
    $s = $instanceClass->getSignature ();
	/*VectorWriter */		$instanceReader = self::$CLASS_TO_VECTOR_WRITER [$s];
    if ($instanceReader != null) {
      $instanceReader->write($this, $instance, $instanceClass);
    } else {
      VectorWriter::OBJECT_VECTOR()->write($this, $instance, $instanceClass);
    }
  }
	
	
	/**
   *
   *
   * @param Object $instance
   * @param MappedClass $instanceClass
   * @ throws SerializationException
   */
	private function serializeClass( $instance, MappedClass $instanceClass)
	{
		assert ($instance != null);
		$serializableFields = SerializabilityUtil::applyFieldSerializationPolicy($instanceClass);

		foreach ($serializableFields as $declField) {
			assert ($declField != null);
			$value = null;
			$propName = $declField->getName();
			$rClass = $instanceClass->getReflectionClass();
			//$rClass = new ReflectionObject($instance);
			if ($rClass == null) {
  	  			require_once(GWTPHP_DIR.'/maps/java/lang/ClassNotFoundException.class.php');
				throw new ClassNotFoundException('MappedClass: '.$instanceClass->getSignature().' do not contains ReflectionClass infomration');
			}

			if (!$rClass->hasProperty($propName)) {
				require_once(GWTPHP_DIR.'/maps/java/lang/SerializationException.class.php');
				throw new SerializationException('MappedClass: '.$instanceClass->getSignature().' do not contains property: '.$propName.' Did you mapped all properties?');
			}

			$rProperty = $rClass->getProperty($propName);
			if ($rProperty->isPublic()) {
				$value = $rProperty->getValue($instance);
			} else { // not public access to property, we try invoke getter method
				$propNameSetter = 'get'. strtoupper($propName[0]). substr($propName, 1,strlen($propName));
				if (!$rClass->hasMethod($propNameSetter)) {
					require_once(GWTPHP_DIR.'/maps/java/lang/SerializationException.class.php');
					throw new SerializationException('MappedClass: '.$instanceClass->getSignature().' do not contains getter method for private property: '.$propName.'. Mapped object should be in pojo style?');
				}
				$rMethod = $rClass->getMethod($propNameSetter) ;
				if ($rMethod->isPublic()) {
					$value = $rMethod->invoke($instance);
				}
				else {
					require_once(GWTPHP_DIR.'/maps/java/lang/SerializationException.class.php');
					throw new SerializationException('MappedClass: '.$instanceClass->getSignature().' do not contains public getter method for private property: '.$propName.'. Mapped object should be in pojo style?');

				}
			}

			$this->serializeValue($value, $declField->getType());

		}


		/* assert (instance != null);

		Field[] declFields = instanceClass.getDeclaredFields();
		Field[] serializableFields = SerializabilityUtil.applyFieldSerializationPolicy(declFields);
		for (Field declField : serializableFields) {
		assert (declField != null);

		boolean isAccessible = declField.isAccessible();
		boolean needsAccessOverride = !isAccessible
		&& !Modifier.isPublic(declField.getModifiers());
		if (needsAccessOverride) {
		// Override the access restrictions
		declField.setAccessible(true);
		}

		Object value;
		try {
		value = declField.get(instance);
		serializeValue(value, declField.getType());

		} catch (IllegalArgumentException e) {
		throw new SerializationException(e);

		} catch (IllegalAccessException e) {
		throw new SerializationException(e);
		}

		if (needsAccessOverride) {
		// Restore the access restrictions
		declField.setAccessible(isAccessible);
		}
		}

		Class<?> superClass = instanceClass.getSuperclass();
		if (serializationPolicy.shouldSerializeFields(superClass)) {
		serializeImpl(instance, superClass);
		}*/

		$superClass = $instanceClass->getSuperclass();
		if ($superClass!= null && $this->serializationPolicy->shouldDeserializeFields($superClass)) {
			$this->serializeImpl($instance,$superClass);
		}
	}

}

abstract class VectorWriter {
	
	/**
	 * 
	 *
	 * @param ServerSerializationStreamWriter $stream
	 * @param object $instance
	 * @throws SerializationException;
	 */
	public abstract function write(ServerSerializationStreamWriter $stream, $instance,MappedClass $instanceClass = null);
        
	/**
	 * @return VectorWriter
	 */
	public static function BOOLEAN_VECTOR() {
		return BooleanVectorWriter::getInstance ();
	}
	/**
	 * @return VectorWriter
	 */
	public static function BYTE_VECTOR() {
		return ByteVectorWriter::getInstance ();
	}
	/**
	 * @return VectorWriter
	 */
	public static function CHAR_VECTOR() {
		return CharVectorWriter::getInstance ();
	}
	/**
	 * @return VectorWriter
	 */
	public static function DOUBLE_VECTOR() {
		return DoubleVectorWriter::getInstance ();
	}
	/**
	 * @return VectorWriter
	 */
	public static function FLOAT_VECTOR() {
		return FloatVectorWriter::getInstance ();
	}
	/**
	 * @return VectorWriter
	 */
	public static function INT_VECTOR() {
		return IntVectorWriter::getInstance ();
	}
	/**
	 * @return VectorWriter
	 */
	public static function LONG_VECTOR() {
		return LongVectorWriter::getInstance ();
	}
	/**
	 * @return VectorWriter
	 */
	public static function OBJECT_VECTOR() {
		return ObjectVectorWriter::getInstance ();
	}
	/**
	 * @return VectorWriter
	 */
	public static function SHORT_VECTOR() {
		return ShortVectorWriter::getInstance ();
	}
	/**
	 * @return VectorWriter
	 */
	public static function STRING_VECTOR() {
		return StringVectorWriter::getInstance ();
	}

}

class BooleanVectorWriter extends VectorWriter {
	private static $instance = null;
	private function __construct() {
	}
	private function __clone() {
	}
	protected static function getInstance() {
		return (self::$instance === null ? self::$instance = new self ( ) : self::$instance);
	}	
	public function write(ServerSerializationStreamWriter $stream, $instance,MappedClass $instanceClass = null) {
        $stream->writeInt(count($instance));
        for ($i = 0, $n = count($instance); $i < $n; ++$i) {
          $stream->writeBoolean($instance[$i]);
        }
    }	
}


class ByteVectorWriter extends VectorWriter {
	private static $instance = null;
	private function __construct() {
	}
	private function __clone() {
	}
	protected static function getInstance() {
		return (self::$instance === null ? self::$instance = new self ( ) : self::$instance);
	}
	public function write(ServerSerializationStreamWriter $stream, $instance,MappedClass $instanceClass = null) {
        $stream->writeInt(count($instance));
        for ($i = 0, $n = count($instance); $i < $n; ++$i) {
          $stream->writeByte($instance[$i]);
        }
    }	
}

class CharVectorWriter extends VectorWriter {
	private static $instance = null;
	private function __construct() {
	}
	private function __clone() {
	}
	protected static function getInstance() {
		return (self::$instance === null ? self::$instance = new self ( ) : self::$instance);
	}
	public function write(ServerSerializationStreamWriter $stream, $instance,MappedClass $instanceClass= null) {
        $stream->writeInt(count($instance));
        for ($i = 0, $n = count($instance); $i < $n; ++$i) {
          $stream->writeChar($instance[$i]);
        }
    }	
}

class DoubleVectorWriter extends VectorWriter {
	private static $instance = null;
	private function __construct() {
	}
	private function __clone() {
	}
	protected static function getInstance() {
		return (self::$instance === null ? self::$instance = new self ( ) : self::$instance);
	}
	public function write(ServerSerializationStreamWriter $stream, $instance,MappedClass $instanceClass= null) {
        $stream->writeInt(count($instance));
        for ($i = 0, $n = count($instance); $i < $n; ++$i) {
          $stream->writeDouble($instance[$i]);
        }
    }	
}

class FloatVectorWriter extends VectorWriter {
	private static $instance = null;
	private function __construct() {
	}
	private function __clone() {
	}
	protected static function getInstance() {
		return (self::$instance === null ? self::$instance = new self ( ) : self::$instance);
	}
	public function write(ServerSerializationStreamWriter $stream, $instance,MappedClass $instanceClass= null) {
        $stream->writeInt(count($instance));
        for ($i = 0, $n = count($instance); $i < $n; ++$i) {
          $stream->writeFloat($instance[$i]);
        }
    }	
}

class IntVectorWriter extends VectorWriter {
	private static $instance = null;
	private function __construct() {
	}
	private function __clone() {
	}
	protected static function getInstance() {
		return (self::$instance === null ? self::$instance = new self ( ) : self::$instance);
	}
	public function write(ServerSerializationStreamWriter $stream, $instance,MappedClass $instanceClass= null) {
        $stream->writeInt(count($instance));
        for ($i = 0, $n = count($instance); $i < $n; ++$i) {
          $stream->writeInt($instance[$i]);
        }
    }	
}

class LongVectorWriter extends VectorWriter {
	private static $instance = null;
	private function __construct() {
	}
	private function __clone() {
	}
	protected static function getInstance() {
		return (self::$instance === null ? self::$instance = new self ( ) : self::$instance);
	}
	public function write(ServerSerializationStreamWriter $stream, $instance,MappedClass $instanceClass= null) {
        $stream->writeInt(count($instance));
        for ($i = 0, $n = count($instance); $i < $n; ++$i) {
          $stream->writeLong($instance[$i]);
        }
    }	
}

class ObjectVectorWriter extends VectorWriter {
	private static $instance = null;
	private function __construct() {
	}
	private function __clone() {
	}
	protected static function getInstance() {
		return (self::$instance === null ? self::$instance = new self ( ) : self::$instance);
	}
	public function write(ServerSerializationStreamWriter $stream, $instance,MappedClass $instanceClass= null) {
        $stream->writeInt(count($instance));
        for ($i = 0, $n = count($instance); $i < $n; ++$i) {
          $stream->writeObject($instance[$i],$instanceClass->getComponentType());
        }
    }	
}

class ShortVectorWriter extends VectorWriter {
	private static $instance = null;
	private function __construct() {
	}
	private function __clone() {
	}
	protected static function getInstance() {
		return (self::$instance === null ? self::$instance = new self ( ) : self::$instance);
	}
	public function write(ServerSerializationStreamWriter $stream, $instance,MappedClass $instanceClass= null) {
        $stream->writeInt(count($instance));
        for ($i = 0, $n = count($instance); $i < $n; ++$i) {
          $stream->writeShort($instance[$i]);
        }
    }	
}

class StringVectorWriter extends VectorWriter {
	private static $instance = null;
	private function __construct() {
	}
	private function __clone() {
	}
	protected static function getInstance() {
		return (self::$instance === null ? self::$instance = new self ( ) : self::$instance);
	}
	public function write(ServerSerializationStreamWriter $stream, $instance,MappedClass $instanceClass= null) {
        $stream->writeInt(count($instance));
        for ($i = 0, $n = count($instance); $i < $n; ++$i) {
          $stream->writeString($instance[$i]);
        }
    }	
}

?>
