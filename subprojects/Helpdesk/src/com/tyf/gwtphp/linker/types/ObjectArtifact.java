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
package com.tyf.gwtphp.linker.types;

import java.util.HashMap;
import java.util.Map;

public class ObjectArtifact extends Element {
	
	/**
	 * 
	 */
	private static final long serialVersionUID = 1L;
	private final String typeCRC;
	private final Map<String, Field> fields = new HashMap<String, Field>();

	public ObjectArtifact(String className, String simpleClassName, String parentClassName,
			String simpleParentClassName, Boolean isInterface, Boolean isAbstract, String typeCRC) {
		super(className, simpleClassName, parentClassName, simpleParentClassName, isInterface, isAbstract);
		this.typeCRC = typeCRC;
	}

	public void putField(String fieldName, Field fieldInfo) {
		fields.put(fieldName, fieldInfo);
	}

	public Map<String, Field> getFields() {
		return fields;
	}

	public String getTypeCRC() {
		return typeCRC;
	}
	
}
