<?PHP
/*
 * GWTPHP is a port to PHP of the GWT RPC package.
 * 
 * <p>This framework is based on GWT (see {@link http://code.google.com/webtoolkit/ gwt-webtoolkit} for details).</p>
 * <p>Design, strategies and part of the methods documentation are developed by Google Team  </p>
 * 
 * <p>PHP port, extensions and modifications by Rafal M.Malinowski. All rights reserved.<br>
 * For more information, please see {@link http://gwtphp.sourceforge.com/}.</p>
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

require_once(GWTPHP_DIR.'/rpc/impl/SerializedInstanceReference.class.php');
require_once(GWTPHP_DIR.'/util/HashMapUtil.class.php');
require_once(GWTPHP_DIR.'/GWTPHPContext.class.php');

class AnonymousSerializedInstanceReference implements SerializedInstanceReference {
	
	
	/**
	 * 
	 * @var String
	 */
	private $name;

	/**
	 * 
	 * @var String
	 */
	private $signature;

	public function __construct($name,$signature) {
		$this->name = $name;
		$this->signature = $signature;
	}

	/**
	 *
	 * @param String $signature of the instance reference
	 * @return void
	 */
	public function setSignature($signature) {
		$this->signature = $signature;
	}
	/**
	 *
	 * @return String signature of the instance reference
	 */
	public function getSignature() {
		return $this->signature;
	}

	/**
	 *
	 * @param String $name of the type
	 * @return void
	 */
	public function setName($name) {
		$this->name = $name;
	}
	/**
	 *
	 * @return String name of the type
	 */
	public function getName() {
		return $this->name;
	}
}
/**
 * Serialization utility class used by the server-side RPC code.
 */
class SerializabilityUtil {
	//unused// const DEFAULT_ENCODING = "UTF-8";
	
	/**
	* A permanent cache of all which classes onto custom field serializers. This
	* is safe to do because a Class is guaranteed not to change within the
	* lifetime of a ClassLoader (and thus, this Map). Access must be
	* synchronized.
	* Map<Class<?>, Class<?>>
	* @var HashMapUtil <MappedClass,MappedClass>
	*/
	private static $classCustomSerializerCache;
	/**
	 * 
	 *
	 * @var HashMapUtil <MappedClass,MappedClass>
	 */
	private static $classSerializableFieldsCache;
	
	private static $JRE_SERIALIZER_PACKAGE = "rpc.core";
	
	public static /*MappedField[]*/ function applyFieldSerializationPolicy(MappedClass $clazz) {
		//		 Field[] serializableFields = getCachedSerializableFieldsForClass(clazz);
		//    if (serializableFields == null) {
		//      ArrayList<Field> fieldList = new ArrayList<Field>();
		//      Field[] fields = clazz.getDeclaredFields();
		//      for (Field field : fields) {
		//        if (fieldQualifiesForSerialization(field)) {
		//          fieldList.add(field);
		//        }
		//      }
		//      serializableFields = fieldList.toArray(new Field[fieldList.size()]);
		//
		//      // sort the fields by name
		//      Arrays.sort(serializableFields, 0, serializableFields.length,
		//          FIELD_COMPARATOR);
		//
		//      putCachedSerializableFieldsForClass(clazz, serializableFields);
		//    }
		//
		//    return serializableFields;
		/*MappedField[]*/ $serializableFields = self::getCachedSerializableFieldsForClass($clazz);
		if ($serializableFields == null) {
			/* ArrayList<Field>*/ $fieldList = array();
			/*MappedField[]*/ $fields = $clazz->getDeclaredFields();
			foreach ($fields as $field) {
				assert ($field != null);
				$fieldList[$field->getName()] = $field;
				
			}
			ksort($fieldList);
			$serializableFields = $fieldList;
			self::putCachedSerializableFieldsForClass($clazz, $serializableFields);
		}
		
		return $serializableFields;
	}
	
