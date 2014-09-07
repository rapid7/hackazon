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

public class Function {

	private final String name;
	private final String returnType;
	private final String returnTypeCRC;
	private final String[] paramsTypeNames;
	private final String[] paramNames;
	private final String[] exceptions;
	
	public Function(String name, String returnType, String returnTypeCRC, 
			String[] params, String[] paramNames, String[] errors){
		this.name = name;
		this.returnType = returnType;
		this.returnTypeCRC = returnTypeCRC;
		this.paramNames = paramNames;
		this.paramsTypeNames = params;
		this.exceptions = errors;
	}

	public String getName() {
		return name;
	}

	public String[] getParamNames() {
		return paramNames;
	}

	public String getReturnType() {
		return returnType;
	}

	public String getReturnTypeCRC() {
		return returnTypeCRC;
	}

	public String[] getParamsTypeNames() {
		return paramsTypeNames;
	}

	public String[] getExceptions() {
		return exceptions;
	}
}
