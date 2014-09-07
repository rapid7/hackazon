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
import java.util.Set;

import com.google.gwt.core.ext.typeinfo.JArrayType;
import com.google.gwt.core.ext.typeinfo.JClassType;
import com.google.gwt.core.ext.typeinfo.JParameterizedType;
import com.google.gwt.core.ext.typeinfo.JPrimitiveType;
import com.google.gwt.core.ext.typeinfo.JType;
import com.google.gwt.user.rebind.rpc.SerializationUtils;
import com.google.gwt.user.server.rpc.SerializationPolicy;
import com.google.gwt.user.server.rpc.impl.SerializabilityUtil;
import com.google.gwt.user.server.rpc.impl.StandardSerializationPolicy;

public class TypeUtil {

	static SerializationPolicy policy = new StandardSerializationPolicy(
			new HashMap<Class<?>, Boolean>(), new HashMap<Class<?>, Boolean>(),
			new HashMap<Class<?>, String>());

	/**
	 * Returns the special binary name of a type. This type name corresponds the one
	 * used in the GWT-PHP RPC library. This method also puts all other types referenced
	 * into the given set.
	 * 
	 * E.g. HashMap<String, Integer> actually contains three types, all of which will be 
	 * discovered and their names combined to get the final PHP RPC type name.
	 * 
	 * @param type
	 *            TypeOracle type to get the name for
	 * @param typeSet 
	 *            A Set to receive classes discovered during type name parsing
	 * @return binary name for a type
	 */
	public static String getPHPRpcTypeName(JType type, Set<JType> typeSet) {
		JPrimitiveType primitiveType = type.isPrimitive();
		if (primitiveType != null) {
			return primitiveType.getJNISignature();
		}

		JArrayType arrayType = type.isArray();
		if (arrayType != null) {
			JType component = arrayType.getComponentType();
			if (component.isClassOrInterface() != null) {
				return "[L" + getPHPRpcTypeName(arrayType.getComponentType(), typeSet) + ";";
			} else {
				return "[" + getPHPRpcTypeName(arrayType.getComponentType(), typeSet);
			}
		}

		JParameterizedType parameterizedType = type.isParameterized();
		if (parameterizedType != null) {
			String base = getPHPRpcTypeName(parameterizedType.getBaseType(), typeSet);
			StringBuilder sb = new StringBuilder(base);
			sb.append('<');
			JClassType[] args = parameterizedType.getTypeArgs();
			for (int i=0; i<args.length; i++){
				sb.append(getPHPRpcTypeName(args[i], typeSet));
				if (i!=args.length-1) sb.append(',');
			}
			sb.append('>');
			return sb.toString();
		}

		JClassType classType = type.isClassOrInterface();
		assert (classType != null);

		JClassType enclosingType = classType.getEnclosingType();
		if (enclosingType != null) {
			return getPHPRpcTypeName(enclosingType, typeSet) + "$" + classType.getSimpleSourceName();
		}

		typeSet.add(classType);
		return classType.getQualifiedSourceName();
	}

	/**
	 * Recursively generate the type signature. Handles array and generics
	 * cases.
	 */
	public static String getCRC(JType type) throws ClassNotFoundException {
		JPrimitiveType primitiveType = type.isPrimitive();
		if (primitiveType != null) {
			Class<?> clazz = PrimitiveTypes.getClass(primitiveType.getSimpleSourceName());
			return SerializabilityUtil.getSerializationSignature(clazz, policy);
		}

		JArrayType arrayType = type.isArray();
		if (arrayType != null) {
			JType component = arrayType.getComponentType();
			String parentCRC = SerializabilityUtil.getSerializationSignature(
					Class.forName(SerializationUtils.getRpcTypeName(type)), policy);
			if (component.isClassOrInterface() != null) {
				return parentCRC + "[L" + getCRC(arrayType.getComponentType()) + ";";
			} else {
				return parentCRC + "[" + getCRC(arrayType.getComponentType());
			}
		}

		JParameterizedType parameterizedType = type.isParameterized();
		if (parameterizedType != null) {
			String base = getCRC(parameterizedType.getBaseType());
			StringBuilder sb = new StringBuilder(base);
			sb.append('<');
			JClassType[] args = parameterizedType.getTypeArgs();
			for (int i=0; i<args.length; i++){
				sb.append(getCRC(args[i]));
				if (i!=args.length-1) sb.append(',');
			}
			sb.append('>');
			return sb.toString();
		}

		JClassType classType = type.isClassOrInterface();
		assert (classType != null);

		JClassType enclosingType = classType.getEnclosingType();
		if (enclosingType != null) {
			return getCRC(enclosingType)
					+ "$"
					+ SerializabilityUtil.getSerializationSignature(
							Class.forName(classType.getQualifiedSourceName()), policy);
		}

		return SerializabilityUtil.getSerializationSignature(
				Class.forName(type.getQualifiedSourceName()), policy);
	}

	public static String toPHPType(JType type) {
		// TODO: more robust conversion
		if (type.getSimpleSourceName().equals("String")){
			return "string";
		}
		return type.getSimpleSourceName().toLowerCase();
	}

}