	/**
	 * 
	 * @deprecated 
	 * @param array<MappedField> $fields
	 * @return array<MappedField>
	 */
	public static /*Field[]*/ function applyFieldSerializationPolicy2(/*MappedField[]*/ $fields) {
   
		/* ArrayList<Field>*/ $fieldList = array();
    foreach ($fields as $field) {
      assert ($field != null);

	      /*int fieldModifiers = field.getModifiers();
	      if (Modifier.isStatic(fieldModifiers)
	          || Modifier.isTransient(fieldModifiers)
	          || Modifier.isFinal(fieldModifiers)) {
	        continue;
	      }
	
	      fieldList.add(field);
	      */
      $fieldList[$field->getName()] = $field;
    }

		/*Field[] fieldSubset = fieldList.toArray(new Field[fieldList.size()]);
		
		// sort the fields by name
		Comparator<Field> comparator = new Comparator<Field>() {
		  public int compare(Field f1, Field f2) {
		    return f1.getName().compareTo(f2.getName());
		  }
		};
		Arrays.sort(fieldSubset, 0, fieldSubset.length, comparator);
		*/
		ksort($fieldList);
    return $fieldList;
  }
	/** 
	 *
	 * @return HashMapUtil
	 */
	public static function getClassCustomSerializerCache() {
		return (SerializabilityUtil::$classCustomSerializerCache == null) ? 
		SerializabilityUtil::$classCustomSerializerCache = new HashMapUtil() :
		SerializabilityUtil::$classCustomSerializerCache ;
	}
		
/** 
	 *
	 * @return HashMapUtil
	 */
	public static function getClassSerializableFieldsCache() {
		return (SerializabilityUtil::$classSerializableFieldsCache == null) ? 
		SerializabilityUtil::$classSerializableFieldsCache = new HashMapUtil() :
		SerializabilityUtil::$classSerializableFieldsCache ;
	}

	
	/**
	 * @param string $encodedSerializedInstanceReference
	 * @return SerializedInstanceReference 
	 *
	 */
	public static function  decodeSerializedInstanceReference(
	$encodedSerializedInstanceReference) {
		//$components = encodedSerializedInstanceReference.split();

		//list($name, $signature) = split(SerializedInstanceReference::SERIALIZED_REFERENCE_SEPARATOR, encodedSerializedInstanceReference);
		$components = explode(SERIALIZED_REFERENCE_SEPARATOR, $encodedSerializedInstanceReference);
		return new AnonymousSerializedInstanceReference(count($components) > 0 ? $components[0] : '', count($components) > 1 ? $components[1] : '');

	}
	
	// TODO: to tu serializacja obiektow
	/**
	 * Enter description here...
	 *
	 * @param MappedClass $mappedClass
	 * @return ReflectionClass
	 */
	public static function hasCustomFieldSerializer(MappedClass $instanceType) {		
		assert($instanceType != null);
		
		if ($instanceType->isArray()) {
			return null;
		}
		
		$result = SerializabilityUtil::getCachedSerializerForClass($instanceType);
		if ($result !== null) {
	      // this class has a custom serializer
	      return $result;
	    }
	    
        if (SerializabilityUtil::containsCachedSerializerForClass($instanceType)) {
	      // this class definitely has no custom serializer
	      return null;
	    }
	    // compute whether this class has a custom serializer
	    $result = SerializabilityUtil::computeHasCustomFieldSerializer($instanceType);
	    SerializabilityUtil::putCachedSerializerForClass($instanceType, $result);
	    return $result;
	}
	// TODO: 
	/**
	 * Enter description here...
	 *
	 * @param MappedClass $mappedClass
	 * @return ReflectionClass
	 */
	public static function computeHasCustomFieldSerializer(MappedClass $instanceType) {
		assert($instanceType != null);
		$qualifiedTypeName = $instanceType->getName();
		// delted
		//		$qualifiedTypeName = null;
		//		if ($instanceType->isArray()) {
		//			/*MappedClass*/
		//			$componentType = $instanceType->getComponentType();
		//			
		//			if ($componentType->isPrimitive()) {
		//				$qualifiedTypeName = 'java.lang.'.$componentType->getName();
		//				
		//			} else {
		//				$qualifiedTypeName = 'java.lang.Object';
		//				
		//			}
		//			$qualifiedTypeName .= '_Array';
		//
		//		} else {
		//			$qualifiedTypeName = $instanceType->getName();
		//			
		//		}
		// delted
		
		$classLoader = GWTPHPContext::getInstance()->getClassLoader();

		$simpleSerializerName = $qualifiedTypeName."_CustomFieldSerializer";


		$customSerializer = SerializabilityUtil::getCustomFieldSerializer($classLoader, $simpleSerializerName);
		if ($customSerializer != null) {
			return $customSerializer;
		}

		// Try with the regular name
		/*ReflectionClass*/
		$customSerializerClass = SerializabilityUtil::getCustomFieldSerializer($classLoader,
			SerializabilityUtil::$JRE_SERIALIZER_PACKAGE.'.'.$simpleSerializerName);
		if ($customSerializerClass != null) {
			return $customSerializerClass;
		}

		return null;

	}
	
