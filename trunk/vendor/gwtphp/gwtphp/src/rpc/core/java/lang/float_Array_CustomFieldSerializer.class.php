<?php

class float_Array_CustomFieldSerializer {
	/**
	 * 
	 *
	 * @param SerializationStreamReader $streamReader
	 * @param unknown_type $instance
	 * @throws SerializationException
	 */
	public static function deserialize(SerializationStreamReader $streamReader,
	$instance)  {
		for ($itemIndex = 0; $itemIndex < count($instance); ++$itemIndex) {
			$instance[$itemIndex] = $streamReader->readFloat();
		}
	}
	/**
 * 
 *
 * @param SerializationStreamWriter $streamWriter
 * @param unknown_type $instance
 * @throws SerializationException
 */
	public static function serialize(SerializationStreamWriter $streamWriter,
	$instance,MappedClass $instanceClass)  {
		$itemCount = count($instance);
		$streamWriter->writeInt($itemCount);
		for ($itemIndex = 0; $itemIndex < $itemCount; ++$itemIndex) {
			$streamWriter->writeFloat($instance[$itemIndex]);
		}
	}
}

?>