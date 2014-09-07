/*
 * GWTPHP is a port to PHP of the GWT RPC package.
 * This framework is based on GWT.
 * Design, strategies and part of the methods documentation are developed by Google Inc.
 * PHP port, extensions and modifications by Rafal M.Malinowski. All rights reserved.
 * Additional modifications, GWT generators and linkers by Yifei Teng. All rights reserved.
 * For more information, please see {@link https://github.com/tengyifei/gwtphp}
 *
 * Licensed under the Apache License, Version 2.0 (the "License"); you may not
 * use this file except in compliance with the License. You may obtain a copy of
 * the License at
 *
 * http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS, WITHOUT
 * WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied. See the
 * License for the specific language governing permissions and limitations under
 * the License.
 */
package com.tyf.gwtphp.rebind;

import java.util.HashMap;
import java.util.Map;

public class PrimitiveTypes {
	
	private static final Map<String, Class<?>> classMap = new HashMap<String, Class<?>>();
	
	static {
		classMap.put("boolean", boolean .class);
		classMap.put("byte",    byte    .class);
		classMap.put("char",    char    .class);
		classMap.put("double",  double  .class);
		classMap.put("float",   float   .class);
		classMap.put("int",     int     .class);
		classMap.put("long",    long    .class);
		classMap.put("short",   short   .class);
		classMap.put("void",    void    .class);
	}
	
	public static Class<?> getClass(String className) throws ClassNotFoundException{
		if (classMap.containsKey(className)) return classMap.get(className);
		throw new ClassNotFoundException();
	}
}