	/**
	 * 
	 *
	 * @param ClassLoader $classLoader
	 * @param string $qualifiedSerialzierName
	 * @return ReflectionClass
	 */
	private static function getCustomFieldSerializer(ClassLoader $classLoader,
      $qualifiedSerialzierName) {
    try {
    	/*ReflectionClass*/    
     $customSerializerClass = $classLoader->loadClass($qualifiedSerialzierName);
      return $customSerializerClass;
    } catch (ClassNotFoundException $ex) {
      return null;
    }
  }
  ///*MappedField*/ $serializableFields = self::getCachedSerializableFieldsForClass($clazz);
  /**
   *
   * @param MappedClass $clazz
   * @return MappedField[]
   */
  private static /*MappedField[]*/ function getCachedSerializableFieldsForClass(MappedClass $clazz) {
      return self::getClassSerializableFieldsCache()->get($clazz);
  }
	/**
	 * 
	 *
	 * @param MappedClass $instanceType
	 * @return ReflectionClass
	 */
	private static function getCachedSerializerForClass(MappedClass $instanceType) {
	     return self::getClassCustomSerializerCache()->get($instanceType);
	  
	}

/**
 * 
 *
 * @param MappedClass $instanceType
 * @return boolean
 */
   private static function containsCachedSerializerForClass(MappedClass $instanceType) {    
      return self::getClassCustomSerializerCache()->containsKey($instanceType);    
  }
  
  /**
   * 
   *
   * @param MappedClass $instanceType
   * @param ReflectionClass $customFieldSerializer
   */
  private static function putCachedSerializerForClass(MappedClass $instanceType,
  													  ReflectionClass $customFieldSerializer = null) {
  	self::getClassCustomSerializerCache()->put($instanceType,$customFieldSerializer);

  }
  
  /**
   * 
   *
   * @param MappedClass $clazz
   * @param MappedField[] $serializableFields
   */
  private static function  putCachedSerializableFieldsForClass(MappedClass $clazz,array $serializableFields) {
  		self::getClassSerializableFieldsCache()->put($clazz,$serializableFields);

  }
  
  private static $serializationSignetures = array(
					'java.lang.Boolean' => '476441737',
					'java.lang.Byte' => '1571082439',
					'java.lang.Character' => '2663399736',
					'java.lang.Short' => '551743396',
					'java.lang.Integer' => '3438268394',
					'java.lang.Long' => '4227064769',
					'java.lang.Float' => '1718559123',
					'java.lang.Double' => '858496421',
					'java.lang.String' => '2004016611'
  );
  
  /**
   * @return string
   *
   */
  public static function getSerializationSignature($signature) {
  		if (isset(self::$serializationSignetures[$signature])) {
  			return self::$serializationSignetures[$signature];
  		} else {
  			return null;
  		}
  }

}
?>