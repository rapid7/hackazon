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

import java.util.Collection;
import java.util.HashSet;
import java.util.Set;

import com.google.gwt.core.ext.GeneratorContext;
import com.google.gwt.core.ext.RebindResult;
import com.google.gwt.core.ext.TreeLogger;
import com.google.gwt.core.ext.UnableToCompleteException;
import com.google.gwt.core.ext.typeinfo.JClassType;
import com.google.gwt.core.ext.typeinfo.JField;
import com.google.gwt.core.ext.typeinfo.JMethod;
import com.google.gwt.core.ext.typeinfo.JType;
import com.google.gwt.core.ext.typeinfo.TypeOracle;
import com.google.gwt.user.rebind.rpc.ServiceInterfaceProxyGenerator;
import com.tyf.gwtphp.linker.types.Field;
import com.tyf.gwtphp.linker.types.Function;
import com.tyf.gwtphp.linker.types.ObjectArtifact;
import com.tyf.gwtphp.linker.types.ServiceArtifact;

public class PHPRemoteServiceGenerator extends ServiceInterfaceProxyGenerator {

	private static final Set<JType> customObjectSet = new HashSet<JType>();
	private static final Set<String> generatedClasses = new HashSet<String>();

	/**
	 * This method overrides the default RemoteService interface proxy generator, 
	 * gathers type information and passes it to the linker, in addition to
	 * invoking the default generator.
	 */
	@Override
	public RebindResult generateIncrementally(TreeLogger logger, GeneratorContext ctx,
			String requestedClass) throws UnableToCompleteException {

		TypeOracle typeOracle = ctx.getTypeOracle();
		String qualifiedClassName;
		String packageName, className;

		try {
			// get classType and save instance variables
			JClassType classType = typeOracle.getType(requestedClass);
			packageName = classType.getPackage().getName();
			className = classType.getSimpleSourceName();
			qualifiedClassName = packageName + "." + className;
			// prevent re-discovery
			if (generatedClasses.contains(qualifiedClassName))
				return super.generateIncrementally(logger, ctx, requestedClass);

			JClassType supertype = getSuperType(classType);
			ServiceArtifact artifact = new ServiceArtifact(
					classType.getQualifiedSourceName(), classType.getSimpleSourceName(),
					supertype!=null?supertype.getQualifiedSourceName():null, 
					supertype!=null?supertype.getSimpleSourceName():null,
					classType.isInterface()!=null, classType.isAbstract());

			// discover new custom objects, whose information must be known by
			// the server
			Set<ObjectArtifact> objectArtifacts = new HashSet<ObjectArtifact>();
			Set<JType> discoveredTypes = new HashSet<JType>();

			JMethod[] methods = classType.getMethods();
			// parse RPC methods
			for (JMethod method : methods) {
				JType returnType = method.getReturnType();
				JType[] paramTypes = method.getParameterTypes();
				String[] params = new String[paramTypes.length];
				String[] paramNames = new String[params.length];
				JClassType[]exceptionTypes = method.getThrows();
				String[] exceptions = new String[exceptionTypes.length];

				// getRpcTypeName recursively generates the type name, while
				// adding all discovered types to the set, flattening out 
				// arrays & generics, etc.
				String returnTypeName = TypeUtil.getPHPRpcTypeName(returnType, discoveredTypes);
				for (int i = 0; i < params.length; i++) {
					params[i] = TypeUtil.getPHPRpcTypeName(paramTypes[i], discoveredTypes);
					paramNames[i] = method.getParameters()[i].getName();
				}
				for (int i = 0; i < exceptions.length; i++){
					exceptions[i] = TypeUtil.getPHPRpcTypeName(exceptionTypes[i], discoveredTypes);
				}

				// get type signature of the return type
				String returnTypeCRC = TypeUtil.getCRC(returnType);

				Function f = new Function(method.getName(), returnTypeName, returnTypeCRC,
						params, paramNames, exceptions);

				artifact.putMethod(method.getName(), f);
			}
			for (JType type : discoveredTypes) {
				// logger.log(TreeLogger.INFO, type.getQualifiedSourceName());
				objectArtifacts.addAll(discoverObjects(type));
			}

			ctx.commitArtifact(logger, artifact);
			for (ObjectArtifact a : objectArtifacts) {
				ctx.commitArtifact(logger, a);
			}
		} catch (Exception e) {
			logger.log(TreeLogger.ERROR, "ERROR: "+e.getMessage(), e);
			return null;
		}

		return super.generateIncrementally(logger, ctx, requestedClass);
	}

	private Collection<? extends ObjectArtifact> discoverObjects(JType type)
			throws ClassNotFoundException {
		Set<ObjectArtifact> objects = new HashSet<ObjectArtifact>();
		// reduce time wasted doing duplicate discovery
		if (customObjectSet.contains(type))
			return objects;

		if (isCustom(type)) {
			Set<JType> discoveredTypes = new HashSet<JType>();
			JClassType supertype = getSuperType(type);
			JClassType classType;
			ObjectArtifact object;
			if ((classType = type.isClass()) != null) {
				object = new ObjectArtifact(type.getQualifiedSourceName(),
						type.getSimpleSourceName(), 
						supertype!=null?supertype.getQualifiedSourceName():null,
						supertype!=null?supertype.getSimpleSourceName():null, 
						classType.isInterface()!=null, 
						classType.isAbstract(), TypeUtil.getCRC(type));
				for (JField f : classType.getFields()) {
					String fieldName = f.getName();
					String fieldType = TypeUtil.getPHPRpcTypeName(f.getType(), discoveredTypes);
					object.putField(fieldName, new Field(fieldName, fieldType, 
							TypeUtil.toPHPType(f.getType())));
				}
			}else{
				object = new ObjectArtifact(type.getQualifiedSourceName(),
						type.getSimpleSourceName(), 
						supertype!=null?supertype.getQualifiedSourceName():null,
						supertype!=null?supertype.getSimpleSourceName():null, 
						false, false, TypeUtil.getCRC(type));
			}
			objects.add(object);
			customObjectSet.add(type);
			
			// recursively discover the parent classes. This has to be done after the current
			// type is added into the customObjectSet to avoid infinite recursion
			if (classType != null){
				if (classType.getSuperclass() != null){
					objects.addAll(discoverObjects(classType.getSuperclass()));
				}
			}
			
			// recursively discover other custom objects refereced by this object
			for (JType t : discoveredTypes) {
				objects.addAll(discoverObjects(t));
			}
		}
		return objects;
	}

	private JClassType getSuperType(JType type) {
		JClassType classType;
		JClassType parentName = null;
		if ((classType = type.isClass()) != null) {
			JClassType supertype;
			if ((supertype = classType.getSuperclass()) != null){
				if (isCustom(supertype))
					parentName = supertype;
			}
		}
		return parentName;
	}

	/**
	 * Checks if a JType is built-in or user-defined
	 * 
	 * @param returnType
	 * @return
	 */
	private boolean isCustom(JType returnType) {
		if (returnType.isPrimitive() != null)
			return false;
		// exclude built-in Java classes
		if (returnType.getQualifiedSourceName().startsWith("java."))
			return false;
		if (returnType.getQualifiedSourceName().startsWith("com.google.gwt.user.client.rpc."))
			return false;
		return true;
	}

}
